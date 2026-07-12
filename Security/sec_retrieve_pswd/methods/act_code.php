<?php

function act_code()
{
    $chars  = 'abcdefghijklmnopqrstuvxywz';
    $chars .= 'ABCDEFGHIJKLMNOPQRSTUVXYWZ';
    $chars .= '0123456789';
    $max = strlen($chars)-1;
    $act_code = "";
    for($i=0; $i < 255; $i++)
    {
    	$act_code .= $chars[mt_rand(0, $max)];
    }

    return $act_code;
}
