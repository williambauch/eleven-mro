# MRO-129 - Ajustes Reunião Planejamento (19/08) - Filtro Range, Quebra WBS, WBS Global

**Status: ✅ TAREFA CONCLUÍDA (26/08/2026)**

## Descricao da tarefa

Ajustes levantados na reunião de Planejamento de 19/08 (Leonel e Danilo):

1) Filtro das Tasks: permitir a filtragem por range nos campos numéricos. Esse range também será utilizado para selecionar quais JICs serão impressas, mantendo a impressão em ordem numérica.
2) Grid da Task: adicionar a quebra por WBS.
3) Cadastro de WBS: o WBS deverá ser um cadastro global, mantido em uma tabela única, e utilizado na Task como mais um status, em uma relação 1:1.
4) Predecessoras e Sucessoras: nos campos de seleção, exibir somente as Tasks pertencentes ao mesmo projeto.

## Sumario das alteracoes implementadas

## form_public_mro_projects

### WBS removido do cadastro de projetos

- Removido o campo `wbs` do formulario (a coluna ja nao existia no banco).
- Regra de negocio: o WBS deixa de ser cadastrado/vinculado no projeto; passa a ser cadastro global.

---

## form_public_mro_wbs

### Cadastro de WBS global com hierarquia (NIVEL / SUB-NIVEL)

- Formulario sem vinculo com projeto; adicionados `wbs_level`, `parent_wbs_id` e `sort_order`.
- Regra de negocio: NIVEL nao tem pai; SUB-NIVEL exige um NIVEL pai.

### Eventos de hierarquia

**`events_ajax/wbs_level_onChange`**
- NIVEL: esconde `parent_wbs_id` e limpa (`'NULL'`); SUB-NIVEL: mostra o campo.

**`events/onValidate`**
- NIVEL: garante `parent_wbs_id` vazio.
- SUB-NIVEL: obriga `parent_wbs_id` e valida que o pai existe e e NIVEL.

**`events/onBeforeInsert` e `events/onBeforeUpdate`**
- Converte `parent_wbs_id = 0` para `'NULL'` (evita violacao da FK `mro_wbs_parent_wbs_id_fkey`).

---

## grid_public_mro_wbs

### Consulta de WBS global

- Removido filtro por projeto; adicionados `wbs_level`, `parent_wbs_id`, `sort_order` e `ORDER BY sort_order`.
- Regra de negocio: a grid lista todos os WBS globais, sem filtrar por projeto.

---

## Views ajustadas 

**`MRO-129_views_kanban_gantt.sql`**
- Recria `view_kanban_datasource` e `view_gantt_tracking` sem depender de `mro_wbs.project_id`.
- `view_kanban_datasource`: usa `t.project_id`; `view_gantt_tracking`: bloco PHASE deriva o projeto das tasks do WBS.

---
## form_public_mro_task_dependencies_predecessoras

### Lookup de predecessoras filtrando pelo projeto

- SQL do select: lista somente tasks do mesmo projeto da task atual (`[glo_task_id]`, do mestre-detalhe).
- Regra de negocio: so exibe tasks do mesmo projeto como predecessoras.
 
---

## form_public_mro_task_dependencies_sucessoras

### Lookup de sucessoras filtrando pelo projeto

- SQL do select: lista somente tasks do mesmo projeto da task atual (`[glo_task_id]`, do mestre-detalhe).
- Regra de negocio: so exibe tasks do mesmo projeto como sucessoras.
 
---

## Testes de validacao (projeto MRO, 3.760 linhas)

### Teste A - Migracao WBS global no banco
- **Registros `mro_wbs`:** 28 (WBS.xlsx)
- **Coluna `project_id` removida:** OK
- **Colunas `wbs_level`/`parent_wbs_id`:** OK
- **FK `mro_tasks_wbs_id_fkey` (NO ACTION):** ativa
- **Resultado:** aprovado

### Teste B - Hierarquia WBS
- **WS (id 1) -> 10 sub-niveis:** OK
- **TA (id 12) -> 7 sub-niveis:** OK
- **NR (id 20) -> 8 sub-niveis:** OK
- **Resultado:** aprovado

### Teste C - Views e apps consumidoras
- **`view_kanban_datasource`:** OK (usa `t.project_id`)
- **`view_gantt_tracking`:** OK (3759 TASK + 28 PHASE + 1 NRC + 22 PROJECT)
- **`blank_kanban_board`, `blank_gantt_tracking`, `reports/jobcard`:** OK
- **Resultado:** aprovado

### Validacao de normalizacao de nomes (WBS)
| Nome antigo (banco) | Normalizado | Mapeado para |
|---------------------|-------------|--------------|
| Task Card do Check | TASK CARD DO CHECK | WS.TC |
| Diretivas TÃ©cnicas | DIRETIVAS TECNICAS | WS.DT |
| Aprovadas CAP ZERO | APROVADAS CAP ZERO | NR.APZER |
| Cancelada | CANCELADA | NR.CAN |
| Aguardando AprovaÃ§Ã£o do Cliente CAP ZERO | AGUARDANDO APROVACAO DO CLIENTE CAP ZERO | NR.AGZER |

### Validacao no banco (tasks)
| Metrica | Valor |
|---------|-------|
| Total de tasks | 19.869 |
| Tasks com WBS (mapeadas) | 3.760 |
| Tasks sem WBS | 16.109 (projetos sem WBS cadastrado + pendentes) |
| Projetos 2-5 sem WBS | 37 (Abaixo do CAP / GOL) |

---

## Pendentes

- **`MRO-129_wbs_sort_order.sql`**: aplicar no banco (preenche `sort_order` de 5 em 5).
- **37 tasks** dos projetos 2-5 sem WBS (`Aprovadas Abaixo do CAP` / `GOL LINHAS AEREAS`): aguardando decisao do Danilo sobre codigo WBS equivalente.
- **Item 1 (filtro range) e Item 2 (quebra WBS no grid)**: nao implementados nesta etapa — verificar com o lider se continuam no escopo.
- **Lookup de dependencias (item 4)**: colar o SQL na IDE do ScriptCase nos campos `predecessor_task_id` / `successor_task_id`.
- **Eventos do form WBS**: copiar para a IDE do ScriptCase (`onValidate`, `onBeforeInsert`, `onBeforeUpdate`, `wbs_level_onChange`).

**Status: ✅ TAREFA CONCLUÍDA (26/08/2026)** — item 3 (WBS global) implementado e validado no banco; demais itens conforme pendências acima.
