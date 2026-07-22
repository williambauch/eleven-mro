<?php

function mGerarPagina3JMC($pdf) {
    // =========================================================
    // Pagina 3 — gerada do editor em 22/07/2026, 11:40:33
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_3_jmc_trim_tabela.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Fonte: 12px bold → 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(163.25, 23.02, 'N190036001'); // p010 - Card Code - N190036001
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(3.18, 48.15, '190036'); // p021 - Origin Document - 190036
    $pdf->Text(49.21, 48.15, '23'); // p022 - ATA - 23
    $pdf->Text(61.91, 48.15, 'A320-214'); // p023 - A/C Type - A320-214
    $pdf->Text(81.49, 48.15, 'PR-MLD'); // p024 - A/C Reg - PR-MLD
    $pdf->Text(100.01, 48.15, 'A320'); // p025 - Company - A320
    $pdf->Text(122.5, 48.15, '38.25'); // p026 - A/C Work Order - 38.25
    // Fonte: 15px bold → 11.25pt
    $pdf->SetFont('helvetica', 'B', 11.25);
    $pdf->write1DBarcode('N000000000000001900360002901000000A4M001', 'C39', 3.18, 26.72, 100, 5, null, array('text' => true), 'N'); // p011 - JIC Work Order - N000000000000001900360002901000000A4M001 (barcode)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    // Tabela: tabela Materials (9 colunas, 5 linhas)
    $tblHtml = '<table border="1" cellpadding="2" cellspacing="0" style="width:211.93mm;">';
    $tblHtml .= '<tr>';
    $tblHtml .= '<th width="16.85%" style="text-align:left;font-weight:bold;">P/N</th>';
    $tblHtml .= '<th width="3.75%" style="text-align:left;font-weight:bold;">Unit</th>';
    $tblHtml .= '<th width="6.49%" style="text-align:center;font-weight:bold;">Qty planned</th>';
    $tblHtml .= '<th width="11.74%" style="text-align:left;font-weight:bold;"> P/N IW</th>';
    $tblHtml .= '<th width="11.48%" style="text-align:left;font-weight:bold;">Batch / SN</th>';
    $tblHtml .= '<th width="25.72%" style="text-align:left;font-weight:bold;">Description</th>';
    $tblHtml .= '<th width="8.24%" style="text-align:left;font-weight:bold;">Material Address</th>';
    $tblHtml .= '<th width="8.24%" style="text-align:center;font-weight:bold;">Qty applied</th>';
    $tblHtml .= '<th width="7.49%" style="text-align:center;font-weight:bold;">Mat. applied?</th>';
    $tblHtml .= '</tr>';
    for ($i = 0; $i < 5; $i++) {
        $tblHtml .= '<tr>';
        $tblHtml .= '<td width="16.85%" style="text-align:left;">E0399-01</td>';
        $tblHtml .= '<td width="3.75%" style="text-align:left;">UN</td>';
        $tblHtml .= '<td width="6.49%" style="text-align:center;">1</td>';
        $tblHtml .= '<td width="11.74%" style="text-align:left;"> P/N IW</td>';
        $tblHtml .= '<td width="11.48%" style="text-align:left;">Batch / SN</td>';
        $tblHtml .= '<td width="25.72%" style="text-align:left;"> DESCARREGADOR ESTATICO DESCARREGADOR ESTATICODESCARREGADOR ESTATICO</td>';
        $tblHtml .= '<td width="8.24%" style="text-align:left;"></td>';
        $tblHtml .= '<td width="8.24%" style="text-align:center;"></td>';
        $tblHtml .= '<td width="7.49%" style="text-align:center;">YES / NO</td>';
        $tblHtml .= '</tr>';
    }
    $tblHtml .= '</table>';
    $pdf->SetXY(1.85, 61.12);
    $pdf->writeHTML($tblHtml, true, false, false, false, '');
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(48.68, 260.61, 'Provider (Sign & Stamp)'); // p047 - Provider_Sign_Stamp - Provider (Sign & Stamp)
    $pdf->Text(136.26, 261.41, 'Requester (Sign & Stamp)'); // p048 - Requester_Sign_Stamp - Requester (Sign & Stamp)
}
