<?php

function mGerarPagina3JMC($pdf) {
    // =========================================================
    // Pagina 3 — JMC (Job Material Card)
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // Gerado via jobcard_app em 22/07/2026
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo (tamanho nativo = 1200 DPI, Letter) ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_3_jmc_trim.png';
    $pdf->Image($imgFundo, 0, 0, 0, 0, 'PNG');

    // Fonte: 12px bold -> 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(163.25, 23.02, 'N190036001'); // p010 - Card Code - N190036001
    // Fonte: 11px normal -> 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(3.18, 48.15, '190036'); // p021 - Origin Document - 190036
    $pdf->Text(49.21, 48.15, '23'); // p022 - ATA - 23
    $pdf->Text(61.91, 48.15, 'A320-214'); // p023 - A/C Type - A320-214
    $pdf->Text(81.49, 48.15, 'PR-MLD'); // p024 - A/C Reg - PR-MLD
    $pdf->Text(100.01, 48.15, 'A320'); // p025 - Company - A320
    $pdf->Text(122.5, 48.15, '38.25'); // p026 - A/C Work Order - 38.25
    // Fonte: 15px bold -> 11.25pt
    $pdf->SetFont('helvetica', 'B', 11.25);
    $pdf->write1DBarcode('N000000000000001900360002901000000A4M001', 'C39', 3.18, 26.72, 100, 5, null, array('text' => true), 'N'); // p011 - JIC Work Order (barcode)
    // Fonte: 11px normal -> 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(59.8, 72.5, 'P/N IW'); // p031 - P/N IW
    $pdf->Text(83.34, 72.5, 'Batch / SN'); // p032 - Batch / SN
    $pdf->Text(46.04, 72.5, '1'); // p037 - Qty planned
    $pdf->Text(38.1, 72.5, 'UN'); // p029 - Unit
    $pdf->Text(3.18, 72.5, 'E0399-01'); // p028 - P/N
    $pdf->Text(109.01, 72.5, 'DESCARREGADOR ESTATICO'); // p033 - Description
    $pdf->Text(163.51, 72.5, 'Address'); // p038 - Material Address
    $pdf->Text(181.5, 72.5, 'applied'); // p039 - Qty applied
    // Fonte: 11px bold -> 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(209.55, 73.29, 'x'); // new-2 - Mat. applied (Nao)
    $pdf->Text(200.55, 73.29, 'x'); // new-1 - Mat. applied (Sim)
    // Tabela: Materials (10 colunas, 5 linhas)
    $tblHtml = '<table border="0" cellpadding="1" cellspacing="0" style="width:214.57mm;">';
    $tblHtml .= '<tr style="background-color:#f0f0f0;font-weight:bold;">';
    $tblHtml .= '<td width="16.65%" style="text-align:left;border:0.3mm solid #000;">P/N</td>';
    $tblHtml .= '<td width="3.7%" style="text-align:left;border:0.3mm solid #000;">Unit</td>';
    $tblHtml .= '<td width="6.41%" style="text-align:center;border:0.3mm solid #000;">Qty planned</td>';
    $tblHtml .= '<td width="11.59%" style="text-align:left;border:0.3mm solid #000;"> P/N IW</td>';
    $tblHtml .= '<td width="11.34%" style="text-align:left;border:0.3mm solid #000;">Batch / SN</td>';
    $tblHtml .= '<td width="25.4%" style="text-align:left;border:0.3mm solid #000;">Description</td>';
    $tblHtml .= '<td width="8.14%" style="text-align:left;border:0.3mm solid #000;">Material Address</td>';
    $tblHtml .= '<td width="8.14%" style="text-align:center;border:0.3mm solid #000;">Qty applied</td>';
    $tblHtml .= '<td colspan="2" width="8.64%" style="text-align:center;border:0.3mm solid #000;">Mat. applied?</td>';
    $tblHtml .= '</tr>';
    for ($i = 0; $i < 5; $i++) {
        $tblHtml .= '<tr>';
        $tblHtml .= '<td width="16.65%" style="text-align:left;border:0.3mm solid #000;">E0399-01</td>';
        $tblHtml .= '<td width="3.7%" style="text-align:left;border:0.3mm solid #000;">UN</td>';
        $tblHtml .= '<td width="6.41%" style="text-align:center;border:0.3mm solid #000;">1</td>';
        $tblHtml .= '<td width="11.59%" style="text-align:left;border:0.3mm solid #000;"> P/N IW</td>';
        $tblHtml .= '<td width="11.34%" style="text-align:left;border:0.3mm solid #000;">Batch / SN</td>';
        $tblHtml .= '<td width="25.4%" style="text-align:left;border:0.3mm solid #000;"> DESCARREGADOR ESTATICO DESCARREGADOR ESTATICODESCARREGADOR ESTATICO</td>';
        $tblHtml .= '<td width="8.14%" style="text-align:left;border:0.3mm solid #000;"></td>';
        $tblHtml .= '<td width="8.14%" style="text-align:center;border:0.3mm solid #000;"></td>';
        $tblHtml .= '<td width="4.32%" style="text-align:center;border:0.3mm solid #000;">YES</td>';
        $tblHtml .= '<td width="4.32%" style="text-align:center;border:0.3mm solid #000;">NO</td>';
        $tblHtml .= '</tr>';
    }
    $tblHtml .= '</table>';
    $pdf->SetXY(2.65, 78.85);
    $pdf->writeHTML($tblHtml, true, false, false, false, '');
}
