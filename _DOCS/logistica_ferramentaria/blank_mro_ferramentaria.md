# Terminal Ferramentaria (blank_mro_ferramentaria)

Módulo Logística e Ferramentaria — aplicação do tipo Blank.

Terminal de bipagem para controle de ferramentas no chão de fábrica. Permite ao mecânico realizar check-out (retirada) e check-in (devolução) de ferramentas utilizando leitura de código de barras, com validações de conformidade ANAC em tempo real.

## O que o usuário pode fazer

- Selecionar o projeto/check de manutenção ativo.
- Digitar ou bipar o código da tarefa (JIC/NRC).
- Digitar ou bipar o código do crachá do mecânico.
- Digitar ou bipar o código de barras/part number da ferramenta.
- Alternar entre modo SAÍDA (check-out) e RETORNO (check-in).
- Fixar campos de JIC e matrícula para bipagem em série sem precisar redigitá-los.
- Visualizar no painel lateral o histórico de atividades da estação.
- No check-in, informar a condição de retorno da ferramenta: OK, danificada ou extraviada.
- Em caso de avaria ou perda, o sistema redireciona para formulários de relatório SGSO (Qualidade).

## Comportamentos e regras importantes

- **Validações obrigatórias** (em ordem):
  1. O projeto deve ser selecionado.
  2. A tarefa (JIC) deve existir e pertencer ao projeto selecionado.
  3. O mecânico deve estar cadastrado no sistema pela matrícula.
  4. A tarefa deve estar atribuída ao mecânico (via `mro_task_assignments`).
  5. **Trava de Clock-in**: o mecânico só pode retirar ferramentas se possuir um clock-in ativo na tarefa (timesheet com `end_time IS NULL`). Sem isso, o acesso é negado.
  6. A ferramenta deve estar cadastrada no sistema.
- **Check-out**:
  - Se a calibração da ferramenta estiver vencida, o sistema bloqueia a retirada e atualiza o status para "FERRAMENTA EM CALIBRAÇÃO".
  - Se a ferramenta já estiver emprestada, o sistema informa e bloqueia.
  - Se a ferramenta não estiver no empenho planejado da tarefa, exibe um aviso adicional.
  - Registra a transação em `mro_tool_transactions` e atualiza o status da ferramenta para "FERRAMENTA EM EMPRESTIMO".
- **Check-in**:
  - Se a condição for "DANO", a ferramenta é retida como "FERRAMENTA DANIFICADA" e abre um relatório SGSO.
  - Se a condição for "PERDA", a ferramenta é marcada como "FERRAMENTA EXTRAVIADA" e abre um relatório de perda.
  - Se OK, a ferramenta volta ao status "DISPONIVEL".
  - Após o check-in, a transação é fechada e o bloqueio de ferramenta da tarefa é removido (`is_blocked_tool = false`).
- **Interface otimizada para bipagem**: os campos avançam automaticamente com Enter/Tab, e o painel lateral exibe logs em tempo real com feedback visual (verde para sucesso, vermelho para erro).

## Para onde essa tela leva

Em caso de avaria ou perda, redireciona para os formulários de relatório SGSO (TF-60-013 e TF-60-041).

## Dados envolvidos

Tabelas `mro_tasks`, `mro_employees`, `mro_task_assignments`, `mro_timesheet`, `mro_tools`, `mro_tool_transactions`, `mro_projects`, `mro_task_tools`.
