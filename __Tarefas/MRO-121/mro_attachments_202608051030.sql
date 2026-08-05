-- public.mro_attachments definição
-- Drop table
-- DROP TABLE public.mro_attachments;

create table public.mro_attachments (
	attachment_id serial4 not null,
	task_id int4 null,
	project_id int4 null,
	aircraft_id int4 null,
	file_name varchar(255) not null,
	description varchar(255) null,
	file_size_kb int4 null,
	uploaded_by varchar(50) null,
	uploaded_at timestamp default CURRENT_TIMESTAMP null,
	sync_sharepoint bool default false null,
	constraint mro_attachments_pkey primary key (attachment_id)
);

create index idx_mro_attachments_aircraft on
public.mro_attachments
    using btree (aircraft_id);

create index idx_mro_attachments_project on
public.mro_attachments
    using btree (project_id);

create index idx_mro_attachments_task on
public.mro_attachments
    using btree (task_id);



INSERT INTO public.mro_attachments (task_id,project_id,aircraft_id,file_name,description,file_size_kb,uploaded_by,uploaded_at,sync_sharepoint) VALUES
	 (0,0,3,'doc_exemplo.pdf','nome',33133,'william','2026-08-03 17:10:35',false),
	 (0,0,3,'E195-E2(2).png','',793683,'william','2026-08-03 16:54:01',false),
	 (0,0,3,'gato02.jpg','vários gatos',693952,'william','2026-08-03 17:29:44',false),
	 (0,6,0,'gato03.jpg','Gato Sorrindo',5906,'william','2026-08-03 17:46:30',false),
	 (0,6,0,'gato04.jpg','gato 4 no projeto 6',721175,'william','2026-08-03 18:05:44',false),
	 (13831,0,0,'canelinha2.jpg','Task code 900007 ',81528,'william','2026-08-04 10:12:39',false),
	 (0,12,0,'image_3.jpg','Projeto 12',1945591,'william','2026-08-04 11:01:50',false),
	 (0,0,8,'PR-GGE GOL Boeing 737-8EH(WL).jpg','PR-GGE GOL Boeing 737-8EH(WL)',226892,'william','2026-08-04 11:05:30',false),
	 (18861,0,0,'PDF PR-GGE GOL Boeing 737-8EH(WL).pdf','Tarefa DISINFECT PORTABLE WATER SYSTEM',999800,'william','2026-08-04 11:09:20',false),
	 (0,0,8,'mapa de assentos.png','mapa de assentos aircraft_id	8',142202,'william','2026-08-04 11:48:06',false);
INSERT INTO public.mro_attachments (task_id,project_id,aircraft_id,file_name,description,file_size_kb,uploaded_by,uploaded_at,sync_sharepoint) VALUES
	 (15083,0,0,'image_3.jpg','caminho rua verde',1945591,'william','2026-08-04 13:29:52',false);
