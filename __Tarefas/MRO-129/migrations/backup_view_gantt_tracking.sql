-- public.view_gantt_tracking fonte

create or replace
view public.view_gantt_tracking
as
select
    'PROJ_'::text || p.project_id as id_unico,
    null::text as id_pai,
    p.project_name as nome_tarefa,
    p.start_date as data_inicio,
    p.end_date as data_fim,
    p.start_date as baseline_inicio,
    p.end_date as baseline_fim,
    0 as progresso,
    'PROJECT'::text as tipo,
    false as critico,
    p.project_id
from
    mro_projects p
union all
select
    'WBS_'::text || w.wbs_id as id_unico,
    'PROJ_'::text || w.project_id as id_pai,
    w.wbs_name as nome_tarefa,
    null::date as data_inicio,
    null::date as data_fim,
    null::date as baseline_inicio,
    null::date as baseline_fim,
    0 as progresso,
    'PHASE'::text as tipo,
    false as critico,
    w.project_id
from
    mro_wbs w
union all
select
    t.task_id::character varying as id_unico,
    case
        when t.parent_task_id is not null then t.parent_task_id::character varying::text
        else 'WBS_'::text || t.wbs_id
    end as id_pai,
    t.task_name as nome_tarefa,
    t.target_start as data_inicio,
    t.target_end as data_fim,
    t.baseline_start as baseline_inicio,
    t.baseline_end as baseline_fim,
    case
        when t.status_code::text = 'COMPLETED'::text then 100
        when t.status_code::text = 'IN_PROGRESS'::text then 50
        else 0
    end as progresso,
    case
        when t.is_nrc = true then 'NRC'::text
        else 'TASK'::text
    end as tipo,
    t.is_critical_path as critico,
    w.project_id
from
    mro_tasks t
join mro_wbs w on
    t.wbs_id = w.wbs_id;
