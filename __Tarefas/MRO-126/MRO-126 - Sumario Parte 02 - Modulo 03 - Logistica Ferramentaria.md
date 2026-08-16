# Revisão Chão de Fábrica & Backoffice — Parte 02 — Módulo 03 (Logística & Ferramentaria)

> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice, separado por módulos de trabalho.
>
> - [Módulo 01 — Backoffice (Gantt e Kanban)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2001%20-%20Backoffice.md)
> - [Módulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2002%20-%20Operacoes.md)
> - **Módulo 03 — Logística & Ferramentaria (Almoxarifado)** (este arquivo)

---

## Tarefa do Modulo 03 — Logística & Ferramentaria (Almoxarifado)

Provedoria — Tela de Monitoramento de Liberação (Gated Process):

Regra: Grid dedicada que lista apenas as JICs/tarefas que estejam com 100% dos materiais necessários liberados no estoque físico, permitindo ao planejador liberar as tarefas de forma segura para o hangar.

Provedoria — Fluxo de Recolhimento de Material (Bip de Saída):

Regra: No balcão da provedoria, o almoxarife deve realizar a baixa física das peças via leitura do código de barras, atrelando logicamente o lote do componente entregue ao código da JIC correspondente e ao ID do mecânico que realizou a retirada (Rastreabilidade As-Built).

Ferramentaria — Check-in Padrão (Bip de Retorno):

Regra: No ato da devolução da ferramenta no balcão, o atendente bipa o código do ativo e o sistema deve exigir obrigatoriamente a demarcação de seu estado físico de retorno (OK ou Com Avaria). Caso seja marcado como avariado, o sistema altera o status do ativo no banco para indisponível.

No painel do mecânico, criar listagem de ferramentas com avaria pra o ele preencher os formulários de avarias.

---

## Sumario das alteracoes implementadas

### Provedoria — Monitoramento de Liberação (Gated Process) ✅

**Nova app:** `Almoxarifado/grid_mro_material_release/` (Grid)

- **Regra:** lista apenas as JICs/tarefas (`PLANNED`/`NOT_STARTED`) que estejam com **100% dos materiais bloqueantes** (`is_blocking_task`) disponíveis no estoque físico (`stock_balance >= planned_qty`), permitindo ao planejador liberar as tarefas de forma segura para o hangar.

**Arquivos:**
- `sql/schema.sql` — SQL agregado com JOIN `mro_tasks` + `mro_projects` + `mro_task_materials` + `mro_materials`, colunas de `total_materiais`, `materiais_ok` e `pct_disponivel`; `HAVING` garante que só aparecem JICs com 100% de disponibilidade
- `config.json` — declaração da grid (campos, tabelas, variavel `var_project_id`)
- `events/03_onScriptInit/onScriptInit.scriptcase` — filtro opcional por projeto (`var_project_id`) usando `sc_select_where(add)`
- `events/04_onRecord/onRecord.scriptcase` — badge visual de percentual (verde 100% / amarelo parcial / vermelho 0%) e indicador "X de Y" materiais
- `button/btn_liberar_hangar/onRecord.scriptcase` — botão por linha "Liberar para Hangar":
  - **Double-check no clique**: revalida no momento que não há materiais faltantes (trava de segurança)
  - Trava de status (só `PLANNED`/`NOT_STARTED`)
  - `UPDATE mro_tasks SET status_code='RELEASED', is_blocked_material=false`
  - Audit log em `mro_task_history` (`RELEASED_BY_PROVEDORIA` com remarks)
  - `sc_commit_trans()` + `sc_alert` + `sc_redir`

**Menu:** `item_50: Logística e Ferramentaria > Provedoria - Liberação de Materiais (grid_mro_material_release)` no `Security/sec_menu/menu_tree.md`

**Observação de dados:** no estado atual do banco todos os 509 materiais estão com `stock_balance = 0.00`, portanto a grid nasce vazia (nenhuma JIC pronta) — comportamento correto da regra; quando o estoque físico for carregado, as JICs aptas passam a aparecer automaticamente.

---

## Pendências do Modulo 03
- [x] Provedoria: monitoramento de liberação (Gated Process)
- [ ] Provedoria: bip de saída com lote (Rastreabilidade As-Built)
- [ ] Ferramentaria: check-in com condição de retorno (OK/Avaria) + bloqueio por calibração vencida
- [ ] Painel do mecânico: listagem de ferramentas com avaria

---

## Critérios de Aceite (UAT) do Modulo 03
[ ] A ferramenta de ferramentaria exige a condição de retorno no check-in e bloqueia empréstimos se estiver com calibração vencida
[ ] A grid da provedoria lista apenas JICs com 100% dos materiais bloqueantes disponíveis no estoque físico
