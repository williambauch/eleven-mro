<?php

function mGerarPagina2XXX($pdf) {
    // =========================================================
    // Pagina 2 — gerada do editor em 21/07/2026, 17:36:04
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_2_jec_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);

    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(47.89, 50.8, '23'); // p021 - ATA - 23
    $pdf->Text(60.85, 50.8, 'A320-214'); // p022 - A/C Type - A320-214
    $pdf->Text(82.02, 50.8, 'PR-MLD'); // p023 - A/C Reg - PR-MLD
    $pdf->Text(101.07, 50.8, 'A320'); // p024 - Company - A320
    $pdf->Text(121.71, 50.8, '38.25'); // p025 - A/C Work Order - 38.25
    $pdf->Text(3.18, 50.8, '190036'); // p020 - Origin Document - 190036
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->MultiCell(206.11, 0, '• Tool Code - Description - Qty', 0, 'L', false, 1, 3.18, 71.17, true); // new-1 - ToolCode_Description_Qty - • Tool Code - Description - Qty (multilinha)
    // Fonte: 12px bold → 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(163.25, 23.02, 'N190036001'); // p009 - Card Code - N190036001
    // Fonte: 15px bold → 11.25pt
    $pdf->SetFont('helvetica', 'B', 11.25);
    $pdf->write1DBarcode('N000000000000001900360002901000000A4E001', 'C39', 3.18, 26.72, 100, 5, null, array('text' => true), 'N'); // p010 - JIC Work Order - N000000000000001900360002901000000A4E001 (barcode)
}
