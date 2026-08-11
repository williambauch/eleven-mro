<?php
// MRO-122 - Dados do diagrama de fluxo (Rotina Padrao + NRC)
// Fiel ao _DOCS/producao_manutencao/fluxo_rotina_nrc_workflow.md
// Retorna nodes (estados) e links (transicoes) com cores/formatos
function mGerarDadosFluxo()
{
    $nodes = array(
        // ===================== FLUXO NRC =====================
        // Layout: C e E na mesma coluna (sem no no meio da rota),
        // D e Z na coluna central, G e I na mesma coluna,
        // AP acima de H. Evita rotas cruzando nos.
        array('id' => 'A',  'text' => 'blank_abertura_nrc', 'sub' => 'abertura direta de NRC - MRO-122', 'x' => 90,  'y' => 40,  'color' => '#e3f2fd', 'stroke' => '#1565c0', 'shape' => 'rect'),
        array('id' => 'B',  'text' => 'DRAFT', 'sub' => 'Rascunho', 'x' => 90,  'y' => 160, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'C',  'text' => 'PENDING_ENG', 'sub' => 'Fila Engenharia', 'x' => 340, 'y' => 40,  'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'D',  'text' => 'PENDING_PROG', 'sub' => 'Aguardando Programacao', 'x' => 610, 'y' => 160, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'E',  'text' => 'PENDING_COORD', 'sub' => 'Aguardando Coordenador', 'x' => 340, 'y' => 280, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'Z',  'text' => 'Dentro do CAP?', 'sub' => 'motor O&A - mro_engine', 'x' => 610, 'y' => 40, 'color' => '#fff8e1', 'stroke' => '#f9a825', 'shape' => 'diamond'),
        array('id' => 'G',  'text' => 'PENDING_OA', 'sub' => 'Aguardando O&A', 'x' => 870, 'y' => 40,  'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'I',  'text' => 'COMMERCIAL_REVIEW', 'sub' => 'Revisao Comercial', 'x' => 870, 'y' => 400, 'color' => '#fce4ec', 'stroke' => '#c62828', 'shape' => 'rect'),
        array('id' => 'AP', 'text' => 'APPROVED', 'sub' => 'Aprovado Cliente', 'x' => 1130, 'y' => 40, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'H',  'text' => 'RELEASED', 'sub' => 'Liberado', 'x' => 1130, 'y' => 160, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'F',  'text' => 'CANCELLED', 'sub' => 'Cancelado', 'x' => 1130, 'y' => 280, 'color' => '#fce4ec', 'stroke' => '#c62828', 'shape' => 'rect'),

        // ===================== FLUXO ROTINA PADRAO =====================
        // Deslocado para baixo (y+80) para nao colidir com o fluxo NRC
        array('id' => 'RA',  'text' => 'Importacao P6 / Cadastro', 'sub' => 'ctrl_import_excel / form_public_mro_tasks', 'x' => 90,  'y' => 560, 'color' => '#e3f2fd', 'stroke' => '#1565c0', 'shape' => 'rect'),
        array('id' => 'RB',  'text' => 'PLANNED', 'sub' => 'Planejado (P6)', 'x' => 90,  'y' => 700, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'RB1', 'text' => 'NOT_STARTED', 'sub' => 'Nao Iniciado', 'x' => 90,  'y' => 840, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'RR',  'text' => 'RELEASED', 'sub' => 'Liberado', 'x' => 340, 'y' => 560, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RSV', 'text' => 'Painel Supervisor', 'sub' => 'tabs_supervisor', 'x' => 340, 'y' => 720, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RM',  'text' => 'IN_PROGRESS', 'sub' => 'Mecanico executando', 'x' => 590, 'y' => 560, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RP',  'text' => 'PAUSED', 'sub' => 'Pausa rotineira', 'x' => 590, 'y' => 840, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'RH',  'text' => 'PENDING_HANDOVER', 'sub' => 'Passagem de servico', 'x' => 590, 'y' => 980, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'RC',  'text' => 'COMPLETED', 'sub' => 'Concluido', 'x' => 840, 'y' => 560, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RPG', 'text' => 'PENDING_PROG', 'sub' => 'Programacao valida', 'x' => 840, 'y' => 980, 'color' => '#fff3e0', 'stroke' => '#e65100', 'shape' => 'rect'),
        array('id' => 'RV',  'text' => 'HH dentro do orcado?', 'sub' => 'btn_validar_prog_rotina', 'x' => 1090, 'y' => 780, 'color' => '#fff8e1', 'stroke' => '#f9a825', 'shape' => 'diamond'),
        array('id' => 'RR2', 'text' => 'RELEASED novamente', 'sub' => 'atualiza/cria assignments', 'x' => 1090, 'y' => 560, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RBL', 'text' => 'BLOCKED', 'sub' => 'is_blocked_labor', 'x' => 1090, 'y' => 980, 'color' => '#fce4ec', 'stroke' => '#c62828', 'shape' => 'rect'),
        array('id' => 'RQ',  'text' => 'SUPSIG', 'sub' => 'Assinatura Sup/Qualidade', 'x' => 1340, 'y' => 560, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect'),
        array('id' => 'RCL', 'text' => 'CLOSED', 'sub' => 'Fechado', 'x' => 1340, 'y' => 720, 'color' => '#e8f5e9', 'stroke' => '#2e7d32', 'shape' => 'rect')
    );

    $links = array(
        // ===================== FLUXO NRC =====================
        // Labels removidos (colisao entre setas opostas/paralelas):
        // - C->F, E->F, D->F, I->F (btn_cancelar redundante - 1 unico "cancelar"
        //   em E->F basta, e a legenda/icone ja comunica)
        // - E->C (btn_enviar_eng tracejada - oposta a C->E mantida)
        // - D->E (btn_enviar_coord - oposta a E->D mantida)
        // - I->G (btn_enviar_cliente tracejada - oposta a G->I mantida)
        // - G->AP auto-approve (tracejada - btn_aprovar_cliente mantido)
        array('from' => 'A', 'to' => 'B', 'label' => ''),
        array('from' => 'B', 'to' => 'C', 'label' => 'btn_enviar_eng'),
        array('from' => 'B', 'to' => 'D', 'label' => ''),
        array('from' => 'C', 'to' => 'D', 'label' => 'btn_enviar_prog'),
        array('from' => 'C', 'to' => 'E', 'label' => 'btn_enviar_coord'),
        array('from' => 'C', 'to' => 'F', 'label' => ''),
        array('from' => 'E', 'to' => 'C', 'label' => '', 'dash' => true, 'color' => '#ff6f00'),
        array('from' => 'E', 'to' => 'D', 'label' => 'btn_enviar_prog'),
        array('from' => 'E', 'to' => 'F', 'label' => 'cancelar'),
        array('from' => 'D', 'to' => 'E', 'label' => '', ),
        array('from' => 'D', 'to' => 'F', 'label' => ''),
        array('from' => 'D', 'to' => 'Z', 'label' => ''),
        array('from' => 'Z', 'to' => 'H', 'label' => 'auto', 'color' => '#2e7d32'),
        array('from' => 'Z', 'to' => 'G', 'label' => 'Nao'),
        array('from' => 'G', 'to' => 'AP', 'label' => 'btn_aprovar_cliente'),
        array('from' => 'G', 'to' => 'I', 'label' => 'btn_reprovar_cliente'),
        array('from' => 'G', 'to' => 'AP', 'label' => '', 'dash' => true, 'color' => '#2e7d32'),
        array('from' => 'AP', 'to' => 'H', 'label' => 'btn_liberar_para_execucao (grid)'),
        array('from' => 'I', 'to' => 'G', 'label' => '', 'dash' => true, 'color' => '#ff6f00'),
        array('from' => 'I', 'to' => 'D', 'label' => '', 'dash' => true, 'color' => '#ff6f00'),
        array('from' => 'I', 'to' => 'F', 'label' => ''),

        // ===================== FLUXO ROTINA PADRAO =====================
        array('from' => 'RA', 'to' => 'RB', 'label' => ''),
        array('from' => 'RB', 'to' => 'RB1', 'label' => ''),
        array('from' => 'RB1', 'to' => 'RR', 'label' => 'btn_liberar_para_execucao (grid)'),
        array('from' => 'RR', 'to' => 'RSV', 'label' => 'cria assignments por skill (LABOR)', 'dash' => true),
        array('from' => 'RSV', 'to' => 'RM', 'label' => 'dispatch / alocacao'),
        array('from' => 'RM', 'to' => 'RM', 'label' => ''),
        array('from' => 'RM', 'to' => 'RC', 'label' => 'concluido'),
        array('from' => 'RM', 'to' => 'RH', 'label' => 'finalizar turno / repasse'),
        array('from' => 'RM', 'to' => 'RP', 'label' => 'pausa rotineira'),
        array('from' => 'RP', 'to' => 'RM', 'label' => ''),
        array('from' => 'RH', 'to' => 'RPG', 'label' => 'passagem de servico (MRO-120)'),
        array('from' => 'RPG', 'to' => 'RV', 'label' => 'btn_validar_prog_rotina'),
        array('from' => 'RV', 'to' => 'RR2', 'label' => 'Sim', 'color' => '#2e7d32'),
        array('from' => 'RR2', 'to' => 'RM', 'label' => ''),
        array('from' => 'RV', 'to' => 'RBL', 'label' => 'Nao (estouro HH)'),
        array('from' => 'RBL', 'to' => 'RPG', 'label' => 'ajusta orcamento / reprograma', 'dash' => true, 'color' => '#ff6f00'),
        array('from' => 'RC', 'to' => 'RQ', 'label' => ''),
        array('from' => 'RQ', 'to' => 'RCL', 'label' => '')
    );

    return array('nodes' => $nodes, 'links' => $links);
}
