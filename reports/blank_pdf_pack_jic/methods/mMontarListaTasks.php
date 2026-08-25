<?php

function mMontarListaTasks($var_ids)
{
    // ============================================================
    // mMontarListaTasks — Monta a lista de task_id a processar no lote.
    //
    // $var_ids: string CSV de task_id (ja filtrados pela
    //           grid_public_mro_tasks / control_filtro_jic)
    //
    // Retorna array de task_id (deduplicado, ordenado por task_code)
    // ou false em erro de consulta.
    // ============================================================

        $var_lista = array();

        $var_ids_raw = trim((string)$var_ids);

        if (empty($var_ids_raw)) {
            return array();
        }

        foreach (explode(',', $var_ids_raw) as $var_item) {
            $var_item = trim($var_item);
            if ($var_item !== '') {
                $var_lista[] = (int)$var_item;
            }
        }
        $var_lista = array_values(array_unique($var_lista));

        if (count($var_lista) <= 0) {
            return array();
        }

        // Valida os ids no banco e ordena por task_code
        $var_ids_sql = implode(',', $var_lista);
        $var_sql = "SELECT task_id FROM mro_tasks
                    WHERE task_id IN (" . $var_ids_sql . ")
                    ORDER BY task_code";
        sc_lookup(ds_lista, $var_sql);

        if ({ds_lista} === false) {
            return false;
        }

        $var_lista = array();
        if (!empty({ds_lista})) {
            foreach ({ds_lista} as $var_linha) {
                $var_lista[] = (int)$var_linha[0];
            }
        }

        return $var_lista;
}
