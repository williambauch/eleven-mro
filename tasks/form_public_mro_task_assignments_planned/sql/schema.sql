SELECT * FROM "public".mro_task_assignments WHERE

planned_skill_id IN ([usr_skill_id]) 
    AND (supervisor_id IS NULL OR supervisor_id = [usr_employee_id]) 
AND status_code IN ('NOT_STARTED', 'PLANNED', 'RELEASED')  


 ORDER BY task_id DESC