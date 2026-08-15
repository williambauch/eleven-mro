# Revisão Chão de Fábrica & Backoffice — Parte 02 — Módulo 02 (Operações e Chão de Fábrica)

> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice, separado por módulos de trabalho.
>
> - [Módulo 01 — Backoffice (Gantt e Kanban)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2001%20-%20Backoffice.md)
> - **Módulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)** (este arquivo)
> - [Módulo 03 — Logística & Ferramentaria (Almoxarifado)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2003%20-%20Logistica%20Ferramentaria.md)

---

## Tarefa do Modulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)

Auto-Pausa de Timesheet na Abertura de NRC:

Regra: No momento em que o mecânico clicar em "Abrir Não Rotina (NRC)" no tablet para reportar uma discrepância e esta for marcada como "Impeditiva", o sistema deve pausar imediatamente e de forma automática o timesheet/cronômetro de horas ativo da tarefa-mãe de rotina, evitando contaminação de HH

Validação de Concorrência de Timesheet (Múltiplos Mecânicos):

Regra: O sistema permite que múltiplos mecânicos estejam alocados simultaneamente na mesma JIC/tarefa [cite: 103].

Trava: A tarefa só poderá mudar seu status para concluída (COMPLETED) e seguir para a fila do Supervisor se o sistema verificar que não existe nenhum outro mecânico com timesheet ativo (clock-in rodando) na mesma JIC. O último a fazer o clock-out decide se encerra ou repassa o serviço.

Tratamento de Mensagem de Erro por Duplicidade:

Regra: Se o mecânico tentar iniciar um clock-in concorrente ilegal, o sistema deve exibir uma mensagem clara contendo os metadados específicos para auditoria: "Erro: Apontamento duplicado para o Projeto [Nome do Projeto] - JIC [Código da JIC] - Mecânico [Nome do Mecânico]" .

Tipo de Encerramento "Timesheet Finalizado":

Regra: Alterar o nome do clockout de Tarefa 100% Concluída para Timesheet Finalizado/Tarefa Concluída.

Permissão Ampla de Edição de NRC em status DRAFT:

Regra: Desvincular a trava por proprietário do rascunho. Qualquer mecânico habilitado no hangar pode abrir, complementar dados ou editar uma Não-Rotina que esteja no status de DRAFT. O sistema deve registrar de forma imutável o log de quem efetuou cada alteração.

Painel do Supervisor com Restrição de Skill:

Regra: Implementar regra de Row-Level Security (RLS) para que o Supervisor logado no sistema visualize, filtre e gerencie as alocações apenas dos mecânicos que correspondam à sua especialidade técnica (Skill) de atuação. 

Habilitar a seleção de mais de uma skill no cadastro do colaborador.

---

## Sumario das alteracoes implementadas - Tarefa do Modulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)

## Auto-Pausa de Timesheet na Abertura de NRC — JA IMPLEMENTADO (MRO-122)

**Regra:** No momento em que o mecânico clicar em "Abrir Não Rotina (NRC)" no tablet para reportar uma discrepância e esta for marcada como "Impeditiva", o sistema pausa imediatamente e de forma automática o timesheet/cronômetro de horas ativo da tarefa-mãe de rotina, evitando contaminação de HH.

---

## `blank_abertura_nrc`

### Criacao automatica de NR com impeditivo sempre ativo (fluxo ativo)

**`events/onExecute`**
- Regra de negocio: impeditivo **SEMPRE ativo** ao criar NR (MRO-122 removeu a tela intermediaria — usuario nao informa mais dados)
- Regra de negocio: pausa o timesheet ativo da rotina-mae, bloqueia assignment (`BLOCKED`), marca `is_blocked_labor=true` e registra evento `BLOCKED`
- Cria a NR em DRAFT, gera codigo hierarquico N-/NN-/NNN- (MRO-125) e tem guarda anti-reexecucao (`glo_ultima_nrc`)

---

## `ctrl_abertura_nrc`

### Tela intermediaria descontinuada

**`events/onValidateSuccess`**
- Deixou de ser chamada pelos botoes (fluxo aponta para a `blank_abertura_nrc`); mantida apenas para consulta/historico

---

## `control_pause_task`

### Concorrencia de timesheet — trava de COMPLETED (ultimo clock-out)

**`events/onValidateSuccess`**
- Regra de negocio: task so vai para `SUPSIG` quando o **ultimo mecanico ativo** faz clock-out (nao existe outro assignment ativo na mesma task)
- Regra de negocio: Fim de Turno com outros ativos encerra como `PENDING_HANDOVER` sem alterar a task; se e o ultimo, task vai para `PENDING_PROG`
- Regra de negocio: ultimo conclui -> task `SUPSIG` + `actual_end` (sem sobrescrever datas manuais) + log em `mro_task_history`

### Renomeacao do clockout de conclusao

**`events/onValidateSuccess`**
- Regra de negocio: texto de encerramento do motivo 6 alterado para **"Timesheet Finalizado/Tarefa Concluída"** (auditoria + historico + label na IDE) — validado no banco 15/08/2026

---

## `form_public_mro_task_assignments`

### Mensagem de duplicidade no clock-in (Apontamento Duplicado)

**`events/onLoad`**
- Regra de negocio: ao abrir o form com outro timesheet `IN_PROGRESS` em assignment diferente, oculta/desabilita o Play e exibe card **"APONTAMENTO DUPLICADO"** com Projeto/JIC/Mecânico/Assignment Id + link "Ir para a tarefa em andamento"

**`button/Play`**
- Regra de negocio: trava de seguranca — se existe outra task em andamento, redireciona direto para o form da tarefa aberta (sem mensagem, o card do onLoad ja informa)
- Eliminado o redirect para `control_pause_task` via link (causava loop de re-submissao gerando timesheets duplicados)

### Bloqueio de Clock-In em status finalizados

**`events/onLoad`**
- Regra de negocio: atribuicao finalizada (`SUPSIG`, `COMPLETED`, `CANCELLED`, `PENDING_HANDOVER`, inspecoes) nao aceita Clock-In — Play/Pause ocultos e card "ATIVIDADE ENCERRADA"

### Otimizacao de load do cronometro (performance)

**`00_JAVASCRIPT_FORM_LOAD/sc_form_onload.js`**
- Disparo do cronometro movido para o `sc_form_onload` (Javascript Geral Form > Onload), removendo o gatilho por imagem inexistente que travava o load
- Regra de negocio: campo `task_id` convertido de SELECT (18.766 opcoes) para **LOOKUP/autocomplete** — resolveu travamento de ~40s no load; validado 15/08/2026

---

## `grid_mro_task_log`

### Cronologia de edicoes da task (log do ScriptCase)

**`sql/schema.sql`**
- Regra de negocio: grid que consolida o log nativo do ScriptCase (`sc_log`) das apps da task — **Tarefa, Skills/HH, Ferramentas, Materiais, Anexos** — em uma cronologia unica por task
- Regra de negocio: filtro por `[glo_task_id]`; extrai o task_id do `description` do log via regex (`substring`), com JOIN nas tabelas de detalhe (`mro_task_resources`, `mro_task_tools`, `mro_task_materials`, `mro_attachments`) para updates/deletes que so trazem a chave do detalhe
- Regra de negocio: campo `application_name` com nome amigavel (Task, Skills, Tools, Materials, Attachments); campos `username_login`/`action_name` renomeados para evitar conflito com palavras reservadas do ScriptCase
- Regra de negocio: mostra quem (login), quando (data), onde (app), acao (insert/update/delete/access) e o que mudou (campos old/new do `description`)

**`events/onRecord`**
- Regra de negocio: formata o `description` em coluna virtual `description_formated` — quebra `||` em linhas, marca `(old)` vermelho / `(new)` verde e os trechos keys/fields legiveis (sem usar colchetes, que o ScriptCase trata como global)

---

## `form_public_mro_tasks`

### Botao Historico de Edicoes

**`button/btn_task_log`**
- Regra de negocio: botao de ligacao na barra de ferramentas do form da task — abre a `grid_mro_task_log` passando `glo_task_id = {task_id}`
- Permite ao mecanico/planejador consultar o historico completo de edicoes da tarefa (DRAFT e demais fases) com auditoria de quem alterou

### Permissao ampla de edicao de NRC em DRAFT

**`events/onLoad`**
- Regra de negocio: **desvinculada a trava por proprietario do rascunho** — qualquer mecanico habilitado pode abrir, complementar dados e editar uma NRC em `DRAFT` (update e botoes de envio liberados para todos)
- Regra de negocio: o **delete** permanece restrito ao criador (operacao destrutiva)
- Regra de negocio: cada edicao e registrada pelo log nativo do ScriptCase (`sc_log`) e consultavel na `grid_mro_task_log` — atende o criterio de auditoria de quem alterou

---

## `mro_employees`

### Multiplas skills por colaborador (migration)

**`migrations/MRO-126_employee_skills_multiplas.sql`**
- Regra de negocio: `skill_id` alterado de `integer` para `varchar(100)` — guarda uma **lista de ids** (ex: `"1,5,14"`) gravada pelo Select Multiplo do ScriptCase
- FK `mro_employees_skill_id_fkey` removida de proposito (lista nao pode ser FK de `mro_skills`)

---

## `sec_Login`

### Login carrega a lista de skills

**`events/onValidate`**
- Regra de negocio: `[usr_skill_id]` passa a carregar a **lista** de skills do colaborador (ex: `"1,5,14"`), nao mais um id unico — usado nos filtros `IN` dos grids

---

## `form_public_mro_task_assignments_planned`

### Grid filtrada pelas skills do supervisor

**`sql/schema.sql`**
- Regra de negocio: filtro `planned_skill_id = [usr_skill_id]` → **`IN ([usr_skill_id])`** — supervisor so ve alocacoes de todas as skills dele

### Lookup de mecanicos filtrado pela skill do assignment

**`events/onRecord`**
- Regra de negocio: fallback `planned_skill_id = 0` quando o assignment nao tiver skill; com 0 o select de mecanicos fica **vazio** (forca definir a skill antes de atribuir)
- Regra de negocio: lookup de `executed_by_employee_id` lista apenas mecanicos com a skill do assignment (`',' || skill_id || ',' LIKE '%,' || {planned_skill_id} || ',%'`)
- **Validado (15/08/2026)**: assignment 15732 (B4) atribuida ao William (skills `11,14,9`) — evento `ASSIGNMENT` + status `ASSIGNED`

---

## `grid_public_mro_task_assignments_progress`, `grid_public_mro_task_assignments_blocked`, `grid_public_mro_task_assignments_completed`

### Grids do supervisor com skill_code

**`sql/schema.sql`**
- Regra de negocio: filtro `planned_skill_id = [usr_skill_id]` → **`IN ([usr_skill_id])`** (mesmo padrao das demais grids do supervisor)
- Campo `skill_code` adicionado via JOIN `mro_skills` para exibir o codigo da skill

---

## `grid_mro_dispatch`, `grid_public_mro_tasks_approval`, `grid_public_mro_tasks_insp`

### Filtro de skill nas demais telas do supervisor/inspetor

**`sql/schema.sql`**
- Regra de negocio: `planned_skill_id = [usr_skill_id]` → **`IN ([usr_skill_id])`** — inspetor e dispatch seguem a mesma regra de multi-skill do supervisor

---

## Pendências do Modulo 02
- Nenhuma pendente — modulo concluido.

---

## Critérios de Aceite (UAT) do Modulo 02
[ ] O timesheet da tarefa-mãe pausa automaticamente no exato instante em que uma NRC impeditiva é criada a partir dela

[ ] A JIC só realiza a transição de status para concluída se todos os mecânicos associados a ela tiverem realizado o clock-out

[x] O Supervisor logado só visualiza a lista de mecânicos que possuem o mesmo skill técnico que o dele — ATENDIDO (filtro `IN` por skills do supervisor + lookup de mecanicos pela skill do assignment)

[ ] Qualquer mecânico consegue alterar o conteúdo de uma NRC se ela estiver em status de DRAFT, gerando log na tabela mro_task_events 
