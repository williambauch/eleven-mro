-- =============================================
-- MRO-126: Tasks que deveriam estar em SUPSIG
-- Descricao: Corrige tasks que ficaram presas em
-- IN_PROGRESS / PENDING_PROG mas cujo ultimo
-- assignment ja foi concluido (SUPSIG).
--
-- Antes da regra MRO-126, o motivo 6 do mecanico
-- atualizava apenas o ASSIGNMENT para SUPSIG e a
-- TASK permanecia IN_PROGRESS/PENDING_PROG.
--
-- Regra aplicada (somente em tasks sem nenhum
-- assignment ativo e com pelo menos um SUPSIG):
--   - Sem assignment em IN_PROGRESS/PAUSED/BLOCKED/
--     ASSIGNED/PENDING_HANDOVER
--   - Com pelo menos um assignment em SUPSIG
--
-- Idempotente: usa WHERE status_code IN (...),
-- pode ser executado mais de uma vez.
-- =============================================

UPDATE "public".mro_tasks AS t
SET status_code = 'SUPSIG',
    updated_at  = CURRENT_TIMESTAMP
WHERE t.status_code IN ('IN_PROGRESS','PENDING_PROG')
  AND NOT EXISTS (
        SELECT 1
        FROM "public".mro_task_assignments AS a
        WHERE a.task_id = t.task_id
          AND a.status_code IN ('IN_PROGRESS','PAUSED','BLOCKED','ASSIGNED','PENDING_HANDOVER')
  )
  AND EXISTS (
        SELECT 1
        FROM "public".mro_task_assignments AS a
        WHERE a.task_id = t.task_id
          AND a.status_code = 'SUPSIG'
  );

-- =============================================
-- Validacao (rodar apos o UPDATE)
-- =============================================
-- SELECT t.task_id, t.task_code, t.status_code
-- FROM "public".mro_tasks t
-- WHERE t.status_code IN ('IN_PROGRESS','PENDING_PROG')
--   AND NOT EXISTS (
--       SELECT 1 FROM "public".mro_task_assignments a
--       WHERE a.task_id = t.task_id
--         AND a.status_code IN ('IN_PROGRESS','PAUSED','BLOCKED','ASSIGNED','PENDING_HANDOVER')
--   )
--   AND EXISTS (
--       SELECT 1 FROM "public".mro_task_assignments a
--       WHERE a.task_id = t.task_id AND a.status_code = 'SUPSIG'
--   );
-- Deve retornar 0 linhas.
