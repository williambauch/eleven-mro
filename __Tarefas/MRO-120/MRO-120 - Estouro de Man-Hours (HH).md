# DEFINICAO DA TAREFA

=========== DEFINICAO DA TAREFA ===========

**Ajustar regras de estouro de Man-Hours (HH), alterar rota da passagem de servico e criar consulta consolidada de apontamentos.**

O controle de horas e transicoes de status no chao de fabrica precisa de ajustes para nao travar a operacao. O sistema deve alertar o estouro de HH sem derrubar o apontamento do usuario, e o fluxo de devolucao/passagem de servico deve ser redirecionado.

Criterios de Aceite (Definition of Done):

- Gestao de Estouro de HH: Ao ultrapassar o tempo estimado, o sistema deve dar visibilidade visual (alerta/cor) do estouro, mas nao deve zerar as horas nem "cair/travar" o apontamento do funcionario. A gravacao do log deve continuar normalmente.

- Passagem de Servico: Alterar a logica de roteamento. Quando houver "Passagem de Servicos", o status/fila deve ser direcionado para a Programacao e nao mais para a Engenharia. Nao deve gerar pendencia de Mao de Obra.
  - A Programacao valida a rotina com o novo botao `btn_validar_prog_rotina` que define a task como `RELEASED` (sem O&A).

- Consulta de Apontamentos: Criar uma tela/grid de consulta de apontamentos consolidados, permitindo filtrar obrigatoriamente por: Projeto, JIC (Job Instruction Card) ou Usuario.

- Multi-atribuicao (Painel do Supervisor): Alterar a logica de atribuicao de responsavel para permitir relacionamento 1:N (mais de 1 funcionario por tarefa).
  > **Ja implementado:** A app `control_split_assignment` (criada na MRO-119) ja permite dividir um assignment em 2, criando multi-atribuicao na pratica. Pendente apenas ajuste visual no painel do supervisor (badge de multi-atribuicao e revisao dos filtros `supervisor_id`).

 # Adendo:
"Caso haja vários mecânicos atuando na mesma tarefa, essa exigência (passagem de serviço) ocorre quando a última pessoa apontada na ficha for sair e a tarefa ainda não estiver concluída . E tem que ter essa logica na passagem de serviço....só será obrigatório informar a passagem quando for o último mecanico atuando na tarefa...."

- Regra nova: Quando há multi-atribuição (vários mecânicos na mesma tarefa), a passagem de serviço só deve ser obrigatória quando o último mecânico atuando for sair e a tarefa ainda não estiver concluída. Se ainda houver outro mecânico trabalhando, a passagem não é exigida.

Informacoes Tecnicas (Para o Desenvolvedor):

- Tabelas impactadas: `mro_tasks`, `mro_task_assignments`, `mro_timesheet`
- Campos chave: `planned_qty_hours` (cota individual do assignment), `duration_minutes` (apontamento real)
- O alerta de HH compara a soma dos `duration_minutes` do timesheet contra o `planned_qty_hours` do assignment (nao o `estimated_hours` da task)

==============================================

# BACKUP e APLICACOES CRIADAS

PROJETO   MRO System

**## Editado**
- `grid_my_tasks` — events/onRecord (criado), events/onApplicationInit (criado)
- `form_public_mro_task_assignments` — events/onLoad, methods_js/iniciar_relogio_mro
- `form_public_mro_task_assignments/button/Play` — corrigido bugs de coluna
- `form_public_mro_tasks` — events/onLoad (workflow rotinas, botoes seletivos), methods/mExibirCardStatus (removido filtro NRC), button/btn_validar_prog_rotina (criado)
- `control_pause_task` — events/onValidateSuccess (rota PENDING_PROG)
- `form_public_mro_task_assignments_{planned,progress,blocked,completed}` — sql/schema.sql (add ORDER BY task_id DESC)
- `form_public_mro_task_assignments_planned` — events/onBeforeUpdate (aceita NOT_STARTED alem de PLANNED para atribuicao de supervisor/mecanico)
- `form_public_mro_task_resources` — events/onLoadRecord (criado - exibe horas consumidas por skill)

**## NOVO**
- `Timesheet/grid_mro_timesheet_consolidado` — Consulta de Apontamentos (criada no menu em Producao e Manutencao)
- `tasks/form_public_mro_tasks/button/btn_validar_prog_rotina` — Botao de validacao de rotina pela Programacao (status RELEASED)

**# UTEIS**
- Skill de registro: `.github/_SKILL/SKILL-REGISTRO-TAREFA.md`
- Plano de implementacao: `__Tarefas/MRO-120/PLANO-IMPLEMENTACAO.md`

## Sumario das alteracoes implementadas

## `grid_my_tasks` — Indicador de estouro de HH

**`events/onRecord`** — Criado
- Cada linha da grid do mecanico agora exibe um badge colorido que compara as horas apontadas vs as horas planejadas do assignment:
  - **Verde:** dentro do limite (<80%)
  - **Amarelo:** atencao (>=80%)
  - **Vermelho:** estouro (>=100%)
- Insert desabilitado na grid (nao faz sentido criar registros manualmente aqui).

---

## `form_public_mro_task_assignments` — Cronometro com alerta de HH

**`events/onLoad`** — Editado
- Cronometro agora exibe, alem do tempo decorrido, um indicador visual de consumo de horas comparando o apontado vs o planejado.
- Cores indicativas: verde (<80%), amarelo (>=80%), vermelho (>=100%).
- O alerta funciona tanto em execucao (IN_PROGRESS) quanto em pausa (PAUSED).

**`methods_js/iniciar_relogio_mro`** — Editado
- Timer em tempo real recalcula o percentual de consumo a cada segundo e atualiza as cores automaticamente.

---

## `control_pause_task` — Passagem de Servico redirecionada para Programacao

**`events/onValidateSuccess`** — Editado
- Quando o mecanico faz "Fim de Turno/Repasse" (motivo_pausa=2), a task nao vai mais para Engenharia — vai para a **fila da Programacao** (`PENDING_PROG`) e **nao gera pendencia de Mao de Obra** (`is_blocked_labor=FALSE`).
- O assignment continua como `PENDING_HANDOVER` para rastreabilidade, mas a task mae ja aparece na fila da Programacao.
- Apos fechar cada sessao do timesheet, o sistema atualiza automaticamente as horas reais apontadas (`actual_qty_hours`) no assignment.
- Migration de backfill: `migrations/MRO-120_backfill_actual_qty_hours.sql`

### Adendo: Handover apenas para o último mecânico

**`events/onValidateSuccess` e `events/onValidate`** — Editados
- Conforme Adendo na definicao da tarefa, a passagem de servico com envio para Programacao so ocorre quando o **ultimo mecanico ativo** da tarefa estiver saindo.
- Se ainda houver outros mecânicos com assignments ativos na mesma tarefa, o assignment que esta saindo tambem vai para **`PENDING_HANDOVER`** (encerrando sua participacao), e a task mae **nao e alterada** — permanece com o status atual para o outro mecânico continuar.
- A validacao de obrigatoriedade do campo `passagem_servico` segue a mesma regra: so e exigido quando for o ultimo mecanico.

---

## `form_public_mro_tasks` — Workflow para Rotinas e NRCs

### Criado: `button/btn_validar_prog_rotina`
- **Novo botao "Valida Rotina"** — Exclusivo para Rotinas Padrao (nao NRC). Executado pela Programacao.
- Ao clicar, calcula o orcado vs o ja trabalhado por skill, e:
  - Se alguma skill estourou o orcamento P6 → **bloqueia** a liberacao e exibe alerta listando as skills estouradas para a Programacao ajustar
  - Se houver saldo positivo e nao tiver assignment `NOT_STARTED` para aquela skill → **cria** novo assignment
  - Se houver saldo positivo e o assignment `NOT_STARTED` existente estiver com horas desatualizadas → **atualiza** as horas
  - Se nao houver saldo nem atualizacao pendente → apenas libera a task
- Task vai para `RELEASED` com log `PROGRAMMING_OK` em `mro_nrc_approval_log`

### Fluxo final

```
PENDING_PROG > btn_validar_prog_rotina
  ├── Se skill estourada → BLOQUEIA (alerta vermelho)
  ├── Se tem saldo → cria/atualiza assignments
  └── Sempre → RELEASED
```

### Editado: `events/onLoad`
- **Card de status agora aparece para todas as tasks** — Antes era exibido apenas para NRCs; agora rotinas tambem veem o card informativo.
- **Botoes padrao (Novo, Salvar, Excluir) controlados por regra** — Para rotinas: Novo fica sempre disponivel, Excluir apenas em DRAFT para o criador, Salvar disponivel exceto em RELEASED/COMPLETED/CANCELLED.
- **Bloco MRO-120 ajustado** — Rotinas em `PENDING_PROG` exibem o botao "Valida Rotina" (`btn_validar_prog_rotina`) em vez do "Validar NRC" (`btn_validar_prog`).

### Fluxos finais

```
NRC:          ... > PENDING_PROG > btn_validar_prog > PENDING_OA > CLIENTE aprova > RELEASED

Rotina Padrao: ... > PENDING_PROG > btn_validar_prog_rotina > RELEASED
```

### Teste validado (WB-ROTINA-C, ID 26332)

| Passo | Acao | De | Para |
|:-----:|------|:--:|:----:|
| 1 | Mecanico inicia a tarefa | `NOT_STARTED` | `IN_PROGRESS` |
| 2 | Mecanico faz Repasse de Turno | `IN_PROGRESS` | `PENDING_PROG` (task) |
| 3 | Programador clica **Valida Rotina** | `PENDING_PROG` | **`RELEASED`** |
| 4 | Log confirmado | — | `PROGRAMMING_OK` (programador) |

---

## `grid_mro_timesheet_consolidado` — Consulta de Apontamentos

### Grid consolidada de apontamentos

**`sql/schema.sql`** — Criado
- SELECT com JOINs em `mro_timesheet`, `mro_employees`, `mro_task_assignments`, `mro_tasks`, `mro_projects`, `mro_skills`
- Colunas: timesheet_id, employee_id, employee_name, employee_registration, appointment_date, start_time, end_time, duration_minutes, status, pause_reason, handover_notes, skill_id, skill_name, planned_qty_hours, actual_qty_hours, task_id, task_code, task_name, estimated_hours, is_nrc, project_id, p6_proj_id, project_name
- Filtros obrigatorios: Projeto, JIC (task_code) ou Usuario/Funcionario
- Menu: Producao e Manutencao > Consulta de Apontamentos

---

## `grid_public_mro_tasks` — Quebra dinamica

### Campos adicionados ao agrupamento (quebra dinamica)
- **Projeto** (`project_name`)
- **Tarefa** (`task_name`)
- **Funcionario** (`employee_name`)
- **Especialidade** (`skill_name`)
- **Status** (`status`)
- **Tipo** (`is_nrc`)

---
## `form_public_mro_task_assignments_planned` — Atribuicao de mecanico

**`events/onBeforeUpdate`** — Editado
- Agora aceita assignments com status `NOT_STARTED` (criados pelo release) alem de `PLANNED` (criacao manual). Antes, assignments liberados pelo `btn_liberar_para_execucao` nao preenchiam `supervisor_id` nem mudavam para `ASSIGNED` ao atribuir um mecanico.

---

## `form_public_mro_task_assignments` — Botao Play

**`button/Play`** — Editado
- Corrigido erro que impedia o cronometro de iniciar (nomes de coluna incorretos no banco). Apos correcao, o mecanico consegue dar Play normalmente.

---

## `tabs_supervisor` — Ordenacao das abas

**`sql/schema.sql` das 4 apps** — Editado
- Assignments da mesma tarefa agora aparecem agrupados (ordenados por task_id), facilitando a visualizacao de multi-atribuicoes pelo supervisor.

---

## `form_public_mro_task_resources` — Consulta de horas consumidas

**`events/onLoadRecord`** — Criado
- Cada recurso (skill) na tela de recursos da tarefa agora exibe quantas horas ja foram efetivamente trabalhadas (consumidas) para aquela skill, com base nos assignments ja finalizados.
