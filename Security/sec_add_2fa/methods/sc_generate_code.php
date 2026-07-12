<?php

function sc_generate_code()
{
    $__code = '';
    for($i=0; $i < 6; $i++){
        $__code .= random_int(0, 9);
    }

    return $__code;
}
