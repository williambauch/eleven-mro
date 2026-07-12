# Painel do Mecânico (grid_my_tasks)

Módulo Produção e Manutenção — aplicação do tipo Grid.

Tela principal do mecânico no tablet. Lista as tarefas ativas alocadas a ele ou ao seu funcionário, permitindo iniciar, pausar ou concluir o trabalho com clock-in/clock-out.

## O que o usuário pode fazer

- Visualizar suas tarefas ativas com status (atribuída, em andamento, pausada ou não iniciada).
- Iniciar o trabalho em uma tarefa (clock-in).
- Pausar o trabalho (clock-out por repasse de turno, descanso ou refeição).
- Reportar impedimento (clock-out crítico por falta de peça, ferramenta quebrada, etc.).
- Finalizar a tarefa após conclusão.

## Comportamentos e regras importantes

- A grid consulta `mro_task_assignments` filtrando pelo `executed_by_employee_id` do usuário logado (obtido de `usr_employee_id`).
- Exibe apenas alocações com status ativos: ASSIGNED, IN_PROGRESS, PAUSED ou NOT_STARTED.
- O mecânico só vê as tarefas que foram efetivamente alocadas a ele pelo supervisor.
- Ao iniciar uma tarefa, o sistema ativa a contagem de horas (timesheet) para aquela alocação.
- O painel é otimizado para uso em dispositivos móveis (tablets) no chão de fábrica.

## Dados envolvidos

Tabela `mro_task_assignments`. Relacionada com `mro_tasks` e `mro_employees`.
