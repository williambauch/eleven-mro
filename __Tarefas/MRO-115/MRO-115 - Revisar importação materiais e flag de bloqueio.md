# DEFINIÇÃO DA TAREFA



=========== DEFINIÇÃO DA TAREFA ===========

**Revisar o motor de importação da planilha de materiais e implementar a flag de bloqueio operacional por item, refletindo o status automaticamente na tabela de tarefas.**



O sistema precisa de uma atualização no fluxo de recebimento de materiais via planilha para garantir que a falta ou a criticidade de um item específico trave a execução da tarefa no chão de fábrica, alertando o Planejamento e a Logística.



Critérios de Aceite (Definition of Done):



Nova Flag em Materiais (DDL): Criar uma coluna booleana (ex: is\_not\_blocking) na tabela mro\_materials. Esta flag indicará se a ausência deste material específico tem o poder de impedir a execução da tarefa.



Atualização de Status na Tarefa (Triggers/PHP): Incluir a lógica de atualização no banco de dados. Se uma tarefa possuir um material vinculado cuja flag is\_not\_blocking seja falsa ou o Status na planilha seja Nulo, Vazio ou “-”, o sistema deve ligar a flag is\_blocked\_material diretamente na tabela mro\_tasks.



Atualização do saldo Empenhado: Atualizar o campo committed\_qty na tabela mro\_task\_materials com o valor que está na coluna Saldo Empenho somente para material vinculado cuja flag is\_not\_blocking seja verdadeira ou o Status na planilha tenha OK.



Informações Técnicas (Para o Desenvolvedor):



Tabelas Impactadas: mro\_materials, mro\_task\_materials, mro\_tasks.



Campos Chave: is\_blocked\_material.



Atenção: Assegurar que a regra do apontamento de horas (Timesheet) impeça o mecânico de iniciar (Clock-in) caso a tarefa herde o bloqueio crítico derivado do novo cadastro de material. Tbm deve impedir o programador de fazer a atribuição da tarefa, e deve mostrar no painel do supervisor na aba Blocked.


# BACKUP e APLICAÇÕES CRIADAS



PROJETO   

**## Editado**

&#x20;



**## NOVO**

&#x20;



**# UTEIS**

&#x20;


## Sumário das alterações implementadas

### `_Banco_de_dados` — Migrations

**`migrations/MRO-115_add_is_blocking_task_mro_materials.sql`**
- Adiciona coluna `is_blocking_task boolean DEFAULT true` em `mro_materials`
- Índice `idx_mro_materials_is_blocking_task`
- COMMENT ON COLUMN documentando a regra

**`migrations/MRO-115_change_unique_constraint_mro_materials.sql`**
- Altera constraint `mro_materials_prod_pn_key` para UNIQUE `(product_code, part_number, stock_location)`

---

### `compras/control_import_empenhos` — Motor de importação

**`events/07_onValidateSuccess/onValidateSuccess.scriptcase`**
- **PASSO A**: Inicializa `$tarefas_alteradas[$task_id]` com `'tem_bloqueio' => false`
- **PASSO B**: Lê `is_blocking_task` do banco (fonte de verdade = `form_public_mro_materials`)
- **Regra de saldo empenhado**: `$pode_contabilizar = (!$is_blocking_task || $material_status_ok)` — se true, `committed_qty` recebe o valor da planilha; se false, zera
- **Regra de bloqueio**: se `$is_blocking_task && !$material_status_ok` → marca `tem_bloqueio = true`
- **PASSO E**: foreach atualiza `is_blocked_material`, `estimated_material_cost` e dispara O&A se NRC

---

### `form_public_mro_task_assignments` — Bloqueio no Clock-in

**Botão Play** (`button/Play.scriptcase`)
- Antes de criar o timesheet, consulta `is_blocked_material` da task vinculada ao assignment
- Se bloqueado, exibe `sc_error_message` e aborta

**Bloqueio visual no onLoad** (`events/05_onLoad/onLoad.scriptcase`)
- Ao carregar a tela, consulta `is_blocked_material` da task via JOIN com `mro_task_assignments`
- Se a tarefa estiver bloqueada e o status nao for COMPLETED/CANCELLED:
  - Botao Play desabilitado via `sc_btn_display('Play', 'off')`
  - Exibe `sc_alert` avisando: "ATENCAO: Esta tarefa esta bloqueada por falta de material."
  - Substitui o `display_tempo` por um aviso vermelho estilizado com icone de bloqueio

---

### `Timesheet/control_pause_task` — Bloqueio ao pular para nova tarefa

**`events/07_onValidateSuccess/onValidateSuccess.scriptcase`**
- Na ROTA 5 (O MECÂNICO ESCOLHEU PULAR PARA UMA NOVA TAREFA), verifica `is_blocked_material` da task destino
- Se bloqueada, exibe erro e aborta

---

### `form_public_mro_materials` — Cadastro de materiais

- Adicionado campo `{is_blocking_task}` do tipo select, label "Material Bloqueante?"
- Opcoes:
  - **Bloqueia (1)** — valor `1` (true)
  - **Nao Bloqueia (0)** — valor `0` (false)
- Tooltip (tippy):
  - **Bloqueia (padrao true):** A falta deste material bloqueia a tarefa. O mecanico nao podera iniciar o apontamento, e o saldo empenhado sera zerado ate que o material receba Status na planilha (OK, OK-CHECK, etc).
  - **Nao Bloqueia (false):** Este material nao bloqueia a execucao da tarefa, mesmo que esteja indisponivel (sem Status na planilha de empenhos). O saldo empenhado sera contabilizado normalmente.
  - Use esta flag para materiais de consumo opcional ou que podem ser substituidos sem impactar a execucao.

---

### `grid_public_mro_materials` — Grade de materiais

- SQL anterior tinha apenas: `material_id, part_number, description, unit_measure, is_consumable`
- SQL atualizado para incluir todos os campos relevantes:
  ```sql
  SELECT material_id, part_number, description, unit_measure, is_consumable,
         unit_cost, product_code, stock_location, stock_balance, is_blocking_task
  FROM public.mro_materials;
  ```
- Permite visualizar e editar o campo `is_blocking_task` diretamente na grid

---

### `grid_public_mro_tasks` — Grade de tarefas

- Desativada a configuracao "Salvar o estado da Consulta na sessao"
- O ScriptCase mantinha o estado anterior da consulta na sessao, fazendo com que a macro `sc_select_where(add)` acumulasse filtros de `project_id` de todas as telas visitadas
- O SQL gerado ficava algo como: `WHERE project_id = 5 AND project_id = 10 AND project_id = 5 AND project_id = 3`
- Com a desativacao, cada abertura da grid aplica apenas o filtro do projeto atual via `sc_select_where(add)`


## Testes de validacao (projeto 5, 1204 linhas)

### Teste F - is_blocking_task = true (padrao, todos materiais bloqueando)
- **BLOQUEIA:** 1690
- **OK (Status):** 718
- **OK (is_blocking_task=false):** 0
- **Total OK:** 718
- **Importacao concluida:** sim
- Arquivo: `__Tarefas/MRO-115/teste_importacao/teste-F-14072026.html`

### Teste G - is_blocking_task = false em 2 materiais (CS080700003721 e CS080200000028)
- **BLOQUEIA:** 1620 (reduziu 70)
- **OK (Status):** 718
- **OK (is_blocking_task=false):** 70 (35 de cada material)
- **Total OK:** 788
- **Importacao concluida:** sim
- Arquivo: `__Tarefas/MRO-115/teste_importacao/teste-G-14072026.html`
- Materiais setados como nao-bloqueantes:
  - material_id 4662 — CS080200000028 (MEK) — stock_location 02
  - material_id 4816 — CS080700003721 (DISCO SPEEDLOK GRAO 120) — stock_location 01

### Validacao no banco remoto (apos teste F)
| Metrica | Valor |
|---------|-------|
| Tarefas com material | 290 |
| Tarefas bloqueadas | 269 |
| Tarefas liberadas | 21 |
| Total empenhos | 1189 |
| Empenhos com saldo | 358 |
| Empenhos zerados | 831 |
| Saldo empenhado | 1.964,33 |
| Custo empenhado | R$ 839.300,75 |



# 

