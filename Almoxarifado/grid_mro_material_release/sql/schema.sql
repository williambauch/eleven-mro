SELECT
    t.task_id,
    t.project_id,
    t.is_blocked_predecessor,
    COALESCE(p.p6_proj_id, 'N/A') AS projeto,
    t.task_code AS jic,
    COALESCE(t.task_name, 'Sem descricao tecnica') AS descricao,
    t.status_code,
    t.target_start,
    t.target_end,
    COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE) AS total_materiais,
    COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE AND m.stock_balance >= tm.planned_qty) AS materiais_ok,
    ROUND(
        100.0 * COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE AND m.stock_balance >= tm.planned_qty)
        / NULLIF(COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE), 0),
        0
    ) AS pct_disponivel
FROM mro_tasks t
JOIN mro_projects p ON t.project_id = p.project_id
JOIN mro_task_materials tm ON tm.task_id = t.task_id
JOIN mro_materials m ON tm.material_id = m.material_id
WHERE t.status_code IN ('PLANNED', 'NOT_STARTED')
  AND tm.is_applied IS NOT TRUE
  AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
GROUP BY
    t.task_id,
    t.project_id,
    t.is_blocked_predecessor,
    p.p6_proj_id,
    t.task_code,
    t.task_name,
    t.status_code,
    t.target_start,
    t.target_end
HAVING COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE AND m.stock_balance >= tm.planned_qty)
     = COUNT(tm.task_material_id) FILTER (WHERE tm.is_applied IS NOT TRUE)
ORDER BY projeto, jic
