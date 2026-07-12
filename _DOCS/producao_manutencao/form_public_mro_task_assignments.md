# Alocações (form_public_mro_task_assignments)

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário de alocação de mecânicos a tarefas de manutenção. Permite ao supervisor designar um mecânico para executar uma tarefa, definir as horas planejadas e acompanhar o status da execução.

## O que o usuário pode fazer

- Alocar um mecânico a uma tarefa específica.
- Definir horas planejadas e especialidade necessária.
- Alterar o status da alocação (não iniciado, em andamento, pausado, bloqueado, concluído).
- Visualizar o tempo decorrido da alocação.

## Dados envolvidos

Tabela `public.mro_task_assignments`. Relacionada com `mro_tasks`, `mro_employees` e `mro_skills`.
