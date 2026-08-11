-- ============================================================
-- MRO-123 - Popular pending_id de exemplo no projeto 7
--
-- Objetivo: gerar volume de dados de pendencia (pending_id)
-- no projeto 7 (TOT03/25) para validar o grafico de gargalos
-- por pendencia no dashboard de encerramento.
--
-- ATENCAO: script de TESTE/DEMONSTRACAO. Altera dados reais.
-- Rode com criterio e, se precisar, desfaça com o UPDATE de
-- reversao no final deste arquivo.
--
-- Amostra: tasks do projeto 7 onde task_id % 7 = 0 (~221 tasks),
-- distribuidas em 13 tipos de pendencia (task_id % 13).
-- Inclui tasks ja finalizadas (COMPLETED) propositalmente,
-- para o dashboard exibir exemplos.
-- ============================================================

-- ------------------------------------------------------------
-- 1. VALIDACAO PREVIA: quantas tasks serao afetadas e como
--    ficara a distribuicao de pendencia (opcional, so leitura)
-- ------------------------------------------------------------
SELECT
    CASE (task_id % 13)
        WHEN 0 THEN 6      -- PARTS
        WHEN 1 THEN 5      -- TOOLS
        WHEN 2 THEN 18     -- APROV (Cliente)
        WHEN 3 THEN 17     -- CUSTOMER
        WHEN 4 THEN 21     -- ENG DEFINI
        WHEN 5 THEN 22     -- PLANEJ
        WHEN 6 THEN 24     -- PREDEC
        WHEN 7 THEN 39     -- FMO
        WHEN 8 THEN 36     -- BORESCOPE
        WHEN 9 THEN 35     -- RII
        WHEN 10 THEN 46    -- FTEST
        WHEN 11 THEN 40    -- OPENING
        WHEN 12 THEN 50    -- NDT
    END AS pending_id,
    COUNT(*) AS qtde_afetadas
FROM public.mro_tasks
WHERE project_id = 7
  AND task_id % 7 = 0
GROUP BY 1
ORDER BY 1;

-- ------------------------------------------------------------
-- 2. UPDATE PRINCIPAL: aplica a distribuicao de pendencia
-- ------------------------------------------------------------
UPDATE public.mro_tasks
SET pending_id = CASE (task_id % 13)
        WHEN 0 THEN 6      -- PARTS
        WHEN 1 THEN 5      -- TOOLS
        WHEN 2 THEN 18     -- APROV (Cliente)
        WHEN 3 THEN 17     -- CUSTOMER
        WHEN 4 THEN 21     -- ENG DEFINI
        WHEN 5 THEN 22     -- PLANEJ
        WHEN 6 THEN 24     -- PREDEC
        WHEN 7 THEN 39     -- FMO
        WHEN 8 THEN 36     -- BORESCOPE
        WHEN 9 THEN 35     -- RII
        WHEN 10 THEN 46    -- FTEST
        WHEN 11 THEN 40    -- OPENING
        WHEN 12 THEN 50    -- NDT
    END,
    updated_at = CURRENT_TIMESTAMP
WHERE project_id = 7
  AND task_id % 7 = 0;

-- ------------------------------------------------------------
-- 3. VALIDACAO POS-UPDATE: distribuicao resultante
-- ------------------------------------------------------------
SELECT p.pending_code, p.pending_description, COUNT(*) AS qtde
FROM public.mro_tasks t
JOIN public.mro_tasks_pending_status p ON t.pending_id = p.pending_id
WHERE t.project_id = 7
  AND t.pending_id IS NOT NULL
GROUP BY p.pending_code, p.pending_description
ORDER BY qtde DESC;

-- ------------------------------------------------------------
-- 4. REVERSAO (se precisar desfazer o teste)
-- ------------------------------------------------------------
-- UPDATE public.mro_tasks
-- SET pending_id = NULL,
--     updated_at = CURRENT_TIMESTAMP
-- WHERE project_id = 7
--   AND task_id % 7 = 0;
