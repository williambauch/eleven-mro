SELECT
    t.timesheet_id AS timesheet_id,
    t.assignment_id AS assignment_id,
    t.employee_id AS employee_id,
    e.full_name AS employee_name,
    e.employee_registration AS employee_registration,
    t.appointment_date AS appointment_date,
    t.start_time AS start_time,
    t.end_time AS end_time,
    t.duration_minutes AS duration_minutes,
    t.status AS status,
    t.pause_reason AS pause_reason,
    t.handover_notes AS handover_notes,
    a.skill_id AS skill_id,
    s.description AS skill_name,
    a.planned_qty_hours AS planned_qty_hours,
    a.actual_qty_hours AS actual_qty_hours,
    tk.task_id AS task_id,
    tk.task_code AS task_code,
    tk.task_name AS task_name,
    tk.estimated_hours AS estimated_hours,
    tk.is_nrc AS is_nrc,
    p.project_id AS project_id,
    p.p6_proj_id AS p6_proj_id,
    p.project_name AS project_name
FROM public.mro_timesheet t
LEFT JOIN public.mro_employees e ON e.employee_id = t.employee_id
LEFT JOIN public.mro_task_assignments a ON a.assignment_id = t.assignment_id
LEFT JOIN public.mro_tasks tk ON tk.task_id = a.task_id
LEFT JOIN public.mro_projects p ON p.project_id = tk.project_id
LEFT JOIN public.mro_skills s ON s.skill_id = a.skill_id
ORDER BY t.start_time DESC