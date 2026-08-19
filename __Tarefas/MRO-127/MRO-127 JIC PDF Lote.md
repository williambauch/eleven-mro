# Impressão do Pack JIC em massa

Deve ter habilitado os recursos de pesquisa e no campo do número da JIC deve ser possível selecionar várias JICs.

Ter a opção de selecionar tudo.

Ter a opção de imprimir somente 1 relatório (Exemplo, imprimir somente a folha da JEC.) - Este recurso tem que ter na impressão individual tbm.


## Rascunho DEV, proposta de implementação William Bauch.

(etapa 00) migrar o código de blank_pdf_pack_jic para um novo arquivo PHP na pasta de bibliotecas internas do projeto (_Bibliotecas_Internas), dedicado apenas a impressão (ex: mro_impressao_JIC.php). **Não mexer no `mro_engine.php`**. Este novo PHP terá escopo para as macros do ScriptCase (sc_lookup, sc_exec_sql, sc_log_add, etc.), pois será chamado de dentro de aplicações. Criar também uma nova aplicação de controle (control_filtro_jic) que vai receber os ids das JICs selecionadas e os tipos de relatórios a serem impressos.

> **Nota:** a seleção múltipla de JICs com opção "selecionar tudo" **já existe nativamente** na `grid_public_mro_tasks` (checkbox de seleção de linhas da grid). Não é necessário implementar nada novo nesse aspecto — o botão de impressão em massa deve apenas consumir a seleção nativa. Não vamos mexer no `mro_engine.php` vamos criar um novo arquivo php dentro Biblioteca interna (_Bibliotecas_Internas) esse php será dedicado a impressao apenas. E ele tem escopo para as macros do scriptcase

(etapa 01) na grid_public_mro_tasks (que já tem todos os filtros configurados) criar um botao do tipo Run PHP que usa o recurso nativo do scriptcase de selecionar as linhas dessa grid_public_mro_tasks montando assim um array com os ids das JICs selecionadas, e passar esse array para a aplicação de filtro de impressão do pack JIC (nova app control_filtro_jic).

(etapa 02) Na control_filtro_jic tem um campo de select multiplo que vai receber o array de ids das JICs selecionadas (isso é opcional), e um campo de multi checks com as opções de relatórios a serem impressos (Exemplo: Folha da JEC, Folha do JIC, etc).

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

no final , gerar um arquivo ZIP com todos os PDFs gerados e disponibilizar para download. Se for um JIC unico, disponibilizar o PDF diretamente para download.

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