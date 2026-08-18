SELECT
    tt.transaction_id AS transaction_id,
    tt.tool_id        AS tool_id,
    tt.employee_id    AS employee_id,
    tt.task_id        AS task_id,
    tk.project_id     AS project_id,
    t.part_number     AS part_number,
    t.description     AS description,
    concat(t.part_number, ' - ', t.description) AS tool_desc,
    tk.task_code      AS task_code,
    concat(tk.task_code, ' - ', tk.task_name) AS task_desc,
    mp.project_id     AS project_id,
    concat(mp.p6_proj_id, ' - ', mp.project_name) AS project_desc,
    tt.checkin_time   AS checkin_time,
    tt.condition_on_return AS condition_on_return,
    r.report_id       AS report_id,
    r.status          AS report_status,
    r.report_type     AS report_type
FROM
    mro_tool_transactions tt
JOIN mro_tools t      ON t.tool_id = tt.tool_id
LEFT JOIN mro_tasks tk ON tk.task_id = tt.task_id
LEFT JOIN mro_projects mp ON mp.project_id = tk.project_id
LEFT JOIN mro_tool_reports r ON r.transaction_id = tt.transaction_id
WHERE
    tt.employee_id = [usr_employee_id]
    AND tt.condition_on_return IN ('DANO', 'PERDA')
ORDER BY
    tt.checkin_time DESC
