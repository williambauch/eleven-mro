# Usuários (sec_grid_sec_users)

Módulo Security — aplicação do tipo Grid.

Tela de listagem de todos os usuários cadastrados no sistema. Exibe dados como nome, login, e-mail, status (ativo/inativo) e se possui privilégio de administrador. A partir desta grid é possível acessar o cadastro de cada usuário para editar dados ou ajustar permissões.

## O que o usuário pode fazer

- Visualizar a relação completa de usuários do sistema.
- Identificar rapidamente quem está ativo ou inativo.
- Acessar a tela de edição de cada usuário para alterar dados cadastrais ou redefinir senha.
- Filtrar e ordenar a lista por qualquer coluna disponível.

## Comportamentos e regras importantes

- A grid consulta diretamente a tabela `public.sec_users`.
- O campo `priv_admin` indica se o usuário tem privilégio de administrador geral.
- A coluna `picture` (foto do usuário) consta na consulta SQL mas pode não ser exibida na grid dependendo da configuração de campos visíveis.

## Dados envolvidos

Os dados ficam na tabela `public.sec_users`.
