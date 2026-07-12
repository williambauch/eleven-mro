# Aeronaves (grid_public_mro_aircraft)

Módulo Gestão de Frota e Projetos — aplicação do tipo Grid.

Tela de consulta e gerenciamento das aeronaves cadastradas no sistema. Cada aeronave é identificada por sua matrícula (registration), modelo, número de série (MSN) e cliente proprietário.

## O que o usuário pode fazer

- Visualizar a frota completa de aeronaves em manutenção.
- Consultar dados como matrícula, modelo, MSN e cliente.
- Acessar o cadastro de cada aeronave para editar informações.
- Filtrar e ordenar a lista por qualquer coluna.

## Comportamentos e regras importantes

- A grid exibe os dados da tabela `public.mro_aircraft` em ordem de cadastro.
- Cada aeronave pode estar associada a um ou mais projetos de manutenção ao longo do tempo.

## Dados envolvidos

Tabela `public.mro_aircraft`.
