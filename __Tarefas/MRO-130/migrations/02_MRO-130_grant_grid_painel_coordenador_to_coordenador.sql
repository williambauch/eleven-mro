-- =============================================
-- MRO-130: Conceder acesso a grid_painel_coordenador
-- Item 2B: nova app de Painel do Coordenador.
-- Permissao SOMENTE para o grupo COORDENADOR (id 5).
-- Padrao segue SKILL-DB-MANUTENCAO / MRO-119_06_add_dependencies_apps_sec.sql
-- =============================================

INSERT INTO "public"."sec_groups_apps" (group_id, app_name, priv_access, priv_insert, priv_delete, priv_update, priv_export, priv_print)
SELECT 5, 'grid_painel_coordenador', 'Y', 'N', 'N', 'N', 'N', 'N'
WHERE NOT EXISTS (
    SELECT 1 FROM "public"."sec_groups_apps"
    WHERE group_id = 5 AND app_name = 'grid_painel_coordenador'
);

-- Remove acesso de outros grupos (defesa em profundidade: garante isolamento)
DELETE FROM "public"."sec_groups_apps"
WHERE app_name = 'grid_painel_coordenador'
  AND group_id <> 5;
