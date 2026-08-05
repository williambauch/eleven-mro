SELECT
    task_material_id,
    task_id,
    material_id,
    planned_qty,
    is_applied,
    applied_qty,
    batch_sn,
    committed_qty,
    committed_unit_cost,
    committed_total_cost,
    material_source,
    unit_cost,
    total_cost
FROM
    "public".mro_task_materials
WHERE task_id = [task_id]