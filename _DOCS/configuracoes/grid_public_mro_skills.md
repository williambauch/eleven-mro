# Especialidades / Skills (grid_public_mro_skills)

Módulo Configurações / Tabelas Básicas — aplicação do tipo Grid.

Tela de cadastro das especialidades técnicas (skills) utilizadas no sistema. Cada skill possui um código, descrição e o tipo de licença ANAC associada. Essas especialidades são usadas para qualificar mecânicos e definir a alocação de tarefas.

## O que o usuário pode fazer

- Visualizar todas as especialidades cadastradas.
- Consultar código, descrição e tipo de licença ANAC.
- Acessar o cadastro de cada skill para edição.

## Comportamentos e regras importantes

- O código da skill (ex: MEC, AVI, ELT) é usado como referência em todo o sistema, desde a importação do P6 até a alocação de tarefas.
- O tipo de licença ANAC (`anac_license_type`) é usado pela matriz de qualificação para validar se o mecânico pode executar uma determinada tarefa.

## Dados envolvidos

Tabela `public.mro_skills`.
