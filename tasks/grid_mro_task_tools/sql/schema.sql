SELECT
    task_tool_id,
    task_id,
    tool_id,
    quantity_required
FROM
    "public".mro_task_tools
WHERE task_id=[task_id]