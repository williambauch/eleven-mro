<?php

function mDisponibilizarDownload($var_arquivo)
{
    // ============================================================
    // mDisponibilizarDownload — Envia o arquivo final (PDF ou ZIP)
    // para download. Usa Content-Disposition com o MIME correto.
    // $var_arquivo: caminho do arquivo final gerado
    // ============================================================

        if (!file_exists($var_arquivo)) {
            echo "Arquivo nao encontrado.";
            return;
        }

        $var_ext = strtolower(pathinfo($var_arquivo, PATHINFO_EXTENSION));
        $var_mime = ($var_ext == 'zip') ? 'application/zip' : 'application/pdf';

        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: ' . $var_mime);
        header('Content-Disposition: attachment; filename="' . basename($var_arquivo) . '"');
        header('Content-Length: ' . filesize($var_arquivo));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile($var_arquivo);
        exit;
}
