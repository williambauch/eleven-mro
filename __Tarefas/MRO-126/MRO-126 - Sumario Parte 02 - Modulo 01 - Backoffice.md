# Revisão Chão de Fábrica & Backoffice — Parte 02 — Módulo 01 (Backoffice)

> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice, separado por módulos de trabalho.
>
> - **Módulo 01 — Backoffice** (este arquivo): Gantt e Kanban
> - [Módulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2002%20-%20Operacoes.md)
> - [Módulo 03 — Logística & Ferramentaria (Almoxarifado)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2003%20-%20Logistica%20Ferramentaria.md)

---

## Tarefa do Modulo 01 — Core PM & Engenharia de Dados (Backoffice)

Revisão do Gantt e Kanban:

Gantt sendo gerado com todos os recursos do Syncfusion. Implementar a edição da tarefa direta pelo Gantt

Kambam por Fase e Status da Tarefa

Ambos podendo ser gerado para 1 ou mais projetos (hoje ele é acionado apenas pelo grid de projetos.

---

## Sumario das alteracoes Modulo 01 — Core PM & Engenharia de Dados (Backoffice)

## `blank_gantt`

### Refatoracao do Gantt de Fases (Syncfusion) e edicao de planejamento

**`events/onExecute`**
- Regra de negocio: salva no banco ao **arrastar a barra** (`target_start`/`target_end`) e permite **editar `task_name`** direto no Gantt
- Regra de negocio: **duplo clique na barra** abre modal com o `form_public_mro_tasks` (edicao completa) e recarrega ao fechar
- Regra de negocio: `task_code`, coluna `%` e `parent_task_id` (rastreabilidade da NRC) nao sao editaveis; arrasto para outra fase atualiza `phase_code` apenas em **rotinas** (NRCs seguem a rotina de origem)
- Sanitizacao de ids de fase (espaco/barra -> `_`) para o Syncfusion renderizar (`A OPEN`, `OP/FUNC`); suporte a **1 ou mais projetos**

---

## `form_public_mro_tasks`

### Validacao de datas de planejamento quando fase definida

**`events/onValidate`**
- Regra de negocio: **ROTINA** com fase definida exige `target_start`/`target_end` (evita task com fase mas sem datas no Gantt)
- Regra de negocio: NRCs seguem a fase da rotina de origem (nao validadas); trata valor `'null'` como vazio

---

## `grid_public_mro_projects`

### Botoes RUN para gerar Gantt/Kanban multi-projeto

**`button/btn_gantt_multi` e `button/btn_kanban_multi`**
- Regra de negocio: botao RUN acumula o `project_id` dos registros selecionados e redireciona para a `blank_gantt` / `blank_kanban_critical` com `glo_project_ids`
- Regra de negocio: exige selecao de ao menos um projeto; lista sanitizada e sem duplicados
- Configurados no IDE (selecao multipla + barra de acao, 14/08/2026)

---

## `blank_kanban_critical`

### Kanban por Fase e Status da Tarefa (Syncfusion) — multi-projeto

**`events/onExecute`**
- Regra de negocio: colunas por status da task + swimlanes por Projeto > Fase Operacional, suportando **1 ou mais projetos** (validado com 2 e 4 — 5 swimlanes, 2474 cards)
- Regra de negocio: card com flag de bloqueio (material/ferramenta/mao de obra/predecessora) exibe **badge vermelho**; NRC identificada com tag **NR**
- Regra de negocio: **duplo clique no card** abre o `form_public_mro_tasks` em modal; drag desativado; toolbar **"Phases"** com expandir/recolher; contadores so com numero; tippy com status agrupados no header


---

## `__Tarefas/MRO-126/migrations`

### Preenchimento de datas de planejamento do projeto 2

**`MRO-126_preencher_datas_projeto2.sql`**
- Migration que distribui `target_start`/`target_end` nas tasks sem data do projeto 2 (MODELO 737NG), com duracao baseada em `estimated_hours`
- Regra de negocio: NRCs herdam as datas da rotina de origem (`parent_task_id`) quando a rotina tiver data

---

## Pendências do Modulo 01
- Nenhuma pendente — modulo concluido.
