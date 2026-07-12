<?php

function send_mail_message($param_message)
{
    /**
     * Send a simple email
     */
    $rs = sc_send_mail_api(array_merge([sett_smtp],array(
        'message' => [
    			'html'          => $param_message,
    			'text'          => '',
    			'to'            => [usr_email],
    			//'attachments' => array('/var/www/arquivo.txt','/var/www/arquivo2.txt'),
    			'subject'       => {lang_subject_mail}
    		]
    )));

    if($rs){
    	sc_alert({lang_mail_sended_ok} );
    }
    else
    {
    	sc_error_message({sc_mail_erro});
    }
}
