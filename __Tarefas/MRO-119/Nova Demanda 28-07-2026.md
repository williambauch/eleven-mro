# Rodrigo Souza - Nova Demanda 28/07/2026
## Resumo das implementações — Bloqueio por Tarefa Predecessora

---

## Banco de Dados — Migrations

### `migrations/MRO-119_03_add_is_blocked_predecessor.sql`
- Adiciona coluna `is_blocked_predecessor` (boolean, NOT NULL, DEFAULT false) em `mro_tasks`
- Cria índices `idx_task_dep_successor` e `idx_task_dep_predecessor` em `mro_task_dependencies`

### `migrations/MRO-119_04_trigger_blocked_predecessor.sql`
- Trigger function `fn_update_blocked_predecessor()` em PL/pgSQL
- **Trigger `trg_task_dependencies_blocked`:** AFTER INSERT, UPDATE ou DELETE em `mro_task_dependencies` — recalcula flag da sucessora
- **Trigger `trg_tasks_status_blocked`:** AFTER UPDATE OF `status_code` em `mro_tasks` — ao concluir/cancelar, recalcula flag de todas as sucessoras

### `migrations/MRO-119_05_sync_blocked_predecessor.sql`
- Sincronização inicial: UPDATE em lote para calcular flag dos registros existentes (616 tasks bloqueadas)

### `migrations/MRO-119_06_add_dependencies_apps_sec.sql`
- Registra `form_public_mro_task_dependencies_predecessoras` e `form_public_mro_task_dependencies_sucessoras` na segurança do sistema

---

## `form_public_mro_tasks` — Formulário principal de edição de tarefas

### Label de bloqueio no onLoad
- `{is_blocked_predecessor_label}` renderiza "Com bloqueio" ou "Sem bloqueio" conforme o valor booleano

### Aba "Dependências"
- Nova aba com duas sub-aplicações (detalhes) vinculadas por `[glo_task_id]`:
  - `form_public_mro_task_dependencies_predecessoras` — tarefas que bloqueiam a task atual
  - `form_public_mro_task_dependencies_sucessoras` — tarefas que a task atual bloqueia

---

## `grid_public_mro_tasks` — Grid principal de tarefas

### Campos adicionados ao filtro refinado e avançado
| Campo | Label |
|---|---|
| `is_blocked_predecessor` | Blocked Predecessor |
| `is_critical_path` | Is Critical Path |
| `is_rii` | Is Rii |
| `requires_rii` | Requires Rii |
| `is_blocked_tool` | Blocked Tool |
| `is_blocked_labor` | Blocked Labor |
| `is_blocked_material` | Blocked Material |

### Ícone de bloqueio no onRecord (`events/onRecord`)
- `btn_predecessor` com `fa-ban`: vermelho se bloqueado, cinza se livre
- Atributo `data-blocked` usado pelo JS para identificar linhas bloqueadas

### Validação no btn_liberar_para_execucao (`button/btn_liberar_para_execucao`)
- Se `is_blocked_predecessor = true`, exibe erro e impede liberação

### Dupla proteção no checkbox RUN (`events/onScriptInit`)
1. **EscondeCheckboxRun:** percorre spans `id_sc_field_btn_predecessor_N` e desabilita (`disabled = true`) o checkbox da linha se bloqueada
2. **nm_marca_check_grid sobrescrita:** "Selecionar Todos" marca apenas checkboxes não desabilitados

---

## `form_public_mro_task_dependencies_predecessoras` (NOVA)

**Vinculada como detalhe do `form_public_mro_tasks`.**
- Filtro: `successor_task_id = [glo_task_id]` (task atual = sucessora)
- CRUD direto na tabela `mro_task_dependencies`

### Eventos
- **`events/onValidate`:** impede auto-referência e duplicidade
- **`events/onAfterInsert`:** consulta `is_blocked_predecessor` no banco e atualiza label no form pai via `sc_master_value`
- **`events/onAfterUpdate`:** mesmo que onAfterInsert
- **`events/onBeforeDelete`:** salva `successor_task_id` em `[glo_succ_id_deleted]`
- **`events/onAfterDelete`:** lê a global e atualiza label no form pai via `sc_master_value`

---

## `form_public_mro_task_dependencies_sucessoras` (NOVA)

**Vinculada como detalhe do `form_public_mro_tasks`.**
- Filtro: `predecessor_task_id = [glo_task_id]` (task atual = predecessora)
- CRUD direto na tabela `mro_task_dependencies`

### Eventos
- **`events/onValidate`:** mesmas validações (auto-referência e duplicidade)
- **Demais eventos vazios:** não atualiza o mestre, pois quem muda o bloqueio é a task sucessora, não a atual

---

## Testes de Validação

| Teste | Operação | Resultado |
|:---:|---|:---:|
| A | INSERT dependência com pred não concluída | ✅ Bloqueio automático |
| B | UPDATE status_code da pred para COMPLETED | ✅ Liberação automática |
| C | DELETE dependência | ✅ Recalculo correto |
| D | Múltiplas preds — concluir apenas uma | ✅ Continua bloqueada (parcial) |
| E | Clicar "Liberar Execução" em task bloqueada | ✅ Bloqueado com erro |

### Métricas (28/07/2026)
| Item | Valor |
|:---|---:|
| Tasks com `is_blocked_predecessor = true` | 616 |
| Tasks com `is_blocked_predecessor = false` | 15.660 |
| Total de tasks | 16.276 |
