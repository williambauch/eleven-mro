-- ===================================================================
-- MRO-119: Adicionar coluna is_blocked_predecessor em mro_tasks
--          + indices para performance da trigger
--
-- Demanda Rodrigo Souza 28/07/2026: filtro de tarefas com impedimento
-- por tarefa predecessora.
--
-- Esta coluna sera atualizada automaticamente por trigger quando:
-- 1. Uma dependencia for criada (INSERT ou UPDATE ou DELETE em mro_task_dependencies)
-- 2. Uma predecessora for concluida (UPDATE status_code em mro_tasks)
-- ===================================================================

BEGIN;

ALTER TABLE public.mro_tasks
ADD COLUMN is_blocked_predecessor boolean NOT NULL DEFAULT false;

-- Indices para performance das subqueries da trigger
CREATE INDEX IF NOT EXISTS idx_task_dep_successor
ON public.mro_task_dependencies(successor_task_id);

CREATE INDEX IF NOT EXISTS idx_task_dep_predecessor
ON public.mro_task_dependencies(predecessor_task_id);

COMMIT;
