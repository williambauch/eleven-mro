# Auditoria de Teste — Task 26331 (WB-ROTINA-B)

> **Objetivo do teste:** Percorrer o fluxo completo de uma Rotina Padrao desde o release ate a passagem de servico, validar o comportamento do `btn_validar_prog_rotina`, e documentar o ponto onde o assignment fica travado (PENDING_HANDOVER sem assignment disponivel para continuidade).
>
> **Data do teste:** 2026-07-30
>
> **Responsavel:** William Bauch

---

## Contexto Inicial

### Task

| Campo | Valor |
|-------|-------|
| **task_id** | 26331 |
| **task_code** | WB-ROTINA-B |
| **task_name** | Teste B de Rotina |
| **status_code** | `NOT_STARTED` |
| **is_nrc** | `false` (Rotina Padrao) |
| **estimated_hours** | 0.00 |
| **project_id** | 10 |
| **project_name** | GOL LINHAS AEREAS S.A - B737-8EH - CHECK NC06 - PR-GGE - SJK |
| **created_by** | planejador |
| **created_at** | 2026-07-17 |

### Recursos P6 (mro_task_resources)

| Resource | Skill | Budgeted Hours |
|----------|-------|:--------------:|
| C4 | Compostos | 0.00 |
| M4 | Motores | 0.00 |
| B4 | Tec Boroscopio | 0.00 |
| S4 | Sistemas | 0.03 |
| MO | Producao | 0.02 |

### Assignments Existentes

**Nenhum.** A task nao possui assignments - serao criados no primeiro release via `btn_liberar_para_execucao`.

---

## Roteiro do Teste

### Passo 1 — Liberar para Execucao

- **Acao:** Clicar `btn_liberar_para_execucao` no `grid_public_mro_tasks`
- **Esperado:**
  - Task atualiza para `RELEASED`
  - Cria assignments `NOT_STARTED` para cada skill (C4, M4, B4, S4, MO)
- **Resultado:** ✅ **OK**
  - Task 26331 → `RELEASED`
  - 5 assignments criados: B4(0h), C4(0h), M4(0h), MO(0.02h), S4(0.03h)

### Passo 2 — Atribuir Mecanico

- **Acao:** Supervisor atribui um mecanico a um dos assignments via `form_public_mro_task_assignments_planned`
- **Esperado:** Assignment vai para `ASSIGNED`
- **Resultado:** ✅ **OK (após correcao)**
  - 1ª tentativa: Assignment 15735 (MO) atribuido a William, mas permaneceu `NOT_STARTED` e `supervisor_id` NULL (onBeforeUpdate so aceitava `PLANNED`)
  - **Correcao aplicada:** onBeforeUpdate passou a aceitar `NOT_STARTED` alem de `PLANNED`
  - 2ª tentativa: Assignment 15735 → **`ASSIGNED`**, supervisor_id=15 ✅
  - Split criou assignment 15737 (Mecanico Teste), tambem ajustado para `ASSIGNED` ✅

### Passo 3 — Iniciar Cronometro (Play)

- **Acao:** Mecanico da Play no assignment
- **Esperado:** Assignment → `IN_PROGRESS`, timesheet criado
- **Resultado:** ✅ **OK**
  - William (15735): Play → `IN_PROGRESS`, rodou ~2min, fez Pause/Repasse
  - Mecanico Teste (15737): Play → `IN_PROGRESS`, rodou ~2min, fez Pause/Repasse

### Passo 4 — Fim de Turno / Repasse

- **Acao:** Mecanico faz Pause > motivo=2 (Fim de Turno/Repasse)
- **Esperado:**
  - Assignment → `PENDING_HANDOVER`
  - Se for o unico ativo: task → `PENDING_PROG`
  - Se houver outros ativos: apenas o assignment encerra, task intacta
- **Resultado:** ✅ **OK**
  - William saiu primeiro: como Mec. Teste ainda estava `ASSIGNED` (nao `IN_PROGRESS`), William foi tratado como ultimo → task → `PENDING_PROG`
  - Mec. Teste saiu depois: task ja estava `PENDING_PROG`, UPDATE reaplicado sem efeito colateral
  - Ambos com `actual_qty_hours` = 0.03h cada

### Passo 5 — Programacao Valida Rotina

- **Acao:** Programador clica `btn_validar_prog_rotina`
- **Esperado:**
  - Task → `RELEASED`
  - Log `PROGRAMMING_OK` em `mro_nrc_approval_log`
  - Se houver saldo P6, cria novos assignments
  - Se nao houver saldo, pergunta se deseja continuar
- **Resultado:** ✅ **OK**
  - Task 26331 → `RELEASED` ✅
  - Log `log_id=71` — `PROGRAMMING_OK` (programador) ✅
  - Saldo P6 calculado: MO 0.02h orcado - 0.06h trabalhado = **gap negativo**, nao criou assignments (comportamento esperado)

### Passo 6 — Ponto Critico (Confirmado)

- **Resultado:** ⚠️ **Beco sem saida documentado**
  - Task esta `RELEASED` mas nao tem assignment disponivel na skill MO para continuar
  - Os assignments `PENDING_HANDOVER` sao registro historico (nao reabrem)
  - Assignments `NOT_STARTED` sao de outras skills (B4, C4, M4, S4)
  - Nao ha botao de split na aba Concluidas
  - **Conclusao:** O `btn_validar_prog_rotina` foi ajustado para calcular o saldo P6 e criar novos assignments automaticamente. Porem, se o orcamento P6 ja foi estourado (gap negativo), a task e liberada sem novos assignments — cabendo ao planejamento ajustar as horas no P6 e re-liberar.

---

## Log de Execucao

| # | Timestamp | Acao | Usuario | Detalhes |
|:-:|-----------|------|---------|----------|
| 1 | 2026-07-30 15:18 | Liberar Execucao | planejador | Task `NOT_STARTED` → `RELEASED`. 5 assignments criados |
| 2 | 2026-07-30 15:35 | Atribuir Mecanico | william | 15735 → `ASSIGNED`, supervisor=william |
| 3 | 2026-07-30 15:36 | Atribuir Mecanico | william | 15737 → `ASSIGNED`, supervisor=william |
| 4 | 2026-07-30 15:39 | Split | william | 15735 dividido: 0.02h → 2x 0.01h (15735 + 15737) |
| 5 | 2026-07-30 15:39 | Play | william | 15735 → `IN_PROGRESS`, timesheet 133 criado |
| 6 | 2026-07-30 15:41 | Pause/Repasse | william | 15735 → `PENDING_HANDOVER` (0.03h). Task → `PENDING_PROG` |
| 7 | 2026-07-30 15:45 | Play | mecanico | 15737 → `IN_PROGRESS`, timesheet 134 criado |
| 8 | 2026-07-30 15:48 | Pause/Repasse | mecanico | 15737 → `PENDING_HANDOVER` (0.03h) |
| 9 | 2026-07-30 15:49 | **Valida Rotina** | **programador** | **Task → `RELEASED`**. Log `PROGRAMMING_OK`. Gap MO: 0.02-0.06=-0.04, nenhum assignment criado |

---

## Observacoes

- Task 26331 tem `estimated_hours = 0.00` — o release usara as `budgeted_hours` dos recursos P6
- Recursos C4, M4, B4 com `budgeted_hours = 0.00` podem gerar assignments com 0h planejadas — avaliar se isso e um problema
- A skill do usuario william (ID 15) e `MO (Producao)` — ele conseguira ver apenas assignments dessa skill no painel

---

## Tabelas de Auditoria (Consultas Padrao)

Sempre verificar estas tabelas ao auditar o fluxo de uma task:

| Tabela | O que verificar | Query Padrao |
|--------|----------------|--------------|
| `mro_tasks` | Status atual da task, `is_nrc`, `estimated_hours`, `project_id` | `SELECT * FROM mro_tasks WHERE task_id = {ID}` |
| `mro_task_assignments` | Todos os assignments da task, status, employee, horas plan/real, supervisor | `SELECT * FROM mro_task_assignments WHERE task_id = {ID} ORDER BY assignment_id` |
| `mro_timesheet` | Sessoes de apontamento (start/end, duration, status) | `SELECT * FROM mro_timesheet WHERE assignment_id IN (subquery) ORDER BY start_time` |
| `mro_assignment_events` | Eventos de auditoria do assignment (START, PAUSED, ASSIGNMENT, SPLIT, etc.) | `SELECT * FROM mro_assignment_events WHERE assignment_id = {ID} ORDER BY event_id` |
| `mro_nrc_approval_log` | Log de aprovacoes (PROGRAMMING_OK, SENT_TO_PROG, OA_REVISION, etc.) | `SELECT * FROM mro_nrc_approval_log WHERE task_id = {ID} ORDER BY log_id` |
| `mro_task_resources` | Recursos P6 vinculados a task (budgeted_hours por skill) | `SELECT * FROM mro_task_resources WHERE task_id = {ID}` |
| `mro_employees` | Dados do funcionario (skill_id, full_name) | `SELECT * FROM mro_employees WHERE employee_id = {ID}` |
| `mro_skills` | Nome e codigo da especialidade | `SELECT * FROM mro_skills WHERE skill_id = {ID}` |
| `mro_projects` | Dados do projeto/contrato (regras de cap) | `SELECT * FROM mro_projects WHERE project_id = {ID}` |
