<?php

function remember_me_validate()
{
    if(isset([remember_me]) && [remember_me] == 1){

        $chars  = 'abcdefghijklmnopqrstuvxywz';
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVXYWZ';
        $chars .= '0123456789!@$*.,;:';
        $max = strlen($chars)-1;
        $code = "cookie_";
        for($i=0; $i < 25; $i++)
        {
            $code .= substr($chars, mt_rand(0, $max), 1);
        }
        
        $slogin = sc_sql_injection([usr_login]);

        sc_exec_sql("UPDATE public.sec_users SET activation_code = ". sc_sql_injection($code) . " WHERE login = ". $slogin);

        $usr_data = array(
            'login' => [usr_login],
            'code' => $code,
        );

        $remember_me_expiry_cookie = 30;
        setcookie("usr_data", sc_encode(serialize($usr_data)),time()+60*60*24* $remember_me_expiry_cookie, '/');}
}
