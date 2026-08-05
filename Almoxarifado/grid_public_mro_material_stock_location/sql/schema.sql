SELECT
    material_id as material_id,
    part_number as part_number,
    description as description,
    product_code as product_code,
    stock_location as stock_location,
    stock_balance as stock_balance,
    unit_measure as unit_measure,
    is_consumable as is_consumable,
    is_blocking_task as is_blocking_task
FROM
    public.mro_materials
WHERE
    part_number = '[glo_part_number]'
ORDER BY
    stock_location
