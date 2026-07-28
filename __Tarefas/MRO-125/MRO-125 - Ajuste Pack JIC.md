# MRO-125 - Ajustes no "Pack JIC" enviados por e-mail pelo Danilo (Capa e Código de Barras)

## Os códigos de Barras funcionam basicamente assim:

tem um total de 40 caracteres, 

Quando a tarefa é uma rotina, o código começa com R e da posição 16 até 21 (no exemplo acima 370021), é o número da rotina (JIC), igual ao exemplo abaixo, da posição 25 até a 28, é o código do projeto(no exemplo abixo o código do projeto é 2945), e a posição 34 e 35 é a especialidade da tarefa (skill), nesse caso A4 = aviônica, se fosse S4 seria Sistemas, se fosse M4, motores, e assim por diante, com base em uma tabela disponível em select 
    skill_id,
    skill_code,
    description,
    anac_license_type,
    integration_alias,
    is_active,
    created_at,
    updated_at
from
    public.mro_skills;
.

Exemplo 01
A imagem mostra um trecho de um formulário ou etiqueta de ordem de trabalho, com fundo branco e texto em preto, organizado em três seções principais:

Lado esquerdo:
Título: JIC Work Order
Abaixo, a tradução em português entre parênteses: (JIC Ordem de Trabalho).
Centro:
Um código de barras horizontal ocupa a maior parte da área central.
Logo abaixo do código de barras aparece a sequência alfanumérica:
R0000000000000003700210002945000000A4I000

Lado direito:
Campo Document N°: seguido do número 370021.
Abaixo, em português: (Código do Cartão).
Mais abaixo, o campo Phase: com o valor APU ON.
Abaixo, em português: (Fase).

O layout é semelhante ao de uma etiqueta ou documento industrial, com informações de identificação e rastreamento, destacando o número do documento, a fase do processo e um código de barras para leitura automatizada.

## Quando a tarefa é uma Não rotina N-
o código começa com N e da posição 16 até 21 é o número da rotina mãe (JIC), e os 3 últimos números são a sequencia da N- (não rotina, nesse caso 001), e a skill da posição 34 e 35 também é a skill.

Exemplo 02
A imagem mostra um cartão de instrução de trabalho com layout em preto e branco, dividido em três áreas principais:

Lado esquerdo
Logotipo DIGEX MRO.
Abaixo do logotipo está o texto:
COM 199912-01/ANAC

Área central
Título em destaque, centralizado:
NON ROUTINE JOB INSTRUCTION CARD

Logo abaixo há um código de barras horizontal.
Sob o código de barras aparece o código alfanumérico:
N0000000000000003700210002945000000A4I001

Lado direito

Há dois campos de identificação:

A/C Work Order: (N° da Ordem de Serviço)
Valor:
09.26

Document N°: (Documento N°)
Valor em destaque:
N370021001

Descrição geral

Trata-se de um cartão de instrução de trabalho não rotineiro utilizado em manutenção aeronáutica. O documento contém o logotipo da empresa, um título identificando o tipo do cartão, um código de barras para rastreamento, o código do documento, o número da ordem de serviço da aeronave (A/C Work Order) e o número do documento. O layout utiliza fundo branco, linhas de separação pretas e textos em negrito para destacar as informações principais.

## Quando a tarefa é uma NN-
o código começa com N também , da posição 12 tem um "N", da posição 13 até a 18 é a JIC (rotina), da 19 até 21 é a segunda sequencia da NN-(nesse caso 001), e os 3 últimos números é a sequencia da N- (001)

Exemplo 03
A imagem mostra um cartão de instrução de trabalho em preto e branco, organizado em três seções principais.

Lado esquerdo
Logotipo:
DIGEX MRO

Abaixo do logotipo:
COM 199912-01/ANAC

Área central
Título centralizado em negrito:
NON ROUTINE JOB INSTRUCTION CARD

Abaixo do título há um código de barras horizontal.
Sob o código de barras está o código alfanumérico:
N0000000000N14A0810010002885000000S4I001

Lado direito

São apresentados dois campos de identificação:

A/C Work Order: (N° da Ordem de Serviço)
Valor:
32.25

Document N°: (Documento N°)
Valor em destaque:
NN14A081001001

Descrição geral

O documento é um Non Routine Job Instruction Card, utilizado para registrar e controlar tarefas de manutenção não rotineiras em aeronaves. O cartão contém o logotipo da organização, um título identificando o tipo de documento, um código de barras para rastreabilidade, um código alfanumérico correspondente ao documento, o número da ordem de serviço da aeronave (A/C Work Order) e o número do documento. O layout utiliza fundo branco, linhas de divisão pretas e informações destacadas em negrito para facilitar a identificação.

## Quando a tarefa é uma NNN
 ela segue um principio parecido, e a cada NR aberta ela acrescenta 3 numeros no código porém sem ultrapassar os 40 caracteres..

 Exemplo 04
 A imagem mostra o cabeçalho de um documento técnico da área de manutenção aeronáutica. Os elementos visíveis incluem:

Logotipo "DIGEX MRO" no canto esquerdo, com a inscrição "COM 19912-01/ANAC" abaixo.
No centro, o título em destaque:
"NON ROUTINE JOB INSTRUCTION CARD" (Cartão de Instrução de Trabalho Não Rotineiro).
Abaixo do título, há um código de barras acompanhado do identificador:
N000000NN1314140080010002885000000P4I001.
No canto superior direito, um campo identificado como:
"A/C Work Order (Nº da Ordem de Serviço)", com o valor 32.25.
Abaixo desse campo, outro identificado como:
"Document Nº (Documento Nº)", contendo o número:
NNN13141400800100, em destaque.

O documento possui um layout em tabela com bordas pretas, típico de formulários técnicos utilizados para controle e rastreabilidade de ordens de serviço em manutenção.

# Resumo das regras do cliente para geração do código de barras
O barcode atual é gerado por uma fórmula simplificada no SQL que não atende às regras reais do cliente.

O barcode tem **40 caracteres** e sua composição varia conforme o tipo da tarefa (Rotina, N-, NN-, NNN-). 
### Regras do cliente (resumo dos 4 exemplos)

| Tipo | Prefixo | JIC (pos.) | Seq. NRC | Projeto (pos.) | Skill (pos.) | Sufixo |
|------|---------|------------|----------|----------------|--------------|--------|
| **Rotina** | `R` | 16-21 (6 dig.) | `000` | 25-28 (4 dig.) | 34-35 (2 dig.) | `I000` |
| **N-** | `N` | 16-21 (6 dig.) | 3 últimos do task_code | 25-28 (4 dig.) | 34-35 (2 dig.) | `I` + seq |
| **NN-** | `N` + `N` na pos.12 | 13-18 (6 dig.) | 19-21 (3 dig.) + 3 últimos | 25-28 (4 dig.) | 34-35 (2 dig.) | `I` + seq |
| **NNN** | `N` + `NN` na pos.11-12 | 14-19 (6 dig.) | 20-22 + 23-25 + 3 últimos | 28-31 (4 dig.) | 37-38 (2 dig.) | `I` + seq |

# Estrutura dos Códigos

## ROTINA (R)

| Posição | Conteúdo | Observação |
|---------:|----------|------------|
| 01 | `"R"` | Identificador de rotina |
| 02–15 | Zeros (14 posições) | Preenchimento fixo |
| 16–21 | JIC | 6 dígitos (LPAD com zeros) |
| 22–24 | `"000"` | Sequencial fixo para rotina |
| 25–28 | `project_id` | 4 dígitos (LPAD com zeros) |
| 29–33 | Zeros (5 posições) | Preenchimento fixo |
| 34–35 | `skill_code` | 2 dígitos (fallback `"XX"`) |
| 36 | `"I"` | Identificador fixo |
| 37–40 | `"0000"` | Valor fixo |

---

## N- (NRC Nível 1)

| Posição | Conteúdo | Observação |
|---------:|----------|------------|
| 01 | `"N"` | Identificador NRC |
| 02–15 | Zeros (14 posições) | Preenchimento fixo |
| 16–21 | JIC | 6 dígitos (LPAD com zeros) |
| 22–24 | Sequencial NRC | 3 dígitos (ex.: `001`) |
| 25–28 | `project_id` | 4 dígitos (LPAD com zeros) |
| 29–33 | Zeros (5 posições) | Preenchimento fixo |
| 34–35 | `skill_code` | 2 dígitos (fallback `"XX"`) |
| 36 | `"I"` | Identificador fixo |
| 37–40 | Sequencial NRC + `"0"` | Ex.: `0010` |

---

## NN- (NRC Nível 2)

| Posição | Conteúdo | Observação |
|---------:|----------|------------|
| 01 | `"N"` | Identificador NRC |
| 02–11 | Zeros (10 posições) | Preenchimento fixo |
| 12 | `"N"` | Indicador de nível 2 |
| 13–18 | JIC | 6 dígitos (LPAD com zeros) |
| 19–21 | Sequencial N1 | 3 dígitos |
| 22–24 | Sequencial N2 | 3 dígitos |
| 25–28 | `project_id` | 4 dígitos (LPAD com zeros) |
| 29–33 | Zeros (5 posições) | Preenchimento fixo |
| 34–35 | `skill_code` | 2 dígitos (fallback `"XX"`) |
| 36 | `"I"` | Identificador fixo |
| 37–40 | Sequencial N2 + `"0"` | Ex.: `0020` |

---

## NNN (NRC Nível 3)

| Posição | Conteúdo | Observação |
|---------:|----------|------------|
| 01 | `"N"` | Identificador NRC |
| 02–10 | Zeros (9 posições) | Preenchimento fixo |
| 11–12 | `"NN"` | Indicador de nível 3 |
| 13–18 | JIC | 6 dígitos (LPAD com zeros) |
| 19–21 | Sequencial N1 | 3 dígitos |
| 22–24 | Sequencial N2 | 3 dígitos |
| 25–27 | Sequencial N3 | 3 dígitos |
| 28–31 | `project_id` | 4 dígitos (LPAD com zeros) |
| 32–36 | Zeros (5 posições) | Preenchimento fixo |
| 37–38 | `skill_code` | 2 dígitos (fallback `"XX"`) |
| 39 | `"I"` | Identificador fixo |
| 40 | Último dígito do Seq. N3 | Ex.: Seq. `123` → posição 40 = `3` |


## O cliente descreve claramente a hierarquia:

- **N- (nível 1)**: `N{JIC}{seq_N}` — ex: `N370021001` (10 chars)
- **NN- (nível 2)**: `NN{JIC}{seq_NN}{seq_N}` — ex: `NN14A081001001` (14 chars)
- **NNN (nível 3)**: `NNN{JIC}{seq1}{seq2}{seq3}` — ex: `NNN131414008001001` (18 chars)

Cada nível adiciona um "N" no prefixo e herda os sequenciais do pai + um novo de 3 dígitos. O código atual não trata isso — vou reescrever a lógica para suportar a hierarquia completa.



| Nível (Pai) | Prefixo Gerado | Estrutura do Código | Exemplo de Entrada | Exemplo Gerado |
|--------------|----------------|---------------------|--------------------|----------------|
| Rotina (`370021`) | `N` | `N` + JIC(6) + sequência(3) | `370021` | `N370021001` |
| N (`N370021001`) | `NN` | `NN` + JIC(6) + sequência N(3) + sequência NN(3) | `N370021001` | `NN370021001001` |
| NN (`NN370021001001`) | `NNN` | `NNN` + JIC(6) + sequências herdadas(6) + sequência NNN(3) | `NN370021001001` | `NNN370021001001001` |
| Formato antigo (`NR370021-01`) | Normaliza para `N` | `NR370021-01` → `N370021001` → gera como um registro `N` | `NR370021-01` | `NN370021001001` |

# Informações passadas na reunião de 23/07 14h

Na capa de Não Rotina do pack jic os campos "List if applicable / Indicar se Aplicável" mostram a lista de não rotinas anteriores. 
Portanto os dois campos * 01 List if applicable e * 02 List if applicable devem exibir o primeiro e último NR antes da atual.


## Sumario das alteracoes implementadas - WILLIAM BAUCH

### `Timesheet/ctrl_abertura_nrc` — Geracao de codigo NRC hierarquica

- O codigo gerava NR370021-01 (NR + JIC + hifen + 2 digitos), mas o cliente especifica N370021001 (N + JIC + 3 digitos).
- Formato alterado de `NR{JIC}-{seq2}` para `N{JIC}{seq3}`, sem hifen, com sequencial de 3 digitos.
- Logica hierarquica completa: rotina gera `N...`, `N` gera `NN...`, `NN` gera `NNN...`, herdando sequenciais do pai e adicionando novo nivel de prefixo `N`.
- Compatibilidade retroativa com formato antigo `NR...` tanto na leitura do pai quanto na busca de filhos existentes.

### `form_public_mro_tasks` — Campo skill_code obrigatorio

- Campo `{Skill_Code}` definido como obrigatorio no formulario, pois e utilizado na montagem do codigo de barras do Pack JIC (posicao 34-35).

### `reports/blank_pdf_pack_jic` — Barcode conforme regras do cliente

- Barcode removido do SQL, calculado via metodo dedicado `mMontarBarcode()` que monta os 40/41 caracteres conforme o tipo detectado pelo task_code (Rotina `R`, NRC nivel 1 `N`, nivel 2 `NN`, nivel 3 `NNN`).
- Cada tipo tem layout proprio de posicoes: prefixo, JIC (6 digitos), sequenciais NRC, project_id (4 digitos), skill_code (2 dig., fallback `XX`), e sufixo `I`.

### `reports/blank_pdf_pack_jic` — Campos de preenchimento manual em branco

- Conforme definido em reuniao (23/07), os campos assinatura, stamp, SHIFT, revisao, list if applicable (pag. 1, 3, 4 e 5) foram mantidos em branco no PDF para preenchimento manual, sem dados do banco.

### `reports/blank_pdf_pack_jic` — Capa padrao JIC para rotinas

- A pagina 01 do PDF tem duas versoes: uma para rotinas (is_nrc false) e outra para nao rotinas (is_nrc true).
- Metodo `mGerarPagina1NR.php` — capa para tasks com `is_nrc = true` (Nao Rotina), com campos especificos como barcode, NRCs anteriores, checkboxes de ferramentas calibráveis.
- Metodo `mGerarPagina1JIC.php` — capa para tasks com `is_nrc = false` (Rotina), implementado com base no layout de `mGerarPagina6.php`.
- Campos mantidos em branco (preenchimento manual conforme reuniao): Revisao da Referencia, Assinatura do Cliente, Assinatura do IIO, datas.

### `reports/blank_pdf_pack_jic` — Listar NRCs anteriores nos campos 01/02 List if applicable

- Na capa JIC de uma Nao Rotina, os campos "01 List if applicable" e "02 List if applicable" exibem o codigo da primeira e da ultima NRC **filhas diretas** do documento atual.
- Cada documento mostra apenas seus filhos diretos:
  - **Rotina** → NRCs N- filhas
  - **N-** → NRCs NN filhas
  - **NN** → NRCs NNN filhas
- Se nao houver NRCs filhas, os campos ficam vazios.
- Exemplo do cliente:
  - Document `130139` (rotina) → `N130139001`, `N130139002`, `N130139003`, `N130139004`
  - Document `N130139004` (N-) → `NN13013900401`
  - Document `NN13013900401` (NN) → `NNN130139004001001`

# Na reuniao do dia 23/07 ficou definido que os campos abaixo do Pack JIC ficaram em branco para ser preenchidos manualmente.
 
- Pagina 01
Revisao da Referencia, Assinatura do Cliente, Assinatura do IIO se necessario
 
- Pagina 03
 Requester (Sign & Stamp),  Provider (Sign & Stamp)
 
- Pagina 04
 SHIFT, STAMP
 
- Pagina 05
 Inspector Stamp,  Incerteza-erro-desv,  Equipment pass,  Inspector Stamp