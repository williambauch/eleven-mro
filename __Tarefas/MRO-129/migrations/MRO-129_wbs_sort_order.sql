-- =============================================================
-- MRO-129 - Coluna de ordenacao (sort_order) na mro_wbs
-- Data: 25/08/2026
-- -------------------------------------------------------------
-- Objetivo:
--  Adicionar coluna sort_order para ordenacao do cadastro WBS.
--  Valores preenchidos de 5 em 5 (10, 15, 20...) na ordem atual,
--  permitindo inserir novos itens no meio sem reordenar tudo.
--  (nome sort_order, pois ORDER e palavra reservada no PostgreSQL)
-- =============================================================

BEGIN;

-- 1) Adiciona a coluna (idempotente)
ALTER TABLE mro_wbs ADD COLUMN IF NOT EXISTS sort_order integer;

-- 2) Popula de 5 em 5 na ordem atual (wbs_id)
WITH ordenado AS (
    SELECT wbs_id, row_number() OVER (ORDER BY wbs_id) * 5 AS novo_ordem
    FROM mro_wbs
)
UPDATE mro_wbs w
SET sort_order = o.novo_ordem
FROM ordenado o
WHERE w.wbs_id = o.wbs_id;

COMMIT;

-- =============================================================
-- NOTAS
-- -------------------------------------------------------------
-- - Ordem final: WS=10, WS.TC=15, WS.DT=20, ..., NR.CAN=140.
-- - Ao inserir novo WBS entre dois existentes, usar valor
--   intermediario (ex: 12 para ficar entre 10 e 15).
-- =============================================================
