-- public.view_gantt_datasource fonte

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
    coalesce(t.target_start, t.baseline_start) as data_inicio,
    case
        when t.is_milestone = true then coalesce(t.target_start, t.baseline_start)
        else coalesce(t.target_end, t.baseline_end)
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
    coalesce(t.target_start, t.baseline_start, '9999-12-31 00:00:00'::timestamp without time zone) as task_sort_date
from
    mro_tasks t
left join mro_project_phases f on
    t.phase_code::text = f.phase_code::text;
