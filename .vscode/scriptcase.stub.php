<?php
/**
 * @ScriptCase Stubs File
 * Este arquivo define funções do ScriptCase para o VS Code
 */

if (!defined('SCRIPTCASE')) {
    define('SCRIPTCASE', true);
}

/**
 * Mensagem de erro que interrompe o processamento
 * @param string $message
 * @return void
 */
function sc_error_message($message) {}

/**
 * Mensagem de erro que não interrompe o processamento
 * @param string $message
 * @return void
 */
function sc_error_continue($message) {}

/**
 * Mensagem de sucesso
 * @param string $message
 * @return void
 */
function sc_message($message) {}

/**
 * Executa uma consulta SQL e armazena o resultado
 * @param mixed &$result
 * @param string $sql
 * @return bool
 */
function sc_lookup(&$result, $sql) {}

/**
 * Executa uma SQL de atualização
 * @param string $sql
 * @return bool
 */
function sc_exec_sql($sql) {}

/**
 * Adiciona um log no sistema
 * @param string $action
 * @param string $message
 * @return void
 */
function sc_log_add($action, $message) {}

/**
 * Redireciona para outra aplicação
 * @param string $app
 * @param array $params
 * @return void
 */
function sc_redir($app, $params = array()) {}

/**
 * Redireciona para outra aplicação (alias)
 * @param string $app
 * @param array $params
 * @return void
 */
function sc_goto($app, $params = array()) {}

/**
 * Exibe uma mensagem de alerta
 * @param string $message
 * @return void
 */
function sc_alert($message) {}

/**
 * Exibe uma mensagem de confirmação
 * @param string $message
 * @return bool
 */
function sc_confirm($message) {}

/**
 * Altera a exibição de um campo
 * @param string $field
 * @param bool $status
 * @return void
 */
function sc_field_display($field, $status) {}

/**
 * Altera o status de edição de um campo
 * @param string $field
 * @param bool $status
 * @return void
 */
function sc_field_readonly($field, $status) {}

/**
 * Obtém/define a cor de um campo
 * @param string $field
 * @param string $color
 * @return void
 */
function sc_field_color($field, $color) {}

/**
 * Define o estilo de um campo
 * @param string $field
 * @param string $style
 * @return void
 */
function sc_field_style($field, $style) {}

/**
 * Executa uma consulta para popular select/combo
 * @param mixed &$result
 * @param string $sql
 * @return bool
 */
function sc_select(&$result, $sql) {}

/**
 * Reseta a conexão com o banco
 * @return void
 */
function sc_reset_connection() {}

/**
 * Valida um campo
 * @param string $field
 * @param string $rule
 * @return bool
 */
function sc_validate($field, $rule) {}

/**
 * Concatena strings
 * @param string ...$strings
 * @return string
 */
function sc_concat(...$strings) {}

/**
 * Commit de transação
 * @return void
 */
function sc_commit_trans() {}

/**
 * Rollback de transação
 * @return void
 */
function sc_rollback_trans() {}

/**
 * Para o cronômetro
 * @return void
 */
function sc_stop_watch() {}

/**
 * Sai da aplicação
 * @return void
 */
function sc_error_exit() {}

/**
 * Exibe warning
 * @param string $message
 * @return void
 */
function sc_warning($message) {}

/**
 * Obtém o ID inserido
 * @return mixed
 */
function sc_insert_id() {}

/**
 * Verifica se campo foi alterado
 * @param string $field
 * @return bool
 */
function sc_field_changed($field) {}

/**
 * Obtém novo valor do campo
 * @param string $field
 * @return mixed
 */
function sc_field_new_value($field) {}

/**
 * Obtém valor antigo do campo
 * @param string $field
 * @return mixed
 */
function sc_field_old_value($field) {}

/**
 * Obtém where atual
 * @return string
 */
function sc_where_current() {}

/**
 * Obtém where original
 * @return string
 */
function sc_where_orig() {}

// Variáveis globais comuns do ScriptCase
global $interno, $nm_apl_dependente, $nm_form_submit, $nm_opc_form_php, $nm_call_php, $script_case_init;

// Definições para evitar erros de sintaxe com variáveis do ScriptCase
// Estas são apenas para o IntelliSense, não afetam a execução real
if (false) {
    // Simulação de variáveis de campo do ScriptCase
    $GLOBALS['sc_field_vars'] = array();
    
    // Função auxiliar para simular acesso a campos
    function __sc_field($name) {
        return $GLOBALS['sc_field_vars'][$name] ?? null;
    }
    
    // Função auxiliar para simular variáveis globais
    function __sc_global($name) {
        return $GLOBALS['sc_global_vars'][$name] ?? null;
    }
}