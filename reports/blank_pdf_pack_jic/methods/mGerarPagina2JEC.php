<?php

function mGerarPagina2JEC($pdf, $data = array()) {
    // =========================================================
    // Pagina 2 — JEC (Job Equipment and Tool Card)
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_2_jec_trim_tabela.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Extrai valores do array de dados
    $var_task_code     = trim($data['task_code']);
    $var_ac_work_order = trim($data['ac_work_order']);
    $var_ata           = trim($data['ata_chapter']);
    $var_reg           = trim($data['aircraft_registration']);
    $var_customer      = trim($data['customer_name']);
    $var_model         = trim($data['model']);
    $var_origin_doc    = trim($data['origin_document']);
    $var_barcode       = trim($data['barcode']);
    $var_task_id       = $data['task_id'];

    // Busca ferramentas da tarefa
    $var_sql_tools = "SELECT COALESCE(string_agg(
        '<tr><td>' || COALESCE(tl.part_number, '') || '</td>' ||
        '<td>' || COALESCE(tl.description, '') || '</td>' ||
        '<td>' || COALESCE(tt.quantity_required::text, '') || '</td></tr>'
        , '' ORDER BY tl.part_number), '') AS tool_rows
    FROM mro_task_tools tt
    LEFT JOIN mro_tools tl ON tt.tool_id = tl.tool_id
    WHERE tt.task_id = " . (int)$var_task_id;
    sc_lookup(ds_tools, $var_sql_tools);

    $var_tool_rows = '';
    if ({ds_tools} !== false && !empty({ds_tools})) {
        $var_tool_rows = {ds_tools[0][0]};
    }

    // Fonte: 12px bold -> 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(163.25, 23.02, $var_task_code); // p009 - Card Code
    // Fonte: 15px bold -> 11.25pt
    $pdf->SetFont('helvetica', 'B', 11.25);
    $pdf->write1DBarcode($var_barcode, 'C39', 3.18, 26.72, 100, 5, null, array('text' => true), 'N'); // p010 - barcode
    // Fonte: 11px normal -> 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(0.79, 50.8, $var_origin_doc); // p020 - Origin Document
    $pdf->Text(47.36, 50.8, $var_ata); // p021 - ATA
    $pdf->Text(59.27, 50.8, $var_model); // p022 - A/C Type
    $pdf->Text(78.58, 50.8, $var_reg); // p023 - A/C Reg
    $pdf->Text(97.9, 50.8, substr($var_customer, 0, 12)); // p024 - Company
    $pdf->Text(120.12, 50.8, $var_ac_work_order); // p025 - A/C Work Order
    // Fonte: 11px bold -> 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    // Tabela: Equipments e Tools (3 colunas)
    $tblHtml = '<table border="1" cellpadding="2" cellspacing="0" style="width:211.68mm;">';
    $tblHtml .= '<tr>';
    $tblHtml .= '<th width="25%" style="text-align:left;font-weight:bold;">Tool Code</th>';
    $tblHtml .= '<th width="67.5%" style="text-align:left;font-weight:bold;">Description</th>';
    $tblHtml .= '<th width="7.5%" style="text-align:left;font-weight:bold;">Qty</th>';
    $tblHtml .= '</tr>';
    // Linhas com dados
    if (!empty($var_tool_rows)) {
        $tblHtml .= $var_tool_rows;
    } else {
        $tblHtml .= '<tr><td colspan="3" style="text-align:center;">Sem ferramentas para listar</td></tr>';
    }
    $tblHtml .= '</table>';
    $pdf->SetXY(1.85, 64.56);
    $pdf->writeHTML($tblHtml, true, false, false, false, '');
}
