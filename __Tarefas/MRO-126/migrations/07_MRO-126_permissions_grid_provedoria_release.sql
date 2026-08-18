-- =============================================
-- MRO-126: Permissoes para grid_provedoria_release
-- Provedoria - Monitoramento de Liberacao (Gated Process)
-- Copia as mesmas permissoes de grid_public_mro_tasks
-- (a grid_provedoria_release e duplicata dela)
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_provedoria_release', 'grid', 'Provedoria - Monitoramento de Liberacao (Gated Process) - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_provedoria_release');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_provedoria_release', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'grid_public_mro_tasks'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
