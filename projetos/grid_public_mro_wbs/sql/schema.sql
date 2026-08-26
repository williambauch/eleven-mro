SELECT
    wbs_id AS wbs_id,
    parent_wbs_id AS parent_wbs_id,
    wbs_code AS wbs_code,
    wbs_name AS wbs_name,
    wbs_level AS wbs_level,
    phase_type AS phase_type,
    sort_order AS sort_order
FROM
    "public".mro_wbs
ORDER BY sort_order