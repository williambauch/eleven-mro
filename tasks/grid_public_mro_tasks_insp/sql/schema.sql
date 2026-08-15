SELECT
    t.task_id             AS task_id,
    t.task_code           AS task_code,
    CONCAT(t.task_code, ' - ', t.task_name) AS task_desc,
    t.jic_number          AS jic_number,
    t.requires_rii        AS requires_rii,
    t.is_rii              AS is_rii,
    t.status_code         AS status_code,
    t.skill_code          AS skill_code,
    t.project_id          AS project_id,
    p.p6_proj_id          AS project_code,
    t.actual_end          AS actual_end,
    t.updated_at          AS updated_at,
    h1.user_login         AS inspector_1_login,
    h2.user_login         AS inspector_2_login
FROM "public".mro_tasks t
LEFT JOIN "public".mro_projects p ON p.project_id = t.project_id
LEFT JOIN "public".mro_task_history h1 ON h1.task_id = t.task_id
     AND h1.action_taken = 'INSPECTOR_1'
LEFT JOIN "public".mro_task_history h2 ON h2.task_id = t.task_id
     AND h2.action_taken = 'INSPECTOR_2'
WHERE t.status_code IN ('PENDING_INSP1','PENDING_INSP2')
  AND EXISTS (
      SELECT 1 FROM "public".mro_task_assignments a
      WHERE a.task_id = t.task_id
        AND a.planned_skill_id = [usr_skill_id]
  )
ORDER BY t.updated_at DESC