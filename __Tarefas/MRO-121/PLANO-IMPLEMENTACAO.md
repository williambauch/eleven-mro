# PLANO DE IMPLEMENTACAO - MRO-121

## Melhorar vinculacao de recursos nas tarefas, visualizacao de estoques e acesso a documentos

---

## 1. ANALISE DO ESTADO ATUAL

### 1.1 Anexos

- **`mro_task_attachments`**: Tabela ja existe, vinculada diretamente a task (`task_id`, `project_id`). Usada pelo app `form_public_mro_task_attachments` (GED).
- **Anexos de Aeronave**: Nao existe tabela especifica. A aeronave (`mro_aircraft`) nao possui relational de documentos.
- **Anexos de Projeto**: Nao existe tabela especifica. O projeto (`mro_projects`) nao possui relational de documentos.
- **Necessidade**: Criar `mro_aircraft_attachments` e `mro_project_attachments` (ou tabela generica `mro_documents` com polimorfismo via `entity_type` + `entity_id`).

### 1.2 Calculo automatico

- **`mro_tasks.estimated_hours`**: Campo ja existe, mas atualmente editavel manualmente. Nao possui calculo automatico.
- **`mro_tasks.estimated_material_cost`**: Campo ja existe, mas atualmente editavel manualmente. Nao possui calculo automatico.
- **`mro_task_resources.budgeted_hours`**: Contem as horas orcadas por recurso (skill) para a task.
- **`mro_task_materials.total_cost`**: Contem o custo total por item de material na task.
- **Necessidade**: Tornar `estimated_hours` e `estimated_material_cost` readonly e alimenta-los automaticamente a partir da soma dos grids filhos.

### 1.3 Ferramentas Pendentes

- **`mro_tool_movements`**: Tabela existente que registra retiradas (`checkout`) e devolucoes (`checkin`) de ferramentas.
- **`mro_tools`**: Cadastro de ferramentas com status (`AVAILABLE`, etc.).
- **Necessidade**: Criar consulta que liste ferramentas em posse de funcionarios (checkin IS NULL) com dados do funcionario.

### 1.4 Multiarmazem

- **`mro_materials.stock_location`**: Campo unico que indica o armazem principal. Nao suporta saldo dividido por armazem.
- **Necessidade**: Criar tabela `mro_material_warehouse_balance` (material_id, warehouse_code, balance) para saldo multiarmazem, ou estender a estrutura existente.

### 1.5 Fluxo de Inclusao de Recursos

- **`form_public_mro_task_materials`**: Formulario que gerencia materiais vinculados a tarefa. Revisar UX para facilitar busca e inclusao.
- **`form_public_mro_task_resources`**: Formulario que gerencia recursos de labor (por skill). Revisar UX.

---

## 2. DECISOES DE DESIGN

### 2.1 Modelo de Documentos

**Opcao A** (Recomendada): Tabela generica `mro_documents` com polimorfismo:
```sql
CREATE TABLE mro_documents (
    document_id SERIAL PRIMARY KEY,
    entity_type VARCHAR(30) NOT NULL,   -- 'AIRCRAFT', 'PROJECT', 'TASK'
    entity_id INTEGER NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_size_kb INTEGER,
    mime_type VARCHAR(100),
    uploaded_by VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT
);
CREATE INDEX idx_mro_docs_entity ON mro_documents(entity_type, entity_id);
```
Isso unifica o armazenamento e evita criar N tabelas de anexos.

**Opcao B**: Criar tabelas separadas `mro_aircraft_attachments` e `mro_project_attachments`.
(Similar a `mro_task_attachments`.)

**Decisao**: Usar **Opcao A** (tabela unificada `mro_documents`) para simplificar manutencao futura e reuso do componente de upload.

### 2.2 Calculo Automatico

- **Trigger no PostgreSQL**: Nao recomendado porque os dados de resources e materials estao em tabelas diferentes e podem ser alterados em momentos distintos.
- **Evento onValidate do form_public_mro_tasks**: Ao salvar a task, calcular e atualizar os campos.
- **Evento onValidateSuccess dos forms filhos**: Ao adicionar/remover resource ou material, recalcular o campo na task pai via SQL direto.
- **Decisao**: Implementar o calculo em ambos os lados:
  1. Nos forms `form_public_mro_task_resources` e `form_public_mro_task_materials` (ao inserir/alterar/excluir)
  2. No `form_public_mro_tasks` (ao salvar a task, como dupla protecao)

### 2.3 Multiarmazem

- Criar tabela `mro_material_warehouse_balance` (material_id, warehouse_code, balance).
- Migration para popular saldos iniciais baseados no `stock_location` e `stock_balance` atuais.
- No `grid_public_mro_materials`, adicionar link no `onRecord` que abre modal (events_ajax) ou nova grid detalhada.

### 2.4 Ferramentas Pendentes

- Criar `grid_mro_tools_pending_return` (tipo Grid ou Blank + SQL).
- Query: `SELECT t.*, tm.*, e.full_name FROM mro_tool_movements tm JOIN mro_tools t ... WHERE tm.checkin_date IS NULL`.
- Incluir filtros por funcionario, ferramenta, periodo.

---

## 3. RISCOS E DEPENDENCIAS

| Risco | Impacto | Mitigacao |
|-------|---------|-----------|
| Dados de `mro_materials.stock_balance` inconsistentes (tudo zero) | Medio | Validar com usuario antes de criar multiarmazem |
| Ferramentas sem movimentacao registrada | Baixo | Incluir no grid ferramentas com status != AVAILABLE |
| Campos `estimated_hours` e `estimated_material_cost` sendo usados em relatorios/exportacoes | Medio | Garantir compatibilidade retroativa |
| Upload de arquivos grandes (>10MB) | Baixo | Validar limite no ScriptCase e configurar php.ini |

---

## 4. ORDEM DE EXECUCAO RECOMENDADA

| Ordem | Atividade | App/Tabela |
|:-----:|-----------|------------|
| 0 | **Migrations de banco** | `mro_documents`, `mro_material_warehouse_balance` |
| 1 | Upload de anexos em Aeronave | `form_public_mro_aircraft` |
| 2 | Upload de anexos em Projetos | `form_public_mro_projects` |
| 3 | Botao de docs na Task + grid + painel mecanico | `form_public_mro_tasks`, `grid_public_mro_tasks`, `grid_my_tasks`, `form_public_mro_task_assignments` |
| 4 | Calculo automatico de estimados | `form_public_mro_tasks`, `form_public_mro_task_resources`, `form_public_mro_task_materials` |
| 5 | Revisao de UI/UX de inclusao de recursos | `form_public_mro_task_materials`, `form_public_mro_task_resources` |
| 6 | Grid de ferramentas pendentes | `grid_mro_tools_pending_return` (NOVA) |
| 7 | Detalhamento multiarmazem | `grid_public_mro_materials` + `form_public_mro_material_warehouse` (NOVA) |

---

## 5. MIGRACOES PREVISTAS

### 5.1 Criacao da tabela `mro_documents`
```sql
CREATE TABLE IF NOT EXISTS "public"."mro_documents" (
    "document_id" SERIAL NOT NULL,
    "entity_type" VARCHAR(30) NOT NULL,
    "entity_id" INTEGER NOT NULL,
    "file_name" VARCHAR(255) NOT NULL,
    "original_name" VARCHAR(255),
    "file_size_kb" INTEGER,
    "mime_type" VARCHAR(100),
    "uploaded_by" VARCHAR(50),
    "uploaded_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "description" TEXT,
    CONSTRAINT "mro_documents_pkey" PRIMARY KEY ("document_id")
);
CREATE INDEX IF NOT EXISTS "idx_mro_docs_entity" ON "public"."mro_documents"("entity_type", "entity_id");
```

### 5.2 Criacao da tabela `mro_material_warehouse_balance`
```sql
CREATE TABLE IF NOT EXISTS "public"."mro_material_warehouse_balance" (
    "balance_id" SERIAL NOT NULL,
    "material_id" INTEGER NOT NULL,
    "warehouse_code" VARCHAR(20) NOT NULL,
    "balance" NUMERIC(10,2) DEFAULT 0.00,
    CONSTRAINT "mro_material_warehouse_balance_pkey" PRIMARY KEY ("balance_id"),
    CONSTRAINT "fk_mwh_material" FOREIGN KEY ("material_id") REFERENCES "public"."mro_materials"("material_id"),
    CONSTRAINT "uq_material_warehouse" UNIQUE ("material_id", "warehouse_code")
);
CREATE INDEX IF NOT EXISTS "idx_mwh_material" ON "public"."mro_material_warehouse_balance"("material_id");
```

### 5.3 Populacao inicial do multiarmazem (baseado em `stock_location` e `stock_balance`)
```sql
INSERT INTO "public"."mro_material_warehouse_balance" (material_id, warehouse_code, balance)
SELECT material_id, COALESCE(NULLIF(stock_location, ''), '01'), stock_balance
FROM "public"."mro_materials"
WHERE stock_balance > 0
ON CONFLICT (material_id, warehouse_code) DO NOTHING;
```

---

## 6. ESFORCO ESTIMADO

| Atividade | Complexidade | Estimativa (h) |
|-----------|:------------:|:--------------:|
| Migrations de banco | Baixa | 1 |
| Upload de anexos Aeronave | Media | 3 |
| Upload de anexos Projetos | Media | 2 |
| Botao docs na Task + grid + mecanico | Alta | 6 |
| Calculo automatico de estimados | Media | 4 |
| Revisao de UI/UX recursos | Media | 3 |
| Grid ferramentas pendentes | Media | 3 |
| Detalhamento multiarmazem | Alta | 5 |
| **Total** | | **27h** |
