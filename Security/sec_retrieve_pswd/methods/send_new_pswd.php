<?php

function send_new_pswd()
{
    $pswd = act_code();
    $sql  = "UPDATE public.sec_users SET pswd = '". hash("md5",$pswd) ."' WHERE login = '". [usr_login] ."'";
    sc_exec_sql($sql);

    send_mail_message({lang_send_new_pswd} . $pswd);
}
