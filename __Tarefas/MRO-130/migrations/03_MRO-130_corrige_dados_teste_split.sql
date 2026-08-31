-- =============================================
-- MRO-130: Corrige dados de teste corrompidos do split
-- Assignments com actual_qty_hours absurdos (dados de teste)
-- e timesheets abertos ha dias (cronometro esquecido).
-- Rodar APOS a regra de bloqueio de clock-in aberto estar ativa.
-- =============================================

-- 1. Zera actual_qty_hours corrompidos (valores absurdos de teste)
UPDATE "public"."mro_task_assignments"
SET actual_qty_hours = NULL,
    updated_at = CURRENT_TIMESTAMP
WHERE assignment_id IN (15722, 15730)
  AND actual_qty_hours > 10;

-- 2. Fecha timesheets IN_PROGRESS orfaos (esquecidos ha dias) da task 18152
UPDATE "public"."mro_timesheet"
SET status = 'PAUSED',
    end_time = COALESCE(end_time, start_time + interval '1 minute'),
    duration_minutes = COALESCE(duration_minutes,
        ROUND(EXTRACT(EPOCH FROM (COALESCE(end_time, start_time + interval '1 minute') - start_time)) / 60))
WHERE timesheet_id IN (430, 143)
  AND status = 'IN_PROGRESS';

-- 3. Recalcula actual_qty_hours dos assignments afetados a partir dos timesheets fechados
UPDATE "public"."mro_task_assignments" a
SET actual_qty_hours = (
        SELECT COALESCE(SUM(ts.duration_minutes), 0)::numeric / 60
        FROM mro_timesheet ts
        WHERE ts.assignment_id = a.assignment_id
          AND ts.duration_minutes IS NOT NULL
    ),
    updated_at = CURRENT_TIMESTAMP
WHERE a.assignment_id IN (15722, 15730);

COMMENT ON COLUMN "public"."mro_task_assignments"."actual_qty_hours"
    IS 'Tempo efetivamente apontado (soma dos timesheets fechados). MRO-130: split exige clock-in fechado antes de redistribuir horas.';
