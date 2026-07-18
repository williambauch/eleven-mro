# Alocações (form_public_mro_task_assignments)

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário de alocação de mecânicos a tarefas de manutenção. Permite ao supervisor designar um mecânico para executar uma tarefa, definir as horas planejadas e acompanhar o status da execução.

## O que o usuário pode fazer

- Alocar um mecânico a uma tarefa específica.
- Definir horas planejadas e especialidade necessária.
- Alterar o status da alocação (não iniciado, em andamento, pausado, bloqueado, concluído).
- Visualizar o tempo decorrido da alocação.
- Iniciar o cronômetro (botão Play) para registro de horas.

## Comportamentos e regras importantes

- **Bloqueio por falta de material**: Se a tarefa estiver com `is_blocked_material = true` na tabela `mro_tasks`, o botão Play é ocultado e o mecânico não consegue iniciar o apontamento. O bloqueio é verificado em duas camadas:
  - **No carregamento da tela (onLoad)**: consulta o `is_blocked_material` da tarefa. Se bloqueada, o botão Play some, exibe um alerta visual vermelho no lugar do cronômetro e mostra um `sc_alert` avisando do bloqueio.
  - **No clique do Play**: antes de criar o timesheet, verifica novamente o bloqueio como segurança adicional. Se bloqueado, exige `sc_error_message` e aborta.
- **Conflito de atividades**: Se o mecânico já possui uma tarefa em andamento, o sistema bloqueia o novo Play e redireciona para a tela de pausa da tarefa ativa.
- **Cronômetro**: Ao iniciar, cria um registro em `mro_timesheet` com status `IN_PROGRESS` e exibe o tempo decorrido em tempo real.

## Dados envolvidos

Tabela `public.mro_task_assignments`. Relacionada com `mro_tasks`, `mro_employees` e `mro_skills`.

Tabelas consultadas: `mro_timesheet`, `mro_tasks` (para verificar bloqueio de material).
