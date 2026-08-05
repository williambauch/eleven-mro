# PLANO - Item 5: Consulta de Ferramentas Pendentes de Devolucao

## Melhorar vinculacao de recursos nas tarefas, visualizacao de estoques e acesso a documentos

---

## 1. OBJETIVO

Criar grid/relatorio para consultar ferramentas pendentes de devolucao
(em posse dos funcionarios) - retiradas (checkout) sem devolucao (checkin nulo).

---

## 2. ESTADO ATUAL (verificado no banco)

| Fato | Valor |
|------|-------|
| Tabela | `mro_tool_transactions` (movimentada pelo terminal `blank_mro_ferramentaria`) |
| Status | `ACTIVE` (em aberto) e `CLOSED` (devolvido) |
| Total de transacoes | 17 |
| **Pendentes (ACTIVE)** | **7** - todas sem checkin_time |
| Ferramenta em emprestimo | `mro_tools.status = 'FERRAMENTA EM EMPRESTIMO'` |
| Vinculo de posse | `employee_id` -> `mro_employees` (full_name, employee_registration) |
| Task | `task_id` -> `mro_tasks` |
| Usuarios | `checkout_user` / `checkin_user` (login do ScriptCase) |

Obs: a tabela `mro_tool_movements` e **legada** (119 registros sem checkin,
sem employee). O fluxo atual do sistema usa `mro_tool_transactions`.

---

## 3. DECISOES DE DESIGN

- **Nova app**: `grid_mro_tools_pending_return` (consulta global, sem filtro por task)
- **Query**: `mro_tool_transactions` com `status = 'ACTIVE'` (equivalente a
  `checkin_time IS NULL`) + JOIN em `mro_tools` (descricao/PN) + JOIN em
  `mro_employees` (funcionario em posse) + JOIN em `mro_tasks` (tarefa)
- **Destaque visual**: dias com a ferramenta (checkout ate hoje) via onRecord

---

## 4. ARQUIVOS

| Arquivo | Acao |
|---------|------|
| `tasks/grid_mro_tools_pending_return/sql/schema.sql` | CRIAR - SELECT da consulta |
| `tasks/grid_mro_tools_pending_return/config.json` | CRIAR - config da grid |
| `tasks/grid_mro_tools_pending_return/events/04_onRecord/onRecord.scriptcase` | CRIAR - destaque visual |

---

## 5. SQL DA CONSULTA

```sql
SELECT
    tt.transaction_id,
    tt.tool_id,
    t.part_number,
    t.description,
    t.serial_number,
    t.status AS tool_status,
    e.full_name AS funcionario,
    e.employee_registration AS matricula,
    tt.task_id,
    tt.checkout_time,
    tt.checkout_user,
    (CURRENT_DATE - tt.checkout_time::date) AS dias_em_posse
FROM mro_tool_transactions tt
LEFT JOIN mro_tools t      ON t.tool_id = tt.tool_id
LEFT JOIN mro_employees e  ON e.employee_id = tt.employee_id
WHERE tt.status = 'ACTIVE'
ORDER BY tt.checkout_time ASC
```

---

## 6. FILTROS UTEIS NA GRID

- Por `part_number`/`description` (busca livre)
- Por `funcionario` (full_name)
- Por `dias_em_posse` (ferramentas ha mais de X dias)

---

## 7. RISCOS E PONTOS DE ATENCAO

- **7 transacoes ACTIVE atualmente** - volume baixo, sem problema de performance
- **Ferramenta com multiplas transacoes ACTIVE**: mesma tool_id pode aparecer
  varias vezes se o terminal permitir - indicador visual no onRecord
- **`condition_on_return`**: preenchido apenas na devolucao (CLOSED), nao
  aparece na consulta de pendentes
- **Regra de negocio**: transacao `ACTIVE` = ferramenta em posse do funcionario.
  A devolucao (checkin) muda para `CLOSED` no terminal `blank_mro_ferramentaria`
