<?php

function remember_me_validate()
{
    if(!isset([sett_remember_me]) || [sett_remember_me] != 'Y'){
    	return;
    }
    if({remember_me} == 1){

        $chars  = 'abcdefghijklmnopqrstuvxywz';
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVXYWZ';
        $chars .= '0123456789!@$*.,;:';
        $max = strlen($chars)-1;
        $code = "cookie_";
        for($i=0; $i < 25; $i++)
        {
            $code .= $chars[mt_rand(0, $max)];
        }
        
        $slogin = sc_sql_injection({login});

        sc_exec_sql("UPDATE public.sec_users SET activation_code = ". sc_sql_injection($code) . " WHERE login = ". $slogin);

        $usr_data = array(
            'login' => {login},
            'code' => $code,
        );
        $remember_me_expiry_cookie = [sett_cookie_expiration_time];
        setcookie("usr_data", sc_encode(serialize($usr_data)),time()+60*60*24* $remember_me_expiry_cookie, '/');
    }
}
