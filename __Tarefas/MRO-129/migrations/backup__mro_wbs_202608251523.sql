-- public.mro_wbs definição
-- Drop table
-- DROP TABLE public.mro_wbs;

create table public.mro_wbs (
	wbs_id serial4 not null,
	project_id int4 null,
	wbs_code varchar(100) null,
	wbs_name varchar(255) null,
	phase_type varchar(50) null,
	constraint mro_wbs_pkey primary key (wbs_id)
);
-- public.mro_wbs chaves estrangeiras

alter table public.mro_wbs add constraint mro_wbs_project_id_fkey foreign key (project_id) references public.mro_projects(project_id);



INSERT INTO public.mro_wbs (project_id,wbs_code,wbs_name,phase_type) VALUES
	 (2,NULL,'Task Card do Check',NULL),
	 (2,NULL,'Diretivas Técnicas',NULL),
	 (2,NULL,'Procedimentos Digex',NULL),
	 (2,NULL,'Cartões Especiais',NULL),
	 (2,NULL,'Tarefas do Workscope em Time & Material',NULL),
	 (2,NULL,'GOL LINHAS AÉREAS S.A - B737-XXX - CHECK NCXX - PR-XXX - SJK',NULL),
	 (3,NULL,'Task Card do Check',NULL),
	 (3,NULL,'Procedimentos Digex',NULL),
	 (3,NULL,'Cancelada',NULL),
	 (3,NULL,'Aprovadas CAP ZERO',NULL);
INSERT INTO public.mro_wbs (project_id,wbs_code,wbs_name,phase_type) VALUES
	 (3,NULL,'Tarefas Adicionais em Time & Material',NULL),
	 (3,NULL,'Rotinas Canceladas',NULL),
	 (4,NULL,'Task Card do Check',NULL),
	 (4,NULL,'Rotinas Canceladas',NULL),
	 (4,NULL,'Diretivas TÃ©cnicas',NULL),
	 (4,NULL,'Procedimentos Digex',NULL),
	 (4,NULL,'Aprovadas Abaixo do CAP',NULL),
	 (4,NULL,'Aprovadas Acima do CAP',NULL),
	 (4,NULL,'Aprovadas CAP ZERO',NULL),
	 (4,NULL,'Cancelada',NULL);
INSERT INTO public.mro_wbs (project_id,wbs_code,wbs_name,phase_type) VALUES
	 (4,NULL,'Tarefas Adicionais em Time & Material',NULL),
	 (5,NULL,'Task Card do Check',NULL),
	 (5,NULL,'Rotinas Canceladas',NULL),
	 (5,NULL,'Diretivas TÃ©cnicas',NULL),
	 (5,NULL,'Procedimentos Digex',NULL),
	 (5,NULL,'Tarefas Adicionais em Time & Material',NULL),
	 (5,NULL,'Aprovadas Abaixo do CAP',NULL),
	 (5,NULL,'Cancelada',NULL),
	 (5,NULL,'Aprovadas Acima do CAP',NULL),
	 (5,NULL,'Aprovadas CAP ZERO',NULL);
INSERT INTO public.mro_wbs (project_id,wbs_code,wbs_name,phase_type) VALUES
	 (5,NULL,'Aguardando AprovaÃ§Ã£o do Cliente CAP ZERO',NULL);
