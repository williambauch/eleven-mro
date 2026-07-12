# Gestão de Não Rotinas — NRC (grid_public_mro_nrc)

Módulo Produção e Manutenção — aplicação do tipo Grid.

Tela de acompanhamento das Não-Rotinas (NRCs) — tarefas não planejadas que surgem durante a execução da manutenção. Exibe todas as NRCs abertas, seus status, bloqueios e referências técnicas.

## O que o usuário pode fazer

- Visualizar todas as não-rotinas do sistema, identificadas por um ícone de alerta.
- Acompanhar o status de cada NRC através de badges coloridos: Rascunho, Engenharia, Programação, Cliente, Em Revisão, Liberado, Cancelado ou Concluído.
- Identificar bloqueios de material, ferramenta ou mão de obra através de ícones coloridos.
- Alternar o status de bloqueio (material, ferramenta, mão de obra) clicando nos ícones.
- Filtrar as NRCs por projeto, status ou outros critérios.

## Comportamentos e regras importantes

- A grid exibe apenas tarefas com `is_nrc = true`, filtrando exclusivamente as não-rotinas.
- **Badges de status**: Cada status possui um badge colorido com ícone específico:
  - DRAFT (cinza): rascunho criado pelo mecânico.
  - PENDING_ENG (azul): aguardando análise da engenharia.
  - PENDING_PROG (amarelo): aguardando programação de recursos.
  - PENDING_OA (amarelo): aguardando aprovação do cliente (Over & Above).
  - COMMERCIAL_REVIEW (amarelo): em revisão comercial.
  - RELEASED (verde): liberado para execução.
  - CANCELLED (vermelho): cancelado.
  - COMPLETED (preto): concluído e inspecionado.
- **Filtro por perfil**: Se o usuário logado tem apenas o perfil "MECANICO", a grid filtra automaticamente para exibir apenas as NRCs criadas por ele.
- Os bloqueios funcionam da mesma forma que na grid de tarefas: clicando no ícone, o status é alternado no banco via Ajax.

## Dados envolvidos

Tabela `public.mro_tasks` com filtro `is_nrc = true`.
