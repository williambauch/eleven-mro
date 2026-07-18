# Almoxarifado / Peças (grid_public_mro_materials)

Módulo Logística e Ferramentaria — aplicação do tipo Grid.

Tela de cadastro e consulta de materiais e peças de reposição utilizadas na manutenção. Cada material possui part number, descrição, unidade de medida, indicador se é consumível e flag de bloqueio operacional.

## O que o usuário pode fazer

- Visualizar o catálogo de materiais disponíveis.
- Consultar part number, descrição, unidade de medida, product_code, stock_location e stock_balance.
- Identificar materiais consumíveis versus permanentes.
- Visualizar e editar a flag `is_blocking_task` diretamente na grade.
- Acessar o cadastro de cada material para edição completa.

## Comportamentos e regras importantes

- A flag `is_blocking_task` (Material Bloqueante?) define se a falta do item bloqueia a execução da tarefa.
- A coluna `stock_balance` exibe o saldo atual em estoque.
- `product_code` e `stock_location` complementam a identificação única do material (a constraint única considera os três campos: product_code + part_number + stock_location).
- O campo `is_consumable` diferencia peças de consumo (parafusos, vedantes, etc.) de itens permanentes.
- A integração futura com o ERP Protheus permitirá consultar saldos em tempo real e criar ordens de compra automaticamente.

## Dados envolvidos

Tabela `public.mro_materials`.
