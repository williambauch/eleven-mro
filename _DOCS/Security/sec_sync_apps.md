# Sincronizar Aplicações (sec_sync_apps)

Módulo Security — aplicação do tipo Form.

Tela de sincroniação entre as aplicações Scriptcase existentes no disco e o registro do sistema de segurança. Permite identificar aplicações novas (não cadastradas) e aplicações removidas (que não existem mais no disco mas ainda constam no banco).

## O que o usuário pode fazer

- Executar a sincronização entre o diretório de aplicações e o banco de dados.
- Optar por excluir automaticamente os registros de aplicações que não existem mais no disco (marcando a opção "Remover deletadas").
- Visualizar o resultado da sincronização: quantas aplicações foram sincronizadas e quantas foram removidas, com os respectivos nomes.

## Comportamentos e regras importantes

- **Validação**: Antes de sincronizar, o sistema escaneia o diretório `_lib/_app_data/` em busca de arquivos de aplicação e compara com o que está registrado em `public.sec_apps`.
- **Aplicações novas**: Cada aplicação encontrada no disco mas não registrada no banco é inserida em `sec_apps` e recebe permissão de acesso para todos os grupos existentes (inserção em `sec_groups_apps`).
- **Aplicações removidas**: Se a opção "Remover deletadas" estiver ativa, o sistema apaga de `sec_apps` e `sec_groups_apps` as aplicações que existem no banco mas não no disco.
- **Resultado**: Após a execução, a tela exibe um resumo com o total de aplicações sincronizadas e deletadas, além de uma tabela detalhada com os nomes.
- Links externos (http/https) no registro de aplicações são ignorados na exclusão.

## Dados envolvidos

Tabelas `public.sec_apps`, `public.sec_groups_apps`. Leitura do diretório `_lib/_app_data/` do Scriptcase.
