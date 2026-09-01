-- =============================================
-- MRO-126 / UAT FR-03: Comentarios das colunas de pessoa em mro_task_assignments
-- Documenta a semantica de employee_id vs executed_by_employee_id
-- (diferenca critica para o bloqueio de fechamento de JIC com ferramenta em custodia)
-- =============================================

COMMENT ON COLUMN public.mro_task_assignments.employee_id IS
    'Mecanico com a atribuicao EM MAOS (clock-in ativo). Preenchido no Play e na  ROTA 5 da control_pause_task. Usado na ferramentaria para vincular as ferramentas em custodia (mro_tool_transactions) ao mecanico e bloquear o fechamento da JIC enquanto houver ferramenta pendente.';

COMMENT ON COLUMN public.mro_task_assignments.executed_by_employee_id IS
    'Mecanico DESIGNADO para executar a task (atribuido pelo supervisor ou split). Pode diferir do employee_id (quem esta com a atribuicao em maos agora).';

COMMENT ON COLUMN public.mro_task_assignments.supervisor_id IS
    'Supervisor responsavel pela atribuicao (quem distribuiu/supervisiona).';
