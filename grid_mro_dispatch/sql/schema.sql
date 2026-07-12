SELECT 
    a.assignment_id,
    t.task_code,
    t.task_name,
    a.planned_qty_hours,
    a.executed_by_employee_id,
    a.status_code,
    e.full_name as mechanic_name
FROM 
    mro_task_assignments a
INNER JOIN mro_tasks t ON a.task_id = t.task_id
LEFT JOIN mro_employees e ON a.executed_by_employee_id = e.employee_id
WHERE 
    a.planned_skill_id = [usr_skill_id] -- Filtra pela especialidade do supervisor logado
    AND (a.supervisor_id IS NULL OR a.supervisor_id = [usr_employee_id]) -- Exclusividade
    AND a.status_code IN ('PLANNED', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED')