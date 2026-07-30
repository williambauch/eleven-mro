-- MRO-120: Atualiza actual_qty_hours dos assignments existentes
-- com base na soma dos duration_minutes do mro_timesheet.
--
-- Uso opcional em producao — roda apenas uma vez para backfill.
-- A partir da implementacao, o campo e atualizado automaticamente
-- via control_pause_task toda vez que uma sessao e fechada.
--

UPDATE mro_task_assignments a
SET actual_qty_hours = (
    SELECT COALESCE(SUM(ts.duration_minutes), 0)::numeric / 60
    FROM mro_timesheet ts
    WHERE ts.assignment_id = a.assignment_id
    AND ts.duration_minutes IS NOT NULL
)
WHERE EXISTS (
    SELECT 1
    FROM mro_timesheet ts
    WHERE ts.assignment_id = a.assignment_id
    AND ts.duration_minutes IS NOT NULL
);
