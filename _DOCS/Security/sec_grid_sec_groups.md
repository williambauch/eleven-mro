# Grupos (sec_grid_sec_groups)

Módulo Security — aplicação do tipo Grid.

Tela de listagem dos grupos de acesso do sistema. Cada grupo possui um identificador numérico e uma descrição. Os grupos são usados para conceder permissões de acesso às aplicações.

## O que o usuário pode fazer

- Visualizar a relação de grupos cadastrados.
- Acessar a edição de cada grupo para alterar descrição ou ajustar permissões associadas.

## Comportamentos e regras importantes

- A consulta é feita diretamente na tabela `public.sec_groups`.
- Os grupos são vinculados a usuários através da associação em `sec_users_groups` e a aplicações através de `sec_groups_apps`.

## Dados envolvidos

Tabela `public.sec_groups`.
