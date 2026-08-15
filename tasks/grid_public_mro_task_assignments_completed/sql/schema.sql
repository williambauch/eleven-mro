SELECT
 a.assignment_id AS assignment_id,
 a.task_id AS task_id,
 CONCAT(t.task_code, ' - ', t.task_name) AS task_desc,
 a.project_id AS project_id,
 p.p6_proj_id AS project_code,
 a.executed_by_employee_id AS employee_id,
 e.full_name AS employee_name,
 a.executed_by_employee_id AS executed_by_employee_id,
 a.planned_skill_id AS planned_skill_id,
 a.skill_id AS skill_id,
 sk.skill_code AS skill_code,
 a.supervisor_id AS supervisor_id,
 a.resource_id AS resource_id,
 a.role_id AS role_id,
 a.planned_qty_hours AS planned_qty_hours,
 a.actual_qty_hours AS actual_qty_hours,
 a.status_code AS assignments_status_code,
 t.status_code AS task_status_code, 
 a.created_at AS created_at,
 a.created_by AS created_by,
 a.updated_at AS updated_at,
 a.updated_by AS updated_by
FROM "public".mro_task_assignments a
LEFT JOIN "public".mro_projects p   ON p.project_id  = a.project_id
LEFT JOIN "public".mro_tasks t      ON t.task_id     = a.task_id
LEFT JOIN "public".mro_employees e  ON e.employee_id = a.executed_by_employee_id
LEFT JOIN "public".mro_skills sk    ON sk.skill_id    = a.skill_id
WHERE a.planned_skill_id IN ([usr_skill_id])
    AND (a.supervisor_id IS NULL OR a.supervisor_id = [usr_employee_id])
    AND a.status_code IN ('PENDING_HANDOVER','SUPSIG')
ORDER BY a.task_id DESC