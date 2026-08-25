# Impressão do Pack JIC em massa

Deve ter habilitado os recursos de pesquisa e no campo do número da JIC deve ser possível selecionar várias JICs.

Ter a opção de selecionar tudo.

Ter a opção de imprimir somente 1 relatório (Exemplo, imprimir somente a folha da JEC.) - Este recurso tem que ter na impressão individual tbm.


## Implementação final (25/08/2026) — resumo do que foi feito

(etapa 00) **Tudo na `blank_pdf_pack_jic`** : ela é a fábrica de PDF (individual e lote). Orquestração no `onExecute`; código grande/complexo em METODOS MENORES. (NÃO USEI biblioteca nova em `_Bibliotecas_Internas POIS TERIA QUE FICAR TUDO EM UM ÚNICO ARQUIVO PHP SENDO DIFÍCIL PARA MANUTENÇÃO`)

(etapa 01) **`grid_public_mro_tasks`**: botões `btn_config_impressao_JIC` (PHP), `btn_marcar_impressao_JIC` (Ajax) e `btn_limpar_impressao_JIC` — definem a origem: ids marcados (`[glo_jic_ids]`) **ou** filtro da grid (`[glo_jic_where]`).

(etapa 02) **`control_filtro_jic`**: campos `filtro_page` (multi-check JIC/JEC/JMC/SHIFT/CALIBRATED), `filtro_agrupado` (S/N) e `filtro_resume` (resumo + aviso de origem dos dados + volumetria). `onLoad` monta o resumo; `onValidate` exige ≥1 página.

(etapa 03) **`onValidateSuccess` da control**: resolve a lista final de ids (WHERE → consulta `mro_tasks` e monta CSV; ou ids marcados), normaliza `agrupado` (S/Sim/Y/Yes/1 → `S`), envia via `sc_redir(..., 'glo_pack_jic_ids=...;glo_jic_paginas=...;glo_jic_agrupado=...', 'modal', '', '800', '600')`.

(etapa 04) **`blank_pdf_pack_jic` (onExecute)**: modo lote (`[glo_pack_jic_ids]`) → logs `em_andamento` → loop gerando PDF por task no temp (`tmp_downloads`) → **se `agrupado=S` usa `mGerarPdfLoteAgrupado` (merge real, 1 PDF com N páginas); senão `mEmpacotarPdfs` (ZIP)** → log geral → **renderiza tela com botão "Baixar arquivo" (fixo) + "Fechar" (vermelho, via `tb_remove`)** → limpeza por idade (60 min). Modo individual (`[glo_task_id]`) mantém o PDF direto + log.

(etapa 05) **Limpeza do temp** por idade (`mDiretorioTemp('limpar_antigos')` — apaga arquivos com mais de 60 min).

**Logs**: tabela `mro_task_print_log` (batch_id, task_id, task_code, usuario, status, arquivo_gerado, etc.).

**Observações**: botão "Baixar" é fixo no lote; PDF único vai direto pro download.

**Status: ✅ TAREFA CONCLUÍDA (25/08/2026)** — fluxo validado ponta a ponta: grid → control (resumo + origem + volumetria) → blank (lote: ZIP/merge real + tela com Baixar/Fechar; individual: PDF direto) → logs em `mro_task_print_log` → limpeza do temp por idade (60 min).

---

## Rascunho DEV, proposta de implementação William Bauch.

(etapa 00) migrar o código de blank_pdf_pack_jic para um novo arquivo PHP na pasta de bibliotecas internas do projeto (_Bibliotecas_Internas), dedicado apenas a impressão (ex: mro_impressao_JIC.php). **Não mexer no `mro_engine.php`**. Este novo PHP terá escopo para as macros do ScriptCase (sc_lookup, sc_exec_sql, sc_log_add, etc.), pois será chamado de dentro de aplicações. Criar também uma nova aplicação de controle (control_filtro_jic) que vai receber os ids das JICs selecionadas e os tipos de relatórios a serem impressos.

> **Nota:** Não vamos mexer no `mro_engine.php` vamos criar um novo arquivo php dentro Biblioteca interna (_Bibliotecas_Internas) esse php será dedicado a impressao apenas. E ele tem escopo para as macros do scriptcase

(etapa 01) na grid_public_mro_tasks (que já tem todos os filtros configurados) criar um botao do tipo PHP chamado  btn_config_impressao_JIC :

// MRO-127: Monta a lista de JICs para impressão em lote e
// redireciona para a control_filtro_jic (seleção de relatórios).
//
// Regras de negócio:
//  - Se [glo_jic_ids] tiver ids (marcados via btn_marcar_impressao_JIC),
//    usa SOMENTE esses ids e limpa [glo_jic_where].
//  - Se [glo_jic_ids] vazio, NAO popula a global com milhares de ids:
//    salva o WHERE do filtro atual que vem de {sc_where_current} em [glo_jic_where] e limpa
//    [glo_jic_ids]. A control_filtro_jic processa direto pelo WHERE.

(etapa 02) Na control_filtro_jic tem:
Páginas Geradas com checkbox para escolher quais páginas serão geradas: JIC - JOB INSTRUCTION CARD	JEC - JOB EQUIPMENT AND TOLL CARD	JMC - JOB MATERIAL CARD	SHIFT TURNOVER	CALIBRATED TOOL STAMTP CONTROL SHEET
  
Agrupar PDF em um único arquivo? Não ou Sim

Campo que exibe em resumo as JIC que estão seleciondas: 3 Task(s) selecionada(s): NRWB-ROTINA-B-01, NWB-ROTINA-B002, WB-ROTINA-B

(etapa 03) o onvalidadeSucess dessa control_filtro_jic chama a geração do PDF em uma nova blank blank_pdf_pack_jic_lote.

(etapa 04) Como pode haver centenas de JICs selecionadas  (com várias páginas), o ideal é que a geração do PDF seja feita em background, e que seja visivel logs de impressão de JICs, para que o usuário possa acompanhar o status da impressão em tela (talvez dando reload em blank_pdf_pack_jic_lote).

Podemos usar um diretorio temporário semelhante ao que já usei no DANFE em outro projeto:

    // Diretorio temporario para o ZIP
    $var_temp_dir = __DIR__ . "/tmp_downloads";
    if (!is_dir($var_temp_dir)) {
        mkdir($var_temp_dir, 0777, true);
    }

 // Salvar PDF no diretorio temporario
    $var_pdf_nome = "DANFE_{$var_chave}.pdf";
    $var_destino = $var_temp_dir . "/" . $var_pdf_nome;
    $var_gravou = file_put_contents($var_destino, $var_pdf);

    if ($var_gravou !== false && file_exists($var_destino)) {
        [arquivos_pdf][] = $var_destino;
    } else {
        echo "Erro ao salvar PDF temporario para chave {$var_chave}.<br>";
        exit;
    }

no final , gerar um arquivo ZIP com todos os PDFs gerados e disponibilizar para download. Se o campo do filtro {filtro_agrupado} vier como 'S' temos que fazer o marge dos PDFs em um unico arquivo, caso contrario gerar um arquivo ZIP com todos os PDFs gerados.
Se for um JIC unico, disponibilizar o PDF diretamente para download.

Registrar logs de impressão em uma tabela no banco de dados, com informações como: usuário que solicitou a impressão, data e hora da solicitação, status da impressão (em andamento, concluída, erro - Tentar registrar o erro se houver), flag se o pdf está em diretorio ou foi apagado na limpeza de pasta e o caminho do arquivo gerado.

(etapa 05) Limpar a pasta temporaria no final de cada BLOCO execução, para evitar que arquivos antigos fiquem ocupando espaço.

---

## Plano revisado (18/08/2026) — fluxo "Todas do filtro" + volumetria

### Contexto
Quando `[glo_jic_ids]` está vazio, o `btn_config_impressao_JIC` usa o `{sc_where_current}`
e poderia popular a global com MILHARES de ids (rotina + NRCs filhas de cada uma),
estourando sessão/URL. O resumo também ficava gigante.

### Decisões

**1. `btn_config_impressao_JIC` (botão da grid)**
- `[glo_jic_ids]` **preenchido** (marcados via barra de ação) → mantém ids, limpa `[glo_jic_where]`.
- `[glo_jic_ids]` **vazio** → **NÃO popular** com milhares; salva o WHERE atual em
  `[glo_jic_where]` e limpa `[glo_jic_ids]`.
- `sc_redir('control_filtro_jic')` nos dois casos.

**2. `control_filtro_jic` — novos campos**
| Campo | Tipo | Uso |
|---|---|---|
| `filtro_somente_rotinas` | Checkbox | "Somente rotinas (sem NRCs filhas)" — aplica `AND parent_task_id IS NULL` sobre a lista atual (ids OU where). **Sempre visível**. Com "Recarregar formulário após alteração do valor" na IDE (sem evento Ajax — o onLoad roda de novo ao marcar/desmarcar) |
| `filtro_resume` | Label HTML | Resumo + **aviso de volumetria embutido** (banner amarelo quando volume alto) |

**3. `onLoad` da `control_filtro_jic`**
- **Modo "todas do filtro"** (`[glo_jic_where]` não vazio):
  - `SELECT task_code FROM mro_tasks WHERE {glo_jic_where}` (+ `AND parent_task_id IS NULL` se `filtro_somente_rotinas` marcado)
  - Conta o total real e exibe TODOS os códigos (sem LIMIT) com scroll — mostrar o volume que será gerado é o objetivo
  - **Aviso de volumetria**: `$var_limiar_volume = 300` (constante configurável) — acima disso, banner amarelo no `filtro_resume` ("Volume alto — a geração pode levar vários minutos")
- **Modo ids explícitos** (`[glo_jic_ids]` preenchido): mantém a lógica `IN (...)` + mesmo tratamento do checkbox `parent_task_id IS NULL` e do aviso de volume.
- Deduplicação: COUNT reflete o que será gerado (com/sem o filtro de rotinas).

**4. `onValidateSuccess` (posterior)**
- Passa para a `blank_pdf_pack_jic_lote`:
  - `[glo_jic_ids]` (ids explícitos) **ou** `[glo_jic_where]` + flag `somente_rotinas` (todas do filtro);
  - a lote monta a lista de tasks a processar.

**5. Riscos / pontos de atenção**
- Performance do COUNT com o WHERE é barata (índice em task_id); a listagem de task_code com milhares de linhas é uma única query (aceitável).
- O resumo SEMPRE reflete o volume que será gerado (objetivo: dar ciência do volume antes do clique em "Gerar PDF").

---

## Andamento (24/08/2026) — Etapa 00 concluída (blank como fábrica de PDF)

### Decisão de arquitetura
- **Não** criar biblioteca em `_Bibliotecas_Internas` (limitação: um arquivo por biblioteca, sem subpastas, e duplicaria as páginas).
- **Tudo na `blank_pdf_pack_jic`**: ela é a fábrica de PDF (individual e lote). Orquestração no `onExecute`; código grande/complexo em methods (facilita manutenção).
- Lote = **uma única execução** da blank recebe a lista inteira (`[glo_jic_ids]` ou `[glo_jic_where]`) e faz o loop interno (sem acumular entre execuções — evita mistura de lotes em abas múltiplas).

### blank_pdf_pack_jic — methods novos (MRO-127)
- `methods/mCarregarDadosTask.php` — SQL base da task (33 campos + skills + materiais + executor + NRCs), extraído do onExecute
- `methods/mGerarPdfTask.php` — monta TCPDF e gera as páginas **selecionadas** (JIC/JEC/JMC/SHIFT/CALIBRATED) para UMA task
- `methods/mGerarPdfLoteAgrupado.php` — **merge real** (agrupado=S): um único TCPDF com as páginas de TODAS as tasks do lote (cada `mGerarPagina*` chama AddPage) → PDF com N páginas reais
- `methods/mEmpacotarPdfs.php` — ZIP (padrão, com fallback PharData) | 1 PDF único; a **concatenação de bytes caiu para fallback** (não é merge real — leitor só abria o 1º PDF)
- `methods/mRegistrarLogImpressao.php` — INSERT/UPDATE na `mro_task_print_log` (assinatura com `$var_mensagem = NULL` e `$var_arquivo = NULL` — o ScriptCase remove valores padrão `''` na reimportação, o que causava HTTP 500)
- `methods/mMontarListaTasks.php` — CSV de ids → lista validada no banco (deduplicada, ordenada por task_code)
- `methods/mDiretorioTemp.php` — `mDiretorioTemp('criar'|'limpar')` — único método com ação (etapa 05)
- `methods/mDisponibilizarDownload.php` — download do PDF/ZIP final

### blank_pdf_pack_jic — onExecute (reescrito como orquestração)
- **Individual** (`[glo_task_id]`): mantém comportamento atual, agora com seleção de páginas via `[glo_jic_paginas]` (padrão: todas) + log individual (`em_andamento` → `concluida`/`erro`)
- **Lote** (`[glo_jic_ids]`): CSV de task_id **já filtrados pela grid/control** → monta lista → logs `em_andamento` → loop (gera PDF por task no temp + log `concluida`/`erro`) → **se `agrupado=S` usa `mGerarPdfLoteAgrupado` (merge real); senão `mEmpacotarPdfs` (ZIP/1 PDF)** → log geral do lote → download → limpeza do temp
- Geração do PDF no lote é **inline no onExecute** (o `mSalvarPdfTask` foi movido para lá — não é mais um method)
- **Decisão (a): somente modo ids** — `glo_jic_where` e `glo_jic_somente_rotinas` **removidos** da blank. O filtro "somente rotinas" passa a ser aplicado na `control_filtro_jic` (no `onValidateSuccess`), que envia os ids finais. Aceito o limite de URL/sessão para volume alto.
- Variáveis globais no `config.json`: `glo_task_id`, `glo_jic_ids`, `glo_jic_paginas`, `glo_jic_agrupado`, `usr_login`
- `sc_include_lib("tcpdf")` mantido

### Comunicação `control_filtro_jic` → blank (`[glo_jic_paginas]`)
- **Formato padronizado: CSV com vírgula, SEM aspas** (ex: `JIC,JEC,JMC`).
- A blank parseia com `explode(',')` + `trim(str_replace(["'", '"'], ...))` — aceita com/sem aspas por tolerância, mas o envio oficial é sem aspas.
- O `onValidate` da control recebe `{filtro_page}` como **array** (multi-check) → o `onValidateSuccess` converte com `implode(',', ...)`.
- `filtro_agrupado`: `S` = merge real em 1 PDF; `N` (padrão) = ZIP.

### Migration
- `migrations/MRO-127_log_impressao_jic.sql` — tabela `mro_task_print_log` (batch_id, task_id, task_code, usuario, data_solicitacao, data_fim, status, mensagem_erro, arquivo_gerado)
- **Sem** `flag_em_diretorio` (decisão: `file_exists` na consulta cobre saber se o arquivo ainda existe; evita UPDATE em massa na limpeza)

### control_filtro_jic — onValidateSuccess (etapa 03 concluída)
- Resolve a lista final de ids: modo WHERE (consulta `mro_tasks` e monta CSV) **ou** modo ids (CSV recebido)
- `[glo_jic_ids]` = CSV final (deduplicado)
- `[glo_jic_paginas]` = CSV com vírgula, sem aspas (via `implode(',')` do array do multi-check)
- `[glo_jic_agrupado]` = `S` (merge real) ou `N` (ZIP, padrão)
- Limpa `[glo_jic_where]` e faz `sc_redir('blank_pdf_pack_jic')`

### Pendências (próximas etapas)
- **PENDENTE** etapa 04: geração em background + tela de acompanhamento dos logs (a blank atual é síncrona)
- **PENDENTE** validar a impressão individual com seleção de página (ex: só JEC)