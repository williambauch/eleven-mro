<?php
function enviar_email($mail_to, $mail_message, $mail_subject) {

    // ---------- VALIDAÇÕES INICIAIS ----------
    if (empty(trim($mail_to)) || empty(trim($mail_message)) || empty(trim($mail_subject))) {
        // Sai da função se algum campo essencial estiver vazio
        return;
    }
	
	    $sql = "select smtp_server, smtp_user, smtp_port, smtp_pass from parametros";
		sc_lookup_field(conn1, $sql,"conn_mysql");
	
	  if (empty({conn1})) {
       // sc_error_message("Não foi possível obter os parâmetros de e-mail (SMTP).");
        return;
    }

		$mail_smtp_server    = {conn1[0]['smtp_server']};          // Nome ou IP do servidor SMTP
		$mail_smtp_user      = {conn1[0]['smtp_user']};           // Nome de usuário SMTP
		$mail_smtp_pass      = {conn1[0]['smtp_pass']};           // Senha SMTP
		$mail_port           = {conn1[0]['smtp_port']};           // Porta do servidor


		$mail_from           = $mail_smtp_user;      // E-mail de origem
		$mail_format         = 'H';                  // Formato da mensagem: (T)exto ou (H)tml
		$mail_copies         = '';                   // Cópias
		$mail_tp_copies      = '';                   // Tipo de cópias: BCC ou CCC
		$mail_tp_connection  = 'S';                  // Segurança da conexão (S)im ou (N)ão


// Envia o e-mail
		sc_mail_send(
			$mail_smtp_server,
			$mail_smtp_user,
			$mail_smtp_pass,
			$mail_from,
			$mail_to,
			$mail_subject,
			$mail_message,
			$mail_format,
			$mail_copies,
			$mail_tp_copies,
			$mail_port,
			$mail_tp_connection
		);
	

}

function enviar_email_new($mail_to, $mail_message, $mail_subject) {
	
	
$conn_sql = "select smtp_server, smtp_user, smtp_port, smtp_pass from parametros";

sc_lookup(conn, $conn_sql, "conn_mysql");

$mail_smtp_server = {conn[0][0]};
$mail_smtp_user = {conn[0][1]};
$mail_smtp_pass = {conn[0][2]};
$mail_port = {conn[0][3]};	

$mail_from          = $mail_smtp_user; 
$mail_format        = 'H';   // H = HTML, T = Texto
$mail_tp_connection = 'S';   // S = SSL
$mail_copies        = '';
$mail_tp_copies     = '';    // BCC / CCC

sc_mail_send(
    $mail_smtp_server,
    $mail_smtp_user,
    $mail_smtp_pass,
    $mail_from,
    $mail_to,
    $mail_subject,
    $mail_message,
    $mail_format,
    $mail_copies,
    $mail_tp_copies,
    $mail_port,
    $mail_tp_connection
);

}

?>