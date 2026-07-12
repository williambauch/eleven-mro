# Timesheet (form_public_mro_timesheet)

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário de registro de apontamento de horas (timesheet). Registra clock-in e clock-out dos mecânicos nas tarefas, calcula a duração em minutos e permite associar comentários.

## O que o usuário pode fazer

- Registrar o início (clock-in) e fim (clock-out) do trabalho em uma tarefa.
- Visualizar a duração calculada automaticamente.
- Adicionar comentários sobre o período trabalhado.
- Registrar sessões de treinamento OJT (On-the-Job Training).

## Dados envolvidos

Tabela `public.mro_timesheet`. Relacionada com `mro_task_assignments` e `mro_employees`.
