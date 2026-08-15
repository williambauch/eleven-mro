-- ============================================================
-- MRO-126 - MULTIPLAS SKILLS POR COLABORADOR
-- ============================================================
-- Altera mro_employees.skill_id de integer para varchar,
-- permitindo o campo tipo Select Multiplo do ScriptCase gravar
-- uma lista de ids separada por virgula (ex: "1,5,14").
--
-- Nenhum JOIN usa mro_employees.skill_id (os JOINs usam
-- mro_task_assignments.planned_skill_id / skill_id), entao a
-- mudanca de tipo nao impacta consultas existentes.
-- ============================================================

-- 0. Remove a FK antiga (integer -> mro_skills), incompativel com varchar
ALTER TABLE public.mro_employees
    DROP CONSTRAINT IF EXISTS mro_employees_skill_id_fkey;

-- 1. Converte os valores existentes para texto (1 skill por colaborador vira "14")
ALTER TABLE public.mro_employees
    ALTER COLUMN skill_id TYPE varchar(100)
    USING (CASE WHEN skill_id IS NULL THEN NULL ELSE skill_id::text END);

-- 2. Comentario documentando o novo formato
COMMENT ON COLUMN public.mro_employees.skill_id
    IS 'Lista de skills do colaborador (ids separados por virgula) - campo Select Multiplo do ScriptCase. Ex: "1,5,14"';

-- NOTA: a FK mro_employees_skill_id_fkey NAO e recriada de proposito,
-- pois o campo agora guarda uma lista (nao pode ser FK de mro_skills).

-- 3. Conferencia
-- SELECT employee_id, login, full_name, skill_id FROM mro_employees ORDER BY employee_id;
