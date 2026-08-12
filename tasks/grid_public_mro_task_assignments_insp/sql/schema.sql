SELECT
    a.assignment_id         AS assignment_id,
    a.task_id               AS task_id,
    t.task_code             AS task_code,
    CONCAT(t.task_code, ' - ', t.task_name) AS task_desc,
    t.jic_number            AS jic_number,
    t.requires_rii          AS requires_rii,
    t.is_rii                AS is_rii,
    a.project_id            AS project_id,
    p.p6_proj_id            AS project_code,
    e.full_name             AS employee_name,
    a.executed_by_employee_id AS executed_by_employee_id,
    a.planned_skill_id      AS planned_skill_id,
    a.skill_id              AS skill_id,
    a.supervisor_id         AS supervisor_id,
    a.planned_qty_hours     AS planned_qty_hours,
    a.actual_qty_hours      AS actual_qty_hours,
    a.status_code           AS status_code,
    a.updated_at            AS updated_at,
    a.updated_by            AS updated_by
FROM "public".mro_task_assignments a
LEFT JOIN "public".mro_projects p   ON p.project_id  = a.project_id
LEFT JOIN "public".mro_tasks t      ON t.task_id     = a.task_id
LEFT JOIN "public".mro_employees e  ON e.employee_id = a.executed_by_employee_id
WHERE a.planned_skill_id = [usr_skill_id]
    AND a.status_code IN ('PENDING_INSP1','PENDING_INSP2')
ORDER BY a.task_id DESC
