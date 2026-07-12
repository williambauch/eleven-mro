# Estrutura Analítica — EAP/WBS (grid_public_mro_wbs)

Módulo Planejamento e Engenharia — aplicação do tipo Grid.

Tela de consulta da Estrutura Analítica do Projeto (EAP), também conhecida como WBS (Work Breakdown Structure). Exibe os pacotes de trabalho que compõem cada projeto, organizados por código, nome e tipo de fase.

## O que o usuário pode fazer

- Visualizar a árvore de pacotes de trabalho de um projeto.
- Consultar o código WBS, nome, tipo de fase e projeto associado.
- Navegar para as tarefas vinculadas a cada pacote de trabalho.

## Comportamentos e regras importantes

- A grid consulta a tabela `public.mro_wbs` e pode ser filtrada por projeto.
- Cada entrada na WBS pode conter múltiplas tarefas associadas.
- O `phase_type` indica a natureza do pacote de trabalho dentro do ciclo de manutenção.

## Dados envolvidos

Tabela `public.mro_wbs`. Relacionada com `public.mro_projects` pelo campo `project_id`.
