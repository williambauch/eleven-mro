-- =============================================
-- Garante as mesmas permissoes de form_public_mro_tasks
-- para form_public_mro_task_resources
-- Motivo: form_public_mro_task_resources estava sem nenhum
-- grupo vinculado, herdando os grupos de acesso
-- =============================================

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'form_public_mro_task_resources', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_tasks'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
