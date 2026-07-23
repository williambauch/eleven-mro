<?php

function mGerarPagina3JMC($pdf, $data)
{
    // =========================================================
        // Pagina 3 — JMC (Job Material Card)
        // Dimensoes LETTER: 215,9 x 279,4 mm
        // =========================================================

        $pdf->AddPage();

        // --- Imagem de fundo ---
        $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_3_jmc_trim_tabela.png';
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

        // Busca materiais da tarefa
        $var_sql_mat = "SELECT COALESCE(string_agg(
            '<tr>' ||
            '<td>' || COALESCE(m.part_number, '') || '</td>' ||
            '<td>' || COALESCE(m.unit_measure, '') || '</td>' ||
            '<td>' || COALESCE(tm.planned_qty::text, '') || '</td>' ||
            '<td>' || COALESCE(m.part_number, '') || '</td>' ||
            '<td>' || COALESCE(tm.batch_sn, '') || '</td>' ||
            '<td>' || COALESCE(m.description, '') || '</td>' ||
            '<td>' || COALESCE(m.stock_location, '') || '</td>' ||
            '<td>' || COALESCE(tm.applied_qty::text, '') || '</td>' ||
            '<td>' || CASE WHEN tm.is_applied = true THEN 'YES' ELSE 'NO' END || '</td>' ||
            '</tr>'
            , '' ORDER BY m.part_number), '') AS material_rows
        FROM mro_task_materials tm
        LEFT JOIN mro_materials m ON tm.material_id = m.material_id
        WHERE tm.task_id = " . (int)$var_task_id;
        sc_lookup(ds_mat_p3, $var_sql_mat);

        $var_mat_rows = '';
        if ({ds_mat_p3} !== false && !empty({ds_mat_p3})) {
            $var_mat_rows = {ds_mat_p3[0][0]};
        }

        // Fonte: 12px bold -> 9pt
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Text(163.25, 23.02, $var_task_code); // p010 - Card Code
        // Fonte: 15px bold -> 11.25pt
        $pdf->SetFont('helvetica', 'B', 11.25);
        $pdf->write1DBarcode($var_barcode, 'C39', 3.18, 26.72, 100, 5, null, array('text' => true), 'N'); // p011 - barcode
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(1.59, 48.15, $var_origin_doc); // p021 - Origin Document
        $pdf->Text(46.83, 48.15, $var_ata); // p022 - ATA
        $pdf->Text(59.53, 48.15, $var_model); // p023 - A/C Type
        $pdf->Text(78.58, 48.15, $var_reg); // p024 - A/C Reg
        $pdf->Text(97.9, 48.15, substr($var_customer, 0, 12)); // p025 - Company
        $pdf->Text(120.39, 48.15, $var_ac_work_order); // p026 - A/C Work Order
        // Fonte: 11px bold -> 8.25pt
        $pdf->SetFont('helvetica', 'B', 8.25);
        // Tabela: Materials (9 colunas)
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
        // Linhas com dados
        if (!empty($var_mat_rows)) {
            $tblHtml .= $var_mat_rows;
        } else {
            $tblHtml .= '<tr><td colspan="9" style="text-align:center;">Sem materiais para listar</td></tr>';
        }
        $tblHtml .= '</table>';
        $pdf->SetXY(1.85, 61.12);
        $pdf->writeHTML($tblHtml, true, false, false, false, '');
        // Fonte: 11px normal -> 8.25pt
        $pdf->SetFont('helvetica', '', 8.25);
        $pdf->Text(48.68, 260.61, '* Provider (Sign & Stamp)'); // p047
        $pdf->Text(136.26, 261.41, '* Requester (Sign & Stamp)'); // p048
}
