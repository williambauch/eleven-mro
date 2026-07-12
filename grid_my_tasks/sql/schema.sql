SELECT *
FROM mro_task_assignments 
WHERE executed_by_employee_id = [usr_employee_id] 
AND status_code IN ('ASSIGNED', 'IN_PROGRESS', 'PAUSED','NOT_STARTED')