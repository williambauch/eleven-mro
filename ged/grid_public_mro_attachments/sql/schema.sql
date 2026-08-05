select
    attachment_id as attachment_id,
    task_id as task_id,
    project_id as project_id,
    aircraft_id as aircraft_id,
    file_name as file_name,
    file_size_kb as file_size_kb,
    uploaded_by as uploaded_by,
    uploaded_at as uploaded_at,
    sync_sharepoint as sync_sharepoint,
    description as description,
    case
        when task_id > 0 then 'TASK'
        when project_id > 0 then 'PROJECT'
        when aircraft_id > 0 then 'AIRCRAFT'
        else 'N/A'
    end as origin_type
from
    public.mro_attachments
