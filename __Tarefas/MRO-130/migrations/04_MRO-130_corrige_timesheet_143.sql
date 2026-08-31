-- =============================================
-- MRO-130: Corrige timesheet 143 (dado de teste corrompido)
-- O timesheet 143 ficou "rodando" de 06/08 a 20/08 (14 dias),
-- gerando duration_minutes = 20376 (~339h) que contamina o
-- actual_qty_hours do assignment 15722 (Gabriela Lichevis).
-- Rodar APOS a migration 03 (que fechou os timesheets).
-- =============================================

-- 1. Remove eventos de auditoria que referenciam o timesheet 143
-- (necessario antes do DELETE por causa da FK mro_assignment_events_timesheet_id_fkey)
DELETE FROM "public"."mro_assignment_events"
WHERE timesheet_id = 143;

-- 2. Remove o timesheet 143 (lixo de teste que corrompe o calculo)
DELETE FROM "public"."mro_timesheet"
WHERE timesheet_id = 143;

-- 3. Recalcula actual_qty_hours dos assignments afetados (agora sem o 143)
UPDATE "public"."mro_task_assignments" a
SET actual_qty_hours = (
        SELECT COALESCE(SUM(ts.duration_minutes), 0)::numeric / 60
        FROM mro_timesheet ts
        WHERE ts.assignment_id = a.assignment_id
          AND ts.duration_minutes IS NOT NULL
    ),
    updated_at = CURRENT_TIMESTAMP
WHERE a.assignment_id IN (15722, 15730);

-- 4. Validacao: confere se nao sobrou valor absurdo (> 24h) em assignments ativos
-- (apenas consulta de auditoria, nao altera dados)
-- SELECT assignment_id, actual_qty_hours
-- FROM mro_task_assignments
-- WHERE actual_qty_hours > 24;
