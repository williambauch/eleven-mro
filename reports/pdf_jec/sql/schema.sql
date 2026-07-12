SELECT 
    t.task_code,
    t.task_name,
    COALESCE(t.instruction_text, '') as instruction,
    COALESCE(t.skill_code, 'GEN') as skill,
    COALESCE(t.estimated_hours, 0) as mh,
    COALESCE(t.zone, 'N/A') as zone,
    COALESCE(t.amm_reference, 'N/A') as amm,
    p.project_name,
    a.registration as reg,      
    a.model as model,  
    a.customer_name as company, 
    '38.25' as work_order,
   ata_chapter as ata_chapter,
    COALESCE((SELECT t2.task_code FROM mro_tasks t2 WHERE t2.task_id = t.parent_task_id), 'N/A') as origin_doc,
    COALESCE(t.is_nrc, FALSE) as is_nrc 
FROM mro_tasks t
LEFT JOIN mro_wbs w ON t.wbs_id = w.wbs_id
LEFT JOIN mro_projects p ON w.project_id = p.project_id
LEFT JOIN mro_aircraft a ON p.aircraft_id = a.aircraft_id
WHERE t.task_id = [glo_task_id]