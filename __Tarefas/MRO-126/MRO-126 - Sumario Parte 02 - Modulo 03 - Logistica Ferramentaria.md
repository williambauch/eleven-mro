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

**Nova app:** `Almoxarifado/grid_provedoria_release/` (Grid)

- **Abordagem:** duplicata da `grid_public_mro_tasks` na IDE (herda campos, botões, onRecord, filtros e layout) — apenas o SQL foi alterado para o Gated Process.
- **Regra:** lista apenas as JICs/tarefas (`PLANNED`/`NOT_STARTED`) que estejam com **100% dos materiais bloqueantes** (`is_blocking_task`) disponíveis no estoque físico (`stock_balance >= planned_qty`), permitindo ao planejador liberar as tarefas de forma segura para o hangar.

**SQL (`sql/schema.sql`):**
- `SELECT` principal com **alias em todos os campos expostos** (`t.task_id AS task_id`, ...)
- Filtro Gated Process via subquery:
  ```sql
  WHERE t.task_id IN (
      SELECT tm.task_id
      FROM mro_task_materials tm
      JOIN mro_materials m ON tm.material_id = m.material_id
      WHERE tm.is_applied IS NOT TRUE
        AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
      GROUP BY tm.task_id
      HAVING COUNT(*) FILTER (WHERE m.stock_balance < tm.planned_qty) = 0
  )
    AND t.status_code IN ('PLANNED', 'NOT_STARTED')
  ORDER BY t.task_code
  ```

**Botão:** herda o `btn_liberar_para_execucao` da duplicata (RUN multi-seleção + `onFinish` com `[glo_aviso_run]`), que já chama `fn_liberar_task_para_execucao(..., 'PLANEJADOR')` — na duplicata pode ser ajustado para origem `'PROVEDORIA'` no audit log.

**Refatoração — código único na biblioteca interna (`_Bibliotecas_Internas/mro_engine.php`):**
- Nova função **`fn_liberar_task_para_execucao($task_id, $projeto, $status, $is_blocked_pred, $origem)`** centraliza a liberação completa:
  1. Valida bloqueio por predecessora
  2. Critica de status (`PLANNING`/`NOT_STARTED`/`PLANNED`/`APPROVED`)
  3. **Valida skill/mão de obra (Gated Process)**: a task só libera se tiver ao menos um recurso LABOR com match em `mro_skills` OU `skill_code` existente em `mro_skills` — bloqueia com mensagem contendo **task_id + task_code**: *"Liberação bloqueada: a tarefa ID X (CODE) não possui skill/recursos de mão de obra definidos. Atribua a skill antes de liberar."*
  4. `UPDATE mro_tasks SET status_code='RELEASED'`
  5. Audit log em `mro_task_history` — `RELEASED` (origem `PLANEJADOR`) ou `RELEASED_BY_PROVEDORIA` (origem `PROVEDORIA`)
  6. Cria os assignments por skill via `fn_criar_assignments_por_skill` (LABOR do P6, com proteção de duplicidade)
- **`btn_liberar_para_execucao`** (grid_public_mro_tasks) refatorado → agora é *thin wrapper* que chama `fn_liberar_task_para_execucao(..., 'PLANEJADOR')`
- Benefício: mesma lógica em um único lugar — a duplicata da provedoria reutiliza a mesma função

**Motivação da validação de skill:** auditoria revelou que 5.215 tasks `NOT_STARTED`/`PLANNED` não têm LABOR nem `skill_code` — liberar sem a validação criaria tasks `RELEASED` sem slots de trabalho (fantasmas). Validado no banco: task 16635 (MI220) → bloqueada; task 28824 (NWB-ROTINA-C003) → libera.

**Front-end — checkbox RUN habilitado só para status liberáveis:**
- `events/04_onRecord/onRecord.scriptcase` — o span do `btn_predecessor` agora inclui `data-status='{status_code}'` (campo da linha, com chaves — não `[...]` que é global)
- `events/03_onScriptInit/onScriptInit.scriptcase` — a função `esconderCheckboxRun()` agora desabilita o checkbox RUN (esmaecido) quando o status **não** está em `PLANNING`/`NOT_STARTED`/`PLANNED`/`APPROVED` **ou** quando bloqueada por predecessora

**Menu:** `item_50: Logística e Ferramentaria > Provedoria - Liberação de Materiais (grid_provedoria_release)` no `Security/sec_menu/menu_tree.md`

**Observação de dados:** no estado atual do banco todos os 509 materiais estão com `stock_balance = 0.00`, portanto a grid nasce vazia (nenhuma JIC pronta) — comportamento correto da regra; quando o estoque físico for carregado, as JICs aptas passam a aparecer automaticamente.

**Nota:** a grid `grid_mro_material_release` (criada manualmente numa primeira abordagem) foi **removida** — substituída pela duplicata `grid_provedoria_release`.

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
