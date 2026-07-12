<?php

function send_act()
{
    $act_code = act_code();
    $act_code = substr($act_code, 0, 32);

    $sql = "UPDATE 
    		public.sec_users
    	    SET
    	    	activation_code = '". $act_code ."'
    	    WHERE
    	        login = '". [usr_login] ."'";

    sc_exec_sql($sql);

    send_mail_message({lang_send_act_code}
                       . "<br/> <a href='http://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']. "?act_code=" . $act_code ."'> http://".$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']. "?act_code=" . $act_code ." </a>");
}
