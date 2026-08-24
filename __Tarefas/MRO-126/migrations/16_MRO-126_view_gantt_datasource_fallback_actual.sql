-- =============================================
-- MRO-126: view_gantt_datasource - fallback com actual_start/actual_end
--
-- Nova ordem de datas no Gantt (TAREFA/NRC):
--   data_inicio: target_start -> actual_start -> baseline_start
--   data_fim:    target_end   -> actual_end   -> baseline_end
--
-- Racional:
--   - target_*   = planejado (editado pelo Gantt)
--   - actual_*   = preenchimento automatico (task iniciada/concluida)
--   - baseline_* = baseline P6 (ultimo recurso)
--
-- Tratamento do actual_end nulo (task em execucao):
--   Se a task JÁ FOI INICIADA (actual_start preenchido) mas ainda nao
--   concluiu (actual_end nulo) e nao possui target_end, a barra mostra
--   apenas o dia de inicio real (1 dia) em vez de puxar a baseline antiga
--   do P6 (que esticava a timeline para 2024/2025).
-- =============================================

create or replace
view public.view_gantt_datasource
as
select
    'P'::text || p.project_id as id_unico,
    null::text as id_pai,
    p.project_name::character varying as nome_tarefa,
    p.p6_proj_id::character varying as codigo_atividade,
    p.start_date as data_inicio,
    p.end_date as data_fim,
    0 as progresso,
    'PROJECT'::text as tipo,
    null::text as predecessores,
    p.project_id,
    false as is_milestone,
    0 as phase_sort,
    '0001-01-01 00:00:00'::timestamp without time zone as task_sort_date
from
    mro_projects p
union all
select
    distinct on
    (t.project_id,
    (coalesce(t.phase_code, 'UNASSIGNED'::character varying))) (('F'::text || t.project_id) || '_'::text) || coalesce(t.phase_code, 'UNASSIGNED'::character varying)::text as id_unico,
    'P'::text || t.project_id as id_pai,
    coalesce(f.phase_name, ('Fase - '::text || coalesce(t.phase_code, 'Sem Fase Atribuída'::character varying)::text)::character varying) as nome_tarefa,
    coalesce(t.phase_code, 'UNASSIGNED'::character varying) as codigo_atividade,
    null::date as data_inicio,
    null::date as data_fim,
    0 as progresso,
    'PHASE'::text as tipo,
    null::text as predecessores,
    t.project_id,
    false as is_milestone,
    coalesce(f.sort_order, 999) as phase_sort,
    '0001-01-02 00:00:00'::timestamp without time zone as task_sort_date
from
    mro_tasks t
left join mro_project_phases f on
    t.phase_code::text = f.phase_code::text
union all
select
    t.task_id::text as id_unico,
    case
        when t.parent_task_id is not null then t.parent_task_id::text
        else (('F'::text || t.project_id) || '_'::text) || coalesce(t.phase_code, 'UNASSIGNED'::character varying)::text
    end as id_pai,
    t.task_name::character varying as nome_tarefa,
    t.task_code::character varying as codigo_atividade,
    -- data_inicio: target -> actual -> baseline
    coalesce(t.target_start, t.actual_start, t.baseline_start) as data_inicio,
    -- data_fim: target -> actual -> baseline
    -- Se a task ja foi iniciada (actual_start) mas nao concluiu (sem actual_end
    -- e sem target_end), usa o proprio inicio real como fim (barra de 1 dia)
    -- em vez de puxar a baseline antiga do P6.
    case
        when t.is_milestone = true then coalesce(t.target_start, t.actual_start, t.baseline_start)
        when t.actual_start is not null and t.target_end is null and t.actual_end is null then t.actual_start
        else coalesce(t.target_end, t.actual_end, t.baseline_end)
    end as data_fim,
    case
        when t.status_code::text = 'COMPLETED'::text then 100
        when t.status_code::text = 'IN_PROGRESS'::text then 50
        else 0
    end as progresso,
    case
        when t.is_nrc = true then 'NRC'::text
        else 'TASK'::text
    end as tipo,
    (
    select
        string_agg(dep.predecessor_task_id || dep.dep_type::text, ','::text) as string_agg
    from
        mro_task_dependencies dep
    where
        dep.successor_task_id = t.task_id) as predecessores,
    t.project_id,
    coalesce(t.is_milestone, false) as is_milestone,
    coalesce(f.sort_order, 999) as phase_sort,
    -- ordenacao tambem segue a nova ordem (target -> actual -> baseline)
    coalesce(t.target_start, t.actual_start, t.baseline_start, '9999-12-31 00:00:00'::timestamp without time zone) as task_sort_date
from
    mro_tasks t
left join mro_project_phases f on
    t.phase_code::text = f.phase_code::text;

-- =============================================
-- Validacao (apos executar):
 SELECT id_unico, data_inicio, data_fim
 FROM view_gantt_datasource
 WHERE project_id = 22 AND tipo IN ('TASK','NRC')
 ORDER BY data_inicio NULLS LAST LIMIT 10;
-- =============================================
