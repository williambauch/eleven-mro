<?php
/**
 * ScriptCase v9 - Stubs File para VS Code IntelliSense
 *
 * Define todas as macros oficiais do ScriptCase v9 para autocompletar
 * e evitar erros de sintaxe no VS Code.
 *
 * Fonte: https://www.scriptcase.com.br/docs/pt_br/v9/manual/14-macros/02-macros/
 *
 * Este arquivo NAO afeta a execucao real no ScriptCase.
 * Serve apenas para IntelliSense do VS Code.
 */

if (!defined('SCRIPTCASE')) {
    define('SCRIPTCASE', true);
}

// =========================================================================
// Categoria: SQL
// =========================================================================

/**
 * Inicializa um conjunto de transacoes na base de dados.
 * @param string $connection Nome da conexao (opcional)
 * @return void
 */
function sc_begin_trans($connection = '') {}

/**
 * Troca dinamicamente as conexoes das aplicacoes.
 * @param string $old_connection Nome da conexao antiga
 * @param string $new_connection Nome da conexao nova
 * @return void
 */
function sc_change_connection($old_connection, $new_connection) {}

/**
 * Efetiva (commit) as modificacoes desde o inicio da transacao.
 * @param string $connection Nome da conexao (opcional)
 * @return void
 */
function sc_commit_trans($connection = '') {}

/**
 * Combina duas ou mais strings e/ou campos da tabela.
 * @param string ...$fields Campos ou strings para concatenar
 * @return string
 */
function sc_concat(...$fields) {}

/**
 * Edita uma conexao existente em tempo de execucao.
 * @param string $connection_name Nome da conexao
 * @param array $arr_conn Array com dados da conexao
 * @return void
 */
function sc_connection_edit($connection_name, $arr_conn) {}

/**
 * Cria novas conexoes dinamicamente.
 * @param string $connection_name Nome da conexao
 * @param array $arr_conn Array com dados da conexao
 * @return void
 */
function sc_connection_new($connection_name, $arr_conn) {}

/**
 * Desativa o tratamento de erros de banco de dados do Scriptcase.
 * @param string $event Nome do evento
 * @return void
 */
function sc_error_continue($event) {}

/**
 * Configura a variavel com a mensagem de erro do BD durante exclusao.
 * @return string
 */
function sc_error_delete() {}

/**
 * Configura a variavel com a mensagem de erro do BD durante inclusao.
 * @return string
 */
function sc_error_insert() {}

/**
 * Configura a variavel com a mensagem de erro do BD durante atualizacao.
 * @return string
 */
function sc_error_update() {}

/**
 * Executa o comando SQL passado como parametro.
 * @param string $sql Comando SQL
 * @param string $connection Nome da conexao (opcional)
 * @return bool
 */
function sc_exec_sql($sql, $connection = '') {}

/**
 * Executa o comando SELECT e retorna os dados em array (linha/coluna).
 * @param mixed $dataset Variavel que recebera o resultado
 * @param string $sql Comando SELECT
 * @param string $connection Nome da conexao (opcional)
 * @return bool
 */
function sc_lookup($dataset, $sql, $connection = '') {}

/**
 * Executa SELECT retornando array onde o indice e o nome da coluna.
 * @param mixed $dataset Variavel que recebera o resultado
 * @param string $sql Comando SELECT
 * @param string $connection Nome da conexao (opcional)
 * @return bool
 */
function sc_lookup_field($dataset, $sql, $connection = '') {}

/**
 * Apaga as trocas feitas usando sc_change_connection.
 * @return void
 */
function sc_reset_change_connection() {}

/**
 * Desfaz as edicoes de conexao feitas pela macro sc_connection_edit.
 * @return void
 */
function sc_reset_connection_edit() {}

/**
 * Desfaz as conexoes feitas pela macro sc_connection_new.
 * @return void
 */
function sc_reset_connection_new() {}

/**
 * Descarta (rollback) um set de transacoes na base de dados.
 * @param string $connection Nome da conexao (opcional)
 * @return void
 */
function sc_rollback_trans($connection = '') {}

/**
 * Executa o comando SELECT e retorna o dataset em uma variavel.
 * @param mixed $dataset Variavel que recebera o dataset
 * @param string $sql Comando SELECT
 * @param string $connection Nome da conexao (opcional)
 * @return bool
 */
function sc_select($dataset, $sql, $connection = '') {}

/**
 * Altera dinamicamente o campo que sera recuperado pela consulta.
 * @param string $field Nome do campo
 * @return void
 */
function sc_select_field($field) {}

/**
 * Altera dinamicamente o campo da clausula ORDER BY da consulta.
 * @param string $field Nome do campo
 * @return void
 */
function sc_select_order($field) {}

/**
 * Adiciona dinamicamente uma condicao a clausula WHERE da consulta.
 * @param string $add Condicao a adicionar
 * @return void
 */
function sc_select_where($add) {}

/**
 * Modifica o tipo de retorno do dataset dos comandos select.
 * @param int $param Tipo de retorno (1=assoc, 2=num, 3=both)
 * @return void
 */
function sc_set_fetchmode($param) {}

/**
 * Protege valor digitado de acordo com o banco de dados utilizado.
 * @param mixed $value Valor a proteger
 * @param string $type Tipo do dado
 * @param string $connection Nome da conexao (opcional)
 * @return mixed
 */
function sc_sql_protect($value, $type, $connection = '') {}

/**
 * Disponibiliza o conteudo do select original mais o filtro.
 * @return string
 */
function sc_where_current() {}

/**
 * Recupera a clausula where do select original da aplicacao.
 * @return string
 */
function sc_where_orig() {}

// =========================================================================
// Categoria: Data
// =========================================================================

/**
 * Calcula e retorna incrementos e decrementos em datas.
 * @param string $date Data
 * @param string $format Formato da data
 * @param string $operator Operador (+ ou -)
 * @param int $days Dias
 * @param int $months Meses
 * @param int $years Anos
 * @return string
 */
function sc_date($date, $format, $operator, $days, $months, $years) {}

/**
 * Converte o conteudo do campo do formato de entrada para o formato de saida.
 * @param string $date Campo de data
 * @param string $format_in Formato de entrada
 * @param string $format_out Formato de saida
 * @return string
 */
function sc_date_conv($date, $format_in, $format_out) {}

/**
 * Calcula a diferenca entre datas em quantidade de dias.
 * @param string $date1 Primeira data
 * @param string $format1 Formato da primeira data
 * @param string $date2 Segunda data
 * @param string $format2 Formato da segunda data
 * @return int
 */
function sc_date_dif($date1, $format1, $date2, $format2) {}

/**
 * Calcula diferenca entre datas, retornando dias, meses e anos.
 * @param string $date1 Primeira data
 * @param string $format1 Formato da primeira data
 * @param string $date2 Segunda data
 * @param string $format2 Formato da segunda data
 * @param string $option Opcao de retorno
 * @return array
 */
function sc_date_dif_2($date1, $format1, $date2, $format2, $option) {}

/**
 * Checa se um campo do tipo data esta vazio, retornando boleano.
 * @param string $date Campo de data
 * @return bool
 */
function sc_date_empty($date) {}

/**
 * Calcula diferenca em horas, retornando horas, minutos e segundos.
 * @param string $datetime01 Primeiro datetime
 * @param string $format01 Formato do primeiro datetime
 * @param string $datetime02 Segundo datetime
 * @param string $format02 Formato do segundo datetime
 * @return array
 */
function sc_time_diff($datetime01, $format01, $datetime02, $format02) {}

// =========================================================================
// Categoria: Controle
// =========================================================================

/**
 * Executa metodos JavaScript em eventos de Formulario, Controle e Calendario.
 * @param string $method_name Nome do metodo Javascript
 * @param array $parameters Array de parametros
 * @return void
 */
function sc_ajax_javascript($method_name, $parameters = array()) {}

/**
 * Altera as propriedades de execucao das aplicacoes.
 * @param string $application Nome da aplicacao
 * @param string $property Propriedade (start, insert, update, delete, etc)
 * @param string $value Valor da propriedade
 * @return void
 */
function sc_apl_conf($application, $property, $value) {}

/**
 * Define o que ocorrera quando a aplicacao perder a sessao.
 * @param string $application Nome da aplicacao
 * @param string $type Tipo
 * @return void
 */
function sc_apl_default($application, $type) {}

/**
 * Executa o calculo de digitos verificadores.
 * @param string $digit Digito verificador
 * @param string $rest Resto da divisao
 * @param string $value Valor
 * @param int $module Modulo
 * @param string $weights Pesos
 * @param string $type Tipo
 * @return void
 */
function sc_calc_dv($digit, $rest, $value, $module, $weights, $type) {}

/**
 * Retorna true se o valor do campo tiver sido modificado.
 * @param string $field Nome do campo
 * @return bool
 */
function sc_changed($field) {}

/**
 * Retorna o campo ou variavel criptografada a sua forma original.
 * @param string $field Campo ou variavel
 * @return string
 */
function sc_decode($field) {}

/**
 * Retorna o campo ou variavel de forma criptografada.
 * @param string $field Campo ou variavel
 * @return string
 */
function sc_encode($field) {}

/**
 * Interrompe a execucao do codigo (usar com sc_error_message).
 * @param string $app_url Nome da aplicacao ou URL (opcional)
 * @param string $target Target (opcional)
 * @return void
 */
function sc_error_exit($app_url = '', $target = '') {}

/**
 * Gera uma mensagem de erro.
 * @param string $text Texto da mensagem
 * @return void
 */
function sc_error_message($text) {}

/**
 * Forca a saida da aplicacao.
 * @param string $option Opcao de saida
 * @return void
 */
function sc_exit($option) {}

/**
 * Ignora as validacoes definidas na tela de configuracao dos campos.
 * @param string $field_name Nome do campo
 * @return void
 */
function sc_field_no_validate($field_name) {}

/**
 * Atribui as propriedades de um campo para uma variavel javascript.
 * @param string $field Nome do campo
 * @return void
 */
function sc_getfield($field) {}

/**
 * Retorna a sigla do idioma em uso.
 * @return string
 */
function sc_get_language() {}

/**
 * Retorna a sigla da configuracao regional em uso.
 * @return string
 */
function sc_get_regional() {}

/**
 * Retorna o nome do tema do layout em uso.
 * @return string
 */
function sc_get_theme() {}

/**
 * Recupera o id da pagina atual em um formulario wizard.
 * @return int
 */
function sc_get_wizard_step() {}

/**
 * Altera dinamicamente o label dos campos nas linhas de quebra.
 * @param string $field Nome do campo
 * @return void
 */
function sc_groupby_label($field) {}

/**
 * Carrega imagens para serem usadas na aplicacao.
 * @param string ...$images Nomes dos arquivos de imagem
 * @return void
 */
function sc_image(...$images) {}

/**
 * Efetua include de rotinas PHP.
 * @param string $file Nome do arquivo
 * @param string $origin Origem (opcional)
 * @return void
 */
function sc_include($file, $origin = '') {}

/**
 * Seleciona dinamicamente as bibliotecas da aplicacao.
 * @param string ...$libs Nomes das bibliotecas
 * @return void
 */
function sc_include_lib(...$libs) {}

/**
 * Inclue na aplicacao um arquivo PHP de uma biblioteca do Scriptcase.
 * @param string $scope Escopo (public, project, etc)
 * @param string $library_name Nome da biblioteca
 * @param string $file Nome do arquivo
 * @param bool $include_once Usar include_once
 * @param bool $require Usar require
 * @return void
 */
function sc_include_library($scope, $library_name, $file, $include_once = true, $require = false) {}

/**
 * Altera dinamicamente o label do campo.
 * @param string $field_name Nome do campo
 * @return string
 */
function sc_label($field_name) {}

/**
 * Retorna o idioma da aplicacao.
 * @return string
 */
function sc_language() {}

/**
 * Cria dinamicamente um link para outra aplicacao.
 * @param string $column Coluna
 * @param string $application Aplicacao destino
 * @param string $parameters Parametros
 * @param string $hint Hint (opcional)
 * @param string $target Target (opcional)
 * @param int $height Altura (opcional)
 * @param int $width Largura (opcional)
 * @return string
 */
function sc_link($column, $application, $parameters, $hint = '', $target = '', $height = 0, $width = 0) {}

/**
 * Adiciona um registro a tabela de log.
 * @param string $action Acao registrada
 * @param string $message Mensagem do log
 * @return void
 */
function sc_log_add($action, $message) {}

/**
 * Retorna o campo descricao da tabela de log em forma de array.
 * @param string $description Campo descricao
 * @return array
 */
function sc_log_split($description) {}

/**
 * Envia e-mails via SMTP.
 * @param string $smtp Servidor SMTP
 * @param string $user Usuario
 * @param string $password Senha
 * @param string $from Remetente
 * @param string $to Destinatario
 * @param string $subject Assunto
 * @param string $message Mensagem
 * @param string $message_type Tipo da mensagem (H=HTML, T=texto)
 * @param string $copies Copias (CC/BCC)
 * @param string $copies_type Tipo das copias
 * @param int $port Porta SMTP
 * @param string $connection_type Tipo de conexao (TLS/SSL/vazio)
 * @param string $attachment Anexo (opcional)
 * @param bool $ssl Usar SSL (opcional)
 * @param string $reply_to Reply-to (opcional)
 * @return array
 */
function sc_mail_send($smtp, $user, $password, $from, $to, $subject, $message, $message_type, $copies, $copies_type, $port, $connection_type, $attachment = '', $ssl = false, $reply_to = '') {}

/**
 * Cria uma string contendo os dados de um link para outra aplicacao.
 * @param string $application Aplicacao destino
 * @param string $parameters Parametros do link
 * @return string
 */
function sc_make_link($application, $parameters) {}

/**
 * Atualiza um objeto da aplicacao Mestre em uma aplicacao Detalhe.
 * @param string $object Nome do objeto
 * @param string $value Valor
 * @return void
 */
function sc_master_value($object, $value) {}

/**
 * Redireciona para outra aplicacao.
 * @param string $app_destino Aplicacao destino ou URL
 * @param string $parameters Parametros (parametro01; parametro02)
 * @param string $target Target (opcional)
 * @param string $error Mensagem de erro (opcional)
 * @param string $modal_height Altura do modal (opcional)
 * @param string $modal_width Largura do modal (opcional)
 * @return void
 */
function sc_redir($app_destino, $parameters = '', $target = '', $error = '', $modal_height = '', $modal_width = '') {}

/**
 * Reseta as configuracoes da macro sc_apl_default.
 * @return void
 */
function sc_reset_apl_default() {}

/**
 * Elimina as variaveis de sessao recebidas como parametro.
 * @param string ...$global_vars Variaveis globais
 * @return void
 */
function sc_reset_global(...$global_vars) {}

/**
 * Envia notificacoes dinamicamente para os usuarios do sistema.
 * @param string $title Titulo
 * @param string $message Mensagem
 * @param string $destiny_type Tipo de destino
 * @param string $to Destinatario
 * @param string $from Remetente
 * @param string $link Link (opcional)
 * @param string $dtexpire Data de expiracao (opcional)
 * @param string $profile Perfil (opcional)
 * @return void
 */
function sc_send_notification($title, $message, $destiny_type, $to, $from, $link = '', $dtexpire = '', $profile = '') {}

/**
 * Disponibiliza o numero sequencial do registro que esta sendo processado.
 * @return int
 */
function sc_seq_register() {}

/**
 * Registra variaveis de sessao.
 * @param mixed $variable Variavel ou campo
 * @return void
 */
function sc_set_global($variable) {}

/**
 * Seleciona a regra das quebras.
 * @return void
 */
function sc_set_groupby_rule() {}

/**
 * Altera o idioma das aplicacoes.
 * @param string $language Sigla do idioma
 * @return void
 */
function sc_set_language($language) {}

/**
 * Altera a configuracao regional das aplicacoes.
 * @param string $regional Sigla regional
 * @return void
 */
function sc_set_regional($regional) {}

/**
 * Define dinamicamente o tema nas aplicacoes.
 * @param string $theme Nome do tema
 * @return void
 */
function sc_set_theme($theme) {}

/**
 * Verifica se esta sendo utilizado um site seguro (protocolo https).
 * @return bool
 */
function sc_site_ssl() {}

/**
 * Calcula e retorna um array com os valores estatisticos.
 * @param array $arr_val Array com valores numericos
 * @param string $tp_var Tipo da variavel
 * @return array
 */
function sc_statistic($arr_val, $tp_var) {}

/**
 * Seta o numero de casas decimais.
 * @param string $field Campo numerico
 * @param int $decimal_places Quantidade de casas decimais
 * @return void
 */
function sc_trunc_num($field, $decimal_places) {}

/**
 * Altera a URL de saida da aplicacao.
 * @param string $url URL de saida
 * @return void
 */
function sc_url_exit($url) {}

/**
 * Retorna o caminho de um arquivo dentro de uma biblioteca.
 * @param string $scope Escopo
 * @param string $library_name Nome da biblioteca
 * @param string $file Nome do arquivo
 * @return string
 */
function sc_url_library($scope, $library_name, $file) {}

/**
 * Gera valor por extenso.
 * @param float $value Valor
 * @param int $line_size Tamanho da linha
 * @param string $type Tipo (maiuscula/minuscula)
 * @return string
 */
function sc_vl_extenso($value, $line_size, $type) {}

/**
 * Gera arquivo tipo ZIP a partir de uma lista de arquivos e/ou diretorios.
 * @param string $files Arquivos
 * @param string $zip Nome do arquivo ZIP
 * @return void
 */
function sc_zip_file($files, $zip) {}

// =========================================================================
// Categoria: Codigo de Barra
// =========================================================================

/**
 * Gera os valores do codigo de barras no padrao Febraban arrecadacao.
 * @param string $bar_code Codigo de barras
 * @param string $segment_code Codigo de seguimento
 * @param string $currency_code Codigo da moeda
 * @param string $value Valor
 * @param string $free Campo livre
 * @return void
 */
function sc_lin_cod_barra_arrecadacao($bar_code, $segment_code, $currency_code, $value, $free) {}

/**
 * Gera a linha digitavel para bloquetos, padrao bancario.
 * @param string $bar_code Codigo de barras
 * @param string $bank_code Codigo do banco
 * @param string $currency_code Codigo da moeda
 * @param string $value Valor
 * @param string $free Campo livre
 * @param string $due_date Data de vencimento
 * @return void
 */
function sc_lin_cod_barra_banco($bar_code, $bank_code, $currency_code, $value, $free, $due_date) {}

/**
 * Gera a linha digitavel padrao arrecadacao.
 * @param string $printable_line Linha digitavel
 * @param string $bar_code Codigo de barras
 * @return void
 */
function sc_lin_digitavel_arrecadacao($printable_line, $bar_code) {}

/**
 * Gera a linha digitavel padrao bancario.
 * @param string $printable_line Linha digitavel
 * @param string $bar_code Codigo de barras
 * @return void
 */
function sc_lin_digitavel_banco($printable_line, $bar_code) {}

// =========================================================================
// Categoria: Filtro
// =========================================================================

/**
 * Disponibiliza o conteudo da clausula where gerada pelo formulario de filtro.
 * @return string
 */
function sc_where_filter() {}

// =========================================================================
// Categoria: Seguranca
// =========================================================================

/**
 * Protege ou libera a utilizacao das aplicacoes em geral.
 * @param string $application Nome da aplicacao
 * @param string $status Status (on/off)
 * @return void
 */
function sc_apl_status($application, $status) {}

/**
 * Recupera os grupos existentes no Active Directory (AD).
 * @return array
 */
function sc_ldap_groups() {}

/**
 * Macro principal para autenticacao LDAP.
 * @param string $server Servidor
 * @param int $version Versao
 * @param string $user Usuario
 * @param string $password Senha
 * @param string $dn DN
 * @param string $group Grupo
 * @param int $port Porta
 * @param string $library Biblioteca
 * @return bool
 */
function sc_ldap_login($server, $version, $user, $password, $dn, $group, $port, $library) {}

/**
 * Libera a conexao apos a utilizacao da macro sc_ldap_login.
 * @return void
 */
function sc_ldap_logout() {}

/**
 * Realiza buscas no LDAP.
 * @param string $filter Filtro (default: 'all')
 * @param array $attributes Atributos
 * @return array
 */
function sc_ldap_search($filter = 'all', $attributes = array()) {}

/**
 * Recupera usuarios do LDAP e seus atributos.
 * @param string $filter Filtro (default: 'all')
 * @param array $attributes Atributos
 * @return array
 */
function sc_ldap_users($filter = 'all', $attributes = array()) {}

/**
 * Apaga todas as alteracoes efetuadas pela macro sc_apl_conf.
 * @param string $application Nome da aplicacao (opcional)
 * @param string $property Propriedade (opcional)
 * @return void
 */
function sc_reset_apl_conf($application = '', $property = '') {}

/**
 * Deleta todas as variaveis de status de seguranca das aplicacoes.
 * @return void
 */
function sc_reset_apl_status() {}

/**
 * Restaura itens da estrutura do menu (retirados pela sc_menu_delete).
 * @return void
 */
function sc_reset_menu_delete() {}

/**
 * Habilita itens da estrutura do menu (desabilitados pela sc_menu_disable).
 * @return void
 */
function sc_reset_menu_disable() {}

/**
 * Protege o campo/variavel contra SQL injection.
 * @param string $field Campo ou variavel
 * @return string
 */
function sc_sql_injection($field) {}

/**
 * Desloga o usuario informado do sistema.
 * @param string $var_name Nome da variavel
 * @param string $var_content Conteudo da variavel
 * @param string $redirect_app Aplicacao de redirecionamento
 * @param string $target Target
 * @return void
 */
function sc_user_logout($var_name, $var_content, $redirect_app, $target) {}

// =========================================================================
// Categoria: Exibicao
// =========================================================================

/**
 * Exibe mensagens personalizadas em eventos Ajax.
 * @param string $message Mensagem
 * @param string $title Titulo (opcional)
 * @param string $visual_config Configuracao visual (opcional)
 * @param string $redirect_params Parametros de redirecionamento (opcional)
 * @return void
 */
function sc_ajax_message($message, $title = '', $visual_config = '', $redirect_params = '') {}

/**
 * Recarrega dinamicamente os dados em aplicacoes de consulta.
 * @return void
 */
function sc_ajax_refresh() {}

/**
 * Exibe uma tela de mensagem no estilo Javascript.
 * @param string $message Mensagem
 * @param array $array Array de opcoes (opcional)
 * @return void
 */
function sc_alert($message, $array = array()) {}

/**
 * Exibe ou nao os campos de um determinado bloco.
 * @param string $block_name Nome do bloco
 * @param string $status on/off
 * @return void
 */
function sc_block_display($block_name, $status) {}

/**
 * Controla dinamicamente a exibicao do captcha na aplicacao.
 * @param string $status on/off
 * @return void
 */
function sc_captcha_display($status) {}

/**
 * Manipula propriedades CSS dos campos e linhas da consulta.
 * @param string $attribute_name Nome do atributo
 * @param string $value Valor
 * @param string $field_name Nome do campo (opcional)
 * @return void
 */
function sc_change_css($attribute_name, $value, $field_name = '') {}

/**
 * Exibe uma tela de confirmacao Javascript.
 * @param string $message Mensagem
 * @return bool
 */
function sc_confirm($message) {}

/**
 * Adiciona um texto de ajuda nos links criados a partir de evento ajax onClick.
 * @param string $field_name Nome do campo
 * @param string $help_message Mensagem de ajuda
 * @param int $max_width Largura maxima (opcional)
 * @return void
 */
function sc_event_hint($field_name, $help_message, $max_width = 0) {}

/**
 * Altera a cor do texto de um determinado campo.
 * @param string $field Nome do campo
 * @param string $color Cor
 * @return void
 */
function sc_field_color($field, $color) {}

/**
 * Bloqueia a digitacao em determinados campos do formulario.
 * @param string $field_condition "Nome_Campo = True/False"
 * @param string $parameter Parametro (opcional)
 * @return void
 */
function sc_field_disabled($field_condition, $parameter = '') {}

/**
 * Bloqueia a digitacao em campos de cada linha nos formularios.
 * @param string $field_condition "Nome_Campo = True/False"
 * @param string $parameter Parametro (opcional)
 * @return void
 */
function sc_field_disabled_record($field_condition, $parameter = '') {}

/**
 * Exibe ou nao um determinado campo dinamicamente.
 * @param string $field Nome do campo
 * @param string $status on/off
 * @return void
 */
function sc_field_display($field, $status) {}

/**
 * Inibe campos da consulta na carga inicial.
 * @param string ...$fields Campos a inibir
 * @return void
 */
function sc_field_init_off(...$fields) {}

/**
 * Transforma em readonly um determinado campo do formulario.
 * @param string $field Nome do campo
 * @param string $status on/off
 * @return void
 */
function sc_field_readonly($field, $status) {}

/**
 * Personaliza dinamicamente o estilo CSS dos campos em Consultas.
 * @param string $field Nome do campo
 * @param string $background_color Cor de fundo
 * @param string $size Tamanho da fonte
 * @param string $color Cor do texto
 * @param string $family Familia da fonte
 * @param string $weight Peso da fonte
 * @return void
 */
function sc_field_style($field, $background_color, $size, $color, $family, $weight) {}

/**
 * Inibe a exibicao do rodape.
 * @return void
 */
function sc_foot_hide() {}

/**
 * Formata valores numericos.
 * @param string $field Campo numerico
 * @param string $group_sym Simbolo de agrupamento
 * @param string $dec_sym Simbolo decimal
 * @param int $dec_places Quantidade de decimais
 * @param string $fill_zeros Preencher zeros
 * @param string $neg_side Lado do negativo
 * @param string $currency_sym Simbolo monetario (opcional)
 * @param string $currency_side Lado do simbolo monetario (opcional)
 * @return string
 */
function sc_format_num($field, $group_sym, $dec_sym, $dec_places, $fill_zeros, $neg_side, $currency_sym = '', $currency_side = '') {}

/**
 * Formata valores numericos utilizando configuracoes regionais.
 * @param string $field Campo numerico
 * @param int $dec_places Quantidade de decimais
 * @param string $fill_zeros Preencher zeros
 * @param string $currency_sym Simbolo monetario (opcional)
 * @return string
 */
function sc_format_num_region($field, $dec_places, $fill_zeros, $currency_sym = '') {}

/**
 * Exibe ou nao o formulario dinamicamente.
 * @param string $status on/off
 * @return void
 */
function sc_form_show($status) {}

/**
 * Disponibiliza a regra da quebra que esta em execucao.
 * @return string
 */
function sc_get_groupby_rule() {}

/**
 * Inibe a exibicao de cabecalho.
 * @return void
 */
function sc_head_hide() {}

/**
 * Desativa Regras de Quebras.
 * @param string ...$groups Grupos a desativar
 * @return void
 */
function sc_hide_groupby_rule(...$groups) {}

/**
 * Seta o focus para um determinado campo do formulario.
 * @param string $field Nome do campo
 * @return void
 */
function sc_set_focus($field) {}

/**
 * Altera a visualizacao do texto do campo.
 * @param string $field Nome do campo
 * @param string $background_color Cor de fundo
 * @param string $size Tamanho da fonte
 * @param string $color Cor do texto
 * @param string $family Familia da fonte
 * @param string $weight Peso da fonte
 * @return void
 */
function sc_text_style($field, $background_color, $size, $color, $family, $weight) {}

/**
 * Ativa ou desativa o controle de mensagens de advertencia.
 * @param string $status on/off
 * @return void
 */
function sc_warning($status) {}

/**
 * Altera dinamicamente propriedades visuais de um widget.
 * @param array $array_options Array de opcoes
 * @return void
 */
function sc_widget_config($array_options) {}

/**
 * Retorna dados comparativos de um widget de indice.
 * @param string $data_name Nome do dado
 * @return array
 */
function sc_widget_data($data_name) {}

/**
 * Retorna o nome do widget em execucao.
 * @return string
 */
function sc_widget_name() {}

/**
 * Recupera o tipo de widget em execucao: index, link ou divider.
 * @return string
 */
function sc_widget_type() {}

// =========================================================================
// Categoria: Botoes
// =========================================================================

/**
 * Recupera o estado atual do botao AJAX da barra de acoes no momento do clique.
 * @return string
 */
function sc_actionbar_clicked_state() {}

/**
 * Desabilita os botoes criados pelo usuario na barra de acao.
 * @param string $button_name Nome do botao
 * @return void
 */
function sc_actionbar_disable($button_name) {}

/**
 * Habilita os botoes na barra de acao que foram desabilitados.
 * @param string $button_name Nome do botao
 * @return void
 */
function sc_actionbar_enable($button_name) {}

/**
 * Esconde dinamicamente o botao da barra de acao.
 * @param string $button_name Nome do botao
 * @return void
 */
function sc_actionbar_hide($button_name) {}

/**
 * Exibe os botoes da barra de acao que foram escondidos.
 * @param string $button_name Nome do botao
 * @return void
 */
function sc_actionbar_show($button_name) {}

/**
 * Define um novo estado para o botao do tipo ajax na barra de acoes.
 * @param string $button_name Nome do botao
 * @param string $state_name Nome do estado
 * @return void
 */
function sc_actionbar_state($button_name, $state_name) {}

/**
 * Retorna true quando o botao Copiar e selecionado em um formulario.
 * @return bool
 */
function sc_btn_copy() {}

/**
 * Retorna true quando o botao Excluir e selecionado em um formulario.
 * @return bool
 */
function sc_btn_delete() {}

/**
 * Habilita ou desabilita dinamicamente um botao da barra de ferramentas.
 * @param string $button_name Nome do botao
 * @param string $status Status
 * @return void
 */
function sc_btn_disabled($button_name, $status) {}

/**
 * Torna visivel ou nao um botao da barra de ferramentas.
 * @param string $button_name Nome do botao
 * @param string $status on/off
 * @return void
 */
function sc_btn_display($button_name, $status) {}

/**
 * Retorna true quando o botao Inserir e selecionado em um formulario.
 * @return bool
 */
function sc_btn_insert() {}

/**
 * Altera dinamicamente a label dos botoes.
 * @param string $button_name Nome do botao
 * @param string $new_label Nova label
 * @return void
 */
function sc_btn_label($button_name, $new_label) {}

/**
 * Retorna true quando o botao Novo e selecionado em um formulario.
 * @return bool
 */
function sc_btn_new() {}

/**
 * Retorna true quando o botao Alterar e selecionado em um formulario.
 * @return bool
 */
function sc_btn_update() {}

// =========================================================================
// Categoria: PDF
// =========================================================================

/**
 * Altera o nome dos arquivos exportados pela consulta.
 * @param string $export_type Tipo de exportacao
 * @param string $file_name Nome do arquivo
 * @return void
 */
function sc_set_export_name($export_type, $file_name) {}

// =========================================================================
// Categoria: Menu
// =========================================================================

/**
 * Adiciona um item ao menu dinamicamente.
 * @param string $menu_name Nome do menu
 * @param string $item_id Id do item
 * @param string $parent_id Id do pai
 * @param string $label Label do item
 * @param string $application Aplicacao
 * @param string $parameters Parametros (opcional)
 * @param string $icon Icone (opcional)
 * @param string $hint Hint (opcional)
 * @param string $target Target (opcional)
 * @param string $mega_menu Mega menu (opcional)
 * @return void
 */
function sc_appmenu_add_item($menu_name, $item_id, $parent_id, $label, $application, $parameters = '', $icon = '', $hint = '', $target = '', $mega_menu = '') {}

/**
 * Cria um menu de forma dinamica.
 * @param string $menu_name Nome do menu
 * @return void
 */
function sc_appmenu_create($menu_name) {}

/**
 * Verifica se existe um item do menu.
 * @param string $menu_name Nome do menu
 * @param string $item_id Id do item
 * @return bool
 */
function sc_appmenu_exist_item($menu_name, $item_id) {}

/**
 * Remove dinamicamente um item do menu.
 * @param string $menu_name Nome do menu
 * @param string $item_id Id do item
 * @return void
 */
function sc_appmenu_remove_item($menu_name, $item_id) {}

/**
 * Limpa o array para montagem dinamica de um menu.
 * @param string $menu_name Nome do menu
 * @return void
 */
function sc_appmenu_reset($menu_name) {}

/**
 * Atualiza um item do menu.
 * @param string $menu_name Nome do menu
 * @param string $item_id Id do item
 * @param string $parent_id Id do pai
 * @param string $label Label do item
 * @param string $application Aplicacao
 * @param string $parameters Parametros (opcional)
 * @param string $icon Icone (opcional)
 * @param string $hint Hint (opcional)
 * @param string $target Target (opcional)
 * @return void
 */
function sc_appmenu_update_item($menu_name, $item_id, $parent_id, $label, $application, $parameters = '', $icon = '', $hint = '', $target = '') {}

/**
 * Desabilita botoes do Menu.
 * @param string $button_id Id do botao
 * @param string $status on/off
 * @return void
 */
function sc_btn_disable($button_id, $status) {}

/**
 * Remove itens da estrutura do menu.
 * @param string $item_id Id do item
 * @return void
 */
function sc_menu_delete($item_id) {}

/**
 * Desabilita itens da estrutura do menu.
 * @param string $item_id Id do item
 * @return void
 */
function sc_menu_disable($item_id) {}

/**
 * Forca a criacao do menu para dispositivos moveis.
 * @param bool $force Forcar mobile
 * @return void
 */
function sc_menu_force_mobile($force) {}

/**
 * Identifica qual item do menu foi clicado.
 * @return string
 */
function sc_menu_item() {}

/**
 * Identifica o nome da aplicacao selecionada nos itens do menu.
 * @return string
 */
function sc_script_name() {}

// =========================================================================
// Categoria: Integracoes / APIs
// =========================================================================

/**
 * Download de arquivos utilizando as APIs de armazenamento.
 * @param string $profile Perfil
 * @param array $settings Configuracoes
 * @param string $file Arquivo
 * @param string $destination Destino
 * @return void
 */
function sc_api_download($profile, $settings, $file, $destination) {}

/**
 * Gera o token_code para Google Cloud.
 * @param string $app_name Nome da aplicacao
 * @param string $json_oauth JSON OAuth
 * @param string $auth_code Codigo de autenticacao
 * @return string
 */
function sc_api_gc_get_obj($app_name, $json_oauth, $auth_code) {}

/**
 * Gera uma URL para autenticacao do usuario da conta google.
 * @param string $app_name Nome da aplicacao
 * @param string $json_oauth JSON OAuth
 * @return string
 */
function sc_api_gc_get_url($app_name, $json_oauth) {}

/**
 * Deleta arquivos armazenados em servicos de armazenamento em nuvem.
 * @param string $profile Perfil
 * @param string $file Arquivo
 * @param string $parents Diretorio pai (opcional)
 * @return void
 */
function sc_api_storage_delete($profile, $file, $parents = '') {}

/**
 * Upload de arquivos utilizando as APIs de Storage.
 * @param string $profile Perfil
 * @param array $settings Configuracoes
 * @param string $file Arquivo
 * @param string $parents Diretorio pai (opcional)
 * @return void
 */
function sc_api_upload($profile, $settings, $file, $parents = '') {}

/**
 * Permite utilizar as APIs integradas ao Scriptcase.
 * @param string $profile Perfil
 * @param array $arr_settings Array de configuracoes
 * @return array
 */
function sc_call_api($profile, $arr_settings) {}

/**
 * Envio dinamico de e-mails integrados com Mandrill e Amazon SES.
 * @param array $arr_settings Array de configuracoes
 * @return array
 */
function sc_send_mail_api($arr_settings) {}

/**
 * Envio dinamico de mensagem SMS para as APIs do Scriptcase.
 * @param array $arr_settings Array de configuracoes
 * @return array
 */
function sc_send_sms($arr_settings) {}
