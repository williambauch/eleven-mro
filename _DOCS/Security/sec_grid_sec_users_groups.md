# Usuários x Grupos (sec_grid_sec_users_groups)

Módulo Security — aplicação do tipo Form.

Tela de associação de usuários a grupos de acesso. A partir do login de um usuário, exibe os dados cadastrais e permite vincular ou desvincular grupos. É a tela responsável por definir em qual grupo cada usuário está alocado.

## O que o usuário pode fazer

- Pesquisar um usuário pelo login.
- Visualizar nome, e-mail e status do usuário.
- Alterar o grupo ao qual o usuário pertence.
- Salvar a alteração para atualizar o vínculo no banco.

## Comportamentos e regras importantes

- Ao carregar a tela, o sistema aplica um filtro automático na consulta para exibir apenas os usuários que pertencem ao grupo selecionado. O filtro é aplicado via `sc_select_where` com uma subquery que cruza `sec_users` com `sec_users_groups`.
- Se nenhum grupo estiver selecionado, a tela carrega todos os usuários sem filtro adicional.
- A gravação persiste os dados na tabela `public.sec_users` e a associação de grupo na tabela `public.sec_users_groups`.

## Dados envolvidos

Tabelas `public.sec_users` e `public.sec_users_groups`.
