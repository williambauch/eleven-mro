<?php

function sc_2fa_send_sms($phone)
{
    $__code = sc_generate_code();


    sc_exec_sql("UPDATE public.sec_users SET mfa =". sc_sql_injection('sms@@'.$phone.'@@'.$__code."@@".date("YmdHis")) ." WHERE login = ". sc_sql_injection([usr_login]) );

    sc_send_sms(array(
        'profile' => [sett_enable_2fa_api],
        'message' => 
    	[
    		'to' => $phone,
    		'message' => sprintf({lang_sec_2fa_sms}, $__code)
    	],
    ));


    $_phone= (string)$phone;
    $_phone = $_phone[0] . $_phone[1] . $_phone[2] . str_repeat("*", strlen($_phone) -4) . $_phone[strlen($_phone) -1];

    $msg = sprintf({lang_sec_2fa_send_to} , $_phone);

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
