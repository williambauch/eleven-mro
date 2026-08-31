INSERT INTO public.mro_sys_status (status_code,"module",label_ptbr,kanban_color,icon,display_order) VALUES
	 ('PLANNED','TASKS','Planejado (P6)','#adb5bd','fas fa-calendar-alt',1),
	 ('NOT_STARTED','TASKS','Não Iniciado (Liberado)','#6c757d','fas fa-clipboard-list',2),
	 ('IN_PROGRESS','TASKS','Em Execução','#28a745','fas fa-play',3),
	 ('PAUSED','TASKS','Pausado','#ffc107','fas fa-pause',4),
	 ('BLOCKED','TASKS','Bloqueado (Impedimento)','#dc3545','fas fa-ban',5),
	 ('PENDING_HANDOVER','TASKS','Aguardando Repasse','#fd7e14','fas fa-exchange-alt',6),
	 ('SUPSIG','TASKS','Aguardando Assinatura','#17a2b8','fas fa-file-signature',7),
	 ('COMPLETED','TASKS','Concluído','#20c997','fas fa-check-double',8),
	 ('DRAFT','NRC','Rascunho (Mecânico)','#6c757d','fas fa-edit',1),
	 ('PENDING_ENG','NRC','Fila Engenharia','#ffc107','fas fa-hard-hat',2);
INSERT INTO public.mro_sys_status (status_code,"module",label_ptbr,kanban_color,icon,display_order) VALUES
	 ('ENG_REVIEW','NRC','Em Análise (Eng)','#17a2b8','fas fa-search',3),
	 ('WAITING_CUSTOMER','NRC','Aguardando Aprovação','#fd7e14','fas fa-user-tie',4),
	 ('APPROVED','NRC','Aprovado (Cliente)','#28a745','fas fa-thumbs-up',5),
	 ('REJECTED','NRC','Reprovado / Diferido','#dc3545','fas fa-thumbs-down',6);
