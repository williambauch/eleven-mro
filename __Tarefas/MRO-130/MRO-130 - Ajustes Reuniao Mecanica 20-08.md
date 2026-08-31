# MRO-130 - Ajustes Reunião Mecânica (20/08) - Clodoaldo e Gabriela

**Status: EM ANDAMENTO**

## Descricao da tarefa

Ajustes levantados na reunião de Mecânica de 20/08 (Leonel, Clodoaldo, Gabriela e Danilo):

1) Exibir o projeto e a matrícula da aeronave na tela do cronômetro do mecânico.
2) No cadastro do projeto, definir quem será o coordenador responsável. Esse coordenador deverá ter um filtro que permita visualizar, no painel do coordenador, as listas de tarefas que precisam ser editadas por ele.
3) No painel do supervisor/mecânico, disponibilizar um filtro inicial para selecionar o projeto que será trabalhado antes de listar as atribuições.
4) O Split, em vez de dividir o tempo ao meio, deve considerar o total atualmente alocado e redistribuí-lo entre as atribuições existentes e a nova atribuição criada.
   Exemplo: hoje, ao fazer um Split de 0,88, o sistema gera 0,44 / 0,44. Se for realizado um novo Split sobre uma das atribuições de 0,44, o resultado atual seria 0,44 / 0,22 / 0,22, deixando a distribuição desproporcional. O esperado é que o total de 0,88 seja redistribuído igualmente entre as três atribuições, resultando em aproximadamente 0,29 / 0,29 / 0,29.

## Sumario das alteracoes implementadas

## form_public_mro_task_assignments

### Exibir projeto e matricula da aeronave no cronometro do mecanico

**`events/onLoad`**
- Adicionada consulta que busca `p6_proj_id` (projeto), `project_name` e `registration` (matricula) via assignment -> project -> aircraft.
- Novo campo `label_projeto` criado na IDE, preenchido com "Projeto: <codigo> | Matricula: <matricula>", com classes CSS (`mro-label-projeto` / `mro-label-matricula`) para estilizacao customizada.
- Regra de negocio: o label fica em campo separado do `#mro_timer`, pois o JS do cronometro reescreve o innerHTML a cada segundo e apagaria a informacao.
- Regra de negocio: matricula exibe "N/A" quando o projeto nao possui aeronave vinculada.

---

## form_public_mro_task_assignments_planned

### Correcao de erro updated_by em UPDATE de mro_tasks

**`events/onBeforeUpdate`**
- Removida a coluna `updated_by` do `UPDATE mro_tasks` (coluna inexistente na tabela, causava erro ao atribuir o primeiro mecanico).
- Regra de negocio: o `updated_at` da task e atualizado pelo trigger `set_timestamp_mro_tasks`, dispensando set manual.
- Correcao pontual validada durante a atribuicao de teste do assignment 18537 (mecanico 14 / task 16635).

---

## form_public_mro_projects

### Adicionar coordenador responsavel no cadastro de projeto

**`migrations/01_MRO-130_add_coordinator_id_mro_projects.sql`**
- Adicionada coluna `coordinator_id integer` (FK para `mro_employees`, ON DELETE SET NULL).
- Regra de negocio: painel do coordenador (grid_painel_coordenador) lista somente projetos onde este campo = [usr_employee_id].

**`config.json`**
- Adicionado campo `coordinator_id` (int4) ao config do formulario.
- Regra de negocio: na IDE, criar campo Lookup filtrando por funcionarios do grupo COORDENADOR (sec_users_groups + mro_employees).

**`sql/schema.sql`**
- Espelhada a coluna `coordinator_id` na query do schema.

---

## grid_painel_coordenador (NOVA app)

### Painel do Coordenador — filtro automatico por projetos do coordenador logado

**`migrations/02_MRO-130_grant_grid_painel_coordenador_to_coordenador.sql`**
- Concede acesso SOMENTE ao grupo COORDENADOR (id 5) na `sec_groups_apps`.
- Defesa em profundidade: remove acesso de outros grupos caso tenham sido cadastrados por engano.

**`producao_manutencao/grid_painel_coordenador/sql/schema.sql`**
- Query filtra `mro_projects.coordinator_id = [usr_employee_id]` e status `PENDING_COORD`, `IN_PROGRESS`, `RELEASED`, `PENDING_PROG`.
- Regra de negocio: a grid vem filtrada direto pelo coordenador logado, sem filtro manual.
- Regra de negocio: filtro aplicado no SELECT principal (unico ponto de controle) — nao repetido em onScriptInit.

**`producao_manutencao/grid_painel_coordenador/events/onScriptInit`**
- Mantido apenas com comentario explicativo; filtro nao duplicado (evita WHERE redundante).

**`producao_manutencao/grid_painel_coordenador/events/onRecord`**
- Icones de bloqueio (material/ferramenta/mao de obra) e NRC/Rotina (padrao do projeto).
- Badge de status no padrao da `grid_public_mro_tasks` (consulta `mro_sys_status` -> `label_ptbr`, `kanban_color`, `icon`), com fallback cinza.
- Botao `btn_editar` que aponta para `form_public_mro_tasks` via `task_id` (a ser "ligado" pela IDE).

**`producao_manutencao/grid_painel_coordenador/events/onHeader`**
- Card explicativo em tons clean (branco/ambar), com acentos PT-BR e linguagem de usuario final (sem nomes tecnicos de tabela/app).
- Explica o que aparece, como o filtro funciona e por que essas tarefas estao ali.

**`Security/sec_menu/menu_tree.md`**
- Adicionado item 54: `Produção e Manutenção > Painel do Coordenador (grid_painel_coordenador)`, antes do `item_38 (Painel do Supervisor)`.

---

**PENDENTE**: itens 3 e 4 (filtro de projeto no painel supervisor/mecanico, redistribuicao do Split).
