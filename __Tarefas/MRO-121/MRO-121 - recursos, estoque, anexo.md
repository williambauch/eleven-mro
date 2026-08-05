# Melhorar a vinculação de recursos nas tarefas, visualização de estoques e acesso a documentos de aeronaves e projetos.


Garantir que os mecânicos e planejadores tenham acesso rápido aos manuais/anexos diretamente na tarefa, além de otimizar como ferramentas e materiais são consultados e incluídos na rotina.

**Critérios de Aceite:**

- 1 **Upload de Anexos:** Habilitar a funcionalidade de upload/inclusão de anexos (PDFs, manuais) diretamente nas telas de Cadastro de Aeronave e Cadastro de Projetos.
- 2 **Acesso a Docs na Tarefa:** Criar um botão na tela de execução da Tarefa que abra um modal/link para visualizar os documentos vinculados ao Projeto e à Aeronave daquela tarefa.
- Incluir no grid de tarefas, form de tarefas, painel do mecânico (tela do cronometro)

- 3 **Revisão de Inclusão:** Revisar e otimizar o fluxo de UI/UX de como ferramentas e materiais são incluídos/solicitados dentro das tarefas.
- 4 **Cálculo automático:** Atualizar o campo Estimated Hours (deixando ele desativado) com as horas incluídas no grid Resources Labor e atualizar o campo Estimated Material Cost (deixando ele desativado) com o custo do grid Material,
- 5 **Consulta de Ferramentas:** Criar grid/relatório para consultar ferramentas pendentes de devolução (em posse dos funcionários).
- 6 **Consulta de Materiais (Multiarmazém):** No grid de consulta de materiais, ao clicar sobre um item específico, abrir detalhamento mostrando o saldo dividido por armazém.

---

## Sumario das alteracoes implementadas

## `mro_attachments` (tabela)

### Tabela unificada de anexos (itens 1 e 2)

**`migrations/MRO-121_create_mro_attachments.sql`**
- Cria tabela relacional com 3 colunas de vinculo: `task_id`, `project_id`, `aircraft_id` (todas nullable)
- Cada anexo pertence a UM contexto: tarefa, projeto OU aeronave (nunca mais de um por registro)
- Regra de negocio: anexo de tarefa tambem herda o projeto e aeronave da tarefa (via cascade no filtro da grid)

### Permissoes das apps

**`migrations/MRO-121_permissions_anexos.sql`**
- Copia permissoes de `grid_public_mro_tasks` para a nova grid de anexos
- Copia permissoes de `form_public_mro_tasks` para o novo form de anexos
- Usa `ON CONFLICT DO UPDATE` para idempotencia (pode ser executado mais de uma vez)

**`migrations/MRO-121_permissions_stock_location.sql`**
- Registra a app em `sec_apps` como consulta ('cons')
- Copia permissoes de `grid_public_mro_materials` (grid de origem da Ligacao de campo) para os grupos
- Usa `ON CONFLICT DO UPDATE` para idempotencia
- Regra de negocio: quem acessa a grid de materiais tambem pode abrir o detalhamento por armazem via link

---

## `grid_public_mro_attachments`

### Listagem de anexos com filtro dinamico por contexto

**`sql/schema.sql`**
- SELECT com aliases em todos os campos para exibicao na grid
- Campo calculado `origin_type` via CASE WHEN: retorna 'TASK', 'PROJECT', 'AIRCRAFT' ou 'N/A'

**`events/onScriptInit`**
- Le 3 globais de contexto: `[glo_att_task_id]`, `[glo_att_project_id]`, `[glo_att_aircraft_id]`
- Valida que pelo menos 1 contexto foi informado (senao exibe erro e aborta)
- Resolve cascade automaticamente: tarefa -> resolve projeto (via mro_tasks) -> resolve aeronave (via mro_projects)
- Monta condicoes OR deduplicadas: `task_id = X OR project_id = Y OR aircraft_id = Z`
- Injeta WHERE via `sc_select_where(add)` com UMA unica chamada (regra obrigatoria da macro)
- Verifica `{sc_where_atual}` para decidir se usa WHERE (primeira condicao) ou AND (condicao adicional)

- Regra de negocio: ao abrir por tarefa, mostra anexos da tarefa + do projeto da tarefa + da aeronave do projeto



---

## `form_public_mro_attachments`

### Formulario unico para incluso e edicao de anexo


**`events/onLoad`**
- Em INCLUSAO (`sc_btn_new`): preenche task_id/project_id/aircraft_id conforme o contexto de onde a app foi aberta
- Prioridade de preenchimento: TAREFA > PROJETO > AERONAVE
- Em EDICAO: nao altera os campos (ja vem da tabela, preserva dados originais)
- Chama `mMontarSubdirAnexos()` sempre (inclusao e edicao) para montar o caminho da pasta de upload

**`methods/mMontarSubdirAnexos`**
- Monta o subdiretorio de upload a partir dos campos do registro (nao das globais)
- Funciona em inclusao E edicao (sempre consulta o banco para resolver o contexto)
- Regras de subdiretorio:
  - Tarefa: resolve project_id via `mro_tasks` -> `/anexos_mro/p{project_id}/t{task_id}`
  - Projeto: `/anexos_mro/p{project_id}`
  - Aeronave: `/anexos_mro/a{aircraft_id}`
- Regra de negocio: o subdir em edicao usa os campos do registro, garantindo que troca de arquivo vai para a pasta correta

---

## `form_public_mro_task_assignments`

### Botao de acesso aos anexos (item 2 - painel do mecânico)

**`button/btn_VerAnexo`**
- Botao adicionado na tela de execucao da tarefa (painel do mecânico)
- Link direto para a `grid_public_mro_attachments`, permitindo ao mecânico apenas VISUALIZAR os anexos
- Sem permissao de inclusao/edicao/exclusao nesse fluxo (somente leitura)
- Regra de negocio: o mecânico consulta manuais e documentos vinculados a tarefa, projeto e aeronave sem sair do fluxo de execucao

---

## `form_public_mro_tasks`

### Reorganizacao do fluxo de inclusao por abas (item 3)

- Reorganizado no ScriptCase o form mestre da tarefa em abas para padronizar o fluxo de inclusao/solicitacao de recursos
- Aba **Geral**: agrupa os dados comuns da tarefa (status, datas, ids e codigos)
- Aba **Resources Labor**: grid de recursos de mao de obra (`form_public_mro_task_resources`)
- Aba **Tools**: grid de ferramentas da tarefa (`grid_mro_task_tools` => `form_mro_task_tools`)
- Aba **Material**: grid de materiais da tarefa (`grid_public_mro_task_materials` => `form_public_mro_task_materials`)
- Regra de negocio: cada tipo de recurso tem area especifica propria, sem misturar com os dados da task
- Vantagem: os mestres-detalhe nao precisam de barra de rolagem — cada aba tem espaco dedicado para listar e incluir itens

---

## `form_public_mro_task_resources`

### Calculo automatico do Estimated Hours (item 4 - parte 1)

**`methods/mRecalcularEstimatedHours`**
- Soma `COALESCE(SUM(budgeted_hours), 0)` de `mro_task_resources` e faz `UPDATE mro_tasks SET estimated_hours`
- Regra de negocio: total sempre calculado do banco (campo readonly nao envia valor); task sem recursos recebe `0`

**`events/onAfterInsert`, `events/onAfterUpdate`, `events/onAfterDelete`**
- Os 3 eventos chamam `mRecalcularEstimatedHours({task_id})` + `sc_master_value("estimated_hours", $var_total)`
- Regra de negocio: mesmo metodo nos 3 eventos garante consistencia; `sc_master_value` so atualiza o form mestre se aberto na mesma sessao

**`events/onLoadRecord`**
- Bloqueia edicao de recursos quando a task esta em status final ou ja distribuido
- Status bloqueados: RELEASED, IN_PROGRESS, COMPLETED, CANCELLED, PENDING_HANDOVER, SUPSIG, CLOSED, APPROVED
- Regra de negocio: quando a task esta liberada/em execucao, o trabalho ja foi distribuido — impede alterar recursos apos inicio

---

## `grid_mro_task_tools`

### Bloqueio de edicao de ferramentas por status da task (item 3)

**`events/onScriptInit`**
- Consulta `status_code` da task vinculada (`mro_tasks`) via `sc_lookup`
- Status bloqueados: RELEASED, IN_PROGRESS, COMPLETED, CANCELLED, PENDING_HANDOVER, SUPSIG, CLOSED, APPROVED
- Quando bloqueado, usa `sc_btn_display('new'/'update'/'delete', 'off')` + `sc_btn_disabled(..., 'on')`
- Quando liberado, reverte com `'on'` + `sc_btn_disabled(..., 'off')`
- Regra de negocio: ferramentas nao podem mais ser alteradas quando a task foi liberada/executada — o trabalho ja foi distribuido aos mecânicos

---

## `form_mro_task_tools`

### Bloqueio de edicao de ferramentas por status da task (item 3)

**`events/onLoad`**
- Mesma regra aplicada ao form de ferramentas (consulta status da task e bloqueia botoes)
- Mantem o preenchimento original `{task_id}=[task_id]` (recebe o id da task do mestre)
- Regra de negocio: bloqueio em grid E form garante que nenhuma alteracao de ferramenta passa pela interface quando a task esta em execucao

---

## `grid_public_mro_task_materials`

### Listagem de materiais da task

**`sql/schema.sql`**
- Listagem de materiais da task com filtro `WHERE task_id = [task_id]`

### Bloqueio de edicao por status da task (item 3)

**`events/onScriptInit`**
- Consulta `status_code` da task vinculada (`mro_tasks`) via `sc_lookup`
- Status bloqueados: RELEASED, IN_PROGRESS, COMPLETED, CANCELLED, PENDING_HANDOVER, SUPSIG, CLOSED, APPROVED
- Quando bloqueado, usa `sc_btn_display('new'/'update'/'delete', 'off')` + `sc_btn_disabled(..., 'on')`
- Regra de negocio: materiais nao podem mais ser alterados quando a task foi liberada/executada — o trabalho ja foi distribuido aos mecânicos

---

## `form_public_mro_task_materials`

### Calculo automatico do Estimated Material Cost (item 4 - parte 2)

**`methods/mRecalcularEstimatedMaterialCost`**
- Soma `COALESCE(SUM(total_cost), 0)` de `mro_task_materials` **EXCLUINDO material do CLIENTE** e grava em `mro_tasks.estimated_material_cost`
- Regra de negocio: material fornecido pelo cliente nao tem custo para a empresa — nao entra no total
- Regra de negocio: os campos do vinculo ja sao gravados pelo form; o metodo so soma para a task (nao refaz UPDATE do vinculo)

**`events/onAfterInsert`, `events/onAfterUpdate`, `events/onAfterDelete`**
- Os 3 eventos chamam `mRecalcularEstimatedMaterialCost({task_id})` + `sc_master_value("estimated_material_cost", $var_total)`
- Regra de negocio: mesmo metodo nos 3 eventos garante consistencia do custo apos qualquer operacao de material

### Recalculo em tela (Ajax onChange)

**`methods/mCalcularTotaisEmTela`**
- Recalcula `total_cost = planned_qty * unit_cost` e `committed_total_cost = committed_qty * committed_unit_cost` com os valores em tela
- Fallback: se `committed_unit_cost` vazio, usa `unit_cost`
- Regra de negocio: se `material_source = CLIENTE`, zera os custos em tela (mesmo padrao do import)
- Regra de negocio: `material_source` vazio recebe default `DIGEX`

**`events_ajax/` (6 eventos onChange)**
- `material_id_onChange`: carrega `unit_cost` do material master se o campo estiver vazio e recalcula
- `material_source_onChange`: aplica default DIGEX; se nao-CLIENTE e custo zerado, recarrega do master; recalcula
- `planned_qty_onChange`, `committed_qty_onChange`, `unit_cost_onChange`, `committed_unit_cost_onChange`: chamam `mCalcularTotaisEmTela`
- Regra de negocio: qualquer mudanca de qty/valor/origem recalcula os totais na hora — usuario ve o custo antes de salvar

### Bloqueio de edicao por status da task (item 3)

**`events/onLoad`**
- Consulta `status_code` da task e bloqueia botoes nos status finais/liberados
- Regra de negocio: bloqueio em grid E form garante que nenhuma alteracao de material passa pela interface quando a task esta em execucao

### Campos exibidos no form (ampliado)

- Campos ja existentes: Material PN, Planned Qty, Applied Qty, Batch Sn, Is Applied
- Campos adicionados: Committed Qty, Committed Unit Cost, Committed Total Cost, Unit Cost, Total Cost
- Form e registro unico aberto pela grid (form busca por PK quando vem da grid, mantendo filtro por task como garantia)
- Regra de negocio: expor os custos no form permite ao planejador conferir o custo estimado do material antes de salvar — base do calculo do Estimated Material Cost da task

---

## `grid_mro_tools_pending_return`

### Consulta de ferramentas pendentes de devolucao (item 5)

**`ferramentaria/grid_mro_tools_pending_return/`**
- Consulta global de ferramentas em posse dos funcionarios (sem filtro por task)
- Query em `mro_tool_transactions` com `status = 'ACTIVE'` (ferramenta nao devolvida)
- JOINs: `mro_tools` (part_number, descricao, serial) + `mro_employees` (funcionario, matricula)
- Campo calculado `dias_em_posse` (checkout ate hoje)
- Regra de negocio: transacao `ACTIVE` = ferramenta em posse do funcionario; a devolucao muda para `CLOSED` no terminal `blank_mro_ferramentaria`
- Regra de negocio: usa a tabela movimentada pelo terminal (`mro_tool_transactions`), nao a tabela legada `mro_tool_movements`

**Menu**
- Adicionado no menu `sec_menu`: "Ferramentas Pendentes" -> `grid_mro_tools_pending_return`
- Regra de negocio: acesso direto pelo menu para almoxarifado/supervisao acompanhar devolucoes pendentes

---

## `grid_public_mro_material_stock_location`

### Detalhamento de saldo por armazem (item 6)

**`Almoxarifado/grid_public_mro_material_stock_location/`**
- Consulta de saldo dividido por armazem de UM material (part_number)
- Query em `mro_materials` com `WHERE part_number = '[glo_part_number]'` (1 linha por armazem)
- Campos: stock_location, stock_balance, part_number, description, product_code, unit_measure
- Regra de negocio: `mro_materials` tem 1 registro por armazem — cada linha ja e o saldo de um local (nao precisa SUM)
- Após pesquisa: confirmado no banco que nao ha outra fonte de saldo alem de `stock_balance`

**Acesso**
- NAO entra no menu — e chamada apenas pela **Ligacao de campo** no campo `part_number` da `grid_public_mro_materials`
- Regra de negocio: ao clicar no PN do material na grid principal, abre o detalhamento por armazem

---

## `grid_public_mro_materials` e `form_public_mro_materials` (movidas)

### Reorganizacao das apps de materiais

- Movidas de `ROOT/` para `Almoxarifado/` (mesmo dominio das apps de estoque)
- `Almoxarifado/grid_public_mro_materials` e `Almoxarifado/form_public_mro_materials`
- Regra de negocio: apps de materiais ficam agrupadas com os terminais de almoxarifado (`blank_almox_kanban`, `blank_mec_materiais`)
- Nome da app nao mudou, entao menu e ligacoes continuam funcionando
