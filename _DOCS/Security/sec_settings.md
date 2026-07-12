# Configurações do Sistema (sec_settings)

Módulo Security — aplicação do tipo Form.

Tela de configurações globais do sistema. Controla desde parâmetros de segurança (tentativas de login, bloqueio por brute force, 2FA) até preferências de e-mail, tema visual e idioma.

## O que o usuário pode fazer

- Ajustar a expiração de sessão e o tempo de cookie para "lembrar-me".
- Ativar ou desativar a recuperação de senha e escolher o método (e-mail, SMS).
- Configurar o limite de tentativas de login e o tempo de bloqueio por brute force.
- Ativar o autenticador em dois fatores (2FA) e escolher o provedor de API (Google Auth, e-mail ou SMS), tempo de expiração do código e modo de exigência.
- Ativar ou desativar o CAPTCHA no login.
- Alterar o tema visual e o idioma do sistema.
- Configurar o provedor SMTP (customizado ou serviço de API de e-mail) com servidor, porta, segurança, usuário e senha.
- Configurar login via redes sociais (Facebook, Google, X/Twitter) com as respectivas chaves de API.
- Controlar exigência de ativação de e-mail para novos usuários e notificação ao administrador.

## Comportamentos e regras importantes

- **Campos condicionais**: Diversos campos aparecem ou desaparecem conforme as escolhas do usuário, usando eventos Ajax:
  - Se "Brute Force" está desligado, os campos de limite de tentativas e tempo de bloqueio ficam ocultos.
  - Se "2FA" está ligado, os campos de configuração de 2FA (modo, API, tipo, expiração) são exibidos; caso contrário, ficam ocultos.
  - Se "Lembrar-me" está desligado, o campo de expiração de cookie fica oculto.
  - Se "Recuperar Senha" está ligado, o campo de método de recuperação aparece.
  - Se o tipo de SMTP é "customizado", os campos de servidor, porta, usuário, senha etc. são exibidos; se é uma API, esses campos ficam ocultos.
- **Carregamento inicial**: Ao abrir a tela, o sistema carrega todas as configurações salvas da tabela `sec_settings` e preenche os campos dinamicamente.
- **Gravação**: Cada configuração é salva individualmente na tabela `sec_settings` através de updates pontuais por `set_name`.
- **Selects dinâmicos**: Os campos de seleção de API de e-mail e 2FA são populados dinamicamente com as APIs disponíveis no ambiente, através da função `nm_list_apis`.
- Após salvar as configurações de SMTP, o sistema monta a estrutura de configuração na sessão para uso posterior pelo mecanismo de envio de e-mails.

## Dados envolvidos

Tabela `public.sec_settings` (estrutura chave-valor com `set_name` e `set_value`).
