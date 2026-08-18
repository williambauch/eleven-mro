-- =============================================
-- MRO-126: Permissoes para blank_mro_reports_resume
-- Resumo da Ocorrencia (Qualidade SGSO)
-- Exibido apos o insert do form_mro_reports
-- Copia as mesmas permissoes de form_mro_reports
-- (grupos que criam/visualizam o relatorio tambem veem o resumo)
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'blank_mro_reports_resume', 'blank', 'Resumo da Ocorrencia - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'blank_mro_reports_resume');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'blank_mro_reports_resume', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_mro_reports'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
