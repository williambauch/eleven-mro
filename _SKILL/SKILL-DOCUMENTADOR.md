---
name: documentacao-fluxo-scriptcase
description: Gera e atualiza a documentação de fluxo de navegação e funcionamento das aplicações Scriptcase do workspace, a partir dos arquivos menu_tree.md encontrados. Use sempre que o usuário pedir para "documentar o sistema", "gerar documentação", "mapear o menu", "atualizar a doc do projeto", "explicar o fluxo das telas/aplicações" ou pedir um raio-x de como os módulos e apps se conectam — mesmo que ele não cite "menu_tree.md" diretamente. Também aciona quando o usuário pede para entender aplicações que não aparecem em nenhum menu, ou quer uma visão geral de um app_name/ específico dentro do projeto Scriptcase.
---

# Gerador de Documentação de Fluxo — Scriptcase

Esta skill gera documentação em Markdown descrevendo o fluxo de navegação do sistema
Scriptcase presente no workspace, usando os arquivos `menu_tree.md` como espinha dorsal
e enriquecendo cada item do menu com o que existe de fato na pasta da aplicação
(`config.json`, `events/`, `events_ajax/`, `button/`, `methods/`, `sql/schema.sql`).

Sempre leia o `AGENTS.md` da raiz do workspace antes de começar, se ele existir. Ele define
convenções do projeto (nomenclatura, macros, padrões de segurança) que a documentação deve
respeitar e refletir. Se o `AGENTS.md` não existir, siga apenas as regras desta skill.

## Regras gerais (herdadas do AGENTS.md)

- Responder e documentar sempre em PT-BR.
- Nunca usar emojis, nem no texto nem em exemplos de código citados.
- Escrever em estilo humano, direto, sem parecer texto gerado por IA (nada de "Vamos
  explorar juntos..." ou floreios).
- Nunca alterar arquivos originais das aplicações Scriptcase. Esta skill é somente leitura
  sobre o código — o único artefato que ela produz é o(s) arquivo(s) de documentação.
- Respeitar as convenções de nomenclatura do projeto ao descrever apps, métodos e botões:
  aplicações em minúsculo com underscore, métodos com prefixo `m`, botões com prefixo `btn`.

## Passo 1 — Localizar os arquivos menu_tree.md

Procure em todo o workspace por arquivos `menu_tree.md` (podem existir vários, um por
aplicação de menu). Use busca recursiva, por exemplo:

```bash
find . -name "menu_tree.md"
```

Cada arquivo encontrado vira um bloco separado na documentação final. Guarde o caminho
relativo de cada um, pois ele identifica o bloco (ex: `menu_principal/menu_tree.md`).

Se nenhum `menu_tree.md` for encontrado, avise o usuário claramente e pergunte se ele quer
que a documentação seja gerada só a partir da varredura de pastas de aplicações (pulando
direto para o Passo 3, sem estrutura de menu).

## Passo 2 — Interpretar a árvore de cada menu_tree.md

Cada linha do `menu_tree.md` segue o formato:

```
Módulo > Submódulo > Nome do Item (nome_da_app)
```

Pode haver mais de um nível de submódulo. Reconstrua a hierarquia completa (módulo,
submódulos intermediários, item folha e o nome técnico da app entre parênteses). Essa
hierarquia é a estrutura de tópicos/subtópicos da documentação daquele bloco.

## Passo 3 — Levantar todas as aplicações que existem de fato no workspace

Localize todas as pastas de aplicação seguindo a estrutura descrita no AGENTS.md (uma
pasta `app_name/` contendo `config.json` e, tipicamente, `events/`). Um jeito confiável:

```bash
find . -name "config.json" -not -path "*/node_modules/*"
```

O nome da pasta pai de cada `config.json` é o nome técnico da aplicação.

## Passo 4 — Cruzar aplicações encontradas com os itens dos menu_tree.md

Para cada aplicação localizada no Passo 3, verifique se o nome técnico dela aparece em
algum item de algum `menu_tree.md` lido no Passo 2.

- Se aparece: ela já tem lugar definido — será documentada dentro do bloco e do galho da
  árvore correspondente.
- Se não aparece em nenhum menu_tree.md: ela é uma aplicação "órfã" e precisa ser tratada
  no Passo 5.

## Passo 5 — Posicionar aplicações órfãs no fluxo mais lógico

Antes de isolar uma app órfã, tente encaixá-la no ponto mais lógico da árvore existente.
Sinais para decidir onde ela se encaixa, em ordem de confiança:

1. **Prefixo/nome parecido** com apps já mapeadas (ex: `form_pedidos_item` perto de
   `form_pedidos` ou de `grid_pedidos`).
2. **Tabela compartilhada**: leia `sql/schema.sql` da app órfã e compare com o schema de
   apps já mapeadas — tabelas em comum indicam mesmo domínio/módulo.
3. **Referência cruzada no código**: procure por `sc_redir` ou chamadas ao nome da app
   órfã dentro dos eventos/métodos de apps já mapeadas (e vice-versa). Isso indica que uma
   app chama a outra dentro de um fluxo existente.
4. **Tipo de aplicação e contexto de negócio** (grid, form, relatório) inferido do
   `config.json` e do conteúdo dos eventos, comparado ao domínio do módulo mais próximo.

Se encontrar um encaixe razoável, adicione o item na árvore do bloco correspondente,
marcando de forma discreta que não veio do menu original, por exemplo:

```
Pedidos > Lançamentos > Itens do Pedido (form_pedidos_item) — não listada no menu_tree.md, associada por tabela compartilhada com form_pedidos
```

Se depois de checar os quatro sinais acima não houver nenhum encaixe plausível, **não
force** a associação. Coloque a aplicação em um bloco novo e separado, ao final da
documentação, chamado "Aplicações sem vínculo de menu identificado", com uma lista simples
das apps e uma linha dizendo por que não foi possível posicioná-las.

## Passo 6 — Documentar cada aplicação pelo comportamento, não pelo código

O foco da documentação é explicar **o que a aplicação faz do ponto de vista de quem usa e
de quem mantém o sistema** — não listar arquivos, eventos ou métodos como se fosse um
índice técnico. Pense em como você explicaria a tela para um colega que nunca viu o
código: o que ela resolve, o que o usuário consegue fazer nela, e o que acontece por trás
quando ele faz cada ação.

Leia `config.json`, `events/`, `events_ajax/`, `button/`, `methods/` e `sql/schema.sql`
como fonte de informação, mas a saída não deve espelhar essa estrutura de pastas. Resuma
em texto corrido e objetivo:

- **O que é essa tela**: uma ou duas frases dizendo o propósito da aplicação dentro do
  fluxo do sistema (ex: "tela onde o operador lança os itens de um pedido já aberto,
  vinculando produto, quantidade e valor").
- **O que o usuário pode fazer**: as ações reais disponíveis (cadastrar, editar, excluir,
  aprovar, gerar relatório, disparar um cálculo, etc.), não a lista de botões em si.
- **Comportamentos e regras importantes**: o que acontece quando o usuário faz algo —
  validações que bloqueiam a gravação, campos que ficam readonly em certas condições,
  cálculos automáticos, mensagens de erro relevantes, integrações com outras telas via
  `sc_redir`. Traduza a regra de negócio para linguagem natural (ex: "não permite salvar
  sem informar o cliente" em vez de "valida `{IdCliente}` com `sc_error_message`").
- **Para onde essa tela leva ou de onde ela vem**: se dispara ou é chamada por outra
  aplicação do fluxo, mencione isso em uma frase, pois ajuda a entender o encadeamento do
  processo.
- **Dados envolvidos**: só mencione tabela(s) quando isso ajudar a entender o
  comportamento (ex: "os dados ficam gravados na tabela de pedidos e no seu detalhe"), sem
  virar um dicionário de campos.

Evite listar nomes de eventos (`01_onApplicationInit`, `Campo_onChange`, etc.), nomes de
métodos ou nomes de botões como itens soltos de uma lista técnica. Se um botão específico
dispara um comportamento importante, descreva o comportamento dentro do texto, não o nome
do arquivo do botão. O código-fonte não deve aparecer citado na documentação.

Se a aplicação for simples (uma grid de consulta sem regra especial, por exemplo), o
resumo pode ter só duas ou três frases — não force parágrafos longos onde não há
comportamento relevante para descrever.

Se alguma pasta esperada (`events/`, `button/`, `methods/`, `sql/schema.sql`) não existir,
apenas não mencione aquele aspecto, sem inventar comportamento.

## Passo 7 — Onde salvar a documentação

Toda a documentação gerada por esta skill vive dentro de uma pasta `_DOCS/` na raiz do
workspace (com underscore na frente, para ficar no topo da listagem de arquivos). Se
`_DOCS/` não existir, crie-a. Não escreva documentação fora dessa pasta.

### Estrutura de pastas e arquivos

Dentro de `_DOCS/`, organize por subpastas — uma para cada bloco de menu (arquivo
`menu_tree.md` encontrado). Cada subpasta segue o formato abaixo:

```
_DOCS/
├── INDICE.md                       # visão geral, aponta para cada subpasta de menu
├── <nome_do_menu_1>/
│   ├── fluxo.md                    # índice do módulo com links para cada app
│   ├── app_exemplo_1.md            # uma aplicação por arquivo
│   ├── app_exemplo_2.md
│   └── ...
├── <nome_do_menu_2>/
│   ├── fluxo.md
│   ├── app_exemplo_3.md
│   └── ...
└── orfas/
    └── sem_vinculo.md              # aplicações sem vínculo de menu identificado
```

`<nome_do_menu_N>` é derivado do nome da pasta onde está o `menu_tree.md` daquele bloco
(ex: `app__menu/menu_tree.md` vira `_DOCS/app__menu/`). Se dois `menu_tree.md` estiverem
em pastas com o mesmo nome, use o caminho relativo inteiro para diferenciar.

### Regra: um arquivo `.md` por aplicação

Cada aplicação (cada item do menu) recebe **seu próprio arquivo** nomeado pelo nome
técnico da app, com extensão `.md`. Exemplos:

- `grid_nf_tomadas_nfephp.md`
- `grid_controle_nfephp.md`
- `app__grid_sec_users.md`

Isso mantém a documentação organizada e fácil de navegar, sem acumular tudo em um
arquivo gigante. O conteúdo de cada arquivo segue as orientações do Passo 6
(documentar pelo comportamento, não pelo código).

### Estrutura do `fluxo.md` (índice do módulo)

O arquivo `fluxo.md` de cada subpasta **não** contém mais a documentação completa das
aplicações. Ele funciona como um índice do módulo, com:

1. Uma descrição geral do módulo (quem tem acesso, contexto de uso, regras comuns
   a todas as apps daquele módulo).
2. Uma lista de links para os arquivos individuais de cada aplicação, com o nome
   de exibição (como aparece no menu) e uma linha de resumo do que a app faz.

Exemplo:

```markdown
# Fluxo — Security/app__menu/menu_tree.md

Última atualização: 2026-07-09.

## Módulo Raiz

Acesso comum a todos os usuários do sistema, tanto administradores quanto clientes.
Cada usuário enxerga apenas os dados da organização à qual pertence.

### Aplicações do módulo

- [Painel (grid_nf_tomadas_nfephp)](grid_nf_tomadas_nfephp.md) — listagem de NF-e recebidas, manifestação e downloads.
- [Empresas (grid_controle_nfephp)](grid_controle_nfephp.md) — cadastro de empresas, certificados digitais e conexão SEFAZ.
- [Histórico SEFAZ (grid_log_nfephp)](grid_log_nfephp.md) — auditoria de operações com a SEFAZ.
```

Os links no `fluxo.md` são relativos (mesmo diretório), apontando para o arquivo `.md`
da aplicação.

### Estrutura do arquivo de cada aplicação

Cada arquivo de aplicação segue o mesmo formato definido no Passo 6, com seções
separadas por cabeçalhos `##`:

```markdown
# Nome de Exibição (nome_tecnico_app)

Módulo <nome> — aplicação do tipo <Grid|Form|...>.

<parágrafo inicial explicando o propósito da tela em uma ou duas frases>

## O que o usuário pode fazer

<lista ou texto corrido com as ações disponíveis>

## Comportamentos e regras importantes

<validações, bloqueios, cores, campos readonly, integrações>

## Para onde essa tela leva

<apps que esta tela chama, apps que chamam esta tela>

## Dados envolvidos

<tabela(s) principal(is) e contexto relevante>
```

### Arquivo de aplicações órfãs

O arquivo `_DOCS/orfas/sem_vinculo.md` segue:

```markdown
# Aplicações sem vínculo de menu identificado

## <Nome da app> (<nome_da_app>)

<texto corrido descrevendo o que a aplicação faz, seguido de uma frase explicando por que
não foi possível posicioná-la em nenhum bloco de menu>
```

E o `_DOCS/INDICE.md` é o ponto de entrada, com links relativos para cada `fluxo.md` e para
o arquivo de órfãs, mais um resumo de uma ou duas linhas do que cada bloco cobre:

```markdown
# Índice da Documentação

- [<nome_do_menu_1>](./<nome_do_menu_1>/fluxo.md) — <resumo curto do módulo>
- [<nome_do_menu_2>](./<nome_do_menu_2>/fluxo.md) — <resumo curto do módulo>
- [Aplicações sem vínculo de menu](./orfas/sem_vinculo.md)
```

Se só existir um `menu_tree.md` no workspace, ainda assim siga essa estrutura de pastas —
facilita quando novos menus forem criados depois.

## Passo 8 — Ao atualizar uma documentação já existente

Se `_DOCS/` já existir, releia o Passo 1 ao Passo 6 do zero (o código pode ter mudado) e
regenere os arquivos afetados dentro de `_DOCS/`, em vez de tentar fazer patch manual
neles. Antes de sobrescrever, avise o usuário do que mudou em relação à versão anterior em
um resumo curto (apps novas, apps removidas, apps que trocaram de bloco ou de pasta).

## Ao final

Depois de gerar os arquivos dentro de `_DOCS/`, apresente um resumo objetivo ao usuário:
quantos blocos de menu foram encontrados, quantas aplicações foram documentadas, quantas
ficaram em `_DOCS/orfas/` e, se fizer sentido, sugira o que falta esclarecer (ex: pastas de
app sem `config.json`, `menu_tree.md` com itens apontando para pastas que não existem no
workspace).
