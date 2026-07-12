<?php

function fb_return()
{
    if(!empty([facebook_error_code]))
    {
    	sc_error_message(urlencode([facebook_error_msg]));
    }
    else
    {
    	get_social("facebook", [facebook_user], [facebook_email], [facebook_name]);
    }
}
