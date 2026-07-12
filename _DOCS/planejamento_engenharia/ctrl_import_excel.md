# Importador P6 (ctrl_import_excel)

Módulo Planejamento e Engenharia — aplicação do tipo Form.

Ferramenta de importação de pacotes de manutenção do Primavera P6. Lê arquivos CSV ou Excel exportados do P6 e monta automaticamente a árvore de tarefas (Projeto, Fase, Tarefa, Sub-tarefa) no banco do MRO.

## O que o usuário pode fazer

- Selecionar o arquivo de exportação do P6.
- Mapear as colunas do arquivo para os campos do sistema.
- Executar a importação para criar projetos, WBS e tarefas automaticamente.

## Comportamentos e regras importantes

- O importador é a principal ponte entre o planejamento feito no Primavera P6 e a execução no MRO.
- Task assignments com recursos de mão de obra (LABOR) são gerados automaticamente durante a importação.

## Dados envolvidos

Tabelas `public.mro_projects`, `public.mro_wbs`, `public.mro_tasks`, `public.mro_task_resources`.
