# Detalhes de O&A (grid_mro_tasks_oa_details)

Módulo Produção e Manutenção — aplicação do tipo Grid.

Grade de detalhamento das NRCs que compõem um lote de Over & Above (O&A). Exibe cada tarefa do lote com seus custos de horas e materiais.

## O que o usuário pode fazer

- Visualizar as NRCs agrupadas em um lote de O&A.
- Consultar horas e custos de materiais de cada NRC.
- Acompanhar o status de aprovação individual.

## Dados envolvidos

Tabela `public.mro_tasks` filtrada por `oa_batch_id`.
