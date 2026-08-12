<?php
function fn_calcular_oa_nrc($v_task_id, $v_projeto_atual, $debug = false) {
    
    $log = "=== INICIANDO DEBUG DO MOTOR O&A ===\n";
    $log .= "Task ID: $v_task_id | Projeto ID: $v_projeto_atual\n";

    if (empty($v_task_id) || empty($v_projeto_atual)) {
        $log .= "ERRO FATAL: Parâmetros vazios. Abortando.\n";
        if ($debug) { echo "<pre>$log</pre>"; die(); }
        return false;
    }

    // ====================================================================
    // PASSO 0: SINCRONIZAR CUSTOS REAIS DE MATERIAIS
    // Soma tudo que está empenhado (Ignorando material do CLIENTE)
    // ====================================================================
    $sql_sum_mat = "SELECT COALESCE(SUM(total_cost), 0) FROM mro_task_materials 
                    WHERE task_id = $v_task_id AND material_source != 'CLIENTE'";
    sc_lookup(rs_sum, $sql_sum_mat);
    $novo_custo_material = (float) {rs_sum[0][0]};
    
    // Atualiza a capa da tarefa com o custo real de materiais
    sc_exec_sql("UPDATE mro_tasks SET estimated_material_cost = $novo_custo_material WHERE task_id = $v_task_id");
    $log .= "Sincronização: Custo total de materiais atualizado para $novo_custo_material\n";

    // ====================================================================
    // PASSO 1: CARREGA AS REGRAS DO CONTRATO
    // ====================================================================
    $sql_contrato = "SELECT is_cap_zero, cap_hh_limit, cap_material_type, cap_material_limit 
                     FROM mro_projects WHERE project_id = " . $v_projeto_atual;
    sc_lookup(rs_cap, $sql_contrato);

    if (empty({rs_cap})) {
        $log .= "ERRO FATAL: Projeto não encontrado ou sem regras de contrato. Abortando.\n";
        if ($debug) { echo "<pre>$log</pre>"; die(); }
        return false; 
    }

    $is_cap_zero   = ({rs_cap[0][0]} == 't' || {rs_cap[0][0]} == 'true' || {rs_cap[0][0]} == 1);
    $cap_hh_limit  = (float) {rs_cap[0][1]};
    $cap_mat_type  = {rs_cap[0][2]}; 
    $cap_mat_limit = (float) {rs_cap[0][3]};

    $log .= "Contrato -> Cap Zero: " . ($is_cap_zero ? 'SIM' : 'NAO') . " | Limite HH: $cap_hh_limit | Tipo Mat: $cap_mat_type | Limite Mat: $cap_mat_limit\n";

    $hh_oa_final  = 0;
    $mat_oa_final = 0;

    // Busca os valores atualizados e o Status da NRC atual
    $sql_totais = "SELECT COALESCE(estimated_hours, 0), COALESCE(estimated_material_cost, 0), status_code 
                   FROM mro_tasks WHERE task_id = " . $v_task_id;
    sc_lookup(rs_totais_nrc, $sql_totais);
    
    if (empty({rs_totais_nrc})) {
        return false; 
    }
                              
    $hh_nrc_atual  = (float) {rs_totais_nrc[0][0]};
    $mat_nrc_atual = (float) {rs_totais_nrc[0][1]};
    $status_atual  = {rs_totais_nrc[0][2]};

    $log .= "Valores NRC Atual -> HH: $hh_nrc_atual | Material: $mat_nrc_atual | Status: $status_atual\n";

    // ====================================================================
    // PASSO 2: O MOTOR DE DECISÃO DE CAP
    // ====================================================================
    if ($is_cap_zero) {
        $log .= "Cálculo: Entrou na regra de CAP ZERO.\n";
        $hh_oa_final  = $hh_nrc_atual;
        $mat_oa_final = $mat_nrc_atual;
        
    } else {
        $log .= "Cálculo: Entrou na regra de Transbordo.\n";
        
        // Acumulado HH
        $sql_hh_acumulado = "SELECT COALESCE(SUM(estimated_hours), 0) FROM mro_tasks 
                             WHERE project_id = $v_projeto_atual AND task_id <> $v_task_id 
                             AND is_nrc = true AND status_code IN ('RELEASED', 'PENDING_OA', 'APPROVED', 'COMPLETED', 'CLOSED')";
        sc_lookup(rs_hh_acum, $sql_hh_acumulado);
        
        $hh_acumulado = (float) {rs_hh_acum[0][0]};
        $hh_disponiveis = $cap_hh_limit - $hh_acumulado;
        if ($hh_disponiveis < 0) $hh_disponiveis = 0;
        
        if ($hh_nrc_atual > $hh_disponiveis) {
            $hh_oa_final = $hh_nrc_atual - $hh_disponiveis;
        }

        // Acumulado Materiais
        if ($cap_mat_type == 'PACKAGE') {
            $sql_mat_acum = "SELECT COALESCE(SUM(estimated_material_cost), 0) FROM mro_tasks 
                             WHERE project_id = $v_projeto_atual AND task_id <> $v_task_id 
                             AND is_nrc = true AND status_code IN ('RELEASED', 'PENDING_OA', 'APPROVED', 'COMPLETED', 'CLOSED')";
            sc_lookup(rs_mat_acum, $sql_mat_acum);
            
            $mat_acumulado = (float) {rs_mat_acum[0][0]};
            $mat_disponivel = $cap_mat_limit - $mat_acumulado;
            if ($mat_disponivel < 0) $mat_disponivel = 0;
            
            if ($mat_nrc_atual > $mat_disponivel) {
                $mat_oa_final = $mat_nrc_atual - $mat_disponivel;
            }
            
        } elseif ($cap_mat_type == 'LINE_ITEM') {
            // Ignora os materiais do cliente na linha também
            $sql_items = "SELECT tm.material_id, tm.total_cost FROM mro_task_materials tm
                          WHERE tm.task_id = $v_task_id AND tm.material_source != 'CLIENTE'";
            sc_lookup(rs_items, $sql_items);
            
            if (!empty({rs_items})) {
                foreach ({rs_items} as $item) {
                    $custo_linha = (float) $item[1];
                    if ($custo_linha > $cap_mat_limit) {
                        $mat_oa_final += ($custo_linha - $cap_mat_limit);
                    }
                }
            }
        }
    }

    // ====================================================================
    // PASSO 3: LÓGICA DE WORKFLOW (DOWNGRADE & UPGRADE DE STATUS)
    // ====================================================================
    $novo_status = $status_atual;

    if ($hh_oa_final > 0 || $mat_oa_final > 0) {
        
        // ESTOUROU O CAP: Precisa de Batch e aprovação do Cliente
        sc_lookup(rs_batch, "SELECT batch_id FROM mro_oa_batches WHERE project_id = $v_projeto_atual AND status = 'DRAFT'");
        if (!empty({rs_batch})) {
            $v_batch_id = {rs_batch[0][0]};
        } else {
            sc_exec_sql("INSERT INTO mro_oa_batches (project_id, status) VALUES ($v_projeto_atual, 'DRAFT')");
            sc_lookup(rs_new_batch, "SELECT MAX(batch_id) FROM mro_oa_batches WHERE project_id = $v_projeto_atual");
            $v_batch_id = {rs_new_batch[0][0]};
        }
        
        // Proteção: Se a tarefa já estava rodando, e os custos aumentaram, bloqueia ela!
        if (in_array($status_atual, ['RELEASED', 'APPROVED', 'COMPLETED'])) {
            $novo_status = 'PENDING_OA';
            $msg = "Revisão Automática: Custo atualizado excedeu o CAP do contrato. Tarefa bloqueada para re-aprovação do cliente.";
            // MRO-117: Registra em mro_task_history
            sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
                         VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
        } elseif ($status_atual != 'DRAFT') {
            // Se estava em PENDING_ENG ou PENDING_PROG, pula pra PENDING_OA
            $novo_status = 'PENDING_OA'; 
            
            // MRO-117: Adiciona log de auditoria no mro_task_history
            $msg = "Revisao Automatica: Custo ultrapassou o limite do CAP do contrato. Encaminhado para aprovacao do cliente.";
            sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks)
                         VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
        }

        sc_exec_sql("UPDATE mro_tasks 
                     SET oa_hours = $hh_oa_final, oa_material_cost = $mat_oa_final, oa_batch_id = $v_batch_id, 
                         status_code = '$novo_status', is_oa = true 
                     WHERE task_id = $v_task_id");

    } else {
        // NÃO ESTOUROU O CAP (Dentro do orçamento)

        // MRO-117: Se veio da Programação (PENDING_PROG), libera direto.
        // Raciocinio: O programador validou a NRC e o motor O&A rodou.
        // Se o custo está dentro do CAP do contrato, não precisa de aprovacao do cliente.
        // A NRC vai direto pra RELEASED (aprovacao automatica).
        if ($status_atual == 'PENDING_PROG') {
            $novo_status = 'RELEASED';
            // MRO-117: Registra aprovacao automatica em mro_task_history
            $msg = "Aprovação Automática: Custo 100% coberto pelo CAP do contrato. NRC liberada.";
            sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
                         VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
        }

        // Se a tarefa estava bloqueada aguardando o cliente (PENDING_OA), e o custo baixou, aprova automático!
        if ($status_atual == 'PENDING_OA') {
            $novo_status = 'RELEASED';
            // MRO-117: Registra aprovacao automatica em mro_task_history
            $msg = "Aprovação Automática: Custo atualizado agora está 100% coberto pelo CAP do contrato.";
            sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
                         VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
        }

        // MRO-122: Ao liberar automaticamente (dentro do CAP), cria os assignments
        // por skill (LABOR) antes de mudar para RELEASED. Sem isso, a NRC ficava
        // RELEASED sem equipe alocada e ninguem conseguia criar os slots depois.
        if ($novo_status == 'RELEASED') {
            fn_criar_assignments_por_skill($v_task_id, $v_projeto_atual);
        }

        sc_exec_sql("UPDATE mro_tasks 
                     SET oa_hours = 0, oa_material_cost = 0, oa_batch_id = NULL, 
                         status_code = '$novo_status', is_oa = false 
                     WHERE task_id = $v_task_id");
    }

    if ($debug) {
        sc_error_message("<div style='text-align:left; font-family:monospace; font-size:13px;'>" . nl2br($log) . "</div>");
        sc_error_exit(); 
    }

    return true;
}

// ====================================================================
// MRO-122: CRIA OS ASSIGNMENTS POR SKILL (LABOR) PARA UMA NRC/TASK
// ====================================================================
// Usado pelo motor O&A antes de liberar automaticamente (RELEASED).
// Replica a logica do btn_liberar_para_execucao para que a NRC aprovada
// automaticamente nao fique sem equipe alocada.
// Protecao: ignora skills que ja possuem assignment (evita duplicidade).
function fn_criar_assignments_por_skill($v_task_id, $v_projeto_atual)
{
    // 0. Busca estimated_hours e skill_code da task (usados como fallback
    // de horas, identico ao btn_liberar_para_execucao)
    sc_lookup(rs_tk, "SELECT COALESCE(estimated_hours, 0), skill_code 
                      FROM mro_tasks WHERE task_id = " . (int)$v_task_id);
    $var_horas_task = !empty({rs_tk}) ? (float){rs_tk[0][0]} : 0;
    $var_skill_task = !empty({rs_tk}) ? {rs_tk[0][1]} : '';

    // 1. Busca os recursos do tipo LABOR (Mao de Obra) alocados na tarefa
    $sql_resources = "SELECT DISTINCT tr.resource_code, tr.budgeted_hours
                      FROM mro_task_resources tr
                      JOIN mro_resources r ON tr.resource_code = r.resource_code
                      WHERE tr.task_id = " . (int)$v_task_id . "
                      AND r.resource_type = 'LABOR'";
    sc_lookup(rs_res, $sql_resources);

    if (!empty({rs_res})) {

        foreach ({rs_res} as $res) {
            $v_res_code = addslashes($res[0]);
            // Fallback de horas: usa o budgeted_hours ou o estimated_hours da task.
            // Trata "0"/"0.00" como vazio (budgeted zerado cai no fallback),
            // identico ao btn_liberar_para_execucao.
            $v_hours    = (!empty($res[1]) && (float)$res[1] > 0) ? (float)$res[1] : $var_horas_task;

            // Busca o skill_id da especialidade
            sc_lookup(rs_sid, "SELECT skill_id FROM mro_skills WHERE skill_code = '$v_res_code'");
            $v_skill_id = !empty({rs_sid}) ? (int){rs_sid[0][0]} : "NULL";

            // Evita duplicidade: so cria o slot se ainda nao existir para a skill
            $var_check = "SELECT 1 FROM mro_task_assignments 
                          WHERE task_id = " . (int)$v_task_id . " AND skill_id = " . $v_skill_id;
            sc_lookup(rs_dup, $var_check);

            if (empty({rs_dup})) {
                $sql_ins = "INSERT INTO mro_task_assignments 
                            (task_id, skill_id, planned_skill_id, status_code, planned_qty_hours, project_id) 
                            VALUES (" . (int)$v_task_id . ", " . $v_skill_id . ", " . $v_skill_id . ", 'NOT_STARTED', " . $v_hours . ", " . (int)$v_projeto_atual . ")";
                sc_exec_sql($sql_ins);
            }
        }

    } else {
        // 2. Fallback: sem recursos LABOR, cria uma alocacao generica
        // com o skill_code da propria task e o total de horas estimadas
        $v_skill_code = addslashes($var_skill_task);

        if (!empty($v_skill_code)) {
            sc_lookup(rs_sid2, "SELECT skill_id FROM mro_skills WHERE skill_code = '$v_skill_code'");
            $v_skill_id2 = !empty({rs_sid2}) ? (int){rs_sid2[0][0]} : "NULL";

            $var_check2 = "SELECT 1 FROM mro_task_assignments 
                           WHERE task_id = " . (int)$v_task_id . " AND skill_id = " . $v_skill_id2;
            sc_lookup(rs_dup2, $var_check2);

            if (empty({rs_dup2})) {
                $sql_ins2 = "INSERT INTO mro_task_assignments 
                             (task_id, skill_id, planned_skill_id, status_code, planned_qty_hours, project_id) 
                             VALUES (" . (int)$v_task_id . ", " . $v_skill_id2 . ", " . $v_skill_id2 . ", 'NOT_STARTED', " . $var_horas_task . ", " . (int)$v_projeto_atual . ")";
                sc_exec_sql($sql_ins2);
            }
        }
    }

    return true;
}

// FIM 

?>