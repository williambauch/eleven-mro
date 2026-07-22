<?php

function mGerarPagina5Calibrated($pdf) {
    // =========================================================
    // Pagina 5 — Calibrated Tool
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // Gerado via jobcard_app em 22/07/2026
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo (tamanho nativo = 1200 DPI, Letter) ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_5_calibrated_trim.png';
    $pdf->Image($imgFundo, 0, 0, 0, 0, 'PNG');

    // Fonte: 13px normal -> 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(66.41, 7.14, 'A320-214'); // p019 - - - A320-214
    $pdf->Text(123.56, 7.14, 'PR-MLD'); // p021 - - - PR-MLD
    $pdf->Text(173.83, 7.14, 'N190036001'); // p023 - - - N190036001
}
