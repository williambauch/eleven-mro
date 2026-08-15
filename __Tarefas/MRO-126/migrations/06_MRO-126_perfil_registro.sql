-- =============================================
-- MRO-126: Perfil REGISTRO + Painel de Registros (Auditoria)
-- Decisao do Gerente de Projeto (13/08/2026):
--   - Time de Registros e um painel SEPARADO do menu do supervisor
--   - Novo perfil (grupo) chamado "Registro" com acesso exclusivo
--   - Tela exibira tasks COMPLETED para conferencia final
-- =============================================

-- =============================================
-- 1. Grupo REGISTRO (group_id = 13)
-- =============================================
INSERT INTO "public".sec_groups (group_id, description)
SELECT 13, 'REGISTRO'
WHERE NOT EXISTS (SELECT 1 FROM "public".sec_groups WHERE group_id = 13);

-- =============================================
-- 2. Usuario de teste "registro" (padrao dos demais)
--    Login: registro | Senha: Registro@321 | Hash MD5
-- =============================================
INSERT INTO "public".sec_users (login, pswd, name, email, active, activation_code, priv_admin, mfa, picture, role, phone, pswd_last_updated, mfa_last_updated)
SELECT 'registro', 'f5203f16f22bfbc69794b2605091fdf2', 'Registro Teste', 'registro@teste.com', 'Y', '', '', '', '\x', '', '', NOW(), NULL
WHERE NOT EXISTS (SELECT 1 FROM "public".sec_users WHERE login = 'registro');

-- =============================================
-- 3. Vinculo usuario x grupo
-- =============================================
INSERT INTO "public".sec_users_groups (login, group_id)
SELECT 'registro', 13
WHERE NOT EXISTS (SELECT 1 FROM "public".sec_users_groups WHERE login = 'registro' AND group_id = 13);

-- =============================================
-- 4. App do Painel de Registros (tasks COMPLETED)
-- =============================================
INSERT INTO "public".sec_apps (app_name, app_type, description)
SELECT 'grid_public_mro_task_registro', 'cons', 'Painel de Registros (Auditoria de Tasks Concluidas)'
WHERE NOT EXISTS (SELECT 1 FROM "public".sec_apps WHERE app_name = 'grid_public_mro_task_registro');

-- =============================================
-- 5. Permissao: grupo REGISTRO (13) + Administrador (1)
--    Grid somente leitura
-- =============================================
INSERT INTO "public".sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
VALUES
('grid_public_mro_task_registro', 1, 'Y', '', '', '', 'Y', 'Y'),
('grid_public_mro_task_registro', 13, 'Y', '', '', '', 'Y', 'Y')
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;

-- =============================================
-- Validacao (rodar apos a migration)
- =============================================
 SELECT group_id, description FROM "public".sec_groups WHERE group_id = 13;
 SELECT login, name, active FROM "public".sec_users WHERE login = 'registro';
 SELECT sga.app_name, sga.group_id, g.description, sga.priv_access
 FROM "public".sec_groups_apps sga
 LEFT JOIN "public".sec_groups g ON g.group_id = sga.group_id
 WHERE sga.app_name = 'grid_public_mro_task_registro';
