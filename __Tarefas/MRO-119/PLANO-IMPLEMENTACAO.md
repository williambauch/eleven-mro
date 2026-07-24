# MRO-119 — Plano de Implementacao

> **Tarefa:** Incluir classificacao por fases, permitir multipla atribuicao e criar filtros avancados de pendencias e impedimentos no grid.
>
> **Data:** 2026-07-23
>
> **Status:** Em execucao — Frentes 1 e 3 concluidas via recursos nativos ScriptCase; Frente 2 pendente

---

## Indice

1. [Visao Geral](#visao-geral)
2. [Analise do Estado Atual](#analise-do-estado-atual)
3. [Frente 1 — Fases (phase_code / baseline_phase_code)](#frente-1--fases)
4. [Frente 2 — Multipla Atribuicao](#frente-2--multipla-atribuicao)
5. [Frente 3 — Filtros de Pendencias](#frente-3--filtros-de-pendencias)
6. [Ordem de Execucao](#ordem-de-execucao)
7. [Riscos e Pontos de Atencao](#riscos-e-pontos-de-atencao)

---

## Visao Geral

A entidade principal de Tarefas precisa ser expandida em 3 frentes independentes:

| # | Frente | Objetivo | Complexidade | Status |
|---|--------|----------|:---:|:---:|
| 1 | **Fases** | Expor `phase_code` e `baseline_phase_code` no Form e Grid com lookup na tabela `mro_project_phases`, filtros de pesquisa e agrupamento (quebra) | Media | :white_check_mark: Concluida |
| 2 | **Multipla Atribuicao** | Permitir que mais de um mecanico/funcionario seja atribuido a mesma tarefa via interface amigavel | Alta | Pendente |
| 3 | **Filtros de Pendencias** | Botoes rapidos no grid: Retidas por predecessora, Falta de ferramentas, Falta de materiais | Baixa | :white_check_mark: Concluida |

### Apps Impactadas

| App | Pasta | Tipo de Impacto |
|-----|-------|-----------------|
| `form_public_mro_tasks` | `tasks/form_public_mro_tasks/` | EDITADO — campos de fase configurados via Select nativo + onLoad readonly |
| `grid_public_mro_tasks` | `tasks/grid_public_mro_tasks/` | EDITADO — colunas de fase, filtro refinado, quebra dinamica |
| `form_public_mro_task_assignments` | `tasks/form_public_mro_task_assignments/` | EDITAR (Frente 2) — suporte a multiplos registros |

### Tabelas Impactadas

| Tabela | Impacto |
|--------|---------|
| `mro_tasks` | Campos `phase_code` e `baseline_phase_code` ja existem — apenas expor na UI |
| `mro_project_phases` | Tabela de referencia para lookup de fases |
| `mro_task_assignments` | Tabela pivot ja existe — interface precisa suportar multiplos |
| `mro_task_dependencies` | Usada no filtro "Retidas por predecessora" |

---

## Analise do Estado Atual

### 1. Fases — Situacao Atual

**Banco de Dados:**
- Campos `phase_code` (varchar 50) e `baseline_phase_code` (varchar 50) **ja existem** em `mro_tasks`
- O grid (`grid_public_mro_tasks/sql/schema.sql`) **ja inclui** essas colunas no SELECT
- **NAO existe FK** entre `mro_tasks.phase_code` e `mro_project_phases.phase_code`

** Tabela `mro_project_phases` (9 registros):**

| phase_code | phase_name | sort_order |
|------------|------------|:---:|
| INDUCTI | Aircraft Induction | 10 |
| ENG RUN | Initial Operational & Functional Tests | 20 |
| A OPEN | Open up & Washing | 30 |
| INSPEC | Inspection Phase | 40 |
| DAMAGE | Damage Assessment | 50 |
| DISCREP | Discrepancies Correction Phase | 60 |
| CLOSING | Access Closing | 70 |
| FINTEST | Final Operational & Functional Tests | 80 |
| FPREP | Flight Preparation & Aircraft Delivery | 90 |

**INCOMPATIBILIDADE CRITICA:** Os codigos em `mro_tasks.phase_code` usam nomenclatura diferente (provavelmente importados do Primavera P6):

**Conclusao do cliente (reuniao 23/07):** Apenas as 22 fases do documento `Refinamento EAP+Pendencias.pdf` serao mantidas na tabela. Os codigos legados do P6 (INSP, FMRO, INREC, etc.) **nao serao cadastrados** — serao tratados como pendencia de importacao para mapeamento futuro.

**Interface (pre-implementacao):**
- Os campos NAO estavam configurados como visiveis, filtraveis ou agrupaveis no ScriptCase
- O form `form_public_mro_tasks` NAO tinha campos para phase_code/baseline_phase_code
- Nao havia evento onLoad, onValidate ou Ajax relacionado a fases

**:white_check_mark: Implementacao (via recursos nativos ScriptCase):**
- Campos `phase_code` e `baseline_phase_code` configurados como **Select** no ScriptCase com lookup SQL direto na tabela `mro_project_phases` (`SELECT phase_code, phase_name FROM public.mro_project_phases ORDER BY sort_order`)
- Adicionados como colunas visiveis no grid, com suporte a **filtro refinado**, **filtro avancado** e **quebra dinamica** (agrupamento)
- No form, exibidos na aba "Datas e Status"
- Unico codigo PHP necessario: regra de readonly do `baseline_phase_code` no `onLoad` (vide `MRO-119- fases e permitir multipla atribuicao.md`)

### 2. Multipla Atribuicao — Situacao Atual

**Banco de Dados:**
- Tabela `mro_task_assignments` **ja e uma tabela pivot** e suporta N registros por task
- Estrutura: `assignment_id`, `task_id`, `resource_id`, `employee_id`, `skill_id`, `planned_skill_id`, `planned_qty_hours`, `actual_qty_hours`, `status_code`, `project_id`, `executed_by_employee_id`, `supervisor_id`, `role_id`
- Apenas **12 tasks** no banco tem mais de 1 assignment (a maioria tem 1)

**Interface:**
- `form_public_mro_task_assignments` e um **form simples** (SQL: `SELECT * FROM mro_task_assignments`)
- Acessa UMA assignment por vez — nao permite visualizar ou gerenciar multiplos assignments
- E usada como sub-aplicacao (detalhe) dentro de `form_public_mro_tasks`
- O mecanico faz clock-in/clock-out por esta app — qualquer alteracao impacta o timesheet

**Conclusao:** O banco suporta multipla atribuicao, mas a interface atual so permite trabalhar com 1 assignment por vez. Precisamos de uma UI que:
- Liste todos os assignments da task atual
- Permita adicionar novos (com employee, skill, horas planejadas)
- Permita remover (com regras de negocio — ex: nao remover se ja tem horas executadas)

### 3. Filtros de Pendencias — Situacao Atual

**Banco de Dados:**
- Campos `is_blocked_material`, `is_blocked_tool`, `is_blocked_labor` (boolean) **ja existem** em `mro_tasks`
- Tabela `mro_task_dependencies` (predecessora/successor) **ja existe** com: `dependency_id`, `predecessor_task_id`, `successor_task_id`, `dep_type`, `lag_hours`

**Interface Atual (grid):**
- `onRecord`: Exibe icones coloridos indicando status de cada bloqueio (vermelho = bloqueado, cinza = ok)
- `btn_mat`, `btn_fer`, `btn_mao` (Ajax): Apenas fazem **TOGGLE** do flag (alternam true/false) — NAO sao filtros
- NAO ha botoes para filtrar a grid mostrando apenas tarefas bloqueadas

**Conclusao:** Os indicadores visuais ja existem. O que falta sao botoes de **FILTRO RAPIDO** que apliquem `sc_select_where` para isolar tarefas com pendencias.

---

## Frente 1 — Fases :white_check_mark: CONCLUIDA

### 1.0 Decisao de Design: Compatibilizacao de Codigos de Fase

**Problema:** Os codigos em `mro_tasks` (P6) nao batem com `mro_project_phases` (canonico).

**Decisao final (reuniao com cliente em 23/07/2026):**

A tabela `mro_project_phases` deve conter **APENAS as 22 fases** que constam no documento `Refinamento EAP+Pendencias.pdf`.

1. **Limpar** todos os registros existentes em `mro_project_phases`
2. **Cadastrar apenas as 22 fases do PDF** com descricoes no formato `"X.Y - Descricao"` (ex: `"0.0 - Aircraft Induction"`)
3. **Codigos legados do P6** (INSP, FMRO, INREC, LUBR, FTEST, FACS, DEL, F1M, FINSP, AACS, IND) **nao serao cadastrados**
4. **Nao criar FK** por enquanto

### 1.1 Banco de Dados — Migration

**Arquivo:** `migrations/MRO-119_01_phases_pdf_only.sql`

Migration executado: `DELETE FROM` todos os registros antigos + `INSERT` das 22 fases aprovadas do PDF Refinamento, com descricoes no formato `"X.Y - Descricao"` e `sort_order` em intervalos de 10.

### 1.2 Implementacao Realizada (recursos nativos ScriptCase)

**Campos Select com lookup:**
- `phase_code` e `baseline_phase_code` configurados como **Select** no ScriptCase
- Lookup SQL: `SELECT phase_code, phase_name FROM public.mro_project_phases ORDER BY sort_order`
- Exibidos no form na aba "Datas e Status"

**Grid:**
- Colunas `phase_code` e `baseline_phase_code` visiveis no `grid_public_mro_tasks`
- Adicionados ao **filtro refinado** e **filtro avancado**
- Habilitada **quebra dinamica** (agrupamento) por ambos os campos

**Form (onLoad):**
- Regra de readonly: `baseline_phase_code` editavel apenas em DRAFT — unico codigo PHP necessario
- `phase_code` sempre editavel

**OBS:** A validacao de existencia da fase (onValidate) e desnecessaria porque o Select nativo do ScriptCase ja restringe as opcoes validas.

---

## Frente 2 — Multipla Atribuicao

### 2.0 Analise do Fluxo Atual

```
form_public_mro_tasks (Mestre)
    └── form_public_mro_task_assignments (Detalhe)
            └── SQL: SELECT * FROM mro_task_assignments
            └── Filtro via [glo_task_id] (variavel global)
            └── Exibe UMA assignment (a primeira ou a ativa)
            └── Botao Play/Pause → mro_timesheet (clock-in/out)
            └── Botao Abrir Nao Rotina → ctrl_abertura_nrc
```

**Problema:** Se uma task tem 2 assignments, o form detalhe so mostra 1. O usuario nao consegue ver nem gerenciar o segundo.

### 2.1 Decisao de Design

**Opcao recomendada: Criar um grid de assignments como sub-aplicacao**

Em vez de modificar o `form_public_mro_task_assignments` (que lida com clock-in/out e timesheet — arriscado quebrar), criar uma **NOVA app** `grid_public_mro_task_assignments` que:

1. Lista TODOS os assignments da task atual
2. Cada linha tem botoes: Editar (abre o form existente), Excluir, Play/Pause
3. Botao "Nova Atribuicao" no topo para adicionar mecanico

**Fluxo proposto:**

```
form_public_mro_tasks (Mestre)
    ├── grid_public_mro_task_assignments (NOVO — lista assignments)
    │       └── Botao "Nova Atribuicao" → form_public_mro_task_assignments (modo insert)
    │       └── Botao "Editar" → form_public_mro_task_assignments (modo update)
    │       └── Botao "Clock-in" → redireciona para form_public_mro_task_assignments
    └── form_public_mro_task_assignments (Existente — mantido para clock-in/out)
```

**Vantagens:**
- Nao quebra o fluxo de clock-in/out existente
- O grid oferece visibilidade total das atribuicoes
- O form continua funcionando para edicao individual e apontamento

### 2.2 Criacao do Grid de Assignments

#### 2.2.1 Estrutura da Nova App

```
tasks/grid_public_mro_task_assignments/
├── config.json
├── sql/
│   └── schema.sql
├── events/
│   ├── 03_onScriptInit/
│   │   └── onScriptInit.scriptcase
│   └── 04_onRecord/
│       └── onRecord.scriptcase
├── button/
│   ├── btn_novo_assignment.scriptcase
│   └── btn_editar_assignment.scriptcase
└── events_ajax/
    └── (vazio por enquanto)
```

#### 2.2.2 SQL Schema

**Arquivo:** `tasks/grid_public_mro_task_assignments/sql/schema.sql`

```sql
SELECT
    a.assignment_id,
    a.task_id,
    a.resource_id,
    a.employee_id,
    a.skill_id,
    a.planned_skill_id,
    a.planned_qty_hours,
    a.actual_qty_hours,
    a.status_code,
    a.role_id,
    a.executed_by_employee_id,
    a.supervisor_id,
    a.project_id,
    -- Nomes para exibicao
    e.full_name AS employee_name,
    s.skill_name AS skill_name,
    r.resource_name AS resource_name,
    a.created_at,
    a.created_by
FROM public.mro_task_assignments a
LEFT JOIN public.mro_employees e ON e.employee_id = a.employee_id
LEFT JOIN public.mro_skills s ON s.skill_code = a.skill_id::varchar  -- ajustar conforme tipo real
LEFT JOIN public.mro_resources r ON r.resource_id = a.resource_id
WHERE a.task_id = [glo_task_id]
ORDER BY a.assignment_id
```

> **Nota:** Ajustar os JOINs conforme a estrutura real das tabelas `mro_employees`, `mro_skills`, `mro_resources`. Verificar se `skill_id` e realmente FK para `mro_skills.skill_code`.

#### 2.2.3 onScriptInit — Filtro pela task mestre

**Arquivo:** `tasks/grid_public_mro_task_assignments/events/03_onScriptInit/onScriptInit.scriptcase`

```php
// MRO-119: Garantir que o grid so mostra assignments da task atual
if (empty([glo_task_id])) {
    sc_error_message("Nenhuma tarefa selecionada.");
    sc_error_exit();
}

$var_task_id = (int)[glo_task_id];
sc_select_where(add) = "WHERE a.task_id = $var_task_id";
```

#### 2.2.4 onRecord — Status visual + botoes de acao

**Arquivo:** `tasks/grid_public_mro_task_assignments/events/04_onRecord/onRecord.scriptcase`

```php
// MRO-119: Status badge do assignment
$status = {status_code};

$status_map = [
    'PLANNED'    => ['label' => 'PLANEJADO',  'color' => '#e9ecef', 'text' => '#495057'],
    'IN_PROGRESS'=> ['label' => 'EM EXECUCAO', 'color' => '#188038', 'text' => '#ffffff'],
    'COMPLETED'  => ['label' => 'CONCLUIDO',  'color' => '#004080', 'text' => '#ffffff'],
    'PAUSED'     => ['label' => 'PAUSADO',    'color' => '#fd7e14', 'text' => '#ffffff'],
];

if (isset($status_map[$status])) {
    $s = $status_map[$status];
    {status_label} = "<span style='background: $s[color]; color: $s[text]; padding: 2px 8px; border-radius: 4px; font-size: 11px;'>$s[label]</span>";
} else {
    {status_label} = $status;
}

// MRO-119: Link para editar/form da assignment
{link_editar} = sc_make_link('form_public_mro_task_assignments', 'assignment_id=' . {assignment_id});

// MRO-119: Dica visual — horas planejadas vs executadas
if ({planned_qty_hours} > 0 && {actual_qty_hours} > 0) {
    $pct = round(({actual_qty_hours} / {planned_qty_hours}) * 100);
    $cor_barra = $pct >= 100 ? '#188038' : ($pct >= 50 ? '#fd7e14' : '#dc3545');
    {progresso} = "<div style='background: #e9ecef; border-radius: 4px; height: 8px; width: 100px;'>
        <div style='background: $cor_barra; height: 8px; width: " . min($pct, 100) . "%; border-radius: 4px;'></div>
    </div><small>$pct%</small>";
} else {
    {progresso} = "<small style='color: #adb5bd;'>--</small>";
}
```

#### 2.2.5 Botao Nova Atribuicao

**Arquivo:** `tasks/grid_public_mro_task_assignments/button/btn_novo_assignment.scriptcase`

```php
// MRO-119: Redireciona para o form de assignment em modo novo
// Passa o task_id para o novo registro
sc_redir('form_public_mro_task_assignments', 'task_id=' . [glo_task_id] . ';mode=new', '_self');
```

> **Nota:** O `form_public_mro_task_assignments` precisa ser adaptado para aceitar o parametro `mode=new` e pre-preencher o `task_id`. Ver evento `onApplicationInit` ou `onLoad`.

#### 2.2.6 Botao Editar Assignment

**Arquivo:** `tasks/grid_public_mro_task_assignments/button/btn_editar_assignment.scriptcase`

```php
// MRO-119: Redireciona para editar o assignment selecionado
sc_redir('form_public_mro_task_assignments', 'assignment_id=' . {assignment_id}, '_self');
```

### 2.3 Ajustes no Form de Assignment Existente

#### 2.3.1 onApplicationInit — Suporte a modo new

**Arquivo:** `tasks/form_public_mro_task_assignments/events/01_onApplicationInit/onApplicationInit.scriptcase`

```php
// MRO-119: Se veio com parametro mode=new, prepara para insercao
if ([var_mode] == 'new') {
    [glo_modo_novo] = true;
}

// Se veio com task_id direto (novo assignment), preenche o campo
if (!empty([var_task_id]) && empty({task_id})) {
    {task_id} = [var_task_id];
}
```

#### 2.3.2 onLoad — Ajuste para modo novo

**Arquivo:** `tasks/form_public_mro_task_assignments/events/05_onLoad/onLoad.scriptcase`

Adicionar no inicio:

```php
// MRO-119: Se for modo novo, ocultar botoes de Play/Pause
if (!empty([glo_modo_novo]) && [glo_modo_novo] === true) {
    sc_btn_display('Play', 'off');
    sc_btn_display('Pause', 'off');
    {display_tempo} = "<div style='color: #adb5bd; text-align: center;'>Novo assignment — aguardando salvamento</div>";
}
```

### 2.4 Integracao: Chamar o Grid a partir do Form da Task

**Arquivo:** `tasks/form_public_mro_tasks/events/05_onLoad/onLoad.scriptcase`

Adicionar:

```php
// MRO-119: Abrir grid de assignments como sub-aplicacao (detalhe)
// Isto e configurado via ScriptCase — nao em codigo
// Configurar a toolbar/link para chamar grid_public_mro_task_assignments
// passando [glo_task_id] = {task_id}
sc_btn_display('btn_assignments', 'on');
```

> **Nota ScriptCase:** A vinculacao mestre-detalhe entre `form_public_mro_tasks` (mestre) e `grid_public_mro_task_assignments` (detalhe) deve ser configurada na interface do ScriptCase, nao em codigo. O campo de ligacao e `task_id`.

### 2.5 Validacoes — Regras de Negocio

#### 2.5.1 Evitar atribuicao duplicada (onValidate do form de assignment)

```php
// MRO-119: Nao permitir mesmo employee + mesma skill na mesma task
if (!empty({employee_id}) && !empty({skill_id}) && !empty({task_id})) {
    $var_emp = (int){employee_id};
    $var_skill = (int){skill_id};
    $var_task = (int){task_id};

    $var_sql_dup = "SELECT assignment_id FROM mro_task_assignments
                    WHERE task_id = $var_task
                      AND employee_id = $var_emp
                      AND skill_id = $var_skill";

    // Se for update, excluir o proprio registro da verificacao
    if (!empty({assignment_id})) {
        $var_sql_dup .= " AND assignment_id != " . (int){assignment_id};
    }

    sc_lookup(ds_dup, $var_sql_dup);

    if ({ds_dup} === false) {
        sc_error_message("Erro ao verificar duplicidade de atribuicao.");
        sc_error_exit();
    }

    if (!empty({ds_dup})) {
        sc_error_message("Este funcionario ja esta atribuido a esta tarefa com a mesma habilidade.");
        sc_error_exit();
    }
}
```

---

## Frente 3 — Filtros de Pendencias :white_check_mark: CONCLUIDA

### 3.1 Visao Geral

Criar **filtros avancados** no `grid_public_mro_tasks` para tarefas retidas por bloqueios.

**:white_check_mark: Implementacao Realizada (recursos nativos ScriptCase):**

Em vez de criar botoes Ajax com `sc_select_where`, os seguintes campos foram adicionados ao **filtro refinado** e **filtro avancado** nativos do ScriptCase:

| Campo | Label | Filtro |
|-------|-------|--------|
| `is_blocked_material` | Blocked Material | Checkbox / Select (true/false) |
| `is_blocked_tool` | Blocked Tool | Checkbox / Select (true/false) |
| `is_blocked_labor` | Blocked Labor | Checkbox / Select (true/false) |

Isso permite que o usuario use os filtros nativos do grid (inclusive combinando com outros campos) sem necessidade de codigo PHP personalizado. Os botoes de toggle existentes (`btn_mat`, `btn_fer`, `btn_mao`) foram mantidos para marcacao rapida de bloqueios.

**Nota:** O filtro "Retidas por Predecessora" (subquery em `mro_task_dependencies`) pode ser implementado posteriormente como um botao Ajax separado ou via um campo calculado no banco, conforme necessidade.

---

## Ordem de Execucao

### :white_check_mark: Etapa 1 — Filtros de Pendencias (CONCLUIDA)

Implementado via recursos nativos ScriptCase — campos `is_blocked_material`, `is_blocked_tool` e `is_blocked_labor` adicionados ao filtro refinado e avancado do grid. Nao foram necessarios eventos Ajax personalizados.

### :white_check_mark: Etapa 2 — Fases (CONCLUIDA)

Implementado via Select nativo ScriptCase com lookup em `mro_project_phases`. Unico codigo PHP: regra de readonly do `baseline_phase_code` no `onLoad` do form.

### Etapa 3 — Multipla Atribuicao (PENDENTE — Alta Complexidade)

| # | Acao | App/Arquivo |
|---|------|-------------|
| 3.1 | Criar app `grid_public_mro_task_assignments` | Nova app ScriptCase |
| 3.2 | Criar `schema.sql` com JOINs para nomes | `grid_public_mro_task_assignments/sql/` |
| 3.3 | Criar `onScriptInit` com filtro por `[glo_task_id]` | `grid_public_mro_task_assignments/events/` |
| 3.4 | Criar `onRecord` com status badge e barra de progresso | `grid_public_mro_task_assignments/events/` |
| 3.5 | Criar botoes `btn_novo_assignment` e `btn_editar_assignment` | `grid_public_mro_task_assignments/button/` |
| 3.6 | Ajustar `form_public_mro_task_assignments` para modo novo | `form_public_mro_task_assignments/events/` |
| 3.7 | Adicionar validacao de duplicidade no onValidate | `form_public_mro_task_assignments/events/` |
| 3.8 | Configurar mestre-detalhe no ScriptCase | Interface ScriptCase |
| 3.9 | Testar fluxo completo: adicionar, editar, remover, clock-in | Homologacao |

---

## Riscos e Pontos de Atencao

### Risco 1 (ATIVO) — Impacto no Timesheet (ALTO)

**Descricao:** O `form_public_mro_task_assignments` e a app onde o mecanico faz clock-in/clock-out. Qualquer alteracao na Frente 2 pode quebrar o apontamento de horas.

**Mitigacao:** MINIMIZAR alteracoes no form existente. Apenas adicionar suporte a `mode=new` no `onApplicationInit` e `onLoad`. NAO alterar a logica de Play/Pause/timesheet. Criar o grid como uma NOVA app separada.

---

## Sumario Final de Arquivos (Frentes 1 e 3)

### Arquivos CRIADOS

| Arquivo | Tipo |
|---------|------|
| `__Tarefas/MRO-119/PLANO-IMPLEMENTACAO.md` | Documentacao |
| `__Tarefas/MRO-119/migrations/MRO-119_01_phases_pdf_only.sql` | Migration — 22 fases do PDF |

### Arquivos EDITADOS (Frentes 1 e 3)

| Arquivo | Alteracao |
|---------|-----------|
| `tasks/form_public_mro_tasks/events/05_onLoad/onLoad.scriptcase` | + regra de readonly baseline_phase_code |
| `tasks/grid_public_mro_tasks/` | Configuracao ScriptCase — colunas, filtros, quebra (feito via interface) |

> **Nota:** Frentes 1 e 3 foram implementadas predominantemente via recursos nativos do ScriptCase (Select fields, filtro refinado/avancado, quebra), minimizando codigo PHP personalizado.

---

## Pendentes (Apos Reuniao 23/07)

- [x] ~~Inserir fases do "segundo select"~~ **Cancelado** — cliente optou por apenas PDF
- [x] ~~Inserir fases legadas do P6~~ **Cancelado** — serao mapeadas via importacao
- [x] **Decisao final (23/07):** 22 fases do PDF com descricoes `"X.Y - Descricao"`
- [x] **Frente 1 — Fases** — Concluida (Select nativo ScriptCase + onLoad readonly)
- [x] **Frente 3 — Filtros de Pendencias** — Concluida (filtro refinado/avancado nativo)

## Proximos Passos

1. [x] **Analisar documento** "Refinamento EAP+Pendencias.pdf" — concluido
2. [x] **Migration** `MRO-119_01_phases_pdf_only.sql` — concluido
3. [x] **Frente 1 — Fases** — Concluida via recursos nativos ScriptCase
4. [x] **Frente 3 — Filtros de Pendencias** — Concluida via recursos nativos ScriptCase
5. [ ] **Frente 2 — Multipla Atribuicao** — Pendente (ver detalhes na secao correspondente)

---

## Anexo — Fases do Sistema (22 fases aprovadas)

Conforme documento `Refinamento EAP+Pendencias.pdf` e decisao do cliente em 23/07/2026.

Ver arquivo `MAPA-FASES.md` para referencia completa com hierarquia e timeline.

```sql
SELECT phase_code, phase_name, sort_order FROM public.mro_project_phases ORDER BY sort_order;
```
