-- =============================================
-- MRO-126: Permissoes para grid_mro_reports_mechanic
-- Painel do mecanico - Ferramentas com avaria
-- Lista as devolucoes DANO/PERDA do mecanico logado,
-- com ou sem relatorio em mro_tool_reports.
--
-- Grupos:
--   3  (MECANICO)      - acesso + insert? nao: somente leitura + acao de abrir form
--   12 (FERRAMENTARIA) - acesso total para consultar avarias
--   1  (Administrador) - ja possui acesso total
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_mro_reports_mechanic', 'grid', 'Painel do Mecanico - Ferramentas com Avaria - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_mro_reports_mechanic');

-- MECANICO: somente leitura (consulta + abrir relatorio)
INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
VALUES ('grid_mro_reports_mechanic', 3, 'Y', '', '', '', '', '')
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- FERRAMENTARIA: acesso + export/print (consultar e imprimir avarias)
INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
VALUES ('grid_mro_reports_mechanic', 12, 'Y', '', '', '', 'Y', 'Y')
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
