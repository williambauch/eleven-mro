# Ajuste do motor O&A — Criação de assignments no auto-approve (MRO-122)

**Arquivo alterado:** `_Bibliotecas_Internas/mro_engine.php`
**Data:** 2026-08-11
**Contexto:** MRO-122 — Gaps ① e ② do diagrama de fluxo (motor O&A liberava NRC sem criar assignments)

---

## Bug encontrado

Quando o motor O&A (`fn_calcular_oa_nrc`) fazia **auto-approve** (NRC dentro do CAP do contrato), a task ia direto para `RELEASED` **sem criar assignments** de mão de obra:

1. **Gap ①** — NRC em `PENDING_OA` e o custo **baixou** (revisão automática) → motor aprovava sozinho → `RELEASED` sem assignments.
2. **Gap ②** — NRC em `PENDING_PROG` validada pelo `btn_validar_prog` dentro do CAP → `RELEASED` sem assignments.

**Impacto:** a NRC ficava em `RELEASED` sem equipe alocada, e ninguém conseguia criar os slots depois (o `btn_liberar_para_execucao` só processa `PLANNING/NOT_STARTED/PLANNED/APPROVED`, **não** `RELEASED`).

**Decisão do superior:** o próprio motor O&A deve criar os assignments antes de mudar para `RELEASED`.

---

## Solução implementada

### 1. Nova função `fn_criar_assignments_por_skill($v_task_id, $v_projeto_atual)`

Replica a lógica do `btn_liberar_para_execucao`:

- Busca os recursos **LABOR** de `mro_task_resources` (JOIN `mro_resources` com `resource_type = 'LABOR'`).
- Para cada skill, busca o `skill_id` em `mro_skills` e insere em `mro_task_assignments` com `status_code = 'NOT_STARTED'`.
- **Proteção contra duplicidade:** verifica se já existe assignment para `task_id + skill_id` antes de inserir.
- **Fallback de horas:** usa o `budgeted_hours` do recurso ou o `estimated_hours` da task quando o budgeted for vazio/zero.
- **Fallback geral:** sem recursos LABOR, cria alocação genérica com o `skill_code` da própria task.

### 2. Chamada no auto-approve

Antes do UPDATE final (no branch "dentro do CAP"):

```php
// MRO-122: Ao liberar automaticamente (dentro do CAP), cria os assignments
if ($novo_status == 'RELEASED') {
    fn_criar_assignments_por_skill($v_task_id, $v_projeto_atual);
}
```

Cobre os dois pontos de auto-approve (`PENDING_PROG` e `PENDING_OA`).

### 3. Correção fina do fallback de horas (0.00)

O Postgres retorna `budgeted_hours` como string (`"0.00"`), e `!empty("0.00")` é `true` — a skill com budgeted zerado recebia `0.00` em vez do fallback. Ajustado para:

```php
// ANTES:
$v_hours = !empty($res[1]) ? (float)$res[1] : $var_horas_task;

// DEPOIS (trata "0"/"0.00" como vazio):
$v_hours = (!empty($res[1]) && (float)$res[1] > 0) ? (float)$res[1] : $var_horas_task;
```

---

## Teste de validação (task 28824 `NWB-ROTINA-C003`)

Cenário: NRC em `PENDING_PROG`, projeto 2 com `cap_hh_limit = 100`, recursos LABOR: A4 (0.02h), E4 (4.44h), MO (0.00h).

| Item | Resultado |
|---|---|
| Status | `PENDING_PROG` → **`RELEASED`** (auto-approve) ✅ |
| Log | `AUTO_APPROVE` (mro_engine) + `PROGRAMMING_OK` registrados ✅ |
| Assignments criados | **3** (A4, E4, MO) — antes eram 0 ✅ |
| Horas | A4 = 0.02, E4 = 4.44 ✅ |
| MO (budgeted 0.00) | **0.00** antes da correção fina; **passa a usar estimated_hours (4.46)** após a correção ✅ |
| `planned_skill_id` | = `skill_id` em todos ✅ |
| `executed_by_employee_id` | NULL (supervisor designa depois) ✅ |

---

## Fluxo resultante

```
PENDING_PROG / PENDING_OA -- motor O&A (dentro do CAP) --> RELEASED + assignments por skill
```

A NRC aprovada automaticamente **nunca mais** chega a RELEASED sem equipe alocada.

---

## Observações

- O `btn_aprovar_cliente` (fluxo manual) continua **sem** criar assignments — `APPROVED` é apenas a aprovação do orçamento; a liberação efetiva fica com o `btn_liberar_para_execucao` (que já cria os slots). Decisão de design: evitar reserva prematura de mão de obra.
- A cópia antiga `Timesheet/ctrl_abertura_nrc/methods/fn_calcular_oa_nrc.php` **não foi alterada** — a versão oficial é a da biblioteca interna.
- Ao subir para o ScriptCase, copiar o `mro_engine.php` completo (base idêntica à versão anterior + a nova função e as chamadas).
