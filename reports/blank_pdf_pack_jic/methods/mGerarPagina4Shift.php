<?php

function mGerarPagina4Shift($pdf, $data)
{
    // =========================================================
        // Pagina 4 — Shift Turnover
        // Dimensoes LETTER: 215,9 x 279,4 mm
        // Suporta N registros: grupos de 6 por pagina
        // =========================================================

        $var_task_code     = trim($data['task_code']);
        $var_reg           = trim($data['aircraft_registration']);
        $var_model         = trim($data['model']);
        $var_task_id       = $data['task_id'];

        // Busca registros de repasse de turno (string_agg + delimitadores)
        $var_sql_shift = "SELECT COALESCE(string_agg(
            COALESCE(ts.handover_notes, '') || '|' ||
            COALESCE(ts.end_time::text, '') || '|' ||
            COALESCE(e.employee_registration, '')
        , '~~' ORDER BY ts.end_time), '') AS shift_data
        FROM mro_timesheet ts
        INNER JOIN mro_task_assignments ta ON ts.assignment_id = ta.assignment_id
        LEFT JOIN mro_employees e ON ts.employee_id = e.employee_id
        WHERE ta.task_id = " . (int)$var_task_id . "
        AND ts.handover_notes IS NOT NULL AND ts.handover_notes != ''";
        sc_lookup(ds_shift, $var_sql_shift);

        $var_records = array();
        if ({ds_shift} !== false && !empty({ds_shift})) {
            $var_raw = {ds_shift[0][0]};
            if (!empty($var_raw)) {
                $var_rows = explode('~~', $var_raw);
                foreach ($var_rows as $var_row) {
                    $var_parts = explode('|', $var_row);
                    if (count($var_parts) >= 3) {
                        $var_records[] = array(
                            'action' => $var_parts[0],
                            'data_action' => $var_parts[1],
                            'mtr' => $var_parts[2],
                        );
                    }
                }
            }
        }

        // Se nao ha registros, cria um vazio para mostrar template
        if (empty($var_records)) {
            $var_records[] = array('action' => '', 'data_action' => '', 'mtr' => '');
        }

        // Posicoes fixas dos 6 cards (Y mm)
        $cards_y = array(
            1 => array('action' => 23.55, 'data_action' => 26.01, 'mtr' => 41.28, 'shift' => 41.28, 'stamp' => 54.5, 'data_stamp' => 61.16),
            2 => array('action' => 70.91, 'data_action' => 73.64, 'mtr' => 88.9,  'shift' => 88.9,  'stamp' => 102.13),
            3 => array('action' => 112.18,'data_action' => 114.91,'mtr' => 130.18,'shift' => 130.18,'stamp' => 143.4),
            4 => array('action' => 153.99,'data_action' => 156.72,'mtr' => 171.98,'shift' => 171.98,'stamp' => 185.21),
            5 => array('action' => 195.26,'data_action' => 197.73,'mtr' => 213.25,'shift' => 213.25,'stamp' => 226.48),
            6 => array('action' => 237.07,'data_action' => 238.74,'mtr' => 251.35,'shift' => 251.35,'stamp' => 264.58),
        );

        // Agrupa registros em paginas de 6
        $var_chunks = array_chunk($var_records, 6);
        $var_page_count = 0;

        foreach ($var_chunks as $var_chunk) {
            $pdf->AddPage();
            $var_page_count++;

            // --- Imagem de fundo ---
            $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_4_shift_trim.png';
            $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
            $pdf->setPageMark();

            // Header
            $pdf->SetFont('helvetica', '', 9.75);
            $pdf->Text(173.57, 6.61, $var_task_code); // p007
            $pdf->Text(123.03, 6.61, $var_reg);       // p005
            $pdf->Text(66.41, 6.61, $var_model);       // p003

            foreach ($var_chunk as $var_idx => $var_rec) {
                $var_card = $var_idx + 1;
                $var_y = $cards_y[$var_card];

                // Formata data
                $var_date_display = '';
                if (!empty($var_rec['data_action'])) {
                    $var_ts = strtotime($var_rec['data_action']);
                    if ($var_ts) $var_date_display = date('d/m/Y', $var_ts);
                }

                // ACTION (MultiCell na esquerda)
                $pdf->SetFont('helvetica', '', 8.25);
                $var_action_x = ($var_card == 3) ? 2.65 : 2.12;
                $pdf->MultiCell(162.45, 0, $var_rec['action'], 0, 'L', false, 1, $var_action_x, $var_y['action'], true);

                // Data_action (Cell na direita)
                $pdf->SetFont('helvetica', '', 13.5);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetXY(170.92, $var_y['data_action']);
                $pdf->Cell(18, 4.76, $var_date_display, 0, 0, 'L', true, '', 0, false, 'T', 'T');

                // MTR
                $pdf->SetFont('helvetica', '', 8.25);
                $pdf->Text(177.01, $var_y['mtr'], $var_rec['mtr']);

                // SHIFT
                $pdf->Text(202.94, $var_y['shift'], '* SHIFT');

                // STAMP
                $pdf->Text(182.56, $var_y['stamp'], '* STAMP');

                // Data_stamp (apenas card 1)
                if ($var_card == 1 && isset($var_y['data_stamp'])) {
                    $pdf->SetFont('helvetica', '', 9.75);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetXY(186.53, $var_y['data_stamp']);
                    $pdf->Cell(18, 3.44, $var_date_display, 0, 0, 'L', true, '', 0, false, 'T', 'T');
                }
            }
        }
}
