-- ============================================================
-- MRO-126 - Preencher datas de planejamento (target_start/end)
-- das tasks do projeto 2 (MODELO 737NG) que estao sem data.
--
-- Regra de negocio (MRO-126 Parte 02):
--   - Rotina com fase definida exige target_start/end (validacao no form)
--   - Tasks sem data nao exibem barra no Gantt (problema relatado)
--
-- Estrategia:
--   1) ROTINAS sem data -> distribuicao uniforme ao longo do
--      periodo do projeto, agrupadas por fase (ordem logica),
--      com duracao baseada em estimated_hours (minimo 1 dia).
--   2) NRCs -> herdam target_start/end da rotina de origem
--      (parent_task_id), quando a rotina tiver data.
--
-- BACKUP (executar antes, salvar o retorno):
--   SELECT task_id, task_code, project_id, is_nrc, phase_code,
--          target_start, target_end, estimated_hours, updated_at
--   FROM mro_tasks
--   WHERE project_id = 2;
-- ============================================================

BEGIN;

-- ------------------------------------------------------------
-- 1) ROTINAS SEM DATA: distribuir uniformemente no periodo
-- ------------------------------------------------------------
WITH projeto AS (
    SELECT project_id, start_date, end_date
    FROM mro_projects
    WHERE project_id = 2
),
base AS (
    SELECT t.task_id,
           COALESCE(t.phase_code, 'ZZ_SEM_FASE') AS fase,
           GREATEST(1, CEIL(COALESCE(t.estimated_hours, 8) / 8))::int AS dur_dias
    FROM mro_tasks t
    WHERE t.project_id = 2
      AND t.is_nrc = false
      AND (t.target_start IS NULL OR t.target_end IS NULL)
),
ord AS (
    SELECT b.task_id, b.fase, b.dur_dias,
           ROW_NUMBER() OVER (
               ORDER BY CASE b.fase
                   WHEN 'INDUCTI' THEN 1
                   WHEN 'OP/FUNC' THEN 2
                   WHEN 'A OPEN'  THEN 3
                   ELSE 4
               END,
               b.task_id
           ) - 1 AS rn,
           COUNT(*) OVER () AS total
    FROM base b
),
calc AS (
    SELECT o.task_id, o.dur_dias, o.rn, o.total,
           -- distribui entre start_date e end_date-2 (folga no fim)
           p.start_date::date
             + (o.rn * (p.end_date::date - p.start_date::date - 2)
                / GREATEST(o.total - 1, 1))::int AS inicio
    FROM ord o
    CROSS JOIN projeto p
)
UPDATE mro_tasks t
SET target_start = c.inicio,
    target_end   = c.inicio + GREATEST(c.dur_dias - 1, 0),
    updated_at   = CURRENT_TIMESTAMP
FROM calc c
WHERE t.task_id = c.task_id;

-- ------------------------------------------------------------
-- 2) NRCs SEM DATA: herdar datas da rotina de origem
-- ------------------------------------------------------------
UPDATE mro_tasks t
SET target_start = p.target_start,
    target_end   = p.target_end,
    updated_at   = CURRENT_TIMESTAMP
FROM mro_tasks p
WHERE t.project_id = 2
  AND t.is_nrc = true
  AND p.task_id = t.parent_task_id
  AND p.target_start IS NOT NULL
  AND (t.target_start IS NULL OR t.target_end IS NULL);

COMMIT;

-- ------------------------------------------------------------
-- VALIDACAO (executar apos):
--   SELECT COUNT(*) FILTER (WHERE target_start IS NULL OR target_end IS NULL) AS ainda_sem_data
--   FROM mro_tasks WHERE project_id = 2;
-- ------------------------------------------------------------
