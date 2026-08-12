# Alocações — Concluídas (grid_public_mro_task_assignments_completed)

Módulo Produção e Manutenção — aplicação do tipo Grid (convertida de Form).

Grid de tarefas finalizadas pelos mecânicos. É a quarta aba do Painel do Supervisor, exibindo alocações com status `PENDING_HANDOVER`/`SUPSIG`, somente leitura.

## Filtros da consulta

- `planned_skill_id` = skill do usuário logado
- `supervisor_id` nulo ou igual ao usuário logado
- `status_code` em `PENDING_HANDOVER`, `SUPSIG`

## Joins aplicados

- `mro_projects` → `p6_proj_id` (código do projeto)
- `mro_tasks` → `task_code + ' - ' + task_name` (descrição da tarefa)
- `mro_employees` → `full_name` (nome do mecânico)

Todos os campos retornados usam alias.

## O que o usuário pode fazer

- Visualizar tarefas concluídas pela equipe.
- Verificar horas reais apontadas versus horas planejadas.
- Acompanhar as assinaturas de qualidade pendentes.
- Acessar o histórico da atribuição via botão de barra de ação (abre a `blank_mro_timeline`).

## Dados envolvidos

Tabela `public.mro_task_assignments` (com joins em `mro_projects`, `mro_tasks` e `mro_employees`).
