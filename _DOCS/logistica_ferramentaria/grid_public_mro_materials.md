# Almoxarifado / Peças (grid_public_mro_materials)

Módulo Logística e Ferramentaria — aplicação do tipo Grid.

Tela de cadastro e consulta de materiais e peças de reposição utilizadas na manutenção. Cada material possui part number, descrição, unidade de medida e indicador se é consumível.

## O que o usuário pode fazer

- Visualizar o catálogo de materiais disponíveis.
- Consultar part number, descrição e unidade de medida.
- Identificar materiais consumíveis versus permanentes.
- Acessar o cadastro de cada material para edição.

## Comportamentos e regras importantes

- O campo `is_consumable` diferencia peças de consumo (parafusos, vedantes, etc.) de itens permanentes.
- A integração futura com o ERP Protheus permitirá consultar saldos em tempo real e criar ordens de compra automaticamente.

## Dados envolvidos

Tabela `public.mro_materials`.
