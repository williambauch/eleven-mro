# Prompt — Como usamos Botões de Barra de Ações em Grids (ScriptCase )

## Contexto

- As grids têm uma barra de ações com botões que agem sobre a LINHA
  SELECIONADA (não são botões normais de formulário).
- A lógica de "qual botão aparece/habilita em cada linha" é feita no evento
  `04_onRecord` (executado para CADA linha).
- A lógica do CLIQUE é feita em eventos Ajax com nome `actbtn_<botao>_onClick`
  (ex.: `actbtn_btn_realizado_onClick.scriptcase`), na pasta `events_ajax/`.
- Convenção de nomes: botões com prefixo `btn_` (ex.: `btn_realizado`,
  `btn_pagar_fatura`, `btn_renovar`).
- Regras de variáveis do projeto: campos `{nome}`, globais `[nome]`, locais
  `$var_nome`. Nunca use `{$var}`.

## Macros utilizadas (ScriptCase)

| Macro | Uso |
|---|---|
| `sc_actionbar_clicked_state()` | Recupera o estado atual do botão AJAX da barra de ações no momento do clique |
| `sc_actionbar_disable("btn_x")` | Desabilita dinamicamente um botão da barra de ações |
| `sc_actionbar_enable("btn_x")` | Habilita um botão desabilitado por `sc_actionbar_disable` |
| `sc_actionbar_hide("btn_x")` | Esconde dinamicamente o botão da barra de ações |
| `sc_actionbar_show("btn_x")` | Exibe um botão escondido por `sc_actionbar_hide` |
| `sc_actionbar_state("btn_x", "stateN")` | Define o estado visual do botão (state1, state2, state3...) — usado em botões com múltiplos estados |

## Padrão 1 — Botão com múltiplos estados (toggle realizado)

Botão que alterna entre estados (ex.: "realizado / não realizado").
No ScriptCase o botão é configurado com 2 ou 3 estados visuais.

### 04_onRecord — define o estado inicial de cada linha

```php
// Habilita os botões para a linha atual
sc_actionbar_enable("btn_realizado");
sc_actionbar_enable("btn_realizar_data");

// Se a conta é cartão de crédito, o botão vira um 3º estado (navegação)
if ({conta.tipo_conta_id} == 4) {
    sc_actionbar_state('btn_realizado', 'state3');
    sc_actionbar_disable("btn_realizar_data");
} else {
    // Define o estado baseado no valor do registro
    if ({financeiro_movimento.realizado} == 's') {
        sc_actionbar_state('btn_realizado', 'state2');
    } else {
        sc_actionbar_state('btn_realizado', 'state1');
    }
}

// Transferência de saldo (tipo 5) não pode ser alternada
if ({financeiro_movimento.tipo_financeiro_id} == 5) {
    sc_actionbar_disable("btn_realizado");
    sc_actionbar_disable("btn_realizar_data");
}
```

### events_ajax/actbtn_btn_realizado_onClick.scriptcase — ação do clique

```php
<?php

$var_empresa_id = (int)[empresa_id];
$var_movimento_id = (int){financeiro_movimento.id};

if ($var_empresa_id <= 0 || $var_movimento_id <= 0) {
    sc_ajax_message("Parâmetros inválidos para atualização do status");
    sc_error_exit();
}

// Verifica o estado atual do botão no momento do clique
if (sc_actionbar_clicked_state() == 'state1') {
    // Altera para o estado 2 (SIM REALIZADO)
    sc_actionbar_state('btn_realizado', 'state2');

    $update_sql = "UPDATE financeiro_movimento
                   SET realizado = 's'
                   WHERE id = " . $var_movimento_id . "
                     AND empresa_id = " . $var_empresa_id . "
                     AND realizado <> 's'";
    sc_log_add("financeiro_realizado", $update_sql);
    sc_exec_sql($update_sql);

} elseif (sc_actionbar_clicked_state() == 'state2') {
    // Altera para o estado 1 (NÃO REALIZADO)
    sc_actionbar_state('btn_realizado', 'state1');

    $update_sql = "UPDATE financeiro_movimento
                   SET realizado = 'n'
                   WHERE id = " . $var_movimento_id . "
                     AND empresa_id = " . $var_empresa_id . "
                     AND realizado <> 'n'";
    sc_log_add("financeiro_realizado", $update_sql);
    sc_exec_sql($update_sql);

} elseif (sc_actionbar_clicked_state() == 'state3') {
    // Estado 3 = navegação (ex.: ir para faturas)
    sc_redir("grid_fatura");
}

sc_ajax_refresh();
```

Pontos importantes:
- `sc_actionbar_clicked_state()` devolve o estado que o botão tinha NA LINHA
  antes do clique (definido no onRecord).
- Cada estado executa uma ação e redefine o estado visual com
  `sc_actionbar_state`.
- Sempre `sc_log_add` antes de `sc_exec_sql` (auditoria).
- `sc_ajax_refresh()` ao final para recarregar o grid sem página nova.
- Sempre filtrar por `empresa_id` nas queries (tenant).

## Padrão 2 — Mostrar/esconder botões conforme o valor do registro

Quando a grid tem vários botões e cada situação de registro exibe um
conjunto diferente.

### 04_onRecord — switch por situação

```php
switch({situacao}) {
    case 'v': // pré-venda
        sc_actionbar_show("btn_pre_venda");
        sc_actionbar_hide("btn_venda_aberto");
        sc_actionbar_hide("btn_pendente_pagamento");
        sc_actionbar_hide("btn_venda_finalizada_resumo");
        sc_actionbar_hide("btn_movimento_venda");
        break;

    case 'a': // venda aberta
        sc_actionbar_show("btn_venda_aberto");
        sc_actionbar_hide("btn_pendente_pagamento");
        sc_actionbar_hide("btn_venda_finalizada_resumo");
        sc_actionbar_hide("btn_pre_venda");
        sc_actionbar_hide("btn_movimento_venda");
        break;

    case 'f': // finalizada
        sc_actionbar_show("btn_venda_finalizada_resumo");
        sc_actionbar_show("btn_movimento_venda");
        sc_actionbar_hide("btn_venda_aberto");
        sc_actionbar_hide("btn_pendente_pagamento");
        sc_actionbar_hide("btn_pre_venda");
        break;
}
```

## Padrão 3 — Habilitar/desabilitar botão por linha (regra de negócio)

Quando o botão existe para todas as linhas, mas só é acionável em algumas.

### 04_onRecord — habilitar/desabilitar condicional

```php
// Ex.: grid_fatura — só permite pagar quando a fatura está em aberto
if ({saldo_fatura} >= 0) {
    sc_actionbar_disable("btn_pagar_fatura");
} else {
    sc_actionbar_enable("btn_pagar_fatura");
}
```

## Padrão 4 — Botão de navegação (link para outra app)

Quando o botão simplesmente redireciona (Botão de Ligação ou sc_redir no Ajax).

```php
// Ex.: no Ajax de um botão "realizar data"
sc_redir("grid_fluxo_de_caixa_saldo");
```

## Regras de ouro (não quebrar)

1. **Todo clique em botão de ação que altera dados** passa por `sc_log_add`
   antes do `sc_exec_sql` (auditoria).
3. `sc_actionbar_clicked_state()` é o ÚNICO jeito de saber qual estado o
   botão estava ao clicar — use sempre no início do `actbtn_*_onClick`.
4. No `04_onRecord`, SEMPRE defina o estado/habilidade/visibilidade do botão
   para CADA linha (o padrão do ScriptCase é por registro, não global).
5. Botões com múltiplos estados usam `sc_actionbar_state` com nomes
   `state1`, `state2`, `state3` — configurados no IDE com ícones/cores
   diferentes.
6. Após ação que muda a linha, chamar `sc_ajax_refresh()` para atualizar o
   grid.
7. `sc_actionbar_hide/show` é para esconder/exibir (some da barra);
   `sc_actionbar_disable/enable` é para desabilitar/habilitar (aparece
   cinza). Use cada um com intenção.
8. Campos do ScriptCase SEMPRE com `{}`; variáveis locais com `$var_`.
   Nunca `{$var}`.


---

**Como usar:** cole o bloco acima na outra IA (junto com a descrição do botão que ela precisa implementar) e peça para gerar o código seguindo esses padrões.
