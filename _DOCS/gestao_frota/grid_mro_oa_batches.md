# Lotes de O&A (grid_mro_oa_batches)

Módulo Gestão de Frota e Projetos — aplicação do tipo Grid.

Tela de acompanhamento dos lotes de Over & Above (O&A) enviados para aprovação do cliente. Cada lote agrupa um conjunto de Não-Rotinas (NRCs) de um determinado projeto, com os totais de horas e materiais envolvidos.

## O que o usuário pode fazer

- Visualizar todos os lotes de O&A cadastrados, ordenados do mais recente para o mais antigo.
- Consultar o status de cada lote (pendente, aprovado, rejeitado), datas de submissão e aprovação, e observações.
- Ver os totais consolidados de horas e custos de materiais do lote.
- Acessar os detalhes de cada lote para visualizar as NRCs que o compõem.

## Comportamentos e regras importantes

- Os totais de horas e materiais de cada lote são calculados automaticamente a partir das tarefas (NRCs) associadas ao lote na tabela `mro_tasks`. O cálculo utiliza subconsultas SQL que somam `oa_hours` e `oa_material_cost` para cada `batch_id`.
- O lote só pode ser submetido à aprovação quando todas as NRCs associadas estiverem com seus custos definidos.
- O fluxo de aprovação pode ser automático (dentro do teto contratual) ou manual (quando excede o CAP).

## Dados envolvidos

Tabelas `public.mro_oa_batches` e `public.mro_tasks` (para cálculo dos totais).
