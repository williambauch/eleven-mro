-- MRO-120: Adiciona index em mro_timesheet.assignment_id para acelerar consultas de HH
-- As queries de apontamento (tempo, HH alert) filtram por assignment_id,
-- mas a tabela nao tinha index nesta coluna, causando full scan.

CREATE INDEX IF NOT EXISTS idx_mro_timesheet_assignment_id
    ON public.mro_timesheet USING btree (assignment_id);
