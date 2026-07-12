<?php

function sc_2fa_send_email()
{
    $_code_tmp = sc_generate_code();

    [code_check] = $_code_tmp;
    [code_created_at] = date("YmdHis");

    sc_send_mail_api(array(
    	'profile' => [sett_enable_2fa_api],
        'message' => [
    			'html'          => sprintf({lang_sec_2fa_mail_msg},$_code_tmp),
    			'text'          => '',
    			'to'            => [usr_email],
    			'subject'       => sprintf({lang_sec_2fa_mail_subject},$_code_tmp)
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
