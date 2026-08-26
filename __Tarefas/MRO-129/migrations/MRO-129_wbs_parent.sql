-- =============================================================
-- MRO-129 - Hierarquia WBS: parent_wbs_id (FK auto-referenciada)
-- Data: 25/08/2026
-- -------------------------------------------------------------
-- Objetivo:
--  Modelar o parentesco NÍVEL -> SUB-NÍVEL na mro_wbs.
--  Cada SUB-NÍVEL aponta para o seu NÍVEL pai via parent_wbs_id.
--  Criterio: prefixo do codigo (WS.* -> WS, TA.* -> TA, NR.* -> NR).
--  Os NÍVEIS raiz (WS, TA, NR) ficam com parent_wbs_id = NULL.
-- =============================================================

BEGIN;

-- 1) Adiciona a coluna parent_wbs_id
ALTER TABLE mro_wbs ADD COLUMN IF NOT EXISTS parent_wbs_id integer;

-- 2) Popula o pai derivando do prefixo do codigo
--    split_part(wbs_code, '.', 1) retorna o prefixo (WS, TA, NR)
UPDATE mro_wbs filho
SET parent_wbs_id = pai.wbs_id
FROM mro_wbs pai
WHERE pai.wbs_code = split_part(filho.wbs_code, '.', 1)
  AND filho.wbs_code <> pai.wbs_code;

-- 3) Adiciona a FK auto-referenciada (idempotente)
ALTER TABLE mro_wbs DROP CONSTRAINT IF EXISTS mro_wbs_parent_wbs_id_fkey;
ALTER TABLE mro_wbs
  ADD CONSTRAINT mro_wbs_parent_wbs_id_fkey
  FOREIGN KEY (parent_wbs_id) REFERENCES mro_wbs(wbs_id) ON DELETE SET NULL;

COMMIT;

-- =============================================================
-- NOTAS
-- -------------------------------------------------------------
-- - Executar APOS o MRO-129_wbs_global.sql (a tabela ja tem os
--   28 registros do WBS.xlsx).
-- - wbs_level continua indicando NIVEL/SUB-NIVEL; o parentesco
--   real fica em parent_wbs_id.
-- - ON DELETE SET NULL: se um NIVEL for excluido, os SUB-NIVEIS
--   ficam sem pai (nao sao apagados).
-- =============================================================
