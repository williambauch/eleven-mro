-- MRO-119: Registrar form_public_mro_task_dependencies_predecessoras e
-- form_public_mro_task_dependencies_sucessoras na seguranca do sistema
-- Permissoes identicas ao form_public_mro_tasks (app mestre)

-- 1. Registrar as aplicacoes no catalogo de apps
INSERT INTO sec_apps (app_name, app_type, description)
VALUES
    ('form_public_mro_task_dependencies_predecessoras', 'form', 'Gerenciamento de tarefas predecessoras (bloqueiam a task atual)'),
    ('form_public_mro_task_dependencies_sucessoras', 'form', 'Gerenciamento de tarefas sucessoras (bloqueadas pela task atual)')
ON CONFLICT (app_name) DO NOTHING;

-- 2. Conceder acesso aos grupos (mesmas permissoes do form_public_mro_tasks)
INSERT INTO sec_groups_apps (group_id, app_name, priv_access, priv_insert, priv_delete, priv_update, priv_export, priv_print)
VALUES
    -- Administrador
    (1,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (1,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- Group Default
    (2,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (2,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- MECANICO (apenas acesso/leitura)
    (3,  'form_public_mro_task_dependencies_predecessoras', 'Y', '', '', '', '', ''),
    (3,  'form_public_mro_task_dependencies_sucessoras', 'Y', '', '', '', '', ''),
    -- ENGENHARIA
    (4,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (4,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- COORDENADOR
    (5,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (5,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- SUPERVISOR (sem acesso)
    (6,  'form_public_mro_task_dependencies_predecessoras', '', '', '', '', '', ''),
    (6,  'form_public_mro_task_dependencies_sucessoras', '', '', '', '', '', ''),
    -- PROGRAMACAO
    (7,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (7,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- PLANEJAMENTO
    (8,  'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (8,  'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- CLIENTE (sem acesso)
    (9,  'form_public_mro_task_dependencies_predecessoras', '', '', '', '', '', ''),
    (9,  'form_public_mro_task_dependencies_sucessoras', '', '', '', '', '', ''),
    -- COMERCIAL
    (10, 'form_public_mro_task_dependencies_predecessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    (10, 'form_public_mro_task_dependencies_sucessoras', 'Y', 'Y', 'Y', 'Y', '', ''),
    -- COMPRAS (sem acesso)
    (11, 'form_public_mro_task_dependencies_predecessoras', '', '', '', '', '', ''),
    (11, 'form_public_mro_task_dependencies_sucessoras', '', '', '', '', '', ''),
    -- FERRAMENTARIA (sem acesso)
    (12, 'form_public_mro_task_dependencies_predecessoras', '', '', '', '', '', ''),
    (12, 'form_public_mro_task_dependencies_sucessoras', '', '', '', '', '', '')
ON CONFLICT (group_id, app_name) DO NOTHING;
