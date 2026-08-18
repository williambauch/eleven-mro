-- =============================================
-- MRO-126: Permissoes para blank_mro_provedoria_bip
-- Provedoria - Bip de Saida
-- Copia as mesmas permissoes de blank_mro_materials_kanban
-- (a blank_mro_provedoria_bip e um blank de apoio da provedoria)
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'blank_mro_provedoria_bip', 'blank', 'Provedoria - Bip de Saida - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'blank_mro_provedoria_bip');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'blank_mro_provedoria_bip', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'blank_mro_materials_kanban'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
