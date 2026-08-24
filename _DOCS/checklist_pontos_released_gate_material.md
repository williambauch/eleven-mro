# Checklist — Pontos que mudam a Task para `RELEASED` e status do Gate de Material

> Objetivo: mapear TODOS os lugares no código que fazem `status_code = 'RELEASED'`
> e verificar se cada um **já tem o gate de material** (detecção de material
> bloqueante `is_blocking_task` que desvia para `PENDING_PROVIDER`).
>
> Regra de negócio (MRO-126):
> - JIC **SEM** material bloqueante → `RELEASED` (direto)
> - JIC **COM** material bloqueante → `PENDING_PROVIDER` (fila da provedoria; **sem** assignments)
> - Provedoria libera → `RELEASED` + `is_blocked_material = false`

---

## 1. Liberação por Planejamento (Rotina e NRC em planejamento)

| Item | Valor |
|---|---|
| Arquivo | `_Bibliotecas_Internas/mro_engine.php` — `fn_liberar_task_para_execucao()` |
| Chamado por | `tasks/grid_public_mro_tasks/button/btn_liberar_para_execucao/onRecord.scriptcase` (origem `PLANEJADOR`) |
| UPDATE | `UPDATE mro_tasks SET status_code = '$var_novo_status'` (linha ~412) |
| Gate de material | ✅ **SIM** — `is_blocking_task = true` ou NULL (não aplicado) → `PENDING_PROVIDER`; sem assignments |
| Origem alternativa | `tasks/grid_provedoria_release/...` e `Almoxarifado/grid_provedoria_release/...` (origem `PROVEDORIA`) → sempre `RELEASED` + desmarca `is_blocked_material` |

**Status: ✅ Já implementado**

---

## 2. Liberação pela Provedoria (Gated Process)

| Item | Valor |
|---|---|
| Arquivo | `_Bibliotecas_Internas/mro_engine.php` — `fn_liberar_task_para_execucao()` (origem `PROVEDORIA`) |
| Chamado por | `Almoxarifado/grid_provedoria_release/button/btn_liberar_para_execucao/onRecord.scriptcase` |
| UPDATE | `UPDATE mro_tasks SET status_code = 'RELEASED'` + `is_blocked_material = false` (linha ~424) |
| Gate de material | ✅ **SIM** — é o release FINAL: origem `PROVEDORIA` sempre vai para `RELEASED` e desmarca o bloqueio |

**Status: ✅ Já implementado**

---

## 3. Validação de Rotina pela Programação

| Item | Valor |
|---|---|
| Arquivo | `tasks/form_public_mro_tasks/button/btn_validar_prog_rotina.scriptcase` |
| UPDATE | `UPDATE mro_tasks SET status_code = 'RELEASED', updated_at = CURRENT_TIMESTAMP` (linha ~204) |
| Usa `mro_engine`? | ❌ Não — lógica própria (P6, saldo de horas, cria/atualiza assignments inline) |
| Gate de material | ❌ **NÃO TEM** — vai direto para `RELEASED` sem verificar material bloqueante |

**Status: ❌ Falta implementar** (se a regra deve valer também aqui)

---

## 4. Aprovação de NRC pelo Cliente (O&A — Over And Above)

| Item | Valor |
|---|---|
| Arquivo | `Over And Above/grid_mro_tasks_oa_details/button/Aprovar/onRecord.scriptcase` |
| UPDATE | `UPDATE mro_tasks SET status_code = 'RELEASED'` (linha ~11) |
| Usa `mro_engine`? | ❌ Não — UPDATE direto + `mro_nrc_approval_log` |
| Gate de material | ❌ **NÃO TEM** |

**Status: ❌ Falta implementar** (se a regra deve valer também aqui)

---

## 5. Auto-aprovação NRC pelo Motor O&A (dentro do CAP)

| Item | Valor |
|---|---|
| Arquivo | `_Bibliotecas_Internas/mro_engine.php` — `fn_calcular_oa_nrc()` |
| UPDATE | `UPDATE mro_tasks SET ... status_code = '$novo_status'` onde `$novo_status = 'RELEASED'` (linhas 183-206) |
| Usa `mro_engine`? | ✅ Sim (é função do próprio arquivo) |
| Gate de material | ❌ **NÃO TEM** — auto-approve vai direto para `RELEASED` (e cria assignments via `fn_criar_assignments_por_skill`) |

**Status: ❌ Falta implementar** (se a regra deve valer também para NRC auto-aprovada)

---

## 6. Importação de Empenhos / Ações automáticas (migrations e auxiliares)

| Item | Valor |
|---|---|
| Arquivo | `__Tarefas/MRO-126/migrations/01_MRO-126_tasks_released_para_in_progress.sql` (UPDATE pontual em lote) |
| Gate de material | N/A (migration pontual, não é fluxo de liberação) |
| Observação | Não é um ponto de liberação operacional |

**Status: ⚠️ Fora de escopo (migration pontual)**

---

## 7. Apps antigas / backup (não ativas)

| Item | Valor |
|---|---|
| Arquivo | `backup/grid_public_mro_tasks_copy/...`, `backup/grid_public_mro_tasks_15072026_MRO-117/...`, `backup/form_public_mro_nrc/...` |
| Gate de material | N/A (apps em `backup/`, não usadas) |

**Status: ⚠️ Ignorar (backup)**

---

## Resumo

| # | Ponto de liberação | Usa `mro_engine`? | Gate de material? |
|---|---|---|---|
| 1 | Planejamento (grid_public_mro_tasks) | ✅ | ✅ **OK** |
| 2 | Provedoria (grid_provedoria_release) | ✅ | ✅ **OK** |
| 3 | Programação rotina (btn_validar_prog_rotina) | ❌ | ❌ **FALTA** |
| 4 | Aprovação cliente O&A (grid_mro_tasks_oa_details) | ❌ | ❌ **FALTA** |
| 5 | Auto-approve NRC (fn_calcular_oa_nrc) | ✅ | ❌ **FALTA** |

### Pendências (itens 3, 4 e 5)

Se a regra de negócio deve valer para **todos** os pontos de liberação, é preciso:

1. **Criar função auxiliar** no `mro_engine.php`:
   ```php
   function fn_tem_material_bloqueante($v_task_id) {
       sc_lookup(rs_tm, "SELECT COUNT(*) FROM mro_task_materials tm
                         JOIN mro_materials m ON m.material_id = tm.material_id
                         WHERE tm.task_id = " . (int)$v_task_id . "
                           AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)");
       return (!empty({rs_tm}) && (int){rs_tm[0][0]} > 0);
   }
   ```
2. **Aplicar** nos 3 pontos (3, 4, 5): se `fn_tem_material_bloqueante()` → `PENDING_PROVIDER` (sem assignments); senão → `RELEASED`.
3. **Cadastrar** o status `PENDING_PROVIDER` em `mro_sys_status` (label, cor, ícone) e ajustar o Kanban/dashboards que filtram por status.

---

## Verificação feita em

- `_Bibliotecas_Internas/mro_engine.php` (linhas 385-447 — gate já implementado em `fn_liberar_task_para_execucao`)
- `tasks/form_public_mro_tasks/button/btn_validar_prog_rotina.scriptcase` (linha ~204)
- `Over And Above/grid_mro_tasks_oa_details/button/Aprovar/onRecord.scriptcase` (linha ~11)
- `_Bibliotecas_Internas/mro_engine.php` `fn_calcular_oa_nrc` (linhas 183-206)
- `_Bibliotecas_Internas/backup_20082026_mro_engine.php` (versão anterior, sem o gate em `fn_liberar_task_para_execucao` — confirma que o gate foi adicionado em 20/08)
