# Histórico de Empréstimos (grid_public_mro_tool_movements)

Módulo Logística e Ferramentaria — aplicação do tipo Grid.

Tela de consulta do histórico de movimentações de ferramentas. Registra cada operação de check-out e check-in realizada no terminal de ferramentaria, permitindo rastrear por ferramenta, mecânico, tarefa e condição de retorno.

## O que o usuário pode fazer

- Consultar o histórico completo de empréstimos de ferramentas.
- Visualizar dados como ferramenta, mecânico, tarefa, horários de retirada e devolução, e condição de retorno.
- Rastrear uma ferramenta específica ao longo do tempo.
- Filtrar movimentações por período, ferramenta ou mecânico.

## Comportamentos e regras importantes

- Cada movimentação registra tool_id, resource_id (mecânico), task_id, checkin/checkout times e return_condition.
- A grid é essencialmente uma consulta à tabela `public.mro_tool_movements`, que é alimentada automaticamente pelo terminal de ferramentaria.

## Dados envolvidos

Tabela `public.mro_tool_movements`. Relacionada com `mro_tools`, `mro_employees` e `mro_tasks`.
