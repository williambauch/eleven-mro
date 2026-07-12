<?php

function get_settings()
{
    /*@@settings@@*/
        
    $check_sql = "SELECT set_name, set_value  FROM public.sec_settings";
       
    sc_lookup(rs, $check_sql);

    if (isset({rs[0][0]}))     // Row found
    {
        foreach({rs} as $f ){
            $_SESSION[ 'sett_'. $f[0] ] = $f[1];
        }
    }
}
