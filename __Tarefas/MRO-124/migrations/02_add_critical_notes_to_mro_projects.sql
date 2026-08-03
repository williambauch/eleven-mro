-- MRO-124 - Adiciona campo livre Critical para anotação interna do projeto
ALTER TABLE public.mro_projects
    ADD COLUMN IF NOT EXISTS critical_notes TEXT NULL;
