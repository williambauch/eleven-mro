# Revisão Chão de Fábrica & Backoffice

🎯 Objetivo 
Implementar e ajustar os motores de dados, regras de validação no tablet e interfaces do backoffice para garantir que a operação de manutenção no hangar rode em total conformidade com o escopo contratado, sem gerar desvios ou custos extras não faturados 

📋 Requisitos e Regras de Negócio (Lógica de Desenvolvimento)
1. Módulo Core PM & Engenharia de Dados (Backoffice)
Revisão do Gantt e Kanban:

Gantt sendo gerado com todos os recursos do Syncfusion. Implementar a edição da tarefa direta pelo Gantt

Kambam por Fase e Status da Tarefa

Ambos podendo ser gerado para 1 ou mais projetos (hoje ele é acionado apenas pelo grid de projetos.

2. Módulo de Operações e Chão de Fábrica (Mobile / Tablet)
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

3. Módulo de Logística & Ferramentaria (Almoxarifado)
Provedoria — Tela de Monitoramento de Liberação (Gated Process):

Regra: Grid dedicada que lista apenas as JICs/tarefas que estejam com 100% dos materiais necessários liberados no estoque físico, permitindo ao planejador liberar as tarefas de forma segura para o hangar.

Provedoria — Fluxo de Recolhimento de Material (Bip de Saída):

Regra: No balcão da provedoria, o almoxarife deve realizar a baixa física das peças via leitura do código de barras, atrelando logicamente o lote do componente entregue ao código da JIC correspondente e ao ID do mecânico que realizou a retirada (Rastreabilidade As-Built).

Ferramentaria — Check-in Padrão (Bip de Retorno):

Regra: No ato da devolução da ferramenta no balcão, o atendente bipa o código do ativo e o sistema deve exigir obrigatoriamente a demarcação de seu estado físico de retorno (OK ou Com Avaria). Caso seja marcado como avariado, o sistema altera o status do ativo no banco para indisponível.

No painel do mecânico, criar listagem de ferramentas com avaria pra o ele preencher os formulários de avarias.

🔍 Critérios de Aceite (UAT)
[ ] O timesheet da tarefa-mãe pausa automaticamente no exato instante em que uma NRC impeditiva é criada a partir dela

[ ] A JIC só realiza a transição de status para concluída se todos os mecânicos associados a ela tiverem realizado o clock-out

[ ] O Supervisor logado só visualiza a lista de mecânicos que possuem o mesmo skill técnico que o dele 

[ ] Qualquer mecânico consegue alterar o conteúdo de uma NRC se ela estiver em status de DRAFT, gerando log na tabela mro_task_events 

[ ] A ferramenta de ferramentaria exige a condição de retorno no check-in e bloqueia empréstimos se estiver com calibração vencida

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

### Pendências (para próxima implementação)
- [ ] Auto-pausa do timesheet na abertura de NRC impeditiva
- [ ] Validação de concorrência de timesheet (múltiplos mecânicos) — trava de COMPLETED
- [ ] Mensagem de erro de clock-in duplicado com metadados (Projeto/JIC/Mecânico)
- [ ] Renomear clockout "Tarefa 100% Concluída" → "Timesheet Finalizado/Tarefa Concluída"
- [ ] Edição ampla de NRC em DRAFT com log imutável (mro_task_events — tabela ainda não existe no banco)
- [ ] RLS do Supervisor por Skill + múltiplas skills no colaborador
- [ ] Provedoria: monitoramento de liberação (Gated Process), bip de saída com lote, ferramentaria com check-in de avaria