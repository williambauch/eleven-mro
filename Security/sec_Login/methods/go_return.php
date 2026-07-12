<?php

function go_return()
{
    if(!empty([google_error_code]))
    {
    	sc_error_message(urlencode([google_error_msg]));
    }
    else
    {
    	get_social("google", [google_user], [google_email], [google_name], [google_photo]);
    }
}
