# Filtros da grid_public_mro_tasks — Organização por grupo

Referência: MRO-122 — Item 2 (Revisão de grids com novos filtros)
Tela unificada de tarefas e Não Rotinas (NRC).

---

## STATUS

| Campo (banco) | Campo (tela) | Operador | Grupo de filtro |
|---|---|---|---|
| `task_id` | Task Id | Igual a | Avançado |
| `task_code` | Task Code | Igual a | Avançado |
| `task_name` | Task Name | Contém | Avançado |
| `wbs_id` | Wbs Id | Igual a | Avançado |
| `status_code` | Status Code | Contém | Refinado |
| `pending_id` | Pending Status | Selecione | Refinado |
| `phase_code` | Phase Code | Contém | Refinado |
| `baseline_phase_code` | Baseline Phase Code | Contém | Refinado |
| `deferment_status` | Deferment Status | Selecione | Refinado |
| `is_blocked_material` | Blocked Material | Selecione | Refinado |
| `is_blocked_tool` | Blocked Tool | Selecione | Refinado |
| `is_blocked_labor` | Blocked Labor | Selecione | Refinado |
| `is_blocked_predecessor` | Blocked Predecessor | Selecione | Refinado |
| `is_predecessor_manual` | Predecessor Manual | Selecione | Refinado |

## CRITICIDADE

| Campo (banco) | Campo (tela) | Operador | Grupo de filtro |
|---|---|---|---|
| `is_critical_path` | Critical Path | Selecione | Refinado |
| `is_rii` | Is Rii | Selecione | Refinado |
| `requires_rii` | Requires Rii | Selecione | Refinado |
| `is_oa` | Is Oa | Selecione | Refinado |

## ORIGEM

| Campo (banco) | Campo (tela) | Operador | Grupo de filtro |
|---|---|---|---|
| `project_id` | Project Id | Igual a | Avançado |
| `origin_document` | Origin Document | Contém | Avançado |
| `parent_task_id` | Parent Task Id | Igual a | Avançado |
| `root_task_id` | Root Task Id | Igual a | Avançado |
| `jic_number` | Jic Number | Contém | Avançado |
| `created_by` | Created By | Igual a | Avançado |

---

## Notas

- `nrc_status` **não entra** em nenhum grupo: campo legado órfão (só gravado como 'DRAFT'), o workflow real roda no `status_code`.
- `is_milestone` **não entra**: não é status, criticidade nem origem — é tipo de item de cronograma (marco).
- Filtro Refinado = opções simples (Sim/Não/Lista); Filtro Avançado = muitas variações de opção (IDs, textos).
