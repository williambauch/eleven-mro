-- =============================================================
-- MRO-129 - Item 3: WBS como cadastro GLOBAL (status da task, 1:1)
-- Data: 25/08/2026
-- -------------------------------------------------------------
-- Objetivo:
--  1. Remover a ligacao mro_wbs.project_id -> mro_projects
--  2. Adicionar wbs_level (NIVEL / SUB-NIVEL) na mro_wbs
--  3. Truncar a mro_wbs e importar a lista definitiva do WBS.xlsx
--  4. Reativar a FK mro_tasks.wbs_id -> mro_wbs.wbs_id
--  5. Sincronizar mro_tasks.wbs_id (mapeamento por nome -> codigo)
-- =============================================================

BEGIN;

-- -------------------------------------------------------------
-- 0) BACKUP dos dados atuais (para rollback / consulta)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS mro_wbs_bkp_20260825;
CREATE TABLE mro_wbs_bkp_20260825 AS SELECT * FROM mro_wbs;

-- -------------------------------------------------------------
-- 1) Desliga a FK da task (para poder truncar a mro_wbs)
-- -------------------------------------------------------------
ALTER TABLE mro_tasks DROP CONSTRAINT IF EXISTS mro_tasks_wbs_id_fkey;

-- -------------------------------------------------------------
-- 2) Remove a ligacao com projeto (FK + coluna)
--    ATENCAO: as views view_kanban_datasource e view_gantt_tracking
--    dependiam de mro_wbs.project_id. Elas JA FORAM ajustadas no
--    arquivo MRO-129_views_kanban_gantt.sql (executar ANTES deste).
-- -------------------------------------------------------------
ALTER TABLE mro_wbs DROP CONSTRAINT IF EXISTS mro_wbs_project_id_fkey;
ALTER TABLE mro_wbs DROP COLUMN IF EXISTS project_id;

-- -------------------------------------------------------------
-- 3) Adiciona wbs_level (NIVEL / SUB-NIVEL) e unique no codigo
-- -------------------------------------------------------------
ALTER TABLE mro_wbs ADD COLUMN IF NOT EXISTS wbs_level varchar(20);
ALTER TABLE mro_wbs ADD CONSTRAINT mro_wbs_wbs_code_unique UNIQUE (wbs_code);

-- -------------------------------------------------------------
-- 4) Limpa e recomeca (os dados vem do WBS.xlsx - Danilo)
--    Salvaguarda: garante que a FK da task esta removida ANTES do
--    truncate (nao usar CASCADE, pois truncaria mro_tasks junto).
-- -------------------------------------------------------------
ALTER TABLE mro_tasks DROP CONSTRAINT IF EXISTS mro_tasks_wbs_id_fkey;
TRUNCATE TABLE mro_wbs RESTART IDENTITY;

-- -------------------------------------------------------------
-- 5) Importacao da lista definitiva (WBS.xlsx)
--    Código | Descricao | Nivel
-- -------------------------------------------------------------
INSERT INTO mro_wbs (wbs_code, wbs_name, wbs_level) VALUES
('WS',      'TAREFAS DO WORKSCOPE',                                  'NIVEL'),
('WS.TC',   'TASK CARD DO CHECK',                                    'SUB-NIVEL'),
('WS.DT',   'DIRETIVAS TECNICAS',                                    'SUB-NIVEL'),
('WS.CE',   'CARTOES ESPECIAIS',                                     'SUB-NIVEL'),
('WS.CPCP', 'PROGRAMA DE CONTROLE E PREVENCAO A CORROSAO',           'SUB-NIVEL'),
('WS.MD',   'MODIFICACOES',                                          'SUB-NIVEL'),
('WS.RPCP', 'COMPONENTES CONTROLADOS',                               'SUB-NIVEL'),
('WS.DGX',  'PROCEDIMENTOS DIGEX',                                   'SUB-NIVEL'),
('WS.TM',   'TAREFAS DO WORKSCOPE - TIME & MATERIAL (TATM)',         'SUB-NIVEL'),
('WS.RTCN', 'ROTINAS CANCELADAS',                                    'SUB-NIVEL'),
('WS.RET',  'RETRABALHO DIGEX',                                      'SUB-NIVEL'),
('TA',      'TAREFAS ADICIONAIS',                                    'NIVEL'),
('TA.CPCP', 'PROGRAMA DE CONTROLE E PREVENCAO A CORROSAO ADICIONAIS','SUB-NIVEL'),
('TA.DT',   'DIRETIVAS TECNICAS ADICIONAIS',                         'SUB-NIVEL'),
('TA.MD',   'MODIFICACOES ADICIONAIS',                               'SUB-NIVEL'),
('TA.RPCP', 'COMPONENTES CONTROLADOS ADICIONAIS',                    'SUB-NIVEL'),
('TA.PR',   'PILOT REPORTS',                                         'SUB-NIVEL'),
('TA.TAFR', 'TAREFAS ADICIONAIS - FLAT RATE',                        'SUB-NIVEL'),
('TA.TATM', 'TAREFAS ADICIONAIS - TIME & MATERIAL',                  'SUB-NIVEL'),
('NR',      'NAO ROTINAS',                                           'NIVEL'),
('NR.AGACM', 'NR''S - ACIMA DO CAP - AGUARDANDO APROVACAO DO CLIENTE', 'SUB-NIVEL'),
('NR.AGZER', 'NR''S - CAP ZERO - AGUARDANDO APROVACAO DO CLIENTE',   'SUB-NIVEL'),
('NR.APACM', 'NR''S - ACIMA DO CAP - APROVADAS',                     'SUB-NIVEL'),
('NR.APZER', 'NR''S - CAP ZERO - APROVADAS',                         'SUB-NIVEL'),
('NR.NOV',   'NR''S - NOVAS',                                        'SUB-NIVEL'),
('NR.APTM',  'NR''S - APROVADAS TIME & MATERIAL',                    'SUB-NIVEL'),
('NR.IND',   'NR''S - INDEFERIDAS',                                  'SUB-NIVEL'),
('NR.CAN',   'NR''S - CANCELADAS',                                   'SUB-NIVEL');

-- -------------------------------------------------------------
-- 6) Sincroniza mro_tasks.wbs_id
--    Mapeia os WBS antigos (por nome normalizado) para os novos
--    codigos globais. Tasks sem match ficam com wbs_id NULL.
--    (Executado ANTES de reativar a FK, para nao viola-la)
-- -------------------------------------------------------------

-- 6.1) Mapeamento nome antigo (normalizado) -> codigo novo
CREATE TEMP TABLE tmp_wbs_map (old_name text, new_code varchar);

INSERT INTO tmp_wbs_map (old_name, new_code) VALUES
('TASK CARD DO CHECK',                          'WS.TC'),
('DIRETIVAS TECNICAS',                          'WS.DT'),
('PROCEDIMENTOS DIGEX',                         'WS.DGX'),
('CARTOES ESPECIAIS',                           'WS.CE'),
('TAREFAS DO WORKSCOPE EM TIME & MATERIAL',     'WS.TM'),
('ROTINAS CANCELADAS',                          'WS.RTCN'),
('RETRABALHO DIGEX',                            'WS.RET'),
('TAREFAS ADICIONAIS EM TIME & MATERIAL',       'TA.TATM'),
('CANCELADA',                                   'NR.CAN'),
('APROVADAS CAP ZERO',                          'NR.APZER'),
('APROVADAS ACIMA DO CAP',                      'NR.APACM'),
('AGUARDANDO APROVACAO DO CLIENTE CAP ZERO',    'NR.AGZER');
-- NOTA: 'APROVADAS ABAIXO DO CAP' (441 tasks) e 'GOL LINHAS AEREAS...' (2 tasks)
--       ficam sem vínculo (wbs_id = NULL) — PENDENTE decisão do Danilo sobre o
--       código equivalente no WBS.xlsx.

-- 6.2) Atualiza as tasks pelo nome normalizado do WBS antigo
UPDATE mro_tasks t
SET wbs_id = nw.wbs_id
FROM mro_wbs_bkp_20260825 b
JOIN tmp_wbs_map m
  ON m.old_name = translate(
         upper(replace(replace(b.wbs_name, 'TÃ©cnicas', 'Tecnicas'), 'AprovaÃ§Ã£o', 'Aprovacao')),
         'ÁÉÍÓÚÀÂÊÔÃÕÇ', 'AEIOUAEAOAOC')
JOIN mro_wbs nw ON nw.wbs_code = m.new_code
WHERE t.wbs_id = b.wbs_id
  AND t.wbs_id IS NOT NULL;

-- 6.3) Limpa a temp
DROP TABLE tmp_wbs_map;

-- -------------------------------------------------------------
-- 7) Reativa a FK da task (task.wbs_id -> wbs global)
-- -------------------------------------------------------------
ALTER TABLE mro_tasks
  ADD CONSTRAINT mro_tasks_wbs_id_fkey
  FOREIGN KEY (wbs_id) REFERENCES mro_wbs(wbs_id) ON DELETE NO ACTION;

COMMIT;

-- =============================================================
-- NOTAS / PENDENCIAS
-- -------------------------------------------------------------
-- - Nomes antigos nao mapeados ficam com wbs_id NULL na task
--   (ex.: 'GOL LINHAS AEREAS...', 'Aguardando Aprovacao do
--   Cliente CAP ZERO' -> sem equivalente direto no WBS.xlsx).
-- - Ajuste no reports/jobcard: join agora usa t.project_id
--   (task ja tem project_id proprio).
-- - form_public_mro_projects: campo 'wbs' (varchar) removido
--   do config.json (a coluna ja nao existia no banco).
-- =============================================================
