<?php

function send_mail_to_new_user()
{
    $act_code = act_code();
    $message = " <a href='http://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']. "?a=" . $act_code ."'> http://".$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']. "?a=" . $act_code ." </a>";

    // Update record
    $update_sql = 'UPDATE public.sec_users'
        . ' SET activation_code = ' . sc_sql_injection($act_code)
        . ' WHERE login = '. sc_sql_injection({login});
    sc_exec_sql($update_sql);



    /**
     * Send a simple email
     */
    sc_send_mail_api(array_merge([sett_smtp],array(
        'message' => [
    			'html'          => sprintf({lang_send_actcode_newuser}, $message),
    			'text'          => '',
    			'to'            => {email},
    			//'attachments' => array('/var/www/arquivo.txt','/var/www/arquivo2.txt'),
    			'subject'       => {lang_subject_new_user}
    		]
    )));

    sc_log_add("Send active code", {lang_sended_active_code});
}
