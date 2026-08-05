<?php

function mRecalcularEstimatedMaterialCost($var_task_id)
{
    // mRecalcularEstimatedMaterialCost
        // Soma SUM(total_cost) dos materiais da task EXCLUINDO material do
        // CLIENTE e grava o total em mro_tasks.estimated_material_cost.
        // Os campos unit_cost/total_cost do vinculo ja sao gravados pelo
        // proprio form (ScriptCase) e pelo mCalcularTotaisEmTela em tela.
        // Retorna o total (float) para o chamador usar no sc_master_value.

        $var_task_id = (int)$var_task_id;

        if ($var_task_id <= 0) {
            return 0;
        }

        // Soma o custo dos materiais EXCLUINDO material do CLIENTE
        $var_sql = "SELECT COALESCE(SUM(total_cost), 0)
                    FROM mro_task_materials
                    WHERE task_id = " . $var_task_id . "
                      AND material_source != 'CLIENTE'";
        sc_lookup(rs_soma, $var_sql);

        if ({rs_soma} === false) {
            sc_log_add("recalc_est_mat_cost", "Erro ao somar custo da task " . $var_task_id);
            return 0;
        }

        $var_total = (float){rs_soma[0][0]};

        $var_update = "UPDATE mro_tasks
                       SET estimated_material_cost = " . $var_total . "
                       WHERE task_id = " . $var_task_id;
        sc_log_add("recalc_est_mat_cost", "task " . $var_task_id . " = " . $var_total);
        sc_exec_sql($var_update);

        return $var_total;
}
