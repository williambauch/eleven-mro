# PLANO DE IMPLEMENTACAO - MRO-123

## Dashboard de Metricas de Encerramento e Gargalos

Painel visual (Dashboard) com metricas de encerramento e gargalos de producao filtrados por Projeto.

---

## 1. ANALISE DO ESTADO ATUAL

### 1.1 Objetivo

A gestao precisa de uma visao macro do status de um projeto selecionado. O dashboard deve focar na velocidade de entrega (tarefas encerradas) e nos bloqueios que estao atrasando o cronograma (gargalos).

**Criterios de aceite:**
1. **Filtro Global:** o painel obedece a um filtro inicial de "Projeto Selecionado".
2. **Metrica de Encerramento:** graficos/cards com a quantidade de tarefas encerradas, quebrada por DATA e por EQUIPE.
3. **Metrica de Gargalos:** graficos/cards com quantidades de tarefas paradas/impedidas, agrupadas pelo tipo de pendencias (Retida por Material, Falta de Ferramenta, Aguardando Cliente, etc.).

### 1.2 Solucao tecnica definida (do proprio MRO-123.md)

- Aplicacao tipo **Control** passa os parametros de filtro para uma aplicacao tipo **Blank**.
- A Blank consulta as tabelas `mro_tasks`, `mro_task_assignments` e `mro_task_resources` para gerar as metricas.
- Graficos com a biblioteca externa `_Bibliotecas_Externas/lib_syncfusion`.
- Se necessario exibir grid de dados, usar iframe com aplicacoes do tipo grid criadas com selects especiais.
- SQLs validados via MCP (dbhub) — `.vscode/mcp.json`.

### 1.3 Referencias de padrao ja existentes no projeto

| Referencia | O que aproveitar |
|---|---|
| `ferramentaria/dash_ferramentaria` | Padrao completo de dashboard: `sc_lookup` no `onExecute` + JSON injetado no JS + `ej.charts.Chart` / `ej.charts.AccumulationChart` + fallback "Sem dados" |
| `gantt/blank_gantt` | Padrao de captura do filtro de projeto: `$project_filter = [var_project_id]` no `01_onExecute` |
| `Kanban/blank_kanban_board` | Mesmo padrao de filtro + inclusao dos 3 arquivos Syncfusion no `<head>` |
| `compras/control_import_empenhos` | Padrao de app Control com `[var_project_id]` vindo da sessao |
| `tasks/control_split_assignment` | Padrao de Control com `sc_begin_trans`/`sc_commit_trans` (se necessario) |
| `__Tarefas/MRO-122/migrations/MRO-122_permissions_blank_abertura_nrc.sql` | Padrao de migration de permissoes: registrar app em `sec_apps` + copiar permissoes de grupo |

### 1.4 Estrutura de dados validada (via MCP no banco real)

**Tabela `mro_tasks` (61 colunas) — campos relevantes:**
- `task_id`, `task_code`, `task_name`, `task_type`, `is_nrc`, `parent_task_id`, `root_task_id`
- `status_code` (15 valores em uso: PLANNED 14.383, NOT_STARTED 2.546, COMPLETED 1.724, RELEASED 43, etc.)
- `project_id`, `phase_code`, `skill_code` (**vazio nas tasks importadas do P6**)
- Flags de bloqueio: `is_blocked_material` (274), `is_blocked_tool` (2), `is_blocked_labor` (33), `is_blocked_predecessor` (620)
- `pending_id` (FK para `mro_tasks_pending_status` — **apenas 2 tasks usam hoje**)
- `actual_start`/`actual_end` (**vazios em TODAS as 1.724 tasks COMPLETED**)
- `created_at`, `updated_at`, `target_start`, `target_end`, `baseline_start`, `baseline_end`

**Tabela `mro_task_assignments` (17 colunas):**
- `assignment_id`, `task_id`, `project_id`, `skill_id`, `employee_id`, `supervisor_id`
- `status_code` (ASSIGNED, NOT_STARTED, IN_PROGRESS, PAUSED, BLOCKED, PENDING_HANDOVER, SUPSIG — **nenhum COMPLETED ainda**)
- `actual_qty_hours`, `planned_qty_hours`, `updated_at`

**Tabela `mro_task_resources` (6 colunas):**
- `allocation_id`, `project_id`, `task_id`, `resource_code`, `budgeted_hours`, `created_at`
- **Mistura LABOR (skills S4/A4/M4...) com materiais (part numbers)** — precisa filtrar

**Tabelas auxiliares:**
- `mro_skills`: `skill_id`, `skill_code` (S4 Sistemas, A4 Avionica, M4 Motores, E4 Estruturas, I4 Interiores, P4 Pintura, N4 NDT, MO Producao, GSE4...), `description`
- `mro_resources`: `resource_code`, `resource_type` ('LABOR' por padrao), `resource_name`
- `mro_tasks_pending_status` (55 tipos de pendencias: PARTS, TOOLS, CUSTOMER, APROV, ENG DEFINI, PREDEC, etc.)
- `mro_projects` (20 projetos; 2 = CHECK C - PR-GUO, 6 = MODELO EMB 190, 7 = TOT03/25)
- `mro_timesheet`: `end_time`, `status` ('COMPLETED' = 8 registros reais) — **fonte de data real de encerramento**
- `mro_assignment_events`: `event_type` (START/PAUSED/BLOCKED/SUPSIG), `event_timestamp` — auditoria

---

## 2. DECISOES DE DESIGN

### 2.1 Localizacao das aplicacoes

**DEFINIDO:** pasta `reports/` (decisao do lider).

```
reports/
├── control_dash_encerramento/     (app Control — filtro de projeto)
└── blank_dash_encerramento/       (app Blank — graficos e cards Syncfusion)
```

### 2.2 Arquitetura Control -> Blank

- **`reports/control_dash_encerramento`** (tipo Control):
  - Select de **projeto** + campos de **periodo de encerramento** (data inicial e data final) + botao "Gerar Dashboard".
  - `09_onValidateSuccess`: valida projeto selecionado, grava **`[glo_project_id]`**, **`[glo_periodo_inicial]`** e **`[glo_periodo_final]`** com `sc_set_global` e redireciona com `sc_redir(blank_dash_encerramento)` (decidido no checklist 6.4).
- **`reports/blank_dash_encerramento`** (tipo Blank):
  - `01_onExecute`: captura **apenas `[glo_project_id]`** (definido pelo Control antes do `sc_redir` — nao usar `[var_project_id]` para a mesma info), `[glo_periodo_inicial]` e `[glo_periodo_final]` (fallback ultimos 30 dias), executa as consultas de metricas e gera os JSONs.
  - Inclui os 3 arquivos Syncfusion no `<head>` (material3.css + ej2-syncfusion.js + mro_config.js).
  - Renderiza cards KPI + graficos com `ej.charts.Chart` (colunas/barras) e `ej.charts.AccumulationChart` (rosca/pizza), seguindo o padrao da `dash_ferramentaria`.
  - Fallback "Sem dados" em todos os graficos (padrao `dash_ferramentaria`).

### 2.3 Metricas definidas

**Cards KPI (linha superior):**
- Total de tarefas do projeto
- Tarefas em execucao (IN_PROGRESS + RELEASED)
- Tarefas bloqueadas (soma das flags + pending)
- Tarefas encerradas (COMPLETED)

**Grafico 1 — Encerramento por DATA (Colunas):**
- Fonte principal: **`mro_tasks.actual_end`** (preenchido manualmente pela equipe DIGEX — decidido no checklist 6.1).
- Fallbacks: `mro_task_assignments.updated_at` (mudanca de status) e `mro_timesheet.end_time` (encerramento real de producao).
- Janela definida pelo **periodo configuravel no Control** (`[glo_periodo_inicial]` e `[glo_periodo_final]` — datas de inicio e fim).

**Grafico 2 — Encerramento por EQUIPE (Barras horizontais):**
- Fonte: `mro_task_assignments.skill_id` (chave de juncao) -> **exibicao por `mro_skills.skill_code`** (S4, A4, M4...) — decidido no checklist 6.2.
- Fallback: `mro_task_resources.resource_code` filtrando por `mro_resources.resource_type = 'LABOR'`.

**Grafico 3 — Gargalos por tipo de pendencia (Rosca/Pizza):**
- **Dois filtros independentes** (decidido no checklist 6.3):
  - **Filtro bloqueios (flags booleanas):** `is_blocked_material` -> "Retida por Material", `is_blocked_tool` -> "Falta de Ferramenta", `is_blocked_labor` -> "Falta de Mao de Obra", `is_blocked_predecessor` **ou** `is_predecessor_manual` -> "Aguardando Predecessora".
  - **Filtro pendencias (pending_id):** agrupado por `mro_tasks_pending_status.pending_description` (PARTS, TOOLS, CUSTOMER, ENG DEFINI, PREDEC...) — poucas tasks hoje (implantacao recente), mas o dashboard ja nasce preparado.

**Grid opcional (iframe):**
- Top tarefas bloqueadas (task_code, descricao, tipo de pendencia, dias parado).
- Se necessario, criar grid com select especial ou usar iframe de `grid_public_mro_tasks` com `var_project_id`.

---

## 3. RISCOS E DEPENDENCIAS

| Risco | Impacto | Mitigacao |
|-------|---------|-----------|
| Nenhuma task COMPLETED tem `actual_end` preenchido (dados vem da importacao P6) | ALTO — metrica de encerramento por data ficaria vazia | Usar `mro_task_assignments.updated_at` e `mro_timesheet.end_time` como fontes de data real; fallback "Sem dados" |
| `mro_task_resources` mistura LABOR e materiais (part numbers) | MEDIO — equipe por recurso ficaria errada | Filtrar por `mro_resources.resource_type = 'LABOR'` ou usar `mro_task_assignments.skill_id` como fonte principal |
| `mro_tasks.skill_code` vazio nas tasks do P6 | MEDIO — grafico por equipe sem dados | Usar `mro_task_assignments.skill_id -> mro_skills` |
| Gargalos dispersos entre flags e `pending_id` | MEDIO — rosca de gargalos pode ficar vazia | **DECIDIDO:** dois filtros independentes — Filtro 1 (flags booleanas + `is_predecessor_manual`) e Filtro 2 (`pending_id` -> descricao) |
| Poucos dados reais de producao (assignments COMPLETED = 0, timesheet COMPLETED = 8) | MEDIO — graficos com pouco volume | Fallback "Sem dados" padrao; testar com projeto 7 (1.169 COMPLETED) e 6 (499) |
| `actual_end` preenchido manualmente pela DIGEX (sem gravacao automatica) | MEDIO — dados so aparecem apos a DIGEX informar | **DECIDIDO:** hierarquia de fontes — `actual_end` principal, fallback assignments.updated_at e timesheet.end_time |
| `[glo_project_id]` / `[glo_periodo_inicial]` / `[glo_periodo_final]` precisam ser criados como variaveis globais no ScriptCase IDE | MEDIO — blank nao recebe o filtro | Documentar no plano; Control grava via `sc_set_global` antes do `sc_redir` |
| Permissoes (ACL) das novas apps | MEDIO — usuarios nao acessam o dashboard | Migration SQL registrando em `sec_apps` + copiando permissoes de grupo (padrao MRO-122) |
| Inclusao no menu | BAIXO — dashboard nao aparece | Registrar item no menu (padrao `menu_tree.md`) — definir com lider |
| Timezone/datas (banco armazena timestamps sem timezone) | BAIXO | Usar `TO_CHAR(..., 'DD/MM')` ou `::date` no SQL; nao depender de NOW() local |

---

## 4. ORDEM DE EXECUCAO RECOMENDADA

| Ordem | Atividade | App/Tabela |
|:-----:|-----------|------------|
| 1 | Definir consultas SQL de metricas e valida-las via MCP (dbhub) | `mro_tasks`, `mro_task_assignments`, `mro_tasks_pending_status`, `mro_timesheet`, `mro_skills` |
| 2 | Criar app Blank `reports/blank_dash_encerramento` (onExecute com consultas + graficos Syncfusion) | `blank_dash_encerramento` (nova) |
| 3 | Criar app Control `reports/control_dash_encerramento` (select de projeto + sc_set_global + sc_redir) | `control_dash_encerramento` (nova) |
| 4 | Migration de permissoes (sec_apps + sec_groups_apps) | `migrations/MRO-123_permissions.sql` |
| 5 | Registrar item no menu (definir com lider) | `sec_menu` / `menu_tree.md` |
| 6 | Testes funcionais (projetos 2, 6 e 7) e validacao visual dos graficos | - |

---

## 5. MIGRACOES PREVISTAS

| Migration | Descricao | Status |
|-----------|-----------|:------:|
| `migrations/MRO-123_permissions.sql` | Registrar `control_dash_encerramento` e `blank_dash_encerramento` em `sec_apps` (app_type = 'control'/'blank') e copiar permissoes de grupo de app existente | PENDENTE |
| Alteracao de schema | Nenhuma prevista — as tabelas ja possuem todos os campos necessarios (flags, pending_id, skill_id, timesheet) | NAO APLICAVEL |

---

## 6. CHECKLIST DOS PONTOS CRITICOS DE ATENCAO

> Checklist para resolvermos em conjunto antes/durante a implementacao. Marcar com [x] quando resolvido.

### 6.1 Fonte de dados de ENCERRAMENTO

- [x] **DECIDIDO:** fonte de data de encerramento = **`mro_tasks.actual_end`** quando preenchido. O fluxo de producao **NAO grava** este campo automaticamente — ele sera **informado manualmente pela equipe da DIGEX**.
  - Hierarquia de fontes: (a) `mro_tasks.actual_end` (principal, preenchido pela DIGEX); (b) fallback `mro_task_assignments.updated_at`; (c) fallback `mro_timesheet.end_time`.
- [x] Confirmar se o fluxo de producao (control_pause_task rota 6 -> SUPSIG -> signoff) ja grava `actual_end` em `mro_tasks`. **DECIDIDO: NAO grava** — preenchimento manual pela equipe DIGEX; esta tarefa NAO altera o fluxo de producao.
- [x] **DECIDIDO:** janela de tempo do grafico por data = **periodo configuravel no Control, junto com o projeto** — novos globais **`[glo_periodo_inicial]`** e **`[glo_periodo_final]`** (datas de inicio e fim).

### 6.2 Fonte de dados de EQUIPE

- [x] **DECIDIDO:** fonte principal = **`mro_task_assignments.skill_id`** como chave de juncao; **exibir `mro_skills.skill_code`** (S4, A4, M4...) no grafico (skill_id e apenas a chave da tabela).
- [ ] Validar se as skills (S4, A4, M4, E4, I4, P4, N4, MO, GSE4, DIGEX) cobrem as equipes que a gestao quer enxergar, ou se quer agrupamento por time/equipe de turno.

### 6.3 Fonte de dados de GARGALOS

- [x] **DECIDIDO: dois filtros independentes no dashboard:**
  - **Filtro 1 — Bloqueios (flags booleanas):** `is_blocked_material`, `is_blocked_tool`, `is_blocked_labor`, `is_blocked_predecessor` **+ `is_predecessor_manual`** (620 + manual).
  - **Filtro 2 — Pendencias (pending_id):** agrupado por `mro_tasks_pending_status.pending_description` (55 tipos). Poucas tasks hoje (2) porque e implantacao recente, mas o dashboard ja nasce preparado.
- [ ] Decidir se tarefas `BLOCKED` (status_code) sem flag/pending entram no grafico de gargalos (hoje 6 assignments BLOCKED no fluxo de producao).
- [ ] Definir se o grafico de gargalos mostra tarefas paradas em qualquer status ou apenas em status de execucao (IN_PROGRESS/PAUSED/BLOCKED).

### 6.4 Integracao Control -> Blank (filtro de projeto)

- [x] **DECIDIDO:** o Control (filtro) gera **`[glo_project_id]`** (projeto), **`[glo_periodo_inicial]`** e **`[glo_periodo_final]`** (periodo de encerramento) via `sc_set_global`; a Blank consome **apenas os globais** (sem `[var_project_id]` — nao faz sentido ter duas variaveis para a mesma informacao).
- [ ] Definir comportamento quando nao ha projeto selecionado (bloquear com mensagem? default projeto 2 como `blank_kanban_board`?).
- [ ] Validar se `[glo_project_id]` ja e usado em outras apps com o mesmo nome (para nao conflitar sessao).

### 6.5 Permissoes e menu

- [ ] Definir quais grupos de usuarios podem acessar o dashboard (GERENCIA? SUPERVISOR? PLANEJAMENTO?).
- [ ] Definir se entra no menu e em qual posicao (ex: "Producao e Manutencao > Dashboard de Encerramento").
- [ ] Migration de permissoes deve copiar de qual app existente? (sugestao: `tabs_supervisor` ou `dash_ferramentaria`).

### 6.6 Biblioteca Syncfusion

- [x] Confirmar copia local da biblioteca: **`C:\xampp\htdocs\MRO_System\_Bibliotecas_Externas\lib_syncfusion`** (3 arquivos: `styles/material3.css`, `scripts/ej2-syncfusion.js`, `scripts/mro_config.js` — licenca registrada em `mro_config.js`) — usar como referencia de modulos e licenca.
- [ ] Validar que o bundle `ej2-syncfusion.js` inclui `ej.charts.Chart` e `ej.charts.AccumulationChart` com os modulos necessarios (ColumnSeries, BarSeries, PieSeries, Tooltip, DataLabel) — confirmar no `dash_ferramentaria` (ja funciona).
- [ ] Confirmar que o acesso aos 3 arquivos via `/libs/syncfusion/...` funciona no ambiente real do ScriptCase (o caminho local `_Bibliotecas_Externas/lib_syncfusion` precisa estar publicada como `libs` no servidor).

### 6.7 Testes

- [ ] Validar metricas com dados reais: projeto 7 (TOT03/25 — 1.169 COMPLETED), projeto 6 (MODELO EMB 190 — 499 COMPLETED), projeto 2 (CHECK C - PR-GUO — 1.678 tasks, flags de bloqueio presentes).
- [ ] Testar dashboard com projeto sem dados (ex: projeto 3) — deve exibir "Sem dados" sem quebrar.
- [ ] Testar responsividade/altura dos graficos em tela cheia (padrao `dash_ferramentaria` usa altura 130px nos cards).

---

## 7. CONSULTAS SQL PROPOSTAS (para validacao via MCP)

### 7.1 Projetos para o filtro

```sql
SELECT project_id, project_name, p6_proj_id, project_status
FROM mro_projects
ORDER BY project_status, project_name;
```

### 7.2 KPI — totais por projeto

```sql
SELECT
    COUNT(*) AS total_tarefas,
    COUNT(*) FILTER (WHERE status_code IN ('IN_PROGRESS','RELEASED')) AS em_execucao,
    COUNT(*) FILTER (WHERE status_code = 'COMPLETED') AS encerradas,
    COUNT(*) FILTER (WHERE status_code IN ('BLOCKED','PENDING','PENDING_OA','PENDING_ENG','PENDING_PROG','PENDING_COORD','COMMERCIAL_REVIEW','WAITING_CUSTOMER')
        OR is_blocked_material OR is_blocked_tool OR is_blocked_labor OR is_blocked_predecessor
        OR pending_id IS NOT NULL) AS bloqueadas
FROM mro_tasks
WHERE project_id = :projeto;
```

### 7.3 Encerramento por data (actual_end principal + fallbacks)

```sql
-- Fonte principal: mro_tasks.actual_end (preenchido manualmente pela DIGEX)
-- Periodo: :data_inicial e :data_final (vindos de [glo_periodo_inicial] e [glo_periodo_final] do Control)
SELECT TO_CHAR(d.data_base, 'DD/MM') AS data_encerramento, COUNT(*) AS qtde
FROM (
    SELECT actual_end AS data_base
    FROM mro_tasks
    WHERE project_id = :projeto
      AND status_code = 'COMPLETED'
      AND actual_end IS NOT NULL
      AND actual_end::date >= :data_inicial::date
      AND actual_end::date <= :data_final::date
    UNION ALL
    SELECT updated_at AS data_base
    FROM mro_task_assignments
    WHERE project_id = :projeto
      AND status_code IN ('COMPLETED','SUPSIG','PENDING_HANDOVER')
      AND updated_at::date >= :data_inicial::date
      AND updated_at::date <= :data_final::date
    UNION ALL
    SELECT ts.end_time AS data_base
    FROM mro_timesheet ts
    JOIN mro_task_assignments a ON ts.assignment_id = a.assignment_id
    WHERE a.project_id = :projeto AND ts.status = 'COMPLETED'
      AND ts.end_time::date >= :data_inicial::date
      AND ts.end_time::date <= :data_final::date
) d
GROUP BY d.data_base
ORDER BY d.data_base;
```

### 7.4 Encerramento por equipe (skill)

```sql
SELECT COALESCE(s.skill_code, 'SEM SKILL') AS equipe, COUNT(*) AS qtde
FROM mro_task_assignments a
LEFT JOIN mro_skills s ON a.skill_id = s.skill_id
WHERE a.project_id = :projeto
  AND a.status_code IN ('COMPLETED','SUPSIG','PENDING_HANDOVER')
GROUP BY s.skill_code
ORDER BY qtde DESC;
```

### 7.5 Gargalos por tipo de pendencia (dois filtros independentes)

```sql
-- FILTRO 1: Bloqueios (flags booleanas) — inclui is_predecessor_manual
SELECT
    CASE
        WHEN is_blocked_material THEN 'Retida por Material'
        WHEN is_blocked_tool THEN 'Falta de Ferramenta'
        WHEN is_blocked_labor THEN 'Falta de Mao de Obra'
        WHEN is_blocked_predecessor OR is_predecessor_manual THEN 'Aguardando Predecessora'
        ELSE 'Outros'
    END AS tipo_pendencia,
    COUNT(*) AS qtde
FROM mro_tasks
WHERE project_id = :projeto
  AND (is_blocked_material OR is_blocked_tool OR is_blocked_labor
       OR is_blocked_predecessor OR is_predecessor_manual)
GROUP BY tipo_pendencia
ORDER BY qtde DESC;

-- FILTRO 2: Pendencias (pending_id) — agrupado pela descricao
SELECT p.pending_code, p.pending_description, COUNT(*) AS qtde
FROM mro_tasks t
JOIN mro_tasks_pending_status p ON t.pending_id = p.pending_id
WHERE t.project_id = :projeto
GROUP BY p.pending_code, p.pending_description
ORDER BY qtde DESC;
```
