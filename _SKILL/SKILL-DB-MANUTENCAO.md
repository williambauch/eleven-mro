---
name: db-mro-manutencao
description: Cria e altera tabelas, colunas, constraints, índices, funções e triggers no banco PostgreSQL do MRO System, seguindo rigorosamente os padrões existentes. Use sempre que o usuário pedir para "criar tabela", "alterar tabela", "adicionar coluna", "criar índice", "modificar schema", "adicionar constraint", "criar trigger", "atualizar o banco", "migração", ou similar. Também aciona quando o usuário quer adicionar documentação (COMMENT ON) em tabelas e colunas existentes.
---

# SKILL — Manutenção do Banco de Dados MRO System (PostgreSQL)

Esta SKILL contém as regras, padrões e templates para criar e alterar tabelas,
colunas, constraints, índices, funções e triggers no banco `elevenmy_mro`,
garantindo consistência com o schema existente.

## Regras Obrigatórias

1. **SEMPRE** adicionar `COMMENT ON TABLE` após criar uma tabela.
2. **SEMPRE** adicionar `COMMENT ON COLUMN` em colunas que não sejam autoexplicativas (flags, campos de regra de negócio, FKs, campos de status).
3. **TODAS** as tabelas com PK inteira usam sequence nomeada no padrão `{tabela}_{coluna_pk}_seq`.
4. **TODAS** as tabelas usam `WITH (oids = false)`.
5. **TODAS** as tabelas de domínio devem ter `created_at` e `updated_at` com `DEFAULT now()` e trigger de auto-update.
6. **Nomenclatura** de tabelas: prefixo `mro_` (negócio), `sec_` (segurança), `sys_` (sistema), `sc_` (ScriptCase), `view_` (views).
7. Colunas em `snake_case`, nomes descritivos em português ou inglês técnico.
8. Schema sempre `"public"`.
9. Constraints nomeadas seguindo padrão: `{tabela}_pkey`, `{tabela}_{coluna}_key` (UNIQUE), `fk_{tabela}_{coluna}` (FK).
10. **NUNCA** usar `CHECK` constraints — validações são feitas no backend.
11. Alertar o usuário para rodar o script no banco PostgreSQL `elevenmy_mro`.
12. Toda alteração deve ser escrita como script SQL migratório (um arquivo `.sql`), salvo em `_Banco_de_dados/migrations/` com nome `YYYY-MM-DD_descricao.sql`.
13. Após criar qualquer script, **perguntar se ele quer que você tente executar diretamente no banco** via a ferramenta de banco disponível (MCP DBHub ou psql).

---

## Templates

### 1. Tabela Nova (com sequence)

Use este template para criar uma tabela nova com PK inteira sequencial:

```sql
-- =============================================
-- Tabela: mro_NOME_DA_TABELA
-- Descrição: O QUE ESTA TABELA ARMAZENA
-- =============================================
DROP TABLE IF EXISTS "mro_NOME_DA_TABELA";
DROP SEQUENCE IF EXISTS mro_NOME_DA_TABELA_NOME_id_seq;
CREATE SEQUENCE mro_NOME_DA_TABELA_NOME_id_seq
    INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."mro_NOME_DA_TABELA" (
    "NOME_id" integer DEFAULT nextval('mro_NOME_DA_TABELA_NOME_id_seq') NOT NULL,
    "campo1" character varying(255) NOT NULL,
    "campo2" character varying(50),
    "campo3" integer,
    "campo4_fk" integer,
    "flag_ativo" boolean DEFAULT true,
    "valor" numeric(10,2) DEFAULT '0.00',
    "descricao" text,
    "data_evento" date,
    "created_at" timestamp DEFAULT now(),
    "updated_at" timestamp DEFAULT now(),
    CONSTRAINT "mro_NOME_DA_TABELA_pkey" PRIMARY KEY ("NOME_id"),
    CONSTRAINT "mro_NOME_DA_TABELA_UNICO_key" UNIQUE ("campo1")
) WITH (oids = false);

COMMENT ON TABLE "public"."mro_NOME_DA_TABELA" IS 'Descrição clara do propósito da tabela';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."campo3" IS 'Explicação do campo se não for autoexplicativo';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."campo4_fk" IS 'FK para a tabela X — referência Y';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."flag_ativo" IS 'Se true, o registro está ativo no sistema';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."valor" IS 'Valor em reais (BRL) do X';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."data_evento" IS 'Data em que o evento Y ocorreu';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."created_at" IS 'Data/hora de criação do registro';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."updated_at" IS 'Data/hora da última alteração (atualizado automaticamente por trigger)';
```

### 2. Tabela Nova (PK natural — sem sequence)

Use quando a chave primária for um código textual (ex: `phase_code`, `status_code`):

```sql
-- =============================================
-- Tabela: mro_NOME_DA_TABELA
-- Descrição: O QUE ESTA TABELA ARMAZENA
-- =============================================
DROP TABLE IF EXISTS "mro_NOME_DA_TABELA";

CREATE TABLE "public"."mro_NOME_DA_TABELA" (
    "codigo" character varying(20) NOT NULL,
    "nome" character varying(100) NOT NULL,
    "descricao" text,
    "ordem" integer DEFAULT '0',
    "created_at" timestamp DEFAULT now(),
    CONSTRAINT "mro_NOME_DA_TABELA_pkey" PRIMARY KEY ("codigo")
) WITH (oids = false);

COMMENT ON TABLE "public"."mro_NOME_DA_TABELA" IS 'Descrição clara do propósito da tabela';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."codigo" IS 'Código único do registro';
COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."ordem" IS 'Define a ordem de exibição';
```

### 3. Tabela Associativa (PK composta)

```sql
-- =============================================
-- Tabela: mro_ENTIDADE1_ENTIDADE2
-- Descrição: Associação N:N entre X e Y
-- =============================================
DROP TABLE IF EXISTS "mro_ENTIDADE1_ENTIDADE2";

CREATE TABLE "public"."mro_ENTIDADE1_ENTIDADE2" (
    "entidade1_id" integer NOT NULL,
    "entidade2_id" integer NOT NULL,
    "observacao" text,
    "created_at" timestamp DEFAULT now(),
    CONSTRAINT "mro_ENTIDADE1_ENTIDADE2_pkey" PRIMARY KEY ("entidade1_id", "entidade2_id")
) WITH (oids = false);

COMMENT ON TABLE "public"."mro_ENTIDADE1_ENTIDADE2" IS 'Associação N:N entre Tabela X e Tabela Y';
COMMENT ON COLUMN "public"."mro_ENTIDADE1_ENTIDADE2"."entidade1_id" IS 'FK para mro_X — referência Y';
COMMENT ON COLUMN "public"."mro_ENTIDADE1_ENTIDADE2"."entidade2_id" IS 'FK para mro_Y — referência Z';
```

### 4. Adicionar Coluna

```sql
-- =============================================
-- Adicionar coluna em mro_NOME_DA_TABELA
-- Descrição: MOTIVO DA ALTERACAO
-- =============================================
ALTER TABLE "public"."mro_NOME_DA_TABELA"
    ADD COLUMN "novo_campo" character varying(100);

COMMENT ON COLUMN "public"."mro_NOME_DA_TABELA"."novo_campo" IS 'Descrição do novo campo e seu propósito';
```

### 5. Adicionar Foreign Key

```sql
-- =============================================
-- FK: mro_TABELA_ORIGEM -> mro_TABELA_DESTINO
-- Descrição: MOTIVO DO RELACIONAMENTO
-- =============================================
ALTER TABLE ONLY "public"."mro_TABELA_ORIGEM"
    ADD CONSTRAINT "fk_mro_TABELA_ORIGEM_coluna_fk"
    FOREIGN KEY ("coluna_fk")
    REFERENCES "public"."mro_TABELA_DESTINO"("coluna_pk")
    ON DELETE RESTRICT NOT DEFERRABLE;

COMMENT ON COLUMN "public"."mro_TABELA_ORIGEM"."coluna_fk" IS 'FK para mro_TABELA_DESTINO — referência X';

-- Padrões de ON DELETE:
-- ON DELETE CASCADE   → dependência forte (ex: evento depende da atribuição)
-- ON DELETE RESTRICT  → relação protegida (ex: projeto não pode excluir aeronave em uso)
-- ON DELETE SET NULL  → relação opcional (ex: supervisor opcional)
```

### 6. Adicionar Índice

```sql
-- =============================================
-- Índice: idx_mro_TABELA_CAMPO
-- Descrição: Acelerar consultas por CAMPO
-- =============================================
CREATE INDEX IF NOT EXISTS "idx_mro_TABELA_CAMPO"
    ON "public"."mro_TABELA" USING btree ("coluna");

-- Índice composto:
CREATE INDEX IF NOT EXISTS "idx_mro_TABELA_CAMPOS"
    ON "public"."mro_TABELA" USING btree ("coluna1", "coluna2");
```

### 7. Trigger de Auto-Update `updated_at`

A função já existe no banco. Para ativar em uma tabela que ainda não tem:

```sql
-- =============================================
-- Trigger: set_timestamp_mro_NOME_DA_TABELA
-- Descrição: Atualiza updated_at automaticamente
-- =============================================
DROP TRIGGER IF EXISTS "set_timestamp_mro_NOME_DA_TABELA" ON "public"."mro_NOME_DA_TABELA";
CREATE TRIGGER "set_timestamp_mro_NOME_DA_TABELA"
    BEFORE UPDATE ON "public"."mro_NOME_DA_TABELA"
    FOR EACH ROW EXECUTE FUNCTION trigger_set_timestamp();
```

### 8. Adicionar UNIQUE Constraint

```sql
-- =============================================
-- UNIQUE: mro_TABELA_colunas_key
-- Descrição: MOTIVO DA RESTRICAO
-- =============================================
ALTER TABLE "public"."mro_TABELA"
    ADD CONSTRAINT "mro_TABELA_coluna_key" UNIQUE ("coluna");

-- UNIQUE composta:
ALTER TABLE "public"."mro_TABELA"
    ADD CONSTRAINT "mro_TABELA_col1_col2_key" UNIQUE ("coluna1", "coluna2");
```

### 9. Migração Completa (arquivo)

Sempre salvar em `_Banco_de_dados/migrations/YYYY-MM-DD_descricao.sql`:

```sql
-- =============================================
-- Migration: YYYY-MM-DD_descricao
-- Descrição: O QUE ESTA MIGRACAO FAZ
-- =============================================
-- Autor: <nome>
-- Data: YYYY-MM-DD

-- ---------------------------------------------
-- 1. Criar tabela X
-- ---------------------------------------------
...

-- ---------------------------------------------
-- 2. Adicionar coluna em Y
-- ---------------------------------------------
...

-- ---------------------------------------------
-- 3. Comentários
-- ---------------------------------------------
...
```

---

## Padrões de Tipos de Dados

| Tipo | Quando usar |
|---|---|
| `character varying(N)` | Textos curtos/médios. N variável: 20 (códigos), 50 (nomes curtos), 100 (nomes completos), 255 (emails, logins, descrições médias) |
| `text` | Descrições longas, observações, remarks, logs, JSON strings |
| `integer` | Chaves, FKs, contadores, sort_order |
| `numeric(10,2)` | **Padrão universal** para valores monetários, horas, quantidades |
| `numeric(12,2)` | Valores maiores (custo acumulado) |
| `boolean DEFAULT false` | Flags de controle (is_ativo, is_nrc, requires_rii) |
| `boolean DEFAULT true` | Flags de permissão (is_active, is_public) |
| `timestamp` | Datas completas com hora (created_at, updated_at, data de eventos) |
| `timestamp DEFAULT now()` | Padrão para created_at e updated_at |
| `timestamp DEFAULT CURRENT_TIMESTAMP` | Alternativa aceitável (equivalente a now()) |
| `date` | Apenas data sem hora (start_date, end_date, data de nascimento) |
| `date DEFAULT CURRENT_DATE` | Data corrente como padrão |
| `jsonb` | Apenas para payloads de integração (raro) |
| `bytea` | Apenas para armazenar imagens/binários (ex: foto do usuário) |

---

## Padrões de DEFAULT Values

| Tipo | Formato do DEFAULT |
|---|---|
| Timestamp | `DEFAULT now()` ou `DEFAULT CURRENT_TIMESTAMP` |
| Date | `DEFAULT CURRENT_DATE` |
| String curta | `DEFAULT 'UN'`, `DEFAULT 'DRAFT'`, `DEFAULT 'LABOR'` |
| Booleano | `DEFAULT true` ou `DEFAULT false` (sem aspas) |
| Inteiro | `DEFAULT '0'` (com aspas, sim) |
| Decimal | `DEFAULT '0.00'` (sempre com aspas) |
| NULL explícito | `DEFAULT NULL` (ou omitir) |

---

## Regras de Constraints

| Constraint | Nomenclatura | Exemplo |
|---|---|---|
| PRIMARY KEY | `{tabela}_pkey` | `mro_aircraft_pkey` |
| UNIQUE (1 coluna) | `{tabela}_{coluna}_key` | `mro_aircraft_registration_key` |
| UNIQUE (2+ colunas) | `{tabela}_{col1}_{col2}_key` | `mro_materials_prod_pn_key` |
| FOREIGN KEY | `fk_{tabela}_{coluna}` | `fk_mro_projects_aircraft` |
| INDEX | `idx_{tabela}_{coluna}` | `idx_mro_task_assignments_proj` |

---

## Regras de Comentários (COMMENT ON)

### OBRIGATÓRIO adicionar COMMENT ON TABLE para:
- Toda tabela nova
- Toda view nova

### OBRIGATÓRIO adicionar COMMENT ON COLUMN para:
- Foreign Keys (`FK para mro_X — referência Y`)
- Flags booleanas com regra de negócio (`Se true, significa que...`)
- Campos de valor monetário (`Valor em reais (BRL)`)
- Campos de data com significado especial (`Data em que...`)
- Campos calculados ou derivados
- Campos com default não óbvio
- Campos de status (explicitar os valores possíveis)
- Campos de tipo/lote (batch_sn, part_number, etc.)

### DISPENSADO de COMMENT ON COLUMN:
- `created_at` e `updated_at` (já são autoexplicativos com a trigger)
- `NOME_id` (autoexplicativo como PK)
- Campos com nome autoexplicativo como `full_name`, `email`, `login`, `password`

---

## Exemplos Reais do Schema

### Tabela com sequence e updated_at
```sql
CREATE TABLE "public"."mro_employees" (
    "employee_id" integer DEFAULT nextval('mro_employees_employee_id_seq') NOT NULL,
    "login" character varying(255) NOT NULL,
    "full_name" character varying(255) NOT NULL,
    "employee_registration" character varying(50),
    "email" character varying(255),
    "canac_code" character varying(20),
    "is_supervisor" boolean DEFAULT false,
    "is_inspector" boolean DEFAULT false,
    "is_active" boolean DEFAULT true,
    "created_at" timestamp DEFAULT now(),
    "updated_at" timestamp DEFAULT now(),
    "skill_id" integer,
    CONSTRAINT "mro_employees_login_key" UNIQUE ("login"),
    CONSTRAINT "mro_employees_pkey" PRIMARY KEY ("employee_id")
) WITH (oids = false);

COMMENT ON TABLE "public"."mro_employees" IS 'Cadastro de colaboradores internos da MRO';
COMMENT ON COLUMN "public"."mro_employees"."canac_code" IS 'Código ANAC do profissional';
COMMENT ON COLUMN "public"."mro_employees"."is_supervisor" IS 'Se true, pode distribuir tarefas';
COMMENT ON COLUMN "public"."mro_employees"."is_inspector" IS 'Se true, pode aprovar/assinar tarefas';
```

### Tabela com valores decimais
```sql
CREATE TABLE "public"."mro_timesheet" (
    "timesheet_id" integer DEFAULT nextval('mro_timesheet_timesheet_id_seq') NOT NULL,
    "assignment_id" integer,
    "start_time" timestamp,
    "end_time" timestamp,
    "total_hours" numeric(10,2) DEFAULT '0.00',
    "status" character varying(20) DEFAULT 'RUNNING',
    "created_at" timestamp DEFAULT now(),
    CONSTRAINT "mro_timesheet_pkey" PRIMARY KEY ("timesheet_id")
) WITH (oids = false);
```

---

## Erros Comuns a Evitar

| Erro | Correção |
|---|---|
| Usar `SERIAL` ou `BIGSERIAL` | Usar `integer + sequence` no padrão do projeto |
| Usar `varchar` sem length | Usar `character varying(N)` com N explícito |
| Usar `VARCHAR` (maiúsculo) | Usar `character varying` (minúsculo) |
| Esquecer `WITH (oids = false)` | Sempre adicionar no final |
| Usar `NOT NULL` sem default em coluna nova em tabela existente | Usar `DEFAULT` primeiro, depois alterar para `NOT NULL` |
| Nomear FK como `{tabela}_id_fkey` (padrão PostgreSQL automático) | Nomear explicitamente como `fk_{tabela}_{coluna}` |
| Criar coluna `updated_at` sem trigger | Sempre criar a trigger junto |
| Esquecer `COMMENT ON` | Sempre documentar tabelas e colunas não óbvias |
| Usar `CURRENT_TIMESTAMP` em tabela que tem `updated_at` com trigger | Manter `updated_at` com `DEFAULT now()` + trigger |
