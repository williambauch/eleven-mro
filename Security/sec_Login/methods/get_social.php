<?php

function get_social($resource, $user_id, $email, $name, $picture)
{
    if(empty($user_id)) return;

    	$sql = "SELECT 
    				priv_admin,
    				active, 
    				\"name\", 
    				email, 
    				mfa, 
    				pswd_last_updated,
    				login,
    				phone,
    				picture,
    				mfa_last_updated
    			FROM public.sec_users 
    				WHERE email = ". sc_sql_injection($email).
    		" OR login = (
    		SELECT
    			public.sec_users_social.login
    		FROM
    			public.sec_users_social
    		WHERE
    			public.sec_users_social.resource = '". $resource ."'
    			AND public.sec_users_social.resource_id = ". sc_sql_injection($user_id) .")";
    	

    	sc_lookup(rs, $sql);

    	if({rs} === false || count({rs}) == 0)
    	{
    		link_user($resource, $user_id, $email, $name, $picture);
    		sc_log_add('login Fail', {lang_login_fail} . $resource. "_".$user_id);
    		
    		sc_error_message({lang_error_login});
    	}
    	else if({rs[0][1]} == 'Y')
    	{
    		[usr_login]		= {rs[0][6]};
    		[usr_priv_admin] 	= ({rs[0][0]} == 'Y') ? TRUE : FALSE;
    		[usr_name]		= {rs[0][2]};
    		[usr_email]		= {rs[0][3]};
    		[usr_phone]		= {rs[0][7]};
    		[remember_me] = {remember_me};
        	//[usr_picture] = $picture;
        
    		$usr_grp = array();

    		sc_lookup(rs_groups, "SELECT group_id FROM public.sec_users_groups WHERE login =". sc_sql_injection([usr_login]) );

    		if(isset({rs_groups[0][0]})){
    			foreach({rs_groups} as $r){
    				$usr_grp[] = $r[0];
    			}
    		}

    		[usr_groups] = $usr_grp;
    	
    		if(in_array([sett_group_administrator], [usr_groups])){
    			[usr_priv_admin] = TRUE;
    		}
    	
    	
    	
    		if(!empty($picture)){
    			$path_img = $_SESSION['scriptcase']['sec_Login']['glo_nm_path_imag_temp'] .'/sc_img_'. [usr_login] . hash("md5",date('YmdHis')) . '.png';
    			$picture_data = file_get_contents($picture);
    			file_put_contents($this->Ini->root . $path_img, $picture_data);
    			[usr_picture] = $path_img;
    			update_login_img([usr_login], $picture_data);
    		}
    		if(empty([usr_picture]) && !empty({rs[0][8]})){
    			if(substr({rs[0][8]}, 0, 5) != 'http:' && substr({rs[0][8]}, 0, 6) != 'https:'){
    				$path_img = $_SESSION['scriptcase']['sec_Login']['glo_nm_path_imag_temp'] .'/sc_img_'. [usr_login] . hash("md5",date('YmdHis')) . '.png';
    				file_put_contents($this->Ini->root . $path_img, {rs[0][8]});
    				[usr_picture] = $path_img;
    			}
    			else{
    				[usr_picture] = {rs[0][8]};
    			}
    		}
    	
    	
    	
    	
    //mfa
    		if( isset([sett_enable_2fa]) && [sett_enable_2fa] == 'Y' ){
    			if(!empty({rs[0][4]})){


    			$mfa_key = ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '').($_SERVER['REMOTE_ADDR'] ?? '' ). ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['SERVER_NAME'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? '');
    			$mfa_key = '_'.hash("md5",$mfa_key);


    				$diff = 0;
    				if( [sett_mfa_last_updated] != 0 && !empty({rs[0][9]}) ){
    					$diff = sc_date_dif(
    						date("Y-m-d", strtotime({rs[0][9]} . " +".[sett_mfa_last_updated]. "days") ),
    						"aaaa-mm-dd",
    						date("Y-m-d"),
    						"aaaa-mm-dd");
    				}
    				$diff_cookie = 0;
    				if(isset($_COOKIE[ $mfa_key ]) && !empty($_COOKIE[ $mfa_key ])){
    					$diff_cookie = sc_date_dif(
    						date("Y-m-d", strtotime(sc_decode($_COOKIE[ $mfa_key ]) . " +".[sett_mfa_last_updated]. "days") ),
    						"aaaa-mm-dd",
    						date("Y-m-d"),
    						"aaaa-mm-dd");
    				}
    				if($diff <= 0 || $diff_cookie <= 0){
    					sc_redir('sec_control_2fa');
    				}
    			}
    			else if([sett_enable_2fa_mode] == 'all'){
    				sc_apl_status('sec_add_2fa', 'on');
    				sc_redir('sec_add_2fa',redir_menu=1);
    			}

    		}
    // END mfa    
        	remember_me_validate();
    		sc_validate_success();
    	}
    	else
    	{
    		sc_error_message({lang_error_not_active});
    		sc_error_exit();
    	}
}
