-- =============================================
-- MRO-121: Cria tabela mro_attachments (modelo relacional)
-- Espelha o padrao da mro_task_attachments (task_id + project_id),
-- acrescentando aircraft_id para anexos de aeronave.
-- =============================================

CREATE TABLE IF NOT EXISTS "public"."mro_attachments" (
    "attachment_id"   SERIAL NOT NULL,
    "task_id"         INTEGER,
    "project_id"      INTEGER,
    "aircraft_id"     INTEGER,
    "file_name"       VARCHAR(255) NOT NULL,
    "file_size_kb"    INTEGER,
    "uploaded_by"     VARCHAR(50),
    "uploaded_at"     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "sync_sharepoint" BOOLEAN DEFAULT false,
    "description"     TEXT,
    CONSTRAINT "mro_attachments_pkey" PRIMARY KEY ("attachment_id")
);

CREATE INDEX IF NOT EXISTS "idx_mro_attachments_task"
    ON "public"."mro_attachments" USING btree ("task_id");

CREATE INDEX IF NOT EXISTS "idx_mro_attachments_project"
    ON "public"."mro_attachments" USING btree ("project_id");

CREATE INDEX IF NOT EXISTS "idx_mro_attachments_aircraft"
    ON "public"."mro_attachments" USING btree ("aircraft_id");

