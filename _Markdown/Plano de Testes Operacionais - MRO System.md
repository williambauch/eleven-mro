# Plano de Testes Operacionais — MRO System

> **Meta:** Logar com cada perfil e executar o fluxo completo de ponta a ponta, validando que as transições de estado, regras de negócio e integrações funcionam conforme o esperado.

---

## Roteiro de testes (ordem cronológica)

Os testes seguem a ordem real do fluxo de manutenção: **Setup → Planejamento → Execução → Encerramento**.

---

## Fase 1 — Setup Inicial

### 1.1 Administrador (`william` / `Eleven@2026`)

Responsável por configurar o ambiente, cadastrar aeronaves, projetos e criar os usuários do sistema.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 1.1.1 | Acessar o sistema | Login | Logar com `william`, verificar se entrou sem erros | ☐ |
| 1.1.2 | Verificar permissões | Qualquer grid | Abrir `grid_public_mro_aircraft` — deve ver todos os registros | ☐ |
| 1.1.3 | Cadastrar aeronave | `form_public_mro_aircraft` | Criar uma aeronave com matrícula, modelo, MSN, cliente | ☐ |
| 1.1.4 | Cadastrar projeto | `form_public_mro_projects` | Criar um projeto/check vinculado à aeronave (ex: "1C Check") | ☐ |
| 1.1.5 | Gerenciar grupos de acesso | `sec_grid_sec_groups` | Verificar se todos os 12 grupos existem | ☐ |
| 1.1.6 | Verificar usuários existentes | `sec_grid_sec_users` | Confirmar que `william` está com `priv_admin = Y` | ☐ |
| 1.1.7 | Vincular usuário a grupo | `sec_grid_sec_users_groups` | Verificar quais grupos `william` pertence | ☐ |
| 1.1.8 | Verificar permissões de aplicações | `sec_form_sec_groups_apps` | Escolher um grupo e ver quais permissões estão habilitadas | ☐ |

---

## Fase 2 — Planejamento e Engenharia

### 2.1 Planejador (`planejador` / `Planejador@321`)

Responsável por estruturar a WBS e cadastrar tarefas.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 2.1.1 | Logar como planejador | Login | Logar com `planejador` | ☐ |
| 2.1.2 | Acessar WBS do projeto | `grid_public_mro_wbs` | Filtrar pelo projeto criado, verificar árvore de pacotes | ☐ |
| 2.1.3 | Criar pacote WBS | `form_public_mro_wbs` | Cadastrar um pacote (ex: "Desmontagem", "Inspeção") | ☐ |
| 2.1.4 | Cadastrar tarefa manual | `form_public_mro_tasks` | Criar tarefa dentro do pacote WBS com horas estimadas | ☐ |
| 2.1.5 | Visualizar tarefas | `grid_public_mro_tasks` | Confirmar que a tarefa aparece na grid com status PLANNED | ☐ |
| 2.1.6 | Alocar materiais | `form_public_mro_task_materials` | Vincular materiais à tarefa (se houver) | ☐ |
| 2.1.7 | Alocar mão de obra | `form_public_mro_task_resources` | Vincular recursos/skills à tarefa | ☐ |

### 2.2 Engenheiro (`engenheiro` / `Engenheiro@321`)

Responsável por análise técnica e liberação de tarefas.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 2.2.1 | Logar como engenheiro | Login | Logar com `engenheiro` | ☐ |
| 2.2.2 | Ver grid de tarefas | `grid_public_mro_tasks` | Visualizar tarefas do projeto | ☐ |
| 2.2.3 | Alternar bloqueios | `grid_public_mro_tasks` | Clicar nos ícones de bloqueio (material, ferramenta, mão de obra) — deve alternar cor cinza/colorida | ☐ |
| 2.2.4 | Liberar tarefa p/ execução | `grid_public_mro_tasks` | Clicar "Liberar para Execução" — status deve ir para RELEASED | ☐ |
| 2.2.5 | Verificar alocações criadas | Painel do Supervisor | As alocações automáticas devem aparecer em "A Distribuir" | ☐ |

### 2.3 Programador (`programador` / `Programador@321`)

Responsável pelo cronograma e ajustes finos.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 2.3.1 | Logar como programador | Login | Logar com `programador` | ☐ |
| 2.3.2 | Visualizar Gantt | `gantt` | Verificar se o cronograma das tarefas aparece no gráfico | ☐ |
| 2.3.3 | Ver Gantt por skill | `blank_gantt_por_skill` | Filtrar por especialidade | ☐ |
| 2.3.4 | Acompanhar progresso | `blank_gantt_tracking` | Ver tracking do cronograma | ☐ |

---

## Fase 3 — Início da Execução

### 3.1 Supervisor (`supervisor` / `Programador@321`)

Responsável por alocar os mecânicos às tarefas liberadas.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 3.1.1 | Logar como supervisor | Login | Logar com `supervisor` | ☐ |
| 3.1.2 | Acessar Painel do Supervisor | `tabs_supervisor` | Ver as 4 abas: A Distribuir, Em Andamento, Com Impedimentos, Concluídas | ☐ |
| 3.1.3 | Ver aba "A Distribuir" | Aba 1 | A tarefa liberada deve aparecer aqui | ☐ |
| 3.1.4 | Alocar mecânico | `grid_mro_dispatch` ou `form_planned` | Vincular a tarefa ao usuário `mecanico` | ☐ |
| 3.1.5 | Confirmar alocação | Aba "Em Andamento" | A tarefa deve migrar para "Em Andamento" após alocada | ☐ |
| 3.1.6 | Vincular ferramentas à tarefa | `form_mro_task_tools` | Adicionar ferramentas necessárias à tarefa | ☐ |
| 3.1.7 | Ver Kanban | `blank_kanban_board` | Ver tarefas no quadro visual | ☐ |

---

## Fase 4 — Execução no Chão de Fábrica

### 4.1 Mecânico (`mecanico` / `Mecanico@321`) — Tablet

Responsável por executar as tarefas no hangar.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 4.1.1 | Logar como mecânico | Login (tablet) | Logar com `mecanico` | ☐ |
| 4.1.2 | Ver Painel do Mecânico | `grid_my_tasks` | A tarefa alocada deve aparecer na lista | ☐ |
| 4.1.3 | Iniciar tarefa (clock-in) | `grid_my_tasks` | Clicar para iniciar — relógio deve começar a contar | ☐ |
| 4.1.4 | Verificar timesheet ativo | `grid_public_mro_timesheet` | Deve haver um registro com `end_time = NULL` | ☐ |
| 4.1.5 | Pausar tarefa | `grid_my_tasks` ou `control_pause_task` | Clicar pausar — registrar motivo (descanso, refeição) | ☐ |
| 4.1.6 | Retomar tarefa | `grid_my_tasks` | Clicar para retomar — relógio volta a contar | ☐ |

### 4.2 Ferramentaria (`ferramentaria` / `Ferramentaria@321`)

Responsável por liberar ferramentas via terminal de bipagem.

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 4.2.1 | Logar como ferramentaria | Login | Logar com `ferramentaria` | ☐ |
| 4.2.2 | Ver terminal de bipagem | `blank_mro_ferramentaria` | Abrir o terminal — interface de bipagem deve carregar | ☐ |
| 4.2.3 | Check-out (SAÍDA) | Terminal | Bipar tarefa → crachá do mecânico → ferramenta | ☐ |
| 4.2.4 | Validar trava de clock-in | Terminal | Se mecânico não tiver clock-in ativo, deve bloquear | ☐ |
| 4.2.5 | Validar trava de calibração | Terminal | Se ferramenta com calibração vencida, deve bloquear | ☐ |
| 4.2.6 | Confirmar empréstimo | `grid_public_mro_tool_movements` | A movimentação deve aparecer no histórico | ☐ |
| 4.2.7 | Check-in (RETORNO) | Terminal | Devolver ferramenta com condição OK | ☐ |
| 4.2.8 | Check-in com DANO | Terminal | Devolver marcando DANO — deve abrir relatório SGSO | ☐ |
| 4.2.9 | Check-in com PERDA | Terminal | Devolver marcando PERDA — deve marcar como extraviada | ☐ |
| 4.2.10 | Verificar incidente | `form_public_mro_tool_incidents` / `grid_public_mro_tool_incidents` | Incidente deve estar registrado | ☐ |
| 4.2.11 | Gestão de materiais | `grid_public_mro_materials` | Consultar materiais e peças | ☐ |

### 4.3 Mecânico — Continuação

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 4.3.1 | Reportar impedimento | `grid_my_tasks` | Reportar bloqueio (ex: ferramenta quebrada, falta peça) | ☐ |
| 4.3.2 | Verificar bloqueio no supervisor | `tabs_supervisor` (aba "Com Impedimentos") | Tarefa deve migrar para aba de impedimentos | ☐ |
| 4.3.3 | Supervisor resolver bloqueio | `tabs_supervisor` | Supervisor alterna bloqueio para resolvido | ☐ |
| 4.3.4 | Abrir Não-Rotina (NRC) | `ctrl_abertura_nrc` | Criar NRC a partir de uma discrepância encontrada, anexar foto | ☐ |
| 4.3.5 | Verificar NRC na grid | `grid_public_mro_nrc` | NRC deve aparecer com badge DRAFT (cinza) | ☐ |
| 4.3.6 | Finalizar tarefa | `grid_my_tasks` | Concluir serviço — clock-out final | ☐ |

---

## Fase 5 — Pós-Execução e Qualidade

### 5.1 Supervisor — Encerramento

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 5.1.1 | Ver tarefa concluída | Aba "Concluídas" | Tarefa deve estar na aba de concluídas | ☐ |
| 5.1.2 | Ver dispatch atualizado | `grid_mro_dispatch` | Mostrar horas realizadas vs planejadas | ☐ |
| 5.1.3 | Ver timeline do projeto | `blank_mro_timeline` | Timeline deve mostrar as tarefas executadas | ☐ |
| 5.1.4 | Ver Kanban atualizado | `blank_kanban_board` | Tarefa deve estar na coluna correta | ☐ |

### 5.2 Qualidade/Inspetoria — Assinatura Digital

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 5.2.1 | Logar como administrador | Login | Voltar a logar com `william` (qualidade não tem user próprio) | ☐ |
| 5.2.2 | Registrar assinatura digital | `form_public_mro_task_signoffs` | Selecionar tarefa concluída e assinar digitalmente | ☐ |
| 5.2.3 | Verificar assinatura na grid | `grid_public_mro_task_signoffs` | A assinatura deve aparecer no histórico | ☐ |
| 5.2.4 | Gerar PDF do Job Card | `jobcard` ou `pdf_jobcard` | Gerar PDF da tarefa concluída e assinada | ☐ |
| 5.2.5 | Gerar PDF do JIC | `pdf_jic` ou `pdf_jic_print` | Gerar PDF do JIC | ☐ |
| 5.2.6 | Gerar PDF do JEC | `pdf_jec` | Gerar PDF do JEC (se aplicável) | ☐ |
| 5.2.7 | Gerar Pack de PDFs | `pdf_pack` / `pdf_pack_universal` | Gerar pacote completo de documentos | ☐ |

---

## Fase 6 — Over & Above (O&A) e Aprovação

### 6.1 Engenharia — Análise de NRC

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 6.1.1 | Logar como engenheiro | Login | Logar com `engenheiro` | ☐ |
| 6.1.2 | Ver NRC na grid | `grid_public_mro_nrc` | A NRC aberta pelo mecânico deve aparecer com badge DRAFT | ☐ |
| 6.1.3 | Avançar NRC p/ PENDING_ENG | Grid | Alterar status para PENDING_ENG | ☐ |
| 6.1.4 | Anexar plano de ação | Formulário NRC | Anexar documentação técnica | ☐ |

### 6.2 Cliente (`cliente` / `Cliente@321`)

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 6.2.1 | Logar como cliente | Login | Logar com `cliente` (grupo 9) | ☐ |
| 6.2.2 | Visualizar projetos | `grid_public_mro_projects` | Ver projetos disponíveis (visão restrita) | ☐ |
| 6.2.3 | Aprovar lote O&A | `grid_mro_oa_batches` | Localizar lote de O&A e aprovar | ☐ |

### 6.3 Comercial (`comercial` / `Comercial@321`)

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 6.3.1 | Logar como comercial | Login | Logar com `comercial` | ☐ |
| 6.3.2 | Visualizar NRCs comerciais | `grid_public_mro_nrc` | Ver NRCs em COMMERCIAL_REVIEW | ☐ |
| 6.3.3 | Acompanhar lotes O&A | `grid_mro_oa_batches` | Visualizar lotes pendentes de aprovação | ☐ |

---

## Fase 7 — Acompanhamento e Relatórios

### 7.1 Coordenador (`coordenador` / `Coordenador@321`)

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 7.1.1 | Logar como coordenador | Login | Logar com `coordenador` | ☐ |
| 7.1.2 | Ver dashboard geral | `tabs_supervisor` | Visão consolidada das tarefas | ☐ |
| 7.1.3 | Ver gráfico de tarefas | `chart_public_mro_tasks` | Gráfico com distribuição por status/projeto | ☐ |
| 7.1.4 | Ver timeline do projeto | `blank_mro_timeline` | Linha do tempo geral | ☐ |
| 7.1.5 | Ver Kanban crítico | `blank_kanban_critical` | Tarefas críticas ou bloqueadas | ☐ |

### 7.2 Administrador — Fechamento

| # | O que testar | Onde | Como testar | ✓ |
|---|-------------|------|-------------|---|
| 7.2.1 | Verificar logs de integração | `grid_public_sys_integration_logs` | Conferir logs de comunicação com sistemas externos | ☐ |
| 7.2.2 | Ver relatórios ferramentaria | `form_mro_reports` | Gerar relatório de movimentações | ☐ |
| 7.2.3 | Verificar cadastro completo | Diversos | Navegar por grids para confirmar dados consistentes | ☐ |

---

## Resumo dos cenários de teste

| Fase | Perfil | Cenários |
|------|--------|:--------:|
| 1 — Setup | Administrador | 8 |
| 2 — Planejamento | Planejador | 7 |
| 2 — Planejamento | Engenheiro | 5 |
| 2 — Planejamento | Programador | 4 |
| 3 — Execução | Supervisor | 7 |
| 4 — Execução | Mecânico | 9 |
| 4 — Execução | Ferramentaria | 11 |
| 5 — Pós-execução | Supervisor | 4 |
| 5 — Pós-execução | Qualidade (Admin) | 7 |
| 6 — O&A | Engenheiro | 4 |
| 6 — O&A | Cliente | 3 |
| 6 — O&A | Comercial | 3 |
| 7 — Acompanhamento | Coordenador | 5 |
| 7 — Fechamento | Administrador | 3 |
| | **Total** | **80** |

---

## Fluxo de status esperado por objeto

### Tarefa (Rotina)
```
PLANNED → RELEASED → (alocado via supervisor) → IN_PROGRESS → COMPLETED → (assinatura digital)
```

### Tarefa (NRC)
```
DRAFT → PENDING_ENG → PENDING_PROG → PENDING_OA OU RELEASED → COMPLETED
```

### Alocação (Task Assignment)
```
NOT_STARTED → IN_PROGRESS → PAUSED / BLOCKED → IN_PROGRESS → COMPLETED
```

### Ferramenta (Tool Transaction)
```
Check-out → FERRAMENTA EM EMPRESTIMO → Check-in → DISPONIVEL
```

---

> **Observação:** Alguns fluxos dependem de cadeia completa (ex: NRC só vira O&A se estourar o CAP financeiro). Em ambiente de teste, esses comportamentos podem ser simulados ajustando valores manuais.
