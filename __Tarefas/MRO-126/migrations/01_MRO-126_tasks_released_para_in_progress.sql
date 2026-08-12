-- =============================================
-- MRO-126: Tasks RELEASED -> IN_PROGRESS
-- Descricao: Atualiza para IN_PROGRESS as tasks
-- que ja possuem pelo menos um mecanico atribuido
-- (executed_by_employee_id preenchido), mas que
-- ficaram presas em RELEASED por terem sido
-- liberadas antes da regra de transicao existir.
--
-- Regra de negocio (MRO-126):
-- Quando o primeiro mecanico e atribuido a uma
-- task RELEASED, a task deve ir para IN_PROGRESS.
-- Este script corrige o historico retroativo.
--
-- Idempotente: usa WHERE status_code = 'RELEASED',
-- entao pode ser executado mais de uma vez sem
-- efeito colateral.
-- =============================================

UPDATE "public".mro_tasks AS t
SET status_code = 'IN_PROGRESS',
    updated_at  = CURRENT_TIMESTAMP
WHERE t.status_code = 'RELEASED'
  AND EXISTS (
        SELECT 1
        FROM "public".mro_task_assignments AS a
        WHERE a.task_id = t.task_id
          AND a.executed_by_employee_id IS NOT NULL
  );

-- =============================================
-- Validacao (rodar apos o UPDATE)
-- =============================================
-- SELECT t.task_id, t.task_code, t.status_code
-- FROM "public".mro_tasks t
-- JOIN "public".mro_task_assignments a ON a.task_id = t.task_id
-- WHERE t.status_code = 'RELEASED'
--   AND a.executed_by_employee_id IS NOT NULL
-- GROUP BY t.task_id, t.task_code, t.status_code;
-- Deve retornar 0 linhas.
