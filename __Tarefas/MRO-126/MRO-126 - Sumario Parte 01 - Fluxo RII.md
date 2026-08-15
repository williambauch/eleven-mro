# 🔄 Revisão Chão de Fábrica & Backoffice — Parte 01: Fluxo de Execução, Aprovação e Duplo Check (RII)

> **Parte 01** — Alterações referentes ao documento [`Fluxo de Execução, Aprovação e Duplo Check (RII).md`](Fluxo%20de%20Execu%C3%A7%C3%A3o%2C%20Aprova%C3%A7%C3%A3o%20e%20Duplo%20Check%20%28RII%29.md)
>
> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice: [`MRO-126 - Sumario Parte 02 - Chão de Fábrica e Backoffice.md`](MRO-126%20-%20Sumario%20Parte%2002%20-%20Ch%C3%A3o%20de%20F%C3%A1brica%20e%20Backoffice.md)

## 🎯 Objetivo

Implementar o fluxo de **Execução → Aprovação → Duplo Check (RII)** das tarefas, garantindo conformidade regulatória (ANAC) e segregação rígida de funções, conforme a máquina de estados `SUPSIG → PENDING_INSP1 → PENDING_INSP2 → COMPLETED`.

---

## 📝 Log de Implementação

### 12/08/2026

**1. Transição da Task RELEASED → IN_PROGRESS na primeira atribuição**

Regra: quando a TASK é "liberada para execução" ou "auto-approve do mro_engine", ela fica com status `RELEASED`. Ao atribuir o primeiro mecânico, a task deve ir para `IN_PROGRESS`.

- Arquivo: `tasks/form_public_mro_task_assignments_planned/events/14_onBeforeUpdate/onBeforeUpdate.scriptcase`
- Lógica adicionada (bloco MRO-126):
  - Descobre o `task_id` do assignment que está sendo atribuído
  - Conta mecânicos já atribuídos à mesma task (excluindo o assignment atual, `executed_by_employee_id IS NOT NULL`)
  - Se for o primeiro mecânico e a task estiver `RELEASED`, executa UPDATE atômico: `status_code = 'IN_PROGRESS'`, `updated_at = CURRENT_TIMESTAMP`, `updated_by = [usr_login]` (com `WHERE status_code = 'RELEASED'` para evitar regressão de status e race condition)
  - Registra `sc_log_add` com a transição `RELEASED -> IN_PROGRESS`
- Macros: `sc_lookup`, `sc_exec_sql`, `sc_log_add`
- Obs: não usar `sc_error_update` como condição booleana (é variável de mensagem de erro do banco)

**2. Refatoração da aba "Concluídas" do Painel do Supervisor**

- App: `form_public_mro_task_assignments_completed` → **renomeada** para `grid_public_mro_task_assignments_completed` (Form → Grid)
- Pasta: `tasks/grid_public_mro_task_assignments_completed/`
- SQL com JOINs (todos os campos com alias):
  - `mro_projects` → `p6_proj_id` (project_code)
  - `mro_tasks` → `CONCAT(task_code, ' - ', task_name)` (task_desc)
  - `mro_employees` → `full_name` (employee_name) via `executed_by_employee_id` (campo `employee_id` é NULL no banco)
- Filtros mantidos: `planned_skill_id = [usr_skill_id]`, `(supervisor_id IS NULL OR supervisor_id = [usr_employee_id])`, `status_code IN ('PENDING_HANDOVER','SUPSIG')`
- Campo virtual `ver_historico` removido — será botão de barra de ação no ScriptCase
- Eventos reestruturados para Grid: `01_onApplicationInit` (insert/update/delete off), `02_onNavigate`, `03_onScriptInit`, `04_onRecord`, `05_onHeader`, `06_onFooter`
- Referências atualizadas: `ROOT/tabs_supervisor/sql/schema.sql` (serializado), `_DOCS/producao_manutencao/` (fluxo.md, tabs_supervisor.md, doc da app), `__Tarefas/MRO-119`, `__Tarefas/MRO-120`, `__Tarefas/MRO-122`
- Docs: `_DOCS/producao_manutencao/grid_public_mro_task_assignments_completed.md`

**3. Botão "Fechar" na blank_mro_timeline**

- Arquivo: `ROOT/blank_mro_timeline/events/01_onExecute/onExecute.scriptcase`
- Adicionada classe CSS `.scButton_danger` (vermelha) e botão `Fechar` com `onclick="self.parent.tb_remove();"` para fechar o modal aberto no "Ver Histórico"

### 13/08/2026 — Decisões do Gerente de Projeto (fluxo RII)

**1. Assinatura digital → FUTURO**
- Hoje o sistema deve apenas **registrar quem aprovou** (Inspetor 1/2)
- Já implementado: `mro_task_history` grava `INSPECTOR_1`/`INSPECTOR_2` com `user_login` + `action_date`
- Quando a assinatura digital (tokens/hash) for implementada, complementará o registro atual

**2. Time de Registros (Etapa 4) → FUTURO + PERFIL SEPARADO**
- Painel **separado do menu do supervisor** (não fica no `menu_supervisor`)
- Criar novo **grupo "Registro"** com acesso exclusivo
- Tela exibirá tasks `COMPLETED` para conferência final e geração do Job Card ("Zero Papel")

### 13/08/2026 — Implementação: Perfil REGISTRO + Painel de Registros

**Migration:** `__Tarefas/MRO-126/migrations/06_MRO-126_perfil_registro.sql`
- **Grupo:** `REGISTRO` (group_id = 13)
- **Usuário teste:** `registro` / `Registro@321`
- **App:** `grid_public_mro_task_registro` (tipo `cons`, somente leitura)
- **Permissões:** Administrador (1) + REGISTRO (13) — access Y, insert/update/delete vazios, export/print Y

**App nova:** `tasks/grid_public_mro_task_registro/`
- `config.json` + `sql/schema.sql` + eventos padrão de Grid
- **SQL:** **TODAS** as tasks `COMPLETED` (sem filtro de RII — o Time de Registros audita todo o encerramento)
- Sem filtro de skill — é painel de auditoria geral (Time de Registros)

**Menu (`sec_menu`):** criar item **"Auditoria"** > subitem **"Painel de Registros"** → `grid_public_mro_task_registro` (configurado no IDE)

**Credenciais:** `_DOCS/credenciais_acesso.md` atualizado com o grupo 13 (REGISTRO)

---

## Apps do MRO-126 (Painel do Supervisor / Fluxo RII)

| App | Tipo | Função | Acesso |
|---|---|---|---|
| `menu_supervisor` | Menu | Menu responsivo do supervisor (substitui `tabs_supervisor`) | Administrador, SUPERVISOR |
| `grid_public_mro_task_assignments_progress` | Grid | Atribuições em Andamento (substitui form antigo) | Administrador, SUPERVISOR |
| `grid_public_mro_task_assignments_blocked` | Grid | Atribuições com Impedimentos (substitui form antigo) | Administrador, SUPERVISOR |
| `grid_public_mro_task_assignments_completed` | Grid | Atribuições Concluídas (substitui form antigo) | Administrador, SUPERVISOR |
| `grid_public_mro_tasks_approval` | Grid | Aprovação de Tasks em SUPSIG (roteamento RII) | Administrador, SUPERVISOR |
| `grid_public_mro_tasks_insp` | Grid | Inspeção RII (PENDING_INSP1/PENDING_INSP2) — renomeada de `grid_public_mro_task_assignments_insp` | Administrador, SUPERVISOR |
| `grid_public_mro_task_registro` | Grid | Painel de Registros (TODAS as tasks COMPLETED) — perfil REGISTRO | Administrador, REGISTRO |
| `grid_public_mro_task_history` | Grid | Histórico de transições de uma task (`mro_task_history`) — filtro por `[glo_task_id]` | Administrador, SUPERVISOR |

> **Obs:** as 8 apps estão implementadas. (a `grid_public_mro_task_history` foi implementada/importada em 13/08/2026)

---
##  SUMARIO DE ALTERAÇÕES (MRO-126) PARTE 01 - WILLIAM BAUCH
##  Aprovação de Tarefas e Inspeção RII (Duplo Check)!

## Regras de Negócio por Aplicação

## menu_supervisor

### Menu responsivo do supervisor

**`Timesheet/menu_supervisor/`**
- Substitui o antigo `tabs_supervisor` (Tabs) por um **Menu responsivo** (tablet/celular), com a mesma identidade visual do sistema
- Itens: A Distribuir, Em Andamento, Com Impedimentos, Concluídas, Finalização (aprovação) e Inspeção
- Regra de negócio: itens de **Finalização** e **Inspeção** ficam visíveis **somente para usuários marcados como inspetor** (`[usr_is_inspector]`)
- Implementado via `sc_menu_delete("item_5")` e `sc_menu_delete("item_6")` no `events/onApplicationInit` quando `[usr_is_inspector]` é falso; `sc_reset_menu_delete()` restaura para inspetores

---

## grid_public_mro_task_assignments_progress

### Atribuições em Andamento

**`tasks/grid_public_mro_task_assignments_progress/`**
- Grid das alocações em execução (status `ASSIGNED`, `IN_PROGRESS`)
- Regra de negócio: lista apenas assignments da **skill do supervisor logado** (`planned_skill_id = [usr_skill_id]`) e da sua equipe (`supervisor_id` nulo ou igual ao usuário)
- JOINs com `mro_projects`, `mro_tasks` e `mro_employees` para exibir código do projeto, descrição da task e nome do mecânico
- Regra de negócio: exibe o status do assignment (`assignments_status_code`) e o status da task (`task_status_code`)

---

## grid_public_mro_task_assignments_blocked

### Atribuições com Impedimentos

**`tasks/grid_public_mro_task_assignments_blocked/`**
- Grid das alocações bloqueadas (status `BLOCKED`)
- Regra de negócio: lista apenas assignments da **skill do supervisor logado** e da sua equipe (mesmo filtro das demais abas)
- JOINs com `mro_projects`, `mro_tasks` e `mro_employees`
- Exibe status do assignment e da task para o supervisor identificar o impedimento

---

## grid_public_mro_task_assignments_completed

### Atribuições Concluídas (aba do supervisor)

**`tasks/grid_public_mro_task_assignments_completed/`**
- Grid das alocações finalizadas (status `PENDING_HANDOVER`, `SUPSIG`)
- Regra de negócio: filtro por skill do supervisor (`planned_skill_id = [usr_skill_id]`) e equipe (`supervisor_id` nulo ou igual)
- JOINs com `mro_projects`, `mro_tasks` e `mro_employees`
- Regra de negócio: o **nome do mecânico** vem de `mro_employees.full_name` via `executed_by_employee_id` (campo `employee_id` é NULL no banco)
- Botão de barra de ação "Ver Histórico" abre a `blank_mro_timeline` (configurado no IDE)

---

## grid_public_mro_tasks_approval

### Aprovação de Tarefas (SUPSIG → COMPLETED/PENDING_INSP1)

**`tasks/grid_public_mro_tasks_approval/`**
- Grid das tasks em **SUPSIG** (aguardando aprovação do supervisor)
- Regra de negócio: filtra por skill do supervisor (`EXISTS` no assignment com `planned_skill_id = [usr_skill_id]`)
- Botão AJAX `btn_aprovar` (2 estados: "Aprovar" / "Aprovado"):
  - Regra de negócio: task **sem RII** (`requires_rii=false` E `is_rii=false`) → `COMPLETED`
  - Regra de negócio: task **com RII** (`requires_rii=true` OU `is_rii=true`) → `PENDING_INSP1` (fila de inspeção)
- Idempotente: `UPDATE ... WHERE status_code = 'SUPSIG'` (evita re-aprovação)
- Auditoria: grava `APPROVED`/`ROUTED_TO_INSP1` no `mro_task_history` + `sc_log_add`

---

## grid_public_mro_tasks_insp

### Inspeção RII (PENDING_INSP1 → PENDING_INSP2/COMPLETED)

**`tasks/grid_public_mro_tasks_insp/`**
- Grid das tasks na fila de inspeção (`PENDING_INSP1`, `PENDING_INSP2`)
- Regra de negócio: visível **somente para inspetores** (`[usr_is_inspector]` no `onScriptInit` com `sc_alert` + exit)
- Regra de negócio: filtra por skill do inspetor (`EXISTS` no assignment)
- Botão AJAX `btn_assinar` (2 estados: "Assinar" / "Assinado"):
  - Regra de negócio (segregação ANAC): **somente inspetor** pode assinar; **quem executou a task não pode inspecionar** o próprio trabalho
  - Regra de negócio: `PENDING_INSP1` com `is_rii=true` → `PENDING_INSP2` (duplo check); `is_rii=false` → `COMPLETED`
  - Regra de negócio: `PENDING_INSP2` → `COMPLETED`, validando que **Inspetor 2 ≠ Inspetor 1**
- Auditoria: grava `INSPECTOR_1`/`INSPECTOR_2` no `mro_task_history` + `sc_log_add`
- Exibe quem já assinou (`inspector_1_login`/`inspector_2_login` via JOIN no histórico; nome resolvido por lookup no IDE)

---

## grid_public_mro_task_registro

### Painel de Registros (Auditoria ANAC)

**`tasks/grid_public_mro_task_registro/`**
- Grid do Time de Registros — acesso exclusivo do **perfil REGISTRO** (grupo 13)
- Regra de negócio: exibe **TODAS** as tasks `COMPLETED` (auditoria geral do encerramento — não só as que passaram pelo RII)
- Regra de negócio: painel **separado do menu do supervisor** — item "Auditoria > Painel de Registros" no `sec_menu`
- Exibe os inspetores que assinaram (`inspector_1_login`/`inspector_2_login` via JOIN no histórico)
- Finalidade: conferência final das assinaturas e geração do Job Card eletrônico ("Zero Papel")

---

## grid_public_mro_task_history

### Consulta do Histórico de Tasks (implementada)

**`tasks/grid_public_mro_task_history/`**
- Grid de consulta das transições de status de uma task na tabela `mro_task_history`
- Regra de negócio: filtra por `[glo_task_id]` — exibe a trilha de auditoria de **uma task específica** (aberta via botão/ligação)
- Campos: `log_id`, `task_id`, `action_taken`, `user_login`, `action_date`, `remarks`, `batch_id`
- Ordenação: `action_date DESC` (mais recente primeiro)
- Permissões: Administrador + SUPERVISOR (herda de `tabs_supervisor`)

---

## 📋 Pendências desta parte

- [ ] Assinatura digital (tokens/hash) — decisão do gerente: **FUTURO** (hoje registra apenas `INSPECTOR_1`/`INSPECTOR_2`)
- [ ] Job Card eletrônico / "Zero Papel" (Etapa 4 — Time de Registros) — **FUTURO**
