# Guia de Testes — Task e NRC (MRO-117)

## Objetivo

Validar o fluxo completo de **Rotinas (Task)** e **Não-Rotinas (NRC)** na aplicação unificada `form_public_mro_tasks` / `grid_public_mro_tasks`, simulando todos os perfis de usuário e seus respectivos acessos.

---

## 1. Premissas

- Usuário de teste deve ter **apenas o perfil sendo testado** (ex: só MECANICO, só ENGENHARIA, etc.)
- As NRCs são criadas exclusivamente pela `ctrl_abertura_nrc` — o formulário `form_public_mro_tasks` não permite insert direto de NRC
- Tarefas do tipo Rotina são criadas normalmente pelo formulário (insert liberado)

### Regras de Negócio — Criação de NRCs

1. **Fluxo principal (Mecânico em campo):** O mecânico abre NRCs exclusivamente pelo **clock-in/clock-out** — ou seja, enquanto está executando uma tarefa (assignment ativa) e encontra alguma discrepância/disconformidade. Ele usa o botão no `form_public_mro_task_assignments` para disparar o `ctrl_abertura_nrc`, que detecta automaticamente a assignment em andamento.

2. **Fluxo secundário (Qualquer perfil):** Qualquer usuário (incluindo o próprio mecânico) também pode abrir uma **NRC em cima de outra NRC** — ou seja, o `parent_task_id` pode apontar tanto para uma Rotina Padrão quanto para uma Não-Rotina existente. Isso é feito pela rota de backoffice na `grid_public_mro_tasks`, selecionando a task de origem manualmente.

**Resumo:** Mecânico cria NRC via assignment ativa (campo). Qualquer perfil pode criar NRC a partir de outra NRC (backoffice).

---

## 2. Perfis de Usuário e Fluxo Envolvido

| Perfil | Fluxo NRC | Fluxo Rotina |
|--------|:---------:|:------------:|
| MECANICO | Fase 1 (DRAFT) | Visualização |
| ENGENHARIA | Fase 2 (PENDING_ENG) | Visualização |
| COORDENADOR | Fase 3 (PENDING_COORD) | Visualização |
| PROGRAMACAO | Fase 4 (PENDING_PROG) | Visualização |
| CLIENTE | Fase 5 (PENDING_OA) | Visualização |
| COMERCIAL | Fase 5/6 (PENDING_OA / COMMERCIAL_REVIEW) | Visualização |
| ADMINISTRADOR | Todas (acesso total) | Inserir/Editar |
| SUPERVISOR | Visualização | Visualização |
| PLANEJAMENTO | Visualização | Visualização |
| COMPRAS | Visualização | Visualização |
| FERRAMENTARIA | Visualização | Visualização |

---

## 3. Roteiro de Testes

### 3.1. Testes de Grid (`grid_public_mro_tasks`)

#### 3.1.1. Badge de Status

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 1 | Abrir grid com tarefas em diversos status | Cada linha exibe o badge com cor e ícone correspondente ao `status_code` |
| 2 | Conferir badge de tarefa DRAFT | Ícone `fa-pen`, cor cinza |
| 3 | Conferir badge de tarefa PENDING_ENG | Ícone `fa-hard-hat`, cor laranja |
| 4 | Conferir badge de tarefa PENDING_PROG | Ícone `fa-user-gear`, cor azul |
| 5 | Conferir badge de tarefa RELEASED | Ícone `fa-check`, cor verde |
| 6 | Conferir badge de tarefa CANCELLED | Ícone `fa-xmark`, cor vermelho escuro |
| 7 | Se existir tarefa com status_code não cadastrado na `mro_sys_status` | Badge exibe o código do status em cinza (fallback) |

#### 3.1.2. Ícones Visuais

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 8 | Identificar tarefa NRC na grid | Ícone `fa-triangle-exclamation` laranja (`{nr}`) |
| 9 | Identificar tarefa Rotina na grid | Ícone `fa-list-check` azul (`{nr}`) |
| 10 | Clicar no ícone de bloqueio de material (`btn_mat`) | Alterna bloqueio entre vermelho e cinza |
| 11 | Clicar no ícone de bloqueio de ferramenta (`btn_fer`) | Alterna bloqueio entre roxo e cinza |
| 12 | Clicar no ícone de bloqueio de mão de obra (`btn_mao`) | Alterna bloqueio entre laranja e cinza |

#### 3.1.3. Filtro MECANICO

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 13 | Logar como MECANICO (apenas esse perfil) e abrir grid | Exibe **apenas** tarefas onde `created_by = [usr_login]` |
| 14 | Logar como SUPERVISOR e abrir grid | Exibe **todas** as tarefas (sem filtro) |

---

### 3.2. Testes de Formulário (`form_public_mro_tasks`)

#### 3.2.1. Rotina Padrão (is_nrc = false)

> **Nota:** Os campos `parent_task_id`, `origin_document`, `deferment_status`, `deferment_reason` e o bloco `bloco_financeiro` são **exclusivos de Não-Rotinas (NRC)**. Em Rotinas comuns eles ficam ocultos porque não fazem sentido no contexto — `parent_task_id` e `origin_document` só existem quando a tarefa foi originada de outra (NRC), deferment é um recurso de fluxo de NRC, e bloco_financeiro é específico de O&A (Over & Above) que só ocorre em NRC.

| # | Responsável | Ação | Resultado Esperado |
|---|-------------|------|-------------------|
| 15 | PLANEJAMENTO / ADMINISTRADOR | Abrir tarefa do tipo Rotina para edição | `{routine_type}` exibe "Rotina Padrão" |
| 16 | PLANEJAMENTO / ADMINISTRADOR | Verificar campos ocultos | `parent_task_id`, `origin_document`, `deferment_status`, `deferment_reason` — **não aparecem** |
| 17 | PLANEJAMENTO / ADMINISTRADOR | Verificar bloco financeiro | `bloco_financeiro` — **oculto** |
| 18 | PLANEJAMENTO / ADMINISTRADOR | Verificar botões de ação NRC | Nenhum botão de workflow NRC visível (btn_enviar_eng, btn_aprovar_cliente, etc.) |
| 19 | PLANEJAMENTO / ADMINISTRADOR | Verificar botões padrão | `new`, `update`, `delete`, `copy`, `print` visíveis conforme permissão |
| 20 | PLANEJAMENTO / ADMINISTRADOR | Inserir nova Rotina | Formulário abre em modo insert normalmente |

#### 3.2.2. Não-Rotina (is_nrc = true)

##### Preparação

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 21 | Criar NRC via `ctrl_abertura_nrc` | NRC criada com status DRAFT |
| 22 | Abrir NRC recem-criada no `form_public_mro_tasks` | `{routine_type}` exibe "Não Rotina (RN)" |

##### Visual Geral

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 23 | Verificar campos visíveis | `parent_task_id`, `origin_document` — **visíveis** |
| 24 | Verificar campos read-only | `project_id`, `task_code`, `parent_task_id` — **não editáveis** |
| 25 | Verificar campos obrigatórios | `parent_task_id`, `instruction_text`, `task_name` — validados ao salvar |
| 26 | Verificar bloco financeiro | `bloco_financeiro` — **oculto** (só ativado para não-mecânicos nos status `PENDING_OA`, `COMMERCIAL_REVIEW` ou `RELEASED`). Em DRAFT permanece OFF independente do grupo do usuário |
| 27 | Verificar campos de deferment | `deferment_status` visível; ao marcar, `deferment_reason` aparece |
| 28 | Verificar botão Novo | `new` — **oculto** |
| 29 | Verificar botão Excluir | `delete` — **oculto** (exceto para o criador da NRC em DRAFT) |
| 30 | Verificar botão Atualizar | `update` — **oculto** (exceto perfis autorizados por status) |

##### Fluxo NRC - Fase 1: DRAFT (criador da NRC)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 31 | Logar como qualquer perfil (MECANICO, PLANEJAMENTO, etc.), criar NRC via `ctrl_abertura_nrc`. Após o redirect automático para o form, a NRC recém-criada abre com `created_by = [usr_login]` | Botões visíveis: `btn_enviar_eng`, `btn_enviar_prog`, `delete`, `update`. Regra: DRAFT somente quem criou pode alterar |
| 32 | Clicar em btn_enviar_eng | Status muda para `PENDING_ENG` — **Fase 2 (Engenharia)**: `{instruction_text}` fica read-only. Apenas ENGENHARIA vê botões: `btn_enviar_prog`, `btn_enviar_coord`, `btn_cancelar`, `update`. Redirect para o form |
| 33 | Clicar em btn_enviar_prog (em outra NRC) | Status muda para `PENDING_PROG` — **Fase 4 (Programação)**: `{instruction_text}` fica read-only. Apenas PROGRAMACAO vê botões: `btn_enviar_coord`, `btn_validar_prog`, `btn_cancelar`, `update`. Pula a etapa de Engenharia |
| 34 | Clicar em delete | NRC é excluída |
| 35 | Editar campos (update) | Formulário permite edição |

##### Fluxo NRC - Fase 2: PENDING_ENG (ENGENHARIA)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 36 | Logar como ENGENHARIA, abrir NRC em PENDING_ENG | Botões visíveis: `btn_enviar_prog`, `btn_enviar_coord`, `btn_cancelar`, `update` |
| 37 | Verificar campo `{instruction_text}` | Read-only (ninguém altera o relato original) |
| 38 | Clicar em btn_enviar_prog | Status muda para `PENDING_PROG` |
| 39 | Clicar em btn_enviar_coord | Status muda para `PENDING_COORD` |
| 40 | Clicar em btn_cancelar | Status muda para `CANCELLED` |

##### Fluxo NRC - Fase 3: PENDING_COORD (COORDENADOR)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 41 | Logar como COORDENADOR, abrir NRC em PENDING_COORD | Botões visíveis: `btn_enviar_eng`, `btn_enviar_prog`, `btn_cancelar`, `update` |
| 42 | Clicar em btn_enviar_prog | Status muda para `PENDING_PROG` |

##### Fluxo NRC - Fase 4: PENDING_PROG (PROGRAMACAO)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 43 | Logar como PROGRAMACAO, abrir NRC em PENDING_PROG | Botões visíveis: `btn_enviar_coord`, `btn_validar_prog`, `btn_cancelar`, `update` |
| 44 | Verificar campo `{instruction_text}` | Read-only |
| 45 | Clicar em btn_validar_prog | Dispara lógica de O&A, status vai para `PENDING_OA` |
| 46 | Clicar em btn_enviar_coord | Status vai para `PENDING_COORD` |

##### Fluxo NRC - Fase 5: PENDING_OA (CLIENTE / COMERCIAL)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 47 | Logar como CLIENTE, abrir NRC em PENDING_OA | Botões visíveis: `btn_aprovar_cliente`, `btn_reprovar_cliente` |
| 48 | Clicar em btn_aprovar_cliente | Status vai para `RELEASED` |
| 49 | Clicar em btn_reprovar_cliente | Status vai para `COMMERCIAL_REVIEW` |
| 50 | Logar como COMERCIAL, abrir NRC em PENDING_OA | Botões visíveis: `btn_aprovar_cliente`, `btn_reprovar_cliente` |
| 51 | Verificar bloco_financeiro | **Visível** (COMERCIAL não é MECANICO) |

##### Fluxo NRC - Fase 6: COMMERCIAL_REVIEW (COMERCIAL)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 52 | Logar como COMERCIAL, abrir NRC em COMMERCIAL_REVIEW | Botões visíveis: `btn_enviar_prog`, `btn_enviar_cliente`, `btn_cancelar`, `update` |
| 53 | Clicar em btn_enviar_cliente | Status volta para `PENDING_OA` (reapresenta ao cliente) |
| 54 | Clicar em btn_enviar_prog | Status vai para `PENDING_PROG` (negociação manual) |
| 55 | Verificar bloco_financeiro | **Visível** |

##### Fluxo NRC - Fase 7: RELEASED

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 56 | Logar como ADMINISTRADOR, abrir NRC em RELEASED | Nenhum botão de ação NRC visível |
| 57 | Verificar bloco_financeiro para não-MECANICO | **Visível** |

---

### 3.3. Validações Específicas

#### 3.3.1. skill_code vazio

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 58 | Salvar NRC ou Rotina com `skill_code` em branco | Campo é convertido para `NULL` no banco (sem erro de FK) |

#### 3.3.2. Campos Obrigatórios (NRC)

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 59 | Tentar salvar NRC com `parent_task_id` vazio | Erro: "O campo Task Origem (Parent) é obrigatório para Não-Rotinas." |
| 60 | Tentar salvar NRC com `instruction_text` vazio | Erro: "O campo Instrução é obrigatório para Não-Rotinas." |
| 61 | Tentar salvar NRC com `task_name` vazio | Erro: "O campo Nome da Tarefa é obrigatório para Não-Rotinas." |

#### 3.3.3. deferment

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 62 | Abrir NRC, marcar `deferment_status` | Campo `deferment_reason` aparece |
| 63 | Desmarcar `deferment_status` | Campo `deferment_reason` some e valor é limpo |
| 64 | Abrir Rotina | `deferment_status` e `deferment_reason` não aparecem |

---

### 3.4. Testes de Segurança e ACL

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 65 | Logar como MECANICO, acessar `grid_public_mro_tasks` | Grid abre (permissão concedida na migration) |
| 66 | Logar como MECANICO, tentar editar NRC criada por outro usuário | Grid filtrada, não vê tarefas de outros. Mesmo se acessar direto, não tem botões de ação (só o criador mexe em DRAFT) |
| 67 | Logar como perfil sem acesso (ex: um grupo novo sem permissão) | Grid bloqueada |

---

### 3.5. Regressão — Rotinas

| # | Ação | Resultado Esperado |
|---|------|-------------------|
| 68 | Criar nova Rotina (is_nrc = false) | Insert funciona, `{routine_type}` = "Rotina Padrão" |
| 69 | Editar Rotina existente | Update funciona, campos NRC não aparecem |
| 70 | Verificar status de Rotina | Badge na grid exibe o status normalmente |
| 71 | Verificar ícones de bloqueio em Rotina | Funcionam normalmente (btn_mat, btn_fer, btn_mao) |

---

## 4. Fluxo Resumido (Mapa Mental)

> Diagrama do fluxo de aprovação NRC movido para [`_DOCS/producao_manutencao/fluxo_nrc_workflow.md`](../_DOCS/producao_manutencao/fluxo_nrc_workflow.md)

---

## 5. Cenários de Erro (Testes Negativos)

| # | Cenário | Resultado Esperado |
|---|---------|-------------------|
| 72 | Abrir `form_public_mro_tasks?task_id=0` (registro inexistente) | Form exibe tela de registro não encontrado |
| 73 | Tentar inserir NRC diretamente via URL `.../form_public_mro_tasks?insert=1` | Form abre em modo insert normalmente (Rotina) — NRC não pode ser inserida diretamente |
| 74 | Acessar form_public_mro_nrc antigo | Aplicação deve estar desativada/redirecionando |
| 75 | Simular erro de banco no lookup de status | Badge exibe fallback (cinza com status_code) |

---

## 6. Checklist de Regras de Negócio

- [ ] `project_id` é read-only para NRC
- [ ] `task_code` é read-only para NRC
- [ ] `parent_task_id` é read-only para NRC
- [ ] `parent_task_id` é obrigatório para NRC
- [ ] `instruction_text` é obrigatório para NRC
- [ ] `task_name` é obrigatório para NRC
- [ ] `skill_code` vazio é convertido para NULL (não quebra FK)
- [ ] `parent_task_id` e `origin_document` só aparecem em NRC
- [ ] `deferment_status` e `deferment_reason` só aparecem em NRC
- [ ] `bloco_financeiro` só aparece para NRC em status >= PENDING_OA, para não-mecânicos
- [ ] MECANICO vê apenas próprias tarefas na grid
- [ ] DRAFT: somente quem criou pode alterar (botões visible para o `created_by`)
- [ ] Nenhum botão de workflow NRC aparece em Rotinas

---

> **Instruções de uso:** Copie este roteiro para sua ferramenta de gestão de testes (Excel, TestRail, etc.) e marque cada caso como ✅ APROVADO ou ❌ REPROVADO conforme o resultado obtido.


