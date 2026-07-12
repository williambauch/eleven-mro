<?php

function sc_2fa_ga_generate_img()
{
    list($client, $arr_2fa) = sc_call_api([sett_enable_2fa_api]);

    [secret] = $client->generateSecret();
    {google_auth_img} = "<img src='". $client->getURL([usr_login], $arr_2fa['domain'], [secret]) . "' />";
}
