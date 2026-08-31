<?php

function mGerarPagina6XXX($pdf) {
    // =========================================================
    // Pagina 6 — gerada do editor em 28/07/2026, 09:08:10
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_1_jic_PADRAO_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Fonte: 18px bold → 13.5pt
    $pdf->SetFont('helvetica', 'B', 13.5);
    $pdf->Text(175.68, 17.73, '370021'); // p003 - Document - Código do Cartão - 370021
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->write1DBarcode('R000000000000003700210002945000000A4I000', 'C39', 39.95, 24.08, 100, 5, null, array('text' => true), 'N'); // p010 - Código de Barras - R000000000000003700210002945000000A4I000 (barcode)
    // Fonte: 14px bold → 10.5pt
    $pdf->SetFont('helvetica', 'B', 10.5);
    $pdf->Text(161.4, 29.1, 'APU ON'); // p006 - Phase - APU ON
    // Fonte: 14px normal → 10.5pt
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->Text(32.28, 45.24, '09.26'); // p008 - A/C Work Order: - 09.26
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(49.21, 0, '20-100-00', 0, 'L', false, 1, 84.93, 45.77, true); // p011 - Origin Document: - 20-100-00 (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(20.64, 0, 'SJK', 0, 'L', false, 1, 152.93, 45.77, true); // p013 - Station: - SJK (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->MultiCell(25.14, 0, 'RIV02/26', 0, 'L', false, 1, 189.44, 45.77, true); // p015 - Project: - RIV02/26 (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(11.38, 54.77, '21'); // p017 - ATA: - 21
    $pdf->MultiCell(21.43, 0, 'RIV', 0, 'L', false, 1, 53.45, 54.77, true); // p019 - Company: - RIV (multilinha)
    $pdf->Text(94.19, 54.77, 'B737-7BC'); // p021 - A/C Type - B737-7BC
    $pdf->Text(142.61, 54.77, '32575'); // p023 - A/C SN: - 32575
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(191.56, 54.77, 'PR-BBS'); // p025 - A/C Reg: - PR-BBS
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(101.34, 0, '1 Aviônica -- 10 h', 0, 'L', false, 1, 23.28, 63.76, true); // p027 - Skills - 1 Aviônica -- 10 h (multilinha)
    $pdf->Text(193.15, 63.76, '1500FH'); // p030 - Frequencia - 1500FH
    // Fonte: 12px bold → 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(159.54, 64.03, '2'); // p029 - Estimated Hours - 2
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(182.3, 0, '192CL 192CR 324AAL 324ABL 334GB 334MB 344GB 344MB 431CL 441CR 571BB 571DB 671BB 671DB', 0, 'L', false, 1, 31.22, 74.88, true); // p033 - Access Panels: - 192CL 192CR 324AAL 324ABL 334GB 334MB 344GB 344MB 431CL 441CR 571BB 571DB 671BB 671DB (multilinha)
    // Fonte: 14px normal → 10.5pt
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->MultiCell(194.47, 0, 'PERFORM A GENERAL VISUAL INSPECTION OF THE BONDING STRAPS AT THE FOLLOW', 0, 'L', false, 1, 19.05, 86.25, true); // p036 - Activity - PERFORM A GENERAL VISUAL INSPECTION OF THE BONDING STRAPS AT THE FOLLOW (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(36.78, 0, '20-100-00', 0, 'L', false, 1, 21.96, 102.66, true); // p038 - MPD Ref - 20-100-00 (multilinha)
    $pdf->MultiCell(53.45, 0, '133 134 190 192 324 325 334', 0, 'L', false, 1, 83.87, 102.66, true); // p040 - Zone&Area - 133 134 190 192 324 325 334 (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->MultiCell(56.62, 0, '20-100-00-0', 0, 'L', false, 1, 157.16, 102.66, true); // p042 - DOC Ref: - 20-100-00-0 (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(80.96, 0, 'In accordance With: (Reference)', 0, 'L', false, 1, 1.32, 121.44, true); // p032 - In accordance With: (Reference) - In accordance With: (Reference) (multilinha)
    $pdf->MultiCell(52.65, 0, 'Revisão da Referência', 0, 'L', false, 1, 84.4, 121.44, true); // p002 - Revisão da Referência - Revisão da Referência (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(158.49, 122.5, 'X'); // p009 - Discrepancy Found (YES) - X
    $pdf->Text(184.41, 122.5, 'X'); // new-1 - Discrepancy Found (NO) - X
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->MultiCell(136.26, 0, 'Action Taken', 0, 'L', false, 1, 1.32, 144.73, true); // p035 - Action Taken - Action Taken (multilinha)
    $pdf->Text(152.14, 133.09, '01 List if applicable'); // p039 - 01 List if applicable - 01 List if applicable
    $pdf->Text(152.14, 141.29, '02 List if applicable'); // new-3 - 02 List if applicable (02) - 02 List if applicable
    $pdf->Text(152.14, 149.49, '03 List if applicable'); // new-4 - 03 List if applicable 03 - 03 List if applicable
    $pdf->Text(152.14, 157.96, '04 List if applicable'); // new-5 - 04 List if applicable 04 - 04 List if applicable
    $pdf->Text(152.14, 166.16, '05 List if applicable'); // new-6 - 05 List if applicable 05 - 05 List if applicable
    $pdf->Text(152.14, 174.63, '06 List if applicable'); // new-7 - 06 List if applicable 06 - 06 List if applicable
    $pdf->Text(152.14, 182.83, '07 List if applicable'); // new-8 - 07 List if applicable 07 - 07 List if applicable
    $pdf->MultiCell(50.27, 0, 'P/N Out:', 0, 'L', false, 1, 16.67, 190.24, true); // p001 - P/N Out: - P/N Out: (multilinha)
    $pdf->MultiCell(52.65, 0, 'S/N Out', 0, 'L', false, 1, 84.14, 190.24, true); // p004 - S/N Out - S/N Out (multilinha)
    $pdf->MultiCell(50.54, 0, 'P/N In', 0, 'L', false, 1, 16.67, 200.55, true); // p005 - P/N In - P/N In (multilinha)
    $pdf->MultiCell(52.92, 0, 'S/N In', 0, 'L', false, 1, 84.14, 200.55, true); // p007 - S/N In - S/N In (multilinha)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(159.28, 201.08, 'X'); // p028 - Were Calibrating Tools Used (YES) - X
    $pdf->Text(191.29, 201.08, 'X'); // p031 - Were Calibrating Tools Used (NO) - X
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(1.32, 223.84, 'Performed By'); // p012 - Performed By - Performed By
    $pdf->Text(69.59, 223.84, 'Assinatura do IIO se necessário'); // p014 - Assinatura do IIO se necessário - Assinatura do IIO se necessário
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(159.81, 226.75, 'X'); // p034 - Planned RII ITEM (YES) - X
    $pdf->Text(191.82, 226.75, 'X'); // p037 - Planned RII ITEM (NO) - X
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(11.64, 230.98, '27/07/2026'); // p016 - Date Performed By - 27/07/2026
    $pdf->Text(79.64, 230.98, '27/07/2026'); // new-2 - Date Assinatura do IIO se necessário - 27/07/2026
    $pdf->MultiCell(54.5, 0, 'Identificação', 0, 'L', false, 1, 159.81, 246.06, true); // p022 - Identificação - Identificação (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(1.06, 254.79, 'Customer Full Name'); // p018 - Customer Full Name - Customer Full Name
    $pdf->Text(69.59, 254.79, 'Assinatura do Cliente'); // p020 - Assinatura do Cliente - Assinatura do Cliente
    $pdf->MultiCell(103.98, 0, 'Motivo do Diferimento', 0, 'L', false, 1, 33.6, 264.32, true); // p024 - Motivo do Diferimento - Motivo do Diferimento (multilinha)
    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(159.81, 264.32, '27/07/2026'); // p026 - Data Reason for Deferment - 27/07/2026
}
