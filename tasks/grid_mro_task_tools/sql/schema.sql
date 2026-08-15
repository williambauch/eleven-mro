SELECT
    tt.task_tool_id as task_tool_id,
    tt.task_id as task_id ,
    tt.tool_id as tool_id,
    tt.quantity_required as quantity_required,
    t.description as description,
    t.part_number  as part_number
FROM
    "public".mro_task_tools tt
inner join mro_tools t on t.tool_id = tt.tool_id  
WHERE tt.task_id=[task_id]