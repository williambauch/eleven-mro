SELECT
    movement_id,
    tool_id,
    task_id,
    resource_id,
    checkout_time,
    checkin_time,
    return_condition
FROM
    "public".mro_tool_movements
WHERE task_id=[task_id]