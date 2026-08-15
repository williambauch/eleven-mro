SELECT
    log_id,
    task_id,
    action_taken,
    user_login,
    action_date,
    remarks,
    batch_id
FROM
    "public".mro_task_history
where  task_id = [glo_task_id]
order by action_date DESC