# Dispatch (grid_mro_dispatch)

Módulo Produção e Manutenção — aplicação do tipo Grid.

Visão de despacho operacional das tarefas alocadas aos mecânicos. Exibe em uma única tela o código da tarefa, o nome do mecânico, as horas planejadas e o status da alocação. Usada pelo supervisor para alocação rápida e acompanhamento da distribuição da equipe.

## O que o usuário pode fazer

- Visualizar todas as alocações com mecânico e tarefa.
- Consultar horas planejadas versus realizadas.
- Acompanhar o status de cada alocação.

## Dados envolvidos

Tabelas `public.mro_task_assignments` e `public.mro_tasks` (join por assignment e task).
