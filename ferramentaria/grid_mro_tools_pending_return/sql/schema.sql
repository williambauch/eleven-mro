SELECT
    tt.transaction_id as transaction_id,
    tt.tool_id as tool_id,
    t.part_number as part_number,
    t.description as description,
    t.serial_number as serial_number,
    t.status as tool_status,
    e.full_name as funcionario,
    e.employee_registration as matricula,
    tt.task_id as task_id,
    tt.checkout_time as checkout_time,
    tt.checkout_user as checkout_user,
    (CURRENT_DATE - tt.checkout_time::date) as dias_em_posse
FROM
    mro_tool_transactions tt
LEFT JOIN mro_tools t      ON t.tool_id = tt.tool_id
LEFT JOIN mro_employees e  ON e.employee_id = tt.employee_id
WHERE
    tt.status = 'ACTIVE'
ORDER BY
    tt.checkout_time ASC