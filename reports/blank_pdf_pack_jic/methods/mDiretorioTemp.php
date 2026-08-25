<?php

function mDiretorioTemp($var_acao)
{
    // ============================================================
        // mDiretorioTemp — Gerencia o diretorio temporario dos PDFs
        // do lote de impressao.
        //
        // $var_acao: 'criar'         -> cria o diretorio se nao existir e
        //                               retorna o caminho (ou false se sem permissao)
        //            'limpar'         -> apaga TODOS os arquivos do diretorio
        //            'limpar_antigos' -> apaga apenas arquivos com mais de
        //                               60 minutos (etapa 05 — mantem o arquivo
        //                               disponivel para o download por um tempo)
        // ============================================================

            $var_temp_dir = __DIR__ . '/tmp_downloads';

            if ($var_acao == 'criar') {
                if (!is_dir($var_temp_dir)) {
                    if (!@mkdir($var_temp_dir, 0777, true) && !is_dir($var_temp_dir)) {
                        return false;
                    }
                }
                // Confirma que o diretorio tem permissao de escrita
                if (!is_writable($var_temp_dir)) {
                    return false;
                }
                return $var_temp_dir;
            }

            if ($var_acao == 'limpar' || $var_acao == 'limpar_antigos') {
                if (!is_dir($var_temp_dir)) {
                    return;
                }

                $var_agora = time();
                $var_limite = 3600; // 60 minutos em segundos

                foreach (glob($var_temp_dir . '/*') as $var_arquivo) {
                    if (!is_file($var_arquivo)) {
                        continue;
                    }

                    if ($var_acao == 'limpar') {
                        @unlink($var_arquivo);
                    } elseif ($var_acao == 'limpar_antigos') {
                        $var_mod = @filemtime($var_arquivo);
                        if ($var_mod !== false && ($var_agora - $var_mod) > $var_limite) {
                            @unlink($var_arquivo);
                        }
                    }
                }
            }
}
