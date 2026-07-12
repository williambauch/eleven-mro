# Kanban Crítico (blank_kanban_critical)

Módulo Produção e Manutenção — aplicação do tipo Blank.

Quadro Kanban focado em tarefas críticas ou bloqueadas. Destaca as tarefas que estão impedindo o andamento do projeto, permitindo ao supervisor priorizar ações de desbloqueio.

## O que o usuário pode fazer

- Visualizar apenas tarefas críticas ou bloqueadas.
- Priorizar ações para desbloqueio.

## Dados envolvidos

Tabela `public.mro_task_assignments` com status BLOCKED ou tarefas com bloqueios ativos.
