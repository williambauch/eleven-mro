# Aplicações (sec_grid_sec_apps)

Módulo Security — aplicação do tipo Grid.

Tela de consulta de todas as aplicações registradas no sistema de segurança. Cada aplicação Scriptcase que passou pelo processo de sincronização aparece aqui com seu nome técnico e descrição.

## O que o usuário pode fazer

- Consultar a lista de aplicações registradas.
- Visualizar o nome técnico (`app_name`) e a descrição de cada aplicação.

## Comportamentos e regras importantes

- A grid apenas exibe os dados, sem permitir cadastro ou exclusão direta. O registro de novas aplicações é feito através da tela de sincronização (`sec_sync_apps`).
- A consulta SQL é simples, sem filtros ou joins, refletindo diretamente a tabela `public.sec_apps`.

## Dados envolvidos

Tabela `public.sec_apps`.
