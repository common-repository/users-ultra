<?php
class XooUserRegister {
	
	

	function __construct() 
	{			
		add_action( 'init', array($this, 'uultra_handle_hooks_actions') );			
		add_action( 'init', array($this, 'uultra_handle_post') );			
		add_action( 'user_register', array($this, 'uultra_registration_activity'),15,1 );	

	}
	
	//this function handles the registration hook - 10-24-2014	
	function uultra_handle_hooks_actions ()	
	{
		if (function_exists('uultra_registration_hook')) 
		{		
			add_action( 'user_register', 'uultra_registration_hook' );	
		
		}
		
		if (function_exists('uultra_after_login_hook')) 
		{		
			add_action( 'wp_login', 'uultra_after_login_hook' , 100,2);			
		}			
		
				
	}
	
	function uultra_registration_activity($user_id)
	{
		global $xoouserultra;
		
	     // Add to activity wall.		
		$xoouserultra->wall->wall_save_activity($user_id, 'newuser')	;
	
	}

	function uultra_handle_post () 
	{		
		
		/*Form is fired*/	    
		if (isset($_POST['xoouserultra-register-form'])) {
			
			/* Prepare array of fields */
			$this->uultra_prepare_request( $_POST );
       			
			/* Validate, get errors, etc before we create account */
			$this->uultra_handle_errors();
            			
			/* Create account */
			$this->uultra_create_account();
				
		}
		
		/*Upgrading Is Fired*/	    
		if (isset($_POST['xoouserultra-upgrading-account-confirm'])) {			
					
			/* Create account */
			$this->uultra_upgrade_account();

		}
		
	}
		
	/*Prepare user meta*/
	function uultra_prepare_request ($array ) 
	{
		foreach($array as $k => $v) 
		{
			//$this->usermeta_secondary[$k] = $v; //this has been added for passwor
			
			if ($k == 'usersultra-register' || $k == 'user_pass_confirm' || $k == 'user_pass' || $k == 'xoouserultra-register-form') continue;
			
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function uultra_handle_errors() 
	{
	    global $xoouserultra, $uupro20_recaptcha;
		
		//check if retype-password
		$email_retype = $xoouserultra->get_option("set_email_retype");
        
        //CHECK NONCE
        if(!isset($_POST['uultra_csrf_token'])){
            
            $this->errors[] = __('<strong>ERROR:</strong> Nonce not received.','users-ultra');    
            
        }else{
            
            if(wp_verify_nonce($_POST['uultra_csrf_token'], 'uultra_reg_action')){
                
                        // Nonce is matched and valid. do whatever you want now.

             }else{
                
                       // Invalid nonce. you can throw an error here.
                       $this->errors[] = __('<strong>ERROR:</strong> Invalid Nonce.','users-ultra');
             }
            
        }
        
        //END NONCE
		
		
		if(get_option('users_can_register') == '1')
		{
		    foreach($this->usermeta as $key => $value) 
			{
		    
		        /* Validate username */
		        if ($key == 'user_login') 
				{
		            if (esc_attr($value) == '') {
		                $this->errors[] = __('<strong>ERROR:</strong> Please enter a username.','users-ultra');
		            } elseif (username_exists($value)) {
		                $this->errors[] = __('<strong>ERROR:</strong> This username is already registered. Please choose another one.','users-ultra');
		            }
		        }
		    
		        /* Validate email */
		        if ($key == 'user_email') 
				{
		            if (esc_attr($value) == '') 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> Please type your e-mail address.','users-ultra');
						
		            } elseif (!is_email($value)) 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> The email address isn\'t correct.','users-ultra');
					
					} elseif ($value!=$_POST['user_email_2']) 
					{
		               if($email_retype!='no')						
						{					
		               		 $this->errors[] = __('<strong>ERROR:</strong> The emails are different.','users-ultra');						
						}
		            } elseif (email_exists($value)) 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> This email is already registered, please choose another one.','users-ultra');
		            }
					
					//check domain restriction					
					$domain_restriction = $xoouserultra->get_option("force_domain_only");
					
					if($domain_restriction !='')
					{
						$allowed = array();						
						$allowed = explode(',', $domain_restriction);
						
						$email = $value;
						
						$domain = array_pop(explode('@', $email));

						if ( ! in_array($domain, $allowed))
						{
							// Not allowed
							$this->errors[] = __('<strong>ERROR:</strong> Your domain name is now allowed!','users-ultra');
						}

					}	
					
		        }
				
		    
		    }
			
			//check if auto-password
			$auto_password = $xoouserultra->get_option("set_password");			
			
			if($auto_password =='' || $auto_password==1)
			{	
			
				 /* Validate passowrd */
				 if ($_POST["user_pass"]=="") 
				 {
					$this->errors[] = __('<strong>ERROR:</strong> Please type your password.','users-ultra');           
					
				 }
				 
				 if ($_POST["user_pass"]!= $_POST["user_pass_confirm"]) 
				 {
					 $this->errors[] = __('<strong>ERROR:</strong> The passwords must be identical','users-ultra');           
					
				 }
				 
				 //password strenght 				 
				 $this->uultra_check_pass_strenght($_POST["user_pass"]);				 
				 
				 
				 
			 }else{ //send random password
				 
				
			}

			
			$g_recaptcha_response = '';		
			if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']!=''){
				
				$g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);		
			}
            
			
			//check reCaptcha
			$is_valid_recaptcha = true;	
			if(isset($uupro20_recaptcha) && $xoouserultra->get_option('recaptcha_site_key')!='' && $xoouserultra->get_option('recaptcha_secret_key')!='' && $xoouserultra->get_option('recaptcha_display_registration')=='1' ){
				
				$is_valid_recaptcha = $uupro20_recaptcha->validate_recaptcha_field($g_recaptcha_response);	
			
			}
			
			if(!$is_valid_recaptcha ){
				
					$this->errors[] = __('<strong>ERROR:</strong> Please complete reCaptcha Test first.','users-ultra');		
			}
			
			
		}
		else
		{
		    $this->errors[] = __('<strong>ERROR:</strong> Registration is disabled for this site.','users-ultra');
		}
		
		
	}
	
	function uultra_check_pass_strenght($password)
	{
		global $xoouserultra;
		$res= true;
		
		$PASSWORD_LENGHT = $xoouserultra->get_option('uultra_password_lenght');
		
		if($PASSWORD_LENGHT==''){$PASSWORD_LENGHT=7;}
		
		if(strlen($password)<$PASSWORD_LENGHT)
		{
			 $this->errors[] = __('<strong>ERROR:</strong> The Password must be at least '.$PASSWORD_LENGHT.' characters long','users-ultra');      
		}
		
		////must contain at least one number and one letter		
		$active = $xoouserultra->get_option('uultra_password_1_letter_1_number');		
		if($active==1)
		{
			$ret_validate_password = $this->validate_password_numbers_letters($password);
			if(!$ret_validate_password)
			{
				$this->errors[] = __('<strong>ERROR:</strong> The Password must contain at least one number and one letter','users-ultra'); 
			}
			    
		}
		
		////must contain at least one upper case character	
		$active = $xoouserultra->get_option('uultra_password_one_uppercase');		
		if($active==1)
		{
			$ret_validate_password = $this->validate_password_one_uppercase($password);
			if(!$ret_validate_password)
			{
				$this->errors[] = __('<strong>ERROR:</strong> The Password must contain at least one upper case character','users-ultra'); 
			}
			    
		}
		
		////must contain at least one lower case character
		$active = $xoouserultra->get_option('uultra_password_one_lowercase');		
		if($active==1)
		{
			$ret_validate_password = $this->validate_password_one_lowerrcase($password);
			if(!$ret_validate_password)
			{
				$this->errors[] = __('<strong>ERROR:</strong> The Password must contain at least one lower case character','users-ultra'); 
			}
			    
		}
		
			
		
		return $res;
	
	
	}
	
	//validate password one letter and one number	
	function validate_password_numbers_letters ($myString)
	{
		$ret = false;
		
		
		if (preg_match('/[A-Za-z]/', $myString) && preg_match('/[0-9]/', $myString))
		{
			$ret = true;
		}
					
		return $ret;
	
	
	}
	
	//at least one upper case character 	
	function validate_password_one_uppercase ($myString)
	{	
		
		if( preg_match( '~[A-Z]~', $myString) ){
   			 $ret = true;
		} else {
			
			$ret = false;
		  
		}
					
		return $ret;
	
	}
	
	//at least one lower case character 	
	function validate_password_one_lowerrcase ($myString)
	{	
		
		if( preg_match( '~[a-z]~', $myString) ){
   			 $ret = true;
		} else {
			
			$ret = false;
		  
		}
					
		return $ret;	
	
	}
	
		
	// File upload handler:
	function upload_front_avatar($o_id)
	{
		global $xoouserultra;
		global $wpdb;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
				
		/// Upload file using Wordpress functions:
		$file = $_FILES['user_pic'];
		
		
		$original_max_width = $xoouserultra->get_option('media_avatar_width'); 
        $original_max_height =$xoouserultra->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height==80)
		{			
			$original_max_width = 100;			
			$original_max_height = 100;			
		}
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->genRandomString();
		
		$rand_name = "avatar_".$rand."_".session_id()."_".time(); 		
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
			
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') 
		{
			if($o_id != '')
			{
				
				   if(!is_dir($path_pics."/".$o_id."")) {
						$this->CreateDir($path_pics."/".$o_id);								   
					}					
										
					$pathBig = $path_pics."/".$o_id."/".$rand_name.".".$ext;
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
						$path = $site_url.$upload_folder."/".$o_id."/";
						
						//check max width
												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
							if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							{
								$old = umask(0);
								chmod($pathBig, 0755);
								umask($old);
														
							}						
						
						}
						
						
						$new_avatar = $rand_name.".".$ext;						
						$new_avatar_url = $path.$rand_name.".".$ext;					
						
						//check if there is another avatar						
						$user_pic = get_user_meta($o_id, 'user_pic', true);						
						
						if ( $user_pic!="" )
			            {
							//there is a pending avatar - delete avatar																
									
							$path_pics = $site_url.$xoouserultra->get_option('media_uploading_folder');							
							$path_avatar = $path_pics."/".$o_id."/".$user_pic;					
														
							//delete								
							if(file_exists($path_avatar))
							{
								unlink($path_avatar);
							}
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);
							
						}else{
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);
												
						}
						
						//update user meta
					}
									
			     }  		
			
        } // image type
		
		// Create response array:
		$uploadResponse = array('image' => $new_avatar_url);
		
	}
	
	 public function createthumb($imagen,$newImage,$toWidth, $toHeight,$extorig)
	{             				
				
                 $ext=strtolower($extorig);
                 switch($ext)
                  {
                   case 'png' : $img = imagecreatefrompng($imagen);
                   break;
                   case 'jpg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'jpeg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'gif' : $img = imagecreatefromgif($imagen);
                   break;
                  }

               
                $width = imagesx($img);
                $height = imagesy($img);  
				

				
				$xscale=$width/$toWidth;
				$yscale=$height/$toHeight;
				
				// Recalculate new size with default ratio
				if ($yscale>$xscale){
					$new_w = round($width * (1/$yscale));
					$new_h = round($height * (1/$yscale));
				}
				else {
					$new_w = round($width * (1/$xscale));
					$new_h = round($height * (1/$xscale));
				}
				
				
				
				if($width < $toWidth)  {
					
					$new_w = $width;	
				
				//}else {					
					//$new_w = $current_w;			
				
				}
				
				if($height < $toHeight)  {
					
					$new_h = $height;	
				
				
				}
			
				
                $dst_img = imagecreatetruecolor($new_w,$new_h);
				
				/* fix PNG transparency issues */                       
				imagefill($dst_img, 0, 0, IMG_COLOR_TRANSPARENT);         
				imagesavealpha($dst_img, true);      
				imagealphablending($dst_img, true); 				
                imagecopyresampled($dst_img,$img,0,0,0,0,$new_w,$new_h,imagesx($img),imagesy($img));
               
                
				
				 switch($ext)
                  {
                   case 'png' : $img = imagepng($dst_img,"$newImage",9);
                   break;
                   case 'jpg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'jpeg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'gif' : $img = imagegif($dst_img,"$newImage");
                   break;
                  }
				  
				   imagedestroy($dst_img);	
				
				
				
                return true;

        }
	
	public function CreateDir($root){

               if (is_dir($root))        {

                        $retorno = "0";
                }else{

                        $oldumask = umask(0);
                        $valrRet = mkdir($root,0777);
                        umask($oldumask);


                        $retorno = "1";
                }

    }
	
	public function genRandomString() 
	{
		$length = 5;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
	
	/*Create user*/
	function uultra_upgrade_account() 
	{
		global $xoouserultra;
		session_start();
		
		
		$user_id = get_current_user_id();
		
		//get package
		$package = $xoouserultra->paypal->get_package($_POST["usersultra_package_id"]);
		$amount = $package->package_amount;
		$p_name = $package->package_name;
		$package_id = $package->package_id;
		$package_role = $package->package_role;
		
				
		//payment Method
		$payment_method = 'paypal';
		
		//create transaction
		$transaction_key = session_id()."_".time();
		
		$order_data = array('user_id' => $user_id,
		 'transaction_key' => $transaction_key,
		 'amount' => $amount,
		 'order_package_id' => $package_id ,
		 'product_name' => $p_name ,
		 'status' => 'pending',
		 'type' => '2',
		 'method' => $payment_method); 
		 
		if( $amount > 0)
		 {
			 $xoouserultra->order->create_order($order_data);
			
		 }
		 
												
		 
		  if($payment_method=="paypal" && $amount > 0)
		 {
			  $ipn = $xoouserultra->paypal->get_ipn_link($order_data, 'upgrade');
			  
			  //redirect to paypal
			  //echo $ipn;
			  header("Location: $ipn");exit;						  
			  exit;					  
			 
		 }
	
	}
	
	
	/*Create user*/
	function uultra_create_account() 
	{
		
		global $xoouserultra, $uultra_group;
		session_start();
		
			
			/* Create profile when there is no error */
			if (!isset($this->errors)) 
			{				
				
				/* Create account, update user meta */
				$sanitized_user_login = sanitize_user($_POST['user_login']);
				
				/*Check if we have to use email as*/				
				if($xoouserultra->get_option('allow_registering_only_with_email')=='yes')
				{
					$sanitized_user_login = sanitize_user($_POST['user_email']);
								
				}							
				
				/* Get password */
				if (isset($_POST['user_pass']) && $_POST['user_pass'] != '') 
				{
					$user_pass = $_POST['user_pass'];
					
				} else {
					
					$user_pass = wp_generate_password( 12, false);
				}
				
				$visitor_ip = $_SERVER['REMOTE_ADDR'];	
				
				//check blocked ip				
				$ip_defendermodule = $xoouserultra->get_option('uultra_ip_defender');
				
				if($ip_defendermodule=='yes') //module activated
				{
					$is_blocked =$xoouserultra->defender->check_ip($visitor_ip);
					
					if($is_blocked==0)
					{
						/* We create the New user only if ip not blocked */
						$user_id = wp_create_user( $sanitized_user_login, $user_pass, $_POST['user_email'] );
					
					}else{
						
						/* redirect users */
						$this->redirect_blocked_user();
						exit;					
					
					}	
				
				}else{
					
					/* We create the New user */
					$user_id = wp_create_user( $sanitized_user_login, $user_pass, $_POST['user_email'] );					
					/* We assign the custom profile form for this user*/					
					
				
				}			
								
				if ( ! $user_id ) 
				{

				}else{					
					
					//Role Set  - 03-11-2004			
					if($xoouserultra->get_option('uultra_roles_automatic_set')=='yes')
					{
						//get custom role
						$new_role = $xoouserultra->get_option('uultra_roles_automatic_set_role');
						
						//set custom role for this user
						if($new_role!="")
						{
							$user = new WP_User( $user_id );
							$user->set_role( $new_role );						
						}
						
					}				
					
					/*Set user role on registration*/
					$allow_user_role_registration = $xoouserultra->get_option('uultra_roles_actives_registration');
					$allowed_user_roles = $xoouserultra->role->uultra_allowed_user_roles_registration();
					$user_role = isset($this->usermeta['uultra_custom_user_role']) ? $this->usermeta['uultra_custom_user_role'] : '';
					
					if(!empty($user_role) && isset($allowed_user_roles[$user_role]) && $allow_user_role_registration =="yes")
					{	
						$user = new WP_User( $user_id );
						$user->set_role( $user_role );
					}
					
					/*End user role on registration*/				
									
					/*We've got a valid user id then let's create the meta informaion*/						
					foreach($this->usermeta as $key => $value) 
					{						
						 
						if (is_array($value))   // checkboxes
						{
							$value = implode(',', $value);
						}
						
						update_user_meta($user_id, $key, esc_attr($value));

						/* update core fields - email, url, pass */
						if ( in_array( $key, array('user_email', 'user_url', 'display_name') ) )
						{
							wp_update_user( array('ID' => $user_id, $key => esc_attr($value)) );
						}		
						
						
					}
					
					//update custom form					
					$custom_form = $_POST['uultra-custom-form-id'];		
					
					if($custom_form!="")
					{
						update_user_meta($user_id, 'uultra_custom_registration_form', $custom_form);
						
						//check if role has been assigned for this form						
						$custom_role = $this->get_custom_form_role($custom_form);
						
						if($custom_role!="")
						{
							$user = new WP_User( $user_id );
							$user->set_role( $custom_role );
						
						}						
					
					}		
					
					
					//set custom group.					
					$add_user_to_group = $xoouserultra->get_option('uultra_groups_automatic_set');
					
					if($add_user_to_group=="yes" && isset($uultra_group))
					{
						$group_to_assign = $xoouserultra->get_option('uultra_groups_automatic_set_group');						
						$uultra_group->save_user_group_rel($user_id, $group_to_assign);				
					}						
										
										
					//update visitor ip 08/10/2004
					$visitor_ip = $_SERVER['REMOTE_ADDR'];
					update_user_meta($user_id, 'uultra_user_registered_ip', $visitor_ip);
					
					
					//update user pic
					if(isset($_FILES['user_pic']))
					{
						$this->upload_front_avatar($user_id );
							
					}
					
					//set account status					
					$xoouserultra->login->user_account_status($user_id);
					
					$verify_key = $xoouserultra->login->get_unique_verify_account_id();					
					update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);							
					
					 //mailchimp					 
					 if(isset($_POST["uultra-mailchimp-confirmation"]) && $_POST["uultra-mailchimp-confirmation"]==1)
					 {
						 $list_id =  $xoouserultra->get_option('mailchimp_list_id');					 
						 $xoouserultra->subscribe->mailchimp_subscribe($user_id, $list_id);
						 update_user_meta ($user_id, 'xoouser_mailchimp', 1);				 						
					
					 }
										
					
				}
				
				

				//check if it's a paid sign up				
				if($xoouserultra->get_option('registration_rules')==4)
				{
					//this is a paid sign up					
										
					//get package
					$package = $xoouserultra->paypal->get_package($_POST["usersultra_package_id"]);
					$amount = $package->package_amount;
					$p_name = $package->package_name;
					$package_id = $package->package_id;
					$package_role = $package->package_role;
					$package_registration_form = $package->package_registration_form;		
					$package_group = $package->package_group;			
					
					
					//set custom role for this package
					if($package_role!="")
					{
						$user = new WP_User( $user_id );
						$user->set_role( $package_role );						
					}
					
					//set custom form
					if($package_registration_form!="")
					{						
					
						update_user_meta($user_id, 'uultra_custom_registration_form', $package_registration_form);
						
					}					
					
					//set custom group.					
					$add_user_to_group = $xoouserultra->get_option('uultra_groups_automatic_set');
					
					if($package_group!="" && isset($uultra_group))
					{
						//$group_to_assign = $xoouserultra->get_option('uultra_groups_automatic_set_group');						
						$uultra_group->save_user_group_rel($user_id, $package_group);				
					}	
					
					//payment Method
					$payment_method = 'paypal';
					
					//create transaction
					$transaction_key = session_id()."_".time();						 
					
					 //update status
					 update_user_meta ($user_id, 'usersultra_account_status', 'pending_payment');
					 //package 
					 update_user_meta ($user_id, 'usersultra_user_package_id', $package_id);
					 
					 //mailchimp					 
					 if(isset($_POST["uultra-mailchimp-confirmation"]) && $_POST["uultra-mailchimp-confirmation"]==1)
					 {						
						 //do mailchimp stuff	
						 $list_id =  $xoouserultra->get_option('mailchimp_list_id');					 
						 $xoouserultra->subscribe->mailchimp_subscribe($user_id, $list_id);	
						 update_user_meta ($user_id, 'xoouser_mailchimp', 1);					
					
					  }
					  
					  $payment_procesor = false;
					  if($_POST["uultra_payment_method"]=='' || $_POST["uultra_payment_method"]=='paypal')
					  {
						  $payment_procesor = true;
						  $payment_method="paypal";
					
					  }elseif($_POST["uultra_payment_method"]=='bank'){  
					  
					  	   $payment_method="bank";
					  }
					  
					 // echo "Payment Method " . $payment_method;
					  
					  //create order					  
					  $order_data = array('user_id' => $user_id,
						 'transaction_key' => $transaction_key,
						 'amount' => $amount,
						 'order_package_id' => $package_id ,
						 'product_name' => $p_name ,
						 'status' => 'pending',
						  'type' => '1',
						 'method' => $payment_method); 
						 
						// print_r($order_data);
						 
						if( $amount > 0)
						{
							 $xoouserultra->order->create_order($order_data);
							
						}				
					  
									 
					 //set expiration date					 
					 if($payment_method=="paypal" && $amount > 0 && $payment_procesor)
					 {
						  $ipn = $xoouserultra->paypal->get_ipn_link($order_data, 'ini');
						  
						  //create basic widgets
						  $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);
						  
						  //redirect to paypal
						  header("Location: $ipn");exit;						  
						  exit;	
					
					 }elseif($payment_method=="bank" && $amount > 0 && !$payment_procesor){
						 
						 
						  //notify depending on status
					      $xoouserultra->login->user_account_notify($user_id, $_POST['user_email'],  $sanitized_user_login, $user_pass);
						   //create basic widgets
						  $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);
						 
						 				  
						 
					 }else{						 
						 
						 //paid membership but free plan selected						 
						 //notify depending on status
					      $xoouserultra->login->user_account_notify($user_id, $_POST['user_email'],  $sanitized_user_login, $user_pass);
						  
						  //check if requires admin approval						  
						  if($package->package_approvation=="yes")
						  {							  
							  //create basic widgets
							  $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);		  
							  
							 
						  }else{
							  
							  //this package doesn't require moderation
							   update_user_meta ($user_id, 'usersultra_account_status', 'active');
							  //notify user					   
		 					   $xoouserultra->messaging->welcome_email($_POST['user_email'], $sanitized_user_login, $user_pass);
							  
							   //login
							   $secure = "";		
							  //already exists then we log in
							  wp_set_auth_cookie( $user_id, true, $secure );
							  
							  //create basic widgets
							  $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);
									
							  //redirect
							  $xoouserultra->login->login_registration_afterlogin();
							  
						  
						  }
						  
						  
						 
						 
					 }
					 
					 
					 
					 
				
				}else{
					
					//this is not a paid sign up
					
					 //create basic widgets
					 $xoouserultra->customizer->set_default_widgets_layout($user_id);
					
					//notify depending on status
					$xoouserultra->login->user_account_notify($user_id, $_POST['user_email'],  $sanitized_user_login, $user_pass);
										
				
				}	
				
				
				 //check if login automatically
				  $activation_type= $xoouserultra->get_option('registration_rules');
				  
				  if($activation_type==1)
				  {					  					  
					  //login
					   $secure = "";		
					  //already exists then we log in
					  wp_set_auth_cookie( $user_id, true, $secure );	
					  
					  //create basic widgets
					  $xoouserultra->customizer->set_default_widgets_layout($user_id);
								
					  //redirect
		              $xoouserultra->login->login_registration_afterlogin();						
	  
	              } 
				
				
			} //end error link
			
	}
	
	public function get_custom_form_role ($form_id)
	{
		global  $xoouserultra;
		
		$html ='';	
				
		$forms = get_option('usersultra_custom_forms_collection');			
		$form = $forms[$form_id] ;
		$form_role =$form['role'];
		
		return $form_role;
		
	}
	
	
	public function redirect_blocked_user()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');		
					    
		//check redir		
		$account_page_id = $xoouserultra->get_option('uultra_ip_defender_redirect_page');
		$my_account_url = get_permalink($account_page_id);
				
		if($my_account_url=="")
		{
			$url = $_SERVER['REQUEST_URI'];
				
		}else{
					
			$url = $my_account_url;				
				
		}
				
		wp_redirect( $url );
		exit;
	
	}
	/*Get errors display*/
	function get_errors() {
		global $xoouserultra;
		$display = null;
		if (isset($this->errors) && count($this->errors)>0) 
		{
		$display .= '<div class="xoouserultra-errors">';
			foreach($this->errors as $newError) {
				
				$display .= '<span class="xoouserultra-error xoouserultra-error-block"><i class="usersultra-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		} else {
		
			$this->registered = 1;
			
			$uultra_settings = get_option('userultra_options');

            // Display custom registraion message
            if (isset($uultra_settings['msg_register_success']) && !empty($uultra_settings['msg_register_success']))
			{
                $display .= '<div class="xoouserultra-success"><span><i class="fa fa-ok"></i>' . remove_script_tags($uultra_settings['msg_register_success']) . '</span></div>';
            
			}else{
				
                $display .= '<div class="xoouserultra-success"><span><i class="fa fa-ok"></i>'.__('Registration successful. Please check your email.','users-ultra').'</span></div>';
            }

            // Add text/HTML setting to be displayed after registration message
            if (isset($uultra_settings['html_register_success_after']) && !empty($uultra_settings['html_register_success_after'])) 
			
			{
                $display .= '<div class="xoouserultra-success-html">' . remove_script_tags($uultra_settings['html_register_success_after']) . '</div>';
            }
			
			
			
			if (isset($_POST['redirect_to'])) {
				wp_redirect( $_POST['redirect_to'] );
			}
			
		}
		return $display;
	}

}

$key = "register";
$this->{$key} = new XooUserRegister();