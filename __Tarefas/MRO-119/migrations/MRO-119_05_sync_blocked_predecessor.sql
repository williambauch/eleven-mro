-- ===================================================================
-- MRO-119: Sincronizar is_blocked_predecessor para registros existentes
--
-- Deve rodar APOS as migrations 03 e 04.
-- A trigger ja esta ativa, entao novas operacoes serao automaticas.
-- Este UPDATE e necessario apenas para os registros que ja existiam
-- antes da criacao da coluna e triggers.
-- ===================================================================

BEGIN;

UPDATE mro_tasks t
SET is_blocked_predecessor = EXISTS (
    SELECT 1
    FROM mro_task_dependencies d
    JOIN mro_tasks pred ON pred.task_id = d.predecessor_task_id
    WHERE d.successor_task_id = t.task_id
      AND pred.status_code NOT IN ('COMPLETED', 'CANCELLED')
)
WHERE t.task_id IN (
    SELECT successor_task_id FROM mro_task_dependencies
);

COMMIT;
