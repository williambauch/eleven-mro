-- ============================================================
-- MRO-123 - Popular datas reais (baseline/target/actual) em 10
-- tasks COMPLETED do projeto 7 (TOT03/25)
--
-- Objetivo: gerar volume de dados de encerramento com fonte
-- "Manual" (actual_end preenchido) para validar o grafico
-- "Encerramento por Data" do dashboard.
--
-- ATENCAO: script de TESTE/DEMONSTRACAO. Altera dados reais.
-- Rode com criterio; reversao no final do arquivo.
--
-- As datas sao distribuídas por ROW_NUMBER() para que cada task
-- tenha datas diferentes (o grafico mostrara barras em dias
-- distintos, todas na fonte Manual/verde).
-- ============================================================

-- ------------------------------------------------------------
-- 1. VALIDACAO PREVIA (opcional, so leitura)
--    Confirma a amostra e as datas que serao aplicadas
-- ------------------------------------------------------------
SELECT t.task_id, t.task_code,
       (DATE '2026-07-11' + (rn % 30) * INTERVAL '1 day') AS novo_actual_end
FROM (
    SELECT task_id, task_code,
           ROW_NUMBER() OVER (ORDER BY updated_at DESC, task_id) AS rn
    FROM public.mro_tasks
    WHERE project_id = 7 AND status_code = 'COMPLETED'
    ORDER BY updated_at DESC, task_id
    LIMIT 10
) t;

-- ------------------------------------------------------------
-- 2. UPDATE PRINCIPAL: aplica datas nas 10 tasks
--    Distribuicao: actual_end espalhado entre 11/07 e 09/08
-- ------------------------------------------------------------
WITH amostra AS (
    SELECT task_id,
           ROW_NUMBER() OVER (ORDER BY updated_at DESC, task_id) AS rn
    FROM public.mro_tasks
    WHERE project_id = 7 AND status_code = 'COMPLETED'
    ORDER BY updated_at DESC, task_id
    LIMIT 10
)
UPDATE public.mro_tasks t
SET baseline_start = (DATE '2026-06-20' + a.rn * INTERVAL '1 day')::timestamp,
    baseline_end   = (DATE '2026-06-20' + (a.rn + 1) * INTERVAL '1 day')::timestamp,
    target_start   = (DATE '2026-07-05' + a.rn * INTERVAL '1 day')::timestamp,
    target_end     = (DATE '2026-07-05' + (a.rn + 2) * INTERVAL '1 day')::timestamp,
    actual_start   = (DATE '2026-07-08' + a.rn * INTERVAL '1 day')::timestamp,
    actual_end     = (DATE '2026-07-11' + a.rn * INTERVAL '1 day')::timestamp,
    updated_at     = CURRENT_TIMESTAMP
FROM amostra a
WHERE t.task_id = a.task_id;

-- ------------------------------------------------------------
-- 3. VALIDACAO POS-UPDATE: datas aplicadas
-- ------------------------------------------------------------
SELECT task_id, task_code,
       TO_CHAR(baseline_start, 'DD/MM/YYYY') AS baseline_inicio,
       TO_CHAR(baseline_end,   'DD/MM/YYYY') AS baseline_fim,
       TO_CHAR(target_start,   'DD/MM/YYYY') AS target_inicio,
       TO_CHAR(target_end,     'DD/MM/YYYY') AS target_fim,
       TO_CHAR(actual_start,   'DD/MM/YYYY') AS actual_inicio,
       TO_CHAR(actual_end,     'DD/MM/YYYY') AS actual_fim
FROM public.mro_tasks
WHERE project_id = 7
  AND actual_end IS NOT NULL
ORDER BY actual_end
LIMIT 10;

-- ------------------------------------------------------------
-- 4. REVERSAO (se precisar desfazer o teste)
-- ------------------------------------------------------------
-- UPDATE public.mro_tasks
-- SET baseline_start = NULL,
--     baseline_end   = NULL,
--     target_start   = NULL,
--     target_end     = NULL,
--     actual_start   = NULL,
--     actual_end     = NULL,
--     updated_at     = CURRENT_TIMESTAMP
-- WHERE project_id = 7
--   AND task_id IN (15085, 15099, 15120, 15127, 15141, 15148, 15155, 15176, 15183, 15190);
