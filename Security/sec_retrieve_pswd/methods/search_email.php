<?php

function search_email($param_login)
{
    $param_login = sc_sql_injection($param_login);
    switch( [sett_login_mode] ){
    	default:
    	case 'username':
    		$str_login_validate = "login =". $param_login;
    		break;
    	case 'email':
    		$str_login_validate = "email =". $param_login;
    		break;
    	case 'both':
    		$str_login_validate = "(login =". $param_login . " OR email =".$param_login . ")";
    		break;
    }

    $sql = "SELECT email, login FROM public.sec_users WHERE ". $str_login_validate;

    sc_lookup(rs, $sql);

    if({rs} === FALSE || count({rs}) == 0)
    {
    	sc_error_message({lang_error_login_not_exist});
    	sc_error_exit();
    }
    else
    {
    	return array({rs[0][0]}, {rs[0][1]});
    }
}
