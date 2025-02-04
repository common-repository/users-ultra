<?php
class XooUserUser {
	
	var $messages_process;
	
	var $profile_order_field;
	
	var $profile_role;	
	var $profile_order;	
	var $uultra_args;
	var $emoticon_list;
	
	var $wp_users_fields = array("user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim");
	

	function __construct() 
	{
		
		$this->set_emoticons();			
		$this->uultra_replace_default_avatar();
		
		
		add_action('init', array( $this, 'handle_init' ));		
		
		if (isset($_POST['uultra-form-cvs-form-conf'])) 
		{
            
            
			/* Let's Update the Profile */
			$this->process_cvs($_FILES);
				
		}
		
		if (isset($_POST['uultra-conf-close-account-post'])) 
		{
			/* Let's Close this Account */
			add_action('init', array( $this, 'close_user_account' ));
				
		}
		
		add_action( 'wp_ajax_refresh_avatar', array( $this, 'refresh_avatar' ));
		add_action( 'wp_ajax_delete_user_avatar', array( $this, 'delete_user_avatar' ));	
		
		add_action( 'wp_ajax_nopriv_send_reset_link', array( $this, 'send_reset_link' ));		
		add_action( 'wp_ajax_nopriv_confirm_reset_password', array( $this, 'confirm_reset_password' ));
		add_action( 'wp_ajax_confirm_reset_password', array( $this, 'confirm_reset_password' ));
		add_action( 'wp_ajax_confirm_reset_password_user', array( $this, 'confirm_reset_password_user' ));
		
		add_action( 'wp_ajax_confirm_update_email_user', array( $this, 'confirm_update_email_user' ));		
		
		add_action( 'wp_ajax_get_pending_moderation_list', array( $this, 'get_pending_moderation_list' ));
		add_action( 'wp_ajax_user_approve_pending_account', array( $this, 'user_approve_pending_account' ));
		add_action( 'wp_ajax_user_resend_activation_link', array( $this, 'user_resend_activation_link' ));	
		
		add_action( 'wp_ajax_user_delete_account', array( $this, 'user_delete_account' ));		
		add_action( 'wp_ajax_get_pending_activation_list', array( $this, 'get_pending_activation_list' ));
		add_action( 'wp_ajax_get_pending_payment_list', array( $this, 'get_pending_payment_list' ));
		add_action( 'wp_ajax_user_package_edit_form', array( $this, 'user_package_edit_form' ));
		add_action( 'wp_ajax_user_package_edit_form_confirm', array( $this, 'user_package_edit_form_confirm' ));
		add_action( 'wp_ajax_user_status_change_confirm', array( $this, 'user_status_change_confirm' ));
		add_action( 'wp_ajax_user_customform_change_confirm', array( $this, 'user_customform_change_confirm' ));				
		add_action( 'wp_ajax_user_expiration_edit_form_confirm', array( $this, 'user_expiration_edit_form_confirm' ));	
		add_action( 'wp_ajax_user_see_details_backend', array( $this, 'user_see_submited_details_backend' ));
		
		add_action( 'wp_ajax_uultra_user_private_user_deletion', array( $this, 'uultra_user_private_user_deletion' ));	
		add_action( 'wp_ajax_uultra_delete_exported_csv_file', array( $this, 'uultra_delete_exported_csv_file' ));
		add_action( 'wp_ajax_uultra_user_change_role', array( $this, 'uultra_user_change_role' ));
		
		
			
			
		add_action('wp',  array(&$this, 'update_online_users'), 9);		
		add_action( 'wp_ajax_sync_users', array( $this, 'sync_users' ));
		add_action( 'wp_ajax_uultra_apply_default_layout_common_users', array( $this, 'uultra_apply_default_layout_common_users' ));
			add_action( 'wp_ajax_uultra_apply_membership_l_users', array( $this, 'uultra_apply_membership_l_users' ));
		
				
		
		$this->method_dect = array(
            'text' => 'text_box',
            'fileupload' => '',
            'textarea' => 'text_box',
            'select' => 'drop_down',
            'radio' => 'drop_down',
            'checkbox' => 'drop_down',
            'password' => '',
            'datetime' => 'text_box'
        );
		
		

	}
	
	function handle_init()
	
	{
		if (isset($_POST['xoouserultra-profile-edition-form'])) 
		{			
			/* This prepares the array taking values from the POST */
			$this->prepare( $_POST );
       			
			/* We validate everthying before updateing the profile */
			$this->handle();
			
			/* Let's Update the Profile */
			$this->update_me();
				
		}	
		
		
		if (isset($_POST['xoouserultra-profile-edition-form-admin'])) 
		{			
			/* This prepares the array taking values from the POST */
			$this->prepare( $_POST );
       			
			/* We validate everthying before updateing the profile */
			$this->handle();
			
			/* Let's Update the Profile */
			$this->update_me_admin();
				
		}	
		
		
	
	}
	
	
	/******************************************
	Default WP avatar
	******************************************/
	function uultra_replace_default_avatar() 
	{
		
		global  $xoouserultra;
		
		if($this->get_option("uultra_override_avatar") == 'yes')
		{
			add_filter('get_avatar', array($this,'uultra_get_avatar'), 99, 5);
		
		}
		
	}
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('userultra_options');
		if (isset($settings[$option])) 
		{
			return $settings[$option];
			
		}else{
			
		    return '';
		}
		    
	}
	
	/******************************************
	Is user online
	******************************************/
	function is_user_online($user_id) 
	{
		$online = get_transient('uultra_users_online');
		if (isset($online) && is_array($online) && isset($online[$user_id]) )
			return true;
		return false;
	}
	
	
	
	/******************************************
	Emoticons list
	******************************************/
	function set_emoticons()
	{		
		$emoticon_list["uultra_yes"] = array("shortocde"=>":yes:");
		$emoticon_list["uultra_yahoo"] = array("shortocde"=>":yahoo:");
		$emoticon_list["uultra_wink"] = array("shortocde"=>";-)");
		$emoticon_list["uultra_whistle3"] = array("shortocde"=>":whistle:");
		$emoticon_list["uultra_wacko"] = array("shortocde"=>":wacko:");
		$emoticon_list["uultra_unsure"] = array("shortocde"=>":unsure:");
		$emoticon_list["uultra_smile"] = array("shortocde"=>":-)");
		$emoticon_list["uultra_scratch"] = array("shortocde"=>":scratch:");		
		$emoticon_list["uultra_sad"] = array("shortocde"=>":-(");
		//$emoticon_list["uultra_rose"] = array("shortocde"=>"");
		
		//$emoticon_list["uultra_negative"] = array("shortocde"=>"");
		//$emoticon_list["uultra_heart"] = array("shortocde"=>"");
		$emoticon_list["uultra_good"] = array("shortocde"=>":good:");
		
		$emoticon_list["uultra_cry"] = array("shortocde"=>":cry:");		
		$emoticon_list["uultra_cool"] = array("shortocde"=>"B-)");
		$emoticon_list["uultra_bye"] = array("shortocde"=>":bye:");
		
		$this->emoticon_list = $emoticon_list;
		
	}
	
	function get_emoticons($icon)
	{
		return $this->emoticon_list[$icon];
	
	}
	
	function parse_emoticons($message)
	{
		$icons = $this->emoticon_list;
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_yes.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':yes:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_yahoo.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':yahoo:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_wink.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(';-)',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_whistle3.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':whistle:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_wacko.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':wacko:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_unsure.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':unsure:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_smile.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':-)',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_scratch.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':scratch:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_sad.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':-(',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_good.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':good:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_cry.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':cry:',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_cool.gif";			
		$html='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace('B-)',$html ,$message);
		
		$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/uultra_bye.gif";			
		$html ='<img src="'.$ico_url.'" class="uultra-emoti-msg-ico">';	
		$message = str_replace(':bye:',$html ,$message);		
		
		
		return $message;
	
	}
	
	/******************************************
	Get online users
	******************************************/
	function get_online_users()
	{
		$online = get_transient('uultra_users_online');
		if (is_array($online)) {
			
			foreach($online as $k=>$t){
				$include[] = $k;
			}
			
			$query['include'] = $include;
			
			$wp_user_query = $this->get_cached_query( $query );
			if (! empty( $wp_user_query->results )) {
				return $wp_user_query->results;
			}
			
		}
	}
	/******************************************
	Update online users
	******************************************/
	function update_online_users()
	{
	  if(is_user_logged_in()){

		if(($logged_in_users = get_transient('uultra_users_online')) === false) $logged_in_users = array();

		$current_user = wp_get_current_user();
		$current_user = $current_user->ID;  
		$current_time = current_time('timestamp');

		if(!isset($logged_in_users[$current_user]) || ($logged_in_users[$current_user] < ($current_time - (15 * 60) ))){
		  $logged_in_users[$current_user] = $current_time;
		  set_transient('uultra_users_online', $logged_in_users, (30 * 60) );
		}

	  }
	}
	
	
	public function close_user_account()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		require_once(ABSPATH. 'wp-admin/includes/ms.php' );
		
		//close
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		
		
		if(!is_super_admin( $current_user ))
		{			
				
			wp_delete_user( $current_user->ID );
			
			//delete for multisite wpmu 		
			if(function_exists('wpmu_delete_user')) 
			{		
				wpmu_delete_user( $user_id );				
			}
			
			wp_clear_auth_cookie();		
		
		
		}
		
	
	}
	
	public function uultra_user_private_user_deletion()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		
		$html = '';		
		
		//close
		$current_user = $_POST["user_id"];
		
		if(!is_super_admin( $current_user ))
		{
			//delete meta data		
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'usermeta WHERE user_id = "'.$current_user.'" ' ;			
			$wpdb->query( $sql );
			
			//delete media					
			wp_delete_user( $current_user );				
							
			
				$html .= '<div class="user-ultra-success">'. __("The user has been removed!", 'users-ultra').'</div>';
		}else{
			
				$html .= '<div class="user-ultra-warning">'. __("We're sorry Users Ultra doesn't delete admin users.", 'users-ultra').'</div>';
			
		}
		echo $html;
		die();		
			
	}
	
	function get_all_user_roles ($user_id ) 
	{
		$user = new WP_User( $user_id );
		
		$html = '';

		if ( !empty( $user->roles ) && is_array( $user->roles ) ) 
		{
			foreach ( $user->roles as $role )
				$html .= $role;
		}
		
		return $html;
		
	}
	
	function uultra_get_all_user_roles_array ($user_id ) 
	{
		$user = new WP_User( $user_id );
		
		$html = array();;

		if ( !empty( $user->roles ) && is_array( $user->roles ) ) 
		{
			foreach ( $user->roles as $role )
				$html[]= $role;
		}
		
		return $html;
		
	}
	
	
	
	
	public function show_protected_content($atts, $content)
	{
		global  $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'display_rule' => 'logged_in_based', //logged_in_based, membership_based, role_based, group_based
			'roles' => '', //administrator,subscriber	
			'groups' => '', //any group ID		
			'membership_id' => '', // the ID of the membership package separated by commas
			'custom_message_loggedin' =>'', // custom message
			'custom_message_capability' =>__("You can't see this content.",'users-ultra'), // custom message
			'ccap' =>'', // custom capabilities
			'custom_message_membership' =>'', // custom message
			'custom_message_role' =>'', // custom message
			'custom_message_group' =>'' // custom message
						
			
		), $atts ) );
		
		$package_list = array();
		 
		 if($custom_message_loggedin == "")
		 {
			$custom_message_loggedin =  __('Content visible only for registered users. ','users-ultra');
					
		 }elseif($custom_message_loggedin == "_blank"){
			 
			 $custom_message_loggedin =  "";		 
		
		}
		 
		 if($membership_id != "")
		 {
			 $package_list  = explode(',', $membership_id);					
		 }		
			
		
		if($display_rule == "logged_in_based")
		{
			//logged in based			
			if (!is_user_logged_in() && $custom_message_loggedin != "_blank") 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_loggedin.'</div>';
				
			} else {
				
				if($ccap=='')
				{
					//the users is logged in then display content
					return do_shortcode($content);	
				
				}else{
					
					//check for especial capabilities
					
					$user_id = get_current_user_id();
					
					if($this->check_user_special_capability($user_id, $ccap))
					{						
						return do_shortcode($content);					
					
					}else{						
						
						return  '<div class="uupublic-ultra-info">'.$custom_message_capability.'</div>';					
					
					}
				}
				
							
				
			}	
		
		}elseif($display_rule == "role_based"){					
			
			//logged in based			
			if (!is_user_logged_in()) 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_role.'</div>';
				
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();					
				$package = $this->get_user_package($user_id);	
								
				if($this->check_user_content_roles($user_id, $roles))
				{
					//the users is logged in then display content
					return do_shortcode($content);
				
				}else{
					
					return  '<div class="uupublic-ultra-info">'.$custom_message_role.'</div>';
					
					
				}
				
			
			}
		
		}elseif($display_rule == "group_based"){					
			
			//logged in based			
			if (!is_user_logged_in()) 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_group.'</div>';
				
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();					
				$package = $this->get_user_package($user_id);	
								
				if($this->check_user_content_groups($user_id, $groups))
				{
					//the users is logged in then display content
					return do_shortcode($content);
				
				}else{
					
					return  '<div class="uupublic-ultra-info">'.$custom_message_group.'</div>';
					
					
				}
				
			
			}
			
			
		
		}elseif($display_rule == "membership_based"){
			
			
			//check logged in		
			if (!is_user_logged_in() && $custom_message_membership != "_blank") 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_membership.'</div>';
				
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();					
				$package = $this->get_user_package($user_id);	
				
				if ( in_array($package , $package_list) )
				{
					if($ccap=='')
					{
						//the users is logged in then display content
						return do_shortcode($content);	
					
					}else{
						
						//check for especial capabilities
						
						$user_id = get_current_user_id();						
						if($this->check_user_special_capability($user_id, $ccap))
						{						
							return do_shortcode($content);					
						
						}else{						
							
							return  '<div class="uupublic-ultra-info">'.$custom_message_capability.'</div>';					
						
						}
						
					}
					
				}else{
					
					return  '<div class="uupublic-ultra-info">'.$custom_message_membership.'</div>';
					
				}
				
				//the users is logged in then display content								
				
			}		
			
		
		}
	
	}
	
	
	public function check_user_content_groups($user_id, $groups)
	{
		global $wpdb,  $xoouserultra, $uultra_group;
		$groups_that_can_see = array();
		$groups_that_can_see  = explode(',', $groups);	
		
		
		if(isset($uultra_group))		
		{
		
			//is this user allowed to see this post.				
			$user_groups =$uultra_group->get_all_user_groups($user_id);
					
			foreach ($groups_that_can_see as $group)
			{					
				if(in_array($group, $user_groups))
				{
					return true; //user belongs to this group					
				}				
					
			}
		
		}
		
	
		return false;
	
	}
	
	public function check_user_content_roles($user_id, $roles)
	{
		global $wpdb,  $xoouserultra;
		$roles_that_can_see = array();
		$roles_that_can_see  = explode(',', $roles);	
		
		foreach ($roles_that_can_see as $role)
		{	
			
			if($this->uultra_is_user_in_role($user_id,$role)) // the selected user 
			{
				return true;
			
			}
			
		
		}
		
		return false;
	
	}
	
	//Check if user can see this content based on special capabilities	
	public function check_user_special_capability($user_id, $ccap)
	{		
		global $wpdb,  $xoouserultra;
		
				//get user's ccap		
		$user_ccap_list = get_user_meta($user_id, 'ccap', true);
		
		if($user_ccap_list != "")
		{
			$user_ccap_array = array();
			
			$user_ccap_array  = explode(',', $user_ccap_list);	
			
			//check if user can see this content			
			if ( in_array($ccap , $user_ccap_array) )
			{
					return true;
						
			}else{					
					return  false;
						
			}
		
		}else{
			
			return false;		
		
		}		
	}
	
	public function get_user_package($user_id)
	{		
		global $wpdb,  $xoouserultra;
		
		return get_user_meta($user_id, 'usersultra_user_package_id', true);	
	
	}
	
	public function get_user_account_type_info($user_id)
	{		
		global $wpdb,  $xoouserultra;
		
		$result = array();
		
		$current_package_id = get_user_meta($user_id, 'usersultra_user_package_id', true);	
		
		$current_user_package = $xoouserultra->paypal->get_package($current_package_id);		
		$amount = $current_user_package->package_amount;
		
		if($amount==0)
		{
			$result = array('id' =>0, 'name' => __('Free','users-ultra'), 'price' => 0, 'creation' => 0 , 'expiraton' => 0);		
		
		}else{
			
			$result = array('id' => $current_package_id, 'name' => $current_user_package->package_name, 'price' =>$amount, 'creation' => 0 , 'expiraton' => 0);
			
		
		}
		
		return $result;
	
	}
	
	/*Edit Users See Submited Details*/
	public function user_see_submited_details_backend ()
	{
		global $wpdb,  $xoouserultra;
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		$user_id = $_POST["user_id"];
		
		$html .= $this->get_admin_profile_info($user_id);
		
		
		
		
		echo $html;
		die();
		
	}
	
	public function  get_admin_profile_info ($user_id)	
	{
		
		$array = get_option('usersultra_profile_fields');

		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
		$html .= '              
                  <div class="widget-ultra">
                    <h3 class="uultra-basic">Basic Information</h3>
                     <section class="default-bg small-triangle-comfrey"></section>
                     <div class="uultra-table">';
		
	
		foreach($array as $key => $field) 
		{

			extract($field);
			
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($show_in_widget))
			    $show_in_widget = 1;
				
			
			
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 ) 
			{
				$html .= '<div class="uultra-profile-seperator">'.$name.'</div>';
			}
			
			if ( $type == 'usermeta' && $deleted == 0 )			
			{				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				{
					$html .= ' <span class="data-a">'.$name.':</span><span class="data-b">'.$this->get_user_meta_custom( $user_id, $meta).'</span> ';
				}
			
			}
				 	
				
			
		}
		
		$html .= '</div>                               
                   </div>              
                 ';
				
		$html .= '<p>
          <a href="#" class="button uultra-user-edit-package-close " data-user="'.$user_id.'">'. __("Cancel","xoousers").'</a>
         
        </p>' ;
		return $html;
		
	}
	
    /*Edit Users Basic info in Backend*/
	public function user_package_edit_form ()
	{
		global $wpdb,  $xoouserultra;
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		$user_id = $_POST["user_id"];
		
		
		$html = '<div class="uuultra-users-membership-edition">';
		
		$html.= '<h2>' .__( 'Packages', 'xoousers' ). '</h2>';
		
		$packages = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_packages  ORDER BY `package_amount` ASC' );
		
		if ( empty( $packages ) )
			{
				$html.= '<p>' .__( 'You have no packages yet.', 'xoousers' ). '</p>';
			
			}else{
				
				
				
				$html .= "<ul>" ;
				
				$current_user_package = get_user_meta( $user_id, "usersultra_user_package_id", true);
				
				if($current_user_package=="")
					{
						$checked = 'checked="checked"';
						
					}
				
				$n = count( $packages );
				$num_unread = 0;
				
				$default_checked = 0;
				
				$html.= '<li> 
					
					<div class="uultra-package-opt">
					
					<span class="uultra-package-title">
					<input type="radio" name="usersultra_package_id_'.$user_id.'" value="" id="package_'.$package->package_id.'"  '.$checked.'/>
					
    - '.__("Free Package", "xoousers").'</span>
					
					</div>
					<div class="uultra-package-desc">
					<p>'.__("User will have only basic features", "xoousers").'</p>
					</div>
					
					
						
	     </li>';
				
				foreach ( $packages as $package )
				{
					$checked = '';
					
					if($default_checked==0)
					{
						//$checked = 'checked="checked"';
						
					}
					
					
					
					if($current_user_package==$package->package_id )
					{
						$checked = 'checked="checked"';
						
					}
					
					
					$html.= '<li> 
					
					<div class="uultra-package-opt">
					
					<span class="uultra-package-title"><input type="radio" name="usersultra_package_id_'.$user_id.'" value="'.$package->package_id.'" id="package_'.$package->package_id.'"  '.$checked.'/>
    - '.$package->package_name.'</span>
					
					<span class="uultra-package-cost">'.$currency_symbol.$package->package_amount.' </span></div>
					<div class="uultra-package-desc">
					<p>'.$package->package_desc.'</p>
					</div>
					
					
						
	     </li>';
		 
		 $default_checked++;
				
				
				}
				
				$html .= "</ul>" ;
				
				$html .= '<p>
          <a href="#" class="button uultra-user-edit-package-close " data-user="'.$user_id.'">'. __("Cancel","xoousers").'</a>
           <a href="#" class="button-primary uultra-user-edit-package-confirm" data-user="'.$user_id.'">'.__('Confirm','users-ultra').'</a>
        </p>' ;
		
		
				$html .= '<p id="uultra-u-package-ed-'.$user_id.'"></p>';
		
		
		      
		}
		
		$html .= '</div>' ;
		
		echo $html;
		 die();
		
	}
	
	
	
	
	
	
	 /*Edit Users Basic info in Backend*/
	public function edit_user_package_admin_form ($user_id = NULL)
	{
		global $wpdb,  $xoouserultra;
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		
		
		$html = '';		
				
		$packages = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_packages  ORDER BY `package_amount` ASC' );
		
		if ( empty( $packages ) )
			{
				$html.= '<p>' .__( 'You have no packages yet.', 'xoousers' ). '</p>';
			
			}else{				
				
				
				$html .= '<select class="xoouserultra-input" name="uultra-user-package-edition" id="uultra-user-package-edition" >';
				
				$current_user_package = get_user_meta( $user_id, "usersultra_user_package_id", true);
				
				if($current_user_package=="")
					{
						$checked = 'selected="selected"';
						
					}
				
				$n = count( $packages );
				$num_unread = 0;
				
				$default_checked = 0;
				
				 $html .= '<option value="" '.$checked.' >'.__("Free Package", "xoousers").'</option>';
				
						 
		 
		
				
				foreach ( $packages as $package )
				{
					$checked = '';
					
					if($default_checked==0)
					{
						//$checked = 'checked="checked"';
						
					}
					
					
					
					if($current_user_package==$package->package_id )
					{
						$checked = 'selected="selected"';
						
					}
					
					
					  $html .= '<option value="'.$package->package_id.'" '.$checked.' >'.$package->package_name.' - '.$currency_symbol.$package->package_amount.' </option>';
					
					
					
		 
		 $default_checked++;
				
				
				}
				
				$html .= "</select>" ;
				$html .= '<a href="#" class="button-primary uultra-user-edit-package-confirm" data-user="'.$user_id.'">'.__('Ok','users-ultra').'</a>';
				$html .= '<span id="uultra-package-conf"></span>';
		
		      
		}
		
	;
		
		return $html;
		
		
	}
	
	
	/*Update user status*/
	public function user_customform_change_confirm ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$html = "";
		
		$user_id = $_POST["user_id"];
		$custom_form_id = $_POST["custom_form_id"];		
		
		//update metaquery
		update_user_meta ($user_id, 'uultra_custom_registration_form', $custom_form_id);			
			
		$html .='<div class="user-ultra-success">'.__(" SUCCESS! The user's form has been changed ", "xoousers").'</div>';					
				
		
		echo $html;
		die();
		 
	}
	
	/*Update user status*/
	public function user_status_change_confirm ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		$html = "";
		
		$user_id = $_POST["user_id"];
		$status_id = $_POST["status_id"];
		
		if($this->uultra_is_user_in_role($user_id,'administrator')) // the selected user is an admin
		{			
						
			$html .='<div class="user-ultra-error">'.__(" ERROR! You can't change the status of an administrator. ", "xoousers").'</div>';
			
			
				
		
		}else{
			
			if($status_id!="")
			{
				 				
				//update metaquery
				update_user_meta ($user_id, 'usersultra_account_status', $status_id);			
			
				$html .='<div class="user-ultra-success">'.__(" SUCCESS! The user's status has been changed to : ".$status_id."", "xoousers").'</div>';					
				
			
			}		
			
			//notify user
			$user = get_user_by('id',$user_id);
		
			
		
		}
		
		
		echo $html;
		die();
		 
	}
	
	/*Edit Users Basic info save changes*/
	public function user_package_edit_form_confirm ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		$html = "";
		
		$user_id = $_POST["user_id"];
		$package_id = $_POST["package_id"];
		
		if($this->uultra_is_user_in_role($user_id,'administrator')) // the selected user is an admin
		{			
						
			$html .='<div class="user-ultra-error">'.__(" ERROR! You can't change the role of an administrator. ", "xoousers").'</div>';
			
			//assign package
			
			if($package_id=="")
			{
				 delete_user_meta($user_id, 'usersultra_user_package_id') ;
			
			}else{				
				
				 //update metaquery
				 update_user_meta ($user_id, 'usersultra_user_package_id', $package_id);	
			
			}
						
			 //create basic widgets
			 delete_user_meta($user_id, 'uultra_profile_widget_setup') ;
			 $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);
			
			$html .='<div class="user-ultra-success">'.__(" SUCCESS! The user's membership plan has been changed ", "xoousers").'</div>';
			
		
		}else{
			
			if($package_id=="")
			{
				 delete_user_meta($user_id, 'usersultra_user_package_id') ;
			
			}else{
				
				//update metaquery
				update_user_meta ($user_id, 'usersultra_user_package_id', $package_id);
				
				//role settings					
				$package = $xoouserultra->paypal->get_package($package_id);					
				$package_role = $package->package_role;
					
				//set custom role for this package
				if($package_role!="")
				{
					$user = new WP_User( $user_id );
					$user->set_role( $package_role );
					
					$html .='<div class="user-ultra-success">'.__(" SUCCESS! The role has been changed ", "xoousers").'</div>';
											
				}				
				
			
			}
		
			//get package		
			$package = $xoouserultra->paypal->get_package($package_id);
		 
			//notify user
			$user = get_user_by('id',$user_id);			
			
			 //create basic widgets
			 delete_user_meta($user_id, 'uultra_profile_widget_setup') ;			 
			 $xoouserultra->customizer->set_default_widgets_layout($user_id,  $package_id);					 
			
			$html .='<div class="user-ultra-success">'.__(" SUCCESS! The user's membership plan has been changed ", "xoousers").'</div>';	
			
		
		}
		
		
		echo $html;
		die();
		 
	}
	
	
	/*The user change their role*/
	public function uultra_user_change_role ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
				
		$user_id = get_current_user_id();
		$role = $_POST["role"];	
		
		if($this->uultra_is_user_in_role($user_id,'administrator')) // the selected user is an admin
		{			
						
			$html .='<div class="uupublic-ultra-error">'.__(" ERROR! You can't change the role of an administrator. ", "xoousers").'</div>';								
					
		}else{
			
			if($role!="" && $xoouserultra->get_option('uultra_roles_actives_backend')=='yes')
			{		
							
				//set custom role for this package
				if($role!="")
				{
					$user = new WP_User( $user_id );
					$user->set_role( $role );
					
					$html .='<div class="uupublic-ultra-success">'.__(" SUCCESS! The role has been changed ", "xoousers").'</div>';
											
				}				
				
			
			}
		
					
		}
		
		
		echo $html;
		die();
		 
	}
	
	
	/*Edit user expiration date*/
	public function user_expiration_edit_form_confirm ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		
		$html = "";
		
		$user_id = $_POST["user_id"];
		$expiration_id = $_POST["expiration_id"];
		
		$error_message = '';		
		$current_date = date("Y-m-d");
		
				
		if($expiration_id=='')
		{
			$error_message .='<div class="user-ultra-error">'.__(" ERROR! Expiration date can't be an empy value. ", "xoousers").'</div>';
		
		}
		
		$expiration_id = date("Y-m-d", strtotime($expiration_id));
		
		if($expiration_id<$current_date)
		{
			$error_message .='<div class="user-ultra-error">'.__(" ERROR! Expiration date should be in the future. ", "xoousers").'</div>';	
		
		}
		
		if($error_message=='') // the selected user is an admin
		{								
			
			
				//expiration meta data
				update_user_meta ($user_id, 'usersultra_membership_expiration', $expiration_id);
			
			$html .='<div class="user-ultra-success">'.__(" SUCCESS! The expiration date has been changed ", "xoousers").'</div>';
											
							
			
		
		}else{
			
			
			$html .=$error_message;
			
							
				
				
			
		}
			
		
		
		echo $html;
		die();
		 
		
		
		
	}
	
	function uultra_is_user_in_role( $user_id, $role  )
	{
		return in_array( $role, $this->uultra_get_all_user_roles_array( $user_id ) );
	}
	
	
	
	/*Process uploads*/
	function process_cvs($array) 
	{
		global $wpdb,  $xoouserultra;
		
		/* File upload conditions */
		$this->allowed_extensions = array("csv");
		
		
		$send_welcome_email = false;
		
		if(isset($_POST["uultra-send-welcome-email"] ) && $_POST["uultra-send-welcome-email"]==1)
		{
			$send_welcome_email = true;
		
		}
		
		$account_status = "";
		
		if(isset($_POST["uultra-activate-account"] ) )
		{
			$account_status = $_POST["uultra-activate-account"];		
		}
		
		$package = "";
		
		if(isset($_POST["uultra-package"] ) )
		{
			$package = $_POST["uultra-package"];		
		}
		
		
							
		if (isset($_FILES))
		{
			foreach ($_FILES as $key => $array) {
				
								
				extract($array);
				
				$file = $_FILES[$key];
				
				$info = pathinfo($file['name']);
				$real_name = $file['name'];
				$ext = $info['extension'];
				$ext=strtolower($ext);
		
				
				if ($name) {
				    
					
					if ( !in_array($ext, $this->allowed_extensions) )
					{
						$this->messages_process .= __('The file format is not allowed!','users-ultra');											
					
					} else {
					
						/*Upload file*/									
						$path_f = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
						
						$target_path = $path_f.'/import/';
						// Checking for upload directory, if not exists then new created. 
						if(!is_dir($target_path))
						    mkdir($target_path, 0755);
						
						$target_path = $target_path . time() . '_'. basename( $name );						
						move_uploaded_file( $tmp_name, $target_path);
						
										
						//now that the files is up we have to start the uploading
						
						$row = 0;
						if (($handle = fopen($target_path, "r")) !== FALSE) 
						{			
							
							
	 								
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
							{
								$num = count($data);
								
								if($row == 0) //these are the headers
								{
									$this->messages_process .='<h3>Imported Data</h3>';
									$this->messages_process .=  '<table class="wp-list-table widefat">
							<tr><th>Row</th>';
							
									foreach($data as $element)
									{
										$headers[] = $element;										
										$this->messages_process .= '<th>' . $element . '</th>';
									
									}
									
									$this->messages_process .='</tr>';
									
									$columns = count($data);								
									
								
								}
								
								if($row > 0) //this is not the header then we create the user
								{
																		
									$this->create_user_import ($data, $headers, $send_welcome_email, $account_status, $package, $row);
								}
								$row++;
															
								
							}
							
							fclose($handle);
							
						
							$this->messages_process .='</table>';
							$this->messages_process .= '<p> <strong>'.__('--- Finished ---  ', 'users-ultra').'</strong></p>';
						}

						
					}
				}
			}
		}
		
	}
	
	public function create_user_import ($user, $headers, $send_welcome_email, $account_status, $package, $count)
	{
		global $wpdb,  $xoouserultra;
		
		//username, email, display name, first name and last name
		
		$user_name = $user[0]; 
		$email = $user[1];
		$display_name = $user[2];				
		//metadata		
		$f_name = $user[3];
		$l_name = $user[4];
		
		$columns = count($user);		
		
		//print_r($headers);
		
						
		$user_pass = wp_generate_password( 12, false);		
		/* Create account, update user meta */
		$sanitized_user_login = sanitize_user($user_name);
		
		
		
		if(!email_exists($email))
		{
			
		
			/* We create the New user */
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $email);
			
			if ( ! $user_id ) 
			{
	
			}else{
				
				//set account status					
				$xoouserultra->login->user_account_status($user_id);
						
				$verify_key = $xoouserultra->login->get_unique_verify_account_id();	
								
				update_user_meta ($user_id, 'display_name', $display_name);
				update_user_meta ($user_id, 'first_name', $f_name);
				update_user_meta ($user_id, 'last_name', $l_name);								
				update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);
				
				if(isset($package))
				{
					update_user_meta ($user_id, 'usersultra_user_package_id', $package);
					
					///loop through all the extra meta data	
					$xoouserultra->customizer->set_default_widgets_layout($user_id, $package);				
				
				}else{
				
					///loop through all the extra meta data	
					$xoouserultra->customizer->set_default_widgets_layout($user_id);
				
				}
				
				if($columns > 5)
				{
					
					for($i=5; $i<$columns; $i++):
									if(in_array($headers[$i], $this->wp_users_fields))
										wp_update_user( array( 'ID' => $user_id, $headers[$i] => $user[$i] ) );
									else
										update_user_meta($user_id, $headers[$i], $user[$i]);
					endfor;
					
							$this->messages_process .=  "<tr><td>" . ($count ) . "</td>";
							
							foreach ($user as $element)
								$this->messages_process .= "<td>$element</td>";

							$this->messages_process .= "</tr>\n";

							flush();
				}		
							
				
				
				
				
							
				if($send_welcome_email)
				{
					//status
					
					if($account_status=="active")
					{
						
						update_user_meta ($user_id, 'usersultra_account_status','active');
						
						//automatic activation
						$xoouserultra->messaging->welcome_email($email, $sanitized_user_login, $user_pass);
						
					}
					
					if($account_status=="pending")
					{
						
						update_user_meta ($user_id, 'usersultra_account_status','pending');
						
						 //email activation link		  			  
						  $web_url =$xoouserultra->login->get_my_account_direct_link();			  
						  $pos = strpos("page_id", $web_url);		  
						  $unique_key = get_user_meta($user_id, 'xoouser_ultra_very_key', true);
						  
						  if ($pos === false) // this is a tweak that applies when not Friendly URL is set.
						  {
								//
								$activation_link = $web_url."?act_link=".$unique_key;
									
						  } else {
									 
							   // found then we're using seo links					 
							   $activation_link = $web_url."&act_link=".$unique_key;
									
						  }
						  
						  //send link to user
						  $xoouserultra->messaging->welcome_email_with_activation($email, $sanitized_user_login, $user_pass, $activation_link);

					}
					
				}
						
			}
		
		}else{
			  //email exists
		
		} //end if
		
		//echo $this->csvImportResult;
		
	}
	
	
	
	public function get_user_meta ($meta)
	{
		$user_id = get_current_user_id();		
		return get_user_meta( $user_id, $meta, true);
		
	}
	
	public function get_user_meta_custom ($user_id, $meta)
	{
		return get_user_meta( $user_id, $meta, true);
		
	}
	
	public function uultra_apply_default_layout_common_users ()
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                    
			'meta_value' => 'active',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		

		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		$users= $user_query->get_results();

		
		$count = 0;
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				
				$user_id = $user->ID;				
				$package_id =get_user_meta($user_id, 'usersultra_user_package_id', true);
				
				if($package_id=='')
				{
					$count++;			
					//udpate widgets				
				    delete_user_meta($user_id, 'uultra_profile_widget_setup') ;
				    $xoouserultra->customizer->set_default_widgets_layout($user_id);
				
				}
				
			}
					
		
		}
		
		echo "<div class='user-ultra-success'>".__(" DONE! The updating process has been finished. ".$count." users updated ", 'users-ultra')."</div>";
		
		die();
	}
	
	public function uultra_apply_membership_l_users ()
	{
		global $wpdb,  $xoouserultra;
		
		
		$package_id = $_POST['package_id'];
		
		$args = array( 	
						
			'meta_key' => 'usersultra_user_package_id',                    
			'meta_value' => $package_id,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		

		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		$users= $user_query->get_results();
		
		

		
		$count = 0;
		
		if (!empty($users) && $package_id!='')
		{
			
			foreach($users as $user) 
			{
				
				$user_id = $user->ID;				
				$package_id =get_user_meta($user_id, 'usersultra_user_package_id', true);
				
				if($package_id!='')
				{
					$count++;			
					//udpate widgets				
				    delete_user_meta($user_id, 'uultra_profile_widget_setup') ;
				   $xoouserultra->customizer->set_default_widgets_layout($user_id, $package_id);
				   
				
				}
				
			}
					
		
		}
		
		echo "<div class='user-ultra-success'>".__(" DONE! The updating process has been finished. ".$count." users updated ", 'users-ultra')."</div>";
		
		die();
	}
	
	public function sync_users ()
	{
		global $wpdb,  $xoouserultra;
		
		//$sql = 'SELECT ID,display_name FROM ' . $wpdb->prefix . 'users  ' ;
		//$users = $wpdb->get_results($sql );
		
		$users= new WP_User_Query( array ( 'orderby' => 'ID', 'order' => 'DESC' ) );
		$users= $users->get_results();

		
		$count = 0;
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				$count++;
				$user_id = $user->ID;
				update_user_meta ($user_id, 'usersultra_account_status', 'active');
				update_user_meta ($user_id, 'display_name', $user->display_name);
				
				//udpate widgets
				///loop through all the extra meta data	
				$xoouserultra->customizer->set_default_widgets_layout($user_id);
				
				
			}
					
		
		}
		
		echo "<div class='user-ultra-success'>".__(" SUCCESS! The sync process has been finished. ".$count." users were updated ", 'users-ultra')."</div>";
		
		die();
	}
	
	/*Get Stats*/
	public function get_amount_period ($month, $day, $year)
	{
		global $wpdb,  $xoouserultra;
		
		$sql = 'SELECT count(*) as total, user_registered, ID FROM ' . $wpdb->prefix . 'users  WHERE ID <> 0  ' ;
		
		if($day!=""){$sql .= " AND DAY(user_registered) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(user_registered) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(user_registered) = '$year'";}	
		
		$users = $wpdb->get_results($sql );
		
		//echo $sql;
		
		$res_total = $xoouserultra->commmonmethods->fetch_result($users);
		
		if($res_total->total=="")
		{
			return 0;
			
		}else{
			
			return $res_total->total;
			
		}
		
	
	
	}
	
	/*Get Pending Payment*/
	public function get_pending_payment_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		$pic_boder_type = "";
		 $pic_size_type="";		
		
		$users = $this->get_pending_payment($howmany);
		
		$html = '<h3>'.__('Pending Payment','users-ultra').'</h3>';
		
		$html .= '<div id="uultra-user-acti-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'users-ultra').'</th>
						<th style="width:15%;">'.__('Username', 'users-ultra').'</th>
						
						<th >'.__('Email', 'users-ultra').'</th>
						<th>'.__('Registered', 'users-ultra').'</th>
						<th>'.__('Action', 'users-ultra').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Deny','users-ultra').'					   </a> <a href="#" class="button-primary uultradmin-user-approve" user-id="'.$user_id.'">'.__('Confirm','users-ultra').'
					   </a></td></tr>';
					
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending payment users.','users-ultra').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
	/*Get Pending*/
	public function get_pending_moderation_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		$pic_boder_type = "";
		$pic_size_type = "";
		
		
		$users = $this->get_pending_moderation($howmany);
		
		$html = '<h3>'.__('Pending Moderation','users-ultra').'</h3>';
		
		$html .= '<div id="uultra-user-acti-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'users-ultra').'</th>
						<th style="width:15%;">'.__('Username', 'users-ultra').'</th>
						
						<th >'.__('Email', 'users-ultra').'</th>
						<th>'.__('Registered', 'users-ultra').'</th>
						<th>'.__('Action', 'users-ultra').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Deny','users-ultra').'					   </a> <a href="#" class="button-primary uultradmin-user-approve" user-id="'.$user_id.'">'.__('Confirm','users-ultra').'
					   </a></td></tr>';
					
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending moderation users.','users-ultra').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
	/*Get Pending*/
	public function get_pending_activation_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		
		$users = $this->get_pending_activation($howmany);
		
		$html = '<h3>'.__('Pending Confirmation','users-ultra').'</h3>';
		
		$html .= '<div id="uultra-user-acti-pending-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'users-ultra').'</th>
						<th style="width:15%;">'.__('Username', 'users-ultra').'</th>
						
						<th >'.__('Email', 'users-ultra').'</th>
						<th>'.__('Registered', 'users-ultra').'</th>
						<th>'.__('Action', 'users-ultra').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Delete','users-ultra').'					   </a> <a href="#" class="button-primary uultradmin-user-resend-link" user-id="'.$user_id.'">'.__('Send Link','users-ultra').'
					   </a><a href="#" class="button-primary uultradmin-user-approve-2" user-id="'.$user_id.'">'.__('Confirm','users-ultra').'
					   </a></td></tr>';
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending confirmation users.','users-ultra').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
	/*Send Activation Link Account*/
	public function user_send_activation_link ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'active');
		
		$user = get_user_by( 'id', $user_id );
		
	
		
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->confirm_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been activated", 'users-ultra')."</div>";
		
		die();
	
	
	}
	
	/*Resend link Account*/
	public function user_resend_activation_link ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		$user = get_user_by( 'id', $user_id );
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->login->user_resend_activation_link($user_id, $u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("Activation link sent", 'users-ultra')."</div>";
		
		die();
	
	
	}
	
	
	
	/*Activate Account*/
	public function user_approve_pending_account ()
	{
		global $wpdb,  $xoouserultra;
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'active');
		
		$user = get_user_by( 'id', $user_id );
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->confirm_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been activated", 'users-ultra')."</div>";
		
		die();
	
	
	}
	
	/*Activate Account*/
	public function user_delete_account ()
	{
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'deleted');
		
		$user = get_user_by( 'id', $user_id );
		
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->deny_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been deleted", 'users-ultra')."</div>";
		
		die();
	
	
	}
	
		/*Get Pending Payment*/
	public function get_pending_payment ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                    
			'meta_value' => 'pending_payment',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	/*Get Pending*/
	public function get_pending_moderation ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                    //(string) - Custom field key.
			'meta_value' => 'pending_admin',                  //(string|array) - Custom field value.
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	/*Get Pending Activation*/
	public function get_pending_activation ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                   
			'meta_value' => 'pending',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		$user_query = new WP_User_Query( $args );		 
     	$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	/*Get Pending Activation Count*/
	public function get_pending_activation_count ()
	{
		global $wpdb,  $xoouserultra;
		
		$total = 0;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                   
			'meta_value' => 'pending_admin',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		$user_query = new WP_User_Query( $args );		 
     	$total = $user_query->get_total() ;		
		return $total;
		
	
	}
	
	
	/* This is the */
	public function signup_status( $method )
	{
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_social_signup',                    //(string) - Custom field key.
			'meta_value' => $method,                  //(string|array) - Custom field value.
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		
		$total = $user_query->get_total();
		return $total;
		// Output results
	
	
	}
	
	public function confirm_reset_password_user()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$wp_rewrite = new WP_Rewrite();
		
		$user_id = get_current_user_id();		
				
		//check redir		
		//$account_page_id = get_option('xoousersultra_my_account_page');
		$account_page_id = $xoouserultra->get_option('login_page_id');
		
		
		$my_account_url = get_permalink($account_page_id);		
		
		
		$PASSWORD_LENGHT =7;
		
		$password1 = $_POST['p1'];
		$password2 = $_POST['p2'];
		
		$html = '';
		$validation = '';
		
		//check password
		
		if($password1!=$password2)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password must be identical ", 'users-ultra')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'users-ultra')."</div>";
			$html = $validation;		
		}
		
		
		if($validation=="" )
		{
		
			if($user_id >0 )
			{
					//echo "user id: ". $user_id;
					$user = get_userdata($user_id);
					//print_r($user);
					$user_id = $user->ID;
					$user_email = $user->user_email;
					$user_login = $user->user_login;			
					
					wp_set_password( $password1, $user_id ) ;
					
					//notify user					
					$xoouserultra->messaging->send_new_password_to_user($user_email, $user_login, $password1);
					
					$html = "<div class='uupublic-ultra-success'>".__(" Success!! The new password has been sent to ".$user_email."  ", 'users-ultra')."</div>";
					
					// Here is the magic:
					wp_cache_delete($user_id, 'users');
					wp_cache_delete($username, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
					wp_logout();
					wp_signon(array('user_login' => $user_login, 'user_password' => $password1));
					
				}else{
									
				}
					
			}
		 echo $html;
		 die();
		
	
	}
	
	function validate_valid_email ($myString)
	{
		$ret = true;
		if (!filter_var($myString, FILTER_VALIDATE_EMAIL)) {
    		// invalid e-mail address
			$ret = false;
		}
					
		return $ret;
	
	
	}
	
	public function confirm_update_email_user()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$wp_rewrite = new WP_Rewrite();
		
		$user_id = get_current_user_id();
	
	
		$email = $_POST['email'];
		$html = '';
		$validation = '';
		
	
		//validate if it's a valid email address	
		$ret_validate_email = $this->validate_valid_email($email);
		
		if($email=="")
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Please type your new email ", 'users-ultra')."</div>";
			$html = $validation;			
		}
		
		if(!$ret_validate_email)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Please type a valid email address ", 'users-ultra')."</div>";
			$html = $validation;			
		}
		
		$current_user = get_userdata($user_id);
		//print_r($user);
		$current_user_email = $current_user->user_email;
		
		//check if already used
		
		$check_user = get_user_by('email',$email);
		$user_check_id = $check_user->ID;
		$user_check_email = $check_user->ID;
		
		if($validation=="" )
		{
		
			if($user_check_id==$user_id) //this is the same user then change email
			{
				$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! You haven't changed your email. ", 'users-ultra')."</div>";
				$html = $validation;
				
			
			}else{ //email already used by another user
			
				if($user_check_email!="")
				{
			
					$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! The email is in use already ", 'users-ultra')."</div>";
					$html = $validation;
				
				}else{
					
					//email available
					
				}
				
			
			}
		
		}
		
		
		
		if($validation=="" )
		{
		
			if($user_id >0 )
			{
					$user = get_userdata($user_id);
					$user_id = $user->ID;
					$user_email = $user->user_email;
					$user_login = $user->user_login;	
					
					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
					
					//update mailchimp?
					$mail_chimp = get_user_meta( $user_id, 'xoouser_mailchimp', true);
					
					if($mail_chimp==1) //the user has a mailchip accoun, then we have to sync
					{
						if($xoouserultra->get_option('mailchimp_api'))
						{
							$list_id =  $xoouserultra->get_option('mailchimp_list_id');					 
							$xoouserultra->subscribe->mailchimp_subscribe($user_id, $list_id);
						}
					}
					
					
																
										
					$html = "<div class='uupublic-ultra-success'>".__(" Success!! Your email account has been changed to : ".$email."  ", 'users-ultra')."</div>";
					
																			
				}else{
					
									
				}
					
			}
		 echo $html;
		 die();
		
	
	}
	
	public function check_force_upgrade()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		
		$user_id = get_current_user_id();		
		$force_upgrade = $xoouserultra->get_option('force_account_upgrading');
		
		if($force_upgrade=='yes')
		{
			//check if user already upgraded
			
		
		
		}else{
			
			return false;		
		
		}
		
		
	}
	
	public function confirm_reset_password()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$wp_rewrite = new WP_Rewrite();
		
				
		//check redir		
		//$account_page_id = get_option('xoousersultra_my_account_page');
		$account_page_id = $xoouserultra->get_option('login_page_id');
		$my_account_url = get_permalink($account_page_id);
		
		
		
		$PASSWORD_LENGHT =7;
		
		$password1 = $_POST['p1'];
		$password2 = $_POST['p2'];
		$key = $_POST['key'];
		
		$html = '';
		$validation = '';
		
		//check password
		
		if($password1!=$password2)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password must be identical ", 'users-ultra')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'users-ultra')."</div>";
			$html = $validation;		
		}
		
		
		$user = $this->get_one_user_with_key($key);
		
		
		if($validation=="" )
		{
			
			if($user->ID >0 )
			{
				//print_r($user);
				$user_id = $user->ID;
				$user_email = $user->user_email;
				$user_login = $user->user_login;
				
				wp_set_password( $password1, $user_id ) ;
				
				//notify user
				
				$xoouserultra->messaging->send_new_password_to_user($user_email, $user_login, $password1);
				
				$html = "<div class='uupublic-ultra-success'>".__(" Success!! The new password has been sent to ".$user_email."  ", 'users-ultra')."</div>";
				
				$html .= "<div class=''>".__('<a href="'.$my_account_url.'" title="'.__("Login","xoousers").'">CLICK HERE TO LOGIN</a>', 'users-ultra')."</div>";
				
								
			}else{
				
				// we couldn't find the user			
				$html = "<div class='uupublic-ultra-error'>".__(" ERROR! Invalid reset link ", 'users-ultra')."</div>";
			
			}
					
		}
		 echo $html;
		 die();
		
	
	}
	
	public function send_reset_link()
	{
		session_start();
		global $wpdb,  $xoouserultra;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$html = "";
		
		// Adding support for login by email
		if(is_email($_POST['user_login']))
		{
			  $user = get_user_by( 'email', $_POST['user_login'] );
			  
			 			  
			  // check if active					
			  $user_id =$user->ID;				
			 
			  if($user_id=="")
			  {
				  //user not found
				  $html = __('Email not found','users-ultra');
			  
			  }else{
				  
				  //user found 				  
				   if(!$this->is_active($user_id) && !is_super_admin($user_id))
				   {
					   					   
					   //user is not active					   
					   $html = __('Your account is not active yet.','users-ultra');				   
					   $noactive = true;
						  
				   }else{
				   
				   
				   
				   }
				 
			  }
		  
		  }else{
			  
			  // User is trying to login using username			  
			  $user = get_user_by('login',$_POST['user_login']);
			  
			  // check if active and it's not an admin		
			  $user_id =$user->ID;
			  
			  if($user_id=="")
			  {
				  //user not found
				  $html = __('User not found','users-ultra');
			  
			  }else{
				  
				  //user found 
				  
				   if(!$this->is_active($user_id) && !is_super_admin($user_id))
				   {
					   					   
					   //user is not active					   
					   $html = __('Your account is not active yet.','users-ultra');				   
					   $noactive = true;
						  
				   }else{
				   
				   
				   
				   }
				 
			  }	
			  
		  
		  }
		  
		  if($html=="" && isset($user))
		  {
			  //generate reset link
			  $unique_key =  $xoouserultra->login->get_unique_verify_account_id();
			  
			  //web url
			  $web_url = $xoouserultra->login->get_login_page_direct_link();
			  
			  $pos = strpos("page_id", $web_url);

			  
			  if ($pos === false) //not page_id found
			  {
				    //
					$reset_link = $web_url."?resskey=".$unique_key;
					
			  } else {
				     
					 // found then we're using seo links					 
					 $reset_link = $web_url."&resskey=".$unique_key;
					
			  }
			  
			  //update meta
			  update_user_meta ($user_id, 'xoouser_ultra_very_key', $unique_key);	
			  
			  //notify users			  
			  $xoouserultra->messaging->send_reset_link($user, $reset_link);			  
			  
			  //send reset link to user		  			  
			   $html = "<div class='uupublic-ultra-success'>".__(" A reset link has been sent to your email ", 'users-ultra')."</div>";
			   
			  
			  
			 
		  }
		  
		 
		 echo $html;
		 die();
	}
	
	function get_me_wphtml_editor($meta, $content)
	{
		// Turn on the output buffer
		ob_start();
		
		$editor_id = $meta;				
		$editor_settings = array('media_buttons' => false , 'textarea_rows' => 15 , 'teeny' =>true); 
							
					
		wp_editor( $content, $editor_id , $editor_settings);
		
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		
		// Return the content you want to the calling function
		return $editor_contents;

	
	
	}
	
	function edit_user_custorm_form ( $user_id)
	{
		
		global  $xoouserultra, $uultra_form;
		
		$html = '';
		
		
		$forms = $uultra_form->get_all();
		
		//get user form		
		$custom_form = $this->get_user_meta_custom( $user_id, 'uultra_custom_registration_form');
		
		$html .='<select name="p_custom_registration_form" id="p_custom_registration_form">';
		
		if($custom_form ==''){$selected = 'selected="selected"';}
		
		$html .='<option value="" '.$selected.'>
					'.__('Default Registration Form','users-ultra').'
				</option>';
				
			
			 if(!empty($forms))
			 {
                
				  foreach ( $forms as $key => $form )
				  {
					  $selected ='';
					  if($custom_form ==$key){$selected = 'selected="selected"';}
					  
					  $html .='<option value="'.$key.'" '.$selected.'>	'. $form['name'].'</option> ';
					
				  }
			  
			 }
                
		$html .='</select>';
		$html .= '<a href="#" class="button-primary uultra-user-edit-customform-confirm" data-user="'.$user_id.'">'.__('Ok','users-ultra').'</a>';
		$html .= '<span id="uultra-customform-conf"></span>';
		
		return $html;
	
	}
	
	public function get_user_groups_editing( $user_id)
	{
		global  $xoouserultra,  $uultra_group;
		$html = null;
		
		$groups = $uultra_group->get_all();
		
		if ( !empty( $groups ) )
		{
			$users_groups = array();
			$users_groups =  $uultra_group->get_all_user_groups($user_id);
			foreach ( $groups as $group )
			{
				$checked = '';
				if (in_array($group->group_id, $users_groups))
				{
					$checked = 'checked="checked"';
				}
				
				$html .= '<input type="checkbox" name="uultra_user_group[]" id="uultra_user_group_'.$group->group_id.'" value="'.$group->group_id.'" '.$check_va.' '.$checked.' /> <label for="uultra_user_group_'.$group->group_id.'"><span></span>'.$group->group_name.'</label> ';
				
			
			}			
				
		}
		
		return $html;
		
		
	}
	
		/* This is the */
	public function edit_profile_form_admin( $user_id )
	{
		global  $xoouserultra, $uultra_group;
		$html = null;
		
		
		// Optimized condition and added strict conditions
		if ($user_id>0) 
		{
			
			$user = get_userdata($user_id);
			
			
			$u_status =  $this->get_user_meta_custom($user_id, 'usersultra_account_status');
			$u_ip =  $this->get_user_meta_custom($user_id, 'uultra_user_registered_ip');
			$u_role =  $this->get_all_user_roles($user_id);
			$u_last_login =   $this->get_user_meta_custom($user_id, 'uultra_last_login');
			$badges = $this->display_optional_fields( $user_id,'only', 'badges'); 
			
			//expiration date
			
			$expiration_date = $this->get_user_meta_custom( $user_id, 'usersultra_membership_expiration'); 
			
			
			
		$html .= '<div class="xoouserultra-clear"></div>';				
		$html .= '<form action="" method="post" id="xoouserultra-profile-edition-form-admin" name="xoouserultra-profile-edition-form-admin">';
		$html .= '<input type="hidden" name="user_id" value="'.$user_id.'">';
		
		
		$html .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show uultra-heading-user-edition"  widget-id="99999">'.__('Account Information', 'users-ultra').'';		
		
		$html .= '<span widget-id="99999" style="background-position: 0px -20px;" id="uultra-user-edition-icon-close-99999" class="uultra-user-editions-icon-close-open"></span>';
		
		$html .='</div>';
		
		
		$date_format = $xoouserultra->get_option('uultra_date_format');
		
		
		$html .= '<div class="" id="uultra-user-edition-block-account-info-99999">';		
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('User ID:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .= $user_id;	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Registration Date:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .= date($date_format,strtotime($user->user_registered));	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Expiration Date:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		
		//expiration date		
		$html .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="uultra-edit-user-expiration-date" id="uultra-edit-user-expiration-date" value="'.$expiration_date.'"  title="'.$name.'"  '.$disabled.'/>';
		
		$html .= '<a href="#" class="button-primary uultra-user-edit-expiration-confirm" data-user="'.$user_id.'">'.__('Change','users-ultra').'</a>';
		$html .= '<span id="uultra-expiration-date-conf"></span>';
		
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Badges:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .=$badges;	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Role:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .= $u_role;	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		 
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Assign Membership','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .= $this->edit_user_package_admin_form( $user_id);	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Assign Custom Form','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		$html .= $this->edit_user_custorm_form( $user_id);	
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		//status
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
		$html .= '<span>'.__('Status:','users-ultra').'</span></label>';
		$html .= '<div class="xoouserultra-field-value">';
		
		$u_status =  $this->get_user_meta_custom( $user_id, 'usersultra_account_status'); 
		
		$u_status_active='';
		$u_status_pending='';
		$u_status_pending_admin='';
		$u_status_pending_payment='';
		
		if($u_status =='active') {$u_status_active='selected="selected"';};
		if($u_status =='pending') {$u_status_pending='selected="selected"';};
		if($u_status =='pending_admin') {$u_status_pending_admin='selected="selected"';};
		if($u_status =='pending_payment') {$u_status_pending_payment='selected="selected"';};
		
		
		$html .= '<select name="uultra_user_status" id="uultra_user_status">               
               <option value="active" '.$u_status_active.'>'.__('Active','users-ultra').'</option>
               <option value="pending" '.$u_status_pending.'>'.__('Pending Confirmation','users-ultra').'</option>
               <option value="pending_admin" '.$u_status_pending_admin.'>'.__('Pending Admin','users-ultra').'</option>
               <option value="pending_payment" '.$u_status_pending_payment.'>'.__('Pending Payment','users-ultra').'</option>
               
          </select>';
		  
		
		$html .= '<a href="#" class="button-primary uultra-user-edit-status-confirm" data-user="'.$user_id.'">'.__('Ok','users-ultra').'</a>';
		$html .= '<span id="uultra-status-conf"></span>';
		
		
		$html .= '</div>';	
		$html .= '</div>';		
		$html .= '<div class="xoouserultra-clear"></div>';
		
		$html .= '</div>';
		
		
		if(isset($uultra_group))
		{
			
			$html .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show uultra-heading-user-edition"  widget-id="990999">'.__('Groups', 'users-ultra').'';		
			
			$html .= '<span widget-id="990999" style="background-position: 0px -20px;" id="uultra-user-edition-icon-close-990999" class="uultra-user-editions-icon-close-open"></span>';
			
			$html .='</div>';
		
		
		
			$html .= '<div class="" id="uultra-user-edition-block-account-info-990999">';
			$html .= '<p>'.__('The user will be added to the selected groups.','users-ultra').'</p>';		
			$html .= $this->get_user_groups_editing($user_id);						
			$html .= '</div>';	
			
			$html .= '</br></br>';
			$html .= '<div class="xoouserultra-clear"></div>';	
			
			
		}
		
		
		$array = array();
		
		//get user form		
		$custom_form = $this->get_user_meta_custom( $user_id, 'uultra_custom_registration_form');
		
		if($custom_form!="")
		{			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);
		
		}else{			
			
			$array = get_option('usersultra_profile_fields');			
		
		}
		
		if(!is_array($array))
		{
			$array = array();
		
		}
		
		//echo $custom_form;
		
		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
		
		
	
		foreach($array as $key => $field) 
		{		

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			if($required == 1 && in_array($field, $xoouserultra->include_for_validation))
			{
			    $required_class = ' required';
			}
			
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0 ) 
			{
				if($sep_open)
				{
					$html .= '</div>'; //close previous sep box
					
					$sep_open = false;
					
				
				}
				
							
				$html .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show uultra-heading-user-edition" widget-id="'.$meta.'">'.$name.'';		
						
				$html .= '<span widget-id="'.$meta.'" style="background-position: 0px -20px;" id="uultra-user-edition-icon-close-'.$meta.'" class="uultra-user-editions-icon-close-open"></span>';				
				$html .= '</div>';
				
				if(!$sep_open)
				{
					$html .= '<div class="" id="uultra-user-edition-block-account-info-'.$meta.'">';
					
					$sep_open = true;
				
				}
				
			}else{
				
				//$sep_open = false;
				
			}
			
			
			if ( $type == 'usermeta' )			
			{
				
				 $show_field_status =  true;
				 
			 if ($show_field_status) 				 
			 {
					 
				
				$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) {
                            $html .= '<i class="fa fa-' . $icon . '"></i>';
                    } else {
                            $html .= '<i class="fa fa-icon-none"></i>';
                    }
											
					$html .= '<span>'.$name.'</span></label>';
					
					
				} else {
					$html .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				}
				
				$html .= '<div class="xoouserultra-field-value">';			
				
				
				
				 $xoouserultra->role->uultra_get_user_roles_by_id($user_id);
				 				
					
					switch($field) {
					
						case 'textarea':
						
						    //check if html editor active
							$html .= $this->get_me_wphtml_editor($meta, $this->get_user_meta_custom( $user_id, $meta));
														
							break;
							
						case 'text':
							$html .= '<input type="text" class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta_custom( $user_id, $meta).'"  title="'.$name.'"  '.$disabled.'/>';
							break;
							
							
						case 'datetime':
						    $html .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta_custom( $user_id,$meta).'"  title="'.$name.'"  '.$disabled.'/>';
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' ) 
							{
								$loop = $xoouserultra->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
							}elseif(isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($choices);
								
							
							}
							
							if (isset($loop)) 
							{
								$html .= '<select class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" '.$disabled.'>';							
								
								
								foreach($loop as $sh) 
								{
									
									$option = trim($option);								    
								    $html .= '<option value="'.$sh.'" '.selected( $this->get_user_meta_custom( $user_id, $meta), $sh, 0 ).' '.$disabled.'>'.$sh.'</option>';
								
								}
								
								$html .= '</select>';
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							
							break;
							
						case 'radio':
						
							if (isset($array[$key]['choices']))
							{
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($choices);
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as  $option) 
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim($option);
									
									$html .= '<label class="xoouserultra-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" '.$disabled.' id="uultra_multi_radio_'.$meta.'_'.$counter.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_user_meta_custom( $user_id,$meta), $option, 0 );
									$html .= '/> <label for="uultra_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label> </label>';
									
									$counter++;
									
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
							if (isset($array[$key]['choices'])) 
							{
								
																
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($choices);
								
						
								
							}
							if (isset($loop) && $loop[0] != '') {
							  $counter =0;
								foreach($loop as $option) {
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim($option);
									$html .= '<div class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="uultra_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" '.$disabled.' ';
									
									
									$values = explode(',', $this->get_user_meta_custom( $user_id, $meta));
									
									if (in_array($option, $values)) {
										
									$html .= 'checked="checked"';
									}
									$html .= '/> <label  for="uultra_multi_box_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label></div>';
									
									$counter++;
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
						
							
					}
					
					
						//get meta
						$check_va = "";
						$ischecked = $this->get_user_meta_custom( $user_id,"hide_".$meta);
						//echo "meta: ".$ischecked ;
						 
						 if($ischecked==1) $check_va = 'checked="checked"';
						
						$html .= '<div class="xoouserultra-hide-from-public">
										<input type="checkbox" name="hide_'.$meta.'" id="hide_'.$meta.'" value="1" '.$check_va.' /> <label for="hide_'.$meta.'"><span></span>'.__('Hide from Public','users-ultra').'</label>
									</div>';

					
					
				$html .= '</div>';
				$html .= '</div><div class="xoouserultra-clear"></div>';
				
				} //end if roles
				
			} //end if user meta
		}
		
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">
						<label class="xoouserultra-field-type xoouserultra-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="xoouserultra-field-value">
						    <input type="hidden" name="xoouserultra-profile-edition-form-admin" value="xoouserultra-profile-edition-form-admin" />
							<input type="submit" name="xoouserultra-update" id="xoouserultra-update" class="xoouserultra-button" value="'.__('Update','users-ultra').'" />
						</div>
					</div><div class="xoouserultra-clear"></div>';
					
		
		$html .= '</form>';
		
		} // End of the Profile Edition Function
		
		return $html;
	}
	
	/* This is the */
	public function edit_profile_form( $sidebar_class=null, $redirect_to=null )
	{
		global  $xoouserultra;
		$html = null;
		
		$user_id = get_current_user_id();
		
		// Optimized condition and added strict conditions
		if (!isset($xoousers_register->registered) || $xoousers_register->registered != 1) 
		{
			
			
		$html .= '<div class="xoouserultra-clear"></div>';				
		$html .= '<form action="" method="post" id="xoouserultra-profile-edition-form">';
		
		
		$array = array();
		//get user form		
		$custom_form = $this->get_user_meta( 'uultra_custom_registration_form');
		
		if($custom_form!="")
		{			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);
		
		}else{			
			
			$array = get_option('usersultra_profile_fields');			
		
		}
		
		//echo $custom_form;
		
		if(!is_array($array))
		{
			$array = array();
		
		}
		
		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
	
		foreach($array as $key => $field) 
		{
			//echo "<pre>".print_r($field) . "</pre>";
			
			$show_to_user_role_list = '';
			$show_to_user_role = 0;			
			$edit_by_user_role = 0;
			$edit_by_user_role_list = '';	

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			if($required == 1 && in_array($field, $xoouserultra->include_for_validation))
			{
			    $required_class = ' required';
			}
			
			
					
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0 ) 
			{
				if(!isset($show_to_user_role) || $show_to_user_role =="")
				{
					$show_to_user_role = 0;			
				}
				
				if(!isset($show_to_user_role_list) || $show_to_user_role_list =="")
				{
					$show_to_user_role_list = '';	
					
				}
				
				$xoouserultra->role->uultra_get_user_roles_by_id($user_id);
				$show_field_status =  $xoouserultra->role->uultra_fields_by_user_role($show_to_user_role, $show_to_user_role_list);
				
				if ($show_field_status) 				 
			 	{
					$html .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$name.'</div>';
					
				}
				 
				 
				
			}
			
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0)			
			{
	
			
				if(!isset($show_to_user_role) || $show_to_user_role =="")
				{
					$show_to_user_role = 0;			
				}
				
				if(!isset($show_to_user_role_list) || $show_to_user_role_list =="")
				{
					$show_to_user_role_list = '';	
					
					
				}else{
					
				
				}
				
			 
			 
				 $xoouserultra->role->uultra_get_user_roles_by_id($user_id);
				 $show_field_status =  $xoouserultra->role->uultra_fields_by_user_role($show_to_user_role, $show_to_user_role_list);
				 
		 
				
			 if ($show_field_status) 				 
			 {
					 
				
				$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) {
                            $html .= '<i class="fa fa-' . $icon . '"></i>';
                    } else {
                            $html .= '<i class="fa fa-none"></i>';
                    }
											
					$html .= '<span>'.$name.' '.$required_text.' </span></label>';
					
					
															
					
					
				} else {
					$html .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				}
				
				$html .= '<div class="xoouserultra-field-value">';
				
				
				
				if ($can_edit == 0)
				{
					
                     $disabled = 'disabled="disabled"';
					 
			 	}else{
                     
					  $disabled = null;
                }
				
				
				if(!isset($edit_by_user_role) || $edit_by_user_role =="")
				{
					$edit_by_user_role = 0;			
				}
				
				if(!isset($edit_by_user_role_list) || $edit_by_user_role_list =="")
				{
					$edit_by_user_role_list = '';	
					
				}
				
				 $xoouserultra->role->uultra_get_user_roles_by_id($user_id);
				 $edit_field_status =  $xoouserultra->role->uultra_fields_by_user_role($edit_by_user_role, $edit_by_user_role_list);
				 
				 if (!$edit_field_status) {
					 
					  $disabled = 'disabled="disabled"';
					 
				 }
					
				
					
					switch($field) {
					
						case 'textarea':
						
						    //check if html editor active
							$html .= $this->get_me_wphtml_editor($meta, $this->get_user_meta( $meta));
							
							break;
							
						case 'text':
							$html .= '<input type="text" class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'"  '.$disabled.'/>';
							
														
							break;
							
							
						case 'datetime':
						    $html .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'"  '.$disabled.'/>';
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' ) 
							{
								$loop = $xoouserultra->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
							}elseif(isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
							
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($array[$key]['choices']);
								
							
							}
							
							if (isset($loop)) 
							{
								$html .= '<select class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" '.$disabled.'>';							
								
								
								foreach($loop as $sh) 
								{
									
									$option = trim($option);								    
								    $html .= '<option value="'.$sh.'" '.selected( $this->get_user_meta( $meta), $sh, 0 ).' '.$disabled.'>'.$sh.'</option>';
								
								}
								
								$html .= '</select>';
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							
							break;
							
						case 'radio':
						
							if (isset($array[$key]['choices']))
							{
								
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($choices);
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as  $option) 
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim($option);
									
									$html .= '<label class="xoouserultra-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" '.$disabled.' id="uultra_multi_radio_'.$meta.'_'.$counter.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_user_meta( $meta), $option, 0 );
									$html .= '/> <label for="uultra_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label> </label>';
									
									$counter++;
									
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
							if (isset($array[$key]['choices'])) 
							{
								
								$loop = $xoouserultra->uultra_one_line_checkbox_on_window_fix($choices);
								
								
							}
							if (isset($loop) && $loop[0] != '') {
							  $counter =0;
								foreach($loop as $option) {
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim($option);
									$html .= '<div class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="uultra_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" '.$disabled.' ';
									
									
									$values = explode(',', $this->get_user_meta($meta));
									
									if (in_array($option, $values)) {
										
									$html .= 'checked="checked"';
									}
									$html .= '/> <label  for="uultra_multi_box_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label></div>';
									
									$counter++;
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
					}
					
					if (isset($array[$key]['help_text']) && $help_text != '') 
					{
						$html .= '<div class="xoouserultra-help">'.$help_text.'</div><div class="xoouserultra-clear"></div>';
					}
					
					//print_r($array[$key]);
					
					/*User can hide this from public*/
					if (isset($array[$key]['can_hide']) && $can_hide == 1) {
						
						//get meta
						$check_va = "";
						$ischecked = $this->get_user_meta("hide_".$meta);
						//echo "meta: ".$ischecked ;
						 
						 if($ischecked==1) $check_va = 'checked="checked"';
						
						$html .= '<div class="xoouserultra-hide-from-public">
										<input type="checkbox" name="hide_'.$meta.'" id="hide_'.$meta.'" value="1" '.$check_va.' /> <label for="hide_'.$meta.'"><span></span>'.__('Hide from Public','users-ultra').'</label>
									</div>';

					} elseif ($can_hide == 0 && $private == 0) {
						
						
					   
					}
					
				$html .= '</div>';
				$html .= '</div><div class="xoouserultra-clear"></div>';
				
				} //end if roles
				
			} //end if user meta
		}
		
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">
						<label class="xoouserultra-field-type xoouserultra-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="xoouserultra-field-value">
						    <input type="hidden" name="xoouserultra-profile-edition-form" value="xoouserultra-profile-edition-form" />
							<input type="submit" name="xoouserultra-update" id="xoouserultra-update" class="xoouserultra-button" value="'.__('Update','users-ultra').'" />
						</div>
					</div><div class="xoouserultra-clear"></div>';
					
		
		$html .= '</form>';
		
		} // End of the Profile Edition Function
		
		return $html;
	}
	
	
	/*Get All Packages for Upgrade */
	public function get_change_role_my_account ()
	{
		global $wpdb,  $xoouserultra;
		
		
		$user_id = get_current_user_id();
		
		
		$html = '';
		
		if($xoouserultra->get_option('uultra_roles_actives_backend')=='yes')
		{
			
			//text to display
			$label_for_role = $this->get_option('label_for_registration_user_role');
			$label_for_role_1 = $this->get_option('label_for_registration_user_role_1');
			$custom_text = $this->get_option('uultra_roles_actives_backend_text');
			
			
			
			if($label_for_role =="")
			{
				$label_for_role = __('Select your Role','users-ultra');
			
			}
			
			if($label_for_role_1 =="")
			{
				$label_for_role_1 = __('Role','users-ultra');
			
			}			
			
			
			 
			$html .= '  <div class="commons-panel-content">';					
			$html .= ' <h2>'. $label_for_role.'</h2>';			
			$html .= $custom_text ;	
			
			$html .= '<p>';
			$html .= $xoouserultra->role->get_private_roles_registration($user_id);	
			$html .= '</p>';	
						
						
			$html .= '<p><input type="submit" name="xoouserultra-change-user-role-backend" id="xoouserultra-change-user-role-backend" class="xoouserultra-button" value="'.__('SUBMIT','users-ultra').'" /></p>';
			
			$html .= '<p id="uultra-change-role-confmsg" style="display:none"></p>';
					
			$html .= "</div>" ;
			
			
			
		
		}
		
		return $html;
		
	}
	
	
	/*Update Profile from admin*/
	function update_me_admin() 
	{
		global  $xoouserultra, $uultra_group;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
	
		$user_id = $_POST['user_id'];
		
		//get user form		
		$custom_form = $this->get_user_meta_custom($user_id, 'uultra_custom_registration_form');
		
		if($custom_form!="")
		{			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);
		
		}else{			
			
			$array = get_option('usersultra_profile_fields');			
		
		}
		
		$array_check = $array;
		
		
		 // Get list of dattime fields
        $date_time_fields = array();

        foreach ($array as $key => $field) 
		{
            extract($field);

            if (isset($array[$key]['field']) && $array[$key]['field'] == 'checkbox') 
			{
				//echo "is meta field: " .$meta;
                update_user_meta($user_id, $meta, null);
            }

            // Filter date/time custom fields
            if (isset($array[$key]['field']) && $array[$key]['field'] == 'datetime')
			{
                array_push($date_time_fields, $array[$key]['meta']);
            }
			
			
        }
			
			/* Check if the were errors before updating the profile */
			if (!isset($this->errors)) 
			{
				/* Now update all user meta */
				foreach($this->usermeta as $key => $value) 
				{
					// save checkboxes
                    if (is_array($value)) 
					{ // checkboxes
                        $value = implode(',', $value);
                    }
					//echo $key. " ";
					update_user_meta($user_id, "hide_".$key, "");
					
					
					if($key=="display_name")
					{
						wp_update_user( array( 'ID' => $user_id, 'display_name' => esc_attr($value) ) );
					}
					
										
					if ($this->field_allow_html($key,$array_check)) 
					{
						update_user_meta($user_id, $key, $value);
						
					}else{
						
						update_user_meta($user_id, $key, esc_attr($value));
					
					
					}				
			
						
				}
				
				//upate activity
				
								
			}
			
			
			//update user groups
			if($user_id !='' && isset($uultra_group))
			{
				//delete user's groups					
				$uultra_group->groups_and_users_rel_del($user_id);
					
				$groups = array();
				
				if(isset($_POST['uultra_user_group']))
				{
					$groups = $_POST['uultra_user_group'];					
					
					foreach ($groups as $group ) 
					{
						//add user to group
						$uultra_group->save_user_group_rel($user_id, $group);
						
					}
				
				}		
			
			}
			
			
			
	}
	
	/*Update Profile*/
	function update_me() 
	{
		global  $xoouserultra;
		
	
		$user_id = get_current_user_id();		
		$logged_in_user = get_user_by('id',$user_id);
		
		//get user form		
		$custom_form = $this->get_user_meta( 'uultra_custom_registration_form');
		
		$array = array();
		
		if($custom_form!="")
		{
			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);
		
		}else{			
			
			$array = get_option('usersultra_profile_fields');			
		
		}
		
		$array_check = $array;		
		if($xoouserultra->get_option('notify_admin_when_profile_updated')=='yes')		
		{
			
			$xoouserultra->messaging->notify_admin_profile_change($logged_in_user);		
		
		}		
		
		 // Get list of dattime fields
        $date_time_fields = array();
		
		if(!is_array($array)){$array=array();}

        foreach ($array as $key => $field) 
		{
            extract($field);

            if (isset($array[$key]['field']) && $array[$key]['field'] == 'checkbox') 
			{
				//echo "is meta field: " .$meta;
                update_user_meta($user_id, $meta, null);
            }

            // Filter date/time custom fields
            if (isset($array[$key]['field']) && $array[$key]['field'] == 'datetime')
			{
                array_push($date_time_fields, $array[$key]['meta']);
            }
			
			
        }
			
			/* Check if the were errors before updating the profile */
			if (!isset($this->errors)) 
			{
				/* Now update all user meta */
				foreach($this->usermeta as $key => $value) 
				{
					// save checkboxes
                    if (is_array($value)) 
					{ // checkboxes
                        $value = implode(',', $value);
                    }
					//echo $key. " ";
					update_user_meta($user_id, "hide_".$key, "");
					
					if($key=="display_name")
					{
						wp_update_user( array( 'ID' => $user_id, 'display_name' => esc_attr($value) ) );
					}
					
					
					if ($this->field_allow_html($key,$array_check)) 
					{
						update_user_meta($user_id, $key, $value);
						
					}else{
						
						update_user_meta($user_id, $key, esc_attr($value));
					
					
					}				
			
						
				}
				
				//upate activity
				
								
			}
			
	}
	
	function field_allow_html ($field_to_check, $fields_set)
	{
		
		foreach ($fields_set as $key => $field) 
		{
            extract($field);
			
			if($meta==$field_to_check)
			{
				
				if (isset($allow_html) && $allow_html == '1') 
				{
					return true;
					
				}else{
					
					return false;
				
				
				}  			
			} 		                    		
			
        }
		
		return false;
	
	}
	
	/*Post value*/
	function get_post_value($meta) 
	{
				
		if (isset($_POST['xoouserultra-register-form'])) {
			if (isset($_POST[$meta]) ) {
				return $_POST[$meta];
			}
		} else {
			if (strstr($meta, 'country')) {
			return 'United States';
			}
		}
	}
	
	
	/******************************************
	Get user by ID, username
	******************************************/
	function get_user_data_by_uri() 
	{
		
		global  $xoouserultra, $wpdb;	
		
		
		$u_nick = get_query_var('uu_username');
		
		if($u_nick=="") //permalink not activated
		{
			$u_nick=$this->parse_user_id_from_url();	
			
		}
		
		
		
		$nice_url_type = $xoouserultra->get_option('usersultra_permalink_type');
			
		
		if ($nice_url_type == 'ID' || $nice_url_type == '' ) 
		{
			
			$user = get_user_by('id',$u_nick);				
			
		}elseif ($nice_url_type == 'username') {			
						
			$user = get_user_by('slug',$u_nick);
		
		}elseif ($nice_url_type == 'user_nicename') {			
						
			$user = get_user_by('slug',$u_nick);
				
		}
			
		return $user;
	}
	
	public function get_display_name($user_id)
	{
		global  $xoouserultra;
		
		$display_name = "";
		
		$display_type = $xoouserultra->get_option('uprofile_setting_display_name');
		$display_type = 'display_name';
		
		$user = get_user_by('id',$user_id);
		
		if ($display_type == 'fr_la_name' || $display_type == '' ) 
		{
			$f_name = get_user_meta($user_id, 'first_name', true);
	        $l_name = get_user_meta($user_id, 'last_name', true);	
			
			$display_name = $f_name. " " .  $l_name;			
			
		}elseif ($display_type == 'username') {
				
			$display_name =$user->user_login;
		
		}elseif ($display_type == 'user_nicename') {
				
			$display_name =$user->user_nicename;
		
		
		}elseif ($display_type == 'display_name') {
			
			
			$display_name =$user->display_name;
							
				
		}
		
		
		return ucfirst($display_name);
	
	
	}
	
	
	
	
	/*Prepare user meta*/
	function prepare ($array )
	{
		
		foreach($array as $k => $v) 
		{
			if ($k == 'usersultra-update' || $k == 'xoouserultra-profile-edition-form'  ) continue;
			
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function handle() 
	{
	   
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
	}
	
	public function get_user_info()
	{
		$current_user = wp_get_current_user();
		return $current_user;

		
	}
	
	/******************************************
	Get permalink for user
	******************************************/
	function get_user_profile_permalink( $user_id=0) 
	{
		
		global  $xoouserultra;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
	
		
				
		if ($user_id > 0) 
		{
		
			$user = get_userdata($user_id);
			$nice_url_type = $xoouserultra->get_option('usersultra_permalink_type');
			
						
			if ($nice_url_type == 'ID' || $nice_url_type == '' ) 
			{
				$formated_user_login = $user_id;
			
			}elseif ($nice_url_type == 'username') {
				
				$formated_user_login = $user->user_nicename;
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			
			}elseif ($nice_url_type == 'name'){
				
				$formated_user_login = $xoouserultra->get_fname_by_userid( $user_id );
			
			}elseif ($nice_url_type == 'display_name'){
				
				$formated_user_login = get_user_meta( $user_id, 'display_name', true);					
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			
			}elseif ($nice_url_type == 'custom_display_name'){
				
				$formated_user_login = get_user_meta( $user_id, 'display_name', true);					
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			
							
				
			}
			
			$formated_user_login = strtolower ($formated_user_login);
			$profile_page_id = $xoouserultra->get_option('profile_page_id');
		    			

			/* append permalink */
			if ( $xoouserultra->get_option('usersultra_permalink_type') == '' )
			{
				$link = add_query_arg( 'uu_username', $formated_user_login, get_page_link($profile_page_id) );
				
			}else{
				
				$link = trailingslashit ( trailingslashit( get_page_link($profile_page_id) ) . $formated_user_login );
				
			}
		
		} else {
			$link = get_page_link($page_id);
		}

		return $link;
	}
	
	function parse_user_id_from_url()
	{
		$user_id="";
		
		if(isset($_GET["page_id"]) && $_GET["page_id"]>0)
		{
			$page_id = $_GET["page_id"];
			$user_id = $this->extract_string($page_id, '/', '/');
		
		
		}
		
		return $user_id;
		
	
	}
	
	function extract_string($str, $start, $end)
		{
		$str_low = $str;
		$pos_start = strpos($str_low, $start);
		$pos_end = strpos($str_low, $end, ($pos_start + strlen($start)));
		if ( ($pos_start !== false) && ($pos_end !== false) )
		{
		$pos1 = $pos_start + strlen($start);
		$pos2 = $pos_end - $pos1;
		return substr($str, $pos1, $pos2);
		}
	}
	
	/**
	Get Internatl Menu Links
	******************************************/
	public function get_internal_links($slug, $slug_2, $id)
	{
		$url = "";
			
			if(!isset($_GET["page_id"]) && !isset($_POST["page_id"]) )
			{
				$url = '?module='.$slug.'&'.$slug_2.'='. $id.'';	
				
			}else{
				
				if(isset($_GET["page_id"]) )
			    {
					
					$page_id = $_GET["page_id"];
				
				}else{
					
					$page_id = $_POST["page_id"];
					
				}
				
				
				$url = '?page_id='.$page_id.'&module='.$slug.'&'.$slug_2.'='. $id.'';			
			
			}
			
		
		return $url;	
		
	
	}
	
	/**
	Get Internal Messaging Menu Links
	******************************************/
	public function get_internal_pmb_links($slug, $slug_2, $id)
	{
		$url = "";
			
			if(!isset($_GET["page_id"]) && !isset($_POST["page_id"]) )
			{
				$url = '?module='.$slug.'&'.$slug_2.'='. $id.'';	
				
			}else{
				
				if(isset($_GET["page_id"]) )
			    {
					
					$page_id = $_GET["page_id"];
				
				}else{
					
					$page_id = $_POST["page_id"];
					
				}
				
				
				$url = '?page_id='.$page_id.'&module='.$slug.'&'.$slug_2.'='. $id.'';			
			
			}
			
		
		return $url;	
		
	
	}
	
	public function build_user_menu_navigator()
	{
		global $xoouserultra;
		
		$html="";
		
		
	}
	
	
	/**
	Get Menu Links
	******************************************/
	public function get_user_backend_menu($slug, $menu_item_id = null)
	{
		global $xoouserultra;
		
		$url = "";
		
		if($slug=="dashboard")
		{
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=dashboard"><span><i class="fa fa-tachometer fa-2x"></i></span>'.__('Dashboard', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=dashboard"><span><i class="fa fa-tachometer fa-2x"></i></span>'.__('Dashboard', 'users-ultra').'</a>';			
			
			}
			
		}elseif($slug=="profile"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="profile-customizer"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=profile-customizer"><span><i class="fa fa-puzzle-piece fa-2x"></i></span>'.__('Profile Customizer', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=profile-customizer"><span><i class="fa fa-puzzle-piece fa-2x"></i></span>'.__('Profile Customizer', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="account"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=account"><span><i class="fa fa-wrench  fa-2x"></i></span>'.__('My Account', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=account"><span><i class="fa fa-wrench  fa-2x"></i></span>'.__('My Account', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="settings"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=settings"><span><i class="fa fa-gear  fa-2x"></i></span>'.__('Settings', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=settings"><span><i class="fa fa-gear  fa-2x"></i></span>'.__('Settings', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="wootracker"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=wootracker"><span><i class="fa fa-truck   fa-2x"></i></span>'.__('My Purchases', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=wootracker"><span><i class="fa fa-truck   fa-2x"></i></span>'.__('My Purchases', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="myorders"){			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=myorders"><span><i class="fa fa-list   fa-2x"></i></span>'.__('My Orders', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=myorders"><span><i class="fa fa-list   fa-2x"></i></span>'.__('My Orders', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="messages"){
			
			//check if unread replies or messages			
			$user_id = get_current_user_id();
			$total = $xoouserultra->mymessage->get_unread_messages_amount($user_id);	
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=messages"><span><i class="fa fa-envelope-o fa-2x"></i></span>'.__('My Messages', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=messages"><span><i class="fa fa-envelope-o fa-2x"></i></span>'.__('Messages', 'users-ultra').'</a>';			
			
			}
			
			if($total>0)
			{
				$url .= '<div class="uultra-noti-bubble" title="'.__('Unread Messages', 'users-ultra').'">'.$total.'</div>';
			
			}
			
			
		
		}elseif($slug=="photos"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=photos"><span><i class="fa fa-camera fa-2x"></i></span>'.__('Photos', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=photos"><span><i class="fa fa-camera fa-2x"></i></span>'.__('Photos', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="videos"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=videos"><span><i class="fa fa-video-camera fa-2x"></i></span>'.__('My Videos', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=videos"><span><i class="fa fa-video-camera fa-2x"></i></span>'.__('My Videos', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="friends"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=friends"><span><i class="fa fa-users fa-2x"></i></span>'.__('My Friends', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=friends"><span><i class="fa fa-users fa-2x"></i></span>'.__('My Friends', 'users-ultra').'</a>';			
			
			}
		
		
		}elseif($slug=="posts"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=posts"><span><i class="fa fa-edit fa-2x"></i></span>'.__('My Posts', 'users-ultra').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=posts"><span><i class="fa fa-edit fa-2x"></i></span>'.__('My Posts', 'users-ultra').'</a>';			
			
			}
		
		}elseif($slug=="logout"){		
							
		     $url = '<a class="uultra-btn-u-menu" href="'.$xoouserultra->get_logout_url().'"><span><i class="fa fa-arrow-circle-right fa-2x"></i></span>'.__('Logout', 'users-ultra').'</a>';			
			
			
				
		}
		
		return $url;	
		
	
	
	}
	
	/**
	Get Menu Links
	******************************************/
	public function get_user_backend_menu_new($module, $menu_item_id = null)
	{
		global $xoouserultra;
		
		$url = "";
		
	
		$slug = $module["slug"];
		$link_type = $module["link_type"];
		
		$uri = $this->uultra_build_user_menu_uri($slug, $link_type);	
		
		$url = '<a class="uultra-btn-u-menu" href="'.$uri.'"><span><i class="fa '.$module["icon"].' fa-2x"></i></span><span class="uultra-user-menu-text">'.$module['title'].'</span></a>';	
				
		//messsages
		if($module["slug"]=='messages')
		{
			//check if unread replies or messages			
			$user_id = get_current_user_id();
			$total = $xoouserultra->mymessage->get_unread_messages_amount($user_id);
			
			if($total>0)
			{
				$url .= '<div class="uultra-noti-bubble" title="'.__('Unread Messages', 'users-ultra').'">'.$total.'</div>';			
			}			
		
		}
		
		//friends
		if($module["slug"]=='friends')
		{
			//check if unread replies or messages			
			$user_id = get_current_user_id();
			$total = $xoouserultra->social->get_total_friend_request($user_id);			
			if($total>0)
			{
				$url .= '<div class="uultra-noti-bubble" title="'.__('Friend Requests', 'users-ultra').'">'.$total.'</div>';			
			}			
		
		}
		return $url;	
		
	
	}
	
	function uultra_build_user_menu_uri($slug, $link_type)
	{
		global $xoouserultra;
		$uri = "";
		
		//get slug
		$custom_module_slug  = $xoouserultra->get_option('uultra_custom_module_slug');
		
		if(!isset($_GET["page_id"]))
		{
			$uri = '?module='.$slug;
			
		}else{
						
			$uri = '?page_id='.$_GET["page_id"].'&module='.$slug;
			
		}
		
		if($link_type=='custom')
		{
			$uri = '?custom-module='.$slug;
		
		
		}
		
		if($slug=='logout')
		{
			$uri = $xoouserultra->get_logout_url();
		
		}
		
		return $uri;
	
	}
	
	function get_profile_user_id ()
	{
		$user_id = '';
		
		//get current user			
		$current_user = $this->get_user_data_by_uri();
				
		if(isset($current_user->ID))
		{
			$user_id = $current_user->ID;				
				
		}		
				
		//check if logged in and seeing my own profile
		if (is_user_logged_in() && $user_id=="") 
		{					
			$user_id=get_current_user_id(); 
			$current_user = get_user_by('id',$user_id);
				
		}
		
		return $user_id;
	
	}
	
	/**
	Display Public Profile
	******************************************/
	public function show_public_profile($atts)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/user.php');
		
		extract( shortcode_atts( array(
		
			'template' => 'profile', //this is the template file's name	
			'user_id' => '', //this is the template file's name	
			'template_width' => '', //this is the template width			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'fixed', // dynamic or fixed	
			'pic_size' => 120, // size in pixels of the user's picture				
			'gallery_type' => '', // lightbox or single page for each photo				
			'media_options_exclude' => '', // rating, description, tags, category
			
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'optional_right_col_fields_to_display' => '', 
			'profile_fields_to_display' => '', // all or empty
			
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_social' => 'yes', // display social
			'display_photo_rating' => 'yes', // display social	
			'display_photo_title' => 'yes', //yes or no
			'display_photo_description' => 'yes', //yes or no
			'display_gallery_rating' => 'yes', // 
			'display_private_message' => 'yes', // 
			'display_photo_title_on_profile' => 'yes', //
			'display_gallery_title_on_profile' => 'yes', //
			'disable_navigation_bar' => 'yes', //
			
			
			
			
		), $atts ) );
		
		//exclude modules	
		$modules = array();
		$modules  = explode(',', $media_options_exclude);	
		
		
		$display_gallery = false;
		if(isset($_GET["gal_id"]))
		{
			$display_gallery = true;			
			$gal_id = $_GET["gal_id"];				
		
		}
		
		$display_photo = false;
		if(isset($_GET["photo_id"]))
		{
			$display_photo = true;			
			$photo_id = $_GET["photo_id"];				
		
		}
		
		if($template_width !='')
		{
			$this->mFancyTemplateWidth = $template_width ;
		
		}
		
		//check if it's a shortcode call
		
		if($user_id!="") // a shortocode attribute has been submited
		{
			
			$current_user = get_user_by('id',$user_id);
			
		}else{
			
			
			
				//get current user			
				$current_user = $this->get_user_data_by_uri();
				
				if(isset($current_user->ID))
				{
					$user_id = $current_user->ID;				
				
				}		
				
				//check if logged in and seeing my own profile
				if (is_user_logged_in() && $user_id=="") 
				{					
					$user_id=get_current_user_id(); 
					$current_user = get_user_by('id',$user_id);
				
				}
				
				
				//update stats for this user
				if($user_id>0)
				{
					$xoouserultra->statistc->update_hits($user_id, 'user');				
				
				}
		
		
		}	
		
		//check visibility settings		
		$photos_available = $this->do_logged_validation();
		
				
		$display_inactive = $xoouserultra->get_option('uultra_display_not_confirmed_profiles');
		
		//validate display rule		
		if($display_inactive==0 && !$this->is_active($user_id))
		{
			$display = false;
		
		}else{
			
			$display = true;		
		
		}
		
		
		if($user_id>0 && $display )
		{
			$xoouserultra->customizer->uultra_is_paid_user($user_id);			
			$current_template = $xoouserultra->customizer->get_default_profile_template();					
			
			if($current_template==1 || $current_template=="") // 3 columns
			{	
									
				//get template
				$cols = array(1,2,3);
				$html = $this->get_basic_template($user_id, $atts, $display_country_flag, $display_photo_rating,$display_photo_description, $display_photo_title, $display_photo_title_on_profile, $display_gallery_title_on_profile, $gallery_type, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type, $optional_fields_to_display, $cols, $disable_navigation_bar);			
				return $html;
			
			}
			
			if($current_template==3) //two cols
			{	
				$cols = array(1,2);
									
				//get template
				$html = $this->get_basic_template($user_id, $atts, $display_country_flag, $display_photo_rating,$display_photo_description, $display_photo_title, $display_photo_title_on_profile, $display_gallery_title_on_profile,$gallery_type, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type, $optional_fields_to_display, $cols, $disable_navigation_bar);			
				return $html;
			
			}
			
			if($current_template==4) //one col
			{
				$cols = array(1);									
				//get template
				$html = $this->get_basic_template($user_id, $atts, $display_country_flag, $display_photo_rating,$display_photo_description, $display_photo_title,  $display_photo_title_on_profile, $display_gallery_title_on_profile,$gallery_type, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type, $optional_fields_to_display, $cols, $disable_navigation_bar);			
				return $html;
			
			}
			
			if($current_template==2 ) //basic
			{	
									
			
				//turn on output buffering to capture script output
				ob_start();				
				require_once(xoousers_path.'/templates/'.xoousers_template."/".$template.".php");				
				$content = ob_get_clean();
				//ob_end_clean();				
				return $content ;
			
			}
			
			
				
		}elseif($user_id>0 && !$display){
			
			
			$display_inactive = $xoouserultra->get_option('uultra_display_not_confirmed_profiles_message');		
			$html = '<p>'.$display_inactive.'</p>';
			return $html ;		
			
					
		}else{
			
			//user not found
			echo do_shortcode("[usersultra_login]");
			
		}
		
					
			
		
		
	}
	
	public function get_fancy_template_style($part)
	{
		global $xoouserultra;	
		//style customizing		
		$profile_customizing = array();
		$profile_customizing = $xoouserultra->customizer->get_profile_customizing();
		$style = 'style="';
		
		if($part=='main_cont')
		{
			
			if($profile_customizing['uultra_profile_bg_color']!="")
			{
				$style .= 'background-color:'.$profile_customizing['uultra_profile_bg_color'].' !important';
				$added = true;
				
			}
			
			if(isset($this->mFancyTemplateWidth) && $this->mFancyTemplateWidth!='')
			{
				if($added){
					
					$separator = " ; ";				
				}
				
				$style .= $separator.'max-width: '.$this->mFancyTemplateWidth.' !important;  width:'.$this->mFancyTemplateWidth.' !important';			
			}
			
		}elseif($part=='inferior_cont'){
			
			if($profile_customizing['uultra_profile_inferior_bg_color']!="")
			{
				$style .= 'background-color:'.$profile_customizing['uultra_profile_inferior_bg_color'].' !important';
				
			}
		
		}elseif($part=='user_prof_bg_color'){
			
			if($profile_customizing['uultra_profile_image_bg_color']!="")
			{
				$style .= 'background-color:'.$profile_customizing['uultra_profile_image_bg_color'].' !important';
				
			}
			
			
		}
		
		
		
		$style .= '"';		
		return $style;
	
	}
	
	//this functions builds the front-end profile's navigator	
	function get_profile_navitagor_links($user_id)
	{
		
		global $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$html = '';		
				
	
		$modules = $xoouserultra->customizer->uultra_get_front_profile_navigator_membership();	
		
		 //check if bbPress active				   
		 $options_to_display = $xoouserultra->get_option('uulltra_bbp_modules');		
				
		foreach($modules as $key => $module)
		{
			
			if($key==7 && $xoouserultra->get_option('uulltra_bbp_status')!='1')
			{				
				continue;				
			
			}
			
			//is available by the admin?
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id,$key))
			{
				$stats = $this->get_front_link_stats($user_id, $key);
				$html .=' <li><p class="cat"><a href="?'.$module['slug'].'">'.$module['title'].'</a></p>
					
                          <p class="number">'.$stats.'</p>
                    </li>';				
			}  
			
				
			
		}
		
		return $html;
	
	}
	
	function get_front_link_stats($user_id, $key)
	{
		
		global $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$html = '';
		
		$post_type = 'post';
		
		if($key==1) //followwers
		{					
			//total followers
			$html =  $xoouserultra->social->get_followers_total($user_id);
		
		}elseif($key==2){ // Following
		
			//total following
			$html =  $xoouserultra->social->get_following_total($user_id);
		
		}elseif($key==3){ // Photos
		
			//total photos
			$html =  $xoouserultra->photogallery->get_total_photos($user_id);
		
		}elseif($key==4){ // Videos
		
			//total videos
			$html =  $xoouserultra->photogallery->get_total_videos($user_id);
		
		}elseif($key==5){ // Posts
		
			//total posts
			$html = $xoouserultra->publisher->count_user_posts_published($user_id, $post_type);
		
		}elseif($key==6){ // Friends
		
			//total friends
			$html =  $xoouserultra->social->get_friends_total($user_id);
		
		}elseif($key==7){ // Topics
		
			//total topics
			$html = $xoouserultra->bbpress->count_user_posts_published($user_id, "topic");
			
		}	
		
		
		return $html;
	
	}
	
	//this functions builds the front-end profile's TOP navigator	
	function get_top_profile_navigator_links($user_id)
	{
		
		global $xoouserultra, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/load.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$html = '';
		
		
		
		//my id
		$my_id = get_current_user_id();
		
		if ($my_id != $user_id) { //great idea sugested by http://www.usersultra.com/support/profile/snedkeren/
		
			$html = '<ul class="uultra-inner-nav">';		
					
			//$modules = get_option('userultra_default_user_profile_front_navigator');	
			
			$modules = $xoouserultra->customizer->uultra_get_front_profile_navigator_membership($this->mIsPaidMembership);		
			
			
			
			$html .='<li><a href="?my_profile" title="'.__('Profile','users-ultra').'"><i class="fa fa-lg fa-user uultra-icon-menu"></i><span class="uultra-top-nav-labels-resp">'.__('Profile','users-ultra').'</span></a></li>  '; 
			
			//is available by the admin?
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id,8))
			{
				$html .='<li><a href="#" id="uultra-send-private-message-box" user-id="'.$user_id.'" title="'.__('Send Message','users-ultra').'" ><i class="fa fa-lg fa-envelope uultra-icon-menu"></i><span class="uultra-top-nav-labels-resp">'.__('Send Message','users-ultra').'</span></a></li> ';
			
			}
			
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id, 1) || $xoouserultra->customizer->user_front_nav_menu_allowed($user_id, 2))
			{
				
				$html .='<li><a href="#" user-id="'.$user_id.'" id="uu-follow-request-header" title="'.__('Follow','users-ultra').'"><i class="fa fa-lg fa-eye uultra-icon-menu"></i><span class="uultra-top-nav-labels-resp">'.__('Follow','users-ultra').'</span></a></li>';
				
			}
			
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id, 6))
			{
				$html .='<li><a id="uu-send-friend-request" href="#" user-id="'.$user_id.'" title="'.__('Send Friend Request','users-ultra').'"><i class="fa fa-lg fa-retweet uultra-icon-menu"></i><span class="uultra-top-nav-labels-resp">'.__('Send Friend Request','users-ultra').'</span></a></li>   '; 
				 
			}		
			
			$html .= '</ul>';		
			
			
			//new style 12-06-2014
		
		}else{
			
			$account_page_id = get_option('xoousersultra_my_account_page');				
			$my_account_url = get_page_link($account_page_id);	
			
			$uri_account = $my_account_url."?module=dashboard";
				
			
			// users seeing his/her own profile			
			$html .= '<div class="uultra-inner-nav-owm-profile">';			
			$html .= '<ul class="public-top-options">';
			
			
			$html .= '<li>';
			$html .= '<a class="uultra-btn-top1-menu" href="?my_profile" title="'.__('My Profile', 'users-ultra').'"><span><i class="fa fa-user fa-2x"></i></span></a>';			
			$html .= '</li>';
			
			$html .= '<li>';
			$html .= '<a class="uultra-btn-top1-menu" href="'.$uri_account.'" title="'.__('My Account', 'users-ultra').'"><span><i class="fa fa-wrench fa-2x"></i></span></a>';			
			$html .= '</li>';
			
			//is available by the admin?
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id,8)) //messages
			{
				
				$uri = $my_account_url."?module=messages";
			
				$html .= '<li>';
				$html .= '<a class="uultra-btn-top1-menu" href="'.$uri.'" title="'.__('Unread Messages', 'users-ultra').'"><span><i class="fa fa-envelope-o fa-2x"></i></span></a>';	
					
			
				//check if unread replies or messages			
				$user_id = get_current_user_id();
				$total = $xoouserultra->mymessage->get_unread_messages_amount($user_id);
					
				if($total>0)
				{
					$html .= '<div class="uultra-noti-bubble-top" title="'.__('Unread Messages', 'users-ultra').'">'.$total.'</div>';			
				}		
				$html .= '</li>';
			
			}
			
			
			//is available by the admin?
			if($xoouserultra->customizer->user_front_nav_menu_allowed($user_id,6)) //friends
			{
				
				
				$uri = $my_account_url."?module=friends";
			
				$html .= '<li>';
				$html .= '<a class="uultra-btn-top1-menu" href="'.$uri.'" title="'.__('Friend Requests', 'users-ultra').'"><span><i class="fa fa-users fa-2x"></i></span></a>';				
			
				//check if unread replies or messages			
				$user_id = get_current_user_id();
				$total = $xoouserultra->social->get_total_friend_request($user_id);
					
				if($total>0)
				{
						$html .= '<div class="uultra-noti-bubble-top" title="'.__('Friend Requests', 'users-ultra').'">'.$total.'</div>';			
				}		
				$html .= '</li>';
			
			}		
			
			$html .= '</ul>';
			$html .= '</div>';
			
			
		
		}
	
		return $html;
	
	}
	
	
	
	//basic template	
	public function get_basic_template($user_id, $atts, $display_country_flag,  $display_photo_rating, $display_photo_description, $display_photo_title, $display_photo_title_on_profile, $display_gallery_title_on_profile, $gallery_type, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type, $optional_fields_to_display, $cols, $disable_navigation_bar)
	{
		global $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/user.php');		
			
		//turn on output buffering to capture script output
        ob_start();		
		
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/uupro/profile_fancy.php"))
		{
			
			include($theme_path."/uupro/profile_fancy.php");
		
		}else{
			
			include(xoousers_path.'/templates/'.xoousers_template."/profile_fancy.php");
		
		}
		
		
        $content = ob_get_clean();
		//ob_end_clean();
		
		
		return $content ;
	
	}
	
	
	
	function get_width_of_column($total_cols)
	{
		$html = '';
		
		if($total_cols == 2)
		{
			$html = 'style="width:49%" ';		
		}
		
		if($total_cols == 1)
		{
			$html = 'style="width:99%" ';		
		}
		
		return $html;
	
	}
	
	function uultra_force_upgrade_check($user_id)
	{
		
		global $xoouserultra;
		
		//check if updgrade forced
		
		$force_upgrade = 'no';
		
		$force_upgrade = $xoouserultra->get_option('force_account_upgrading');
		
		if($force_upgrade=='yes')
		{
			//check if already upgraded
			
			$upgraded = get_user_meta($user_id, 'uultra_force_upgrade_check', true);
			
			if($upgraded=='yes')
			{				
				$force_upgrade = 'no';
			
			}else{
				
				$force_upgrade = 'yes';			
			
			}			
		
		
		}
		
		return $force_upgrade ;
		
	}
	
	function contact_me_public_form_directory($user_id)
	{
		$html = '<div id="uultra-dialog-form-'.$user_id.'" user-id="'.$user_id.'" class="uultra-send-pm-box uultra-dialog-form-directory" title="'.__("Send Private Message", "xoousers").'">';
		
		//check if logged in and seeing my own profile
		if (is_user_logged_in() ) 
		{
			
		$html .= '	<form>
			<fieldset>
			
			<div class="uultra-field-msbox-div-history" id="uultra-msg-history-list-'.$user_id.'"></div>
			
			<div class="uultra-field-msbox-div"><input type="text" name="uu_subject_'.$user_id.'" id="uu_subject_'.$user_id.'" class="text" placeholder="'.__("Type Subject", "xoousers").'"></div>
			
			<div class="uultra-field-msbox-div"><textarea name="uu_message_'.$user_id.'"  id="uu_message_'.$user_id.'" cols="" rows="" class="text uultra-private-message-txt-box" placeholder="'.__("Type Message", "xoousers").'"></textarea></div>
			
			
			<div class="uultra-field-emoticons-div">'.$this->get_message_emoticons_list_directory($user_id).'</div>
			
			</fieldset>';
			
			
		$html .= '	</form>';
		
		}else{
			
			$html .= '<p>'.__("You have to be logged in to send messages","xoousers").'</p>';
			
		}
		
		
		$html .= '	</div>';
	
	return $html;
	}
	
	
	
	function contact_me_public_form()
	{
		$html = '<div id="uultra-dialog-form" class="uultra-send-pm-box" title="'.__("Send Private Message", "xoousers").'">';
		
		//check if logged in and seeing my own profile
		if (is_user_logged_in() ) 
		{
			
		$html .= '	<form>
			<fieldset>
			
			<div class="uultra-field-msbox-div-history" id="uultra-msg-history-list"></div>
			
			<div class="uultra-field-msbox-div"><input type="text" name="uu_subject" id="uu_subject" class="text" placeholder="'.__("Type Subject", "xoousers").'"></div>
			
			<div class="uultra-field-msbox-div"><textarea name="uu_message"  id="uu_message" cols="" rows="" class="text uultra-private-message-txt-box" placeholder="'.__("Type Message", "xoousers").'"></textarea></div>
			
			
			<div class="uultra-field-emoticons-div">'.$this->get_message_emoticons_list().'</div>
			
			</fieldset>';
			
			
		$html .= '	</form>';
		
		}else{
			
			$html .= '<p>'.__("You have to be logged in to send messages","xoousers").'</p>';
			
		}
		
		
		$html .= '	</div>';
	
	return $html;
	}
	
	/*Contact Admin Form*/
	
	function contact_admin_form_internal()
	{
		$html = '<div id="uultra-dialog-form-contact-admin" class="uultra-send-pm-box" title="'.__("Send Private Message To Admin", "xoousers").'">';
		
		//check if logged in and seeing my own profile
		if (is_user_logged_in() ) 
		{
			
		$html .= '	<form>
			<fieldset>		
			
			
			<div class="uultra-field-msbox-div"><input type="text" name="uu_subject" id="uu_subject" class="text" placeholder="'.__("Type Subject", "xoousers").'"></div>
			
			<div class="uultra-field-msbox-div"><textarea name="uu_message"  id="uu_message" cols="" rows="" class="text uultra-private-message-txt-box-admin" placeholder="'.__("Type Message", "xoousers").'"></textarea></div>
			
			
			<div class="uultra-field-emoticons-div">'.$this->get_message_emoticons_list().'</div>
			
			</fieldset>';
			
			
		$html .= '	</form>';
		
		}else{
			
			$html .= '<p>'.__("You have to be logged in to send messages","xoousers").'</p>';
			
		}
		
		
		$html .= '	</div>';
	
	return $html;
	}
	
	
	public function uultra_get_administrators_list(){
        
		global $wp_roles;
        $user_roles = array();		
		
		$user_query = new WP_User_Query( array( 'role' => 'Administrator' ) );

     

     
		// User Loop
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
								
				 $user_roles[$user->ID] = $user->display_name;
			}
		} else {
			
		}


        return $user_roles;
    }
	
	public function get_message_emoticons_list()
	{
		$icons = $this->emoticon_list;
		$html="";		
		
		foreach($icons as $icon => $array_data) 
		{
			$short = $array_data["shortocde"];
			$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/".$icon.".gif";			
			$html .='<img src="'.$ico_url.'" icoid="'.$short.'" alt="'.$short.'" class="uultra-emoti-msg-ico">';
		
		}
		
		return $html;
	}
	
	public function get_message_emoticons_list_directory($user_id)
	{
		$icons = $this->emoticon_list;
		$html="";		
		
		foreach($icons as $icon => $array_data) 
		{
			$short = $array_data["shortocde"];
			$ico_url = xoousers_url."templates/".xoousers_template."/img/emoticons/".$icon.".gif";			
			$html .='<img src="'.$ico_url.'" icoid="'.$short.'" alt="'.$short.'" user-id="'.$user_id.'" class="uultra-emoti-msg-ico-directory">';
		
		}
		
		return $html;
	}
		
	
	public function get_profile_bg($user_id)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$site_url = site_url()."/";		
		$profile_customizing = $xoouserultra->customizer->get_profile_customizing();
		
		$html = "";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');		
		$user_pic = get_user_meta($user_id, 'user_profile_bg', true);
		
		
		if($user_pic!="")
		{
			$src = $site_url.$upload_folder.'/'.$user_id.'/'.$user_pic;			
			$html .= '<img class="landscape" src="'.$src.'" />';
		}else{
			
			
			if($profile_customizing['uultra_profile_image_bg_color']=="")
			{
				//check if admin set a custom image				
				$admin_img = $xoouserultra->customizer->get_custom_bg_for_user_profile();
				
				if($admin_img=="")
				{
					//default image only if color hasn't been set
					$src = xoousers_url.'/templates/'.xoousers_template.'/img/1920X1000.png';			
					$html .= '<img class="landscape" src="'.$src.'" />';
					
				}else{
					
					//default image by admin
					$src = $admin_img;			
					$html .= '<img class="landscape" src="'.$src.'" />';
					
				}
				
				
				
			}
			
			
						
		} 
		
		
		return $html;	
	
	}
	
	
	public function get_profile_cover_upload_btn($user_id)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$site_url = site_url()."/";
		
		$html = '';
		
		
		$my_id = get_current_user_id();
		
		if ($my_id == $user_id) // i am seeing my own id 
		{
			
			$html .= '<div class="uultra-change-profile-cover-div">';
			$html .= ' <a class="uultra-btn-change-users-profile-cover" href="#" id="uultra-btn-save-customizer-change" title="" > '.__('Change Cover','users-ultra').'</a>';
			$html .= '</div>';
			
		
		}
		
		return $html;
		

	
	}
	
	public function has_profile_bg($user_id)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$site_url = site_url()."/";
		
		$profile_customizing = $xoouserultra->customizer->get_profile_customizing();
		
		$html = "";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');		
		$user_pic = get_user_meta($user_id, 'user_profile_bg', true);
		
		
		if($user_pic!="")
		{
			return true;
		}else{
			
			return false;
						
		} 
		

	
	}
	
	
	function get_user_desc_exerpt($the_excerpt,$excerpt_length)
	{
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);
	
		if(count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '… ');
			$the_excerpt = implode(' ', $words);
		endif;
	
		$the_excerpt = '' . $the_excerpt . '';
	
		return $the_excerpt;
	}
	
	public function get_profile_bg_url($user_id)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$site_url = site_url()."/";
		
		$html = "";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');		
		$user_pic = get_user_meta($user_id, 'user_profile_bg', true);
		
		
		if($user_pic!="")
		{
			$src = $site_url.$upload_folder.'/'.$user_id.'/'.$user_pic;			
			$html .= $src;		
			
		} 
		
		
		return $html;
	
	
	}
	
	public function get_column_widgets($col)
	{
		//get col
	
	
	
	}
	
	public function do_logged_validation()
	{
		global $xoouserultra;
		
		$photo_visibility = $xoouserultra->get_option("uurofile_setting_display_photos");
		
		if($photo_visibility=='public' || $photo_visibility=="")
		{
			$photos_available = true;
		
		}else{
			
			 if (!is_user_logged_in()) 
		     {
				 $photos_available = false;
			
			 }else{
				 
				 $photos_available = true;
				
			 }
		
		}
		
		return $photos_available;
	
	
	
	}
	
	/**
	Display most visited users List
	******************************************/
	public function show_most_visited_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'item_width' => '25%', // this is the width of each item or user in the directory			
			'howmany' => 3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_most_visited_users($howmany);
		
		$html.='<div class="uultra-mostvisited-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'.  $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	public function get_most_visited_users ($howmany)
	{
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT u.*, stat.stat_item_id,
		  stat.stat_module , stat.stat_total_hits
		  
		  FROM ' . $wpdb->prefix . 'users u  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_stats stat ON (stat.stat_item_id = u.ID)";
				
		$sql .= " WHERE stat.stat_item_id = u.ID AND  stat.stat_module= 'user' ORDER BY stat.stat_total_hits DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		return $rows;
		
	}
	
	/**
	Display top rated users List
	******************************************/
	public function show_minified_profile($atts)
	{
		global  $xoouserultra;
		
		extract( shortcode_atts( array(	
			
			'item_width' => '', // this is the width of each item or user in the directory			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 50, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => 'social,country', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => '',
			'display_country_flag' => '',
			
			
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_logged_in_user();
		
		$html.='<div class="uultra-miniprofile-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'            
               </div>        
            
                <div class="info-div"> 
								
				 <p class="uu-direct-name"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">'. $this->get_display_name($user_id).' </a> <span>'.$this->get_user_country_flag($user_id).'</span></p> ';
                
                 if ($optional_fields_to_display!="") 
				 { 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);                  
                
                  }
				  
				  $html .= '<div class="tool-div-bar"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'" '.__('See Profile','users-ultra').'><i class="fa fa-eye fa-lg"></i> </a> 
				  <a class="uultra-btn-profile" href="'.$xoouserultra->get_logout_url().'" title="'.__('Logout','users-ultra').'"> <i class="fa fa-power-off fa-lg"></i> </a>  </div> ';
                
                  $html .= '</div> ';
            
            $html .=' </li>';			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function get_logged_in_user()
	{
		global  $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		$sql = "SELECT ID, user_login, user_nicename from ".$wpdb->prefix ."users WHERE ID = '".$logged_user_id."' ";
				
		$rows = $wpdb->get_results($sql);
			
		return $rows;
	}
	
	/**
	Display top rated users List
	******************************************/
	public function show_latest_users($atts)
	{
		global    $xoouserultra;
		
		extract( shortcode_atts( array(	
			
			'item_width' => '', // this is the width of each item or user in the directory			
			'howmany' =>3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 50, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => '',
			'display_country_flag' => '',
			
			
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_latest_users($howmany);
		
		$html.='<div class="uultra-latest-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
			
			 
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div"> 
				
				
				
				 
				 
				         
			
				 <p class="uu-direct-name"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">'. $this->get_display_name($user_id).' </a> <span>'.$this->get_user_country_flag($user_id).'</span></p> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);                  
                
                  }
				  
				  
				  $html .= '<div class="tool-div-bar"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'" title="'.__('See Profile','users-ultra').'" alt="'.__('See Profile','users-ultra').'" "><i class="fa fa-eye fa-lg"></i> </a>  </div> ';
                
                  $html .= '</div> ';
				  
				 /* $html .= '				                   
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> ';*/
            
            
            $html .=' </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	

	
	function get_latest_users( $howmany )
	{
		global  $wpdb,  $xoouserultra;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		// prepare arguments
		$args  = array(
		
		'orderby' => 'ID',
		'order' => 'DESC',
		'number' => $howmany,
		
		// check for two meta_values
		'meta_query' => array(
			array(
				
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
				),
			
		));

			
		
		
				

		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
			
		return $res;
	}
	
	function get_latest_users_private( $howmany )
	{
		global  $wpdb,  $xoouserultra;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		// prepare arguments
		$args  = array(
		
		'orderby' => 'ID',
		'order' => 'DESC',
		'number' => $howmany,
		
		);

			
		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
			
		return $res;
	}
	
	
	/*Used in the Admin Only*/
	function get_users_filtered( $args )
	{

        global $wpdb,$blog_id, $wp_query;	
		
		
		extract($args);		
		$memberlist_verified = 1;		
		$blog_id = get_current_blog_id();

		$paged = (!empty($_GET['paged'])) ? $_GET['paged'] : 1;
		
		//$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		
		$offset = ( ($paged -1) * $per_page);

		/** QUERY ARGS BEGIN **/		
		if (isset($args['exclude']))
		{
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		
		
		/** QUERY BY EMAILS **/		
		if ( $uultra_meta=="" )
		{			
			$query['search'] = $keyword;
			$query['search_columns']= array('user_login', 'user_email');
		}
		
		$query['meta_query'] = array('relation' => strtoupper($relation) );
		
		/*This is applied only if we have to filter certain roles*/
		if (isset($role) &&  $role!="")
		{
			//echo "rol set;";
			$roles = explode(',',$role);
			
			if (count($roles) >= 2)
			{
				$query['meta_query']['relation'] = 'or';
			}
			
			foreach($roles as $subrole)
			{
				
				$query['meta_query'][] = array(
				'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
				'value' => $subrole,
				'compare' => 'like'
				);
			}
		}
		
	
	   
		if ($status)
		{
			
			$query['meta_query'][] = array(
					'key' => 'usersultra_account_status',
					'value' => $status,
					'compare' => 'LIKE'
				);
				
		}
		
		if ($keyword)
		{
			
			/*$query['meta_query'][] = array(
					'key' => 'display_name',
					'value' => $keyword,
					'compare' => 'LIKE'
				);*/
				
		}
		
		if ($uultra_meta)
		{
			
			$query['meta_query'][] = array(
					'key' => $uultra_meta,
					'value' => $keyword,
					'compare' => 'LIKE'
				);				
		}
		
		if ($uultra_membership)
		{
			
			$query['meta_query'][] = array(
					'key' => 'usersultra_user_package_id',
					'value' => $uultra_membership,
					'compare' => 'LIKE'
				);				
		}
		
			
		if (isset($memberlist_withavatar) && $memberlist_withavatar == 1)
		{
				$query['meta_query'][] = array(
					'key' => 'profilepicture',
					'value' => '',
					'compare' => '!='
				);
		}
			
    	if ($sortby) $query['orderby'] = $sortby;			
	    if ($order) $query['order'] = strtoupper($order); // asc to ASC
			
		/** QUERY ARGS END **/
			
		$query['number'] = $per_page;
		$query['offset'] = $offset;
			
		/* Search mode */
		if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
		{
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			$user_count_query = new WP_User_Query($count_args);
						
		}

		if ($per_page) 
		{			
		
			/* Get Total Users */
			if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
			{
				$user_count = $user_count_query->get_results();								
				$total_users = $user_count ? count($user_count) : 1;
				
			} else {
				
				//echo "HEREE";
				
				$result = count_users();
				$total_users = $result['total_users'];
				
				//print_r($result);
			}
			
			$total_pages = ceil($total_users / $per_page);
		
		}
		
		$wp_user_query = new WP_User_Query($query);
		
	
		if (! empty( $wp_user_query->results )) 
		{
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('paged','%#%'),
					'total'        => $total_pages,
					'current'      => $paged,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','users-ultra'),
					'next_text'    => __('Next »','users-ultra'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
		}
		
				
		return $arr;
		
		
	}
	
	//Delete file
	public function uultra_delete_exported_csv_file()
	{
		
		global    $xoouserultra;
		
		$path_f = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		$target_path = $path_f.'/import/';
		$file = $target_path."uultra_data_export.csv";
		
		if(file_exists($file))
		{
			unlink($file);
		
		}
		die();
	
	}
	
	//Check if CSV exists
	public function get_downloadable_csv_check()
	{
		global    $xoouserultra;
		$path_f = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		$site_url = site_url()."/";
						
		$target_path = $path_f.'/import/';
		$file = $target_path."uultra_data_export.csv";	
		
		
		$html = "";
		
		if(file_exists($file))
		{
			$download_file = $site_url.$xoouserultra->get_option('media_uploading_folder')."/import/uultra_data_export.csv";
			$html .= ' <div class="uuultra-top-noti-admin " id="uultra-csv-download-box">';
			$html .= "<div class='user-ultra-warning'><p>".__("RECENT EXPORTED USERS", 'users-ultra')." <a href='".$download_file."' target='_blank'>".__("CLICK HERE TO DOWNLOAD THE CSV FILE ", 'users-ultra')."</a>. ".__("If you don't need it anymore we highly recommend to delete this file. ", 'users-ultra')." <a href='#' id='uultra-delete-csv-export-file'> ".__("CLICK HERE TO DELETE THE CSV FILE", 'users-ultra')."</a></p></div>";
			
			
			$html .= '</div>';
		
		}
		
		return $html;
	
	}
	
	//Creating downloadable CSV files using
	public function get_downloadable_csv($users)
	{
		global    $xoouserultra;		
	
		$path_f = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
						
		$target_path = $path_f.'/import/';
		// Checking for upload directory, if not exists then new created. 
		if(!is_dir($target_path))
			    mkdir($target_path, 0755);		
		
		// create a file pointer connected to the output stream
		$file = $target_path."uultra_data_export.csv";
		
		if(file_exists($file ))
		{
			unlink($file);
		}
		
		$output = fopen($file, 'w');
		
		
		// output the column headings		
		//order username, email, display name, first name and last name
		$headers_array = array('Nick', 'Email' , 'Display Name' , 'First Name', 'Last Name', 'ID', 'IP', 'Role',    'Status' , 'Registered');
		
		fputcsv($output,$headers_array  );
		
		if (!empty($users['users']))
		{
			
			foreach($users['users'] as $user) 
			{
				$user_id = $user->ID;
				$u_status =  $this->get_user_meta_custom($user_id, 'usersultra_account_status');
				$u_ip =  $this->get_user_meta_custom($user_id, 'uultra_user_registered_ip');
				$u_role =  $this->get_all_user_roles($user_id);
				
				$user_info = get_userdata($user_id);
				
				$user_data =  array($user->user_login, $user->user_email, $user_info->display_name,  $user_info->first_name , $user_info->last_name,  $user->ID, $u_ip, $u_role,  $u_status, $user->user_registered  );
				
				
				fputcsv($output, $user_data);
			
			}
		
		}
	
		
		// make php send the generated csv lines to the browser
   		 fclose($output);
		
		
	}
	
	//get user status
	public function get_user_status($user)
	{
		global    $xoouserultra;
		
		return $this->get_user_meta_custom($user);
		
		
	}
	
	
	/**
	Display top rated users List
	******************************************/
	public function show_top_rated_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'item_width' => '46%', // this is the width of each item or user in the directory			
			'howmany' => 2, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_top_rated_users($howmany);
		
		$html.='<div class="uultra-toprated-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">'.__('See Profile', 'users-ultra').'</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	public function get_user_display_name($user_id)
	{
		$display_name = "";
		
		$user = get_user_by('id',$user_id);
		
		$display_name = get_user_meta($user_id, 'display_name', true);
		
		if($display_name=="")
		{			
			$display_name =$user->display_name;		
		
		}
		
		return $display_name;
	
	}
	
	
	public function get_top_rated_users ($howmany)
	{
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT u.*, rate.ajaxrating_votesummary_user_id,
		  rate.ajaxrating_votesummary_total_score 
		  
		  FROM ' . $wpdb->prefix . 'users u  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_ajaxrating_votesummary rate ON (rate.ajaxrating_votesummary_user_id = u.ID)";
				
		$sql .= " WHERE rate.ajaxrating_votesummary_user_id = u.ID ORDER BY rate.ajaxrating_votesummary_total_score DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		return $rows;
		
	}
	
	/**
	Display promoted users List
	******************************************/
	public function show_promoted_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(
		
			'users_list' => '', // users list separated by commas
			'item_width' => '100%', // this is the width of each item or user in the directory			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', //
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_latest_photos' => 'yes', 			
			'display_latest_photos_size' => 90,
			'display_latest_photos_howmany' =>8, 
			'display_promote_desc' =>'',
			'display_promote_title' =>'',
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
		), $atts ) );
		
		$html = "";
		
		$users_list = $this->users_shortcodes_promoted($users_list);
		
		$html.='<div class="uultra-promoted-users">
			
			<ul>';
		
		foreach($users_list['users'] as $user) 		
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" >
               
               <div class="prof-photo">
               
                   '.$xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p> 
				 
				 <p>'.$this->get_user_country_flag($user_id).'</p>				 
				 <p>'.$this->get_user_social_icons($user_id).'</p>
				 
				 
				               
                
                 <div class="social-icon-divider">  </div> ';
				
				 if ($display_latest_photos=="yes") 
				 {
					 $html .= $this->get_user_spot_photo($user_id, $display_latest_photos_size, $display_latest_photos_howmany); 
				 
				 
				 }
				 
				  if ($display_promote_desc!="") 
				 {
					  $html .= "<h3>" .$display_promote_title."</h3>";
					 $html .= "<p class='desc'>" .$display_promote_desc."</p>";
				 
				 
				 }
				 
				 
					
				 
				 if ($optional_fields_to_display!="") 
				 { 
                 
                   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                  }
				  
				  
				  
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function get_user_spot_photo($user_id, $display_latest_photos_size, $display_latest_photos_howmany)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');
		
		$html = "";
		
		$rows = $xoouserultra->photogallery->get_user_photos($user_id, $display_latest_photos_howmany);
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-promototed-photo-list">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
					
				$file=$photo->photo_thumb;				
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded' style='max-width:".$display_latest_photos_size."px'/> </a>";
					
							
					
				$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
		
		
		}
		
		return $html;
		
	
	
	}
	
	public function get_current_user_id_for_custom_fields($user_id, $within_widget)
	{
		if($within_widget == 'yes' && $user_id=='') //shortcode used within profile's widget
		{
			//get from uri				
			$current_user = $this->get_user_data_by_uri();
				
			if(isset($current_user->ID))
			{
				$user_id = $current_user->ID;					
			}
			
			return $user_id;
			
		}else{
			
			
			if($user_id=="" && is_user_logged_in())
			{
				$user_id = get_current_user_id();				
				return $user_id ;
				
			}else{
				
				return $user_id;
			
			
			}
			
		
		
		}
		
	
		
	}
	
	function uultra_get_user_custom_form_array($user_id)
	{
		//get user form		
		$custom_form = $this->get_user_meta( 'uultra_custom_registration_form', $user_id); 
			
		if($custom_form!="")
		{
				
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);
			
		}else{
				
			$array = get_option('usersultra_profile_fields');	
			
		}
		
		return $array;			
	
	}
	
	/**
	Display custom information of user
	******************************************/
	public function show_user_custom_metainfo($atts)
	{
		global   $xoouserultra;		
		
		extract( shortcode_atts( array(		
		
		    'user_id' => '', // this is the width of each item or user in the directory
			'fields_list' => '',		//example first_name, last_name
			'template' => 'table',		//table,block,simple
			'within_widget' => 'no'		//is it being used in widget
				
			
		), $atts ) );
		
		$native_wp_metas= array('user_nicename','user_email' , 'user_registered', 'display_name' , 'first_name' , 'last_name', 'ID');
		
		$html = "";
			
		$user_id = $this->get_current_user_id_for_custom_fields($user_id, $within_widget );
		
		//echo "UID: " .$user_id ;
		
		if($user_id!="")
		{
			
			$fields_list_array = array();			
			$fields_list_array  = explode(',', $fields_list);
			
			//custom form fields
			$array=$this->uultra_get_user_custom_form_array($user_id);			
					
			if($template =='table')
			{
				$html .='<table class="uultra-custom-profile-fields-list" width="100%" border="0" cellspacing="0" cellpadding="0">';
			
			}
			
			foreach($fields_list_array as $fields) 
			{
				$field  = explode(':', $fields);
				$meta =  preg_replace('/\s+/', '', $field[0]);
				$meta_label = $field[1];
				
				//check if visible
			
				//check if this is a native WP field			
				if(in_array($meta, $native_wp_metas)	)
				{
					$meta_value=get_the_author_meta( $meta, $user_id );
					
					/* Show the label */
					if ($meta_value!='')
					{
						if($template =='table')
						{
												
						$html .= '<tr>';											
						$html .= '<td>' .$meta_label .': </td>';
						$html .= '<td>' .$meta_value.'</td>';						
						$html .= '</tr>';
						
						}elseif($template =='block'){
																									
							$html .= '<strong class="uultra-p-custom-field-shortcode">' .$meta_label .': </strong>';
							$html .= '<p class="uultra-p-custom-field-shortcode">' .$meta_value.'</p>';	
						
						}elseif($template =='simple'){
																									
							$html .= $meta_label." ".$meta_value;
													
						}
					
					}										
										
				
				}else{
					
					if($template =='table')
					{
						$meta_value=$this->get_user_meta_custom( $user_id, $meta);
						
						if ($meta_value!='')
						{
						
							$html .= '<tr>';	
							$html .= '<td>' .$meta_label .': </td>';				
							$html .= '<td>' .$meta_value .'</td>';
							$html .= '</tr>';	
						
						
						}
						
					}elseif($template =='block'){
						
						    $meta_value=$this->get_user_meta_custom( $user_id, $meta);
							
							if ($meta_value!='')
							{							
																		
								$html .= '<strong class="uultra-p-custom-field-shortcode">' .$meta_label .': </strong>';
								$html .= '<p class="uultra-p-custom-field-shortcode">' .$meta_value.'</p>';
							
							}
							
					}elseif($template =='simple'){
						
						
							$meta_value=$this->get_user_meta_custom( $user_id, $meta);
							
							if ($meta_value!='')
							{						
																									
								$html .= $meta_label." ".$meta_value;
							
							}
					
					}elseif($template =='nolabel'){
						
						
							$meta_value=$this->get_user_meta_custom( $user_id, $meta);
							
							if ($meta_value!='')
							{						
																									
								$html .=$meta_value;
							
							}
													
					}
					
										
			
				} // end if WP native meta
			
			} // end for			
			
			if($template =='table')
			{
				 $html .='</table>';
			}		
		
		} //end if
		
		return $html ;
	
	}
	
	
	
	/**
	Display featured users List
	******************************************/
	public function show_featured_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(		
		
		    'users_list' => '', // this is the width of each item or user in the directory	
			'meta_key_to_search' => '', // 
			'meta_keyword' => '', // 			
			'item_width' => '21%', // this is the width of each item or user in the directory			
			'howmany' => 10, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',			
			'list_order' => 'ASC', // asc or desc ordering
		), $atts ) );
		
		$html = "";
		
		if($meta_key_to_search=='')
		{
		
			$users_list = $this->users_shortcodes_featured($users_list);
		
		}else{
			
			$users_list = $this->users_shortcodes_featured_by_metakey( $meta_key_to_search, $meta_keyword );			
			
		}
		
		$html.='<div class="uultra-featured-users">
			
			<ul>';
		
		foreach($users_list['users'] as $user) 		
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" >
               
               <div class="prof-photo">
               
                   '.$xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function users_shortcodes_promoted( $users_list )
	{
		global  $wpdb,  $xoouserultra;
		
		$users_list  = explode(',', $users_list);
		
			
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$query['include'][] = array($users_list);
			
				

		$wp_user_query = new WP_User_Query(array('include' =>$users_list ));		
		$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	function users_shortcodes_featured( $users_list )
	{
		global  $wpdb,  $xoouserultra;
		
		$users_list  = explode(',', $users_list);
		
			
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$query['include'][] = array($users_list);
		$wp_user_query = new WP_User_Query(array('include' =>$users_list ));		
		$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	function users_shortcodes_featured_by_metakey( $meta_key, $keyword )
	{
		global  $wpdb,  $xoouserultra;
		
		$query['meta_query'][] = array(
				'key' => $meta_key,
				'value' => $keyword,
				'compare' => 'LIKE'
			);
		
		$query['include'][] = array($users_list);
		$wp_user_query = new WP_User_Query($query);		
		$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	private function uultra_build_search_field_array($custom_form) 	
	{
		
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$custom_fields = get_option($custom_form);
				
		}else{			
					
			$custom_fields = get_option('usersultra_profile_fields');			
				
		}
		
	
		
		
        $this->search_banned_field_type = array('fileupload', 'password', 'datetime');

        $this->show_combined_search_field = false;
        $this->show_nontext_search_fields = false;

        $this->all_text_search_field = array();
        $this->combined_search_field = array();
        $this->nontext_search_fields = array();
        $this->checkbox_search_fields = array();

        $included_fields = '';
        if ($this->search_args['fields'] != '')
            $included_fields = explode(',', $this->search_args['fields']);

        $excluded_fields = explode(',', $this->search_args['exclude_fields']);

        $search_filters = array();
        $search_filters = explode(',', $this->search_args['filters']);

        foreach ($custom_fields as $key => $value)
		{
            if (isset($value['type']) && $value['type'] == 'usermeta') {
                if (isset($value['field']) && !in_array($value['field'], $this->search_banned_field_type)) {
                    if (isset($value['meta']) && !in_array($value['meta'], $excluded_fields)) {
                        switch ($value['field']) {
                            case 'text':
                            case 'textarea':
                            case 'datetime':

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters)) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                } else {
                                    if ($this->show_combined_search_field === false)
                                        $this->show_combined_search_field = true;

                                    $this->all_text_search_field[] = $value['meta'];

                                    if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                        $this->combined_search_field[] = $value['meta'];
                                }



                                break;

                            case 'select':
                            case 'radio':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_field == true || $is_in_filter == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                }
                                break;

                            case 'checkbox':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_filter == true || $is_in_field == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->checkbox_search_fields[] = $value;
                                }
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        }
    }
	
	function uultra_get_field_custom_label($field, $label_array) 
	{
		
	}

    /* Setup search form */

    function uultra_search_form($args=array()) 
	{
		global $xoouserultra, $predefined;
		

        // Determine search form is loaded
        $this->uultra_search = true;
        /* Default Arguments */
        $defaults = array(
            'fields' => null,
            'filters' => null,
			'filter_labels' => null, //separated by commas age:From
            'exclude_fields' => null,
            'operator' => 'AND',
			'width' => 'AND',
			'custom_form' => '',
			'target_page_url' => '',
            'use_in_sidebar' => null,
            'users_are_called' => __('Users', 'users-ultra'),
            'combined_search_text' =>  __('type user name here', 'users-ultra'),
            'button_text' =>  __('Search', 'users-ultra'),
            'reset_button_text' =>__('Reset', 'users-ultra')
        );
		
		$filter_lab = array();
		
		if($filter_labels!='')
		{			
			$filter_lab = explode(',', $filter_labels);			
		}
		
		
		
		

        $this->search_args = wp_parse_args($args, $defaults);

        $this->search_operator = $this->search_args['operator'];

        if (strtolower($this->search_args['operator']) != 'and' && strtolower($this->search_args['operator']) != 'or') {
            $this->search_args['operator'] = 'AND';
        }

        // Prepare array of all fields to load
        $this->uultra_build_search_field_array($this->search_args['custom_form']);
		
		$action_url = '';
		
		if( $this->search_args['target_page_url']!='')
		{			
			$action_url = "action='".$this->search_args['target_page_url']."'";			
		}
		
		//echo "URL" . $action_url;
		
		

        $sidebar_class = null;
        if ($this->search_args['use_in_sidebar'])
            $sidebar_class = 'uultra-sidebar';

        $display = null;

        $display.='<div class="xoouserultra-wrap xoouserultra-wrap-form uultra-search-wrap' . $sidebar_class . '">';
        $display.='<div class="xoouserultra-inner xoouserultra-clearfix">';
        $display.='<div class="xoouserultra-head">' . sprintf(__('Search %s', 'users-ultra'), $this->search_args['users_are_called']) . '</div>';
		
        $display.='<form method="get" id="uultra_search_form" name="uultra_search_form" class="uultra-search-form uultra-clearfix" '.$action_url.'>';

        // Check For default fields Start
        if ($this->show_combined_search_field === true) {            
		
			$display.='<p class="uultra-p uultra-search-p">';
            $display.= $xoouserultra->htmlbuilder->text_box(array(
                        'class' => 'uultra-search-input uultra-combined-search',
                        'value' => isset($_GET['uultra_combined_search']) ? $_GET['uultra_combined_search'] : '',
                        'name' => 'uultra_combined_search',
                        'placeholder' => $this->search_args['combined_search_text']
                    ));

            if (count($this->combined_search_field) > 0) {
                $display.='<input type="hidden" name="uultra_combined_search_fields" value="' . implode(',', $this->combined_search_field) . '" />';
            } else {
                $display.='<input type="hidden" name="uultra_combined_search_fields" value="' . implode(',', $this->all_text_search_field) . '" />';
            }


            $display.='</p>';
        }

        // Check For default fields End
        // Custom Search Fields Creation Starts

        if ($this->show_nontext_search_fields === true) {			
			
            $counter = 0;
            $display.='<p class="uultra-p uultra-search-p">';
            foreach ($this->nontext_search_fields as $key => $value) 
			{				
				
                $method_name = '';
                $method_name = $this->method_dect[$value['field']];
                if ($method_name != '') 
				{					
					
                    if ($counter > 0 && $counter % 2 == 0) {
                        $display.='</p>';
                        $display.='<p class="uultra-p uultra-search-p">';
                    }

                    $counter++;

                    $class = 'uultra-search-input uultra-search-input-left uultra-search-meta-' . $value['meta'];
                    if ($counter > 0 && $counter % 2 == 0)
                        $class = 'uultra-search-input uultra-search-input-right uultra-search-meta-' . $value['meta'];


                    if ($method_name == 'drop_down') 
					{
						//echo "here: ".$method_name;
                        $loop = array();						

                        if (isset($value['predefined_options']) && $value['predefined_options'] != '' && $value['predefined_options'] != '0') {
							
							$defined_loop = $xoouserultra->commmonmethods->get_predifined( $value['predefined_options'] );
							
							

                            foreach ($defined_loop as $option) {
                                if ($option == '' || $option == null) {
                                    $loop[$option] = $value['name'];
                                } else {
                                    $loop[$option] = $option;
                                }
                            }
                        } else if (isset($value['choices']) && $value['choices'] != '')
						 {
							
							$loop_default = $xoouserultra->uultra_one_line_checkbox_on_window_fix($value['choices'] );
							
					
							
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_POST['uultra_search'][$value['meta']]))
                            $_POST['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);


                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '0';
                        $name = 'uultra_search[' . $value['meta'] . ']';

                        if ($value['field'] == 'checkbox') 
						{
                            $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : array();
                            $name = 'uultra_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= $xoouserultra->htmlbuilder->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
						
                    } else if ($method_name == 'text_box') {
                        if (isset($_GET['uultra_search'][$value['meta']]))
                            $_GET['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);


                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '';
                        $name = 'uultra_search[' . $value['meta'] . ']';

                        $display.= $xoouserultra->htmlbuilder->text_box(array(
                                    'class' => $class,
                                    'name' => $name,
                                    'placeholder' => $value['name'],
                                    'value' => $default
                                ));
                    }
                }
            }
            $display.='</p>';


            if (isset($this->checkbox_search_fields) && count($this->checkbox_search_fields) > 0) {
				
				
                foreach ($this->checkbox_search_fields as $key => $value) 
				{					
                    $display.='<p class="uultra-p uultra-search-p uultra-multiselect-p">';

                    $method_name = '';
                    $method_name = $this->method_dect[$value['field']];
                    if ($method_name != '') {
                        $class = 'uultra-search-input uultra-search-multiselect uultra-search-meta-' . $value['meta'];

                        $loop = array();

                        if (isset($value['predefined_loop']) && $value['predefined_loop'] != '' && $value['predefined_loop'] != '0') {
                            //$defined_loop = $predefined->get_array($value['predefined_loop']);
							$defined_loop = $xoouserultra->commmonmethods->get_predifined( $value['predefined_options'] );
							

                            foreach ($defined_loop as $option)
                                $loop[$option] = $option;
                        } else if (isset($value['choices']) && $value['choices'] != '') {
							
                            //$loop_default = explode(PHP_EOL, $value['choices']);
							
							$loop_default = $xoouserultra->uultra_one_line_checkbox_on_window_fix($value['choices']);
							
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_GET['uultra_search'][$value['meta']]))
                            $_GET['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);

                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '0';
                        $name = 'uultra_search[' . $value['meta'] . ']';
                        if ($value['field'] == 'checkbox') 
						{
                            $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : array();
                            $name = 'uultra_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) 
						{
                            $display.= $xoouserultra->htmlbuilder->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
						
						//between 
						
						
                    }

                    $display.='</p>';
                }
            }
        }

        $display.='<input type="hidden" name="userspage" id="userspage" value="" />';
        $display.='<input type="hidden" name="uultra-search-fired" id="uultra-search-fired" value="1" />';

        // Custom Search Fields Creation Ends
        // Submit Button
        $display.='<div class="uultra-searchbtn-div">';
        $display.=$xoouserultra->htmlbuilder->button('submit', array(
                    'class' => 'uultra-button-alt xoouserultra-button uultra-search-submit',
                    'name' => 'uultra-search',
                    'value' => $this->search_args['button_text']
                ));
        $display.='&nbsp;';
        $display.=$xoouserultra->htmlbuilder->button('button', array(
                    'class' => 'uultra-button-alt xoouserultra-button uultra-search-reset',
                    'name' => 'uultra-search-reset',
                    'value' => $this->search_args['reset_button_text'],
                    'id' => 'uultra-reset-search'
                ));

        $display.='</div>';
        $display.='</form>';

        $display.='</div>';
        $display.='</div>';
        /* Extra Clearfix for Avada Theme */
        $display.='<div class="uultra-clearfix"></div>';

        return $display;
    }
	
	/* Search user by more criteria */
	function uultra_query_search_displayname( &$query ) {
		global $wpdb;
		$search_string = esc_attr( trim( get_query_var('uultra_combined_search') ) );
		$query->query_where .= $wpdb->prepare( " OR $wpdb->users.display_name LIKE %s", '%' . like_escape( $search_string ) . '%' );
		
		
		
	}

    /* Apply search params and Generate Results */
	
	function search_result($args) 
	{
		
		global $wpdb,$blog_id, $wp_query, $wp_rewrite, $paged;

	
		extract($args);		
		
		$memberlist_verified = 1;		
		$blog_id = get_current_blog_id();
		
		//echo $blog_id;

		$wp_query->query_vars['paged'] > 1 ? $page = $wp_query->query_vars['paged'] : $page = 1;
		
		$offset = ( ($page -1) * $per_page);

		/** QUERY ARGS BEGIN **/
		
		if (isset($args['exclude']) && $args['exclude']!='')
		{
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		
		
		/*This is applied only if we have to filder certain roles*/
		if (isset($role) &&  $role!="")
		{
			//echo "rol set;";
			$roles = explode(',',$role);
			
			if (count($roles) >= 2)
			{
				//$query['meta_query']['relation'] = 'OR';
				
				$query['meta_query'] = array('relation' => 'OR' );
			}
			
			foreach($roles as $subrole)
			{
				
				$query['meta_query'][] = array(
					'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
					'value' => $subrole,
					'compare' => 'like'
				);
			}
			
		}else{
			
			
			$query['meta_query'] = array('relation' => strtoupper($relation) );
					
			
		
		}
		
		
		
	
	    if (isset($_GET['uultra_search'])) 
		{

        foreach ($_GET['uultra_search'] as $key => $value)
		{
			
			
			//echo $key ." val: " . $value;
			$target =  $value;

						
						
						/*if ($->field_type($key) == 'multiselect' ||
							$->field_type($key) == 'checkbox' ||
							$uultra->field_type($key) == 'checkbox-full'
							) {
							$like = 'like';
						} else {
							$like = '=';
						}*/
					
			$like = 'like';
			if (isset($target)  && $target != '' && $key != 'role' )
			{
				if (substr( trim( htmlspecialchars_decode($args[$key])  ) , 0, 1) === '>')
				{
					$choices = explode('>', trim(  htmlspecialchars_decode($args[$key]) ));
					$target = $choices[1];
					$query['meta_query'][] = array(
									'key' => $key,
									'value' => $target,
									'compare' => '>'
						);
						
				}elseif (substr( trim(  htmlspecialchars_decode($args[$key]) ) , 0, 1) === '<') {
								$choices = explode('<', trim(  htmlspecialchars_decode($args[$key]) ));
								$target = $choices[1];
								$query['meta_query'][] = array(
									'key' => $key,
									'value' => $target,
									'compare' => '<'
								);
								
				} elseif (strstr( esc_attr( trim(  $args[$key] ) ) , ':'))				{
								$choices = explode(':', esc_attr( trim(  $args[$key] ) ));
								$min = $choices[0];
								$max = $choices[1];
								$query['meta_query'][] = array(
									'key' => $key,
									'value' => array($min, $max),
									'compare' => 'between'
								);
								
				} elseif (strstr( esc_attr( trim( $args[$key] ) ) , ',')){
							$choices = explode(',', esc_attr( trim(  $args[$key] ) ));
								foreach($choices as $choice){
									$query['meta_query'][] = array(
										'key' => $key,
										'value' => $choice,
										'compare' => $like
									);
								}
				} else {
					
									if(!is_string($target)) $target= '';
								
									$query['meta_query'][] = array(
										'key' => $key,
										'value' => esc_attr( trim( $target ) ),
										'compare' => $like
									);
				}
							
			}
				
						
						
						
                 } //end for each
				 
				 } //end if 
	
			 if ($memberlist_verified)
			 {
				$query['meta_query'][] = array(
					'key' => 'usersultra_account_status',
					'value' => 'active',
					'compare' => 'LIKE'
				);
			}
			
			if (isset($memberlist_withavatar) && $memberlist_withavatar == 1){
				$query['meta_query'][] = array(
					'key' => 'profilepicture',
					'value' => '',
					'compare' => '!='
				);
			}
			
			
		/**
			CUSTOM SEARCH FILTERS 
		**
		**
		**/
		
		if (isset($_GET['uultra_combined_search'])) 
		{
			 //echo "YES1";
			
			/* Searchuser query param */
			$search_string = esc_attr( trim( get_value('uultra_combined_search') ) );
			
			if ($search_string != '') 
			{
			
				 if (get_value('uultra_combined_search_fields') != '' && get_value('uultra_combined_search') != '') 
				 {
					 
					// echo "YES3";
					//$customfilters = explode(',',$args['memberlist_filters']);
					
					$customfilters = explode(',', get_value('uultra_combined_search_fields'));

                    $combined_search_text = esc_sql(like_escape(get_value('uultra_combined_search')));

					
					if ($customfilters)
					{
						if (count($customfilters) > 1) 
						{
							//$query['meta_query']['relation'] = 'or';
						}
						
						//print_r($customfilters);
										
						$query['meta_query'][] = array(
							'key' => 'display_name',
							'value' => $search_string,
							'compare' => 'LIKE'
						);
						
					}
				}
				
				
				}
			
			}		
			
			
			if ($sortby) $query['orderby'] = $sortby;			
			if ($order) $query['order'] = strtoupper($order); 
			
			/** QUERY ARGS END **/
			
			$query['number'] = $per_page;
			$query['offset'] = $offset;
			
			/* Search mode */
		if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
		{
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			$user_count_query = new WP_User_Query($count_args);
						
		}

		if ($per_page) 
		{			
		
			/* Get Total Users */
			if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
			{
				$user_count = $user_count_query->get_results();								
				$total_users = $user_count ? count($user_count) : 1;
				
			} else {
				
				
				$result = count_users();
				$total_users = $result['total_users'];
			}
			
			$total_pages = ceil($total_users / $per_page);
		
		}
		
	
		
		$wp_user_query = new WP_User_Query($query);
		
		//print_r($query);
		
	
		
		if (! empty( $wp_user_query->results )) 
		{
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					//'base'         => @add_query_arg('paged','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','users-ultra'),
					'next_text'    => __('Next »','users-ultra'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
		}
		
		
		$this->searched_users = $arr;
		
     }
	 
	 /******************************************
	Get a cached query
	******************************************/
	function get_cached_query($query)
	{
		$cached = $this->get_cached_results;
		$testcache = serialize($query);
		if ( !isset($cached["$testcache"]) ) 
		{
			$cached["$testcache"] = new WP_User_Query( unserialize($testcache) );
			update_option('uultra_cached_results', $cached);
			$query = $cached["$testcache"];
		} else {
			$query = $cached["$testcache"];
		}
		
		return $query;
	}
	
	
	
	
	/**
	Display Members List Minified
	******************************************/
	public function show_online_users($atts)
	{
		global $xoouserultra;
		extract( shortcode_atts( array(		
		
			'template' => 'basic', // basic - mini -list
			'container_width' => '100%', // this is the main container dimension			
			'item_width' => '20%', // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'avatar_rounded', // avatar_rounded
			'pic_size_type' => 'fixed', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found_text' => __('Online Users','users-ultra'), // display total found						
		), $atts ) );
		
		$html = '';
		
		
		$search_array = array('list_per_page' => $list_per_page, 'list_order' => $list_order);		
		$users_list = $this->get_online_users($search_array);
		
		//display pages
		$disp_array = array('total' => $users_list['total'], 'text' => $display_total_found_text);
		
		//echo "template: " . $template;
		
		$html .='<div class="usersultra-front-directory-wrap">';
		
		if(count($users_list)>0)
		{
		
			$html .='<ul class="usersultra-online-users-results">';
			
			foreach($users_list as $user) : $user_id = $user->ID; 
			
			   if($pic_boder_type=="rounded")
			   {
				   $class_avatar = "avatar";				   
				}			
								
				
				if ($template=='' || $template=='basic') 
				{
					$html .=' <li class="rounded" style="width:'.$item_width.'">';
					$html .=' <div class="xoousers-prof-photo">
				   
						'.$xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);
				
					$html .=' </div>';
				
					$html .=' <div class="info-div">';					
					$html .='<p class="uu-direct-name">'. $xoouserultra->userpanel->get_display_name($user_id).'</p>';
						
					$html .=' <div class="social-icon-divider">  </div> ';
						
					 if ($optional_fields_to_display!="") 
					 {
						 $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);  				 
						
					 } 
					 
					 $html .=' </div>';					 
					 
					 $html .=' </li>';						 
					
					
				 }elseif($template=='mini'){ 				 
				 		
						
						$html .='<li class="avatar_mini " style="width:'.$pic_size.'px">';				 		
						$html .='<div class="xoousers-prof-photo"> ';
				   
						$html .= $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);
						 if ($optional_fields_to_display!="") 
					 {
						 $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);  				 
						
					 } 
					 
				
						$html .=' </div>';						
						$html .=' </li>';
						
				 }elseif($template=='list'){ 
				 
				 
				 		$html .=' <li class="list_mini_badges" style="width:'.$item_width.'">';				 	
						
						$html .='<div class="xoousers-prof-photo"> ';
						
										   
						$html .= $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);
						
						$html .='<p class="uu-direct-name-online-mini">'. $xoouserultra->userpanel->get_display_name($user_id).'</p>';
						
						 if ($optional_fields_to_display!="") 
						 {
							 $html .='<span>';
							 $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);  	
							 
							 $html .='</span>';			 
							
						 } 
						 
				
						$html .=' </div>';		
											
						$html .=' </li>';
				 
				 
				 } //end if
					 
				
				
				
				
			endforeach;
			
			$html .='</ul">';
		
		}else{
			
			$html .='<p">' . __("There are no online users ",'users-ultra').'</p>';
			
		}
		$html .=' </div>';
	
		//get template
		
		return $html;
		
	}
   
	/**
	Display Members List Minified
	******************************************/
	public function show_users_directory_mini($atts)
	{
		extract( shortcode_atts( array(
		
			'template' => 'directory_mini', //this is the template file's name
			'container_width' => '100%', // this is the main container dimension
			'item_width' => '10%', // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 10, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => __('Users','users-ultra'), // display total found
			
			'list_order' => 'DESC', // asc or desc ordering
		), $atts ) );
		
		
		$search_array = array('list_per_page' => $list_per_page, 'list_order' => $list_order);		
		$users_list = $this->users($search_array);
		
		//display pages
		$disp_array = array('total' => $users_list['total'], 'text' => $display_total_found_text);
		
		$total_f = $this->get_total_found($disp_array);		
	
		//get template
		require(xoousers_path.'/templates/'.xoousers_template."/".$template.".php");
		
	}
	
	
	public function get_current_page()
	{
		$page = "";		
		if(isset($_GET["ultra-page"]))
		{
			$page = $_GET["ultra-page"];
		
		}else{
			
			$page = 1;	
		
		}
		
		return $page;
		
	
	}
	
	
	
	/**
	Display Members List
	******************************************/
	public function show_users_directory($atts)
	{
		global $xoouserultra;
		
		$atts_temp = $atts;
		
		extract( shortcode_atts( array(
		
			'template' => 'directory_default', //this is the template file's name			
			'container_width' => '100%', // this is the main container dimension
			'item_width' => '21%', // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // 
			
			'display_to_logged_in_only' => '', // yes or null or empy
			'display_to_logged_in_only_text' => __('Only logged in users can see this page', 'users-ultra'), 
			
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => __('Users', 'users-ultra'), // display total found			
			'list_order' => 'ASC', // asc or desc ordering
			'sort_by' => 'ID', // 
			'role' => '', // filter by role
			'relation' => 'AND', // relation
			'exclude' => NULL // exclude by user id
		), $atts ) );
		
		
		$page = $this->get_current_page();		
		$search_array = array('list_per_page' => $list_per_page, 'order' => $list_order, 'sortby' => $sort_by);	
				
		$args= array('per_page' => $list_per_page, 'relation' => $relation, 'role' => $role, 'exclude' => $exclude, 'order' => $list_order, 'sortby' => $sort_by);
		
		
		
		$html ='';		
		$html .='<div class="usersultra-front-directory-wrap">
		       	<div class="usersultra-searcher">
			    </div>';
				
		//only logged in  
		
		if($display_to_logged_in_only=='yes' && !is_user_logged_in())
		{
			$html .=' <p>'. $display_to_logged_in_only_text.'</p>';
		
		}else{
			
			//display to all users			
			
			$this->current_users_page = $page;		
			$this->search_result($args);
						
			$users_list = $this->searched_users;
			
			//display pages
			$disp_array = array('total' => $users_list['total'], 'text' => $display_total_found_text);		
			$total_f = $this->get_total_found($disp_array);		
		
			
			  

			if (isset($users_list['paginate'])) {
				
			$html .=' <div class="usersultra-paginate top_display">'. $users_list['paginate'].'</div>';
			
			 } 
			
			if ($display_total_found=='yes') 
			{			
				$html .=$total_f;
			}
					
			
			$html .= $this->get_directory_template($users_list, $atts_temp);  	    
			
			if (isset($users_list['paginate'])) 
			{
				$html .=' <div class="usersultra-paginate bottom_display">'. $users_list['paginate'].'</div>';
			
			 } 
			 
		 
		 
	     } //end if logged in users
		 
		 
		 

 		$html .='</div>';
		
		
		return $html;
		
		
	}
	
	public function get_directory_template($users_list, $atts)
	{
		
		global $xoouserultra;
		
		$html = '';
		
		extract( shortcode_atts( array(
		
			'template' => 'directory_default', //this is the template file's name	
			'columns' => '', //  3 options, 1-column name 2-meta 3-visibility  - 4-tootlip
			'header_tooltips' => 'no', 		//no or yes
			'private_content_text' => __('Private Content Visible Only to Logged In Users','users-ultra') , 		//no or yes
			'private_content_protection_type' => '',  //1-null 2-role
			'show_to_user_role_list' => '',  // comma separated example; administrator,author
			'optional_fields_to_display' => '', //			
			
			'container_width' => '100%', // this is the main container dimension			
			'item_height' => 'auto', // auto height			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture					
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => 'Users' // display total found			
			
		), $atts ) );
		
		
        
		if(count($users_list['users'])>0)
		{
			
			if($template == 'directory_default')
			{
			
				$html .='<ul class="usersultra-front-results">';
				
				foreach($users_list['users'] as $user)
				{
					
					$user_id = $user->ID;			
							
				   if($pic_boder_type=="rounded")
				   {
					   $class_avatar = "avatar";
					   
					}			
					
					$html .='<li class="rounded" style="width:'.$item_width.'">';               
					$html .='<div class="xoousers-prof-photo">';				   
					$html .= $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);             
					   
					$html .=' </div> ';	
												   
					$html .=' <div class="info-div">';		
					$html .='<p class="uu-direct-name">'.  $xoouserultra->userpanel->get_display_name($user_id).'</p>';
						
						
					$html .=' <div class="social-icon-divider">                                       
						 
						  </div> ';
						
						if ($optional_fields_to_display!="") 
						{ 					 
						 
						   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display)  ;			 
						
						 }
						
						$html .=' </div> 
						 
						  <div class="uultra-view-profile-bar">';
						  
						  $html .='  <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">'.__("See Profile",'users-ultra').'</a>
						  
						  </div> ';
					
					
					$html .='</li>';
					
					
				}    //end for each
				
				 $html .=' </ul>';
			 
			}elseif($template == 'directory_table'){
				
				//columns = 
				$table_columns = array();
				$table_columns =  explode(",", $columns);
				
				$table_headers = array();
				$table_metas = array();
				
				//print_r($table_columns);
				
				foreach($table_columns as $col)
				{
					$col_data = explode(":",$col);					
					$table_headers[] = array('label'=>$col_data[0], 'tooltip'=>$col_data[3]);
					$table_metas[] = array('meta'=>$col_data[1] , 'visible'=>$col_data[2]);				
				
				}
				
				//turn on output buffering to capture script output
				ob_start();
				include(xoousers_path."templates/".xoousers_template."/directory_v2.php");	
				$html = ob_get_clean();
				return  $html;				
			
			}elseif($template == 'directory_minified'){
				
				ob_start();
				include(xoousers_path."templates/".xoousers_template."/directory_v3.php");	
				$html = ob_get_clean();
				return  $html;					
			 
			} //end if			 
			 
       } // end if
	   
	    
		 
	   return $html;
	
	
	}
	
	
	
	public function get_result_pages($reg_count,$page, $list_perpage)
	{
			
		
		$total_pages = ceil($reg_count / $list_perpage);
		
		
		$big = 999999999; // need an unlikely integer
		$arr = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','users-ultra'),
					'next_text'    => __('Next','users-ultra'),
					'type'         => 'plain',
				));
	return $arr;
	
	}
	
	public function get_custom_search_fields($fields_list)
	{
		
		$display .= '<div class="xoouserultra-field-value">';
					
					switch($field) {
					
												
						case 'text':
							$display .= '<input type="text" class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
							break;							
							
						case 'datetime':
						    $display .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $this->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
								$loop = explode(PHP_EOL, $choices);
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'">';
								
								foreach($loop as $option)
								{
									
								$option = trim(stripslashes($option));
								    
								$display .= '<option value="'.$option.'" '.selected( $this->get_post_value($meta), $option, 0 ).'>'.$option.'</option>';
								}
								$display .= '</select>';
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'radio':
						
							if (isset($array[$key]['choices']))
							{
								$loop = explode(PHP_EOL, $choices);
							}
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim(stripslashes($option));
									$display .= '<label class="xoouserultra-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> '.$option.'</label>';
									
									$counter++;
									
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
						
							if (isset($array[$key]['choices'])) 
							{
								$loop = explode(PHP_EOL, $choices);
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim(stripslashes($option));
									$display .= '<label class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> '.$option.'</label>';
									
									$counter++;
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
							
					}
		
	
	        $display .= '</div>';
			
			return  $display ;
	
	}
	
	public function get_total_found($users_list)
	{
		extract($users_list);
		
		if($total=="" ){$total=0;}
		
		$html = '<div class="uultra-search-results">
			<h1>'.__('Total found: ','users-ultra').''.$total .' '.$text.'</h1>
			
			</div>';
			
		return $html;
	
	
	}
		
	public  function public_profile_get_album_link ($id, $user_id) 
	{
		$url ="";		
		$url = $this->get_user_profile_permalink($user_id)."?gal_id=".$id;		
		
		return $url;
	
	}
	
	public  function public_profile_get_photo_link ($id, $user_id) 
	{
		$url ="";		
		$url = $this->get_user_profile_permalink($user_id)."?photo_id=".$id;		
		
		return $url;
	
	}	
	
	
	public  function public_profile_display_social ($user_id) 
	{
		 global  $xoouserultra;
		 
		 $array = get_option('usersultra_profile_fields');	
		 		
		$html_social ="<div class='uultra-prof-social-icon'>";
			

		foreach($array as $key=>$field) 
		{					
			
			if($field['social']==1)
			{
									
				$icon = $field['icon'];
				
				//get meta
				$social_meta = get_user_meta($user_id, $field['meta'], true);
				
				if($social_meta!=""){
				
				$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";
				
				}
				
				
				
			}
			
		}	
		
		$html_social .="</div>";
		
		return $html_social;
	
	}
	
	public function get_user_country_flag($user_id)
	{
		global  $xoouserultra;
		
		$u_meta = get_user_meta($user_id, 'country', true);
		
		//get country ISO code	
		$img = "";	
											
		$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));	
		
		if($isocode!=0)		
		{	
						
			$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
			$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';
		
		}
		
		return  $img;
	
	
	}
	
	public  function display_optional_fields_pro ($user_id, $display_country_flag, $fields_to_display) 
	{
		 global  $xoouserultra;
		
		$fields = array();
		
		$array = array();
		$fields_list = "";
		$fields  = explode(',', $fields_to_display);
		
		if(is_array($fields) && $fields_to_display!="")
		{
		
			foreach ($fields as $field) 
			{
				//get meta
				
				$u_meta = get_user_meta($user_id, $field, true);
				
				if( $field =='country')
				{
					//rule applied to country only
				
					if($display_country_flag=='only') //only flag
					{
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');						
						
						}else{
							
						//get country ISO code		
												
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							
							$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
							$fields_list .= "<p class='country_name uultra-respo-prof-data-hide'>".$img."</p>";
						
						
						}					
										
					}elseif($display_country_flag=='both'){
						
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');;
							
						
						}else{
						
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							if($isocode!="0")
							{
								$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
								$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
								$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
							
							}
						
						}
					
					}elseif($display_country_flag=='name'){					
						
						$fields_list .= "<p class='country_name'>".$u_meta."</p>";		
							
					
					}
				
				}elseif($field =='description'){
					
					if($u_meta=="")				
					{
						$u_meta = __("This user hasn't entered a description yet", 'users-ultra');
					
					
					}else{
						
						$u_meta = $this->get_user_desc_exerpt($u_meta,15);
						
					}
					
					$fields_list .= "<p class='desc'>".$u_meta."</p>";
					
				
				}elseif($field =='badges'){
					
					$badges = $xoouserultra->badge->uultra_show_badges($user_id);				
					$fields_list .= $badges;
					
				
					
				}elseif($field =='social'){ //this rule applies only to social icons				
									
								
					//get user form		
				    $custom_form = get_user_meta($user_id, 'uultra_custom_registration_form', true);						
					if($custom_form!="")
					{
						$custom_form = 'usersultra_profile_fields_'.$custom_form;	 	
						$array = get_option($custom_form);
						
					}else{
						
						$array = get_option('usersultra_profile_fields');			
						
					}					
					
							
					$html_social ="<div class='uultra-social-icons uultra-respo-prof-data-hide'><ul>";
					
					if(!is_array($array))
					{
						$array = array();
					}
	
					foreach($array as $key=>$field) 
					{
						$_fsocial = "";
						
						if(isset($field['social']))	
						{
							$_fsocial = $field['social'];					
						}		
					
						
						if($_fsocial==1)
						{
												
							$icon = $field['icon'];
							
							//get meta
							$social_meta = get_user_meta($user_id, $field['meta'], true);		
							
							//echo "Social meta: " .$field['meta'];				
							
														
							if($social_meta!="")
							{
								$social_meta = apply_filters('uultra_social_url_' .$field['meta'], $social_meta);
								
								$html_social .="<li><a href='".$social_meta."' target='_blank'><i class='uultra-socialicons fa fa-".$icon." '></i></a></li>";
					
							}
							
							
							
						}
						
					}	
					
					$html_social .="</ul></div>";			
					
					
					$fields_list .= $html_social;
					
					
				
				
				}elseif($field =='rating'){ //this rule applies only to rating
				
								
					$fields_list.= "<div class='ratebox'>";
					$fields_list.= $xoouserultra->rating->get_rating($user_id,"user_id");
					$fields_list.= "</div>";
				
				
				}elseif($field =='like'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_item_likes_profile($user_id,"user");	
				
				}elseif($field =='friend'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_friends($user_id);		
							
				
				}else{
						
					$fields_list .= "<p>".$u_meta."</p>";
				
				
				
				}
				
				
			
			} //end for
			
		} //end if
		
		return $fields_list;
		
		
	
	
	}
	
	public  function display_optional_fields_pro_minified ($user_id, $display_country_flag, $fields_to_display) 
	{
		 global  $xoouserultra;
		
		$fields = array();
		$fields_list = "";
		$fields  = explode(',', $fields_to_display);
		
		if(is_array($fields) && $fields_to_display!="")
		{
		
			foreach ($fields as $field) 
			{
				//get meta
				
				$u_meta = get_user_meta($user_id, $field, true);
				
				if( $field =='country')
				{
					//rule applied to country only
				
					if($display_country_flag=='only') //only flag
					{
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');						
						
						}else{
							
						//get country ISO code		
												
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							
							$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
							$fields_list .= "<p class='country_name'>".$img."</p>";
						
						
						}					
										
					}elseif($display_country_flag=='both'){
						
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');;
							
						
						}else{
						
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							if($isocode!="0")
							{
								$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
								$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
								$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
							
							}
						
						}
					
					}elseif($display_country_flag=='name'){					
						
						$fields_list .= "<p class='country_name'>".$u_meta."</p>";		
							
					
					}
				
				}elseif($field =='description'){
					
					if($u_meta=="")				
					{
						$u_meta = __("This user hasn't entered a description yet", 'users-ultra');
					
					
					}else{
						
						$u_meta = $this->get_user_desc_exerpt($u_meta,15);
						
					}
					
					$fields_list .= "<p class='desc'>".$u_meta."</p>";
					
				
				}elseif($field =='badges'){
					
					$badges = $xoouserultra->badge->uultra_show_badges($user_id);				
					$fields_list .= $badges;
					
				
					
				}elseif($field =='social'){ //this rule applies only to social icons					
									
								
					//get user form		
				    $custom_form = get_user_meta($user_id, 'uultra_custom_registration_form', true);						
					if($custom_form!="")
					{
						$custom_form = 'usersultra_profile_fields_'.$custom_form;	 	
						$array = get_option($custom_form);
						
					}else{
						
						$array = get_option('usersultra_profile_fields');			
						
					}					
					
							
					$html_social ="<div class='uultra-social-icons uultra-respo-prof-data-hide'><ul>";
						
	
					foreach($array as $key=>$field) 
					{
						$_fsocial = "";
						
						if(isset($field['social']))	
						{
							$_fsocial = $field['social'];					
						}		
					
						
						if($_fsocial==1)
						{
												
							$icon = $field['icon'];
							
							//get meta
							$social_meta = get_user_meta($user_id, $field['meta'], true);		
							
							//echo "Social meta: " .$field['meta'];				
							
														
							if($social_meta!="")
							{
								$social_meta = apply_filters('uultra_social_url_' .$field['meta'], $social_meta);
								
								$html_social .="<li><a href='".$social_meta."' target='_blank'><i class='uultra-socialicons fa fa-".$icon." '></i></a></li>";
					
							}
							
							
							
						}
						
					}	
					
					$html_social .="</ul></div>";			
					
					
					$fields_list .= $html_social;
					
					
				
				
				}elseif($field =='rating'){ //this rule applies only to rating
				
								
					$fields_list.= "<div class='ratebox uultra-respo-prof-data-hide'>";
					$fields_list.= $xoouserultra->rating->get_rating($user_id,"user_id");
					$fields_list.= "</div>";
				
				
				}elseif($field =='like'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_item_likes($user_id,"user");	
				
				}elseif($field =='friend'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_friends($user_id);							
				
				}else{
						
					$fields_list .= "<p>".$u_meta."</p>";				
				
				}
				
				
			
			} //end for
			
		} //end if
		
		return $fields_list;
		
		
	
	
	}
	
	/*Used for the directory listings*/	
	public  function display_optional_fields ($user_id, $display_country_flag, $fields) 
	{
		 global  $xoouserultra;
		
		$fields_list = "";
		$fields  = explode(',', $fields);
		
		foreach ($fields as $field) 
		{
			//get meta
			
			$u_meta = get_user_meta($user_id, $field, true);
			
			if( $field =='country')
			{
				//rule applied to country only
			
				if($display_country_flag=='only') //only flag
				{
					if($u_meta=="")				
				    {
						//$fields_list .= __("Country not available", 'users-ultra');						
					
					}else{
						
					//get country ISO code		
											
						$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
						
						$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
						$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
						$fields_list .= "<p class='country_name'>".$img."</p>";
					
					
					}					
									
				}elseif($display_country_flag=='both'){
					
					if($u_meta=="")				
				    {
						$fields_list .= __("Country not available", 'users-ultra');;
						
					
					}else{
					
						$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
						if($isocode!="0")
						{
							$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
							$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
						
						}
					
					}
				
				}elseif($display_country_flag=='name'){					
					
					$fields_list .= "<p class='country_name'>".$u_meta."</p>";		
						
				
				}
			
			}elseif($field =='description'){
				
				if($u_meta=="")				
				{
					$u_meta = __("This user hasn't entered a description yet", 'users-ultra');
				
				
				}else{
					
					$u_meta = $this->get_user_desc_exerpt($u_meta,15);
					
				}
				
				$fields_list .= "<p class='uultra-card-profile-desc'>".$u_meta."</p>";
				
			}elseif($field =='badges'){
					
					$badges = $xoouserultra->badge->uultra_show_badges($user_id);				
					$fields_list .= $badges;	
				
			}elseif($field =='social'){ //this rule applies only to social icons
				
								
				//get user form		
				$custom_form = get_user_meta($user_id, 'uultra_custom_registration_form', true);						
				if($custom_form!="")
				{
					$custom_form = 'usersultra_profile_fields_'.$custom_form;	 	
					$array = get_option($custom_form);
						
				}else{
						
					$array = get_option('usersultra_profile_fields');			
						
				}						
							
				$html_social ="<div class='uultra-prof-social-icon'>";
					
                
				if(is_array($array))
				{
					foreach($array as $key=>$field) 
					{
						$_fsocial = "";
						
						if(isset($field['social']))	
						{
							$_fsocial = $field['social'];					
						}		
					
						
						if($_fsocial==1)
						{
												
							$icon = $field['icon'];	
												
							//get meta
							$social_meta = get_user_meta($user_id, $field['meta'], true);						
							
							 if($social_meta!="")
							 {
									$social_meta = apply_filters('uultra_social_url_' .$field['meta'], $social_meta);								
									$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";					
							 }
							
												
							
						}
						
					} //end for each
				
				} //end if	
				
				$html_social .="</div>";			
				
				
				$fields_list .= $html_social;
				
				
			
			
			}elseif($field =='rating'){ //this rule applies only to rating
			
			   				
				$fields_list.= "<div class='ratebox'>";
				$fields_list.= $xoouserultra->rating->get_rating($user_id,"user_id");
				$fields_list.= "</div>";
			
			
			}elseif($field =='like'){ //like rules			   				
				
				$fields_list.= $xoouserultra->social->get_item_likes($user_id,"user");	
			
			}elseif($field =='friend'){ //like rules			   				
				
				$fields_list.= $xoouserultra->social->get_friends($user_id);
			
			}elseif($field =='follow'){ //add follow button			   				
				
				$fields_list.= $xoouserultra->social->get_follow_button($user_id);		
						
			
			}else{
					
				$fields_list .= "<p>".$u_meta."</p>";
			
			
			
			}
			
			
		
		}
		
		return $fields_list;
		
		
	
	
	}
	
	//this is used for the new directory style which display users in tables
	public  function display_fields_on_table_directory ($user_id, $pic_size, $display_country_flag, $field) 
	{
		global  $xoouserultra;
		
		//echo "User  : " . $user_id;
		//get meta				
		$u_meta = get_user_meta($user_id, $field, true);
				
				if( $field =='country')
				{
					//rule applied to country only				
					if($display_country_flag=='only') //only flag
					{
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');						
						
						}else{
							
						//get country ISO code		
												
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							
							$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
							$fields_list .= "<p class='country_name'>".$img."</p>";
						
						
						}					
										
					}elseif($display_country_flag=='both'){
						
						if($u_meta=="")				
						{
							//$fields_list .= __("Country not available", 'users-ultra');;
							
						
						}else{
						
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							if($isocode!="0")
							{
								$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
								$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
								$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
							
							}
						
						}
					
					}elseif($display_country_flag=='name'){					
						
						$fields_list .= $u_meta;		
							
					
					}
					
					if($fields_list=='')
					{
						$fields_list .= 'n/a';
					
					}
				
				}elseif($field =='description'){
					
					if($u_meta=="")				
					{
						$u_meta = __("This user hasn't entered a description yet", 'users-ultra');
					
					
					}else{
						
						$u_meta = $this->get_user_desc_exerpt($u_meta,15);
						
					}
					
					$fields_list .= $u_meta;
					
				
				}elseif($field =='badges'){
					
					$badges = $xoouserultra->badge->uultra_show_badges($user_id);				
					$fields_list .= $badges;
					
				
				}elseif($field =='avatar'){
					
									
					$fields_list .= $this->get_user_pic( $user_id, $pic_size, 'avatar', 'rounded', 'fixed');
				
				}elseif($field =='role'){					
									
					$fields_list =  $this->get_all_user_roles($user_id);
				
				}elseif($field =='message'){					
									
					$fields_list =  '<a href="#" class="uultra-directory-send-private-message-box" title="' .__('Send Private Message', 'users-ultra'). '" '.$qtip_style.' user-id='.$user_id.'><i class="fa fa-envelope-o reg_tooltip"></i></a>';	
					
					$fields_list .= $this->contact_me_public_form_directory($user_id);		
					
				}elseif($field =='social'){ //this rule applies only to social icons		
									
								
					//get user form		
				    $custom_form = get_user_meta($user_id, 'uultra_custom_registration_form', true);						
					if($custom_form!="")
					{
						$custom_form = 'usersultra_profile_fields_'.$custom_form;	 	
						$array = get_option($custom_form);
						
					}else{
						
						$array = get_option('usersultra_profile_fields');			
						
					}					
					
							
					$html_social ="<div class='uultra-social-icons'><ul>";
						
	
					foreach($array as $key=>$field) 
					{
						$_fsocial = "";
						
						if(isset($field['social']))	
						{
							$_fsocial = $field['social'];					
						}		
					
						
						if($_fsocial==1)
						{
												
							$icon = $field['icon'];
							
							//get meta
							$social_meta = get_user_meta($user_id, $field['meta'], true);		
							
							//echo "Social meta: " .$field['meta'];				
							
														
							if($social_meta!="")
							{
								$social_meta = apply_filters('uultra_social_url_' .$field['meta'], $social_meta);								
								$html_social .="<li><a href='".$social_meta."' target='_blank'><i class='uultra-socialicons fa fa-".$icon." '></i></a></li>";
					
							}						
							
						}
						
					}	
					
					$html_social .="</ul></div>";					
					
					$fields_list .= $html_social;
					
					
				
				
				}elseif($field =='rating'){ //this rule applies only to rating
				
								
					$fields_list.= "<div class='ratebox uultra-ratebox-left'>";
					$fields_list.= $xoouserultra->rating->get_rating($user_id,"user_id");
					$fields_list.= "</div>";
				
				
				}elseif($field =='like'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_item_likes($user_id,"user");	
				
				}elseif($field =='friend'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_friends($user_id);		
							
				
				}else{
						
					$fields_list .= $u_meta;			
				
				}
				
				if($fields_list=='')
				{
					$fields_list .= __('n/a','users-ultra');
					
				}
				
				
			
					
		return $fields_list;
		
		
	
	
	}
	
	
	
	public function get_user_social_icons($user_id)
	{
		
		
		$array = get_option('usersultra_profile_fields');			
		$html_social ="<div class='uultra-prof-social-icon'>";
					

				foreach($array as $key=>$field) 
				{			
				
					
					if($field['social']==1)
					{
											
						$icon = $field['icon'];
						
						//get meta
						$social_meta = get_user_meta($user_id, $field['meta'], true);
						
						$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";
						
						
						
					}
					
				}	
				
				$html_social .="</div>";	
				
				return $html_social;
	
	
	}
	
	
	public function get_user_social_icons_widget($user_id)
	{
		
		
		$array = get_option('usersultra_profile_fields');			
		$html_social =' <div class="uultra-social-icons"><ul>';
					

				foreach($array as $key=>$field) 
				{			
				
					
					if($field['social']==1)
					{
											
						$icon = $field['icon'];
						
						//get meta
						$social_meta = get_user_meta($user_id, $field['meta'], true);
						
						
						if($social_meta!="")
						{
							$html_social .="<li><a href='".$social_meta."' target='_blank'> <i class='fa fa-lg uultra-socialicons fa-".$icon."'></i></a></li>";
						
						}
						
						
						
					}
					
				}	
				
				$html_social .="<ul></div>";	
				
				return $html_social;
	
	
	}
	/* Get picture by ID */
	function refresh_avatar() 
	{
		$user_id = get_current_user_id();
		
		echo $this->get_user_pic( $user_id, $pic_size, 'avatar', 'rounded', 'dynamic');
		die();
	}
	
	/* delete avatar */
	function delete_user_avatar() 
	{
		$user_id = get_current_user_id();
		
		update_user_meta($user_id, 'user_pic', '');
		die();
	}
	
	
	/* Overrides default get avatar function  */
	function uultra_get_avatar( $avatar, $id_or_email, $size, $default, $alt='' ) 
	{
		global $xoouserultra;
		
		$pic_boder_type = '';
		$cache_rand = '';
		
		
		if (isset($id_or_email->user_id))
		{
			$id_or_email = $id_or_email->user_id;
			
		} elseif (is_email($id_or_email)){
			
			$user = get_user_by('email', $id_or_email);
			$id_or_email = $user->ID;
		}
		
		
		$site_url = site_url()."/";
		 
		
		$pic_size = "";
		
		$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
		$path = $site_url.$upload_folder."/".$id_or_email."/";			
		$author_pic = get_the_author_meta('user_pic', $id_or_email);
		
		//get user url
		$user_url=$this->get_user_profile_permalink($id_or_email);
		
		if($pic_boder_type==NULL)
		{
			$pic_boder_type = 'uultra-user-avatar-default-style';
		
		}
		
		if($xoouserultra->get_option('uultra_force_cache_issue')=='yes')
		{
			$cache_by_pass = '?rand_cache='.$cache_rand;
		
		}
		
		if($author_pic!='')		
		{
			
			//get user's main picture - medium size will be used to be displayed			
			 $avatar_pic = $path.$author_pic;
			 $avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.''.$cache_by_pass.'" class="'.$pic_boder_type.'"  id="uultra-avatar-img-'.$id.'" style="max-width:64px"/></a>';
			 
			 return $avatar;
			
		}else{
			
			
			return $avatar;
			
			
		}
	
		
		
		
		
		
	}
	
	
	/* Get picture by ID */
	function get_user_pic( $id, $size, $pic_type=NULL, $pic_boder_type= NULL, $size_type=NULL, $with_url=true  ) 
	{
		
		 global  $xoouserultra;
		 
		 require_once(ABSPATH . 'wp-includes/link-template.php');
	 
		
		$site_url = site_url()."/";
		
		//rand_val_cache		
		$cache_rand = time();
			 
		$avatar = "";
		$pic_size = "";
		
		$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
		$path = $site_url.$upload_folder."/".$id."/";			
		$author_pic = get_the_author_meta('user_pic', $id);
		
		//get user url
		$user_url=$this->get_user_profile_permalink($id);
		
		if($pic_boder_type=='none'){$pic_boder_type='uultra-none';}
		
		
		if($size_type=="fixed" || $size_type=="")
		{
			$dimension = "width:";
			$dimension_2 = "height:";
		}
		
		if($size_type=="dynamic" )
		{
			$dimension = "max-width:";
		
		}
		
		if($size!="")
		{
			$pic_size = $dimension.$size."px".";".$dimension_2.$size."px";
		
		}
		
		if($xoouserultra->get_option('uultra_force_cache_issue')=='yes')
		{
			$cache_by_pass = '?rand_cache='.$cache_rand;
		
		}
		
		if($pic_boder_type==NULL)
		{
			$pic_boder_type = 'uultra-user-avatar-default-style';
		
		}
		
		
		
		if($pic_type=='avatar')
		{
		
			if ($author_pic  != '') 
			{
				$avatar_pic = $path.$author_pic;
				$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.''.$cache_by_pass.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="uultra-avatar-img-'.$id.'" /></a>';
				
			} else {
				
				//get gravatar				
				$user = get_user_by( 'id', $id );						
				$has_gravatar = get_user_meta( $id, 'uuultra_has_gravatar', true); 		
				
				//check if facebook is required.				
				$facebook_avatar = $xoouserultra->get_option('uultra_use_facebook_avatar');	
				$facebook_id = get_user_meta( $id, 'xoouser_ultra_facebook_id', true); 	
								
				
				if(($has_gravatar==1 && $facebook_avatar=='') || ($has_gravatar==1 && $facebook_avatar=='no'))
				{					
					$avatar_pic = "//gravatar.com/avatar/" . md5(strtolower($user->user_email)) . "?d=" . urlencode($default) . "&s=" . $size;
				
				
				}elseif($facebook_avatar=='yes' && $facebook_id!=""){
					
					$avatar_pic = "//graph.facebook.com/".$facebook_id."/picture?type=normal";
				
				}else{
					
					//check if admin uploaded a custom picture
					
					$custom_avatar_file = get_option('uultra_default_user_avatar');
					if($custom_avatar_file=='')
					{
						$avatar_pic = "//gravatar.com/avatar/" . md5(strtolower($user->user_email)) . "?d=" . urlencode($default) . "&s=" . $size;
					
					}else{
						
						$upload_folder =  $xoouserultra->get_option('media_uploading_folder');						
						$avatar_pic = $site_url.$upload_folder."/custom_avatar_image/".$custom_avatar_file;
						
					}				
				
				} //end if has gravatar
		 
				//$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="uultra-avatar-img-'.$id.'" title="'.$user->display_name.'" /></a>';	
				
				
				if($with_url)
				{
		 
					$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="bup-avatar-img-'.$id.'" title="'.$user->display_name.'" /></a>';
				
				}else{
					
					$avatar=  '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="bup-avatar-img-'.$id.'" title="'.$user->display_name.'" />';
				
				}	
			
				
			}
		
		}elseif($pic_type=='mainpicture'){
			    
				//get user's main picture - medium size will be used to be displayed			
			    $avatar_pic = $path.$author_pic;
				$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="uultra-avatar-img-'.$id.'"/></a>';
		
		
		}
		
		return $avatar;
	}
	
	function validate_if_user_has_gravatar($user_id)
	{
		
		$has_gravatar = get_user_meta( $user_id, 'uuultra_has_gravatar', true);
		
		if($has_gravatar=='' || $has_gravatar=='0')
		{			
			//check if user has a valid gravatar
			if($this->uultra_validate_gravatar($user_id))
			{
				//has a valid gravatar				
				update_user_meta($user_id, 'uuultra_has_gravatar', 1);			
			
			}else{
				
				delete_user_meta($user_id, 'uuultra_has_gravatar')	;		
				
			}
		
		
		}
	
	}
	
	
	/**
	 * Utility function to check if a gravatar exists for a given email or id
	 * @param int|string|object $id_or_email A user ID,  email address, or comment object
	 * @return bool if the gravatar exists or not
	 */
	
	function uultra_validate_gravatar($id_or_email) 
	{
	  //id or email code borrowed from wp-includes/pluggable.php
		$email = '';
		if ( is_numeric($id_or_email) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			if ( $user )
				$email = $user->user_email;
		} elseif ( is_object($id_or_email) ) {
			// No avatar for pingbacks or trackbacks
			$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
			if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
				return false;
	
			if ( !empty($id_or_email->user_id) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);
				if ( $user)
					$email = $user->user_email;
			} elseif ( !empty($id_or_email->comment_author_email) ) {
				$email = $id_or_email->comment_author_email;
			}
		} else {
			$email = $id_or_email;
		}
	
		$hashkey = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';
	
		$data = wp_cache_get($hashkey);
		if (false === $data) {
			$response = wp_remote_head($uri);
			if( is_wp_error($response) ) {
				$data = 'not200';
			} else {
				$data = $response['response']['code'];
			}
			wp_cache_set($hashkey, $data, $group = '', $expire = 60*5);
	
		}		
		if ($data == '200'){
			return true;
		} else {
			return false;
		}
	}
	
	function validate_gravatar($email) 
	{
		// Craft a potential url and test its headers
		/*$hash = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers($uri);
		if (!preg_match("|200|", $headers[0])) {
			$has_valid_avatar = FALSE;
		} else {
			$has_valid_avatar = TRUE;
		}*/
		$has_valid_avatar = TRUE;
		return $has_valid_avatar;
	}

	function get_avatar_url( $avatar) 
	{

		preg_match( '#src=["|\'](.+)["|\']#Uuis', $avatar, $matches );
	
		return ( isset( $matches[1] ) && ! empty( $matches[1]) ) ?
			(string) $matches[1] : '';  
	
	}
	
	public function avatar_uploader($avatar_is_called=NULL) 
	{
		
	   // Uploading functionality trigger:
	  // (Most of the code comes from media.php and handlers.js)
	      $template_dir = get_template_directory_uri();
?>
		
		<div id="uploadContainer" style="margin-top: 10px;">
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
				<div id="plupload-upload-ui-avatar" class="hide-if-no-js">
                
					<div id="drag-drop-area-avatar">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php	_e('Drop '.$avatar_is_called.' here', 'users-ultra') ; ?></p>
							<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
							                            
                            
<div class="uultra-uploader-buttons" id="plupload-browse-button-avatar">
                            <?php	_e('Select Image', 'users-ultra') ; ?>
                            </div>
                            
                            <div class="uultra-uploader-buttons-delete-cancel" id="btn-delete-user-avatar">
                            <?php	_e('Remove '.$avatar_is_called.'', 'users-ultra') ; ?>
                            </div>
                            
                           
														
						</div>
                        
                        <div id="progressbar-avatar"></div>                 
                         <div id="symposium_filelist_avatar" class="cb"></div>
					</div>
				</div>
                
                 
			
			</div>
            
           
		</div>
        
         <form id="uultra_frm_img_cropper" name="uultra_frm_img_cropper" method="post">                
                
                	<input type="hidden" name="image_to_crop" value="" id="image_to_crop" />
                    <input type="hidden" name="crop_image" value="crop_image" id="crop_image" />                   
                
                </form>

		<?php
			
			$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button-avatar',
				'container'           => 'plupload-upload-ui-avatar',
				'drop_element'        => 'uultra-drag-avatar-section',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => false,
				'max_file_size'       => wp_max_upload_size().'b',
				//'max_file_size'       => get_option('drag-drop-filesize').'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', "xoousers"), 'extensions' => "jpg,png,gif,jpeg")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'action'      => 'ajax_upload_avatar' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init); ?>

			<script type="text/javascript">
			
				jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_avatar = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

					// Check for drag'n'drop functionality:
					uploader_avatar.bind('Init', function(up){
						
						var uploaddiv_avatar = $('#plupload-upload-ui-avatar');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_avatar.addClass('drag-drop');
							
							$('#drag-drop-area-avatar')
								.bind('dragover.wp-uploader', function(){ uploaddiv_avatar.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_avatar.removeClass('drag-over'); });

						} else{
							uploaddiv_avatar.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_avatar.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_avatar.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
						// Limit to one limit:
						if (files.length > 1){
							alert("<?php _e('You may only upload one image at a time!', 'users-ultra'); ?>");
							return false;
						}
						
						// Remove extra files:
						if (up.files.length > 1){
							up.removeFile(uploader_avatar.files[0]);
							up.refresh();
						}
						
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert("<?php _e('The file you selected exceeds the maximum filesize limit.', 'users-ultra'); ?>");
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							jQuery('#symposium_filelist_avatar').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_avatar.start();
						
					});
					
					// A new file was uploaded:
					uploader_avatar.bind('FileUploaded', function(up, file, response){					
						
						
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;							
						
						$("#image_to_crop").val(img_name);
						$("#uultra_frm_img_cropper").submit();

						
						
						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "refresh_avatar"},
							
							success: function(data){
								
								//$( "#uu-upload-avatar-box" ).slideUp("slow");								
								$("#uu-backend-avatar-section").html(data);
								
								//jQuery("#uu-message-noti-id").slideDown();
								//setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
								
								
								}
						});
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_avatar.bind('Error', function(up, err) {
						alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_avatar.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: progressBarValue
						});
						
						jQuery('#progressbar-avatar').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					});
					
					// Close window after upload ///////////////////////////////
					uploader_avatar.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: 0
						});
						
						
					});
					
					
					
				});
				
					
			</script>
			
		<?php
	
	
	}
	
	function get_one_user_with_key($key)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_very_key',                    
			'meta_value' => $key,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();	
		
		if(count($users)>0)
		{
			foreach ($users as $user)
			{
				return $user;
			
			}
			
		
		}else{
			
			
			
		}
		
	
	}
	
	function get_user_with_key($key)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_very_key',                    
			'meta_value' => $key,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();	
		
		if(count($users)>0)
		{
			return true;
		
		}else{
			
			return false;
			
		}
		
	
	}
	
	function users_shortcodes( $args )
	{
		global  $wpdb,  $xoouserultra;
		
		
		extract($args);
		
		$page = (!empty($_GET['ultra-page'])) ? $_GET['ultra-page'] : 1;
		$offset = ( ($page -1) * $args['list_per_page'] );

		/* setup query params */
		//$query = $this->setup_query( $args );
		
		/* pagi stuff */
		$query['number'] = $args['list_per_page'];
		$query['offset'] = $offset;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$count_args = array_merge($query, array('number'=>99999999999));
		unset($count_args['offset']);
		
		$user_count_query = new WP_User_Query($count_args);

		if ($args['list_per_page']) {
		$user_count = $user_count_query->get_results();
		$total_users = $user_count ? count($user_count) : 1;
		$total_pages = ceil($total_users / $args['list_per_page']);
		}

		$wp_user_query = new WP_User_Query($query);
		
		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','users-ultra'),
					'next_text'    => __('Next','users-ultra'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	
	function users( $args )
	{
		global  $wpdb,  $xoouserultra;
		$blog_id = get_current_blog_id();
		
		extract($args);
		
		
		$page = (!empty($_GET['ultra-page'])) ? $_GET['ultra-page'] : 1;		
		$offset = ( ($page -1) * $args['list_per_page'] );
		
		if(isset($_GET["usersultra_searchuser"]) && $_GET["usersultra_searchuser"] !="")
		{
			$key = $_GET["usersultra_searchuser"];
						
			$query['meta_query'] = array('relation' => 'AND' );			
			$query['meta_query'][] = array(
				'key' => 'display_name',
				'value' => $key,
				'compare' => 'LIKE'
			);	
			
		}
				
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);		
				
		$query['number'] = $args['list_per_page'];
		$query['offset'] = $offset;
		$query['order' ] = $list_order;
		$query['orderby' ] = 'ID';
		

		$count_args = array_merge($query, array('number'=>99999999999));	
		
		unset($count_args['offset']);
		
		$user_count_query = new WP_User_Query($count_args);

		//calculates pages
		if ($args['list_per_page'])
		{
			$user_count = $user_count_query->get_results();
		    $total_users = $user_count ? count($user_count) : 1;
		    $total_pages = ceil($total_users / $args['list_per_page']);
		}
		

		$wp_user_query = new WP_User_Query($query);
		
		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','users-ultra'),
					'next_text'    => __('Next','users-ultra'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
			
			$arr['total'] =$total_users;
			
		return $arr;
	}
	
	
	
	
	public function get_members_list($args)
	{
		global  $wpdb,  $xoouserultra;
						
		extract($args);
		
		$blog_id = get_current_blog_id();
		
		$query['meta_query'] = array('relation' => strtoupper($list_relation) );
		
		$query['meta_query'][] = array(
				'key' => 'userultra_verified',
				'value' => 1,
				'compare' => '='
			);
		

		//$query['orderby'] = $list_sortby;
		
		//$query['order'] = strtoupper($list_order); // asc to ASC
		
		$query['number'] = $list_per_page;	
				
			
		$wp_user_query = $xoouserultra->get_results($query);
		
		
		
		if (! empty( $wp_user_query->results ))
		{

			$arr['users'] = $wp_user_query->results;
			
			
		}
		if (isset($arr)) return $arr;
		
	}
	
	/*---->> Check if user is active before login  ****/
	
	function is_active($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		
		if ($checkuser == 'active' || $checkuser == '') //this is a tweak for already members
		{
			return true;
		
		}else{
			
			return false;
		
		}			
		
	}
	
	/*---->> Check if user is pending activation by admin   ****/
	function get_status($user_id) 
	{
		$status ="";
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		
		if ($checkuser == 'pending') 
		{
			$status =  __("Pending","xoousers");
			
		}elseif($checkuser == 'pending_admin'){
			
			$status =__("Pending Admin","xoousers");
		
		}elseif($checkuser == 'active' || $checkuser == ''){
			
			$status =  __("Active","xoousers");
		
		}
		
		 
			
		return $status;
	}
	
	/*---->> Check if user is pending activation by admin   ****/
	function is_pending($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		if ($checkuser == 'pending' || $checkuser == 'pending_admin')
			return true;
		return false;
	}
	
	/*---->> Activate user    ****/
	function activate($user_id, $user_login = null)
	{
		if ($user_login != '')
		{
			$user = get_user_by('login', $user_login);
			$user_id = $user->ID;
		}
		delete_user_meta($user_id, 'usersultra_account_verify');
		update_user_meta($user_id, 'usersultra_account_status', 'active');
		
		$password = get_user_meta($user_id, 'usersultra_pending_pass', true);
		$form = get_user_meta($user_id, 'usersultra_pending_form', true);
		
		//notify user by email
		
		delete_user_meta($user_id, 'usersultra_pending_pass');
		delete_user_meta($user_id, 'usersultra_pending_form');
	}

	
	
	
	/******************************************
	Get user ID only by query var
	******************************************/
	public function get_member_by_queryvar_from_id()
	{
		$arg = get_query_var('uu_username');
		if ( $arg ) 
		{
			$user = $this->get_member_by( $arg );
			return $user->ID;
		}
	}
	
	public function get_custom_user_meta ($meta, $user_id)
	{
		return get_user_meta( $user_id, $meta, true);
		
	}
	
	public function  get_profile_info ($user_id)	
	{
		
		$array = get_option('usersultra_profile_fields');

		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
		$html .= '';
		
	
		foreach($array as $key => $field) 
		{

			extract($field);
			
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($show_in_widget))
			    $show_in_widget = 1;
				
			
			
			/* Fieldset separator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0  && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
				$html .= '<div class="uultra-profile-seperator">'.$name.'</div>';
			}
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0  && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1)			
			{				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				{
					$html .= ' <span class="data-a">'.$name.':</span><span class="data-b">'.$this->get_custom_user_meta( $meta, $user_id).'</span> ';
				}
			
			}
				 	
				
			
		}
		
		$html .= '';
		return $html;
		
	}
	
	
	
	
}

$key = "userpanel";
$this->{$key} = new XooUserUser();