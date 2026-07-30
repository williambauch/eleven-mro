# MRO-120 — Plano de Implementacao

> **Tarefa:** Ajustar regras de estouro de Man-Hours (HH), alterar rota da passagem de serviço e criar consulta consolidada de apontamentos.
>
> **Data:** 2026-07-29 (Revisado apos reimportacao do projeto)
>
> **Status:** Pendente — Nenhuma frente iniciada

---

## Indice

1. [Visao Geral](#visao-geral)
2. [Analise do Estado Atual](#analise-do-estado-atual)
3. [Frente 1 — Gestao de Estouro de HH](#frente-1--gestao-de-estouro-de-hh)
4. [Frente 2 — Passagem de Servico](#frente-2--passagem-de-servico)
5. [Frente 3 — Consulta de Apontamentos](#frente-3--consulta-de-apontamentos)
6. [Frente 4 — Multi-atribuicao (Painel do Supervisor)](#frente-4--multi-atribuicao-painel-do-supervisor)
7. [Ordem de Execucao](#ordem-de-execucao)
8. [Riscos e Pontos de Atencao](#riscos-e-pontos-de-atencao)

---

## Visao Geral

A tarefa MRO-120 abrange 4 frentes independentes que visam ajustar o fluxo de apontamento de horas, passagem de serviço e atribuicao de responsaveis no chao de fabrica.

| # | Frente | Objetivo | Complexidade | Status |
|---|--------|----------|:---:|:---:|
| 1 | **Estouro de HH** | Alertar visualmente quando horas executadas excederem estimadas, sem travar o apontamento | Media | Pendente |
| 2 | **Passagem de Servico** | Redirecionar rota de passagem de servico para Programacao (nao Engenharia) e nao gerar pendencia de MO | Media | Pendente |
| 3 | **Consulta de Apontamentos** | Nova grid consolidada de apontamentos com filtros obrigatorios por Projeto, JIC ou Usuario | Alta | Pendente |
| 4 | **Multi-atribuicao (Supervisor)** | Permitir 1:N (mais de 1 funcionario por tarefa). Split ja implementado via `control_split_assignment` (MRO-119). Pendente apenas ajuste visual no painel do supervisor. | Media | Parcial |

### Apps Impactadas

| App | Pasta | Tipo de Impacto |
|-----|-------|-----------------|
| `grid_public_mro_tasks` | `tasks/grid_public_mro_tasks/` | EDITADO — Frente 1: indicador visual de estouro de HH no onRecord |
| `form_public_mro_tasks` | `tasks/form_public_mro_tasks/` | EDITADO — Frente 1: alerta visual no form |
| `form_public_mro_timesheet` | `Timesheet/form_public_mro_timesheet/` | VERIFICAR — Frente 1: eventos vazios, sem trava identificada |
| `control_pause_task` | `Timesheet/control_pause_task/` | EDITADO — Frente 2: alterar fluxo de passagem de servico (PENDING_HANDOVER) |
| `grid_mro_timesheet_consolidado` (NOVA) | `Timesheet/grid_mro_timesheet_consolidado/` | CRIAR — Frente 3: consulta de apontamentos |
| `ROOT/tabs_supervisor` | `ROOT/tabs_supervisor/` | VERIFICAR — config serializada. Frente 4: ajustar queries das 4 sub-apps, nao o tabs |
| `form_public_mro_task_assignments_planned` | `tasks/form_public_mro_task_assignments_planned/` | EDITADO — Frente 4: ampliar query para N registros |
| `form_public_mro_task_assignments_progress` | `tasks/form_public_mro_task_assignments_progress/` | EDITADO — Frente 4: ampliar query para N registros |
| `form_public_mro_task_assignments_blocked` | `tasks/form_public_mro_task_assignments_blocked/` | EDITADO — Frente 4: ampliar query para N registros |
| `form_public_mro_task_assignments_completed` | `tasks/form_public_mro_task_assignments_completed/` | EDITADO — Frente 4: ampliar query para N registros |

### Novas Apps Descobertas na Reimportacao

| App | Pasta | Relevancia |
|-----|-------|------------|
| `control_pause_task` | `Timesheet/control_pause_task/` | **CRITICA** — Gerencia pausa com passagem de servico (status PENDING_HANDOVER). Essencial para Frente 2. |
| `control_split_assignment` | `tasks/control_split_assignment/` | Media — Split de assignments (MRO-119). **Ja implementa a multi-atribuicao (1:N)**. Usado como base para Frente 4. |

### Tabelas Impactadas

| Tabela | Impacto |
|--------|---------|
| `mro_tasks` | Campos `estimated_hours` e `actual_qty_hours` (via assignment) ja existem — apenas leitura para comparacao |
| `mro_task_assignments` | Tabela pivot ja suporta N registros por task — ampliar consulta nas apps do supervisor |
| `mro_timesheet` | Tabela de apontamentos — criar consulta consolidada com JOINs |
| `mro_employees` | JOIN para nomes de funcionarios |
| `mro_projects` | JOIN para nomes de projetos |

---

## Analise do Estado Atual

### 1. Estouro de HH — Situacao Atual

**Banco de Dados:**
- `mro_tasks.estimated_hours` (numeric) — horas estimadas para a tarefa
- `mro_task_assignments.actual_qty_hours` (numeric) — horas reais executadas por assignment
- `mro_timesheet` — registros de clock-in/clock-out com `start_time`, `end_time`, `duration_minutes`

**Fluxo de Apontamento:**
1. Mecanico abre `form_public_mro_task_assignments` (assignment)
2. Clica em Play → `start_time` registrado em `mro_timesheet`
3. Clica em Pause/Stop → `end_time` + `duration_minutes` calculados
4. `actual_qty_hours` na assignment e atualizado

**Problema Identificado:** Quando as horas executadas ultrapassam as horas estimadas, o sistema atualmente pode travar/derrubar o apontamento do funcionario. A regra precisa ser ajustada para apenas alertar visualmente sem interromper o fluxo.

### 2. Passagem de Servico — Situacao Atual

**Botoes Existentes:**
- `btn_enviar_eng` → status `PENDING_ENG` + log `SENT_TO_ENGINEERING`
- `btn_enviar_prog` → status `PENDING_PROG` + log `SENT_TO_PROG`
- `btn_enviar_coord` → (coordenacao)
- `btn_enviar_cliente` → (cliente)

**Fluxo Atual:** Quando ha "Passagem de Servicos", o status e direcionado para Engenharia (`PENDING_ENG`). A regra de negocio precisa mudar: a passagem de servico deve ir para Programacao (`PENDING_PROG`), nao para Engenharia. Alem disso, nao deve gerar pendencia de Mao de Obra.

### 3. Consulta de Apontamentos — Situacao Atual

**App Existente:** `grid_public_mro_timesheet` — consulta simples que lista todos os registros de `mro_timesheet`.

**Problema:** A grid atual nao possui:
- Filtros obrigatorios (Projeto, JIC, Usuario)
- Dados consolidados (soma de horas por funcionario/tarefa)
- JOINs com `mro_tasks`, `mro_employees`, `mro_projects`

### 4. Multi-atribuicao (Painel do Supervisor) — Situacao Atual

**Apps do Supervisor (tabs_supervisor):**
- `form_public_mro_task_assignments_planned` — assignments planejados
- `form_public_mro_task_assignments_progress` — assignments em andamento
- `form_public_mro_task_assignments_blocked` — assignments com impedimentos
- `form_public_mro_task_assignments_completed` — assignments concluidos

**Schema Atual de `form_public_mro_task_assignments_planned`:**
```sql
SELECT * FROM "public".mro_task_assignments WHERE planned_skill_id = [usr_skill_id]
    AND (supervisor_id IS NULL OR supervisor_id = [usr_employee_id])
AND status_code IN ('NOT_STARTED', 'PLANNED', 'RELEASED')
```

**Problema:** Cada app do supervisor filtra por `planned_skill_id` e `supervisor_id`, retornando apenas os assignments que correspondem ao supervisor logado. Como a tabela `mro_task_assignments` ja e pivot (suporta N registros por task), o ajuste principal e na consulta SQL e na interface para listar todas as atribuicoes, nao apenas uma.

---

## Frente 1 — Gestao de Estouro de HH

### 1.0 Decisao de Design

**Regra:** O sistema nao deve travar/interromper o apontamento quando as horas executadas ultrapassarem as horas estimadas. Deve apenas:
1. Exibir um alerta visual (cor/icone) no grid indicando estouro
2. Exibir um aviso no form da tarefa quando `actual_qty_hours > estimated_hours`
3. Continuar gravando o log normalmente

### 1.1 Alerta no Grid (grid_public_mro_tasks — onRecord)

**Arquivo:** `tasks/grid_public_mro_tasks/events/04_onRecord/onRecord.scriptcase`

Adicionar ao final:

```php
// MRO-120: Indicador de estouro de HH
$var_est = (float){estimated_hours};
// actual_qty_hours e calculado via soma dos assignments ou campo na tasks
// Como actual_qty_hours pode nao estar no SELECT do grid, precisamos obter via lookup
if ($var_est > 0) {
    $var_task_id = (int){task_id};
    $var_sql_hh = "SELECT COALESCE(SUM(actual_qty_hours), 0) FROM mro_task_assignments WHERE task_id = $var_task_id";
    sc_lookup(rs_hh, $var_sql_hh);
    $var_actual = (float)({rs_hh} !== false && !empty({rs_hh}) ? {rs_hh[0][0]} : 0);
    if ($var_actual > $var_est) {
        $var_pct = round(($var_actual / $var_est) * 100);
        {hh_badge} = "<span style='background: #dc3545; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold;' title='Estouro de HH: $var_pct%'>HH " . number_format($var_actual, 1) . "h</span>";
    } else {
        {hh_badge} = "<span style='color: #495057; font-size: 11px;'>" . number_format($var_actual, 1) . "h / " . number_format($var_est, 1) . "h</span>";
    }
} else {
    {hh_badge} = "<span style='color: #adb5bd; font-size: 11px;'>-- h</span>";
}
```

> **Nota:** Se o SELECT do grid ja incluir `actual_qty_hours` como campo agregado, o lookup pode ser dispensado. Verificar schema do grid.

### 1.2 Alerta no Form (form_public_mro_tasks — onLoad)

**Arquivo:** `tasks/form_public_mro_tasks/events/05_onLoad/onLoad.scriptcase`

Adicionar ao final:

```php
// MRO-120: Alerta de estouro de HH no form
if ({estimated_hours} > 0) {
    $var_task_id = (int){task_id};
    $var_sql_hh = "SELECT COALESCE(SUM(actual_qty_hours), 0) FROM mro_task_assignments WHERE task_id = $var_task_id";
    sc_lookup(rs_hh_form, $var_sql_hh);
    $var_actual = (float)({rs_hh_form} !== false && !empty({rs_hh_form}) ? {rs_hh_form[0][0]} : 0);
    if ($var_actual > (float){estimated_hours}) {
        // Exibe alerta visual no topo do form
        {hh_alert} = "<div style='background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 8px 12px; margin-bottom: 10px;'>
            <strong><i class='fa-solid fa-clock'></i> Atencao:</strong> Horas executadas (" . number_format($var_actual, 1) . "h) excedem as horas estimadas (" . number_format((float){estimated_hours}, 1) . "h).
        </div>";
    } else {
        {hh_alert} = "";
    }
} else {
    {hh_alert} = "";
}
```

> **Importante:** Criar um campo calculado `{hh_alert}` no form para exibir o alerta. Pode ser um campo HTML ou label.

### 1.3 Remover Trava no Timesheet

**Arquivo:** `Timesheet/form_public_mro_timesheet/events/`

Identificar e remover/ajustar qualquer logica que atualize o campo `nrc_status` ou que bloqueie o apontamento quando `actual_qty_hours >= estimated_hours`. A gravacao do `mro_timesheet` deve continuar normalmente.

> **Nota:** Verificar eventos onBeforeInsert, onBeforeUpdate, onValidate e botoes Play/Pause para localizar travas existentes.

### 1.4 Log de Estouro

Nao e necessario criar log especifico — o `mro_timesheet` ja registra todos os apontamentos. O alerta visual e suficiente para dar visibilidade ao estouro.

---

## Frente 2 — Passagem de Servico

### 2.0 Decisao de Design

**Regra:** Quando houver "Passagem de Servicos" (Fim de Turno/Repasse), o status/fila deve ser direcionado para a **Programacao** (`PENDING_PROG`) e nao mais para a Engenharia (`PENDING_ENG`). Nao deve gerar pendencia de Mao de Obra (`is_blocked_labor = FALSE`).

**Fluxo Atual (descoberto na reimportacao):**

```
Mecanico → control_pause_task
    ├── motivo_pausa = 1  → PAUSED (pausa curta)
    ├── motivo_pausa = 2  → PENDING_HANDOVER (passagem de servico) ← ESTE MUDA
    ├── motivo_pausa = 3,4,5 → BLOCKED (impedimentos)
    └── motivo_pausa = 6  → SUPSIG (conclusao)
```

Atualmente quando `motivo_pausa = 2`, o assignment vai para `PENDING_HANDOVER`. Nao ha processamento posterior deste status — ele apenas fica parado. A regra de negocio precisa ser ajustada para que, ao ocorrer passagem de servico, o sistema:

1. Atualize a `mro_tasks.status_code` para `PENDING_PROG` (Programacao)
2. Garanta `is_blocked_labor = FALSE`
3. Registre o log de auditoria

### 2.1 Ajuste no Fluxo de Passagem de Servico (control_pause_task)

**Arquivo:** `Timesheet/control_pause_task/events/07_onValidateSuccess/onValidateSuccess.scriptcase`

Na rota `$v_motivo == 2` (Fim de Turno/Repasse), alterar para:

```php
} elseif ($v_motivo == 2) { 
    // ROTA 3: FIM DE TURNO / REPASSE (Vai para Programacao)
    $motivo_txt = '';
    $status_code = 'PENDING_HANDOVER';  // Status do assignment
    $status_ts = 'COMPLETED';
    $status_type = "Repasse de Turno - Notas: $v_notas";
    
    // MRO-120: Atualiza a task mae para PENDING_PROG e remove pendencia de MO
    $var_sql_task = "SELECT task_id FROM mro_task_assignments WHERE assignment_id = " . (int)$v_ass_id;
    sc_lookup(rs_task, $var_sql_task);
    if (!empty({rs_task})) {
        $var_task_id = (int){rs_task[0][0]};
        sc_exec_sql("UPDATE mro_tasks SET status_code = 'PENDING_PROG', is_blocked_labor = FALSE WHERE task_id = $var_task_id");
        sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($var_task_id, 'SENT_TO_PROG_VIA_HANDOVER', '[usr_login]')");
    }
}
```

> **Importante:** Manter o assignment como `PENDING_HANDOVER` para rastreabilidade, mas atualizar a task mae (`mro_tasks`) para `PENDING_PROG`. Isso permite que a task apareça na fila da Programacao.

### 2.2 Nao Gerar Pendencia de MO

Ja incluso no SQL acima: `is_blocked_labor = FALSE`.

### 2.3 Validacao de Passagem de Servico Obrigatoria (ja existe)

**Arquivo:** `Timesheet/control_pause_task/events/05_onValidate/onValidate.scriptcase`

A validacao ja obriga o preenchimento de `{passagem_servico}` quando `motivo_pausa != 1 && motivo_pausa != 6`. Nao requer alteracao.

```php
if({motivo_pausa}!=1 && {motivo_pausa}!=6){
    if({passagem_servico}==''){
        sc_error_message("Passagem de serviço Obrigatória");
    }
}
```

---

## Frente 3 — Consulta de Apontamentos

### 3.0 Decisao de Design

Criar uma **nova aplicacao** `grid_mro_timesheet_consolidado` (Grid) no diretorio `Timesheet/` que consolide os apontamentos com filtros obrigatorios.

### 3.1 Estrutura da Nova App

```
Timesheet/grid_mro_timesheet_consolidado/
├── config.json
├── sql/
│   └── schema.sql
└── events/
    ├── 03_onScriptInit/
    │   └── onScriptInit.scriptcase
    └── 04_onRecord/
        └── onRecord.scriptcase
```

### 3.2 SQL Schema

**Arquivo:** `Timesheet/grid_mro_timesheet_consolidado/sql/schema.sql`

```sql
SELECT
    t.timesheet_id,
    t.assignment_id,
    t.employee_id,
    t.appointment_date,
    t.start_time,
    t.end_time,
    t.duration_minutes,
    t.status,
    t.pause_reason,
    t.handover_notes,
    -- Dados do funcionario
    e.full_name AS employee_name,
    e.employee_code,
    -- Dados da task
    tk.task_id,
    tk.task_code,
    tk.task_name AS task_title,
    tk.estimated_hours,
    tk.is_nrc,
    -- Dados do projeto
    p.project_id,
    p.project_code,
    p.project_name,
    -- Dados da skill/assignment
    a.planned_qty_hours,
    a.actual_qty_hours,
    a.skill_id,
    s.skill_name
FROM public.mro_timesheet t
LEFT JOIN public.mro_employees e ON e.employee_id = t.employee_id
LEFT JOIN public.mro_task_assignments a ON a.assignment_id = t.assignment_id
LEFT JOIN public.mro_tasks tk ON tk.task_id = COALESCE(t.task_id, a.task_id)
LEFT JOIN public.mro_projects p ON p.project_id = tk.project_id
LEFT JOIN public.mro_skills s ON s.skill_code = a.skill_id::varchar
ORDER BY t.start_time DESC
```

> **Nota:** Ajustar JOINs conforme estrutura real. Verificar se `t.task_id` existe em `mro_timesheet` — se nao existir, usar apenas `a.task_id`.

### 3.3 Filtros Obrigatorios

Configurar no ScriptCase os seguintes campos como **filtros obrigatorios** (required filter):

| Campo | Label | Tipo de Filtro |
|-------|-------|----------------|
| `p.project_id` | Projeto | Select com lookup em `mro_projects` |
| `tk.task_code` | JIC (Job Instruction Card) | Texto livre |
| `t.employee_id` | Usuario/Funcionario | Select com lookup em `mro_employees` |

> **Nota:** Pelo menos 1 dos 3 filtros deve ser preenchido para executar a consulta. Isso pode ser configurado como filtro avancado no ScriptCase ou via validacao no onScriptInit.

### 3.4 onScriptInit — Validacao de Filtro

**Arquivo:** `Timesheet/grid_mro_timesheet_consolidado/events/03_onScriptInit/onScriptInit.scriptcase`

```php
// MRO-120: Validar que pelo menos um filtro obrigatorio foi informado
if (empty({project_id}) && empty({task_code}) && empty({employee_id})) {
    sc_error_message("Informe pelo menos um filtro: Projeto, JIC (Codigo da Tarefa) ou Usuario/Funcionario.");
    sc_error_exit();
}
```

### 3.5 onRecord — Formatacao Visual

**Arquivo:** `Timesheet/grid_mro_timesheet_consolidado/events/04_onRecord/onRecord.scriptcase`

```php
// MRO-120: Formatar duracao no padrao HH:MM
if (!empty({duration_minutes}) && {duration_minutes} > 0) {
    $var_horas = floor({duration_minutes} / 60);
    $var_min = {duration_minutes} % 60;
    {duration_display} = sprintf("%02d:%02d", $var_horas, $var_min);
} else {
    {duration_display} = "--:--";
}

// MRO-120: Badge de status do apontamento
$var_status = {status};
$var_status_map = [
    'RUNNING'     => ['label' => 'EM ANDAMENTO',  'color' => '#188038', 'text' => '#ffffff'],
    'PAUSED'      => ['label' => 'PAUSADO',       'color' => '#fd7e14', 'text' => '#ffffff'],
    'COMPLETED'   => ['label' => 'CONCLUIDO',     'color' => '#004080', 'text' => '#ffffff'],
    'CANCELLED'   => ['label' => 'CANCELADO',     'color' => '#dc3545', 'text' => '#ffffff'],
];
if (isset($var_status_map[$var_status])) {
    $s = $var_status_map[$var_status];
    {status_badge} = "<span style='background: $s[color]; color: $s[text]; padding: 2px 8px; border-radius: 4px; font-size: 11px;'>$s[label]</span>";
} else {
    {status_badge} = $var_status;
}
```

---

## Frente 4 — Multi-atribuicao (Painel do Supervisor)

### 4.0 Decisao de Design

**Objetivo:** Permitir que mais de 1 funcionario seja atribuido a mesma tarefa (relacionamento 1:N), e que o supervisor consiga visualizar e gerenciar essas multi-atribuicoes no Painel do Supervisor.

**Situacao atual:** A tabela `mro_task_assignments` ja e uma tabela pivot e **suporta N registros por task_id**. O banco ja permite multi-atribuicao.

**Ja implementado (MRO-119):** A app `control_split_assignment` ja permite dividir um assignment em 2, criando multi-atribuicao na pratica. Cada assignment splitado vira um registro independente com seu proprio `executed_by_employee_id`, `planned_qty_hours` e `status_code`. Isso ja atende o requisito de relacionamento 1:N. O que falta e apenas o ajuste visual no painel do supervisor.

### 4.1 Apps Impactadas (4 apps do supervisor)

As 4 apps do `tabs_supervisor` precisam de ajustes:

| App | Funcao |
|-----|--------|
| `form_public_mro_task_assignments_planned` | "A Distribuir" — assignments planejados |
| `form_public_mro_task_assignments_progress` | "Em Andamento" — assignments em execucao |
| `form_public_mro_task_assignments_blocked` | "Com Impedimentos" — assignments bloqueados |
| `form_public_mro_task_assignments_completed` | "Concluidas" — assignments finalizados |

### 4.2 O que precisa mudar

**A) Query das apps — remover filtro `supervisor_id`**

Cada app filtra por `planned_skill_id = [usr_skill_id]` e `(supervisor_id IS NULL OR supervisor_id = [usr_employee_id])`. O filtro de `supervisor_id` impede que o supervisor veja assignments de outros supervisores na mesma skill. Como a multi-atribuicao pode envolver funcionarios de diferentes supervisores, esse filtro precisa ser removido.

**Arquivos:** `tasks/form_public_mro_task_assignments_{planned,progress,blocked,completed}/sql/schema.sql`

**Antes:**
```sql
SELECT * FROM mro_task_assignments 
WHERE planned_skill_id = [usr_skill_id] 
  AND (supervisor_id IS NULL OR supervisor_id = [usr_employee_id])
  AND status_code IN (...)
```

**Depois:**
```sql
SELECT a.*, e.full_name AS employee_name
FROM mro_task_assignments a
LEFT JOIN mro_employees e ON e.employee_id = a.employee_id
WHERE a.planned_skill_id = [usr_skill_id]
  AND a.status_code IN (...)
ORDER BY a.task_id, a.assignment_id
```

**B) Indicador visual de multi-atribuicao**

Adicionar no `onRecord` de cada app um badge que mostre quando uma task tem mais de 1 assignment:

```php
$var_count = 0;
$var_sql_count = "SELECT COUNT(*) FROM mro_task_assignments WHERE task_id = " . (int){task_id};
sc_lookup(rs_count, $var_sql_count);
$var_count = !empty({rs_count}) ? (int){rs_count[0][0]} : 0;

if ($var_count > 1) {
    {multi_badge} = "<span style='background:#004080; color:#fff; border-radius:10px; padding:1px 8px; font-size:10px;'>+" . ($var_count - 1) . "</span>";
} else {
    {multi_badge} = "";
}
```

**C) Split de assignment (ja existe)**

A app `control_split_assignment` (criada na MRO-119) ja permite dividir um assignment em 2, criando multi-atribuicao. O botao `btn_split_assignment` existe nas apps do supervisor.

**D) Interface do Supervisor (`ROOT/tabs_supervisor`)**

O `tabs_supervisor` em `ROOT/tabs_supervisor/` gerencia as 4 abas via config serializada. Nao requer alteracao de codigo — apenas verificar se os parametros passados (`assignment_id`, `usr_skill_id`, `usr_employee_id`) continuam compativeis com as queries ajustadas.

### 4.3 Fluxo de multi-atribuicao

```
Task (mro_tasks)
  ├── Assignment A (employee X, skill_id, planned_qty_hours)
  ├── Assignment B (employee Y, skill_id, planned_qty_hours)  ← 1:N
  └── Assignment C (employee Z, skill_id, planned_qty_hours)  ← 1:N

Supervisor ve todos os assignments da sua skill
  └── Pode dividir (split) um assignment em 2 via control_split_assignment
  └── Cada mecanico faz clock-in/out no seu assignment individual
  └── Cada assignment tem seu proprio actual_qty_hours (apontamento independente)
```

### 4.4 O que NAO muda

- O `form_public_mro_task_assignments` continua com insert desligado (assignments sao criados pelo `btn_liberar_para_execucao` ou pelo split)
- O mecanico continua vendo apenas seus proprios assignments no `grid_my_tasks` (filtro por `executed_by_employee_id`)
- O timesheet continua por assignment — cada mecanico aponta horas no seu
- O split de assignment ja existe (`control_split_assignment`)

---

## Ordem de Execucao

### Etapa 1 — Gestao de Estouro de HH

| # | Acao | App/Arquivo |
|---|------|-------------|
| 1.1 | Adicionar indicador de estouro de HH no onRecord do grid | `grid_public_mro_tasks/events/04_onRecord/onRecord.scriptcase` |
| 1.2 | Adicionar alerta visual no onLoad do form | `form_public_mro_tasks/events/05_onLoad/onLoad.scriptcase` |
| 1.3 | Remover travas de estouro no timesheet (se existirem) | `Timesheet/form_public_mro_timesheet/events/` |
| 1.4 | Testar fluxo de apontamento com estouro de HH | Homologacao |

### Etapa 2 — Passagem de Servico

| # | Acao | App/Arquivo |
|---|------|-------------|
| 2.1 | Ajustar rota de passagem de servico no onValidateSuccess | `Timesheet/control_pause_task/events/07_onValidateSuccess/onValidateSuccess.scriptcase` |
| 2.2 | Garantir que is_blocked_labor = FALSE e status = PENDING_PROG na task mae | `Timesheet/control_pause_task/events/07_onValidateSuccess/onValidateSuccess.scriptcase` |
| 2.3 | Verificar validacao de passagem_servico obrigatoria (ja existe) | `Timesheet/control_pause_task/events/05_onValidate/onValidate.scriptcase` |
| 2.4 | Testar fluxo completo de passagem de servico | Homologacao |

### Etapa 3 — Consulta de Apontamentos

| # | Acao | App/Arquivo |
|---|------|-------------|
| 3.1 | Criar app `grid_mro_timesheet_consolidado` | Nova app ScriptCase |
| 3.2 | Criar `schema.sql` com JOINs | `grid_mro_timesheet_consolidado/sql/schema.sql` |
| 3.3 | Criar `onScriptInit` com validacao de filtros obrigatorios | `grid_mro_timesheet_consolidado/events/` |
| 3.4 | Criar `onRecord` com formatacao visual | `grid_mro_timesheet_consolidado/events/` |
| 3.5 | Configurar filtros avancados no ScriptCase | Interface ScriptCase |
| 3.6 | Testar consulta com cada filtro obrigatorio | Homologacao |

### Etapa 4 — Multi-atribuicao (Painel do Supervisor)

> **Nota:** O mecanismo de split (control_split_assignment) ja foi implementado na MRO-119 e ja cria multi-atribuicao (1:N). O que resta e apenas o ajuste visual no painel do supervisor.

| # | Acao | App/Arquivo | Status |
|---|------|-------------|:------:|
| — | Split de assignment (criar multi-atribuicao) | `tasks/control_split_assignment/` (MRO-119) | ✅ OK |
| 4.1 | Remover filtro supervisor_id da query do planned | `tasks/form_public_mro_task_assignments_planned/sql/schema.sql` | Pendente |
| 4.2 | Remover filtro supervisor_id da query do progress | `tasks/form_public_mro_task_assignments_progress/sql/schema.sql` | Pendente |
| 4.3 | Remover filtro supervisor_id da query do blocked | `tasks/form_public_mro_task_assignments_blocked/sql/schema.sql` | Pendente |
| 4.4 | Remover filtro supervisor_id da query do completed | `tasks/form_public_mro_task_assignments_completed/sql/schema.sql` | Pendente |
| 4.5 | Adicionar badge de multi-atribuicao no onRecord das 4 apps | Cada app do supervisor | Pendente |
| 4.6 | Verificar config do tabs_supervisor (ROOT/) | `ROOT/tabs_supervisor/config.json` | Pendente |
| 4.7 | Testar: atribuir 2 mecanicos a mesma task e ver no painel | Homologacao | Pendente |

---

## Riscos e Pontos de Atencao

### Risco 1 (MONITORADO) — Impacto no Timesheet (MEDIO)

**Descricao:** Alteracoes no fluxo de estouro de HH podem impactar o `form_public_mro_timesheet`, que e a app onde o mecanico faz clock-in/clock-out.

**Achado da reimportacao:** As pastas de eventos do `form_public_mro_timesheet` existem mas os arquivos `.scriptcase` estao vazios — nao ha codigo PHP implementado. O `form_public_mro_task_assignments` sim tem eventos preenchidos. A trava de estouro de HH, se existir, esta mais provavelmente no `form_public_mro_task_assignments` (onValidate / onBeforeUpdate) ou no `control_pause_task`.

**Mitigacao:** Verificar eventos do `form_public_mro_task_assignments` e `control_pause_task` para localizar travas. O `form_public_mro_timesheet` nao requer alteracao por enquanto.

### Risco 2 (ATIVO) — Supervisor Ver Tarefas de Outros (MEDIO)

**Descricao:** Ao remover o filtro `supervisor_id` das queries do supervisor, ele pode passar a ver assignments que nao sao da sua equipe.

**Mitigacao:** Manter o filtro por `planned_skill_id` que ja existe. O supervisor ve todas as atribuicoes da skill que ele gerencia, o que e o comportamento desejado.

### Risco 3 (MEDIO) — Performance da Consulta de Apontamentos (MEDIO)

**Descricao:** A `grid_mro_timesheet_consolidado` faz JOINs em varias tabelas (`mro_timesheet`, `mro_employees`, `mro_task_assignments`, `mro_tasks`, `mro_projects`, `mro_skills`). Com muitos registros, a consulta pode ficar lenta.

**Mitigacao:** Garantir que os filtros obrigatorios sejam aplicados via `sc_select_where` no `onScriptInit` para limitar o escopo da consulta. Verificar indexes nas tabelas envolvidas.

### Risco 4 (BAIXO) — Compatibilidade com MRO-119 (BAIXO)

**Descricao:** A MRO-119 (Frente 2) ja preve a criacao de `grid_public_mro_task_assignments` para gestao de multiplas atribuicoes. A Frente 4 da MRO-120 tem objetivo similar mas foca no Painel do Supervisor.

**Mitigacao:** Alinhar com o que ja foi planejado na MRO-119 para evitar duplicidade. A MRO-119 cria um grid generico; a MRO-120 ajusta as queries especificas do supervisor.

---

## Sumario de Arquivos

### Arquivos a CRIAR

| Arquivo | Frente |
|---------|--------|
| `Timesheet/grid_mro_timesheet_consolidado/config.json` | Frente 3 |
| `Timesheet/grid_mro_timesheet_consolidado/sql/schema.sql` | Frente 3 |
| `Timesheet/grid_mro_timesheet_consolidado/events/03_onScriptInit/onScriptInit.scriptcase` | Frente 3 |
| `Timesheet/grid_mro_timesheet_consolidado/events/04_onRecord/onRecord.scriptcase` | Frente 3 |
| `tasks/form_public_mro_tasks/button/btn_validar_prog_rotina.scriptcase` | Frente 2 |

### Arquivos a EDITAR

| Arquivo | Frente | Alteracao |
|---------|--------|-----------|
| `tasks/grid_public_mro_tasks/events/04_onRecord/onRecord.scriptcase` | F1 | + indicador de estouro de HH |
| `tasks/form_public_mro_tasks/events/05_onLoad/onLoad.scriptcase` | F1, F2 | + alerta de estouro de HH (F1); + exibir btn_validar_prog_rotina para rotinas em PENDING_PROG (F2) |
| `Timesheet/control_pause_task/events/07_onValidateSuccess/onValidateSuccess.scriptcase` | F2 | + atualizar task mae para PENDING_PROG + is_blocked_labor=FALSE |
| `tasks/form_public_mro_task_assignments_planned/sql/schema.sql` | F4 | Remover filtro supervisor_id |
| `tasks/form_public_mro_task_assignments_progress/sql/schema.sql` | F4 | Remover filtro supervisor_id |
| `tasks/form_public_mro_task_assignments_blocked/sql/schema.sql` | F4 | Remover filtro supervisor_id |
| `tasks/form_public_mro_task_assignments_completed/sql/schema.sql` | F4 | Remover filtro supervisor_id |

### Arquivos a VERIFICAR (podem precisar de ajuste)

| Arquivo | Frente | Motivo |
|---------|--------|--------|
| `Timesheet/form_public_mro_timesheet/events/` | F1 | Pastas de eventos existem mas arquivos estao vazios — sem trava identificada |
| `Timesheet/form_public_mro_timesheet/config.json` | F1 | Campos e tipos |
| `ROOT/tabs_supervisor/config.json` | F4 | Configuracao das abas (tabs) |
| `ROOT/tabs_supervisor/sql/schema.sql` | F4 | Serializacao das abas (PHP serialized) |

---

## Proximos Passos

1. [ ] **Analisar codigo existente** — Verificar se ha travas de estouro de HH no timesheet
2. [ ] **Frente 1 — Estouro de HH** — Implementar alertas visuais no grid e form
3. [ ] **Frente 2 — Passagem de Servico** — Ajustar rota para Programacao
4. [ ] **Frente 3 — Consulta de Apontamentos** — Criar grid consolidada
5. [ ] **Frente 4 — Multi-atribuicao (Supervisor)** — Ajustar queries do supervisor
6. [ ] **Testes integrados** — Validar fluxo completo
