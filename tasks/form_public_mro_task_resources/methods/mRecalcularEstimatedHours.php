<?php

function mRecalcularEstimatedHours($var_task_id)
{
    // mRecalcularEstimatedHours
    // Soma as horas orcadas (budgeted_hours) dos recursos da task e
    // grava o total em mro_tasks.estimated_hours.
    // Retorna o total (float) para o chamador usar no sc_master_value.

        $var_task_id = (int)$var_task_id;

        if ($var_task_id <= 0) {
            return 0;
        }

        $var_sql = "SELECT COALESCE(SUM(budgeted_hours), 0)
                    FROM mro_task_resources
                    WHERE task_id = " . $var_task_id;
        sc_lookup(rs_soma, $var_sql);

        if ({rs_soma} === false) {
            sc_log_add("recalc_estimated_hours", "Erro ao somar horas da task " . $var_task_id);
            return 0;
        }

        $var_total = (float){rs_soma[0][0]};

        $var_update = "UPDATE mro_tasks
                       SET estimated_hours = " . $var_total . "
                       WHERE task_id = " . $var_task_id;
        sc_log_add("recalc_estimated_hours", "task " . $var_task_id . " = " . $var_total . "h");
        sc_exec_sql($var_update);

        return $var_total;
}
