-- =============================================
-- MRO-130: Limpa assignments orfaos com horas zeradas
-- Remove assignments com planned_qty_hours = 0 (ou NULL) que nao possuem
-- nenhum evento, timesheet ou mecanico atribuido — lixo de importacao
-- em massa (created_by = NULL), sem relevancia para o negocio.
-- O rateio do split passou a ignorar estes registros.
-- =============================================

-- 1. Apaga apenas os totalmente orfaos (sem eventos, timesheets e mecanico)
DELETE FROM "public"."mro_task_assignments" a
WHERE (a.planned_qty_hours = 0 OR a.planned_qty_hours IS NULL)
  AND a.status_code IN ('NOT_STARTED','ASSIGNED','PLANNED')
  AND a.executed_by_employee_id IS NULL
  AND NOT EXISTS (SELECT 1 FROM "public"."mro_assignment_events" e WHERE e.assignment_id = a.assignment_id)
  AND NOT EXISTS (SELECT 1 FROM "public"."mro_timesheet" ts WHERE ts.assignment_id = a.assignment_id);

-- 2. Auditoria: verifica quantos restaram (deve ser 0 se tudo limpo)
-- SELECT COUNT(*) FROM mro_task_assignments
-- WHERE (planned_qty_hours = 0 OR planned_qty_hours IS NULL)
--   AND status_code IN ('NOT_STARTED','ASSIGNED','PLANNED');
