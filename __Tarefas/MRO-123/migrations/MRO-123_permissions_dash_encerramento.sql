-- =============================================
-- MRO-123: Permissoes para blank_dash_encerramento
-- e control_dash_encerramento
-- Dashboard de Metricas de Encerramento e Gargalos
-- Copia as permissoes de apps do mesmo tipo ja existentes:
--   blank_dash_encerramento   <- dash_ferramentaria (blank/dashboard)
--   control_dash_encerramento <- control_split_assignment (control)
-- =============================================

-- ------------------------------------------------------------
-- 1. Registrar as apps em sec_apps (se ainda nao existirem)
-- ------------------------------------------------------------
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'blank_dash_encerramento', 'blank', 'Dashboard de Encerramento e Gargalos - MRO-123'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'blank_dash_encerramento');

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'control_dash_encerramento', 'control', 'Filtro do Dashboard de Encerramento e Gargalos - MRO-123'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'control_dash_encerramento');

-- ------------------------------------------------------------
-- 2. Copiar permissoes de grupo para blank_dash_encerramento
--    Origem: dash_ferramentaria
-- ------------------------------------------------------------
INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'blank_dash_encerramento', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'dash_ferramentaria'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- ------------------------------------------------------------
-- 3. Copiar permissoes de grupo para control_dash_encerramento
--    Origem: control_split_assignment
-- ------------------------------------------------------------
INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'control_dash_encerramento', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'control_split_assignment'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- ------------------------------------------------------------
-- 4. VALIDACAO (opcional, so leitura)
-- ------------------------------------------------------------
-- SELECT s.app_name, s.app_type, COUNT(g.group_id) AS grupos
-- FROM sec_apps s
-- LEFT JOIN sec_groups_apps g ON s.app_name = g.app_name
-- WHERE s.app_name IN ('blank_dash_encerramento', 'control_dash_encerramento')
-- GROUP BY s.app_name, s.app_type;
