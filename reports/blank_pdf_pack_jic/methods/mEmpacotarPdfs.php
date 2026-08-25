<?php

function mEmpacotarPdfs($var_arquivos_pdf, $var_agrupado, $var_temp_dir)
{
    // ============================================================
    // mEmpacotarPdfs — Gera o arquivo final do lote de impressao.
    //
    // $var_arquivos_pdf: array de caminhos dos PDFs gerados
    // $var_agrupado:     'S' = merge em um unico PDF; outro = ZIP
    // $var_temp_dir:     diretorio temporario
    //
    // Regras:
    //  - 1 PDF gerado        -> retorna o proprio PDF (download direto)
    //  - varios + agrupado=S -> merge por concatenacao (Opcao A)
    //  - varios (padrao)     -> ZIP (ZipArchive; fallback PharData;
    //                           ultimo recurso: merge em 1 PDF)
    // Retorna o caminho do arquivo final ou false em erro.
    // ============================================================

        if (!is_array($var_arquivos_pdf) || count($var_arquivos_pdf) <= 0) {
            return false;
        }

        $var_nome_base = 'pack_jic_lote_' . date('Ymd_His');
        $var_agrupado = (trim((string)$var_agrupado) == 'S');

        // 1. JIC unica: disponibiliza o PDF diretamente
        if (count($var_arquivos_pdf) == 1) {
            return $var_arquivos_pdf[0];
        }

        // 2. Merge em um unico PDF (Opcao A — concatenacao TCPDF)
        // Todos os PDFs do Pack sao gerados pelo mesmo TCPDF, entao a
        // concatenacao dos streams e segura (mesma codificacao de fonte).
        $var_destino_merge = $var_temp_dir . '/' . $var_nome_base . '_merge.pdf';
        $var_conteudo = '';
        foreach ($var_arquivos_pdf as $var_arquivo) {
            if (!file_exists($var_arquivo)) {
                continue;
            }
            $var_bin = file_get_contents($var_arquivo);
            if ($var_bin !== false) {
                $var_conteudo .= $var_bin;
            }
        }

        if (empty($var_conteudo)) {
            return false;
        }

        if ($var_agrupado) {
            $var_gravou = file_put_contents($var_destino_merge, $var_conteudo);
            if ($var_gravou !== false && file_exists($var_destino_merge)) {
                return $var_destino_merge;
            }

            return false;
        }

        // 3. ZIP com todos os PDFs (padrao).
        // Usa ZipArchive (extensao zip). Se nao estiver disponivel no
        // servidor, tenta PharData; ultimo recurso: salva o merge em 1 PDF
        // (para nao estourar HTTP 500 por falta de extensao zip).
        $var_destino_zip = $var_temp_dir . '/' . $var_nome_base . '.zip';
        $var_zip_ok = false;

        if (class_exists('ZipArchive')) {
            $var_zip = new ZipArchive();
            if ($var_zip->open($var_destino_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($var_arquivos_pdf as $var_arquivo) {
                    if (file_exists($var_arquivo)) {
                        $var_zip->addFile($var_arquivo, basename($var_arquivo));
                    }
                }
                $var_zip->close();
                $var_zip_ok = file_exists($var_destino_zip);
            }
        }

        if (!$var_zip_ok && class_exists('PharData')) {
            @unlink($var_destino_zip);
            try {
                $var_phar = new PharData($var_destino_zip, 0, null, Phar::ZIP);
                foreach ($var_arquivos_pdf as $var_arquivo) {
                    if (file_exists($var_arquivo)) {
                        $var_phar->addFile($var_arquivo, basename($var_arquivo));
                    }
                }
                $var_zip_ok = file_exists($var_destino_zip);
            } catch (Exception $e) {
                $var_zip_ok = false;
            }
        }

        if ($var_zip_ok) {
            return $var_destino_zip;
        }

        // Ultimo recurso: salva o merge em 1 PDF (evita o HTTP 500)
        $var_gravou = file_put_contents($var_destino_merge, $var_conteudo);
        if ($var_gravou !== false && file_exists($var_destino_merge)) {
            return $var_destino_merge;
        }

        return false;
}
