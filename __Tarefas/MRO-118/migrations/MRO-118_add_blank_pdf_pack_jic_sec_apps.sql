-- MRO-118: Inserir blank_pdf_pack_jic na seguranca do sistema
-- Aplica as mesmas permissoes que grid_public_mro_tasks tem para os grupos solicitados

-- 1. Registrar a aplicacao no catalogo de apps
INSERT INTO sec_apps (app_name, app_type, description)
VALUES ('blank_pdf_pack_jic', 'blank', 'Pack JIC para impressao (JIC, JEC, JMC, Shift Turnover, Calibrated Tool)')
ON CONFLICT (app_name) DO NOTHING;

-- 2. Conceder acesso aos grupos (mesmas permissoes da grid_public_mro_tasks)
INSERT INTO sec_groups_apps (group_id, app_name, priv_access, priv_insert, priv_delete, priv_update, priv_export, priv_print)
VALUES
    (1,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- Administrador
    (2,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- Group Default
    (3,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- MECANICO
    (4,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- ENGENHARIA
    (5,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- COORDENADOR
    (6,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- SUPERVISOR
    (7,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- PROGRAMACAO
    (8,  'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y'),  -- PLANEJAMENTO
    (10, 'blank_pdf_pack_jic', 'Y', '', '', '', 'Y', 'Y')   -- COMERCIAL
ON CONFLICT (group_id, app_name) DO NOTHING;
