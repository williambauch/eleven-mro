<?php

function add_user_to_group($login)
{
    $group_default = [sett_group_default];

    	
    	$sql = "INSERT INTO
    				public.sec_users_groups( login, group_id )
    			VALUES 
    				(". sc_sql_injection($login). ", ".
    					sc_sql_injection($group_default) . ")";

    	sc_exec_sql($sql);
}
