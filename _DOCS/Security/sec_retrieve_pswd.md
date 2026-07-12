# Recuperar Senha (sec_retrieve_pswd)

Módulo Security — aplicação do tipo Form.

Tela de recuperação de senha para usuários que esqueceram suas credenciais. Envia um link ou código de redefinição por e-mail ou SMS, conforme configurado no sistema.

## O que o usuário pode fazer

- Solicitar a recuperação da senha informando o e-mail ou login.
- Receber as instruções de redefinição.
- Definir uma nova senha.

## Comportamentos e regras importantes

- Só fica disponível se a opção "Recuperar Senha" estiver ativada nas configurações do sistema.
- O método de envio (e-mail ou SMS) depende da configuração global.

## Dados envolvidos

Tabela `public.sec_users` e configurações de `sec_settings`.
