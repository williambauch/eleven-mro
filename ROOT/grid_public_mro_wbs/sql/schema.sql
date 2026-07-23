SELECT
    wbs_id,
    project_id,
    wbs_code,
    wbs_name,
    phase_type
FROM
    "public".mro_wbs
WHERE project_id=[project_id]