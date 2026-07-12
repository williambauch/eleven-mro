SELECT 
    b.batch_id,
    b.project_id,
    b.status,
    b.created_at,
    (SELECT COALESCE(SUM(oa_hours), 0) FROM mro_tasks WHERE oa_batch_id = b.batch_id) as total_horas_lote,
    (SELECT COALESCE(SUM(oa_material_cost), 0) FROM mro_tasks WHERE oa_batch_id = b.batch_id) as total_mat_lote,
    b.submitted_at,
    b.approved_at,
    b.remarks
FROM 
    mro_oa_batches b
ORDER BY 
    b.batch_id DESC