# Grupos x Aplicações (sec_form_sec_groups_apps)

Módulo Security — aplicação do tipo Form.

Tela de definição das permissões de cada grupo de acesso sobre as aplicações do sistema. Controla quais ações (acessar, inserir, alterar, excluir, exportar, imprimir) cada grupo pode realizar em cada aplicação.

## O que o usuário pode fazer

- Selecionar um grupo e uma aplicação.
- Definir as permissões de acesso, inserção, alteração, exclusão, exportação e impressão.
- Salvar as permissões para que entrem em vigor imediatamente.

## Comportamentos e regras importantes

- As permissões definidas aqui são lidas no momento do login e aplicadas via `sc_apl_status` e `sc_apl_conf` em todo o sistema.
- Se um usuário pertence a múltiplos grupos, as permissões são cumulativas.

## Dados envolvidos

Tabela `public.sec_groups_apps`.
