# Gantt de Fases — Guia para Reunião (Origem de Dados e Comportamento)

> Documento de apoio para reunião sobre o **blank_gantt** (MRO-126).
> Objetivo: explicar de onde vêm os dados, como o Gantt se comporta e quais pontos de negócio precisam de decisão.

---

## 1. Visão geral

| Item | Descrição |
|---|---|
| Aplicação | `gantt/blank_gantt` (Blank com Syncfusion Gantt) |
| O que mostra | Hierarquia **PROJETO > FASE OPERACIONAL > TAREFA > NRC** na timeline |
| Quem usa | Planejamento / Engenharia (grupos com acesso à blank) |
| Edição permitida | Arrastar barra (datas), editar nome na célula, arrastar para outra fase (rotinas) |
| Edição bloqueada | Add/Delete (CRUD é do `form_public_mro_tasks`); linhas de PROJETO/FASE não são arrastáveis |
| Filtro de entrada | `glo_project_id` (1 projeto) ou `glo_project_ids` (lista, ex.: `2,3`) |

---

## 2. Origem dos dados (cadeia completa)

```
mro_projects (PROJETO)
    ├── mro_project_phases (FASES - phase_code + sort_order)
    ├── mro_tasks (TAREFAS/NRC)
    │     ├── mro_task_dependencies (PREDECESSORAS - dependências)
    │     └── mro_tasks.status_code (progresso derivado)
    └── view_gantt_datasource (VIEW que monta a hierarquia)
            └── blank_gantt (SELECT na view + JSON p/ Syncfusion)
```

### 2.1 A view `view_gantt_datasource`

Monta **3 blocos (UNION ALL)**:

| Bloco | Linha gerada | Campos de data usados |
|---|---|---|
| 1º | **PROJETO** (`P{id}`) | `mro_projects.start_date / end_date` |
| 2º | **FASE** (`F{projeto}_{fase}`) | sem data própria (as barras de fase são agrupamento) |
| 3º | **TAREFA/NRC** (`{task_id}`) | `COALESCE(target_start, baseline_start)` e `COALESCE(target_end, baseline_end)` |

**Regras da view:**
- `id_unico` do PROJETO = `P{project_id}`; da FASE = `F{project_id}_{phase_code}`; da TAREFA = `task_id`
- `id_pai` da TAREFA = `parent_task_id` (se NRC de rotina) ou `F{projeto}_{fase}` (se direto)
- FASE sem `phase_code` na task → vira `UNASSIGNED` ("Sem Fase Atribuída")
- **Progresso derivado do status**: COMPLETED=100, IN_PROGRESS=50, demais=0 (não é editável no Gantt)
- **Predecessoras** = agregado de `mro_task_dependencies` por task

### 2.2 Datas que aparecem na barra (PONTO IMPORTANTE)

Para TAREFA/NRC a view usa na ordem:

1. **`target_start` / `target_end`** → planejamento real (o que o usuário arrasta no Gantt)
2. **`baseline_start` / `baseline_end`** → baseline importado do P6 (**fallback**, só quando não há planejamento)

> ⚠️ **Problema conhecido:** tarefas sem `target_*` caem na baseline antiga do P6 (ex.: projeto 22 mostra barras em 2024/2025). Isso estica a timeline e desloca o scroll. **Decisão pendente na reunião**: manter baseline como fallback ou usar o período do projeto.

---

## 3. Como o Gantt abre (fluxo de filtro)

1. A aplicação de origem (ex.: `grid_public_mro_projects` ou outra) define a variável global:
   - `sc_set_global(glo_project_ids=2,3)` (multi) **ou**
   - `sc_set_global(glo_project_id=2)` (único, compatibilidade)
2. `sc_redir('blank_gantt')` abre a blank.
3. O `blank_gantt` lê as globals, monta o `WHERE` (`project_id = X` ou `project_id IN (2,3)`).
4. Executa o SELECT na view e injeta o JSON no Syncfusion (`ganttData`).
5. **Scroll inicial automático** (`dataBound` + `scrollToDate`):
   - Hoje dentro do range das tarefas → rola até hoje;
   - Hoje fora do range → rola até o início do projeto mais recente;
   - Fallback → início da timeline.

---

## 4. Edição pelo Gantt (o que persiste)

| Ação | O que grava | Onde |
|---|---|---|
| **Arrastar barra** (mover datas) | `target_start` / `target_end` | `mro_tasks` |
| **Editar nome na célula** | `task_name` | `mro_tasks` |
| **Arrastar para outra fase** (rotina) | `phase_code` (validado em `mro_project_phases`) | `mro_tasks` |
| Auditoria | `GANTT_EDIT` + remarks (`nome, inicio, fim`, etc.) | `mro_task_history` |
| Log | `sc_log_add` (`gantt_save`) | `sc_log` |

**Bloqueios:**
- `parent_task_id` **não** é alterado pelo Gantt (rastreabilidade da NRC é do form).
- NRC não muda de fase pelo arrasto (segue a fase da rotina-mãe).
- Linhas PROJETO/FASE têm `pointer-events: none` (não arrastam, não dão alerta).
- Add/Delete desabilitados (`allowAdding: false`, `allowDeleting: false`).
- Duplo clique na barra de TAREFA abre o `form_public_mro_tasks` em modal (edição completa).

---

## 5. Comportamentos validados (testes em 19/08/2026)

| Cenário | Resultado |
|---|---|
| Drag task com planejamento (1793: 22/04 → 23/04) | ✅ salvo + histórico |
| Drag task sem planejamento (2621: null → 27/02) | ✅ criou `target_*` |
| Drag task projeto único (1804: 10/02 → 12/02) | ✅ salvo + histórico |
| Drag em PROJETO/FASE | ✅ bloqueado (sem mover, sem alerta) |
| Scroll inicial multi-projeto (2,3) | ✅ abriu em 16/04 (projeto mais recente) |
| Scroll projeto com baseline antiga (22) | ⚠️ range gigante (2024→2026) — ver seção 2.2 |

---

## 6. Perguntas sugeridas para a reunião (decisões de negócio)

1. **Baseline como fallback**: tarefas sem `target_*` devem mostrar a baseline do P6 ou o período do projeto? (hoje mostra baseline → timeline grande em projetos novos)
2. **Progresso no Gantt**: o % é derivado do status (COMPLETED=100, IN_PROGRESS=50). Deve ser editável no Gantt ou manter derivado?
3. **Fases**: a fase `UNASSIGNED` ("Sem Fase Atribuída") deve existir? As tarefas sem fase aparecem lá — ou deveriam ser bloqueadas/alertadas para o planejador atribuir fase?
4. **Predecessoras**: as setas de dependência são exibidas, mas a validação de "liberação por predecessora" fica no form. O Gantt deve bloquear arrasto que crie sobreposição/conflito com dependência?
5. **NRC e fases**: NRC não muda de fase pelo Gantt (regra atual). Confirmar se isso atende o fluxo ou se o arrasto de NRC para outra fase deve ser permitido.
6. **Período de exibição**: em multi-projeto o range pode ficar grande (ex.: fev→set). Desejável limitar a timeline ao período dos projetos selecionados?
7. **Quem edita**: hoje qualquer usuário com acesso à blank pode arrastar. Deve haver restrição por perfil (ex.: só PLANEJAMENTO) ou por status da task (ex.: RELEASED não edita)?

---

## 7. Referência rápida (campos e macros)

| Item | Valor |
|---|---|
| View | `view_gantt_datasource` |
| Tabela principal | `mro_tasks` (datas `target_start/target_end`, fase `phase_code`, pai `parent_task_id`) |
| Projetos | `mro_projects` (`project_id`, `p6_proj_id`, `start_date`, `end_date`) |
| Fases | `mro_project_phases` (`phase_code`, `phase_name`, `sort_order`) |
| Dependências | `mro_task_dependencies` (`predecessor_task_id`, `successor_task_id`, `dep_type`) |
| Auditoria de edição | `mro_task_history` (action `GANTT_EDIT`) |
| Globals de filtro | `[glo_project_ids]` / `[glo_project_id]` |
| Login/empregado | `[usr_login]`, `[usr_employee_id]` |
