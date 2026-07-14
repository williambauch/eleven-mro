SELECT * FROM "public".mro_task_assignments WHERE planned_skill_id = [usr_skill_id] 
    AND (supervisor_id IS NULL OR supervisor_id = [usr_employee_id]) 
    AND (status_code IN ('BLOCKED') OR task_id IN (SELECT task_id FROM mro_tasks WHERE is_blocked_material = true))