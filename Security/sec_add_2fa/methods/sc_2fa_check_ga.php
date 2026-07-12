<?php

function sc_2fa_check_ga($code)
{
    list($client, $arr_2fa) = sc_call_api([sett_enable_2fa_api]);

    if ($client->checkCode([secret], $code, 60)){
        $sql = "UPDATE public.sec_users SET mfa =". sc_sql_injection('ga@@'.[secret]) ." WHERE login = ". sc_sql_injection([usr_login]);
        sc_exec_sql($sql);
        return true;
    }
    return false;
}
