-- =============================================
-- MRO-122: Permissoes para blank_abertura_nrc
-- Abertura direta de NRC (substitui o fluxo da ctrl_abertura_nrc)
-- Copia as mesmas permissoes da ctrl_abertura_nrc
-- =============================================

INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'blank_abertura_nrc', 'blank', 'Abertura direta de NRC - MRO-122'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'blank_abertura_nrc');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'blank_abertura_nrc', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'ctrl_abertura_nrc'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
