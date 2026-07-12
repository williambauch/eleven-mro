<?php

function fn_calcular_oa_nrc($v_task_id, $v_projeto_atual)
{
    // ====================================================================
    // MOTOR DE CÁLCULO OVER & ABOVE (INTEGRADO NA MRO_TASKS COM LOTES)
    // ====================================================================

    // 1. Carrega as Regras do Contrato do Projeto
    $sql_contrato = "SELECT is_cap_zero, cap_hh_limit, cap_material_type, cap_material_limit 
                     FROM mro_projects WHERE project_id = $v_projeto_atual";
    sc_lookup(rs_cap, $sql_contrato);

    if (empty({rs_cap})) return false; // Aborta se não achar o projeto

    $is_cap_zero   = ({rs_cap[0][0]} == 't' || {rs_cap[0][0]} == 'true' || {rs_cap[0][0]} == 1);
    $cap_hh_limit  = (float) {rs_cap[0][1]};
    $cap_mat_type  = {rs_cap[0][2]}; 
    $cap_mat_limit = (float) {rs_cap[0][3]};

    $hh_oa_final  = 0;
    $mat_oa_final = 0;

    // Busca os valores orçamentados para esta NRC específica
    // Nota: Ajuste 'estimated_hours' e 'estimated_material_cost' para os nomes reais das suas colunas na mro_tasks
    sc_lookup(rs_totais_nrc, "SELECT COALESCE(estimated_hours, 0), COALESCE(estimated_material_cost, 0) 
                              FROM mro_tasks WHERE task_id = $v_task_id");
                              
    $hh_nrc_atual  = (float) {rs_totais_nrc[0][0]};
    $mat_nrc_atual = (float) {rs_totais_nrc[0][1]};

    // ====================================================================
    // 2. O MOTOR DE DECISÃO (TRANSBORDO E ISOLAMENTO)
    // ====================================================================

    if ($is_cap_zero) {
        // CAP ZERO: Curto-circuito. 100% do valor vai para a faturação extra.
        $hh_oa_final  = $hh_nrc_atual;
        $mat_oa_final = $mat_nrc_atual;
        
    } else {
        // 2.1 - CÁLCULO DE MÃO DE OBRA (HH) - Regra do Transbordo Global
        // Soma todas as horas de OUTRAS NRCs deste projeto
        $sql_hh_acumulado = "SELECT COALESCE(SUM(estimated_hours), 0) FROM mro_tasks 
                             WHERE project_id = $v_projeto_atual 
                             AND task_id <> $v_task_id 
                             AND is_nrc = true 
                             AND status_code NOT IN ('CANCELLED', 'REJECTED')";
        sc_lookup(rs_hh_acum, $sql_hh_acumulado);
        
        $hh_disponiveis = $cap_hh_limit - (float) {rs_hh_acum[0][0]};
        if ($hh_disponiveis < 0) $hh_disponiveis = 0;
        
        if ($hh_nrc_atual > $hh_disponiveis) {
            $hh_oa_final = $hh_nrc_atual - $hh_disponiveis;
        }

        // 2.2 - CÁLCULO DE MATERIAIS
        if ($cap_mat_type == 'PACKAGE') {
            // Regra do Transbordo Global para o pacote inteiro do projeto
            $sql_mat_acum = "SELECT COALESCE(SUM(estimated_material_cost), 0) FROM mro_tasks 
                             WHERE project_id = $v_projeto_atual 
                             AND task_id <> $v_task_id 
                             AND is_nrc = true 
                             AND status_code NOT IN ('CANCELLED', 'REJECTED')";
            sc_lookup(rs_mat_acum, $sql_mat_acum);
            
            $mat_disponivel = $cap_mat_limit - (float) {rs_mat_acum[0][0]};
            if ($mat_disponivel < 0) $mat_disponivel = 0;
            
            if ($mat_nrc_atual > $mat_disponivel) {
                $mat_oa_final = $mat_nrc_atual - $mat_disponivel;
            }
            
        } elseif ($cap_mat_type == 'LINE_ITEM') {
            // Regra Line Item: Varre peça a peça na tabela de materiais da tarefa
            // Utilizando o seu campo 'planned_qty' multiplicado pelo custo unitário do material
            $sql_items = "SELECT tm.material_id, (tm.planned_qty * m.unit_cost) as total_linha 
                          FROM mro_task_materials tm
                          JOIN mro_materials m ON tm.material_id = m.material_id
                          WHERE tm.task_id = $v_task_id";
            sc_lookup(rs_items, $sql_items);
            
            if (!empty({rs_items})) {
                foreach ({rs_items} as $item) {
                    $custo_linha = (float) $item[1];
                    if ($custo_linha > $cap_mat_limit) {
                        // Soma apenas o excedente desta peça
                        $mat_oa_final += ($custo_linha - $cap_mat_limit);
                    }
                }
            }
        }
    }

    // ====================================================================
    // 3. A LÓGICA DE AGRUPAMENTO (CRIAÇÃO/VÍNCULO DO LOTE O&A)
    // ====================================================================

    if ($hh_oa_final > 0 || $mat_oa_final > 0) {
        
        // Procura se já existe um Lote O&A aberto (DRAFT) para este projeto
        sc_lookup(rs_batch, "SELECT batch_id FROM mro_oa_batches WHERE project_id = $v_projeto_atual AND status = 'DRAFT'");
        
        if (!empty({rs_batch})) {
            $v_batch_id = {rs_batch[0][0]};
        } else {
            // Não achou? Cria um lote novo para colocar esta e as próximas NRCs
            sc_exec_sql("INSERT INTO mro_oa_batches (project_id, status) VALUES ($v_projeto_atual, 'DRAFT')");
            sc_lookup(rs_new_batch, "SELECT MAX(batch_id) FROM mro_oa_batches WHERE project_id = $v_projeto_atual");
            $v_batch_id = {rs_new_batch[0][0]};
        }
        
        // Atualiza a Task/NRC amarrando-a ao Lote e definindo o valor exato a ser faturado
        sc_exec_sql("UPDATE mro_tasks 
                     SET oa_hours = $hh_oa_final, 
                         oa_material_cost = $mat_oa_final,
                         oa_batch_id = $v_batch_id,
                         status_code = 'PENDING_OA_APPROVAL',
                         is_oa = true 
                     WHERE task_id = $v_task_id");
    } else {
        // 100% coberto pelo contrato. Zera os campos e liberta de qualquer lote.
        sc_exec_sql("UPDATE mro_tasks 
                     SET oa_hours = 0, 
                         oa_material_cost = 0,
                         oa_batch_id = NULL,
                         is_oa = false 
                     WHERE task_id = $v_task_id");
    }

    return true;
}
