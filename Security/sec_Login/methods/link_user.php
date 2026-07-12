<?php

function link_user($resource, $usr_id, $email, $name, $picture)
{
    if([sett_new_users] != 'Y'){
    		return;
    	}
    	$group_default = (int)[sett_group_default];

    	$sql = "INSERT INTO
    				public.sec_users( login, \"name\", pswd, email, active )
    			VALUES 
    				(". sc_sql_injection($usr_id). ", ".
    					sc_sql_injection($name) . ", ".
    					sc_sql_injection(hash("md5",date("YmdHis"))) . ", ".
    					sc_sql_injection($email) .
    					", 'Y'".
    				")";
    			
    	
    	sc_exec_sql($sql);

    	
    	if(!empty($picture)){
    		$picture_data = file_get_contents($picture); 
    		$picture = $_SESSION['scriptcase']['sec_Login']['glo_nm_path_imag_temp'] .'/sc_img_'. [usr_login] . hash("md5",date('YmdHis')) . '.png';
    		file_put_contents($this->Ini->root . $picture, $picture_data);
    		update_login_img($usr_id, $picture_data);
    	}
    	
    	$sql = "INSERT INTO
    				public.sec_users_social( public.sec_users_social.login, public.sec_users_social.resource, public.sec_users_social.resource_id )
    			VALUES 
    				(". sc_sql_injection($usr_id). ", ".
    					sc_sql_injection($resource) . ", ".
    					sc_sql_injection($usr_id) . ")";
    			
    	
    	sc_exec_sql($sql);
    									 
    	
    	$sql = "INSERT INTO
    				public.sec_users_groups( login, group_id )
    			VALUES 
    				(". sc_sql_injection($usr_id). ", ".
    					sc_sql_injection($group_default) . ")";
    			
    	
    	sc_exec_sql($sql);

    	
    	$usr_login			= $usr_id;
    	$usr_priv_admin 	= FALSE;
    	$usr_email			= $email;
    	$usr_picture		= $picture;
    	sc_set_global($usr_login);
    	sc_set_global($usr_priv_admin);
    	sc_set_global($usr_email);
    	sc_set_global($usr_picture);
    	sc_validate_success();
}
