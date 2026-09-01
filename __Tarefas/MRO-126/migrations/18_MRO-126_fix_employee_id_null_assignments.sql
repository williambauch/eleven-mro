-- =============================================
-- MRO-126 / UAT FR-03: Corrige employee_id NULL em mro_task_assignments
--
-- Contexto: o Play (form_public_mro_task_assignments) e a ROTA 5 da
-- control_pause_task atualizavam o assignment para IN_PROGRESS sem
-- preencher employee_id, deixando o campo NULL. Isso quebrava o bloqueio
-- FR-03 (fechamento de JIC com ferramenta em custodia), que nao encontrava
-- as ferramentas do mecanico.
--
-- Correcao: preenche employee_id do assignment com o employee_id do
-- timesheet mais recente vinculado (fonte confiavel do mecanico que
-- trabalhou). Idempotente: so atualiza onde employee_id IS NULL.
-- =============================================

UPDATE public.mro_task_assignments a
SET employee_id = ts.employee_id
FROM mro_timesheet ts
WHERE a.assignment_id = ts.assignment_id
  AND a.employee_id IS NULL
  AND ts.employee_id IS NOT NULL
  AND ts.timesheet_id = (
      SELECT MAX(ts2.timesheet_id)
      FROM mro_timesheet ts2
      WHERE ts2.assignment_id = a.assignment_id
        AND ts2.employee_id IS NOT NULL
  );
