<?php

function mGerarPagina4XXX($pdf) {
    // =========================================================
    // Pagina 4 — gerada do editor em 22/07/2026, 11:06:50
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_4_shift_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(173.57, 6.61, 'N190036001'); // p007 - Card Code - N190036001
    $pdf->Text(123.03, 6.61, 'PR-MLD'); // p005 - A/C Reg - PR-MLD
    $pdf->Text(66.41, 6.61, 'A320-214'); // p003 - A/C Type - A320-214
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 26.01);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p017 - Data_action - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 41.28, 'MTR'); // p022 - MTR - MTR
    $pdf->Text(202.94, 41.28, 'SHIFT'); // p030 - SHIFT - SHIFT
    $pdf->Text(182.56, 54.5, 'STAMP'); // p024 - STAMP - STAMP
    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(186.53, 61.16);
    $pdf->Cell(18, 3.44, 'Data', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p020 - Data_stamp - Data (date)
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 73.64);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p01702 - Data_action 02 - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 88.9, 'MTR'); // p02202 - MTR 02 - MTR
    $pdf->Text(202.94, 88.9, 'SHIFT'); // p03002 - SHIFT 02 - SHIFT
    $pdf->Text(182.56, 102.13, 'STAMP'); // p02402 - STAMP 02 - STAMP
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.12, 23.55, true); // p014 - ACTION - ACTION (multilinha)
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.12, 70.91, true); // p01402 - ACTION 02 - ACTION (multilinha)
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.65, 112.18, true); // p0140203 - ACTION 03 - ACTION (multilinha)
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 114.91);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p0170203 - Data_action 03 - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 130.18, 'MTR'); // p0220203 - MTR 03 - MTR
    $pdf->Text(202.94, 130.18, 'SHIFT'); // p0300203 - SHIFT 03 - SHIFT
    $pdf->Text(182.56, 143.4, 'STAMP'); // p0240203 - STAMP 03 - STAMP
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.12, 153.99, true); // new-4 - ACTION 04 - ACTION (multilinha)
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 156.72);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // new-5 - Data_action 04 - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 171.98, 'MTR'); // new-1 - MTR 02 04 - MTR
    $pdf->Text(202.94, 171.98, 'SHIFT'); // new-2 - SHIFT 04 - SHIFT
    $pdf->Text(182.3, 185.21, 'STAMP'); // new-3 - STAMP 04 - STAMP
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.12, 195.26, true); // new-8 - ACTION 05 - ACTION (multilinha)
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 197.73);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // new-9 - Data_action 05 - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 213.25, 'MTR'); // new-10 - MTR 05 - MTR
    $pdf->Text(202.94, 213.25, 'SHIFT'); // new-6 - SHIFT 05 - SHIFT
    $pdf->Text(182.56, 226.48, 'STAMP'); // new-7 - STAMP 05 - STAMP
    // Fonte: 18px normal → 13.5pt
    $pdf->SetFont('helvetica', '', 13.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(170.92, 238.74);
    $pdf->Cell(18, 4.76, '(Data)', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // new-13 - Data_action 06 - (Data) (date)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(177.01, 251.35, 'MTR'); // new-14 - MTR 06 - MTR
    $pdf->Text(202.94, 251.35, 'SHIFT'); // new-15 - SHIFT 06 - SHIFT
    $pdf->Text(182.56, 264.58, 'STAMP'); // new-16 - STAMP 06 - STAMP
    $pdf->MultiCell(162.45, 0, 'ACTION', 0, 'L', false, 1, 2.12, 237.07, true); // new-12 - ACTION 06 - ACTION (multilinha)
}
