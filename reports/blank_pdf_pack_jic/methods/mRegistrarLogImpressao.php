<?php

function mRegistrarLogImpressao($var_batch_id, $var_task_id, $var_task_code, $var_usuario, $var_status, $var_mensagem, $var_arquivo)
{
    // ============================================================
    // mRegistrarLogImpressao — Insere/atualiza log na mro_task_print_log.
    //
    // $var_batch_id:    identificador do lote (agrupa os logs)
    // $var_task_id:     task processada (0 para o log geral do lote)
    // $var_task_code:   codigo da JIC
    // $var_usuario:     login do usuario que solicitou
    // $var_status:      em_andamento | concluida | erro
    // $var_mensagem:    mensagem de erro (opcional)
    // $var_arquivo:     caminho do arquivo gerado (opcional)
    // ============================================================

        $var_batch_id = addslashes($var_batch_id);
        $var_task_id = (int)$var_task_id;
        $var_task_code = addslashes($var_task_code);
        $var_usuario = addslashes($var_usuario);
        $var_status = addslashes($var_status);
        $var_mensagem = addslashes($var_mensagem);
        $var_arquivo = addslashes($var_arquivo);

        $var_msg_sql = ($var_mensagem != '') ? "'" . $var_mensagem . "'" : "NULL";
        $var_arq_sql = ($var_arquivo != '') ? "'" . $var_arquivo . "'" : "NULL";

        // Busca se ja existe log para este batch+task (para atualizar status)
        $var_sql_busca = "SELECT print_log_id FROM mro_task_print_log
                          WHERE batch_id = '" . $var_batch_id . "'
                          AND task_id = " . $var_task_id;
        sc_lookup(ds_log, $var_sql_busca);

        if ({ds_log} !== false && !empty({ds_log})) {
            $var_print_log_id = (int){ds_log[0][0]};
            $var_sql_upd = "UPDATE mro_task_print_log SET
                                status = '" . $var_status . "',
                                data_fim = NOW(),
                                mensagem_erro = " . $var_msg_sql . ",
                                arquivo_gerado = " . $var_arq_sql . "
                            WHERE print_log_id = " . $var_print_log_id;
            sc_exec_sql($var_sql_upd);
        } else {
            $var_sql_ins = "INSERT INTO mro_task_print_log
                            (batch_id, task_id, task_code, usuario, data_solicitacao, data_fim, status, mensagem_erro, arquivo_gerado)
                            VALUES
                            ('" . $var_batch_id . "',
                             " . $var_task_id . ",
                             '" . $var_task_code . "',
                             '" . $var_usuario . "',
                             NOW(),
                             " . ($var_status == 'em_andamento' ? 'NULL' : 'NOW()') . ",
                             '" . $var_status . "',
                             " . $var_msg_sql . ",
                             " . $var_arq_sql . ")";
            sc_exec_sql($var_sql_ins);
        }
}
