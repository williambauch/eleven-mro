<?php

function mGerarPdfLoteAgrupado($var_lista, $var_paginas)
{
    // ============================================================
    // mGerarPdfLoteAgrupado — Gera um UNICO PDF com as paginas de
    // todas as tasks do lote (merge real, sem concatenar arquivos).
    //
    // $var_lista:   array de task_id a processar
    // $var_paginas: array de paginas ('JIC','JEC','JMC','SHIFT','CALIBRATED')
    //
    // Usa um unico objeto TCPDF e adiciona as paginas de cada task
    // nele (cada mGerarPagina* chama AddPage), produzindo um PDF com
    // N paginas reais (diferente da concatenacao de bytes, que so
    // abre o primeiro PDF).
    //
    // Retorna o binario do PDF unico ou false em erro.
    // ============================================================

        if (!is_array($var_lista) || count($var_lista) <= 0) {
            return false;
        }

        $pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $var_ok = 0;

        foreach ($var_lista as $var_task_id) {
            $var_data = mCarregarDadosTask($var_task_id);
            if ($var_data === false) {
                continue;
            }

            // Adiciona as paginas selecionadas da task no MESMO pdf
            if (in_array('JIC', $var_paginas)) {
                if ($var_data['is_nrc']) {
                    mGerarPagina1NR($pdf, $var_data);
                } else {
                    mGerarPagina1JIC($pdf, $var_data);
                }
            }
            if (in_array('JEC', $var_paginas)) {
                mGerarPagina2JEC($pdf, $var_data);
            }
            if (in_array('JMC', $var_paginas)) {
                mGerarPagina3JMC($pdf, $var_data);
            }
            if (in_array('SHIFT', $var_paginas)) {
                mGerarPagina4Shift($pdf, $var_data);
            }
            if (in_array('CALIBRATED', $var_paginas)) {
                mGerarPagina5Calibrated($pdf, $var_data);
            }

            $var_ok++;
        }

        if ($var_ok <= 0) {
            return false;
        }

        if (ob_get_length()) {
            ob_clean();
        }

        return $pdf->Output('', 'S');
}
