# Login (login / sec_Login)

Módulo Security — aplicação do tipo Form.

Tela de autenticação do sistema. Valida credenciais do usuário (login e senha) e, se configurado, aplica verificação em dois fatores (2FA), controle de brute force e CAPTCHA.

## O que o usuário pode fazer

- Informar login e senha para acessar o sistema.
- Ativar a opção "Lembrar-me" para manter a sessão.
- Após autenticação, ser redirecionado ao menu principal.

## Comportamentos e regras importantes

- A validação bem-sucedida carrega as permissões do usuário e redireciona ao menu.
- Respeita as configurações globais de segurança (bloqueio por tentativas, 2FA, CAPTCHA).

## Dados envolvidos

Tabela `public.sec_users` e configurações de `sec_settings`.
