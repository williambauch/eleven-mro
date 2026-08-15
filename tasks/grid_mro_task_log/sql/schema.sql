SELECT
    t.id,
    t.inserted_date,
    t.username_login,
    t.application,
    t.application_name,
    t.action_name,
    t.ip_user,
    t.description,
    t.task_id_log
FROM (
    SELECT
        sl.id AS id,
        sl.inserted_date AS inserted_date,
        sl.username AS username_login,
        sl.application AS application,
        'Task' AS application_name,
        sl.action AS action_name,
        sl.ip_user AS ip_user,
        sl.description AS description,
        substring(sl.description from 'task_id\s*:\s*([0-9]+)') AS task_id_log
    FROM sc_log sl
    WHERE sl.application = 'form_public_mro_tasks'

    UNION ALL

    SELECT
        sl.id AS id,
        sl.inserted_date AS inserted_date,
        sl.username AS username_login,
        sl.application AS application,
        'Skills' AS application_name,
        sl.action AS action_name,
        sl.ip_user AS ip_user,
        sl.description AS description,
        COALESCE(
            substring(sl.description from 'task_id \(new\)\s*:\s*([0-9]+)'),
            r.task_id::text
        ) AS task_id_log
    FROM sc_log sl
    LEFT JOIN mro_task_resources r
        ON r.allocation_id = substring(sl.description from 'allocation_id\s*:\s*([0-9]+)')::int
    WHERE sl.application = 'form_public_mro_task_resources'

    UNION ALL

    SELECT
        sl.id AS id,
        sl.inserted_date AS inserted_date,
        sl.username AS username_login,
        sl.application AS application,
        'Tools' AS application_name,
        sl.action AS action_name,
        sl.ip_user AS ip_user,
        sl.description AS description,
        COALESCE(
            substring(sl.description from 'task_id \(new\)\s*:\s*([0-9]+)'),
            tl.task_id::text
        ) AS task_id_log
    FROM sc_log sl
    LEFT JOIN mro_task_tools tl
        ON tl.task_tool_id = substring(sl.description from 'task_tool_id\s*:\s*([0-9]+)')::int
    WHERE sl.application = 'form_mro_task_tools'

    UNION ALL

    SELECT
        sl.id AS id,
        sl.inserted_date AS inserted_date,
        sl.username AS username_login,
        sl.application AS application,
        'Materials' AS application_name,
        sl.action AS action_name,
        sl.ip_user AS ip_user,
        sl.description AS description,
        COALESCE(
            substring(sl.description from 'task_id \(new\)\s*:\s*([0-9]+)'),
            mt.task_id::text
        ) AS task_id_log
    FROM sc_log sl
    LEFT JOIN mro_task_materials mt
        ON mt.task_material_id = substring(sl.description from 'task_material_id\s*:\s*([0-9]+)')::int
    WHERE sl.application = 'form_public_mro_task_materials'

    UNION ALL

    SELECT
        sl.id AS id,
        sl.inserted_date AS inserted_date,
        sl.username AS username_login,
        sl.application AS application,
        'Attachments' AS application_name,
        sl.action AS action_name,
        sl.ip_user AS ip_user,
        sl.description AS description,
        COALESCE(
            substring(sl.description from 'task_id \(new\)\s*:\s*([0-9]+)'),
            at.task_id::text
        ) AS task_id_log
    FROM sc_log sl
    LEFT JOIN mro_attachments at
        ON at.attachment_id = substring(sl.description from 'attachment_id\s*:\s*([0-9]+)')::int
    WHERE sl.application = 'form_public_mro_attachments'
) t
WHERE t.task_id_log = CAST([glo_task_id] AS TEXT)
ORDER BY t.id DESC