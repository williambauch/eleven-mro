# Renomeação `mro_nrc_approval_log` → `mro_task_history`

**Tarefa:** MRO-126
**Migration:** `__Tarefas/MRO-126/migrations/04_MRO-126_rename_mro_task_history.sql`

## Contexto

A tabela `mro_nrc_approval_log` deixou de ser usada **somente** para Não-Rotinas. Desde o MRO-117 ela é o **log único de auditoria de transições de status** das tasks (rotinas e NRCs), com `user_login = 'mro_engine'` para eventos automáticos. O nome `mro_task_history` reflete melhor o propósito real.

## Impacto total

- **1 FK** (`task_id → mro_tasks`) — preservada automaticamente pelo `RENAME`
- **0 views** referenciam a tabela
- **0 apps ScriptCase** usam a tabela como `nome_tabela`
- **21 arquivos de código** alterados (20 `.scriptcase` + 1 biblioteca `mro_engine.php`)
- **2 arquivos de backup** mantidos intactos (`backup_11082026_mro_engine.php`, `backup_18072026_mro_engine.php`)

---

## Arquivos alterados (antes → depois)

> Apenas a string `mro_nrc_approval_log` → `mro_task_history` foi alterada em cada ocorrência. Comentários internos que citavam o nome antigo também foram atualizados.

### 1. `_Bibliotecas_Internas/mro_engine.php` (4 ocorrências)

**Antes:**
```php
// MRO-117: Registra em mro_nrc_approval_log
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
```
```php
// MRO-117: Adiciona log de auditoria no mro_nrc_approval_log
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks)
             VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
```
```php
// MRO-117: Registra aprovacao automatica em mro_nrc_approval_log
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
```
```php
// MRO-117: Registra aprovacao automatica em mro_nrc_approval_log
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
```

**Depois:**
```php
// MRO-117: Registra em mro_task_history
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
```
```php
// MRO-117: Adiciona log de auditoria no mro_task_history
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks)
             VALUES ($v_task_id, 'OA_REVISION', 'mro_engine', '$msg')");
```
```php
// MRO-117: Registra aprovacao automatica em mro_task_history
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
```
```php
// MRO-117: Registra aprovacao automatica em mro_task_history
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) 
             VALUES ($v_task_id, 'AUTO_APPROVE', 'mro_engine', '$msg')");
```

---

### 2. `Over And Above/grid_mro_tasks_oa_details/button/Aprovar/onRecord.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```

---

### 3. `Over And Above/grid_mro_tasks_oa_details/button/Reprovar/onRecord.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```

---

### 4. `tasks/form_public_mro_nrc/button/btn_aprovar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```

---

### 5. `tasks/form_public_mro_nrc/button/btn_cancelar.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'CANCELLED', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'CANCELLED', '$user')");
```

---

### 6. `tasks/form_public_mro_nrc/button/btn_enviar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks) VALUES ($id, 'SENT_TO_CLIENT', '$user', 'Renegociação feita com o cliente')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) VALUES ($id, 'SENT_TO_CLIENT', '$user', 'Renegociação feita com o cliente')");
```

---

### 7. `tasks/form_public_mro_nrc/button/btn_enviar_coord.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_CODRDINATOR', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_CODRDINATOR', '$user')");
```

---

### 8. `tasks/form_public_mro_nrc/button/btn_enviar_eng.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_ENGINEERING', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_ENGINEERING', '$user')");
```

---

### 9. `tasks/form_public_mro_nrc/button/btn_enviar_prog.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_PROG', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_PROG', '$user')");
```

---

### 10. `tasks/form_public_mro_nrc/button/btn_reprovar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```

---

### 11. `tasks/form_public_mro_nrc/button/btn_validar_prog.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'PROGRAMMING_OK', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'PROGRAMMING_OK', '$user')");
```

---

### 12. `tasks/form_public_mro_tasks/button/btn_aprovar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'APPROVED_BY_CLIENT', '$user')");
```

---

### 13. `tasks/form_public_mro_tasks/button/btn_cancelar.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'CANCELLED', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'CANCELLED', '$user')");
```

---

### 14. `tasks/form_public_mro_tasks/button/btn_enviar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login, remarks) VALUES ($id, 'SENT_TO_CLIENT', '$user', 'Renegociação feita com o cliente')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login, remarks) VALUES ($id, 'SENT_TO_CLIENT', '$user', 'Renegociação feita com o cliente')");
```

---

### 15. `tasks/form_public_mro_tasks/button/btn_enviar_coord.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_CODRDINATOR', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_CODRDINATOR', '$user')");
```

---

### 16. `tasks/form_public_mro_tasks/button/btn_enviar_eng.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_ENGINEERING', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_ENGINEERING', '$user')");
```

---

### 17. `tasks/form_public_mro_tasks/button/btn_enviar_prog.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_PROG', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'SENT_TO_PROG', '$user')");
```

---

### 18. `tasks/form_public_mro_tasks/button/btn_reprovar_cliente.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'REJECTED_BY_CLIENT', '$user')");
```

---

### 19. `tasks/form_public_mro_tasks/button/btn_validar_prog.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) VALUES ($id, 'PROGRAMMING_OK', '$user')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) VALUES ($id, 'PROGRAMMING_OK', '$user')");
```

---

### 20. `tasks/form_public_mro_tasks/button/btn_validar_prog_rotina.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) 
             VALUES ($var_id, 'PROGRAMMING_OK', '" . addslashes($var_user) . "')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) 
             VALUES ($var_id, 'PROGRAMMING_OK', '" . addslashes($var_user) . "')");
```

---

### 21. `Timesheet/control_pause_task/events/07_onValidateSuccess/onValidateSuccess.scriptcase`

**Antes:**
```php
sc_exec_sql("INSERT INTO mro_nrc_approval_log (task_id, action_taken, user_login) 
             VALUES ($var_tid, 'SENT_TO_PROG_VIA_HANDOVER', '[usr_login]')");
```
**Depois:**
```php
sc_exec_sql("INSERT INTO mro_task_history (task_id, action_taken, user_login) 
             VALUES ($var_tid, 'SENT_TO_PROG_VIA_HANDOVER', '[usr_login]')");
```

---

## Ordem de aplicação

1. Rodar a migration `04_MRO-126_rename_mro_task_history.sql` no banco
2. Aplicar o busca/replace nos 21 arquivos de código (ou recriar as apps no ScriptCase com o novo nome)
3. Validar: `SELECT table_name FROM information_schema.tables WHERE table_name = 'mro_task_history';` (1 linha)

## Reversão

```sql
ALTER TABLE "public".mro_task_history RENAME TO mro_nrc_approval_log;
ALTER SEQUENCE "public".mro_task_history_log_id_seq RENAME TO mro_nrc_approval_log_log_id_seq;
```
E reverter o busca/replace nos arquivos.
