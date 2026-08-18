-- =============================================
-- MRO-126: Separacao de materiais (Bip de Saida)
-- Provedoria - Fluxo de Recolhimento de Material
--
-- Fluxo do POO:
--   Bipa a JIC -> Bipa todos os materiais -> botao finalizar separacao
--   Sem cracha, sem lote individual. O almoxarife separa as pecas e faz
--   um pacote (saco com a JIC impressa) que fica numa prateleira no
--   hangar. O mecanico pega o saco (confianca, sem registro).
--   Grava apenas que o material foi separado: data/hora + usuario.
--
-- Escopo: somente adiciona campos em mro_task_materials
-- (nao cria tabela nova)
-- =============================================

ALTER TABLE public.mro_task_materials
    ADD COLUMN IF NOT EXISTS separated_at timestamp without time zone;

ALTER TABLE public.mro_task_materials
    ADD COLUMN IF NOT EXISTS separated_by varchar(50);

COMMENT ON COLUMN public.mro_task_materials.separated_at IS 'Data/hora em que o material foi separado pela provedoria (bip de saida - pacote/JIC no hangar)';
COMMENT ON COLUMN public.mro_task_materials.separated_by IS 'Usuario da provedoria que realizou a separacao do material (pacote)';

CREATE INDEX IF NOT EXISTS idx_mro_task_materials_separated
    ON public.mro_task_materials (task_id)
    WHERE separated_at IS NULL;
