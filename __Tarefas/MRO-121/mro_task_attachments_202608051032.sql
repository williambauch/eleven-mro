-- public.mro_task_attachments definição
-- Drop table
-- DROP TABLE public.mro_task_attachments;

create table public.mro_task_attachments (
	attachment_id serial4 not null,
	task_id int4 not null,
	project_id int4 not null,
	file_name varchar(255) not null,
	original_name varchar(255) null,
	file_size_kb int4 null,
	uploaded_by varchar(50) null,
	uploaded_at timestamp default CURRENT_TIMESTAMP null,
	sync_sharepoint bool default false null,
	constraint mro_task_attachments_pkey primary key (attachment_id)
);
-- public.mro_task_attachments chaves estrangeiras

alter table public.mro_task_attachments add constraint fk_task_attachment foreign key (task_id) references public.mro_tasks(task_id) on
delete
    cascade;



INSERT INTO public.mro_task_attachments (task_id,project_id,file_name,original_name,file_size_kb,uploaded_by,uploaded_at,sync_sharepoint) VALUES
	 (18144,12,'sc_pdf_20260608132012_492_pdf_jic(1).pdf','',550805,'admin','2026-07-06 12:38:58',false),
	 (18147,12,'sc_pdf_20260608132012_492_pdf_jic.pdf','',550805,'admin','2026-07-07 14:35:06',false),
	 (1,2,'image_3.jpg','',1945591,'','2026-07-16 13:17:33',false);
