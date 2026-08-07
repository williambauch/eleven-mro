# PLANO DE IMPLEMENTACAO - MRO-122

## Otimizar a abertura de NRCs, revisar grids com novos filtros e corrigir a geracao de PDFs

---

## 1. ANALISE DO ESTADO ATUAL

### 1.1 Fluxo de Abertura de NRC

- **`tasks/form_public_mro_task_assignments`**: Botao "Abrir Não Rotina" (link configurado no ScriptCase IDE) — rota do mecanico via assignment ativa (clock-in/clock-out).
- **`tasks/grid_public_mro_tasks`**: Rota de backoffice — abre NRC a partir de uma task de origem selecionada manualmente (task_id_origem).
- **`Timesheet/ctrl_abertura_nrc`**: Tela intermediaria de controle (Form sobre tabela ctrl_abertura_nrc) com campos: `titulo_nrc`, `descricao_detalhada`, `foto_defeito`, `impeditivo`, `referencia_passo`. E o ponto de entrada atual para criar NRC.
  - `events/onLoad`: define a NRC como Rascunho (DRAFT).
  - `events/onValidateSuccess`: roteia mecanico vs backoffice, gera codigo hierarquico (N-/NN-/NNN-) herdando sequenciais do pai (regras MRO-125), insere NRC em `mro_tasks` (is_nrc=true, status DRAFT), aplica impeditivo (pausa timesheet, BLOCKED na assignment, is_blocked_labor) e redireciona para `form_public_mro_tasks` com a nova task_id.
- **Destino atual**: apos salvar, o usuario e levado para `tasks/form_public_mro_tasks` (form unificado task/NRC, apos MRO-117).

**Problema (critério 1):** o usuario passa por 2 telas (botao -> ctrl_abertura_nrc -> form de preenchimento). O objetivo e ir direto para o formulario de preenchimento, eliminando o clique/tela intermediaria e a digitacao de dados.

**Contexto MRO-117:** "por enquanto nao vamos alterar ctrl_abertura_nrc. Ela permanece como esta." — a MRO-122 substitui a chamada por uma nova app Blank, mantendo o ctrl_abertura_nrc intacto para consulta/historico.

### 1.2 Grids (revisao de novos filtros)

- **`tasks/grid_public_mro_tasks`**: grid principal de backoffice; possui eventos onRecord com icones (material, ferramenta, mao de obra, NRC, predecessor) e events_ajax (btn_fer, btn_mao, btn_mat, btn_predecessor).
- **`tasks/grid_public_mro_nrc`**: grid especifica de NRCs (legada apos MRO-117, permanece no menu).
- **`tasks/grid_my_tasks`**: grid do mecanico.
- **Necessidade:** novos filtros a definir — **PENDENTE** detalhes com o lider.

### 1.3 Geracao de PDFs

- Apps de PDF mapeadas em `reports/`: `pdf_pack`, `pdf_pack_jic`, `blank_pdf_pack_jic`, `pdf_jic`, `pdf_jic_print`, `pdf_jobcard`, `pdf_pack_universal`, `pdf_jec`, `jobcard`, `blank_report_test`.
- Contexto recente: MRO-118/MRO-125 ajustaram `pdf_pack_jic`/`blank_pdf_pack_jic` (barcode e codigo hierarquico).
- **Necessidade:** correcao a definir — **PENDENTE** qual PDF, qual erro e cenario de reproducao com o lider.

---

## 2. DECISOES DE DESIGN

### 2.1 Abertura Direta de NRC

- **Objetivo:** o botao "Abrir NRC" deve criar a NRC automaticamente, sem tela intermediaria e sem digitacao de dados, redirecionando direto para o formulario de preenchimento da NRC.
- **Decisao:** **NOVA APP BLANK** — criar aplicacao do tipo Blank (ex: `blank_abertura_nrc`) com codigo similar ao `onValidateSuccess` do `ctrl_abertura_nrc`, executado no evento `01_onExecute`.
  - O usuario **nao informa mais nenhum dado**: `titulo_nrc`, `descricao_detalhada`, `impeditivo`, `foto_defeito` e `referencia_passo` sao eliminados da chamada.
  - **Toda criacao de NR gera as acoes de `impeditivo = S`** (pausa timesheet ativo, BLOCKED na assignment, is_blocked_labor = true, evento BLOCKED).
  - `task_name` = `"Não Rotina de {task_name do pai}"` (ex: rotina mae "PERFORM A PHYSICAL CHECK" -> "Não Rotina de PERFORM A PHYSICAL CHECK").
  - `instruction_text` = **vazio** (NULL).
  - O SELECT do pai passa a buscar tambem o `task_name` (alem de `task_code` e `project_id`).
- **Parametros de entrada (via URL/global, vindos dos botoes atuais):**
  - Rota mecanico: `glo_assignment_id`, `glo_timesheet_id`, `usr_employee_id` (assignment ativa detectada automaticamente).
  - Rota backoffice: `task_id_origem` (task/NRC de origem selecionada).
  - `glo_origem` = 'backoffice' para rota administrativa.
- **Logica mantida do ctrl (MRO-125):** deteccao assignment ativa, geracao do codigo hierarquico N-/NN-/NNN- (herda sequenciais do pai), INSERT em mro_tasks (is_nrc=true, status DRAFT, created_by=[usr_login]), bloqueio por impeditivo e redirect para `form_public_mro_tasks` com a nova task_id.
- **Onde vive a logica:** copiada do `onValidateSuccess` do ctrl_abertura_nrc para o `01_onExecute` da nova blank, com a diferenca de que o impeditivo passa a ser sempre 'S' e nao ha campos de entrada.
- **ctrl_abertura_nrc:** permanece intacta (MRO-117), apenas deixa de ser chamada pelos botoes.

### 2.2 Grids e Filtros

- **DEFINIDO** — lista de filtros registrada em `filtros_grid_public_mro_tasks.md` e no sumario do `MRO-122.md` (Filtro Refinado, Avancado em 3 grupos: Identificacao e Status, Criticidade, Origem).

### 2.3 PDFs

- **PENDENTE** — aguardando detalhe do erro/cenario.

---

## 3. RISCOS E DEPENDENCIAS

| Risco | Impacto | Mitigacao |
|-------|---------|-----------|
| Logica de codigo hierarquico (MRO-125) quebrar ao mover para a nova blank | Alto | Copiar o codigo do onValidateSuccess do ctrl_abertura_nrc sem alterar a regra; testes com NRC em nivel 1, 2 e 3 |
| Logica de impeditivo (pausa timesheet, BLOCKED, is_blocked_labor) se perder | Alto | Impeditivo passa a ser sempre 'S' — validar pausa do timesheet ativo e BLOCKED da assignment |
| NRC criada sem titulo/descricao (sem input do usuario) | Medio | Definir valores padrao claros para task_name e instruction_text |
| ctrl_abertura_nrc era "nao alterar" (MRO-117) | Medio | A app permanece intacta; apenas os botoes passam a chamar a nova blank |
| Permissoes (ACL): MECANICO vs backoffice acessando insert de NRC | Medio | Manter grupos de acesso; validar rota mecanico nao quebra |
| Parametros dos botoes (link configurado no ScriptCase IDE) | Medio | Reaproveitar os mesmos parametros ja usados na chamada atual do ctrl_abertura_nrc |
| Escopo de PDFs indefinido | Medio | Confirmar com lider quais apps e erros antes de editar |

---

## 4. ORDEM DE EXECUCAO RECOMENDADA

| Ordem | Atividade | App/Tabela |
|:-----:|-----------|------------|
| 0 | ~~Definir valores padrao~~ **DEFINIDO**: task_name = "Não Rotina de {task_name pai}"; instruction_text = vazio | - |
| 1 | Criar nova app Blank `blank_abertura_nrc` com logica do onExecute (codigo MRO-125 + impeditivo S) | `blank_abertura_nrc` (nova), `ctrl_abertura_nrc` (fonte da logica) |
| 2 | Apontar botoes "Abrir Não Rotina" (form_public_mro_task_assignments) e rota backoffice (grid_public_mro_tasks) para a nova blank | `form_public_mro_task_assignments`, `grid_public_mro_tasks` |
| 3 | Revisao de grids com novos filtros (DEFINIDO) | `grid_public_mro_tasks` |
| 4 | Correcao da geracao de PDFs | `reports/*` (a definir) |

---

## 5. MIGRACOES PREVISTAS

Nenhuma migration prevista ate o momento — alteracoes parecem ser de aplicacao (fluxo/UX), sem mudanca de schema. Reavaliar apos levantamento.
