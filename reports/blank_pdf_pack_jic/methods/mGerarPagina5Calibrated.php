<?php

function mGerarPagina5Calibrated($pdf, $data = array()) {
    // =========================================================
    // Pagina 5 — Calibrated Tool
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // 8 cards em 2 colunas x 4 linhas, com paginacao
    // =========================================================

    $var_task_code     = trim($data['task_code']);
    $var_reg           = trim($data['aircraft_registration']);
    $var_model         = trim($data['model']);
    $var_task_id       = $data['task_id'];

    // --- Busca ferramentas calibráveis da tarefa ---
    $var_sql_calib = "SELECT COALESCE(string_agg(
        COALESCE(tl.last_calibration_date::text, '') || '|' ||
        COALESCE(tl.description, '') || '|' ||
        COALESCE(tl.calibration_due_date::text, '') || '|' ||
        COALESCE(tl.part_number, '') || '|' ||
        COALESCE(tl.serial_number, '')
    , '~~' ORDER BY tl.part_number), '') AS calib_data
    FROM mro_task_tools tt
    INNER JOIN mro_tools tl ON tt.tool_id = tl.tool_id
    WHERE tt.task_id = " . (int)$var_task_id;
    sc_lookup(ds_calib, $var_sql_calib);

    $var_records = array();
    if ({ds_calib} !== false && !empty({ds_calib})) {
        $var_raw = {ds_calib[0][0]};
        if (!empty($var_raw)) {
            $var_rows = explode('~~', $var_raw);
            foreach ($var_rows as $var_row) {
                $var_parts = explode('|', $var_row);
                if (count($var_parts) >= 5) {
                    $var_ts_data = strtotime($var_parts[0]);
                    $var_ts_prox = strtotime($var_parts[2]);
                    $var_records[] = array(
                        'data'        => $var_ts_data ? date('d/m/Y', $var_ts_data) : ' ',
                        'task_sub'    => $var_parts[1],
                        'inspector'   => '* Inspector Stamp',
                        'measure'     => $var_parts[1],
                        'incer'       => '* Incerteza-erro-desv',
                        'prox_calib'  => $var_ts_prox ? date('d/m/Y', $var_ts_prox) : ' ',
                        'equip_pass'  => '* Equipment pass',
                        'pn'          => $var_parts[3] ?: ' ',
                        'sn'          => $var_parts[4] ?: ' ',
                    );
                }
            }
        }
    }

    // Fallback: se nao ha dados, cria 8 registros placeholder
    if (empty($var_records)) {
        for ($i = 0; $i < 8; $i++) {
            $var_records[] = array(
                'data'        => ' ',
                'task_sub'    => ' ',
                'inspector'   => '* Inspector Stamp',
                'measure'     => ' ',
                'incer'       => '* Incerteza-erro-desv',
                'prox_calib'  => ' ',
                'equip_pass'  => '* Equipment pass',
                'pn'          => ' ',
                'sn'          => ' ',
            );
        }
    }

    // --- Posicoes fixas dos cards ---
    // 4 linhas (base Y) x 2 colunas (esquerda/direita)
    $card_positions = array(
        // base_y  | X col esq          | X col dir
        1 => array('y' => 25.93, 'xl' =>  1.32, 'xr' => 112.18),
        2 => array('y' => 25.93, 'xl' =>  1.32, 'xr' => 112.18),
        3 => array('y' => 91.02, 'xl' =>  1.32, 'xr' => 112.18),
        4 => array('y' => 91.02, 'xl' =>  1.32, 'xr' => 112.18),
        5 => array('y' => 156.63,'xl' =>  1.32, 'xr' => 112.18),
        6 => array('y' => 156.63,'xl' =>  1.32, 'xr' => 112.18),
        7 => array('y' => 222.25,'xl' =>  1.32, 'xr' => 112.18),
        8 => array('y' => 222.25,'xl' =>  1.32, 'xr' => 112.18),
    );

    // Nomes de campo => [ x_offset_esq, x_offset_dir, y_offset, largura, is_multicell ]
    $field_cfg = array(
        'data'       => array(  0.00,   0.00,  0.00,   0,     false),
        'task_sub'   => array( 18.79,  18.79,  0.00,  34.13, true),
        'inspector'  => array( 54.51,  54.51,  0.00,  49.21, true),
        'measure'    => array(  0.00,  -0.26, 14.55,  52.12, true),
        'incer'      => array( 53.98,  54.51, 14.55,  49.74, true),
        'prox_calib' => array( -0.79,   0.00, 28.84,  52.92, true),
        'equip_pass' => array( 54.51,  54.51, 28.84,  48.68, true),
        'pn'         => array(  0.00,  -0.26, 43.66,  52.65, true),
        'sn'         => array( 54.51,  54.77, 43.66,  48.68, true),
    );

    // --- Renderizacao ---
    // Agrupa registros em paginas de 8
    $var_chunks = array_chunk($var_records, 8);
    $var_page_count = 0;

    foreach ($var_chunks as $var_chunk) {
        $pdf->AddPage();
        $var_page_count++;

        $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_5_calibrated_trim.png';
        $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
        $pdf->setPageMark();

        // Header
        $pdf->SetFont('helvetica', '', 9.75);
        $pdf->Text(173.83, 7.14, $var_task_code);
        $pdf->Text(123.56, 7.14, $var_reg);
        $pdf->Text(66.41, 7.14, $var_model);

        // Cards (fonte 10px = 7.5pt)
        $pdf->SetFont('helvetica', '', 7.5);
        foreach ($var_chunk as $var_idx => $var_rec) {
            $var_card = $var_idx + 1;
            $var_pos = $card_positions[$var_card];
            $var_base_y = $var_pos['y'];

            // Card 1=esquerda, 2=direita, 3=esquerda, 4=direita ...
            $var_is_right = ($var_card % 2 == 0);
            $var_x_base = $var_is_right ? $var_pos['xr'] : $var_pos['xl'];

            foreach ($field_cfg as $var_field => $var_cfg) {
                $var_x = $var_x_base + ($var_is_right ? $var_cfg[1] : $var_cfg[0]);
                $var_y = $var_base_y + $var_cfg[2];
                $var_w = $var_cfg[3];
                $var_is_mc = $var_cfg[4];
                if ($var_is_mc && $var_w > 0) {
                    $pdf->MultiCell($var_w, 0, $var_rec[$var_field], 0, 'L', false, 1, $var_x, $var_y, true);
                } else {
                    $pdf->Text($var_x, $var_y, $var_rec[$var_field]);
                }
            }
        }
    }
}
