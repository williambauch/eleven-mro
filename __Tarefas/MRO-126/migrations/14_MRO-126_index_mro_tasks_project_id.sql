-- =============================================
-- MRO-126: Indice em mro_tasks(project_id)
-- Beneficia o blank_gantt e o blank_kanban_critical,
-- que filtram por project_id (unico ou IN lista).
-- Sem o indice, o PostgreSQL faz Seq Scan na tabela
-- inteira (18.766 rows) para filtrar os projetos.
-- =============================================

CREATE INDEX IF NOT EXISTS idx_mro_tasks_project_id
    ON public.mro_tasks (project_id);

-- =============================================
-- Validacao (apos executar):
-- SELECT indexname, indexdef FROM pg_indexes
-- WHERE tablename = 'mro_tasks'
--   AND indexname = 'idx_mro_tasks_project_id';
-- =============================================
