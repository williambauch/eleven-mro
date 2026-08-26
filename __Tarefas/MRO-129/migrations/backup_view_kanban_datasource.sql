-- public.view_kanban_datasource fonte

create or replace
view public.view_kanban_datasource
as
select
    t.task_id,
    t.task_code as codigo,
    t.task_name as descricao,
    coalesce(t.status_code, 'NOT_STARTED'::character varying) as status_chave,
    coalesce(w.wbs_name, 'SEM FASE'::character varying) as fase_nome,
    case
        when t.is_nrc = true then 'High'::text
        else 'Normal'::text
    end as prioridade,
    case
        when t.is_nrc = true then 'Não Rotina'::text
        else 'Rotina'::text
    end as tipo_tarefa,
    coalesce(t.skill_code, 'Geral'::character varying) as skill,
    coalesce(w.project_id, 0) as project_id,
    "left"(coalesce(t.skill_code, 'G'::character varying)::text,
    2) as iniciais_skill
from
    mro_tasks t
left join mro_wbs w on
    t.wbs_id = w.wbs_id
where
    t.status_code is not null;
