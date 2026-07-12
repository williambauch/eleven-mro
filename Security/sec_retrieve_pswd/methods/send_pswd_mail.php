<?php

function send_pswd_mail()
{
    sc_lookup(rs, "SELECT
    		    pswd
    		FROM 
    		    public.sec_users
    		WHERE
    	            login = '". [usr_login] ."'");

    send_mail_message({lang_send_pswd} . " ". {rs[0][0]}) ;
}
