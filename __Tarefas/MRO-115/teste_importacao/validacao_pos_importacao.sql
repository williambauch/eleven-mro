-- MRO-115: Queries de validacao pos-importacao (projeto 5)
-- Rodar no servidor remoto (developer.elevenconsultoria.com.br)

-- 1. Tarefas bloqueadas vs nao bloqueadas (projeto 5)
SELECT 
    is_blocked_material,
    COUNT(*) as total
FROM mro_tasks 
WHERE project_id = 5 
GROUP BY is_blocked_material
ORDER BY is_blocked_material;

-- 2. Materiais marcados como is_blocking_task = false (nao bloqueiam)
SELECT material_id, product_code, part_number, description, is_blocking_task 
FROM mro_materials 
WHERE is_blocking_task = false
ORDER BY product_code;

-- 3. Empenhos com saldo contabilizado (committed_qty > 0)
SELECT COUNT(*) as total_committed,
       SUM(committed_qty) as sum_committed,
       SUM(committed_total_cost) as sum_committed_cost
FROM mro_task_materials tm
JOIN mro_tasks t ON tm.task_id = t.task_id
WHERE t.project_id = 5 AND tm.committed_qty > 0;


-- 4. Empenhos zerados (material bloqueou, committed_qty = 0)
SELECT COUNT(*) as total_zeroed
FROM mro_task_materials tm
JOIN mro_tasks t ON tm.task_id = t.task_id
WHERE t.project_id = 5 AND tm.committed_qty = 0;

-- 5. Amostra de tarefas bloqueadas com seus materiais
SELECT t.task_code, t.is_blocked_material, m.product_code, m.part_number, 
       tm.committed_qty, tm.planned_qty, tm.material_source
FROM mro_tasks t
JOIN mro_task_materials tm ON t.task_id = tm.task_id
JOIN mro_materials m ON tm.material_id = m.material_id
WHERE t.project_id = 5 AND t.is_blocked_material = true
LIMIT 20;


-- 6. Resumo geral do projeto 5
SELECT 
    COUNT(DISTINCT t.task_id) as tarefas_com_material,
    COUNT(DISTINCT CASE WHEN t.is_blocked_material = true THEN t.task_id END) as tarefas_bloqueadas,
    COUNT(DISTINCT CASE WHEN t.is_blocked_material = false THEN t.task_id END) as tarefas_liberadas,
    COUNT(tm.task_material_id) as total_empenhos,
    SUM(CASE WHEN tm.committed_qty > 0 THEN 1 ELSE 0 END) as empenhos_com_saldo,
    SUM(CASE WHEN tm.committed_qty = 0 THEN 1 ELSE 0 END) as empenhos_zerados
FROM mro_tasks t
JOIN mro_task_materials tm ON t.task_id = tm.task_id
WHERE t.project_id = 5;

