-- MRO-115: Altera a chave unica de mro_materials para incluir stock_location
-- Antes: UNIQUE (product_code, part_number)
-- Depois: UNIQUE (product_code, part_number, stock_location)
-- Isso permite que o mesmo material exista em armazens diferentes com custos/saldos distintos.

-- 1. Remove a constraint antiga
ALTER TABLE public.mro_materials DROP CONSTRAINT IF EXISTS mro_materials_prod_pn_key;

-- 2. Cria a nova constraint incluindo stock_location
ALTER TABLE public.mro_materials 
    ADD CONSTRAINT mro_materials_prod_pn_loc_key UNIQUE (product_code, part_number, stock_location);
