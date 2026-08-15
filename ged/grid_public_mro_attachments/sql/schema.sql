SELECT
    attachment_id AS attachment_id,
    task_id AS task_id,
    project_id AS project_id,
    aircraft_id AS aircraft_id,
    file_name AS file_name,
    file_size_kb AS file_size_kb,
    uploaded_by AS uploaded_by,
    uploaded_at AS uploaded_at,
    sync_sharepoint AS sync_sharepoint,
    description AS description,
    case
        when task_id > 0 then 'TASK'
        when project_id > 0 then 'PROJECT'
        when aircraft_id > 0 then 'AIRCRAFT'
        else 'N/A'
    end as origin_type
FROM
    public.mro_attachments