# Alterar Senha (sec_change_pswd)

Módulo Security — aplicação do tipo Form.

Tela de troca de senha do usuário logado. Exige a senha atual para confirmar a identidade antes de definir a nova senha.

## O que o usuário pode fazer

- Informar a senha atual.
- Definir uma nova senha.
- Confirmar a nova senha.

## Comportamentos e regras importantes

- A senha atual é validada antes de permitir a alteração.
- A nova senha deve atender aos critérios de complexidade definidos nas configurações do sistema.

## Dados envolvidos

Tabela `public.sec_users`.
