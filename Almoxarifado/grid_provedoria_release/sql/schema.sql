SELECT
    t.task_id AS task_id,
    t.wbs_id AS wbs_id,
    t.task_code AS task_code,
    t.task_name AS task_name,
    t.task_type AS task_type,
    t.is_nrc AS is_nrc,
    t.parent_task_id AS parent_task_id,
    t.root_task_id AS root_task_id,
    t.ata_chapter AS ata_chapter,
    t.jic_number AS jic_number,
    t.zone AS zone,
    t.requires_rii AS requires_rii,
    t.is_ojt_eligible AS is_ojt_eligible,
    t.status_code AS status_code,
    t.target_start AS target_start,
    t.target_end AS target_end,
    t.actual_start AS actual_start,
    t.actual_end AS actual_end,
    t.created_at AS created_at,
    t.updated_at AS updated_at,
    t.target_duration_hours AS target_duration_hours,
    t.skill_code AS skill_code,
    t.is_blocked_material AS is_blocked_material,
    t.is_blocked_tool AS is_blocked_tool,
    t.is_blocked_labor AS is_blocked_labor,
    t.is_critical_path AS is_critical_path,
    t.baseline_start AS baseline_start,
    t.baseline_end AS baseline_end,
    t.amm_reference AS amm_reference,
    t.access_panel AS access_panel,
    t.estimated_hours AS estimated_hours,
    t.instruction_text AS instruction_text,
    t.safety_warnings AS safety_warnings,
    t.tools_required AS tools_required,
    t.deferment_status AS deferment_status,
    t.deferment_reason AS deferment_reason,
    t.nrc_status AS nrc_status,
    t.project_id AS project_id,
    t.origin_document AS origin_document,
    t.document_reference AS document_reference,
    t.mpd_reference AS mpd_reference,
    t.budgeted_labor_hours AS budgeted_labor_hours,
    t.p6_actual_hours AS p6_actual_hours,
    t.resource_list AS resource_list,
    t.phase_code AS phase_code,
    t.baseline_phase_code AS baseline_phase_code,
    t.frequency AS frequency,
    t.access_panels AS access_panels,
    t.zone_area AS zone_area,
    t.is_rii AS is_rii,
    t.is_oa AS is_oa,
    t.oa_hours AS oa_hours,
    t.oa_material_cost AS oa_material_cost,
    t.oa_batch_id AS oa_batch_id,
    t.estimated_material_cost AS estimated_material_cost,
    t.created_by AS created_by,
    t.corrective_action AS corrective_action,
    t.is_milestone AS is_milestone,
    t.is_blocked_predecessor AS is_blocked_predecessor,
    t.is_predecessor_manual AS is_predecessor_manual,
    t.pending_id AS pending_id
FROM public.mro_tasks t
-- MRO-126: Gated Process da Provedoria - lista apenas JICs com 100%
-- dos materiais bloqueantes disponiveis no estoque fisico
WHERE t.task_id IN (
    SELECT tm.task_id
    FROM mro_task_materials tm
    JOIN mro_materials m ON tm.material_id = m.material_id
    WHERE tm.is_applied IS NOT TRUE
      AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
    GROUP BY tm.task_id
    HAVING COUNT(*) FILTER (WHERE m.stock_balance < tm.planned_qty) = 0
)
  AND t.status_code IN ('PLANNED', 'NOT_STARTED')
ORDER BY t.task_code
