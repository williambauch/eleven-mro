-- =============================================
-- MRO-130: Adicionar coluna coordinator_id em mro_projects
-- Item 2A: permitir cadastrar o coordenador responsavel
-- pelo projeto no formulario de cadastro.
-- =============================================

ALTER TABLE "public"."mro_projects"
    ADD COLUMN IF NOT EXISTS "coordinator_id" integer NULL;

ALTER TABLE "public"."mro_projects"
    ADD CONSTRAINT "fk_mro_projects_coordinator"
    FOREIGN KEY ("coordinator_id")
    REFERENCES "public"."mro_employees"("employee_id")
    ON DELETE SET NULL;

COMMENT ON COLUMN "public"."mro_projects"."coordinator_id"
    IS 'FK para mro_employees — coordenador responsavel pelo projeto (grupo COORDENADOR). Painel do coordenador (grid_painel_coordenador) lista somente projetos onde este campo = [usr_employee_id].';
