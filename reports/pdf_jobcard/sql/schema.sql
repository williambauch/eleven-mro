SELECT 
    -- 1. IDENTIFICAÇÃO DO DOCUMENTO
    p.work_order AS ac_work_order,          -- Puxando do Projeto (W.O. Pai)
    t.task_code AS document_no,             -- Código da NR
    a.customer_name AS company,             
    COALESCE((SELECT t2.task_code FROM mro_tasks t2 WHERE t2.task_id = t.parent_task_id), 'N/A') as origin_doc,  
    
    -- 2. DADOS DA AERONAVE E PROJETO
    a.registration AS ac_registration,      
    p.project_name AS project_number,         
    t.ata_chapter AS ata,                   
    a.model AS ac_type,                     
    a.csn AS ac_sn,               
    
    -- 3. REQUISITOS DE EXECUÇÃO
    t.skill_code AS skill,        
    t.estimated_hours AS estimated_hours,   
    t.zone_area AS area_zone,               
    
    -- 4. TEXTOS DESCRITIVOS
    t.task_name AS discrepancy,           
    t.corrective_action AS corrective_action,
    t.instruction_text AS instruction_text,

    
    -- 5. ASSINATURAS E ORIGEM
    u.name AS originated_by,                
    t.created_at AS origination_date        
FROM 
    mro_tasks t
INNER JOIN 
    mro_projects p ON t.project_id = p.project_id
INNER JOIN 
    mro_aircraft a ON p.aircraft_id = a.aircraft_id
LEFT JOIN 
    sec_users u ON t.created_by = u.login
WHERE 
    t.task_id = [glo_task_id]