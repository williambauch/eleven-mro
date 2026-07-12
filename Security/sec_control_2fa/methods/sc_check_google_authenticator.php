<?php

function sc_check_google_authenticator($code, $secret)
{
    list($client, $arr_2fa) = sc_call_api([sett_enable_2fa_api]);

    if ($client->checkCode($secret, $code, 45)) {
      return true;
    } else {
      return false;
    }
}
