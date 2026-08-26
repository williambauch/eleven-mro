-- =============================================================
-- MRO-129 - Views dependentes de mro_wbs.project_id
-- Data: 25/08/2026
-- -------------------------------------------------------------
-- IMPORTANTE: executar ESTE arquivo ANTES do
-- MRO-129_wbs_global.sql (que remove a coluna project_id da mro_wbs).
-- -------------------------------------------------------------
-- Ajustes:
--  - view_kanban_datasource: w.project_id -> t.project_id
--    (a task ja possui project_id proprio)
--  - view_gantt_tracking:
--      * bloco PHASE (WBS): project_id derivado das tasks que usam o WBS
--        (WBS agora e global, sem project_id proprio)
--      * bloco TASK: w.project_id -> t.project_id
-- =============================================================

BEGIN;

-- -------------------------------------------------------------
-- 1) view_kanban_datasource
-- -------------------------------------------------------------
CREATE OR REPLACE VIEW public.view_kanban_datasource AS
SELECT
    t.task_id,
    t.task_code AS codigo,
    t.task_name AS descricao,
    COALESCE(t.status_code, 'NOT_STARTED'::character varying) AS status_chave,
    COALESCE(w.wbs_name, 'SEM FASE'::character varying) AS fase_nome,
    CASE
        WHEN t.is_nrc = true THEN 'High'::text
        ELSE 'Normal'::text
    END AS prioridade,
    CASE
        WHEN t.is_nrc = true THEN 'Não Rotina'::text
        ELSE 'Rotina'::text
    END AS tipo_tarefa,
    COALESCE(t.skill_code, 'Geral'::character varying) AS skill,
    COALESCE(t.project_id, 0) AS project_id,
    "left"(COALESCE(t.skill_code, 'G'::character varying)::text, 2) AS iniciais_skill
FROM mro_tasks t
LEFT JOIN mro_wbs w ON t.wbs_id = w.wbs_id
WHERE t.status_code IS NOT NULL;

-- -------------------------------------------------------------
-- 2) view_gantt_tracking
-- -------------------------------------------------------------
CREATE OR REPLACE VIEW public.view_gantt_tracking AS
SELECT
    'PROJ_'::text || p.project_id AS id_unico,
    NULL::text AS id_pai,
    p.project_name AS nome_tarefa,
    p.start_date AS data_inicio,
    p.end_date AS data_fim,
    p.start_date AS baseline_inicio,
    p.end_date AS baseline_fim,
    0 AS progresso,
    'PROJECT'::text AS tipo,
    false AS critico,
    p.project_id
FROM mro_projects p
UNION ALL
SELECT
    'WBS_'::text || w.wbs_id AS id_unico,
    'PROJ_'::text || COALESCE((SELECT DISTINCT t2.project_id FROM mro_tasks t2 WHERE t2.wbs_id = w.wbs_id LIMIT 1), 0) AS id_pai,
    w.wbs_name AS nome_tarefa,
    NULL::date AS data_inicio,
    NULL::date AS data_fim,
    NULL::date AS baseline_inicio,
    NULL::date AS baseline_fim,
    0 AS progresso,
    'PHASE'::text AS tipo,
    false AS critico,
    COALESCE((SELECT DISTINCT t2.project_id FROM mro_tasks t2 WHERE t2.wbs_id = w.wbs_id LIMIT 1), 0) AS project_id
FROM mro_wbs w
UNION ALL
SELECT
    t.task_id::character varying AS id_unico,
    CASE
        WHEN t.parent_task_id IS NOT NULL THEN t.parent_task_id::character varying::text
        ELSE 'WBS_'::text || t.wbs_id
    END AS id_pai,
    t.task_name AS nome_tarefa,
    t.target_start AS data_inicio,
    t.target_end AS data_fim,
    t.baseline_start AS baseline_inicio,
    t.baseline_end AS baseline_fim,
    CASE
        WHEN t.status_code::text = 'COMPLETED'::text THEN 100
        WHEN t.status_code::text = 'IN_PROGRESS'::text THEN 50
        ELSE 0
    END AS progresso,
    CASE
        WHEN t.is_nrc = true THEN 'NRC'::text
        ELSE 'TASK'::text
    END AS tipo,
    t.is_critical_path AS critico,
    t.project_id
FROM mro_tasks t
JOIN mro_wbs w ON t.wbs_id = w.wbs_id;

COMMIT;

-- =============================================================
-- NOTAS
-- -------------------------------------------------------------
-- - Backups originais: backup_view_kanban_datasource.sql e
--   backup_view_gantt_tracking.sql (mesma pasta).
-- - Apps consumidoras: Kanban/blank_kanban_board e
--   gantt/blank_gantt_tracking (colunas de saida inalteradas).
-- =============================================================
