<?php

function remember_me_init()
{
    if(isset($_COOKIE['usr_data']) && !empty($_COOKIE['usr_data'])){

        $usr_data = unserialize(sc_decode($_COOKIE['usr_data']));
        
        if(substr($usr_data['code'], 0, 7) == 'cookie_'){
            


            $sql = "SELECT 
                        priv_admin,
                        active, 
                        \"name\", 
                        email 
                    FROM public.sec_users 
                    WHERE login = ". sc_sql_injection($usr_data['login']) ."
                    AND activation_code = ".sc_sql_injection($usr_data['code']);
            sc_lookup(rs, $sql);

            if(count({rs}) != 0 && {rs[0][1]} == 'Y')
            {
                [usr_login]		= $usr_data['login'];
                [usr_priv_admin] 	= ({rs[0][0]} == 'Y') ? TRUE : FALSE;
                [usr_name]		= {rs[0][2]};
                [usr_email]		= {rs[0][3]};
                
                sc_validate_success();
            }
        }
    }
}
