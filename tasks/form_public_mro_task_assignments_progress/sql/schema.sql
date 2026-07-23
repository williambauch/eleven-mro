SELECT * FROM "public".mro_task_assignments WHERE planned_skill_id = [usr_skill_id] 
    AND (supervisor_id IS NULL OR supervisor_id = [usr_employee_id]) 
    AND status_code IN ('ASSIGNED', 'IN_PROGRESS')