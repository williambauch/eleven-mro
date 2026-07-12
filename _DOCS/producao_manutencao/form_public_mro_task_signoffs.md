# Assinaturas (form_public_mro_task_signoffs)

Módulo Produção e Manutenção — aplicação do tipo Form.

Tela de registro de assinaturas digitais para conclusão de tarefas. Permite que inspetores de qualidade assinem digitalmente as tarefas concluídas, cumprindo os requisitos de conformidade ANAC.

## O que o usuário pode fazer

- Registrar assinatura digital em uma tarefa concluída.
- Informar o inspetor responsável e a data da assinatura.
- Vincular a assinatura ao registro da tarefa.

## Comportamentos e regras importantes

- A assinatura digital é o passo final do fluxo de conclusão de uma tarefa.
- O sistema deve validar a qualificação do inspetor antes de permitir a assinatura (matriz de qualificação).

## Dados envolvidos

Tabela `public.mro_task_signoffs`. Relacionada com `mro_tasks` e `mro_employees`.
