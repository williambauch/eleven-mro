<?php

function mGerarPagina1JIC($pdf, $data)
{
    // =========================================================
        // Pagina 1 — JIC (Job Instruction Card)
        // Dimensoes LETTER: 215,9 x 279,4 mm
        // Gerado do editor em 22/07/2026 — dados dinamicos
        // =========================================================

        $pdf->AddPage();

        // --- Imagem de fundo ---
        $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_1_jic_trim.png';
        $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
        $pdf->setPageMark();

        // Extrai valores do array de dados
        $var_type          = trim($data['type']);
        $var_task_code     = trim($data['task_code']);
        $var_ac_work_order = trim($data['ac_work_order']);
        $var_reg           = trim($data['aircraft_registration']);
        $var_customer      = trim($data['customer_name']);
        $var_ata           = trim($data['ata_chapter']);
        $var_project       = trim($data['project']);
        $var_origin_doc    = trim($data['origin_document']);
        $var_doc_ref       = trim($data['document_reference']);
        $var_model         = trim($data['model']);
        $var_esn           = trim($data['aircraft_esn']);
        $var_discrepancy   = trim($data['discrepancy_title']);
        $var_corrective    = trim($data['corrective_action_description']);
        $var_skill         = $data['skill_resources'] ?: $data['skill_code'];
        $var_zone          = trim($data['zone_area']);
        $var_originated_by = trim($data['originated_by_name']);
        $var_est_hours     = trim($data['estimated_hours']);
        $var_barcode       = trim($data['barcode']);
        $var_amm_ref       = trim($data['amm_reference']);
        $var_defer_reason  = trim($data['deferment_reason']);
        $var_is_rii        = $data['is_rii'];
        $var_defer_status  = $data['deferment_status'];
        $var_has_tools     = (int)$data['has_tools'];

        // Fonte: 12px normal -> 9pt
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Text(42.33, 35.45, $var_customer); // p025 - Empresa - A320
        // Fonte: 14px bold -> 10.5pt
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->Text(79.38, 37.31, $var_doc_ref); // p029 - Documento de Origem N° 2 - 0672100000189
        // Fonte: 13px bold -> 9.75pt
        $pdf->SetFont('helvetica', 'B', 9.75);
        $pdf->Text(79.38, 33.07, $var_origin_doc); // p023 - Documento de Origem N° 1 - 190036
        // Fonte: 11px bold -> 8.25pt
        $pdf->SetFont('helvetica', 'B', 8.25);
        $pdf->Text(179.12, 143.14, $data['is_nrc'] ? 'X' : ''); // p086 - Nova NR Aberta (S) - X
        // Fonte: 14px bold -> 10.5pt
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->Text(1.59, 35.45, $var_reg); // p024 - Registro da Aeronave - PR-MLD
        // Fonte: 13px normal -> 9.75pt
        $pdf->SetFont('helvetica', '', 9.75);
        $pdf->Text(138.64, 35.45, $var_ata); // p026 - ata - 23
        // Fonte: 14px bold -> 10.5pt
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->Text(162.98, 33.6, $var_project); // p028 - projeto - AVO03/25
        // Fonte: 15px bold -> 11.25pt
        $pdf->SetFont('helvetica', 'B', 11.25);
        $pdf->Text(162.98, 20.37, $var_task_code); // p011 - Documento N° - N190036001
        // Fonte: 13px normal -> 9.75pt
        $pdf->SetFont('helvetica', '', 9.75);
        $pdf->Text(162.98, 7.14, $var_ac_work_order); // p005 - N° da Ordem de Serviço - 38.25
        // Fonte: 15px normal -> 11.25pt
        $pdf->SetFont('helvetica', '', 11.25);
        $pdf->Text(167.22, 126.47, $var_est_hours); // p060 - Horas Estimadas (Homens/Hora - 2
        // Fonte: 10px normal -> 7.5pt
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->MultiCell(142.61, 0, $var_discrepancy, 0, 'L', false, 1, 1.59, 47.63, true); // p034 - Discrepancy - LH WING - FLAPTRACK 2 - THE DISCHARGER-STATIC IS DAMAGED.
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(166.69, 154.25, '* 01 List if applicable'); // new-1 - Indicar se aplicável 01 - 01 List if applicable
        $pdf->Text(166.69, 161.66, '* 02 List if applicable'); // new-2 - Indicar se aplicável 02 - 02 List if applicable
        // Fonte: 11px bold -> 8.25pt
        $pdf->SetFont('helvetica', 'B', 8.25);
        $pdf->Text(203.46, 180.18, !$var_has_tools ? 'X' : ''); // p114 - Ferramentas Calibráveis (N) - X
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(162.72, 114.04, $var_originated_by); // p053 - Originado por - PALOMA GODOY DA SILVA SANT
        $pdf->Text(162.72, 102.39, $var_zone); // p050 - Area/Zona - Wing LH
        $pdf->MultiCell(52.12, 0, $var_skill, 0, 'L', false, 1, 162.72, 73.82, true); // p042 - Especialidade - 1 A4 -- 2 h (multilinha)
        $pdf->Text(162.72, 61.65, $var_esn); // p039 - Serial Number - 3601
        $pdf->Text(162.72, 50.01, $var_model); // p036 - Tipo de Aeronave - A320-214
        // Fonte: 10px normal -> 7.5pt
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->Text(1.59, 263, $var_defer_reason); // p125 - Motivo do Diferimento - Texto Motivo do Diferimento
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->MultiCell(147.64, 0, $data['corrective_action'], 0, 'L', false, 1, 1.59, 179.39, true); // p080 - Ação Executada - Texto de Ação Executada (multilinha)
        // Fonte: 9px normal -> 6.75pt
        $pdf->SetFont('helvetica', '', 6.75);
        $pdf->Text(99.48, 166.42, '* Revisão da Referência'); // p077 - Revisão da Referência - (Revisão da Referência:)
        $pdf->Text(1.59, 166.42, $var_amm_ref); // p075 - De acordo com: (Referencia) - De acordo com: (Referencia)
        // Fonte: 16px bold -> 12pt
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(45.77, 4.5, $var_type); // p004 - - - NON ROUTINE JOB INSTRUCTION CARD CFEF
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(18.52, 214.58, $data['pn_instalado'] ?: ' '); // p097 - - - P/N Instalado
        $pdf->Text(98.16, 214.58, $data['sn_instalado'] ?: ' '); // p098 - - - S/N Instalado
        $pdf->Text(18.52, 206.9, $data['pn_removido'] ?: ' '); // p093 - - - P/N Removido
        $pdf->Text(98.16, 206.9, $data['sn_removido'] ?: ' '); // p094 - - - S/N Removido
        // Fonte: 11px bold -> 8.25pt
        $pdf->SetFont('helvetica', 'B', 8.25);
        $pdf->Text(42.07, 229.13, !empty($var_defer_status) && $var_defer_status == 'approved' ? 'X' : ''); // new-7 - Diferimento - X
        $pdf->Text(6.35, 229.13, 'X'); // new-6 - Aprovação - X
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(82.02, 248.71, '* Aqui Assinatura do Cliente'); // p121 - Assinatura do Cliente - Aqui Assinatura do Cliente
        $pdf->Text(82.02, 234.95, '(' . $var_customer . ')'); // p111 - - - (Nome Completo do Cliente)
        $pdf->Text(162.72, 249.5, '* Assinatura do IIO se necessário:'); // p122 - Assinatura do IIO se necessário - Assinatura do IIO se necessário:
        $pdf->Text(162.72, 204.79, $data['executor_name'] ?: ' '); // p090 - Executado por - Nome do Executor
        // Fonte: 11px bold -> 8.25pt
        $pdf->SetFont('helvetica', 'B', 8.25);
        $pdf->Text(201.35, 231.51, !$var_is_rii ? 'X' : ''); // new-5 - Item IIO planejado (N) - X
        $pdf->Text(179.12, 231.51, $var_is_rii ? 'X' : ''); // new-4 - Item IIO planejado (S) - X
        $pdf->Text(201.61, 143.14, !$data['is_nrc'] ? 'X' : ''); // p082 - Nova NR Aberta (N) - X
        $pdf->Text(177.54, 180.18, $var_has_tools ? 'X' : ''); // p113 - Ferramentas Calibráveis (S) - X
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->write1DBarcode($var_barcode, 'C39', 45.77, 15.35, 100, 5, null, array('text' => true), 'N'); // p010 - NON ROUTINE Código de Barras - N000000000000001900360002901000000A4I001 (barcode)
        // Fonte: 10px normal -> 7.5pt
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->MultiCell(142.08, 0, $var_amm_ref, 0, 'L', false, 1, 1.59, 128.59, true); // p055 - Engineering Instruction: (Instrução de Engenharia) - Engineering Instruction: (multilinha)
        $pdf->MultiCell(142.08, 0, $var_corrective, 0, 'L', false, 1, 1.59, 87.05, true); // p043 - Corrective Action: (Ação corretiva) - REPLACE THE DISCHARGER-STATIC IAW AMM 23-61-41-000-001-A AND 23-61-41-400-001-A. (multilinha)
        // Fonte: 12px bold -> 9pt
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Text(3.44, 248.71, $var_task_code); // p120 - ID Identificação - ID (Identificação)
        // Fonte: 14px normal -> 10.5pt
        $pdf->SetFont('helvetica', '', 10.5);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetXY(183.09, 258.33);
        $pdf->Cell(18, 3.7, date('d/m/Y'), 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p099 - Data Assinatura do IIO - 12/11/2026 (date)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetXY(132.03, 258.33);
        $pdf->Cell(18, 3.7, date('d/m/Y'), 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p102 - Data Motivo do Diferimento - 10/11/2026 (date)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetXY(183.09, 214.4);
        $pdf->Cell(18, 3.7, date('d/m/Y'), 0, 0, 'L', true, '', 0, false, 'T', 'T'); // new-3 - Data - 10/11/2026 (date)
}
