# Painel do Supervisor (tabs_supervisor)

Módulo Produção e Manutenção — aplicação do tipo Tabs.

Painel gerencial do supervisor de produção. Organiza as alocações de tarefas em quatro abas, cada uma exibindo um formulário específico de task assignments. É a tela principal para o supervisor gerenciar sua equipe.

## O que o usuário pode fazer

- Alternar entre as quatro abas de acompanhamento:

  - **A Distribuir**: tarefas planejadas que ainda não foram alocadas a nenhum mecânico.
  - **Em Andamento**: tarefas em execução pelos mecânicos.
  - **Com Impedimentos**: tarefas bloqueadas por material, ferramenta ou mão de obra.
  - **Concluídas**: tarefas finalizadas pelos mecânicos.

- Em cada aba, visualizar e gerenciar as alocações da equipe.
- Acompanhar o fluxo de trabalho desde a distribuição até a conclusão.

## Comportamentos e regras importantes

- Cada aba carrega um formulário de task assignment específico dentro de um iframe:
  - "A Distribuir" → `form_public_mro_task_assignments_planned`
  - "Em Andamento" → `form_public_mro_task_assignments_progress`
  - "Com Impedimentos" → `form_public_mro_task_assignments_blocked`
  - "Concluídas" → `form_public_mro_task_assignments_completed`
- Cada formulário recebe como parâmetros o `assignment_id`, `usr_skill_id` e `usr_employee_id` para contextualizar os dados.
- A aba "Com Impedimentos" também recebe o `usr_login` como parâmetro adicional.

## Para onde essa tela leva

Cada aba redireciona para os formulários específicos de task assignments, que por sua vez permitem alocar mecânicos, registrar horas e gerenciar impedimentos.

## Dados envolvidos

Tabela `mro_task_assignments`. Interface com múltiplos formulários de task assignments.
