-- ===================================================================
-- MRO-119: Trigger function + triggers para bloqueio por predecessora
--
-- Demanda Rodrigo Souza 28/07/2026: bloqueio automatico de sucessoras
-- quando a predecessora ainda nao foi concluida.
--
-- Fluxo:
-- 1. INSERT em mro_task_dependencies → marca sucessora como bloqueada
-- 2. UPDATE status_code em mro_tasks → recalcula bloqueio de todas as sucessoras
--
-- NOTA: Trigger cobre INSERT, UPDATE e DELETE porque:
-- - Importacao atual (ctrl_import_excel) usa UPSERT (UPDATE ou INSERT)
-- - App ScriptCase faz CRUD completo em mro_task_dependencies
-- ===================================================================

BEGIN;

-- 1. Trigger Function
CREATE OR REPLACE FUNCTION public.fn_update_blocked_predecessor()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE
    v_task_id integer;
BEGIN
    -- Cenario A: INSERT/UPDATE/DELETE na mro_task_dependencies → marca a sucessora
    IF TG_TABLE_NAME = 'mro_task_dependencies' THEN
        IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
            v_task_id := NEW.successor_task_id;
        ELSIF TG_OP = 'DELETE' THEN
            v_task_id := OLD.successor_task_id;
        END IF;

            -- Recalcula o flag da sucessora
            UPDATE mro_tasks t
            SET is_blocked_predecessor = EXISTS (
                SELECT 1
                FROM mro_task_dependencies d
                JOIN mro_tasks pred ON pred.task_id = d.predecessor_task_id
                WHERE d.successor_task_id = t.task_id
                  AND pred.status_code NOT IN ('COMPLETED', 'CANCELLED')
            )
            WHERE t.task_id = v_task_id;

            RETURN NEW;
    END IF;

    -- Cenario B: UPDATE status_code em mro_tasks → recalcula todas as sucessoras
    IF TG_TABLE_NAME = 'mro_tasks' THEN
        IF TG_OP = 'UPDATE' AND (OLD.status_code IS DISTINCT FROM NEW.status_code) THEN
            -- Atualiza flag de TODAS as sucessoras desta task
            UPDATE mro_tasks t
            SET is_blocked_predecessor = EXISTS (
                SELECT 1
                FROM mro_task_dependencies d
                JOIN mro_tasks pred ON pred.task_id = d.predecessor_task_id
                WHERE d.successor_task_id = t.task_id
                  AND pred.status_code NOT IN ('COMPLETED', 'CANCELLED')
            )
            WHERE t.task_id IN (
                SELECT successor_task_id
                FROM mro_task_dependencies
                WHERE predecessor_task_id = NEW.task_id
            );

            -- Atualiza flag da propria task (se for sucessora de alguem)
            UPDATE mro_tasks t
            SET is_blocked_predecessor = EXISTS (
                SELECT 1
                FROM mro_task_dependencies d
                JOIN mro_tasks pred ON pred.task_id = d.predecessor_task_id
                WHERE d.successor_task_id = t.task_id
                  AND pred.status_code NOT IN ('COMPLETED', 'CANCELLED')
            )
            WHERE t.task_id = NEW.task_id;

            RETURN NEW;
        END IF;
    END IF;

    RETURN NEW;
END;
$$;

-- 2. Trigger na mro_task_dependencies (apos INSERT, UPDATE ou DELETE)
CREATE TRIGGER trg_task_dependencies_blocked
AFTER INSERT OR UPDATE OR DELETE ON public.mro_task_dependencies
FOR EACH ROW
EXECUTE FUNCTION public.fn_update_blocked_predecessor();

-- 3. Trigger na mro_tasks (apos UPDATE de status_code)
CREATE TRIGGER trg_tasks_status_blocked
AFTER UPDATE OF status_code ON public.mro_tasks
FOR EACH ROW
EXECUTE FUNCTION public.fn_update_blocked_predecessor();

COMMIT;
