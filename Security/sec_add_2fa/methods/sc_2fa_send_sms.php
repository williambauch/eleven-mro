<?php

function sc_2fa_send_sms($phone)
{
    $_code_tmp = sc_generate_code();

    [code_check] = $_code_tmp;
    [code_created_at] = date("YmdHis");

    sc_send_sms(array(
        'profile' => [sett_enable_2fa_api],
        'message' => 
    	[
    		'to' => '+'.$phone,
    		'message' => sprintf({lang_sec_2fa_sms}, $_code_tmp)
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
