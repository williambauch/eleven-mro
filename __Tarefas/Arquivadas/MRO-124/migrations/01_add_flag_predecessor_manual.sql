-- public.mro_tasks definição
-- MRO-124 | Item 1: Add bloqueio (flag) predecessor manual na tarefa

-- Drop column (rollback)
-- ALTER TABLE public.mro_tasks DROP COLUMN is_predecessor_manual;

ALTER TABLE public.mro_tasks
ADD COLUMN is_predecessor_manual bool default false not null;