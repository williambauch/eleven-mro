-- ============================================================
-- PACK JIC -  Arquivo para validacao 
-- ============================================================

-- ============================================================
-- PAGINA 01 — JIC
-- mGerarPagina1JIC
-- ============================================================
-- Task para teste: 13825 (900001), 13829 (900005), 12 (370034)
SELECT 
    t.task_id, t.task_code, t.task_type, t.task_name AS discrepancy_title,
    t.instruction_text AS corrective_action_description,
    COALESCE(t.origin_document, t.task_code) AS origin_document,
    t.document_reference, t.ata_chapter, t.zone_area,
    t.access_panels, t.is_rii, t.estimated_hours, t.status_code,
    t.skill_code, t.corrective_action, t.deferment_reason,
    t.deferment_status, t.amm_reference, t.tools_required,
    p.work_order AS ac_work_order,
    p.p6_proj_id AS project,
    a.registration AS aircraft_registration,
    a.msn AS aircraft_esn, a.model, a.customer_name,
    e.full_name AS originated_by_name,
    CASE WHEN t.is_nrc IS true THEN 'NON ROUTINE JOB INSTRUCTION CARD CFEF'
         ELSE 'JOB INSTRUCTION CARD CFEF' END AS type,
    (CASE WHEN t.is_nrc IS true THEN 'N' ELSE '0' END ||
     LPAD(COALESCE(NULLIF(t.origin_document, ''), t.task_code), 20, '0') ||
     'I' ||
     CASE WHEN t.is_nrc IS true THEN RIGHT(t.task_code, 3) ELSE '000' END) AS barcode,
    t.is_nrc AS is_nrc
FROM mro_tasks t
INNER JOIN mro_projects p ON t.project_id = p.project_id
LEFT JOIN mro_aircraft a ON p.aircraft_id = a.aircraft_id
LEFT JOIN mro_employees e ON t.created_by = e.login
WHERE t.task_id = 13825;  -- troque pelo task_id desejado

-- ============================================================
-- PAGINA 01 — SKILLS
-- mGerarPagina1JIC (skills)
-- ============================================================
-- Task para teste: 13825 (S4 -- 2.55h), 13829 (A4 -- 7.14h)
SELECT COALESCE(string_agg(resource_code || ' -- ' ||
    CASE WHEN COALESCE(budgeted_hours, 0) = 0 THEN '0h'
         ELSE budgeted_hours::text || 'h'
    END, ', ' ORDER BY resource_code), '') AS skill_list
FROM mro_task_resources
WHERE task_id = 26330
  AND resource_code IN (SELECT DISTINCT skill_code FROM mro_skills);

-- ============================================================
-- PAGINA 01 — MATERIAIS
-- mGerarPagina1JIC (materiais)
-- ============================================================
-- Task para teste: 18206 (2), 18604 (5)
SELECT
    COALESCE(string_agg(CASE WHEN tm.is_applied = true THEN m.part_number END, ', '), '') AS pn_instalado,
    COALESCE(string_agg(CASE WHEN tm.is_applied = true THEN tm.batch_sn END, ', '), '') AS sn_instalado,
    COALESCE(string_agg(CASE WHEN tm.is_applied = false THEN m.part_number END, ', '), '') AS pn_removido,
    COALESCE(string_agg(CASE WHEN tm.is_applied = false THEN tm.batch_sn END, ', '), '') AS sn_removido
FROM mro_task_materials tm
LEFT JOIN mro_materials m ON tm.material_id = m.material_id
WHERE tm.task_id = 18206;

-- ============================================================
-- PAGINA 01 — EXECUTOR
-- mGerarPagina1JIC (executor)
-- ============================================================
-- Task para teste: 18148 (teste), 18861 (Mecanico Teste)
SELECT DISTINCT e.full_name
FROM mro_task_assignments ta
LEFT JOIN mro_employees e ON ta.executed_by_employee_id = e.employee_id
WHERE ta.task_id = 18861
  AND e.full_name IS NOT NULL
LIMIT 1;

-- ============================================================
-- PAGINA 02 — JEC
-- mGerarPagina2JEC
-- ============================================================
-- Task para teste: 13825 (4), 13829 (4)
SELECT COALESCE(string_agg(
    '<tr><td>' || COALESCE(tl.part_number, '') || '</td>' ||
    '<td>' || COALESCE(tl.description, '') || '</td>' ||
    '<td>' || COALESCE(tt.quantity_required::text, '') || '</td></tr>'
    , '' ORDER BY tl.part_number), '') AS tool_rows
FROM mro_task_tools tt
LEFT JOIN mro_tools tl ON tt.tool_id = tl.tool_id
WHERE tt.task_id = 13825;

-- ============================================================
-- PAGINA 03 — JMC
-- mGerarPagina3JMC
-- ============================================================
-- Task para teste: 18206 (2), 18604 (5)
SELECT COALESCE(string_agg(
    '<tr>' ||
    '<td>' || COALESCE(m.part_number, '') || '</td>' ||
    '<td>' || COALESCE(m.unit_measure, '') || '</td>' ||
    '<td>' || COALESCE(tm.planned_qty::text, '') || '</td>' ||
    '<td>' || COALESCE(m.part_number, '') || '</td>' ||
    '<td>' || COALESCE(tm.batch_sn, '') || '</td>' ||
    '<td>' || COALESCE(m.description, '') || '</td>' ||
    '<td>' || COALESCE(m.stock_location, '') || '</td>' ||
    '<td>' || COALESCE(tm.applied_qty::text, '') || '</td>' ||
    '<td>' || CASE WHEN tm.is_applied = true THEN 'YES' ELSE 'NO' END || '</td>' ||
    '</tr>'
    , '' ORDER BY m.part_number), '') AS material_rows
FROM mro_task_materials tm
LEFT JOIN mro_materials m ON tm.material_id = m.material_id
WHERE tm.task_id = 18206;

-- ============================================================
-- PAGINA 04 — Shift Turnover
-- mGerarPagina4Shift
-- ============================================================
-- Task para teste: 18148 (6), 18149 (2), 18861 (2)
SELECT COALESCE(string_agg(
    COALESCE(ts.handover_notes, '') || '|' ||
    COALESCE(ts.end_time::text, '') || '|' ||
    COALESCE(e.employee_registration, '')
, '~~' ORDER BY ts.end_time), '') AS shift_data
FROM mro_timesheet ts
INNER JOIN mro_task_assignments ta ON ts.assignment_id = ta.assignment_id
LEFT JOIN mro_employees e ON ts.employee_id = e.employee_id
WHERE ta.task_id = 18148
  AND ts.handover_notes IS NOT NULL AND ts.handover_notes != '';

-- ============================================================
-- PAGINA 05 — Calibrated Tool
-- mGerarPagina5Calibrated
-- ============================================================
-- Task para teste: 13825 (4), 13829 (4)
SELECT COALESCE(string_agg(
    COALESCE(tl.last_calibration_date::text, '') || '|' ||
    COALESCE(tl.description, '') || '|' ||
    COALESCE(tl.calibration_due_date::text, '') || '|' ||
    COALESCE(tl.part_number, '') || '|' ||
    COALESCE(tl.serial_number, '')
, '~~' ORDER BY tl.part_number), '') AS calib_data
FROM mro_task_tools tt
INNER JOIN mro_tools tl ON tt.tool_id = tl.tool_id
WHERE tt.task_id = 13825;
