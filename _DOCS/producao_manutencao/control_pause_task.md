# Controle de Pausa (control_pause_task)

Módulo Produção e Manutenção — aplicação do tipo Form.

Tela de registro de pausas em tarefas em execução. Permite ao mecânico registrar o motivo da pausa (repasse de turno, descanso, refeição) e retomar a atividade posteriormente, ou pular para uma nova tarefa.

## O que o usuário pode fazer

- Registrar o início e fim de uma pausa.
- Informar o motivo da interrupção.
- Retomar a tarefa após a pausa.
- Pular para uma nova tarefa (encerra a pausa e inicia outra tarefa).

## Comportamentos e regras importantes

- **Bloqueio ao pular para nova tarefa (ROTA 5)**: Quando o mecânico escolhe pular para uma nova tarefa, o sistema verifica se a tarefa destino está com `is_blocked_material = true`. Se estiver bloqueada por falta de material, exibe erro e impede a troca.

## Dados envolvidos

Tabela `public.mro_timesheet`. Consulta `public.mro_tasks` para verificar bloqueio de material ao pular de tarefa.
