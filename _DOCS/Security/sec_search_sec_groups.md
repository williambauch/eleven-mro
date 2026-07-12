# Grupos x Aplicações (sec_search_sec_groups)

Módulo Security — aplicação do tipo Form.

Tela de consulta que permite selecionar um grupo e visualizar quais permissões ele possui sobre as aplicações do sistema. É uma tela de apoio para que administradores verifiquem rapidamente o que determinado grupo pode acessar.

## O que o usuário pode fazer

- Selecionar um grupo de acesso pelo seu identificador.
- Visualizar as permissões associadas ao grupo (acesso, inserção, exclusão, alteração, exportação e impressão) para cada aplicação.

## Comportamentos e regras importantes

- O campo de grupo é populado com os dados da tabela `public.sec_groups`.
- Ao selecionar um grupo, o filtro é aplicado automaticamente via evento `onFilterValidate`, que copia o valor do grupo para o filtro da consulta.

## Dados envolvidos

Tabelas `public.sec_groups` e `public.sec_groups_apps`.
