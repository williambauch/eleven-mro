-- =============================================
-- MRO-121: Permissoes para grid_public_mro_material_stock_location
-- Copia as permissoes de grid_public_mro_materials (grid de consulta)
-- =============================================

-- Grid de Saldo por Armazem
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_material_stock_location', 'cons', 'Saldo por Armazem - MRO-121'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_material_stock_location');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_material_stock_location', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'grid_public_mro_materials'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
