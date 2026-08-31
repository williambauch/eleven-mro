SELECT
    t.task_id             AS task_id,
    t.task_code           AS task_code,
    t.task_name           AS task_name,
    t.status_code         AS status_code,
    t.is_nrc              AS is_nrc,
    t.is_blocked_material AS is_blocked_material,
    t.is_blocked_tool     AS is_blocked_tool,
    t.is_blocked_labor    AS is_blocked_labor,
    p.project_id          AS project_id,
    p.p6_proj_id          AS p6_proj_id,
    p.project_name        AS project_name,
    a.registration        AS registration,
    t.actual_start        AS actual_start,
    t.actual_end          AS actual_end
FROM mro_tasks t
JOIN mro_projects p ON p.project_id = t.project_id
LEFT JOIN mro_aircraft a ON a.aircraft_id = p.aircraft_id
WHERE p.coordinator_id = [usr_employee_id]
  AND t.status_code IN ('PENDING_COORD', 'IN_PROGRESS', 'RELEASED', 'PENDING_PROG')
ORDER BY p.p6_proj_id, t.target_start, t.task_id
