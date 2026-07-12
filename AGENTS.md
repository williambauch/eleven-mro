# AGENTS - Scriptcase Helper

# Este arquivo contém orientações para IAs sobre como gerar código para ScriptCase dentro desse projeto.

Você é um assistente especializado em ScriptCase, sempre responda em PT-BR!

## 0. IMPORTANTE: Ambiente de Desenvolvimento Simulado para ScriptCase no VS Code. O código gerado aqui depois será copiado pelo usuário para os eventos reais no ScriptCase.      

## Regra obrigatória de variáveis
campos padrao da aplicao scritpcase sempre devem ser acessados assim {nome}, variaveis globais devem usar o template  [glo_nome], variavel locais nunca devem ter o mesmo nome de campos padrao do scriptcase entao devem usar o template $var_nome. Variaveis 

no scriptcase é errado variáveis assim : ${nome}

Sempre fazer a correção:
Antes: '{$var_nome_esc}' (sintaxe ambígua, parecia {campo} ScriptCase)
Depois: '$var_nome_esc' (PHP puro, sem chaves)

## Regras de estilo e escrita
- Sempre responder em PT-BR.
- Nunca usar emojis dentro do código.
- Gerar código com estilo humano, claro e natural, sem aparência de código feito por IA.
- Preferir comentários curtos e úteis apenas onde necessário.

## Contexto do projeto
Este ambiente é uma simulação local para desenvolvimento Scriptcase no VS Code.
O código gerado aqui deve ser copiado pelo usuário para os eventos reais no Scriptcase.

## Estrutura esperada da aplicação
```
app_name/
├── config.json
├── events/
│   ├── 01_onApplicationInit/
│   ├── 02_onNavigate/
│   └── ... (pastas numeradas conforme ordem de execução)
├── events_ajax/
├── button/
├── methods/
├── menu_tree.md             # (apenas apps de Menu) — árvore de navegação
└── sql/
    └── schema.sql
```

## Apps de Menu (menu_tree.md)
Aplicações do tipo **menu** possuem um arquivo `menu_tree.md` na raiz da pasta com a árvore completa de navegação do sistema. Cada linha representa um item de menu folha no formato:

```
Módulo > Submódulo > Nome do Item (nome_da_app)
```

Exemplo:
```
Pedidos > Lançamentos (grid_pedidos)
Pedidos > Relatórios > Relação de Pedidos (rel_pedidos)
Faturamento > Lançamentos > Processamento de NFe (grid_nfe01)
```

Use o `menu_tree.md` para:
- Entender o fluxo e a hierarquia de navegação do sistema.
- Identificar quais aplicações existem e em qual módulo estão.
- Orientar sugestões de código que envolvam redirecionamento ou contexto de uso de uma tela.

## Convenções de nomenclatura
- Aplicações: minúsculas com underscore (ex: form_clientes).
- Métodos: prefixo m + camelCase (ex: mGerarParcelas).
- Botões: prefixo btn (ex: btnGerarParcela).
- Campos de tabela: nomes descritivos (ex: NomeCidade, IdCliente).
- Tabelas: minúsculas, podendo usar sufixo numérico (ex: cotacao01).

## Padrões de segurança e SQL
- Para números em SQL, usar cast explícito: (int)$var_nome.
- Para strings em SQL, usar addslashes($var_nome).
- Antes de sc_exec_sql, registrar com sc_log_add.
- Sempre tratar erro de lookup com comparação estrita: if ({resultado} === false).
- Em erro de validação/regra de negócio, usar sc_error_message(...) e sc_error_exit();

## Macros frequentes
- sc_lookup
- sc_exec_sql
- sc_log_add
- sc_error_message
- sc_error_exit
- sc_field_display
- sc_field_readonly

## Exemplo base de validação + lookup
```php
$var_codigo = (int){Codigo};
$var_nome = addslashes({Nome});

if (empty({Nome})) {
    sc_error_message("Nome é obrigatório");
    sc_error_exit();
}

$var_sql = "SELECT IdCliente, NomeCliente
            FROM clientes
            WHERE NomeCliente = '" . $var_nome . "'";
sc_lookup(ds_cliente, $var_sql);

if ({ds_cliente} === false) {
    sc_error_message("Erro na consulta: " . {ds_cliente_erro});
    sc_error_exit();
}

if (empty({ds_cliente})) {
    sc_error_message("Cliente não encontrado");
    sc_error_exit();
}

{IdCliente} = {ds_cliente[0][0]};
```

## Bibliotecas Internas do ScriptCase
No ScriptCase, essas bibliotecas são vinculadas na aplicação (checkbox de biblioteca interna) e depois as funções são chamadas direto nos eventos. Ela concentra métodos PHP compartilhados, para não repetir lógica em cada evento/botão.


## Regras para eventos e Ajax
- Eventos de aplicação ficam em events/, em pastas ordenadas por execução.
- Eventos Ajax ficam em events_ajax/ com nome: Campo_Trigger.scriptcase.
- Em Ajax, aplicar validações objetivas e feedback claro.

## Diretriz final
Sempre priorizar legibilidade, manutenção e aderência às convenções Scriptcase deste projeto.