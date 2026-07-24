-- =============================================
-- Garante as mesmas permissoes de
-- form_public_mro_task_assignments_planned
-- para control_split_assignment
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'control_split_assignment', 'control', 'Split de atribuicao - MRO-119'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'control_split_assignment');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'control_split_assignment', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_task_assignments_planned'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
