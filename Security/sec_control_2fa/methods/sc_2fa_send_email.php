<?php

function sc_2fa_send_email()
{
    $__code = sc_generate_code();


    sc_exec_sql("UPDATE public.sec_users SET mfa =". sc_sql_injection('email@@'.$__code."@@".date("YmdHis")) ." WHERE login = ". sc_sql_injection([usr_login]) );


    sc_send_mail_api(array(
    	'profile' => [sett_enable_2fa_api],
        'message' => [
    			'html'          => sprintf({lang_sec_2fa_mail_msg},$__code),
    			'text'          => '',
    			'to'            => [usr_email],
    			//'attachments' => array('/var/www/arquivo.txt','/var/www/arquivo2.txt'),
    			'subject'       => sprintf({lang_sec_2fa_mail_subject},$__code)
    		]
    ));

    $email = explode('@',[usr_email]);
    $email_1 = $email[0][0] . str_repeat("*", strlen($email[0]) -1);
    $email_2 = explode('.', $email[1]);
    $email_2[0] = $email_2[0][0] . str_repeat("*", strlen($email_2[0]) -1);
    $email_2 = implode('.',$email_2);
    $email =  $email_1 . '@' . $email_2;

    $msg = sprintf({lang_sec_2fa_send_to} , $email);

    sc_alert($msg, array(
                        'title' => {lang_sec_2fa_success_title},
                        'type' => 'success',
                        'timer' => '10000',
                        'showConfirmButton' => false,
                        'position' => 'center',
                        'toast' => true
                        )
                  );
}
