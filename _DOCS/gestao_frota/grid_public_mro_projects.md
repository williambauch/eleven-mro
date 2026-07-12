# Projetos / Checks (grid_public_mro_projects)

Módulo Gestão de Frota e Projetos — aplicação do tipo Grid.

Tela de consulta e gerenciamento dos projetos de manutenção (checks). Cada projeto está vinculado a uma aeronave e possui datas de início e fim, status e um identificador opcional de integração com o Primavera P6.

## O que o usuário pode fazer

- Visualizar todos os projetos de manutenção cadastrados.
- Consultar dados como aeronave associada, período, status e ID de integração P6.
- Acessar o formulário de edição de cada projeto.
- Filtrar projetos por status, aeronave ou período.

## Comportamentos e regras importantes

- O campo `p6_proj_id` armazena o identificador do projeto no Primavera P6, usado na integração entre os sistemas.
- O status do projeto (`project_status`) reflete a etapa atual do ciclo de manutenção.
- A grid pode ser usada como ponto de partida para acessar a EAP (WBS) e as tarefas do projeto.

## Dados envolvidos

Tabela `public.mro_projects`. Relacionada com `public.mro_aircraft` pelo campo `aircraft_id`.
