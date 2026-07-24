# DEFINICAO DA TAREFA

=========== DEFINICAO DA TAREFA ===========

**Incluir classificacao por fases, permitir multipla atribuicao e criar filtros avancados de pendencias e impedimentos no grid.**

A entidade principal de Tarefas precisa ser expandida para melhorar o planejamento e a alocacao. Necessario incluir a rastreabilidade das Fases do projeto, permitir que mais de um mecanico/funcionario seja atribuido a mesma tarefa e criar filtros rapidos para identificar gargalos operacionais.

Criterios de Aceite:

- Novos Campos: Adicionar os campos "Fase Baseline" (baseline_phase_code) e "Fase" (phase_code) (buscando da tabela mro_project_phases) na interface da Tarefa.
- Grid e Pesquisa: Incluir esses dois novos campos nos filtros de pesquisa e habilitar a "quebra" (agrupamento) por eles nos grids.
- Filtros de Pendencias: Criar botoes/filtros rapidos no grid de tarefas para listar: Tarefas retidas por predecessoras, Tarefas com falta de ferramentas, e Tarefas com falta de materiais.

Notas Técnicas:

Adequar o banco de dados (tabela pivot para responsáveis se necessário, em vez de FK única na mro_tasks).

Observação:
no sistema quem delega as tarefas para mecanico/funcionario é o usuário do grupo supervisor. Para isso ele usa o conjunto e aplicações abaixo:
tabs_supervisor
form_public_mro_task_assignments_planned
form_public_mro_task_assignments_progress/
form_public_mro_task_assignments_blocked
form_public_mro_task_assignments_completed


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

## `form_public_mro_tasks`

### Exposicao dos campos phase_code e baseline_phase_code com lookup

- Campos `phase_code` e `baseline_phase_code` (varchar 50) ja existiam na tabela `mro_tasks` e foram exibidos no form na aba "Datas e Status"

**Regras de editabilidade:**
- `phase_code` → **sempre editavel**, permite alterar a fase atual da tarefa a qualquer momento
- `baseline_phase_code` → **editavel apenas enquanto DRAFT e Novo Registro**. Apos a task sair de DRAFT (qualquer outro status), o campo fica readonly. O baseline congela o plano original da tarefa e nao deve mais ser alterado

---

## `grid_public_mro_tasks`

### Campos adicionados ao filtro refinado e avancado

Os seguintes campos foram adicionados ao **filtro refinado** e ao **filtro avançado** do grid:

| Campo | Label |
|---|---|
| `phase_code` | Phase Code |
| `baseline_phase_code` | Baseline Phase Code |
| `is_blocked_tool` | Blocked Tool |
| `is_blocked_labor` | Blocked Labor |
| `is_blocked_material` | Blocked Material |

### Quebra dinamica (agrupamento) por fases

- Adicionado recurso de **quebra dinamica** para os campos `baseline_phase_code` (Fase Baseline) e `phase_code` (Fase), permitindo agrupar tarefas por fase no grid

---

## `control_split_assignment` (NOVA)

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

## `form_public_mro_task_assignments_blocked`

### Botao "Adicionar Mecanico" por linha

**`events/onRecord`**
- Mesmo icone `btn_split_assignment` chamando `control_split_assignment`
- Aba no `tabs_supervisor`: **Com Impedimentos** (status BLOCKED)
- Permite dividir a carga para desbloquear com nova alocacao

---

## Testes de validacao (MODELO 737NG - GOL, 3 assignments)

### Teste A - Split de assignment IN_PROGRESS (7h)
- **Assignment origem:** 15716 (Mecanico Teste, 7h, IN_PROGRESS)
- **Novo funcionario:** Gabriela Lichevis
- **Resultado:** Original atualizado para 3.50h, novo assignment 3.50h NOT_STARTED
- **Validacao no banco:** OK — 2 registros, horas somam 7h

### Teste B - Split de assignment NOT_STARTED (3.50h)
- **Assignment origem:** 15722 (Gabriela Lichevis, 3.50h, NOT_STARTED)
- **Novo funcionario:** Clodoaldo Jose Ribeiro
- **Resultado:** Original atualizado para 1.75h, novo assignment 1.75h NOT_STARTED
- **Validacao no banco:** OK — 3 registros, horas somam 7h

### Teste C - Split de assignment BLOCKED (4h)
- **Assignment origem:** 15712 (Mecanico Teste, 4h, BLOCKED)
- **Novo funcionario:** Bruno de Lima Azevedo
- **Resultado:** Original atualizado para 2.00h, novo assignment 2.00h NOT_STARTED
- **Validacao no banco:** OK — 2 registros, horas somam 4h

### Teste D - Validacao mesmo funcionario
- **Tentativa:** Split para o mesmo funcionario do assignment original
- **Resultado:** Bloqueado com `sc_error_message` — "Nao e possivel dividir o assignment para o mesmo funcionario da atribuicao original."
- **Validacao:** OK — impeditivo funciona corretamente