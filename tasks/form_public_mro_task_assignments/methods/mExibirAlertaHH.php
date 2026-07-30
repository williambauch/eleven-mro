<?php

function mExibirAlertaHH() {

    $var_sql_hh = "SELECT a.planned_qty_hours,
                          COALESCE(ROUND(SUM(ts.duration_minutes)::numeric / 60, 2), 0) AS total_hours
                   FROM mro_task_assignments a
                   LEFT JOIN mro_timesheet ts ON ts.assignment_id = a.assignment_id
                   WHERE a.assignment_id = " . (int){assignment_id} . "
                   GROUP BY a.planned_qty_hours";
    sc_lookup(rs_hh, $var_sql_hh);

    if (!empty({rs_hh})) {
        $var_planned = (float){rs_hh[0][0]};
        $var_total     = (float){rs_hh[0][1]};
        $var_pct = ($var_planned > 0) ? round(($var_total / $var_planned) * 100) : 0;

        if ($var_planned > 0 && $var_total > $var_planned) {
            $var_excesso = $var_total - $var_planned;
            $var_min = round($var_excesso * 60);
            {hh_alert} = "<span style='background:#d93025; color:#fff; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:bold; white-space:nowrap;'> Horas Excedidas +" . number_format($var_excesso, 2) . "h (" . $var_min . "min)</span>";
        } elseif ($var_planned > 0 && $var_total >= ($var_planned * 0.8)) {
            $var_min_total = round($var_total * 60);
            $var_min_plan = round($var_planned * 60);
            {hh_alert} = "<span style='background:#f9ab00; color:#fff; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:bold; white-space:nowrap;'> " . $var_pct . "% — " . number_format($var_total, 2) . "h (" . $var_min_total . "min) / " . number_format($var_planned, 2) . "h (" . $var_min_plan . "min)</span>";
        } elseif ($var_planned > 0) {
            $var_min_total = round($var_total * 60);
            $var_min_plan = round($var_planned * 60);
            {hh_alert} = "<span style='background:#188038; color:#fff; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:bold; white-space:nowrap;'> OK — " . number_format($var_total, 2) . "h (" . $var_min_total . "min) / " . number_format($var_planned, 2) . "h (" . $var_min_plan . "min)</span>";
        } else {
            {hh_alert} = "";
        }
    } else {
        {hh_alert} = "";
    }
}
