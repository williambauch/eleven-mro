# DEFINICAO DA TAREFA

=========== DEFINICAO DA TAREFA ===========

**Melhorar a vinculacao de recursos nas tarefas, visualizacao de estoques e acesso a documentos de aeronaves e projetos.**

Garantir que os mecanicos e planejadores tenham acesso rapido aos manuais/anexos diretamente na tarefa, alem de otimizar como ferramentas e materiais sao consultados e incluidos na rotina.

Criterios de Aceite (Definition of Done):

- **Upload de Anexos (Aeronave):** Habilitar a funcionalidade de upload/inclusao de anexos (PDFs, manuais) diretamente na tela de Cadastro de Aeronave (`form_public_mro_aircraft`).
- **Upload de Anexos (Projetos):** Habilitar a funcionalidade de upload/inclusao de anexos (PDFs, manuais) diretamente na tela de Cadastro de Projetos (`form_public_mro_projects`).
- **Acesso a Docs na Tarefa:** Criar um botao/modal na tela de execucao da Tarefa que permita visualizar os documentos vinculados ao Projeto e a Aeronave daquela tarefa. Incluir no `grid_public_mro_tasks`, `form_public_mro_tasks` e no painel do mecanico (`grid_my_tasks` / tela do cronometro).
- **Revisao de Inclusao de Recursos:** Revisar e otimizar o fluxo de UI/UX de como ferramentas e materiais sao incluidos/solicitados dentro das tarefas (`form_public_mro_task_materials`, `form_public_mro_task_resources`).
- **Calculo Automatico de Estimados:** Atualizar o campo `estimated_hours` (deixando-o desativado/readonly) com a soma das horas do grid Resources Labor (`mro_task_resources.budgeted_hours`). Atualizar o campo `estimated_material_cost` (deixando-o desativado/readonly) com o custo total do grid Material (`mro_task_materials.total_cost`).
- **Consulta de Ferramentas Pendentes:** Criar grid/relatorio para consultar ferramentas pendentes de devolucao (em posse dos funcionarios), baseado na tabela `mro_tool_movements` ou similar.
- **Consulta de Materiais (Multiarmazem):** No grid de consulta de materiais (`grid_public_mro_materials`), ao clicar sobre um item especifico, abrir detalhamento mostrando o saldo dividido por armazem.

Informacoes Tecnicas (Para o Desenvolvedor):

- Tabelas impactadas: `mro_tasks`, `mro_task_materials`, `mro_task_resources`, `mro_materials`, `mro_tools`, `mro_tool_movements`, `mro_aircraft`, `mro_projects`, `mro_task_attachments`
- Campos chave: `estimated_hours` (mro_tasks), `estimated_material_cost` (mro_tasks), `budgeted_hours` (mro_task_resources), `total_cost` (mro_task_materials)
- Novas tabelas previstas: `mro_aircraft_attachments` (anexos de aeronave), `mro_project_attachments` (anexos de projeto), `mro_warehouse_stock` (estoque multiarmazem) — avaliar necessidade durante implementacao
- O campo `estimated_hours` deve ser readonly e alimentado automaticamente via trigger ou evento onValidate/onLoad do form
- O campo `estimated_material_cost` deve ser readonly e alimentado automaticamente via trigger ou evento onValidate/onLoad do form
- Ferramentas pendentes: utilizar a tabela `mro_tool_movements` que ja registra retiradas e devolucoes de ferramentas; criar query que filtra movimentos sem devolucao (checkin IS NULL) agrupados por funcionario
- Multiarmazem: a tabela `mro_materials` ja possui `stock_location` (varchar) que indica o armazem principal; pode ser necessario criar tabela `mro_material_warehouse_balance` (material_id, warehouse_code, balance) para suportar saldo dividido por armazem

==============================================

# BACKUP e APLICACOES CRIADAS

PROJETO   MRO System

**## Editado (previsto)**
- `ROOT/form_public_mro_aircraft` — events/onLoad, events/onValidateSuccess (upload de anexos)
- `ROOT/form_public_mro_projects` — events/onLoad, events/onValidateSuccess (upload de anexos)
- `ROOT/form_public_mro_tasks` — events/onLoad (botao docs, calculo automatico), events/onValidate (calculo), button/btn_docs_modal
- `ROOT/grid_public_mro_tasks` — events/onRecord (botao docs)
- `ROOT/grid_public_mro_materials` — events/onRecord (link para detalhamento multiarmazem)
- `Timesheet/grid_my_tasks` — events/onRecord (botao docs)
- `tasks/form_public_mro_task_assignments` — events/onLoad (botao docs na tela do cronometro)
- `tasks/form_public_mro_task_materials` — revisao de UI/UX
- `tasks/form_public_mro_task_resources` — revisao de UI/UX

**## NOVO (previsto)**
- `Logistica e Ferramentaria/grid_mro_tools_pending_return` — Consulta de Ferramentas Pendentes de Devolucao
- `ROOT/form_public_mro_material_warehouse` — Detalhamento de saldo por armazem (multiarmazem) — ou modal via events_ajax

**# UTEIS**
- Skill de registro: `.github/_SKILL/SKILL-REGISTRO-TAREFA.md`
- Plano de implementacao: `__Tarefas/MRO-121/PLANO-IMPLEMENTACAO.md`

## Sumario das alteracoes implementadas

*(A ser preenchido durante a implementacao)*

---

## `form_public_mro_aircraft`

### Upload de anexos na Aeronave

**`events/onLoad`**
- Pendente.

**`events/onValidateSuccess`**
- Pendente.

---

## `form_public_mro_projects`

### Upload de anexos no Projeto

**`events/onLoad`**
- Pendente.

**`events/onValidateSuccess`**
- Pendente.

---

## `form_public_mro_tasks`

### Botao de acesso a documentos do Projeto/Aeronave

**`events/onLoad`**
- Pendente.

### Calculo automatico de estimated_hours e estimated_material_cost

**`events/onLoad`** / **`events/onValidate`**
- Pendente.

---

## `grid_public_mro_tasks`

### Botao de acesso a documentos por linha

**`events/onRecord`**
- Pendente.

---

## `grid_my_tasks` (Painel do Mecanico)

### Botao de acesso a documentos na grid do mecanico

**`events/onRecord`**
- Pendente.

---

## `form_public_mro_task_assignments` (Tela do Cronometro)

### Botao de acesso a documentos na tela de execucao

**`events/onLoad`**
- Pendente.

---

## `form_public_mro_task_materials`

### Revisao de UI/UX da inclusao de materiais

**`events/onLoad`**
- Pendente.

---

## `form_public_mro_task_resources`

### Revisao de UI/UX da inclusao de recursos de labor (ferramentas)

**`events/onLoad`**
- Pendente.

---

## `grid_mro_tools_pending_return` (NOVA)

### Consulta de ferramentas pendentes de devolucao

**`sql/schema.sql`**
- Pendente.

**`events/onExecute`**
- Pendente.

---

## `grid_public_mro_materials`

### Detalhamento de saldo multiarmazem

**`events/onRecord`**
- Pendente.

---

## Testes de validacao

*(A ser preenchido apos implementacao)*

### Teste A - Upload de anexo na Aeronave
- Pendente.

### Teste B - Upload de anexo no Projeto
- Pendente.

### Teste C - Visualizacao de docs na Tarefa
- Pendente.

### Teste D - Calculo automatico de estimated_hours
- Pendente.

### Teste E - Calculo automatico de estimated_material_cost
- Pendente.

### Teste F - Consulta de ferramentas pendentes
- Pendente.

### Teste G - Detalhamento multiarmazem no grid de materiais
- Pendente.
