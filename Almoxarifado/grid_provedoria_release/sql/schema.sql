SELECT
    t.task_id AS task_id,
    t.task_code AS task_code,
    t.task_name AS task_name,
    t.task_type AS task_type,
    t.status_code AS status_code,
    t.target_start AS target_start,
    t.target_end AS target_end,
    t.project_id AS project_id,
    t.skill_code AS skill_code,
    t.estimated_hours AS estimated_hours,
    t.is_blocked_material AS is_blocked_material,
    t.is_blocked_predecessor AS is_blocked_predecessor,
    t.is_predecessor_manual AS is_predecessor_manual,
    (
        SELECT COUNT(*)
        FROM mro_task_materials tm
        JOIN mro_materials m ON m.material_id = tm.material_id
        WHERE tm.task_id = t.task_id
          AND tm.is_applied IS NOT TRUE
          AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
          AND COALESCE(tm.committed_qty, 0) >= tm.planned_qty
    ) AS total_separar,
    (
        SELECT COUNT(*)
        FROM mro_task_materials tm
        JOIN mro_materials m ON m.material_id = tm.material_id
        WHERE tm.task_id = t.task_id
          AND tm.is_applied IS NOT TRUE
          AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
          AND COALESCE(tm.committed_qty, 0) >= tm.planned_qty
          AND tm.separated_at IS NOT NULL
    ) AS separados,
    ROUND(
        100.0 * (
            SELECT COUNT(*)
            FROM mro_task_materials tm
            JOIN mro_materials m ON m.material_id = tm.material_id
            WHERE tm.task_id = t.task_id
              AND tm.is_applied IS NOT TRUE
              AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
              AND COALESCE(tm.committed_qty, 0) >= tm.planned_qty
              AND tm.separated_at IS NOT NULL
        ) / NULLIF(
            (
                SELECT COUNT(*)
                FROM mro_task_materials tm
                JOIN mro_materials m ON m.material_id = tm.material_id
                WHERE tm.task_id = t.task_id
                  AND tm.is_applied IS NOT TRUE
                  AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
                  AND COALESCE(tm.committed_qty, 0) >= tm.planned_qty
            ), 0
), 1
    ) AS pct_separado
FROM public.mro_tasks t
WHERE t.status_code = 'PENDING_PROVIDER'
  AND t.is_blocked_material IS NOT TRUE
ORDER BY pct_separado DESC, t.task_code