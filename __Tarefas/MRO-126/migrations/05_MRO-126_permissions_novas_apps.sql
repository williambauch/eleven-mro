-- =============================================
-- MRO-126: Permissoes das novas apps do Supervisor
-- Substituicoes:
--   tabs_supervisor => menu_supervisor
--   form_public_mro_task_assignments_progress  => grid_public_mro_task_assignments_progress
--   form_public_mro_task_assignments_completed => grid_public_mro_task_assignments_completed
--   form_public_mro_task_assignments_blocked   => grid_public_mro_task_assignments_blocked
-- Novas:
--   grid_public_mro_tasks_approval (aprovacao RII - apenas supervisor/inspetor)
--   grid_public_mro_tasks_insp (inspecao RII - apenas inspetor)
-- =============================================

-- =============================================
-- 1. menu_supervisor (herda de tabs_supervisor)
--    Grupos: Administrador (1) e SUPERVISOR (6)
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'menu_supervisor', 'menu', 'Menu do Supervisor (responsivo) - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'menu_supervisor');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'menu_supervisor', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'tabs_supervisor'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 2. grid_public_mro_task_assignments_progress
--    (herda de form_public_mro_task_assignments_progress)
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_task_assignments_progress', 'cons', 'Atribuicoes em Andamento - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_task_assignments_progress');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_task_assignments_progress', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_task_assignments_progress'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 3. grid_public_mro_task_assignments_completed
--    (herda de form_public_mro_task_assignments_completed)
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_task_assignments_completed', 'cons', 'Atribuicoes Concluidas - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_task_assignments_completed');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_task_assignments_completed', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_task_assignments_completed'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 4. grid_public_mro_task_assignments_blocked
--    (herda de form_public_mro_task_assignments_blocked)
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_task_assignments_blocked', 'cons', 'Atribuicoes com Impedimentos - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_task_assignments_blocked');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_task_assignments_blocked', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_task_assignments_blocked'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 5. grid_public_mro_tasks_approval (NOVA)
--    Herda de tabs_supervisor (Administrador + SUPERVISOR)
--    O controle de inspetor e feito via [usr_is_inspector]
--    no menu e na grid, nao por grupo de acesso.
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_tasks_approval', 'cons', 'Aprovacao de Tarefas (RII) - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_tasks_approval');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_tasks_approval', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'tabs_supervisor'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 6. grid_public_mro_tasks_insp (NOVA)
--    Herda de tabs_supervisor (Administrador + SUPERVISOR)
--    Fila de Inspecao RII (PENDING_INSP1/PENDING_INSP2)
--    O controle de inspetor e feito via [usr_is_inspector]
--    no menu e na grid, nao por grupo de acesso.
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_tasks_insp', 'cons', 'Inspecao RII (PENDING_INSP1/PENDING_INSP2) - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_tasks_insp');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'grid_public_mro_tasks_insp', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'tabs_supervisor'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- 7. grid_public_mro_task_history
--    Consulta do historico de transicoes de uma task
--    (filtra por [glo_task_id] - aberta via botao/ligacao)
--    Permissoes conforme banco (grupos: 1, 4, 5, 6, 7, 8)
-- =============================================
INSERT INTO sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_task_history', 'cons', 'Historico de transicoes da task - MRO-126'
WHERE NOT EXISTS (SELECT 1 FROM sec_apps WHERE app_name = 'grid_public_mro_task_history');

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
VALUES
('grid_public_mro_task_history', 1, 'Y', '', '', '', '', ''),
('grid_public_mro_task_history', 4, 'Y', '', '', '', 'Y', 'Y'),
('grid_public_mro_task_history', 5, 'Y', '', '', '', 'Y', 'Y'),
('grid_public_mro_task_history', 6, 'Y', '', '', '', 'Y', 'Y'),
('grid_public_mro_task_history', 7, 'Y', '', '', '', 'Y', 'Y'),
('grid_public_mro_task_history', 8, 'Y', '', '', '', 'Y', 'Y')
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- Validacao (rodar apos a migration)
-- =============================================
-- SELECT sga.app_name, sga.group_id, g.description AS grupo, sga.priv_access
-- FROM sec_groups_apps sga
-- LEFT JOIN sec_groups g ON g.group_id = sga.group_id
-- WHERE sga.app_name IN (
--   'menu_supervisor',
--   'grid_public_mro_task_assignments_progress',
--   'grid_public_mro_task_assignments_completed',
--   'grid_public_mro_task_assignments_blocked',
--   'grid_public_mro_tasks_approval',
--   'grid_public_mro_tasks_insp',
--   'grid_public_mro_task_history'
-- )
-- AND sga.priv_access = 'Y'
-- ORDER BY sga.app_name, sga.group_id;
