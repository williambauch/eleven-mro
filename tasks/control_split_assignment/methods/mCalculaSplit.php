<?php

function mCalculaSplit($var_assignment_id)
{
    // ====================================================================
    // MRO-130: Calcula os valores da redistribuicao proporcional do split.
    // Metodo unico usado pelo onLoad (exibicao) e pelo onValidateSuccess
    // (gravacao). Recebe o assignment_id e retorna array com:
    //   total_alocado, qtd_ativos, consumido, saldo, qtd_nova, novo_valor
    // ====================================================================


        $var_result = array(
            'total_alocado' => 0,
            'qtd_ativos'    => 0,
            'consumido'     => 0,
            'saldo'         => 0,
            'qtd_nova'      => 0,
            'novo_valor'    => 0
        );

        $var_assignment_id = (int)$var_assignment_id;
        if ($var_assignment_id <= 0) {
            return $var_result;
        }

        // 1. Descobre a task do assignment
        $var_sql_task = "SELECT task_id FROM mro_task_assignments
                         WHERE assignment_id = " . $var_assignment_id;
        sc_lookup(rs_calc_task, $var_sql_task);

        if (empty({rs_calc_task})) {
            return $var_result;
        }

        $var_task_id = (int){rs_calc_task[0][0]};

        // 2. Total alocado + qtd de assignments ATIVOS da task
        $var_sql_total = "SELECT COALESCE(SUM(planned_qty_hours), 0) AS total,
                                 COUNT(*) AS qtd
                          FROM mro_task_assignments
                          WHERE task_id = " . $var_task_id . "
                            AND status_code IN ('NOT_STARTED','ASSIGNED','IN_PROGRESS','PAUSED','BLOCKED','PENDING_HANDOVER')";
        sc_lookup(rs_calc_total, $var_sql_total);

        $var_total_alocado = !empty({rs_calc_total}) ? (float){rs_calc_total[0][0]} : 0;
        $var_qtd_ativos    = !empty({rs_calc_total}) ? (int){rs_calc_total[0][1]} : 0;

        // 3. Tempo consumido: actual_qty_hours de quem ja trabalhou (qualquer status)
        $var_sql_consumido = "SELECT COALESCE(SUM(actual_qty_hours), 0)
                              FROM mro_task_assignments
                              WHERE task_id = " . $var_task_id . "
                                AND actual_qty_hours IS NOT NULL
                                AND actual_qty_hours > 0";
        sc_lookup(rs_calc_cons, $var_sql_consumido);
        $var_consumido = !empty({rs_calc_cons}) ? (float){rs_calc_cons[0][0]} : 0;

        // 3b. Tempo consumido: timesheets IN_PROGRESS da task (cronometro rodando)
        $var_sql_ts = "SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (NOW() - ts.start_time)) / 3600), 0)
                       FROM mro_timesheet ts
                       JOIN mro_task_assignments a ON a.assignment_id = ts.assignment_id
                       WHERE a.task_id = " . $var_task_id . "
                         AND ts.status = 'IN_PROGRESS'";
        sc_lookup(rs_calc_ts, $var_sql_ts);
        $var_consumido += !empty({rs_calc_ts}) ? (float){rs_calc_ts[0][0]} : 0;

        // 4. Saldo e novo valor por assignment (ativos + 1 novo)
        $var_saldo    = $var_total_alocado - $var_consumido;
        $var_qtd_nova = $var_qtd_ativos + 1;
        $var_novo_valor = ($var_qtd_nova > 0) ? $var_saldo / $var_qtd_nova : 0;
        if ($var_novo_valor < 0) { $var_novo_valor = 0; }

        $var_result['total_alocado'] = $var_total_alocado;
        $var_result['qtd_ativos']    = $var_qtd_ativos;
        $var_result['consumido']     = $var_consumido;
        $var_result['saldo']         = $var_saldo;
        $var_result['qtd_nova']      = $var_qtd_nova;
        $var_result['novo_valor']    = $var_novo_valor;

        return $var_result;
}
