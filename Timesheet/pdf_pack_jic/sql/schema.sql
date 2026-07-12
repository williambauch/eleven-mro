SELECT 
    t.task_id,
    t.task_code,
    t.task_type,
    t.task_name AS discrepancy_title,
    t.instruction_text AS corrective_action_description,
    COALESCE(t.origin_document, t.task_code) AS origin_document,
    t.document_reference,
    t.ata_chapter,
    t.zone_area,
    t.access_panels,
    t.is_rii,
    t.estimated_hours,
    t.status_code,
    p.work_order AS ac_work_order,
    a.registration AS aircraft_registration,
    a.msn AS aircraft_esn,
    e.full_name AS originated_by_name,
    LEFT(a.customer_name,15) AS customer_name,
    p.p6_proj_id as project,
    a.model,
    CASE WHEN is_nrc IS true THEN 'NON ROUTINE JOB INSTRUCTION CARD' ELSE 'JOB INSTRUCTION CARD' END AS type,
    (CASE WHEN is_nrc IS true THEN 'N' ELSE '0' END || 
     LPAD(COALESCE(t.origin_document, t.task_code), 20, '0') || 
     'I' || 
     CASE WHEN is_nrc IS true THEN RIGHT(t.task_code, 3) ELSE '000' END) AS barcode,
     t.skill_code
FROM 
    mro_tasks t
INNER JOIN 
    mro_projects p ON t.project_id = p.project_id
LEFT JOIN 
    mro_aircraft a ON p.aircraft_id = a.aircraft_id
LEFT JOIN 
    mro_employees e ON t.created_by = e.login
WHERE 
    t.task_id = [glo_task_id]