# DEFINICAO DA TAREFA

Resumo:
A entidade principal de Tarefas precisa ser expandida para melhorar o planejamento e a alocação. É necessário incluir a rastreabilidade das Fases do projeto, permitir que mais de um mecânico/funcionário seja atribuído à mesma tarefa e criar filtros rápidos para identificar gargalos operacionais.

Critérios de Aceite:

Novos Campos: Adicionar os campos "Fase Baseline" (baseline_phase_code ) e "Fase" (phase_code) (buscando da tabela mro_project_phases) na interface da Tarefa.

Grid e Pesquisa: Incluir esses dois novos campos nos filtros de pesquisa e habilitar a "quebra" (agrupamento) por eles nos grids.

Filtros de Pendências: Criar botões/filtros rápidos no grid de tarefas para listar: Tarefas retidas por predecessoras, Tarefas com falta de ferramentas, e Tarefas com falta de materiais.

Notas Técnicas:

Adequar o banco de dados (tabela pivot para responsáveis se necessário, em vez de FK única na mro_tasks).

Observação:
no sistema quem delega as tarefas para mecanico/funcionario é o usuário do grupo supervisor. Para isso ele usa o conjunto e aplicações abaixo:
tabs_supervisor
form_public_mro_task_assignments_planned
form_public_mro_task_assignments_progress/
form_public_mro_task_assignments_blocked
form_public_mro_task_assignments_completed


# Rodrigo Souza - Nova Demanda 28/07/2026
### filtro de tarefas “com impedimento” 
Precisa incluir um filtro de tarefas “com impedimento” de tarefa predecessora, ou seja, a tarefa atual está impedida de seguir se tiver uma predecessora amarrada a ela que ainda não foi concluída.

pensar numa lógica, ao criar ou importar ou associar uma tarefa predecessora, a sucessora tem que ficar marcada com algo do tipo “bloqueio predecessora”.

E ao concluir a atividade predecessora, liberar a atividade sucessora.

revisar lógica de “Liberar Tarefa” para só liberar se não tiver nenhum bloqueio

dep_type	Significado	Registros
FS =	Finish-to-Start (Término-Início) — A sucessora só pode começar quando a predecessora terminar	1.198 (99,75%)
SS =	Start-to-Start (Início-Início) — A sucessora pode começar junto com a predecessora	3 (0,25%)

### incluir todos esses campos no grid grid_public_mro_tasks e nos filtros
Is Critical Path
Is Rii
Requires RII
Is Blocked Material
Is Blocked Tool
Is Blocked Labor


## Sumário das alterações implementadas - WILLIAM BAUCH

---

## `mro_project_phases`

### Limpeza e recadastro das fases conforme PDF Refinamento

**`migrations/MRO-119_01_phases_pdf_only.sql`**
- Reuniao com cliente em 23/07/2026 decidiu **descartar** os codigos legados do P6 (INSP, FMRO, INREC, LUBR, FTEST, FACS, DEL, F1M, FINSP, AACS, IND)
- Decisao: manter **apenas** as 22 fases do documento oficial `Refinamento EAP+Pendencias.pdf`
- Migration executado: `DELETE FROM` todos os registros antigos + `INSERT` das 22 fases aprovadas

**`migrations/MRO-119_02_control_split_assignment_permissions.sql`**
- Registra `control_split_assignment` na tabela `sec_apps` (app_type = control)
- Copia as permissoes de grupo da app `form_public_mro_task_assignments_planned` para `control_split_assignment` em `sec_groups_apps`

---

## `form_public_mro_tasks` — Formulário principal de edição de tarefas (criação, alteração, abas de fases, dependências e dados operacionais)

### Exposicao dos campos phase_code e baseline_phase_code com lookup

- Campos `phase_code` e `baseline_phase_code` (varchar 50) ja existiam na tabela `mro_tasks` e foram exibidos no form na aba "Datas e Status"

**Regras de editabilidade:**
- `phase_code` → **sempre editavel**, permite alterar a fase atual da tarefa a qualquer momento
- `baseline_phase_code` → **editavel apenas enquanto DRAFT e Novo Registro**. Apos a task sair de DRAFT (qualquer outro status), o campo fica readonly. O baseline congela o plano original da tarefa e nao deve mais ser alterado

---

## `grid_public_mro_tasks` — Grid principal de consulta, filtros refinados, agrupamento por fases e liberação de tarefas para execução

### Campos adicionados ao filtro refinado e avancado

Os seguintes campos foram adicionados ao **filtro refinado** e ao **filtro avançado** do grid:

| Campo | Label |
|---|---|
| `phase_code` | Phase Code |
| `baseline_phase_code` | Baseline Phase Code |
| `is_blocked_tool` | Blocked Tool |
| `is_blocked_labor` | Blocked Labor |
| `is_blocked_material` | Blocked Material |
| `is_blocked_predecessor` | Blocked Predecessor |
| `is_critical_path` | Is Critical Path |
| `is_rii` | Is Rii |
| `requires_rii` | Requires Rii |

### Quebra dinamica (agrupamento) por fases

- Adicionado recurso de **quebra dinamica** para os campos `baseline_phase_code` (Fase Baseline) e `phase_code` (Fase), permitindo agrupar tarefas por fase no grid

### Badge de bloqueio por predecessora no onRecord

**`events/onRecord`**
- Adicionado badge `predecessor_badge` com icone `fa-link-slash` e estilo vermelho (`#fce4ec` / `#c62828`) quando `is_blocked_predecessor = true`
- Exibe tooltip "Bloqueada por tarefa predecessora"

### Validacao de bloqueio no btn_liberar_para_execucao

**`button/btn_liberar_para_execucao`**
- Adicionada validacao no inicio: se `is_blocked_predecessor = true`, exibe erro e impede liberacao
- Mensagem: "Esta tarefa nao pode ser liberada porque possui dependencia de uma tarefa predecessora que ainda nao foi concluida."

### Dupla protecao no checkbox RUN

**`events/onScriptInit`** (JS)
- **Protecao 1 — Desabilitar checkbox:** A funcao `esconderCheckboxRun()` percorre os spans `id_sc_field_btn_predecessor_N` e, se `data-blocked='1'`, desabilita (`disabled = true`) e desmarca o checkbox `NM_ck_runN` da linha, alem de aplicar opacidade 0.4 no `td`
- **Protecao 2 — Sobrescrita do "Selecionar Todos":** A funcao nativa `nm_marca_check_grid()` (chamada pelo checkbox `NM_ck_run0` no cabecalho) foi sobrescrita para marcar **apenas** checkboxes nao desabilitados (`!this.disabled`), impedindo que o "Selecionar Todos" marque tasks bloqueadas por predecessora
- Executa no `setTimeout` ao carregar e novamente a cada `ajaxComplete` (cobertura para paginacao, ordenacao e filtros)

### Aba "Dependencias" com duas sub-aplicacoes

- Adicionada nova aba **"Dependencias"** no form, contendo duas sub-aplicacoes (detalhes) vinculadas ao mestre via `[glo_task_id]`:
  - `form_public_mro_task_dependencies_predecessoras` — tarefas que bloqueiam a task atual
  - `form_public_mro_task_dependencies_sucessoras` — tarefas que a task atual bloqueia

  ABA Dependências
Essas tarefas bloqueiam a tarefa atual (detalhe_predecessoras)
form_public_mro_task_dependencies_predecessoras
successor_task_id =   [glo_task_id]

Essas tarefas são bloqueadas pela tarefa atual (sucessoras)
form_public_mro_task_dependencies_sucessoras
predecessor_task_id = [glo_task_id]

---

## `Timesheet/control_split_assignment` (NOVA)

### App tipo Control para dividir assignments (multipla atribuicao)

**Criada para permitir multipla atribuicao na mesma task.** O supervisor clica no icone "Adicionar Mecanico" e esta app executa a divisao proporcional de horas.

**`events/onLoad`**
- Recebe `glo_assignment_id_split` (setado pelo icone na app do supervisor)
- Consulta dados do assignment original (task, employee, skill, horas, status)
- Preenche campos de exibicao e armazena horas originais em `glo_split_original_hours`

**`events/onValidate`**
- Valida se o funcionario selecionado e **diferente** do `executed_by_employee_id` do assignment original
- Impede split para o mesmo funcionario com `sc_error_message`

**`events/onValidateSuccess`**
- Executa o split em transacao:
  1. Carrega dados completos do assignment original
  2. **UPDATE** no original: `planned_qty_hours` = metade
  3. **INSERT** novo assignment: mesmo `task_id`, `skill_id`, `project_id`; `status_code = 'NOT_STARTED'`; `executed_by_employee_id` = funcionario escolhido; `planned_qty_hours` = metade
  4. **INSERT** evento `SPLIT` em `mro_assignment_events` com `new_employee_id` e descricao
  5. Exibe card de sucesso verde + botao "Voltar Painel Supervisor"

---

## `form_public_mro_task_assignments_planned`

### Botao "Adicionar Mecanico" por linha

**`events/onRecord`**
- Adicionado icone `btn_split_assignment` (fa-user-plus, verde) via `sc_make_link` para `control_split_assignment`
- Aba no `tabs_supervisor`: **A Distribuir** (status NOT_STARTED / PLANNED / RELEASED)
- Permite dividir assignment antes de atribuir o mecanico

---

## `form_public_mro_task_assignments_progress`

### Botao "Adicionar Mecanico" por linha

**`events/onRecord`**
- Mesmo icone `btn_split_assignment` chamando `control_split_assignment`
- Aba no `tabs_supervisor`: **Em Andamento** (status ASSIGNED / IN_PROGRESS)
- Permite adicionar mais mecanicos durante a execucao

---

## `mro_tasks` — Bloqueio por Predecessora (Nova Demanda 28/07/2026)

### Migration 03 — Adicionar coluna is_blocked_predecessor

**`migrations/MRO-119_03_add_is_blocked_predecessor.sql`**
- Adiciona coluna `is_blocked_predecessor` (boolean, NOT NULL, DEFAULT false) na tabela `mro_tasks`
- Cria indices `idx_task_dep_successor` e `idx_task_dep_predecessor` em `mro_task_dependencies` para performance das subqueries da trigger

### Migration 04 — Trigger function + triggers

**`migrations/MRO-119_04_trigger_blocked_predecessor.sql`**
- Cria trigger function `fn_update_blocked_predecessor()` em PL/pgSQL
- **Trigger 1** (`trg_task_dependencies_blocked`): AFTER INSERT, UPDATE ou DELETE em `mro_task_dependencies` — recalcula `is_blocked_predecessor` da sucessora ao criar, editar ou remover dependencia
- **Trigger 2** (`trg_tasks_status_blocked`): AFTER UPDATE OF `status_code` em `mro_tasks` — ao concluir ou cancelar uma tarefa, recalcula flag de todas as suas sucessoras e da propria task (se for sucessora de alguem)

### Migration 05 — Sincronizacao dos dados existentes

**`migrations/MRO-119_05_sync_blocked_predecessor.sql`**
- Deve rodar APOS as migrations 03 e 04
- Atualiza `is_blocked_predecessor` para todas as tasks que ja possuiam dependencias antes da criacao da coluna
- A partir dai, a trigger mantem o flag sincronizado automaticamente

**`migrations/MRO-119_06_add_dependencies_apps_sec.sql`**
- Registra `form_public_mro_task_dependencies_predecessoras` e `form_public_mro_task_dependencies_sucessoras` na tabela `sec_apps` (app_type = form)
- Concede as mesmas permissoes de grupo do `form_public_mro_tasks` para ambas as apps em `sec_groups_apps`

---

## `form_public_mro_task_dependencies_predecessoras` (NOVA)

### App Form para gerenciar dependencias (predecessoras)

**Vinculada como detalhe do `form_public_mro_tasks`.** Permite adicionar, editar e remover tarefas predecessoras que bloqueiam a tarefa atual.

- Filtro mestre-detalhe: `successor_task_id = [glo_task_id]` (task atual e a sucessora)
- Tipo: Form (CRUD direto na tabela `mro_task_dependencies`)

**`events/onValidate`** — Validacoes de negocio:
- Impede auto-referencia (`predecessor_task_id = successor_task_id`)
- Impede duplicidade (mesmo par ja cadastrado)

**`events/onAfterInsert`** — Apos inserir:
- Consulta `is_blocked_predecessor` no banco (ja atualizado pela trigger)
- Usa `sc_master_value("is_blocked_predecessor", valor)` para atualizar o campo no form pai `form_public_mro_tasks`

**`events/onAfterUpdate`** — Apos alterar:
- Mesma logica do onAfterInsert

**`events/onBeforeDelete`** — Antes de deletar:
- Armazena `successor_task_id` em `[glo_succ_id_deleted]` para usar apos a exclusao

**`events/onAfterDelete`** — Apos deletar:
- Le `[glo_succ_id_deleted]` e atualiza `is_blocked_predecessor` no form pai via `sc_master_value`

---

## `form_public_mro_task_dependencies_sucessoras` (NOVA)

### App Form para gerenciar sucessoras

**Vinculada como detalhe do `form_public_mro_tasks`.** Lista as tarefas que dependem da task atual (bloqueadas por ela).

- Filtro mestre-detalhe: `predecessor_task_id = [glo_task_id]` (task atual e a predecessora)
- Tipo: Form (CRUD direto na tabela `mro_task_dependencies`)
- `events/onValidate`**: mesmas validacoes da `..._predecessoras` (auto-referencia e duplicidade)
- **Não atualiza o mestre** via `sc_master_value` — quem muda o `is_blocked_predecessor` e a task sucessora, nao a atual

---

## Testes de validacao (Trigger Predecessora)

### Teste A - Bloqueio ao criar dependencia (INSERT)
- **Operacao:** `INSERT INTO mro_task_dependencies (predecessor_id=1, successor_id=15070, 'FS')`
- **Pred status:** PLANNED
- **Resultado esperado:** Sucessora marcada como `is_blocked_predecessor = true`
- **Resultado:** ✅ **APROVADO** — trigger `trg_task_dependencies_blocked` funcionou

### Teste B - Liberacao ao concluir predecessora (UPDATE status_code)
- **Operacao:** `UPDATE mro_tasks SET status_code = 'COMPLETED' WHERE task_id = 1`
- **Resultado esperado:** Sucessora liberada (`is_blocked_predecessor = false`)
- **Resultado:** ✅ **APROVADO** — trigger `trg_tasks_status_blocked` funcionou

### Teste C - Remocao de dependencia (DELETE)
- **Operacao:** `DELETE FROM mro_task_dependencies WHERE pred_id=1 AND succ_id=15070`
- **Resultado esperado:** Sucessora permanece `false` (ja havia sido liberada no teste B)
- **Resultado:** ✅ **APROVADO** — trigger `trg_task_dependencies_blocked` funcionou para DELETE

### Teste D - Multiplas predecessoras (liberacao parcial)
- **Cenario:** WB-ROTINA-A com 3 predecessoras ativas (MI220, 370017, 370018)
- **Operacao:** `UPDATE mro_tasks SET status_code = 'COMPLETED' WHERE task_id = 15083` (conclui MI220)
- **Resultado esperado:** WB-ROTINA-A continua bloqueada (ainda ha 370017 e 370018 ativas)
- **Resultado:** ✅ **APROVADO** — `is_blocked_predecessor` permanece `true`

### Teste E - Validacao no Liberar para Execucao
- **Cenario:** Task com `is_blocked_predecessor = true` no grid
- **Operacao:** Clicar no botao "Liberar Execucao"
- **Resultado esperado:** Bloqueado com mensagem de erro
- **Resultado:** ✅ **APROVADO** — teste visual confirmado

### Validacao no banco (aos 28/07/2026)
| Metrica | Valor |
|---------|-------|
| Tasks com `is_blocked_predecessor = true` | 616 |
| Tasks com `is_blocked_predecessor = false` | 15.660 |
| Total de tasks | 16.276 |