-- =============================================
-- Adicionar coluna is_blocking_task em mro_materials
-- Descricao: Flag que indica se a ausencia deste
-- material BLOQUEIA a execucao da tarefa
-- Projeto: MRO-115 - Flag de bloqueio operacional
-- 
-- =============================================

ALTER TABLE "public"."mro_materials"
    ADD COLUMN "is_blocking_task" boolean DEFAULT true;

COMMENT ON COLUMN "public"."mro_materials"."is_blocking_task" IS 'Se true (default), a ausencia deste material BLOQUEIA a tarefa. Se false, a falta dele NAO impede a execucao.';

-- Indice para consultas rapidas por material blocking
CREATE INDEX IF NOT EXISTS "idx_mro_materials_is_blocking_task"
    ON "public"."mro_materials" USING btree ("is_blocking_task");
