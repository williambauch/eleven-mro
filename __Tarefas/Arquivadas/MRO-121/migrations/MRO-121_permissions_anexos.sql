-- =============================================
-- MRO-121: Permissoes para grid_public_mro_attachments e form_public_mro_attachments
-- Copia as permissoes de grid_public_mro_tasks (grid) e form_public_mro_tasks (form)
-- =============================================

-- Grid de Anexos
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_attachments', 'cons', 'Consulta de Anexos - MRO-121'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_attachments');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_attachments', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'grid_public_mro_tasks'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- Form de Anexos
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'form_public_mro_attachments', 'form', 'Cadastro de Anexos - MRO-121'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'form_public_mro_attachments');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'form_public_mro_attachments', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_tasks'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
