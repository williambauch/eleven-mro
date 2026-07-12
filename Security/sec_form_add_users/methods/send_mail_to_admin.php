<?php

function send_mail_to_admin()
{
    $sql = "SELECT
    		email
    	FROM
    		public.sec_users
    	WHERE
    		priv_admin = 'Y'";
    		
    sc_lookup(rs,$sql);

    $emails_admin = array();
    if({rs} !== FALSE && count({rs}) != 0)
    {
    	foreach({rs} as $value)
    		$emails_admin[] = $value[0];
    }


    /**
     * Send a simple email
     */
    sc_send_mail_api(array_merge([sett_smtp],array(
        'message' => [
    			'html'          => sprintf({lang_new_user_sign_in}, {name}, {email}, {email}),
    			'text'          => '',
    			'to'            => implode(';', $emails_admin),
    			//'attachments' => array('/var/www/arquivo.txt','/var/www/arquivo2.txt'),
    			'subject'       => {lang_subject_mail_new_user}
    		]
    )));


    sc_log_add("New user", {lang_send_mail_admin});
}
