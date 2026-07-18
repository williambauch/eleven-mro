-- =============================================
-- MRO-117: Concede acesso do grupo MECANICO a grid_public_mro_tasks
-- Descricao: Necessario para desativar grid_public_mro_nrc sem perder acesso
-- =============================================
UPDATE sec_groups_apps
SET priv_access = 'Y',
    priv_insert = '',
    priv_update = '',
    priv_delete = '',
    priv_export = 'Y',
    priv_print = 'Y'
WHERE app_name = 'grid_public_mro_tasks'
  AND group_id = 3;
