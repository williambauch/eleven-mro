# Autenticação em 2 Fatores (sec_add_2fa)

Módulo Security — aplicação do tipo Form.

Tela de configuração e ativação da autenticação em dois fatores (2FA) para o usuário logado. Dependendo da configuração global do sistema, pode ser obrigatória ou opcional.

## O que o usuário pode fazer

- Visualizar o método de 2FA disponível (Google Authenticator, e-mail ou SMS), de acordo com a configuração definida pelo administrador.
- Escolher o método preferido (quando mais de um está disponível).
- Escanear o QR Code (Google Authenticator) ou solicitar o envio de um código por e-mail ou SMS.
- Informar o código recebido e confirmar a ativação.
- Resetar a configuração de 2FA (desativar).

## Comportamentos e regras importantes

- **Comportamento condicional**: Se o 2FA estiver desabilitado globalmente, a tela exibe apenas uma mensagem informativa e os botões de ação ficam ocultos.
- **Se o usuário já possui 2FA ativo**: A tela oferece a opção de resetar (desativar), a menos que o administrador tenha configurado o modo "obrigatório para todos" (`all`), caso em que o reset é bloqueado.
- **Se o usuário não possui 2FA ativo**: A tela exibe o método disponível conforme configuração:
  - **Google Authenticator**: exibe o QR Code para escaneamento. O usuário informa o código gerado pelo aplicativo para confirmar.
  - **E-mail**: envia um código numérico de 6 dígitos para o e-mail do usuário (com máscara de exibição). O usuário informa o código recebido.
  - **SMS**: envia um código numérico de 6 dígitos por SMS. O usuário informa o telefone e o código recebido.
- **Validação por tempo**: Nos métodos e-mail e SMS, o código tem prazo de expiração configurável. Após o vencimento, o código é invalidado e um novo deve ser solicitado.
- **Contador regressivo**: Após o envio do código, um contador regressivo é exibido na tela indicando o tempo restante para expiração.
- Na ativação bem-sucedida, o método escolhido e o código são registrados na tabela `sec_users` no campo `mfa`, no formato `metodo@@identificador` (ex: `ga@@SEGREDO`, `email@@CODIGO`, `sms@@TELEFONE@@CODIGO`).
- Após ativar o 2FA com sucesso, o cookie de "lembrar-me" (se existir) é removido por segurança.

## Dados envolvidos

Tabela `public.sec_users` (campos `mfa`, `login`). Configurações globais de 2FA lidas de `sec_settings`.
