# Auditoria Pré-Importação — Projeto 22 (GOL PR-GGE)

> **Data da auditoria:** 27/08/2026
> **Objetivo:** registrar o estado do banco **ANTES** da importação do arquivo
> `__Tarefas/Arquivadas/MRO-115/COMPRAS-PRODUTO_EMPENHADOS_POR_PROJETO.xlsx`
> no projeto 22, para comparar com o estado **DEPOIS** da importação.
> Fonte: banco PostgreSQL (via MCP dbhub).
> **Status: ✅ IMPORTADO EM 27/08/2026 (10:57) — ver seção 9 com os resultados pós-importação.**

---

## 1. Identificação do projeto

| Campo | Valor |
|---|---|
| project_id | **22** |
| p6_proj_id | GOL01/26 |
| Nome | GOL LINHAS AÉREAS S.A - B737-8EH - CHECK NC06 - PR-GGE - SJK |
| Aeronave / Matrícula | B737-8EH / PR-GGE |
| Check | NC06 |

---

## 2. Arquivo que será importado

| Campo | Valor |
|---|---|
| Arquivo | `COMPRAS-PRODUTO_EMPENHADOS_POR_PROJETO.xlsx` |
| Origem do arquivo | Exportado do projeto FLYBONDI LV-KDR (task_codes são dele) |
| Total de linhas (dados) | 1.204 |
| Task codes distintos | 290 |
| Task codes que existem no projeto 22 | **26** |

### Os 26 task codes que vão casar com o projeto 22

```
370460  370582  370606  370954  370964  371058  371392  375281
379008  379014  379028  379037  379038  379039  379040  379041
379042  379043  379053  379055  37A003  37A004  37A005  37A006
37A013  37A028
```

> Os demais 264 task codes (NRCs `N370...`, `NN...`) **não existem** no projeto 22
> e serão ignorados pela importação (log "Tarefa não encontrada neste projeto").

---

## 3. Estado das tarefas (mro_tasks) — ANTES

| Métrica | Valor |
|---|---|
| Total de tarefas | **836** |
| Bloqueadas por material (is_blocked_material) | **0** |
| Bloqueadas por ferramenta (is_blocked_tool) | **0** |
| Bloqueadas por mão de obra (is_blocked_labor) | **0** |
| Bloqueadas por predecessora (is_blocked_predecessor) | **0** |
| Tarefas com custo de material (estimated_material_cost > 0) | **0** |
| Soma de estimated_material_cost | **0,00** |

### Distribuição por status

| status_code | Total |
|---|---|
| PLANNED | 806 |
| RELEASED | 29 |
| IN_PROGRESS | 1 |

---

## 4. Estado dos vínculos de material (mro_task_materials) — ANTES

| Métrica | Valor |
|---|---|
| Total de vínculos (task_material_id) | **0** |
| Tarefas com material vinculado | **0** |
| Empenhos com saldo (committed_qty > 0) | **0** |
| Empenhos zerados (committed_qty = 0) | **0** |
| Custo total committed (committed_total_cost) | **0,00** |
| Custo total planejado (total_cost) | **0,00** |

---

## 5. Estado do cadastro de materiais (mro_materials) — ANTES

> Observação: `mro_materials` é global (não por projeto). A importação fará
> UPSERT nos materiais que casarem por (product_code, part_number, stock_location).

| Métrica | Valor |
|---|---|
| Total de materiais cadastrados | **509** |
| Bloqueantes (is_blocking_task = true) | **506** |
| Não bloqueantes (is_blocking_task = false) | **3** |
| Com saldo físico (stock_balance > 0) | **0** |

---

## 6. Regras MRO-115 que serão exercitadas na importação

1. **Saldo empenhado (committed_qty):** só é contabilizado se o material não
   bloqueia (`is_blocking_task = false`) OU se o STATUS da planilha está
   preenchido (ex: OK). Caso contrário, `committed_qty = 0`.
2. **Bloqueio da tarefa (is_blocked_material):** a tarefa é bloqueada quando o
   material bloqueia (`is_blocking_task = true`) **E** o STATUS está vazio ou `-`.
3. **Material CLIENTE:** quando `material_source = CLIENTE`, o `unit_cost` é zerado.
4. **Atualização de custos:** `estimated_material_cost` da tarefa é recalculado
   (soma dos `total_cost`, excluindo CLIENTE).
5. **Limpeza inteligente:** vínculos órfãos (não importados) com `applied_qty = 0`
   são removidos. Como o projeto está zerado, nada será apagado.
6. **NRC:** se alguma tarefa vinculada for NRC, dispara o Motor de O&A
   (`fn_calcular_oa_nrc`).

---

## 7. Consultas de validação pós-importação (rodar DEPOIS)

```sql
-- 7.1 Resumo das tarefas (comparar com seção 3)
SELECT
  COUNT(*) AS total_tasks,
  COUNT(*) FILTER (WHERE is_blocked_material) AS bloqueadas_material,
  COUNT(*) FILTER (WHERE estimated_material_cost > 0) AS tasks_com_custo,
  COALESCE(SUM(estimated_material_cost), 0) AS soma_custo_material
FROM mro_tasks
WHERE project_id = 22;

-- 7.2 Resumo dos vínculos (comparar com seção 4)
SELECT
  COUNT(tm.task_material_id) AS total_vinculos,
  COUNT(DISTINCT tm.task_id) AS tasks_com_material,
  COUNT(*) FILTER (WHERE tm.committed_qty > 0) AS empenhos_com_saldo,
  COUNT(*) FILTER (WHERE tm.committed_qty = 0) AS empenhos_zerados,
  COALESCE(SUM(tm.committed_total_cost), 0) AS custo_total_committed,
  COALESCE(SUM(tm.total_cost), 0) AS custo_total_planejado
FROM mro_task_materials tm
JOIN mro_tasks t ON t.task_id = tm.task_id
WHERE t.project_id = 22;

-- 7.3 Tarefas que ficaram bloqueadas por material
SELECT t.task_id, t.task_code, t.task_name, t.status_code
FROM mro_tasks t
WHERE t.project_id = 22 AND t.is_blocked_material = true
ORDER BY t.task_code;

-- 7.4 Amostra dos materiais vinculados
SELECT t.task_code, m.product_code, m.part_number, m.description,
       tm.planned_qty, tm.committed_qty, tm.material_source, tm.unit_cost,
       tm.total_cost, m.is_blocking_task
FROM mro_task_materials tm
JOIN mro_tasks t ON t.task_id = tm.task_id
JOIN mro_materials m ON m.material_id = tm.material_id
WHERE t.project_id = 22
ORDER BY t.task_code
LIMIT 50;

-- 7.5 Materiais criados/atualizados pela importação (comparar com seção 5)
SELECT COUNT(*) AS total_materiais,
       COUNT(*) FILTER (WHERE is_blocking_task) AS bloqueantes,
       COUNT(*) FILTER (WHERE NOT is_blocking_task) AS nao_bloqueantes
FROM mro_materials;

-- 7.6 Histórico gerado (audit trail)
SELECT h.task_id, t.task_code, h.action_taken, h.user_login, h.remarks, h.action_date
FROM mro_task_history h
JOIN mro_tasks t ON t.task_id = h.task_id
WHERE t.project_id = 22
ORDER BY h.action_date DESC
LIMIT 20;
```

---

## 8. Observações / riscos

- Os materiais vinculados terão dados do arquivo FLYBONDI (product_code, part_number,
  custos, status) — válido para testar a **regra**, mas não representa os materiais
  reais do GOL PR-GGE.
- O projeto 22 está **zerado** (0 vínculos), então a importação não vai conflitar
  com dados existentes.
- A flag manual da task `370020` foi **desmarcada** antes deste teste (agora 0
  bloqueadas no projeto).
- Se precisar reverter o teste: apagar os vínculos criados
  (`DELETE FROM mro_task_materials WHERE task_id IN (tarefas do projeto 22)`)
  e zerar `is_blocked_material`/`estimated_material_cost` das 26 tarefas.

---

## 9. Resultados PÓS-importação (27/08/2026 10:57)

> Queries rodadas via MCP após a importação. Comparar com seções 3, 4 e 5.

### 9.1 Tarefas (mro_tasks) — DEPOIS

| Métrica | ANTES | DEPOIS | Δ |
|---|---|---|---|
| Total de tarefas | 836 | **836** | 0 |
| Bloqueadas por material | 0 | **25** | +25 |
| Tarefas com custo de material | 0 | **26** | +26 |
| Soma de estimated_material_cost | 0,00 | **8.903.560,48** | +8.903.560,48 |

### 9.2 Vínculos (mro_task_materials) — DEPOIS

| Métrica | ANTES | DEPOIS | Δ |
|---|---|---|---|
| Total de vínculos | 0 | **149** | +149 |
| Tarefas com material | 0 | **26** | +26 |
| Empenhos com saldo (committed_qty > 0) | 0 | **47** | +47 |
| Empenhos zerados (committed_qty = 0) | 0 | **102** | +102 |
| Custo total committed | 0,00 | **590.332,47** | +590.332,47 |
| Custo total planejado (total_cost) | 0,00 | **8.903.560,48** | +8.903.560,48 |

### 9.3 As 25 tarefas bloqueadas por material

| Task Code | Nome |
|---|---|
| 370460 | REPLACE IDG FILTERS AND CHANGE OIL - LH |
| 370582 | REPLACE THE POSITIVE PRESSURE RELIEF VALVE FILTERS. |
| 370606 | REPLACE IDG FILTERS AND CHANGE OIL - RH |
| 370954 | DISINFECT PORTABLE WATER SYSTEM |
| 370964 | DISCARD AND REPLACE THE BLEED AIR IN-LINE FILTER. |
| 371058 | ACCOMPLISH DAILY CHECK. |
| 371392 | REPLACE THE EMERGENCY FLASHLIGHT BATTERIES |
| 375281 | PERFORM 737-53-1399 (SB) FUSELAGE SKIN LAP SPLICE INSPECTION |
| 379008 | AIRCRAFT FINAL INSPECTION |
| 379014 | AIRCRAFT REDELIVERY CLEANING |
| 379028 | CLEANING AFTER OPENING OF ACCESS FOR INSPECTION |
| 379037 | ACCESS PANELS CLOSING - UPPER FUSELAGE |
| 379038 | ACCESS PANELS CLOSING - RIGHT WING |
| 379039 | ACCESS PANELS CLOSING - LEFT WING |
| 379040 | ACCESS PANELS CLOSING - POWER PLANT |
| 379041 | ACCESS PANELS CLOSING - LOWER FUSELAGE |
| 379042 | ACCESS PANELS CLOSING - EMPENNAGE |
| 379043 | ACCESS PANELS CLOSING - DOORS |
| 379053 | PERFORM MAINTENANCE PRE-FLIGHT CHECKLIST. |
| 379055 | PERFORM REMOVAL PASSENGER SEATS |
| 37A003 | INTERIM REPAIR ON OUTBOARD AFT FLAP LH |
| 37A004 | LIGHTNING STRIKE AT RIVET IN STA 400 |
| 37A005 | LIGHTNING STRIKE AT FUSELAGE SKIN |
| 37A006 | LIGHT. REPLACE THE CATEGORY C REPAIR |
| 37A028 | PERFORM A PHYSICAL CHECK KPU |

> **Nota:** 25 bloqueadas, mas 26 com material. A única que não bloqueou é a `37A013`
> (material com Status=OK na planilha → sem bloqueio). ✅ Regra MRO-115 funcionando.

### 9.4 Amostra do comportamento das regras (detalhe)

**Task 370460 (bloqueada — 5 materiais):**

| Product | Part Number | planned | committed | unit_cost | Status planilha | Bloqueia? |
|---|---|---|---|---|---|---|
| EP090000000143 | M83248-1-905 | 1 | **1** | 5,02 | OK | Não |
| EP090000027252 | 65-90305-85 | 1 | **0** | 1.056,47 | - | **Sim** |
| EP090000004179 | M83248-1-226 | 1 | **1** | 20,70 | OK | Não |
| EP090000012107 | M83248-1-911 | 1 | **1** | 1,37 | OK-CHECK | Não |
| CS080400000029 | MOBIL JET OIL II | 10 | **0** | 2.154,67 | - | **Sim** |

> Confirmado: `committed_qty` só é contabilizado quando STATUS preenchido (OK/OK-CHECK);
> material bloqueante sem status (vazio/`-`) → `committed_qty = 0` e `is_blocked_material = true`.

### 9.5 Materiais (mro_materials) — DEPOIS

| Métrica | ANTES | DEPOIS | Δ |
|---|---|---|---|
| Total de materiais | 509 | **509** | 0 |
| Bloqueantes | 506 | **506** | 0 |
| Não bloqueantes | 3 | **3** | 0 |

> Nenhum material novo foi criado — todos os product_code do arquivo já existiam
> no cadastro global (UPSERT atualizou description/unit_cost). ✅

### 9.6 Tarefas com mais materiais zerados (maior impacto)

| Task | Materiais | com saldo | zerados |
|---|---|---|---|
| 379037 | 24 | 7 | **17** |
| 379041 | 15 | 1 | **14** |
| 379028 | 13 | 4 | 9 |
| 379042 | 11 | 4 | 7 |
| 379038 | 10 | 4 | 6 |
| 379039 | 10 | 4 | 6 |

### 9.7 Avaliação geral

- ✅ **Importação concluída com sucesso** (log: "Processo finalizado com sucesso!")
- ✅ **26 tarefas** receberam materiais (exatamente as previstas na auditoria)
- ✅ **25 tarefas bloqueadas** — todas com material bloqueante (`is_blocking_task=true`)
  e status vazio/`-` na planilha → **regra MRO-115 funcionando corretamente**
- ✅ **47 empenhos com saldo** contabilizado (status OK/OK-CHECK ou material não bloqueante)
- ✅ **102 empenhos zerados** (material bloqueante sem status)
- ✅ Custo total planejado: R$ 8.903.560,48
- ✅ Custo total committed: R$ 590.332,47 (só o que tem saldo real)
- ✅ Nenhum material duplicado (UPSERT por product_code+part_number+stock_location)
- ✅ Nenhuma NRC vinculada no projeto 22 → Motor O&A não foi disparado (comportamento esperado)

### 9.8 Limpeza / rollback (se necessário)

```sql
-- Reverter o teste (apaga vínculos do projeto 22 e zera flags/custos)
DELETE FROM mro_task_materials tm
USING mro_tasks t
WHERE tm.task_id = t.task_id AND t.project_id = 22;

UPDATE mro_tasks
SET is_blocked_material = false, estimated_material_cost = 0
WHERE project_id = 22;
```

---

LOG REAL DA IMPORTAÇÃO:
SISTEMA: Iniciando Debug...
[10:57:33] Verificando sessão do Projeto...
[10:57:33] Projeto atual identificado: ID 22
[10:57:33] Procurando arquivo Excel em: /var/www/html/developer/scriptcase/file/doc/COMPRAS-PRODUTO_EMPENHADOS_POR_PROJETO(30).xlsx
[10:57:33] Procurando biblioteca em: /var/www/html/developer/scriptcase/app/MRO_System/_lib/lib/php//../../libraries/sys/lib_excel/SimpleXLSX.php
[10:57:33] Biblioteca carregada com sucesso.
[10:57:34] Excel parseado. Total de linhas detectadas: 1204
[10:57:34] Linha 1: Tarefa 370607 não encontrada neste projeto.
[10:57:34] Linha 2: Tarefa 370607 não encontrada neste projeto.
[10:57:34] Linha 3: Tarefa 375076 não encontrada neste projeto.
[10:57:34] Linha 4: Tarefa 375076 não encontrada neste projeto.
[10:57:34] Linha 5: Tarefa 375148 não encontrada neste projeto.
[10:57:34] Linha 6: Tarefa 375149 não encontrada neste projeto.
[10:57:34] Linha 7: Tarefa 370035 não encontrada neste projeto.
[10:57:34] Linha 8: Tarefa 370036 não encontrada neste projeto.
[10:57:34] Linha 9: Tarefa 370096 não encontrada neste projeto.
[10:57:34] Linha 10: Tarefa 370096 não encontrada neste projeto.
[10:57:34] Linha 11: Material EP090000000143 OK (Status=OK) tarefa 370460
[10:57:34] Linha 12: Material EP090000027252 BLOQUEIA tarefa 370460 (Status=-)
[10:57:34] Linha 13: Material EP090000004179 OK (Status=OK) tarefa 370460
[10:57:34] Linha 14: Material EP090000012107 OK (Status=OK-CHECK) tarefa 370460
[10:57:34] Linha 15: Material EP090000000014 BLOQUEIA tarefa 370582 (Status=-)
[10:57:34] Linha 16: Material EP090000000143 OK (Status=OK) tarefa 370606
[10:57:34] Linha 17: Material EP090000027252 BLOQUEIA tarefa 370606 (Status=-)
[10:57:35] Linha 18: Material EP090000004179 OK (Status=OK) tarefa 370606
[10:57:35] Linha 19: Tarefa 370896 não encontrada neste projeto.
[10:57:35] Linha 20: Tarefa 370909 não encontrada neste projeto.
[10:57:35] Linha 21: Tarefa 370951 não encontrada neste projeto.
[10:57:35] Linha 22: Material CS080200000274 BLOQUEIA tarefa 370954 (Status=-)
[10:57:35] Linha 23: Material EP090000022586 OK (Status=OK) tarefa 370954
[10:57:35] Linha 24: Material CS080700002752 OK (Status=OK-CHECK) tarefa 370954
[10:57:35] Linha 25: Material EP090000001139 BLOQUEIA tarefa 370964 (Status=-)
[10:57:35] Linha 26: Material CS080100000672 OK (Status=OK) tarefa 370964
[10:57:35] Linha 27: Material CS030900000031 BLOQUEIA tarefa 371058 (Status=-)
[10:57:35] Linha 28: Material EP090000025725 BLOQUEIA tarefa 371058 (Status=-)
[10:57:35] Linha 29: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 30: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 31: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 32: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 33: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 34: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 35: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 36: Tarefa 371160 não encontrada neste projeto.
[10:57:35] Linha 37: Tarefa 371182 não encontrada neste projeto.
[10:57:35] Linha 38: Tarefa 371403 não encontrada neste projeto.
[10:57:35] Linha 39: Tarefa 371403 não encontrada neste projeto.
[10:57:35] Linha 40: Tarefa 371403 não encontrada neste projeto.
[10:57:35] Linha 41: Tarefa 371414 não encontrada neste projeto.
[10:57:35] Linha 42: Tarefa 371414 não encontrada neste projeto.
[10:57:35] Linha 43: Tarefa 371414 não encontrada neste projeto.
[10:57:35] Linha 44: Material CS080700001560 BLOQUEIA tarefa 379008 (Status=-)
[10:57:35] Linha 45: Material CS030900000031 BLOQUEIA tarefa 379014 (Status=-)
[10:57:35] Linha 46: Material CS080200000028 OK (is_blocking_task=false) tarefa 379014
[10:57:35] Linha 47: Material CS080200000001 OK (Status=OK) tarefa 379014
[10:57:35] Linha 48: Material CS080200000168 BLOQUEIA tarefa 379014 (Status=-)
[10:57:35] Linha 49: Material CS080200000044 BLOQUEIA tarefa 379014 (Status=-)
[10:57:35] Linha 50: Material CS080400000002 BLOQUEIA tarefa 379053 (Status=-)
[10:57:35] Linha 51: Material CS080200000028 OK (is_blocking_task=false) tarefa 379053
[10:57:35] Linha 52: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 53: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 54: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 55: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 56: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 57: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 58: Tarefa 370841 não encontrada neste projeto.
[10:57:35] Linha 59: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 60: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 61: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 62: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 63: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 64: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 65: Tarefa 370842 não encontrada neste projeto.
[10:57:35] Linha 66: Tarefa 370608 não encontrada neste projeto.
[10:57:35] Linha 67: Tarefa 370608 não encontrada neste projeto.
[10:57:35] Linha 68: Tarefa 370608 não encontrada neste projeto.
[10:57:35] Linha 69: Tarefa 371294 não encontrada neste projeto.
[10:57:35] Linha 70: Tarefa 371294 não encontrada neste projeto.
[10:57:35] Linha 71: Tarefa 371294 não encontrada neste projeto.
[10:57:35] Linha 72: Tarefa 371294 não encontrada neste projeto.
[10:57:35] Linha 73: Tarefa 371294 não encontrada neste projeto.
[10:57:35] Linha 74: Tarefa 371320 não encontrada neste projeto.
[10:57:35] Linha 75: Tarefa 371320 não encontrada neste projeto.
[10:57:35] Linha 76: Tarefa 371320 não encontrada neste projeto.
[10:57:35] Linha 77: Material RT256000000095 BLOQUEIA tarefa 371392 (Status=-)
[10:57:35] Linha 78: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 79: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 80: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 81: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 82: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 83: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 84: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 85: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 86: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 87: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 88: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 89: Tarefa 375222 não encontrada neste projeto.
[10:57:35] Linha 90: Material CS080300000310 BLOQUEIA tarefa 379055 (Status=-)
[10:57:35] Linha 91: Material CS080200000028 OK (is_blocking_task=false) tarefa 379028
[10:57:35] Linha 92: Material CS030900000031 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 93: Material CS080200000168 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 94: Material CS080700000025 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 95: Material CS080700000084 OK (Status=OK) tarefa 379028
[10:57:35] Linha 96: Material CS080200000172 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 97: Material CS080700000028 OK (Status=OK) tarefa 379028
[10:57:35] Linha 98: Material CS080100000619 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 99: Material CS080700000466 OK (Status=OK) tarefa 379028
[10:57:35] Linha 100: Material CS080700000174 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 101: Material CS080200000044 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 102: Material CS080700004694 BLOQUEIA tarefa 379028 (Status=-)
[10:57:35] Linha 103: Material CS080200000028 OK (is_blocking_task=false) tarefa 379037
[10:57:35] Linha 104: Material CS080700000174 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 105: Material EP090000036355 OK (Status=OK) tarefa 379037
[10:57:35] Linha 106: Material CS080100000009 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 107: Material CS080100000009 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 108: Material CS080100000301 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 109: Material CS080400000014 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 110: Material CS080700002481 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 111: Material EP090000007226 OK (Status=OK) tarefa 379037
[10:57:35] Linha 112: Material CS080400000248 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 113: Material EP090000005324 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 114: Material CS080300000045 BLOQUEIA tarefa 379037 (Status=-)
[10:57:35] Linha 115: Material EP090000019474 OK (Status=OK) tarefa 379037
[10:57:35] Linha 116: Material CS080200000028 OK (is_blocking_task=false) tarefa 379038
[10:57:35] Linha 117: Material CS080700000084 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 118: Material CS080100000009 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 119: Material CS080100000301 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 120: Material CS080400000014 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 121: Material CS080300000045 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 122: Material CS080700000085 OK (Status=OK) tarefa 379038
[10:57:35] Linha 123: Material CS090000000007 BLOQUEIA tarefa 379038 (Status=-)
[10:57:35] Linha 124: Material EP090000029500 OK (Status=OK) tarefa 379038
[10:57:35] Linha 125: Material EP090000006399 OK (Status=OK-CHECK) tarefa 379038
[10:57:36] Linha 126: Material CS080200000028 OK (is_blocking_task=false) tarefa 379039
[10:57:36] Linha 127: Material CS080700000084 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 128: Material CS080100000009 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 129: Material CS080100000301 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 130: Material CS080400000014 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 131: Material CS080300000045 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 132: Material CS080700000085 OK (Status=OK) tarefa 379039
[10:57:36] Linha 133: Material CS090000000007 BLOQUEIA tarefa 379039 (Status=-)
[10:57:36] Linha 134: Material EP090000029500 OK (Status=OK) tarefa 379039
[10:57:36] Linha 135: Material EP090000006399 OK (Status=OK-CHECK) tarefa 379039
[10:57:36] Linha 136: Material CS080300000045 BLOQUEIA tarefa 379040 (Status=-)
[10:57:36] Linha 137: Material CS080100000652 BLOQUEIA tarefa 379040 (Status=-)
[10:57:36] Linha 138: Material CS080300000047 BLOQUEIA tarefa 379040 (Status=-)
[10:57:36] Linha 139: Material CS080400000103 BLOQUEIA tarefa 379040 (Status=-)
[10:57:36] Linha 140: Material CS080700000174 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 141: Material CS080100000009 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 142: Material CS080100000301 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 143: Material CS080400000014 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 144: Material CS080700002777 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 145: Material CS080100000521 OK (Status=OK) tarefa 379041
[10:57:36] Linha 146: Material EP090000027131 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 147: Material EP090000034376 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 148: Material CS080100000728 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 149: Material CS030900000004 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 150: Material CS080700003783 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 151: Material CS080700003782 BLOQUEIA tarefa 379041 (Status=-)
[10:57:36] Linha 152: Material CS080200000028 OK (is_blocking_task=false) tarefa 379042
[10:57:36] Linha 153: Material CS080700000174 BLOQUEIA tarefa 379042 (Status=-)
[10:57:36] Linha 154: Material CS080100000009 BLOQUEIA tarefa 379042 (Status=-)
[10:57:36] Linha 155: Material CS080400000014 BLOQUEIA tarefa 379042 (Status=-)
[10:57:36] Linha 156: Material CS080300000045 BLOQUEIA tarefa 379042 (Status=-)
[10:57:36] Linha 157: Material CS090000000007 BLOQUEIA tarefa 379042 (Status=-)
[10:57:36] Linha 158: Material CS080300001122 OK (Status=OK-CHECK) tarefa 379042
[10:57:36] Linha 159: Material CS080100000048 BLOQUEIA tarefa 379043 (Status=-)
[10:57:36] Linha 160: Material CS080100000048 BLOQUEIA tarefa 379043 (Status=-)
[10:57:36] Linha 161: Material EP090000001733 OK (Status=OK-CHECK) tarefa 379043
[10:57:36] Linha 162: Material CS080400000369 OK (Status=OK) tarefa 379043
[10:57:36] Linha 163: Material CS080400000029 BLOQUEIA tarefa 370460 (Status=-)
[10:57:36] Linha 164: Material CS080400000029 BLOQUEIA tarefa 370606 (Status=-)
[10:57:36] Linha 165: Tarefa 370951 não encontrada neste projeto.
[10:57:36] Linha 166: Material CS080400000029 BLOQUEIA tarefa 371058 (Status=-)
[10:57:36] Linha 167: Material CS080400000027 BLOQUEIA tarefa 371058 (Status=-)
[10:57:36] Linha 168: Material CS080300000037 BLOQUEIA tarefa 375281 (Status=-)
[10:57:36] Linha 169: Material CS090000000344 BLOQUEIA tarefa 375281 (Status=-)
[10:57:36] Linha 170: Material CS080300000334 BLOQUEIA tarefa 375281 (Status=-)
[10:57:36] Linha 171: Material CS080700000025 BLOQUEIA tarefa 375281 (Status=-)
[10:57:36] Linha 172: Material CS080700000044 OK (Status=OK) tarefa 375281
[10:57:36] Linha 173: Tarefa N370055001 não encontrada neste projeto.
[10:57:36] Linha 174: Tarefa N370372001 não encontrada neste projeto.
[10:57:36] Linha 175: Tarefa N370372001 não encontrada neste projeto.
[10:57:36] Linha 176: Tarefa N370372001 não encontrada neste projeto.
[10:57:36] Linha 177: Tarefa N370411001 não encontrada neste projeto.
[10:57:36] Linha 178: Tarefa N370411002 não encontrada neste projeto.
[10:57:36] Linha 179: Tarefa N370981001 não encontrada neste projeto.
[10:57:36] Linha 180: Tarefa N370981005 não encontrada neste projeto.
[10:57:36] Linha 181: Tarefa N370981005 não encontrada neste projeto.
[10:57:36] Linha 182: Material CS080500000028 BLOQUEIA tarefa 375281 (Status=-)
[10:57:36] Linha 183: Material CS080400000003 BLOQUEIA tarefa 37A003 (Status=-)
[10:57:36] Linha 184: Material EP090000001550 OK (Status=OK) tarefa 37A003
[10:57:36] Linha 185: Material CS080400000391 OK (Status=OK) tarefa 37A003
[10:57:36] Linha 186: Material RT275100000135 BLOQUEIA tarefa 37A003 (Status=-)
[10:57:36] Linha 187: Material CS080100000649 BLOQUEIA tarefa 37A003 (Status=-)
[10:57:36] Linha 188: Material CS080400000003 BLOQUEIA tarefa 37A004 (Status=-)
[10:57:36] Linha 189: Material EP090000001550 OK (Status=OK) tarefa 37A004
[10:57:36] Linha 190: Material CS080400000391 OK (Status=OK) tarefa 37A004
[10:57:37] Linha 191: Material CS080100000649 BLOQUEIA tarefa 37A004 (Status=-)
[10:57:37] Linha 192: Material RT270000000130 BLOQUEIA tarefa 37A004 (Status=-)
[10:57:37] Linha 193: Material CS080400000003 BLOQUEIA tarefa 37A005 (Status=-)
[10:57:37] Linha 194: Material EP090000001550 OK (Status=OK) tarefa 37A005
[10:57:37] Linha 195: Material CS080400000391 OK (Status=OK) tarefa 37A005
[10:57:37] Linha 196: Material CS080100000649 BLOQUEIA tarefa 37A005 (Status=-)
[10:57:37] Linha 197: Material RT275100000137 BLOQUEIA tarefa 37A005 (Status=-)
[10:57:37] Linha 198: Material CS080400000003 BLOQUEIA tarefa 37A006 (Status=-)
[10:57:37] Linha 199: Material EP090000001550 OK (Status=OK) tarefa 37A006
[10:57:37] Linha 200: Material CS080400000391 OK (Status=OK) tarefa 37A006
[10:57:37] Linha 201: Material CS080100000649 BLOQUEIA tarefa 37A006 (Status=-)
[10:57:37] Linha 202: Material RT275100000137 BLOQUEIA tarefa 37A006 (Status=-)
[10:57:37] Linha 203: Tarefa 37A042 não encontrada neste projeto.
[10:57:37] Linha 204: Tarefa 37A042 não encontrada neste projeto.
[10:57:37] Linha 205: Tarefa 37A042 não encontrada neste projeto.
[10:57:37] Linha 206: Tarefa 37A042 não encontrada neste projeto.
[10:57:37] Linha 207: Material CS080700004070 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 208: Material EP090000036963 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 209: Material CS080700003329 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 210: Material EP090000036964 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 211: Material CS080400000238 OK (Status=OK) tarefa 379037
[10:57:37] Linha 212: Material CS080100000660 OK (Status=OK) tarefa 379037
[10:57:37] Linha 213: Material CS080700001906 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 214: Material CS080300000823 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 215: Material CS080400000470 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 216: Material CS080700002910 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 217: Tarefa 37A048 não encontrada neste projeto.
[10:57:37] Linha 218: Tarefa 37A048 não encontrada neste projeto.
[10:57:37] Linha 219: Tarefa 37A049 não encontrada neste projeto.
[10:57:37] Linha 220: Tarefa 37A049 não encontrada neste projeto.
[10:57:37] Linha 221: Material EP090000000186 BLOQUEIA tarefa 37A028 (Status=-)
[10:57:37] Linha 222: Material EP090000000188 OK (Status=OK) tarefa 37A028
[10:57:37] Linha 223: Material CS080300001017 BLOQUEIA tarefa 37A028 (Status=-)
[10:57:37] Linha 224: Tarefa 37A077 não encontrada neste projeto.
[10:57:37] Linha 225: Tarefa 37A078 não encontrada neste projeto.
[10:57:37] Linha 226: Tarefa 37A079 não encontrada neste projeto.
[10:57:37] Linha 227: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 228: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 229: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 230: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 231: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 232: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 233: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 234: Tarefa N370254001 não encontrada neste projeto.
[10:57:37] Linha 235: Tarefa N370254002 não encontrada neste projeto.
[10:57:37] Linha 236: Tarefa N370254002 não encontrada neste projeto.
[10:57:37] Linha 237: Tarefa N370254002 não encontrada neste projeto.
[10:57:37] Linha 238: Tarefa N370254002 não encontrada neste projeto.
[10:57:37] Linha 239: Tarefa N370254002 não encontrada neste projeto.
[10:57:37] Linha 240: Tarefa N370254003 não encontrada neste projeto.
[10:57:37] Linha 241: Tarefa N370254003 não encontrada neste projeto.
[10:57:37] Linha 242: Tarefa N370254003 não encontrada neste projeto.
[10:57:37] Linha 243: Tarefa N370254003 não encontrada neste projeto.
[10:57:37] Linha 244: Tarefa N370254003 não encontrada neste projeto.
[10:57:37] Linha 245: Tarefa N370254004 não encontrada neste projeto.
[10:57:37] Linha 246: Tarefa N370254004 não encontrada neste projeto.
[10:57:37] Linha 247: Tarefa N370254004 não encontrada neste projeto.
[10:57:37] Linha 248: Tarefa N370254004 não encontrada neste projeto.
[10:57:37] Linha 249: Tarefa N370254004 não encontrada neste projeto.
[10:57:37] Linha 250: Tarefa N370254005 não encontrada neste projeto.
[10:57:37] Linha 251: Tarefa N370254005 não encontrada neste projeto.
[10:57:37] Linha 252: Tarefa N370254005 não encontrada neste projeto.
[10:57:37] Linha 253: Tarefa N370254005 não encontrada neste projeto.
[10:57:37] Linha 254: Tarefa N370254005 não encontrada neste projeto.
[10:57:37] Linha 255: Tarefa N370254006 não encontrada neste projeto.
[10:57:37] Linha 256: Tarefa N370254006 não encontrada neste projeto.
[10:57:37] Linha 257: Tarefa N370254006 não encontrada neste projeto.
[10:57:37] Linha 258: Tarefa N370254006 não encontrada neste projeto.
[10:57:37] Linha 259: Tarefa N370254006 não encontrada neste projeto.
[10:57:37] Linha 260: Tarefa N370254007 não encontrada neste projeto.
[10:57:37] Linha 261: Tarefa N370254007 não encontrada neste projeto.
[10:57:37] Linha 262: Tarefa N370254007 não encontrada neste projeto.
[10:57:37] Linha 263: Tarefa N370254007 não encontrada neste projeto.
[10:57:37] Linha 264: Tarefa N370254007 não encontrada neste projeto.
[10:57:37] Linha 265: Tarefa N370255001 não encontrada neste projeto.
[10:57:37] Linha 266: Tarefa N370255001 não encontrada neste projeto.
[10:57:37] Linha 267: Tarefa N370255001 não encontrada neste projeto.
[10:57:37] Linha 268: Tarefa N370255001 não encontrada neste projeto.
[10:57:37] Linha 269: Tarefa N370255001 não encontrada neste projeto.
[10:57:37] Linha 270: Tarefa N370255002 não encontrada neste projeto.
[10:57:37] Linha 271: Tarefa N370255002 não encontrada neste projeto.
[10:57:37] Linha 272: Tarefa N370255002 não encontrada neste projeto.
[10:57:37] Linha 273: Tarefa N370255002 não encontrada neste projeto.
[10:57:37] Linha 274: Tarefa N370255002 não encontrada neste projeto.
[10:57:37] Linha 275: Tarefa N370255003 não encontrada neste projeto.
[10:57:37] Linha 276: Tarefa N370255003 não encontrada neste projeto.
[10:57:37] Linha 277: Tarefa N370255003 não encontrada neste projeto.
[10:57:37] Linha 278: Tarefa N370255003 não encontrada neste projeto.
[10:57:37] Linha 279: Tarefa N370255003 não encontrada neste projeto.
[10:57:37] Linha 280: Material EP090000036855 OK (Status=OK) tarefa 37A013
[10:57:37] Linha 281: Material CS080200000001 BLOQUEIA tarefa 379028 (Status=-)
[10:57:37] Linha 282: Material CS080200000001 BLOQUEIA tarefa 379037 (Status=-)
[10:57:37] Linha 283: Material CS080700000055 BLOQUEIA tarefa 379042 (Status=-)
[10:57:37] Linha 284: Material CS080700000085 OK (Status=OK) tarefa 379042
[10:57:37] Linha 285: Material CS080700004778 OK (Status=OK) tarefa 379042
[10:57:37] Linha 286: Material CS080700004433 BLOQUEIA tarefa 379042 (Status=-)
[10:57:37] Linha 287: Material CS080700004433 BLOQUEIA tarefa 379042 (Status=-)
[10:57:37] Linha 288: Material CS080700003482 OK (Status=OK) tarefa 379037
[10:57:37] Linha 289: Tarefa 37A035 não encontrada neste projeto.
[10:57:37] Linha 290: Tarefa 37A035 não encontrada neste projeto.
[10:57:37] Linha 291: Tarefa 371644 não encontrada neste projeto.
[10:57:37] Linha 292: Tarefa 371645 não encontrada neste projeto.
[10:57:37] Linha 293: Tarefa 37A035 não encontrada neste projeto.
[10:57:37] Linha 294: Material EP090000004179 BLOQUEIA tarefa 370606 (Status=-)
[10:57:37] Linha 295: Material CS080200000001 BLOQUEIA tarefa 379041 (Status=-)
[10:57:37] Linha 296: Tarefa N370267001 não encontrada neste projeto.
[10:57:37] Linha 297: Tarefa N370267002 não encontrada neste projeto.
[10:57:37] Linha 298: Tarefa N370267002 não encontrada neste projeto.
[10:57:37] Linha 299: Tarefa N370267002 não encontrada neste projeto.
[10:57:37] Linha 300: Tarefa N370267003 não encontrada neste projeto.
[10:57:37] Linha 301: Tarefa N370267003 não encontrada neste projeto.
[10:57:37] Linha 302: Tarefa N370267003 não encontrada neste projeto.
[10:57:37] Linha 303: Tarefa N370267007 não encontrada neste projeto.
[10:57:37] Linha 304: Tarefa N370267007 não encontrada neste projeto.
[10:57:37] Linha 305: Tarefa N370267007 não encontrada neste projeto.
[10:57:37] Linha 306: Tarefa N370269020 não encontrada neste projeto.
[10:57:37] Linha 307: Tarefa N370269020 não encontrada neste projeto.
[10:57:37] Linha 308: Tarefa N370269020 não encontrada neste projeto.
[10:57:37] Linha 309: Tarefa N370269025 não encontrada neste projeto.
[10:57:37] Linha 310: Tarefa N370269025 não encontrada neste projeto.
[10:57:37] Linha 311: Tarefa N370269025 não encontrada neste projeto.
[10:57:37] Linha 312: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 313: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 314: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 315: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 316: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 317: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 318: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 319: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 320: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 321: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 322: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 323: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 324: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 325: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 326: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 327: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 328: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 329: Tarefa N370379003 não encontrada neste projeto.
[10:57:37] Linha 330: Tarefa N379030002 não encontrada neste projeto.
[10:57:37] Linha 331: Tarefa N379030002 não encontrada neste projeto.
[10:57:37] Linha 332: Tarefa N379030004 não encontrada neste projeto.
[10:57:37] Linha 333: Tarefa N379030006 não encontrada neste projeto.
[10:57:37] Linha 334: Tarefa N379030006 não encontrada neste projeto.
[10:57:37] Linha 335: Tarefa N379030011 não encontrada neste projeto.
[10:57:37] Linha 336: Tarefa N379030011 não encontrada neste projeto.
[10:57:37] Linha 337: Tarefa N379030012 não encontrada neste projeto.
[10:57:37] Linha 338: Tarefa N379030012 não encontrada neste projeto.
[10:57:37] Linha 339: Tarefa N379031034 não encontrada neste projeto.
[10:57:37] Linha 340: Tarefa N379031034 não encontrada neste projeto.
[10:57:37] Linha 341: Tarefa N379031034 não encontrada neste projeto.
[10:57:37] Linha 342: Tarefa N379034027 não encontrada neste projeto.
[10:57:37] Linha 343: Tarefa N379034027 não encontrada neste projeto.
[10:57:37] Linha 344: Tarefa N379034027 não encontrada neste projeto.
[10:57:37] Linha 345: Tarefa N370253001 não encontrada neste projeto.
[10:57:37] Linha 346: Tarefa N370268020 não encontrada neste projeto.
[10:57:37] Linha 347: Tarefa N370275002 não encontrada neste projeto.
[10:57:37] Linha 348: Tarefa N370275002 não encontrada neste projeto.
[10:57:37] Linha 349: Tarefa N370275002 não encontrada neste projeto.
[10:57:37] Linha 350: Tarefa N370275002 não encontrada neste projeto.
[10:57:37] Linha 351: Tarefa N370300001 não encontrada neste projeto.
[10:57:37] Linha 352: Tarefa N370371001 não encontrada neste projeto.
[10:57:37] Linha 353: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 354: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 355: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 356: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 357: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 358: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 359: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 360: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 361: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 362: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 363: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 364: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 365: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 366: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 367: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 368: Tarefa N370371001 não encontrada neste projeto.
[10:57:38] Linha 369: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 370: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 371: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 372: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 373: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 374: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 375: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 376: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 377: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 378: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 379: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 380: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 381: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 382: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 383: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 384: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 385: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 386: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 387: Tarefa N370388002 não encontrada neste projeto.
[10:57:38] Linha 388: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 389: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 390: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 391: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 392: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 393: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 394: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 395: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 396: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 397: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 398: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 399: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 400: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 401: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 402: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 403: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 404: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 405: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 406: Tarefa N370388003 não encontrada neste projeto.
[10:57:38] Linha 407: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 408: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 409: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 410: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 411: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 412: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 413: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 414: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 415: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 416: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 417: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 418: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 419: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 420: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 421: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 422: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 423: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 424: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 425: Tarefa N370388004 não encontrada neste projeto.
[10:57:38] Linha 426: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 427: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 428: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 429: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 430: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 431: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 432: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 433: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 434: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 435: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 436: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 437: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 438: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 439: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 440: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 441: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 442: Tarefa N370404001 não encontrada neste projeto.
[10:57:38] Linha 443: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 444: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 445: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 446: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 447: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 448: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 449: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 450: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 451: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 452: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 453: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 454: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 455: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 456: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 457: Tarefa N370405001 não encontrada neste projeto.
[10:57:38] Linha 458: Tarefa N379031031 não encontrada neste projeto.
[10:57:38] Linha 459: Tarefa N379031031 não encontrada neste projeto.
[10:57:38] Linha 460: Tarefa N379031031 não encontrada neste projeto.
[10:57:38] Linha 461: Tarefa N379031031 não encontrada neste projeto.
[10:57:38] Linha 462: Tarefa N379031031 não encontrada neste projeto.
[10:57:38] Linha 463: Tarefa N379054001 não encontrada neste projeto.
[10:57:38] Linha 464: Tarefa N379054002 não encontrada neste projeto.
[10:57:38] Linha 465: Tarefa N37A060001 não encontrada neste projeto.
[10:57:38] Linha 466: Tarefa N37A071001 não encontrada neste projeto.
[10:57:38] Linha 467: Tarefa N370268010 não encontrada neste projeto.
[10:57:38] Linha 468: Tarefa N370268010 não encontrada neste projeto.
[10:57:38] Linha 469: Tarefa N370268011 não encontrada neste projeto.
[10:57:38] Linha 470: Tarefa N370268013 não encontrada neste projeto.
[10:57:38] Linha 471: Tarefa N370268014 não encontrada neste projeto.
[10:57:38] Linha 472: Tarefa N370268014 não encontrada neste projeto.
[10:57:38] Linha 473: Tarefa N370268014 não encontrada neste projeto.
[10:57:38] Linha 474: Tarefa N370268014 não encontrada neste projeto.
[10:57:38] Linha 475: Tarefa N370268014 não encontrada neste projeto.
[10:57:38] Linha 476: Tarefa N370268015 não encontrada neste projeto.
[10:57:38] Linha 477: Tarefa N370268015 não encontrada neste projeto.
[10:57:38] Linha 478: Tarefa N370269027 não encontrada neste projeto.
[10:57:38] Linha 479: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 480: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 481: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 482: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 483: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 484: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 485: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 486: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 487: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 488: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 489: Tarefa N370269028 não encontrada neste projeto.
[10:57:38] Linha 490: Tarefa N370448002 não encontrada neste projeto.
[10:57:38] Linha 491: Tarefa N379001001 não encontrada neste projeto.
[10:57:38] Linha 492: Tarefa N37A008005 não encontrada neste projeto.
[10:57:38] Linha 493: Tarefa N37A008005 não encontrada neste projeto.
[10:57:38] Linha 494: Tarefa N37A008005 não encontrada neste projeto.
[10:57:38] Linha 495: Tarefa N37A008005 não encontrada neste projeto.
[10:57:38] Linha 496: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 497: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 498: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 499: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 500: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 501: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 502: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 503: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 504: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 505: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 506: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 507: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 508: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 509: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 510: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 511: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 512: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 513: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 514: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 515: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 516: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 517: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 518: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 519: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 520: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 521: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 522: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 523: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 524: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 525: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 526: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 527: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 528: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 529: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 530: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 531: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 532: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 533: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 534: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 535: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 536: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 537: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 538: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 539: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 540: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 541: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 542: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 543: Tarefa N370175001 não encontrada neste projeto.
[10:57:38] Linha 544: Tarefa N370248002 não encontrada neste projeto.
[10:57:38] Linha 545: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 546: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 547: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 548: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 549: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 550: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 551: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 552: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 553: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 554: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 555: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 556: Tarefa N370275001 não encontrada neste projeto.
[10:57:38] Linha 557: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 558: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 559: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 560: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 561: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 562: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 563: Tarefa N370376001 não encontrada neste projeto.
[10:57:38] Linha 564: Tarefa N370376002 não encontrada neste projeto.
[10:57:38] Linha 565: Tarefa N370376002 não encontrada neste projeto.
[10:57:38] Linha 566: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 567: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 568: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 569: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 570: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 571: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 572: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 573: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 574: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 575: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 576: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 577: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 578: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 579: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 580: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 581: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 582: Tarefa N370385001 não encontrada neste projeto.
[10:57:38] Linha 583: Tarefa N379001002 não encontrada neste projeto.
[10:57:38] Linha 584: Tarefa N379001002 não encontrada neste projeto.
[10:57:38] Linha 585: Tarefa N379030020 não encontrada neste projeto.
[10:57:38] Linha 586: Tarefa N379030020 não encontrada neste projeto.
[10:57:38] Linha 587: Tarefa N379030021 não encontrada neste projeto.
[10:57:38] Linha 588: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 589: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 590: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 591: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 592: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 593: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 594: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 595: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 596: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 597: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 598: Tarefa N379034028 não encontrada neste projeto.
[10:57:38] Linha 599: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 600: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 601: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 602: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 603: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 604: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 605: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 606: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 607: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 608: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 609: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 610: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 611: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 612: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 613: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 614: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 615: Tarefa N379034029 não encontrada neste projeto.
[10:57:38] Linha 616: Tarefa N379035009 não encontrada neste projeto.
[10:57:38] Linha 617: Tarefa N379035009 não encontrada neste projeto.
[10:57:38] Linha 618: Tarefa N379035010 não encontrada neste projeto.
[10:57:38] Linha 619: Tarefa N379035010 não encontrada neste projeto.
[10:57:38] Linha 620: Tarefa N379038001 não encontrada neste projeto.
[10:57:38] Linha 621: Tarefa N379038001 não encontrada neste projeto.
[10:57:38] Linha 622: Tarefa N37A055001 não encontrada neste projeto.
[10:57:38] Linha 623: Tarefa NN370388004001 não encontrada neste projeto.
[10:57:38] Linha 624: Tarefa NN370404001001 não encontrada neste projeto.
[10:57:38] Linha 625: Tarefa NN370404001001 não encontrada neste projeto.
[10:57:38] Linha 626: Tarefa NN370405001001 não encontrada neste projeto.
[10:57:38] Linha 627: Tarefa NN370405001001 não encontrada neste projeto.
[10:57:38] Linha 628: Tarefa NN370405001001 não encontrada neste projeto.
[10:57:38] Linha 629: Tarefa NN370405001001 não encontrada neste projeto.
[10:57:38] Linha 630: Tarefa NN370989001001 não encontrada neste projeto.
[10:57:38] Linha 631: Tarefa NN370989001001 não encontrada neste projeto.
[10:57:38] Linha 632: Tarefa NN370989001001 não encontrada neste projeto.
[10:57:38] Linha 633: Tarefa NN370989001001 não encontrada neste projeto.
[10:57:38] Linha 634: Tarefa NN370989001001 não encontrada neste projeto.
[10:57:38] Linha 635: Tarefa N370174001 não encontrada neste projeto.
[10:57:38] Linha 636: Tarefa N370267006 não encontrada neste projeto.
[10:57:38] Linha 637: Tarefa N370267006 não encontrada neste projeto.
[10:57:38] Linha 638: Tarefa N370267009 não encontrada neste projeto.
[10:57:38] Linha 639: Tarefa N370267010 não encontrada neste projeto.
[10:57:38] Linha 640: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 641: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 642: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 643: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 644: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 645: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 646: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 647: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 648: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 649: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 650: Tarefa N370267012 não encontrada neste projeto.
[10:57:38] Linha 651: Tarefa N370267013 não encontrada neste projeto.
[10:57:38] Linha 652: Tarefa N379035024 não encontrada neste projeto.
[10:57:38] Linha 653: Tarefa N379035024 não encontrada neste projeto.
[10:57:38] Linha 654: Tarefa N379054013 não encontrada neste projeto.
[10:57:38] Linha 655: Tarefa N379054014 não encontrada neste projeto.
[10:57:38] Linha 656: Tarefa N379054016 não encontrada neste projeto.
[10:57:38] Linha 657: Tarefa N379054017 não encontrada neste projeto.
[10:57:38] Linha 658: Tarefa N379054018 não encontrada neste projeto.
[10:57:38] Linha 659: Tarefa N379054019 não encontrada neste projeto.
[10:57:38] Linha 660: Tarefa N379054020 não encontrada neste projeto.
[10:57:38] Linha 661: Tarefa N379054020 não encontrada neste projeto.
[10:57:38] Linha 662: Tarefa N379054021 não encontrada neste projeto.
[10:57:38] Linha 663: Tarefa N379054022 não encontrada neste projeto.
[10:57:38] Linha 664: Tarefa N379054024 não encontrada neste projeto.
[10:57:38] Linha 665: Tarefa N37A058001 não encontrada neste projeto.
[10:57:38] Linha 666: Tarefa N37A059001 não encontrada neste projeto.
[10:57:38] Linha 667: Tarefa N37A059001 não encontrada neste projeto.
[10:57:38] Linha 668: Tarefa N37A061001 não encontrada neste projeto.
[10:57:38] Linha 669: Tarefa N37A069002 não encontrada neste projeto.
[10:57:38] Linha 670: Tarefa N37A069002 não encontrada neste projeto.
[10:57:38] Linha 671: Tarefa NN379032001001 não encontrada neste projeto.
[10:57:38] Linha 672: Tarefa NN379032001001 não encontrada neste projeto.
[10:57:38] Linha 673: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 674: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 675: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 676: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 677: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 678: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 679: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 680: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 681: Tarefa N370248001 não encontrada neste projeto.
[10:57:38] Linha 682: Tarefa N370269006 não encontrada neste projeto.
[10:57:38] Linha 683: Tarefa N370269007 não encontrada neste projeto.
[10:57:38] Linha 684: Tarefa N370269010 não encontrada neste projeto.
[10:57:38] Linha 685: Tarefa N370269011 não encontrada neste projeto.
[10:57:38] Linha 686: Tarefa N370269013 não encontrada neste projeto.
[10:57:38] Linha 687: Tarefa N370537003 não encontrada neste projeto.
[10:57:38] Linha 688: Tarefa N370537003 não encontrada neste projeto.
[10:57:38] Linha 689: Tarefa N370537003 não encontrada neste projeto.
[10:57:38] Linha 690: Tarefa N370537004 não encontrada neste projeto.
[10:57:38] Linha 691: Tarefa N370537004 não encontrada neste projeto.
[10:57:38] Linha 692: Tarefa N370537005 não encontrada neste projeto.
[10:57:38] Linha 693: Tarefa N370537005 não encontrada neste projeto.
[10:57:38] Linha 694: Tarefa N370537006 não encontrada neste projeto.
[10:57:38] Linha 695: Tarefa N370537006 não encontrada neste projeto.
[10:57:38] Linha 696: Tarefa N370589001 não encontrada neste projeto.
[10:57:38] Linha 697: Tarefa N371261001 não encontrada neste projeto.
[10:57:38] Linha 698: Tarefa N379030019 não encontrada neste projeto.
[10:57:38] Linha 699: Tarefa N379035046 não encontrada neste projeto.
[10:57:38] Linha 700: Tarefa N379035046 não encontrada neste projeto.
[10:57:38] Linha 701: Tarefa N379035046 não encontrada neste projeto.
[10:57:38] Linha 702: Tarefa N379035046 não encontrada neste projeto.
[10:57:38] Linha 703: Tarefa N379035047 não encontrada neste projeto.
[10:57:38] Linha 704: Tarefa N379054005 não encontrada neste projeto.
[10:57:38] Linha 705: Tarefa N379054008 não encontrada neste projeto.
[10:57:39] Linha 706: Tarefa N379054010 não encontrada neste projeto.
[10:57:39] Linha 707: Tarefa N379054011 não encontrada neste projeto.
[10:57:39] Linha 708: Tarefa N37A008004 não encontrada neste projeto.
[10:57:39] Linha 709: Tarefa N37A016001 não encontrada neste projeto.
[10:57:39] Linha 710: Tarefa N37A025001 não encontrada neste projeto.
[10:57:39] Linha 711: Tarefa N37A025001 não encontrada neste projeto.
[10:57:39] Linha 712: Tarefa NN370047001001 não encontrada neste projeto.
[10:57:39] Linha 713: Tarefa NN370047001001 não encontrada neste projeto.
[10:57:39] Linha 714: Tarefa NN370047001001 não encontrada neste projeto.
[10:57:39] Linha 715: Tarefa NN370047001001 não encontrada neste projeto.
[10:57:39] Linha 716: Tarefa NN379031003001 não encontrada neste projeto.
[10:57:39] Linha 717: Tarefa N370268021 não encontrada neste projeto.
[10:57:39] Linha 718: Tarefa N370268021 não encontrada neste projeto.
[10:57:39] Linha 719: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 720: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 721: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 722: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 723: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 724: Tarefa N370298001 não encontrada neste projeto.
[10:57:39] Linha 725: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 726: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 727: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 728: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 729: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 730: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 731: Tarefa N370299001 não encontrada neste projeto.
[10:57:39] Linha 732: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 733: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 734: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 735: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 736: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 737: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 738: Tarefa N370299002 não encontrada neste projeto.
[10:57:39] Linha 739: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 740: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 741: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 742: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 743: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 744: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 745: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 746: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 747: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 748: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 749: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 750: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 751: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 752: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 753: Tarefa N370396001 não encontrada neste projeto.
[10:57:39] Linha 754: Tarefa N370537002 não encontrada neste projeto.
[10:57:39] Linha 755: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 756: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 757: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 758: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 759: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 760: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 761: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 762: Tarefa N370587001 não encontrada neste projeto.
[10:57:39] Linha 763: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 764: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 765: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 766: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 767: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 768: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 769: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 770: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 771: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 772: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 773: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 774: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 775: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 776: Tarefa N370989001 não encontrada neste projeto.
[10:57:39] Linha 777: Tarefa N379009001 não encontrada neste projeto.
[10:57:39] Linha 778: Tarefa N37A023001 não encontrada neste projeto.
[10:57:39] Linha 779: Tarefa N37A062001 não encontrada neste projeto.
[10:57:39] Linha 780: Tarefa N37A062001 não encontrada neste projeto.
[10:57:39] Linha 781: Tarefa N37A062001 não encontrada neste projeto.
[10:57:39] Linha 782: Tarefa N37A065001 não encontrada neste projeto.
[10:57:39] Linha 783: Tarefa N37A068001 não encontrada neste projeto.
[10:57:39] Linha 784: Tarefa N37A068001 não encontrada neste projeto.
[10:57:39] Linha 785: Tarefa NN370175001001 não encontrada neste projeto.
[10:57:39] Linha 786: Tarefa NN370175001001 não encontrada neste projeto.
[10:57:39] Linha 787: Tarefa NN370175001001 não encontrada neste projeto.
[10:57:39] Linha 788: Tarefa N370268003 não encontrada neste projeto.
[10:57:39] Linha 789: Tarefa N370268016 não encontrada neste projeto.
[10:57:39] Linha 790: Tarefa N370268016 não encontrada neste projeto.
[10:57:39] Linha 791: Tarefa N370268016 não encontrada neste projeto.
[10:57:39] Linha 792: Tarefa N370268017 não encontrada neste projeto.
[10:57:39] Linha 793: Tarefa N370268017 não encontrada neste projeto.
[10:57:39] Linha 794: Tarefa N370268018 não encontrada neste projeto.
[10:57:39] Linha 795: Tarefa N370268018 não encontrada neste projeto.
[10:57:39] Linha 796: Tarefa N370268018 não encontrada neste projeto.
[10:57:39] Linha 797: Tarefa N370268018 não encontrada neste projeto.
[10:57:39] Linha 798: Tarefa N370269017 não encontrada neste projeto.
[10:57:39] Linha 799: Tarefa N370269024 não encontrada neste projeto.
[10:57:39] Linha 800: Tarefa N370269024 não encontrada neste projeto.
[10:57:39] Linha 801: Tarefa N370538001 não encontrada neste projeto.
[10:57:39] Linha 802: Tarefa N370538001 não encontrada neste projeto.
[10:57:39] Linha 803: Tarefa N370538001 não encontrada neste projeto.
[10:57:39] Linha 804: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 805: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 806: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 807: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 808: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 809: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 810: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 811: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 812: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 813: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 814: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 815: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 816: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 817: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 818: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 819: Tarefa N371674001 não encontrada neste projeto.
[10:57:39] Linha 820: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 821: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 822: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 823: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 824: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 825: Tarefa N379009002 não encontrada neste projeto.
[10:57:39] Linha 826: Tarefa N379030018 não encontrada neste projeto.
[10:57:39] Linha 827: Tarefa N379030018 não encontrada neste projeto.
[10:57:39] Linha 828: Tarefa N379030022 não encontrada neste projeto.
[10:57:39] Linha 829: Tarefa N379030022 não encontrada neste projeto.
[10:57:39] Linha 830: Tarefa N379034025 não encontrada neste projeto.
[10:57:39] Linha 831: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 832: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 833: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 834: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 835: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 836: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 837: Tarefa N379041001 não encontrada neste projeto.
[10:57:39] Linha 838: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 839: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 840: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 841: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 842: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 843: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 844: Tarefa N379041002 não encontrada neste projeto.
[10:57:39] Linha 845: Tarefa N379054027 não encontrada neste projeto.
[10:57:39] Linha 846: Tarefa N379054028 não encontrada neste projeto.
[10:57:39] Linha 847: Tarefa N379054029 não encontrada neste projeto.
[10:57:39] Linha 848: Tarefa N379054030 não encontrada neste projeto.
[10:57:39] Linha 849: Tarefa N37A008006 não encontrada neste projeto.
[10:57:39] Linha 850: Tarefa N37A008006 não encontrada neste projeto.
[10:57:39] Linha 851: Tarefa N37A008006 não encontrada neste projeto.
[10:57:39] Linha 852: Tarefa N37A008007 não encontrada neste projeto.
[10:57:39] Linha 853: Tarefa N37A008008 não encontrada neste projeto.
[10:57:39] Linha 854: Tarefa N37A063001 não encontrada neste projeto.
[10:57:39] Linha 855: Tarefa N37A063001 não encontrada neste projeto.
[10:57:39] Linha 856: Tarefa N37A064001 não encontrada neste projeto.
[10:57:39] Linha 857: Tarefa N370268002 não encontrada neste projeto.
[10:57:39] Linha 858: Tarefa N370268008 não encontrada neste projeto.
[10:57:39] Linha 859: Tarefa N370269008 não encontrada neste projeto.
[10:57:39] Linha 860: Tarefa N370269008 não encontrada neste projeto.
[10:57:39] Linha 861: Tarefa N370269008 não encontrada neste projeto.
[10:57:39] Linha 862: Tarefa N370269008 não encontrada neste projeto.
[10:57:39] Linha 863: Tarefa N370269012 não encontrada neste projeto.
[10:57:39] Linha 864: Tarefa N370269012 não encontrada neste projeto.
[10:57:39] Linha 865: Tarefa N370269012 não encontrada neste projeto.
[10:57:39] Linha 866: Tarefa N370414001 não encontrada neste projeto.
[10:57:39] Linha 867: Tarefa N370414001 não encontrada neste projeto.
[10:57:39] Linha 868: Tarefa N370414001 não encontrada neste projeto.
[10:57:39] Linha 869: Tarefa N370414001 não encontrada neste projeto.
[10:57:39] Linha 870: Tarefa N370414001 não encontrada neste projeto.
[10:57:39] Linha 871: Tarefa N370470001 não encontrada neste projeto.
[10:57:39] Linha 872: Tarefa N370470002 não encontrada neste projeto.
[10:57:39] Linha 873: Tarefa N370470003 não encontrada neste projeto.
[10:57:39] Linha 874: Tarefa N370981002 não encontrada neste projeto.
[10:57:39] Linha 875: Tarefa N370981007 não encontrada neste projeto.
[10:57:39] Linha 876: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 877: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 878: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 879: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 880: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 881: Tarefa N379041003 não encontrada neste projeto.
[10:57:39] Linha 882: Tarefa N379041004 não encontrada neste projeto.
[10:57:39] Linha 883: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 884: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 885: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 886: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 887: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 888: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 889: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 890: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 891: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 892: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 893: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 894: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 895: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 896: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 897: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 898: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 899: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 900: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 901: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 902: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 903: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 904: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 905: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 906: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 907: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 908: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 909: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 910: Tarefa N37A014001 não encontrada neste projeto.
[10:57:39] Linha 911: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 912: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 913: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 914: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 915: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 916: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 917: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 918: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 919: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 920: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 921: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 922: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 923: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 924: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 925: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 926: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 927: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 928: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 929: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 930: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 931: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 932: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 933: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 934: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 935: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 936: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 937: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 938: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 939: Tarefa N37A014002 não encontrada neste projeto.
[10:57:39] Linha 940: Tarefa N37A060002 não encontrada neste projeto.
[10:57:39] Linha 941: Tarefa N37A073001 não encontrada neste projeto.
[10:57:39] Linha 942: Tarefa N37A073001 não encontrada neste projeto.
[10:57:39] Linha 943: Tarefa N37A073002 não encontrada neste projeto.
[10:57:39] Linha 944: Tarefa N37A073003 não encontrada neste projeto.
[10:57:39] Linha 945: Tarefa N37A073003 não encontrada neste projeto.
[10:57:39] Linha 946: Tarefa N37A074001 não encontrada neste projeto.
[10:57:39] Linha 947: Tarefa NN370388003002 não encontrada neste projeto.
[10:57:39] Linha 948: Tarefa NN370388003002 não encontrada neste projeto.
[10:57:39] Linha 949: Tarefa NN370388003002 não encontrada neste projeto.
[10:57:39] Linha 950: Tarefa NN379009006001 não encontrada neste projeto.
[10:57:39] Linha 951: Tarefa N370268006 não encontrada neste projeto.
[10:57:39] Linha 952: Tarefa N370268007 não encontrada neste projeto.
[10:57:39] Linha 953: Tarefa N370268022 não encontrada neste projeto.
[10:57:39] Linha 954: Tarefa N370269015 não encontrada neste projeto.
[10:57:39] Linha 955: Tarefa N370269015 não encontrada neste projeto.
[10:57:39] Linha 956: Tarefa N370269015 não encontrada neste projeto.
[10:57:39] Linha 957: Tarefa N370269015 não encontrada neste projeto.
[10:57:39] Linha 958: Tarefa N370269016 não encontrada neste projeto.
[10:57:39] Linha 959: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 960: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 961: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 962: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 963: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 964: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 965: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 966: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 967: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 968: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 969: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 970: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 971: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 972: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 973: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 974: Tarefa N370302001 não encontrada neste projeto.
[10:57:39] Linha 975: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 976: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 977: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 978: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 979: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 980: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 981: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 982: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 983: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 984: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 985: Tarefa N379035059 não encontrada neste projeto.
[10:57:39] Linha 986: Tarefa N379052001 não encontrada neste projeto.
[10:57:39] Linha 987: Tarefa N379052001 não encontrada neste projeto.
[10:57:39] Linha 988: Tarefa N379052001 não encontrada neste projeto.
[10:57:39] Linha 989: Tarefa N379052002 não encontrada neste projeto.
[10:57:39] Linha 990: Tarefa N379052004 não encontrada neste projeto.
[10:57:39] Linha 991: Tarefa N379052006 não encontrada neste projeto.
[10:57:39] Linha 992: Tarefa N379052007 não encontrada neste projeto.
[10:57:39] Linha 993: Tarefa N379052008 não encontrada neste projeto.
[10:57:39] Linha 994: Tarefa N379052009 não encontrada neste projeto.
[10:57:39] Linha 995: Tarefa N379052010 não encontrada neste projeto.
[10:57:39] Linha 996: Tarefa N379052011 não encontrada neste projeto.
[10:57:39] Linha 997: Tarefa N379052011 não encontrada neste projeto.
[10:57:39] Linha 998: Tarefa N379054003 não encontrada neste projeto.
[10:57:39] Linha 999: Tarefa NN370268012001 não encontrada neste projeto.
[10:57:39] Linha 1000: Tarefa NN370268012001 não encontrada neste projeto.
[10:57:39] Linha 1001: Tarefa NN370268012002 não encontrada neste projeto.
[10:57:39] Linha 1002: Tarefa NN370268012002 não encontrada neste projeto.
[10:57:39] Linha 1003: Tarefa NN370268012002 não encontrada neste projeto.
[10:57:39] Linha 1004: Tarefa NN370268012002 não encontrada neste projeto.
[10:57:39] Linha 1005: Tarefa NN370268012003 não encontrada neste projeto.
[10:57:39] Linha 1006: Tarefa NN370268012004 não encontrada neste projeto.
[10:57:39] Linha 1007: Tarefa NN370268012004 não encontrada neste projeto.
[10:57:39] Linha 1008: Tarefa NN370268012004 não encontrada neste projeto.
[10:57:39] Linha 1009: Tarefa NNN370371001001001 não encontrada neste projeto.
[10:57:39] Linha 1010: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1011: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1012: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1013: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1014: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1015: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1016: Tarefa N370222001 não encontrada neste projeto.
[10:57:39] Linha 1017: Tarefa N370268004 não encontrada neste projeto.
[10:57:39] Linha 1018: Tarefa N370269005 não encontrada neste projeto.
[10:57:39] Linha 1019: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1020: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1021: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1022: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1023: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1024: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1025: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1026: Tarefa 37A082 não encontrada neste projeto.
[10:57:39] Linha 1027: Tarefa 37A083 não encontrada neste projeto.
[10:57:39] Linha 1028: Material CS080400000014 BLOQUEIA tarefa 379041 (Status=-)
[10:57:39] Linha 1029: Material CS080700000055 BLOQUEIA tarefa 379041 (Status=-)
[10:57:39] Linha 1030: Material CS080700000084 BLOQUEIA tarefa 379041 (Status=-)
[10:57:39] Linha 1031: Tarefa N370268001 não encontrada neste projeto.
[10:57:39] Linha 1032: Tarefa N370269026 não encontrada neste projeto.
[10:57:39] Linha 1033: Tarefa N379052012 não encontrada neste projeto.
[10:57:39] Linha 1034: Tarefa N379052012 não encontrada neste projeto.
[10:57:39] Linha 1035: Tarefa N379052012 não encontrada neste projeto.
[10:57:39] Linha 1036: Tarefa N379052012 não encontrada neste projeto.
[10:57:39] Linha 1037: Tarefa N379052013 não encontrada neste projeto.
[10:57:39] Linha 1038: Tarefa N379052013 não encontrada neste projeto.
[10:57:39] Linha 1039: Tarefa N379052013 não encontrada neste projeto.
[10:57:39] Linha 1040: Tarefa N379052013 não encontrada neste projeto.
[10:57:39] Linha 1041: Tarefa N379052014 não encontrada neste projeto.
[10:57:39] Linha 1042: Tarefa N379052014 não encontrada neste projeto.
[10:57:39] Linha 1043: Tarefa N379052014 não encontrada neste projeto.
[10:57:39] Linha 1044: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1045: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1046: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1047: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1048: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1049: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1050: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1051: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1052: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1053: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1054: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1055: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1056: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1057: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1058: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1059: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1060: Tarefa N379052015 não encontrada neste projeto.
[10:57:39] Linha 1061: Tarefa N370092001 não encontrada neste projeto.
[10:57:39] Linha 1062: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1063: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1064: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1065: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1066: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1067: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1068: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1069: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1070: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1071: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1072: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1073: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1074: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1075: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1076: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1077: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1078: Tarefa N370092001 não encontrada neste projeto.
[10:57:40] Linha 1079: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1080: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1081: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1082: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1083: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1084: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1085: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1086: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1087: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1088: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1089: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1090: Tarefa N370124001 não encontrada neste projeto.
[10:57:40] Linha 1091: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1092: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1093: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1094: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1095: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1096: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1097: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1098: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1099: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1100: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1101: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1102: Tarefa N370130001 não encontrada neste projeto.
[10:57:40] Linha 1103: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1104: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1105: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1106: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1107: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1108: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1109: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1110: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1111: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1112: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1113: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1114: Tarefa N370400001 não encontrada neste projeto.
[10:57:40] Linha 1115: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1116: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1117: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1118: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1119: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1120: Tarefa N370403001 não encontrada neste projeto.
[10:57:40] Linha 1121: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1122: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1123: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1124: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1125: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1126: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1127: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1128: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1129: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1130: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1131: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1132: Tarefa N370538002 não encontrada neste projeto.
[10:57:40] Linha 1133: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1134: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1135: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1136: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1137: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1138: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1139: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1140: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1141: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1142: Tarefa N370596001 não encontrada neste projeto.
[10:57:40] Linha 1143: Tarefa N379009007 não encontrada neste projeto.
[10:57:40] Linha 1144: Tarefa N379009007 não encontrada neste projeto.
[10:57:40] Linha 1145: Tarefa N379009007 não encontrada neste projeto.
[10:57:40] Linha 1146: Tarefa N379009007 não encontrada neste projeto.
[10:57:40] Linha 1147: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1148: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1149: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1150: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1151: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1152: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1153: Tarefa N379042001 não encontrada neste projeto.
[10:57:40] Linha 1154: Tarefa N37A014003 não encontrada neste projeto.
[10:57:40] Linha 1155: Tarefa N37A014003 não encontrada neste projeto.
[10:57:40] Linha 1156: Tarefa N37A080001 não encontrada neste projeto.
[10:57:40] Linha 1157: Tarefa N37A080001 não encontrada neste projeto.
[10:57:40] Linha 1158: Tarefa N37A080002 não encontrada neste projeto.
[10:57:40] Linha 1159: Tarefa N370281001 não encontrada neste projeto.
[10:57:40] Linha 1160: Tarefa N370281001 não encontrada neste projeto.
[10:57:40] Linha 1161: Tarefa N370281001 não encontrada neste projeto.
[10:57:40] Linha 1162: Tarefa N370281001 não encontrada neste projeto.
[10:57:40] Linha 1163: Tarefa N370287002 não encontrada neste projeto.
[10:57:40] Linha 1164: Tarefa N370287002 não encontrada neste projeto.
[10:57:40] Linha 1165: Tarefa N370287002 não encontrada neste projeto.
[10:57:40] Linha 1166: Tarefa N370288001 não encontrada neste projeto.
[10:57:40] Linha 1167: Tarefa N370288001 não encontrada neste projeto.
[10:57:40] Linha 1168: Tarefa N370290001 não encontrada neste projeto.
[10:57:40] Linha 1169: Tarefa N370290001 não encontrada neste projeto.
[10:57:40] Linha 1170: Tarefa N370290001 não encontrada neste projeto.
[10:57:40] Linha 1171: Tarefa N370895001 não encontrada neste projeto.
[10:57:40] Linha 1172: Tarefa N370895001 não encontrada neste projeto.
[10:57:40] Linha 1173: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1174: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1175: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1176: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1177: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1178: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1179: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1180: Tarefa N371675001 não encontrada neste projeto.
[10:57:40] Linha 1181: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1182: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1183: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1184: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1185: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1186: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1187: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1188: Tarefa N371675002 não encontrada neste projeto.
[10:57:40] Linha 1189: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1190: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1191: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1192: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1193: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1194: Tarefa N371675003 não encontrada neste projeto.
[10:57:40] Linha 1195: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1196: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1197: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1198: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1199: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1200: Tarefa N371675004 não encontrada neste projeto.
[10:57:40] Linha 1201: Tarefa N371675005 não encontrada neste projeto.
[10:57:40] Linha 1202: Tarefa N371675006 não encontrada neste projeto.
[10:57:40] Linha 1203: Tarefa N371675006 não encontrada neste projeto.
[10:57:40] Linha 1204: Tarefa N37A082001 não encontrada neste projeto.
[10:57:40] Processamento concluído. Iniciando limpeza de resíduos...
[10:57:40] Atualizando custos gerais e executando Motor de O&A...
[10:57:40] Processo finalizado com sucesso!