---
name: registro-tarefa-mro
description: Cria e atualiza o arquivo de registro de tarefas dentro de __Tarefas/<CODIGO>/ seguindo o formato padrao do projeto. Use sempre que o usuario pedir para "registrar tarefa", "criar task", "abrir task", "documentar alteracao", "registrar implementacao" ou quando uma nova atividade de desenvolvimento for iniciada. Diferente da SKILL-DOCUMENTADOR (que documenta fluxo de navegacao das apps ScriptCase), esta skill registra o que foi feito, porque e os resultados de uma tarefa especifica de desenvolvimento.
---

# SKILL — Registro de Tarefas de Desenvolvimento MRO System

Esta skill padroniza a criacao e manutencao dos arquivos de registro de tarefas
dentro da pasta `__Tarefas/<CODIGO_TAREFA>/`. Cada tarefa de desenvolvimento
(seja nova funcionalidade, correcao ou melhoria) deve ter seu proprio diretorio
com documentacao, migrations e artefatos de teste.

Sempre leia o `AGENTS.md` da raiz do workspace antes de comecar, se ele existir.
Ele define convencoes do projeto (nomenclatura, macros, padroes de seguranca)
que este registro deve respeitar e refletir.

---

## Estrutura de pastas

```
__Tarefas/<CODIGO_TAREFA>/
├── <CODIGO_TAREFA> - <titulo da tarefa>.md   # registro principal
├── migrations/                                # scripts SQL migratorios
│   ├── <CODIGO_TAREFA>_descricao.sql
│   └── ...
├── dump_bkp_*.sql                             # backup de dados antes de alterar
└── teste_* /                                  # artefatos de teste (opcional)
    └── ...
```

### Regras de nomenclatura

- Nome do diretorio: `__Tarefas/<CODIGO_TAREFA>/` (ex: `__Tarefas/MRO-115/`)
- Nome do arquivo principal: `<CODIGO_TAREFA> - <titulo da tarefa>.md` (ex: `MRO-115 - Revisar importacao materiais e flag de bloqueio.md`)
- Nome dos migrations: `<CODIGO_TAREFA>_descricao.sql` (ex: `MRO-115_add_is_blocking_task_mro_materials.sql`)
- Pastas de teste: `teste_<descricao>/` (ex: `teste_importacao/`)

---

## Formato do arquivo principal

O arquivo `<CODIGO_TAREFA> - <titulo>.md` deve seguir esta estrutura, nesta ordem:

### 1. Cabecalho — Definicao da Tarefa

```
# DEFINICAO DA TAREFA

=========== DEFINICAO DA TAREFA ===========

**<titulo da tarefa>**

<descricao do contexto e objetivo da tarefa>

Criterios de Aceite (Definition of Done):

<lista de criterios de aceite>

Informacoes Tecnicas (Para o Desenvolvedor):

<tabelas impactadas, campos chave, observacoes importantes>

==============================================
```

### 2. Secao de backup

```
# BACKUP e APLICACOES CRIADAS

PROJETO   <nome>

**## Editado**
<lista de arquivos/aplicacoes editadas>

**## NOVO**
<lista de arquivos/aplicacoes criadas>

**# UTEIS**
<links, comandos, informacoes uteis>
```

### 3. Sumario das alteracoes implementadas

```
## Sumario das alteracoes implementadas

## `<nome_da_aplicacao_ou_pasta>`

### <descricao curta da alteracao 1>

**`caminho/relativo/do/arquivo`**
- <o que foi feito bullet point>
- <regras de negocio implementadas>

### <descricao curta da alteracao 2>

**`caminho/relativo/do/arquivo`**
- <o que foi feito bullet point>

---

## `<outra_aplicacao>`

### <descricao curta>

**`caminho/relativo/do/arquivo`**
- <o que foi feito bullet point>
```

**Regras do sumario:**

- Agrupar as alteracoes pelo **nome da aplicacao/pasta** como heading `##` (ex: `## form_public_mro_tasks`)
- Dentro de cada app, cada alteracao vira um sub-item `###` com a descricao curta (sem repetir o nome da app)
- Separar cada **app/pasta** com `---` (linha horizontal)
- Alteracoes da **mesma app** ficam juntas SEM separador entre si
- Bullet points descritivos do que foi feito, com foco na **regra de negocio**, nao no codigo
- Se um arquivo foi **deletado**, mencionar com tom adequado ("removido", "excluido")
- Se ficou **pendente**, marcar explicitamente com `**PENDENTE**`
- **Caminhos de eventos:** usar apenas o nome do evento, sem prefixo numerico e sem extensao. Ex: `events/onLoad` (e nao `events/05_onLoad/onLoad.scriptcase`), `events_ajax/deferment_status_onClick`
- **`config.json`:** alteracoes de configuracao de campos nao sao documentadas individualmente na tarefa

### 4. Testes de validacao

```
## Testes de validacao (projeto X, Y linhas)

### Teste <Letra> - <descricao do cenario>
- **<METRICA A>:** <valor>
- **<METRICA B>:** <valor>
- **Resultado:** <aprovado/reprovado>
- Arquivo: `__Tarefas/<CODIGO>/teste_<desc>/<arquivo>`

### Validacao no banco <opcional>
| Metrica | Valor |
|---------|-------|
| <nome> | <valor> |
```

---

## Convencoes de escrita

1. **Sempre em PT-BR**, inclusive nomes de pastas e labels.
2. **Sem acentos** nos nomes de arquivos e caminhos (para evitar problemas entre ambientes).
3. Bullet points diretos, sem floreios — estilo tecnico objetivo.
4. Preferir `-` a asteriscos para listas.
5. Usar **negrito** para nomes de arquivos, metricas e labels importantes.
6. Usar ```code blocks``` para SQL, exemplos de codigo ou comandos.
7. Tabelas formatadas com pipe `|` para dados comparativos.

---

## Regras de organizacao

### Migrations
Sempre salvar os scripts SQL migratorios dentro de `__Tarefas/<CODIGO>/migrations/`,
**nao** em `_Banco_de_dados/migrations/`. O nome do arquivo deve comecar com o codigo
da tarefa seguido de descricao:

```
__Tarefas/MRO-115/migrations/MRO-115_add_is_blocking_task_mro_materials.sql
__Tarefas/MRO-115/migrations/MRO-115_change_unique_constraint_mro_materials.sql
```

### Dumps de backup
Se a tarefa envolver alteracao de dados em producao, incluir um dump de backup
antes da migracao:

```
__Tarefas/MRO-115/dump_bkp_mro_materials.sql
```

### Artefatos de teste
Resultados de testes (logs, HTML de validacao, screenshots) devem ficar em subpastas
descritivas dentro do diretorio da tarefa:

```
__Tarefas/MRO-115/teste_importacao/teste-F-14072026.html
__Tarefas/MRO-115/teste_importacao/teste-G-14072026.html
```

---

## Quando usar esta skill

| Situacao | Acionar |
|----------|---------|
| Iniciar uma nova tarefa de desenvolvimento | Criar estrutura `__Tarefas/<CODIGO>/` + arquivo `.md` |
| Implementar alteracoes em apps ScriptCase | Atualizar o sumario com cada alteracao feita |
| Rodar testes de validacao | Adicionar secao de testes com resultados |
| Finalizar a tarefa | Revisar se sumario cobre todas as alteracoes |
| Usuario pedir "registre o que foi feito" | Criar ou atualizar o arquivo de registro |

Nao usar esta skill para:
- Documentar fluxo de navegacao de apps (use SKILL-DOCUMENTADOR)
- Criar/alterar tabelas no banco (use SKILL-DB-MANUTENCAO)
- Criar codigo de eventos ScriptCase (use o AGENTS.md)
