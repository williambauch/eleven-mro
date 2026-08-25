<?php

function mGerarPdfTask($data, $var_paginas)
{
    // ============================================================
    // mGerarPdfTask — Cria o objeto TCPDF e gera as paginas da task
    // conforme a lista de paginas selecionadas.
    // $var_paginas: array com valores 'JIC','JEC','JMC','SHIFT','CALIBRATED'
    // Retorna o objeto $pdf pronto (sem Output).
    // MRO-127: habilita "imprimir somente 1 relatorio" na individual
    // e no lote.
    // ============================================================

        $pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Pagina 01 — Capa JIC (Job Instruction Card)
        // Se for NRC usa capa Não Rotina, senao usa capa Padrão
        if (in_array('JIC', $var_paginas)) {
            if ($data['is_nrc']) {
                mGerarPagina1NR($pdf, $data);
            } else {
                mGerarPagina1JIC($pdf, $data);
            }
        }

        // Pagina 02 — JEC (Job Equipment and Tool Card)
        if (in_array('JEC', $var_paginas)) {
            mGerarPagina2JEC($pdf, $data);
        }

        // Pagina 03 — JMC (Job Material Card)
        if (in_array('JMC', $var_paginas)) {
            mGerarPagina3JMC($pdf, $data);
        }

        // Pagina 04 — Shift Turnover
        if (in_array('SHIFT', $var_paginas)) {
            mGerarPagina4Shift($pdf, $data);
        }

        // Pagina 05 — Calibrated Tool
        if (in_array('CALIBRATED', $var_paginas)) {
            mGerarPagina5Calibrated($pdf, $data);
        }

        return $pdf;
}
