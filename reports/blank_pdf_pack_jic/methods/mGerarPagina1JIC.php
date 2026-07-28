<?php

function mGerarPagina1JIC($pdf, $data) {
    // =========================================================
    // Pagina 1 — Capa JIC Padrão (Job Instruction Card)
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // Baseado em mGerarPagina6.php (capa padrão para rotinas)
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_1_jic_PADRAO_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Fonte: 18px bold → 13.5pt
    $pdf->SetFont('helvetica', 'B', 13.5);
    $pdf->Text(175.68, 17.73, htmlspecialchars($data['task_code'])); // p003 - Document - Codigo do Cartao
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->write1DBarcode(htmlspecialchars($data['barcode']), 'C39', 39.95, 24.08, 100, 5, null, array('text' => true), 'N'); // p010 - Codigo de Barras
    // Fonte: 14px bold → 10.5pt
    $pdf->SetFont('helvetica', 'B', 10.5);
    $pdf->Text(161.4, 29.1, htmlspecialchars(trim($data['phase_code']) ?: '')); // p006 - Phase
    // Fonte: 14px normal → 10.5pt
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->Text(32.28, 45.24, htmlspecialchars($data['ac_work_order'])); // p008 - A/C Work Order:
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(49.21, 0, htmlspecialchars($data['origin_document']), 0, 'L', false, 1, 84.93, 45.77, true); // p011 - Origin Document: (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(20.64, 0, htmlspecialchars($data['project']), 0, 'L', false, 1, 152.93, 45.77, true); // p013 - Station: (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->MultiCell(25.14, 0, htmlspecialchars($data['project']), 0, 'L', false, 1, 189.44, 45.77, true); // p015 - Project: (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(11.38, 54.77, htmlspecialchars($data['ata_chapter'])); // p017 - ATA:
    $pdf->MultiCell(19.31, 0, htmlspecialchars(substr($data['customer_name'], 0, 12)), 0, 'L', false, 1, 54.24, 54.77, true); // p019 - Company: (multilinha, limitado 12 chars)
    $pdf->Text(94.19, 54.77, htmlspecialchars($data['model'])); // p021 - A/C Type
    $pdf->Text(142.61, 54.77, htmlspecialchars($data['aircraft_esn'])); // p023 - A/C SN:
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(191.56, 54.77, htmlspecialchars($data['aircraft_registration'])); // p025 - A/C Reg:

    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(101.34, 0, htmlspecialchars($data['skill_resources']), 0, 'L', false, 1, 23.28, 63.76, true); // p027 - Skills (multilinha)
    $pdf->Text(193.15, 63.76, htmlspecialchars(trim($data['frequency']) ?: '')); // p030 - Frequencia
    // Fonte: 12px bold → 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(159.54, 64.03, htmlspecialchars($data['estimated_hours'])); // p029 - Estimated Hours
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(182.3, 0, htmlspecialchars($data['access_panels']), 0, 'L', false, 1, 31.22, 74.88, true); // p033 - Access Panels: (multilinha)

    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->MultiCell(194.47, 0, htmlspecialchars($data['corrective_action']), 0, 'L', false, 1, 19.05, 86.25, true); // Activity/Action Taken

    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(36.78, 0, htmlspecialchars($data['amm_reference']), 0, 'L', false, 1, 21.96, 102.66, true); // p038 - MPD Ref (multilinha)
    $pdf->MultiCell(53.45, 0, htmlspecialchars($data['zone_area']), 0, 'L', false, 1, 83.87, 102.66, true); // p040 - Zone&Area (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->MultiCell(56.62, 0, htmlspecialchars($data['document_reference']), 0, 'L', false, 1, 157.16, 102.66, true); // p042 - DOC Ref: (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(80.96, 0, htmlspecialchars($data['amm_reference'] ?: ''), 0, 'L', false, 1, 1.32, 121.44, true); // p032 - In accordance With: (Reference)
    $pdf->MultiCell(52.65, 0, '', 0, 'L', false, 1, 84.4, 121.44, true); // p002 - Revisao da Referencia (em branco)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(158.49, 122.5, $data['is_nrc'] ? 'X' : ''); // p009 - Discrepancy Found (YES)
    $pdf->Text(184.41, 122.5, $data['is_nrc'] ? '' : 'X'); // new-1 - Discrepancy Found (NO)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(152.14, 133.09, $data['prev_nrc_first'] ?: ''); // p039 - 01 List if applicable (primeira NRC)
    $pdf->Text(152.14, 141.29, $data['prev_nrc_last'] ?: ''); // new-3 - 02 List if applicable (ultima NRC)
    $pdf->MultiCell(136.26, 0, htmlspecialchars($data['corrective_action'] ?: ''), 0, 'L', false, 1, 1.32, 144.73, true); // p035 - Action Taken (multilinha)
    $pdf->Text(152.14, 149.49, ''); // new-4 - 03 List if applicable
    $pdf->Text(152.14, 157.96, ''); // new-5 - 04 List if applicable
    $pdf->Text(152.14, 166.16, ''); // new-6 - 05 List if applicable
    $pdf->Text(152.14, 174.63, ''); // new-7 - 06 List if applicable
    $pdf->Text(152.14, 182.83, ''); // new-8 - 07 List if applicable
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(50.27, 0, htmlspecialchars($data['pn_removido'] ?: ''), 0, 'L', false, 1, 16.67, 190.24, true); // p001 - P/N Out: (multilinha)
    $pdf->MultiCell(52.65, 0, htmlspecialchars($data['sn_removido'] ?: ''), 0, 'L', false, 1, 84.14, 190.24, true); // p004 - S/N Out (multilinha)
    $pdf->MultiCell(50.54, 0, htmlspecialchars($data['pn_instalado'] ?: ''), 0, 'L', false, 1, 16.67, 200.55, true); // p005 - P/N In (multilinha)
    $pdf->MultiCell(52.92, 0, htmlspecialchars($data['sn_instalado'] ?: ''), 0, 'L', false, 1, 84.14, 200.55, true); // p007 - S/N In (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(159.28, 201.08, $data['has_tools'] ? 'X' : ''); // p028 - Were Calibrating Tools Used (YES)
    $pdf->Text(191.29, 201.08, $data['has_tools'] ? '' : 'X'); // p031 - Were Calibrating Tools Used (NO)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(1.32, 223.84, htmlspecialchars($data['executor_name'] ?: '')); // p012 - Performed By
    $pdf->Text(69.59, 223.84, ''); // p014 - Assinatura do IIO se necessario (em branco)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(159.81, 226.75, $data['is_rii'] ? 'X' : ''); // p034 - Planned RII ITEM (YES)
    $pdf->Text(191.82, 226.75, $data['is_rii'] ? '' : 'X'); // p037 - Planned RII ITEM (NO)
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(11.64, 230.98, ''); // p016 - Date Performed By (em branco)
    $pdf->Text(79.64, 230.98, ''); // new-2 - Date Assinatura do IIO se necessario (em branco)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(54.5, 0, htmlspecialchars($data['task_code'] ?: ''), 0, 'L', false, 1, 159.81, 246.06, true); // p022 - Identificacao (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(1.06, 254.79, htmlspecialchars($data['customer_name'] ?: '')); // p018 - Customer Full Name
    $pdf->Text(69.59, 254.79, ''); // p020 - Assinatura do Cliente (em branco)
    $pdf->MultiCell(103.98, 0, htmlspecialchars($data['deferment_reason'] ?: ''), 0, 'L', false, 1, 33.6, 264.32, true); // p024 - Motivo do Diferimento (multilinha)
    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(159.81, 264.32, ''); // p026 - Data Reason for Deferment (em branco)
}
