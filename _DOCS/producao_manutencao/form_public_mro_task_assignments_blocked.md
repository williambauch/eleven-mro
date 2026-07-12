# Alocações — Bloqueadas (form_public_mro_task_assignments_blocked)

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário de tarefas com impedimentos reportados. É a terceira aba do Painel do Supervisor, exibindo alocações com status BLOCKED.

## O que o usuário pode fazer

- Visualizar tarefas bloqueadas por material, ferramenta ou mão de obra.
- Registrar ações para desbloqueio.
- Remover o bloqueio quando o problema for resolvido.

## Comportamentos e regras importantes

- A aba também recebe o login do usuário (`usr_login`) como contexto adicional.
- O bloqueio pode ser removido diretamente pelo supervisor assim que o recurso estiver disponível.

## Dados envolvidos

Tabela `public.mro_task_assignments` com status BLOCKED.
