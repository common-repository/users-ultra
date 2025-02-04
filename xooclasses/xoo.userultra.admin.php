<?php
class XooUserAdmin extends XooUserUltraCommon 
{

	var $options;
	var $wp_all_pages = false;
	var $userultra_default_options;
	var $valid_c;
	
	var $notifications_email = array();

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userultra';
		
		$this->set_default_email_messages();				
		$this->update_default_option_ini();		
		$this->set_font_awesome();
		
		
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
	
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		add_action('admin_init', array(&$this, 'do_valid_checks'), 9);
		add_action( 'admin_init', array( &$this, 'uultra_vv_c_de_b' ), 9);
		
		add_action( 'wp_ajax_save_fields_settings', array( &$this, 'save_fields_settings' ));
		
		
		add_action( 'wp_ajax_add_new_custom_profile_field', array( &$this, 'add_new_custom_profile_field' ));
		add_action( 'wp_ajax_delete_profile_field', array( &$this, 'delete_profile_field' ));
		add_action( 'wp_ajax_sort_fileds_list', array( &$this, 'sort_fileds_list' ));
		
		//user to get all fields
		add_action( 'wp_ajax_uultra_reload_custom_fields_set', array( &$this, 'uultra_reload_custom_fields_set' ));
		
		//used to edit a field
		add_action( 'wp_ajax_uultra_reload_field_to_edit', array( &$this, 'uultra_reload_field_to_edit' ));	
		
		add_action( 'wp_ajax_uultra_vv_c_de_a', array( &$this, 'uultra_vv_c_de_a' ));
		add_action( 'wp_ajax_custom_fields_reset', array( &$this, 'custom_fields_reset' ));			
		add_action( 'wp_ajax_create_uploader_folder', array( &$this, 'create_uploader_folder' ));
		add_action( 'wp_ajax_uultra_duplicate_selected_form', array( &$this, 'uultra_duplicate_selected_form' ));
		
		add_action( 'wp_ajax_uultra_update_order_status', array( &$this, 'update_order_status' ));
		
			
		
	}
	
	function update_order_status()
	{
		global $xoouserultra;
		
		$order_id = sanitize_text_field($_POST["order_id"]);
		$order_status = sanitize_text_field($_POST["order_status"]);
		$transaction_id = sanitize_text_field($_POST["transaction_id"]);	
			
		$xoouserultra->order->update_order_status ($order_id,$order_status);
		
		/*Update Order With Payment Response*/				
		$xoouserultra->order->update_order_payment_response($order_id,$transaction_id);	
		
		$html =  __('DONE!','users-ultra');	
		
		echo $html;
		die();
	
	}	
	
	
	
	
	function admin_init() 
	{
		
		$this->tabs = array(
		    'main' => __('Dashboard','users-ultra'),
			'fields' => __('Fields','users-ultra'),
			'settings' => __('Settings','users-ultra'),			
			'membership' => __('Membership','users-ultra'),
			'users' => __('Users','users-ultra'),
			'orders' => __('Orders','users-ultra'),			
			'import' => __('Sync & Import','users-ultra'),
			'customizer' => __('Appearance & Customization', 'users-ultra'),			
			'mail' => __('Notifications','users-ultra'),			
			'permalinks' => __('Permalinks','users-ultra'),
			'gateway' => __('Gateways','users-ultra'),
			'help' => __('Doc','users-ultra'),
		);
		
		$this->default_tab = 'main';		
		
		$this->tabs_membership = array(
		    'main' => __('Membership Plans','users-ultra'),
			
		);
		$this->default_tab_membership = 'main';
		
		
	}
	
	public function update_default_option_ini () 
	{
		$this->options = get_option('userultra_options');		
		$this->userultra_set_default_options();
		
		if (!get_option('userultra_options')) 
		{
			
			update_option('userultra_options', $this->userultra_default_options );
		}
		
		if (!get_option('userultra_pro_active')) 
		{
			
			update_option('userultra_pro_active', true);
		}	
		
		
	}
	
		
	public function custom_fields_reset () 
	{
		
		if($_POST["p_confirm"]=="yes")
		{			
			
			//multi fields		
			$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
			
			if($custom_form!="")
			{
				$custom_form = 'usersultra_profile_fields_'.$custom_form;		
				$fields_set_to_update =$custom_form;
				
			}else{
				
				//$fields = get_option('usersultra_profile_fields');
				$fields_set_to_update ='usersultra_profile_fields';
			
			}
			
			update_option($fields_set_to_update, NULL);
		
		}
		
	}
	

	
	
	
	public function uultra_vv_c_de_a () 
	{		
		global $xoouserultra, $wpdb ;
		
		 	
		$p = sanitize_text_field($_POST["p_s_le"]);		
		
		//validate ulr
		
		$domain = $_SERVER['SERVER_NAME'];		
		$server_add = $_SERVER['SERVER_ADDR'];
		
		
		$url = uultraxoousers_pro_url."check_l_p.php";	
		
		
		$response = wp_remote_post(
            $url,
            array(
                'body' => array(
                    'd'   => $domain,
                    'server_ip'     => $server_add,
                    'sial_key' => $p,
					'action' => 'validate',
					
                )
            )
        );

		
		
		$response = json_decode($response["body"]);
		
		$message =$response->{'message'}; 
		$result =$response->{'result'}; 
		$expiration =$response->{'expiration'};
		$serial =$response->{'serial'};
        $version =$response->{'version'};
		
		//validate
		
		if ( is_multisite() ) // See if being activated on the entire network or one blog
		{		
			
	 
			// Get this so we can switch back to it later
			$current_blog = $wpdb->blogid;
			// For storing the list of activated blogs
			$activated = array();
			
			// Get all blogs in the network and activate plugin on each one
			
			$args = array(
				'network_id' => $wpdb->siteid,
				'public'     => null,
				'archived'   => null,
				'mature'     => null,
				'spam'       => null,
				'deleted'    => null,
				'limit'      => 100,
				'offset'     => 0,
			);
			$blog_ids = wp_get_sites( $args ); 
		   // print_r($blog_ids);
		
		
			foreach ($blog_ids as $key => $blog)
			{
				$blog_id = $blog["blog_id"];

				switch_to_blog($blog_id);				
				
				if($result =="OK")
				{
					update_option('uultra_c_key',$serial );
                    update_option('uultra_c_version',$version );                   
					update_option('uultra_c_expiration',$expiration );
					
					$html = '<div class="user-ultra-success">'. __("Congratulations!. Your copy has been validated", 'users-ultra').'</div>';
				
				}elseif($result =="EXP"){
					
					update_option('uultra_c_key',"" );
                    update_option('uultra_c_version',"");
					update_option('uultra_c_expiration',$expiration );
					
					$html = '<div class="user-ultra-error">'. __("We are sorry the serial key you have used has expired", 'users-ultra').'</div>';
				
				}elseif($result =="NO"){
					
					//update_option('uultra_c_key',"" );
                  //  update_option('uultra_c_version',"" );
					//update_option('uultra_c_expiration',$expiration );
					
					$html = '<div class="user-ultra-error">'. __("We are sorry your serial key is not valid", 'users-ultra').'</div>';
				
				}
				
				
			} //end for each
			
			//echo "current blog : " . $current_blog;
			// Switch back to the current blog
			switch_to_blog($current_blog); 
			
			
		}else{
			
			//this is not a multisite
			
			if($result =="OK")
			{
				update_option('uultra_c_key',$serial );
                update_option('uultra_c_version',$version );
				update_option('uultra_c_expiration',$expiration );
				
				$html = '<div class="user-ultra-success">'. __("Congratulations!. Your copy has been validated", 'users-ultra').'</div>';
			
			}elseif($result =="EXP"){
				
				update_option('uultra_c_key',"" );
                update_option('uultra_c_version',"" );
				update_option('uultra_c_expiration',$expiration );
				
				$html = '<div class="user-ultra-error">'. __("We are sorry the serial key you have used has expired", 'users-ultra').'</div>';
			
			}elseif($result =="NO"){
				
				//update_option('uultra_c_key',"" );
               // update_option('uultra_c_version',"" );
				//update_option('uultra_c_expiration',$expiration );
				
				$html = '<div class="user-ultra-error">'. __("We are sorry your serial key is not valid", 'users-ultra').'</div>';
			
			}
			
			
			
		
		}
		
		//
		echo "Domain: " .$domain;
		echo $html;		 
		
		
		die();
		
	}
    
    
	function get_plugin_pro_version()
	{
        $version = "free";
        
        $serial=  get_option('uultra_c_key' );
        $uu_version = get_option('uultra_c_version');
        
        if($uu_version!=""){
            
             $version = $uu_version;          
            
        }      
        
        
        return $version;        
        
    }
    
    function get_current_verson()
	{
        $version = $this->get_plugin_pro_version();
         // 1-free 2-personal 3-Professional 5-Plus 7-Ultimate
        
            
        if($version=="free"|| $version=="1"){
            
            $version = '<span class="uultra-licence-version">'.__("FREE VERSION", 'users-ultra').'</span>';          
            
        }elseif($version=="2"){
            
            $version = '<span class="uultra-licence-version-per">'.__("PERSONAL VERSION", 'users-ultra').'</span>';
        
        }elseif($version=="3"){
            
            $version = '<span class="uultra-licence-version">'.__("PROFESSIONAL VERSION", 'users-ultra').'</span>';
    
         }elseif($version=="5"){
            
           $version = '<span class="uultra-licence-version">'.__("PLUS VERSION", 'users-ultra').'</span>';
        
         }elseif($version=="7"){
            
            $version = '<span class="uultra-licence-version">'.__("ULTIMATE VERSION", 'users-ultra').'</span>';
            
        }     
        
        
        return $version;        
        
    }
    
    function check_pro_version_message()
	{
        $html = "";
        
        $serial=  get_option('uultra_c_key' );
        $uu_version = get_option('uultra_c_version');
        
        if($uu_version=="" || $serial ==""){          
           
            
            $html .= ' <div ><div class="user-ultra-warning">'.__("This feature is available only on Paid Versions. Upgrade your plugin by <a href='https://usersultra.com/index.php#pricing_comparision' target='_blank'>clicking here</a>", 'users-ultra').'</div></div>';
            
        }      
        
        
        return $html;
        
        
    }
    
    function check_pro_version_features_message()
	{
        $html = "";
        
        $serial=  get_option('uultra_c_key' );
        $uu_version = get_option('uultra_c_version');
        
        if($uu_version=="" || $serial ==""){          
           
            
            $html .= ' <div ><div class="user-ultra-warning">'.__("You are using the Free Version. Some features may be unavailable. Please compare versions by <a href='https://usersultra.com/compare-packages.php' target='_blank'>clicking here</a>", 'users-ultra').'</div></div>';
            
        }      
        
        
        return $html;
        
        
    }
    
    
    
    function get_plugin_pro_allowed_versions()
	{
        // 1-free 2-personal 3-Professional 5-Plus 7-Ultimate
        
        ///modules///////
        
        //1-PayPal
        //2-Profile Customizer
        //3-Photo Gallery
        //4-Users Directory
        //5-Users Grids
        //6-Front End Publisher
        //7-Registration Fields 
        //8-Email Templates
        //9-Online Users
        //10-Private Messages
        //11-Friends
        //12-Followers
        //13-Users Management            
        //14-User Avatar
        //15-User Stars Rating
        //16-Medallions & Fulfillments
        //17-Protect Posts
        
        $plans = array();
        
        $plans[2] = array(1);
        $plans[3] = array();
        $plans[5] = array();
        $plans[7] = array();
        
    }
	
	
	
	
	function get_pending_verify_requests_count()
	{
		$count = 0;
		
		// verification status
		$pending = get_option('userultra_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return '<span class="upadmin-bubble-new">'.$count.'</span>';
		}
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		
		// verification status
		$pending = get_option('userultra_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return $count;
		}
	}
	
	
	public function uultra_vv_c_de_b () 
	{		

		
		
		
	}
	
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		//$icon = userultra_url . "admin/images/$slug-32.png";
		//echo '<style type="text/css">';
			///if (in_array( $screen->id, array( $slug ) ) || strstr($screen->id, $slug) ) {
				//print "#icon-$slug {background: url('{$icon}') no-repeat left;}";
		//	}
		//echo '</style>';
	}

	function add_styles()
	{
	
		wp_register_style('userultra_admin', xoousers_url.'admin/css/userlutra.admin.css');
		wp_enqueue_style('userultra_admin');			
			
		wp_register_script('userultra_chosen', xoousers_url . 'admin/scripts/admin-chosen.js');
		wp_enqueue_script('userultra_chosen');
		
		//color picker		
		 wp_enqueue_style( 'wp-color-picker' );		 
		 wp_register_script( 'userultra_color_picker', xoousers_url.'admin/scripts/color-picker-js.js', array( 
			'wp-color-picker'
		) );
		wp_enqueue_script( 'userultra_color_picker' );
		
		
		wp_register_script( 'userultra_admin', xoousers_url.'admin/scripts/admin.js', array( 
			'jquery','jquery-ui-core','jquery-ui-draggable','jquery-ui-droppable',	'jquery-ui-sortable', 'jquery-ui-tabs'	), null );
		wp_enqueue_script( 'userultra_admin' );
		
		
		/* Font Awesome */
		wp_register_style( 'xoouserultra_font_awesome', xoousers_url.'css/css/font-awesome.min.css');
		wp_enqueue_style('xoouserultra_font_awesome');
		
		/*google graph*/		
		wp_register_script('userultra_jsgooglapli', 'https://www.google.com/jsapi');
		wp_enqueue_script('userultra_jsgooglapli');
		
		//
		
  		// Add the styles first, in the <head> (last parameter false, true = bottom of page!)
		wp_enqueue_style('qtip', xoousers_url.'js/qtip/jquery.qtip.min.css' , null, false, false);
		
		// Using imagesLoaded? Do this.
		wp_enqueue_script('imagesloaded',  xoousers_url.'js/qtip/imagesloaded.pkgd.min.js' , null, false, true);
		wp_enqueue_script('qtip',  xoousers_url.'js/qtip/jquery.qtip.min.js', array('jquery', 'imagesloaded'), false, true);		
				
		
	}
	
	function add_menu() 
	{
		global $xoouserultra ;
	
		$pending_count = $xoouserultra->userpanel->get_pending_activation_count();
		$pending_title = esc_attr( sprintf(__( '%d new manual activation requests','users-ultra'), $pending_count ) );
		if ($pending_count > 0)
		{
			$menu_label = sprintf( __( 'Users Ultra %s','xoousers' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			
		} else {
			
			$menu_label = __('Users Ultra','users-ultra');
		}
		
		add_menu_page( __('Users Ultra','users-ultra'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), xoousers_url .'admin/images/small_logo_16x16.png', '159.140');
		
		//
        
        add_submenu_page( 'userultra', __('Licensing','users-ultra'), __('Licensing','users-ultra'), 'manage_options', 'userultra&tab=licence', array(&$this, 'admin_page') );
        
       //	if(isset($bupcomplement))
		//{
		
		
		
		//}
        
		
		do_action('userultra_admin_menu_hook');
		
			
	}
	
	function admin_tabs_membership( $current = null ) 
	{
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	
	
	function do_action(){
		global $userultra;
				
		
	}
	
	public function do_valid_checks()
	{
		
		global $xoouserultra ;
		
		$va = get_option('uultra_c_key');
		if($va=="")
		{
			//
			$this->valid_c = "no";
		
		}
	
	
	}
	
	
	function reset() {
		update_option('userultra', $this->userultra_default_options() );
		$this->options = array_merge( $this->options, $his->userultra_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','users-ultra').'</strong></p></div>';
	}
	
	
	function woo_sync() {
		userultra_admin_woo_sync();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been added.','users-ultra').'</strong></p></div>';
	}
	
	function woo_sync_del(){
		userultra_admin_woo_sync_erase();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been removed.','users-ultra').'</strong></p></div>';
	}
	
	
	
	/* set a global option */
	function userultra_set_option($option, $newvalue)
	{
		
        $newvalue = sanitize_text_field($newvalue);
        $settings = get_option('userultra_options');		
		$settings[$option] = $newvalue;
		update_option('userultra_options', $settings);
	}
	
	/* default options */
	function userultra_set_default_options()
	{
	
		$this->userultra_default_options = array(
						'html_user_login_message' => __('Please log in to view / edit your profile.','users-ultra'),
						'html_login_to_view' => __('Please log in to view user profiles.','users-ultra'),
						'html_private_content' => __('This content is for members only. You must log in to view this content.','users-ultra'),
						'clickable_profile' => 1,
						'set_password' => 1,
						'guests_can_view' => 1,
						'users_can_view' => 1,
						'style' => 'default',
						'profile_page_id' => '0',
						'hide_admin_bar' => '1',
						'uultra_loggedin_activated' => '0',
						'registration_rules' => '1',
						
						'media_avatar_width' => '190',
						'media_avatar_height' => '190',
						
						
						
						'media_photo_mini_width' => '80',
						'media_photo_mini_height' => '80',
						'media_photo_thumb_width' => '350',
						'media_photo_thumb_height' => '250',
						'media_photo_large_width' => '700',
						'media_photo_large_height' => '800',
						'media_uploading_folder' => 'wp-content/usersultramedia',
						
						'uultra_groups_post_title' => __('No rights!','users-ultra'),
						'uultra_groups_post_content' => __('Sorry you have no rights to view this post!','users-ultra'),
						'uultra_groups_post_comment_content' => __('Sorry no rights to view comments!','users-ultra'), 
						
						'uultra_groups_page_title' => __('No rights!','users-ultra'),
						'uultra_groups_page_content' => __('Sorry you have no rights to view this page!','users-ultra'),
						'uultra_groups_page_comment_content' => __('Sorry no rights to view comments!','users-ultra'),
						
						'uultra_loggedin_post_title' => __('No rights!','users-ultra'),
						'uultra_loggedin_post_content' => __('Sorry you have to be logged in to view this post!','users-ultra'),
						'uultra_loggedin_post_comment_content' => __('Sorry no rights to view comments!','users-ultra'), 						
						'uultra_loggedin_page_title' => __('No rights!','users-ultra'),
						'uultra_loggedin_page_content' => __('Sorry you have to be logged in to view this page!','users-ultra'),
						'uultra_loggedin_page_comment_content' => __('Sorry no rights to view comments!','users-ultra'), 
						
						'uultra_front_publisher_default_amount' => '9999',
						'uultra_front_publisher_default_status' => 'pending',
						'uultra_front_publisher_allows_category' =>  'yes',
						'uultra_front_publisher_default_category' => '',	
						'uultra_front_publisher_post_type_label_singular' => __('Post','users-ultra'),
						'uultra_front_publisher_post_type_label_plural' => __('Posts','users-ultra'),								
						
						'usersultra_my_account_slug' => 'myaccount',						
						'usersultra_slug' => 'profile',
						'usersultra_login_slug' => 'login',
						'usersultra_registration_slug' => 'registration',
						'usersultra_directory_slug' => 'directory',	
						'mailchimp_text' => __('Subscribe to receive our periodic email updates','users-ultra'),	
						'uultra_password_lenght' => '7',						
														
										
						'login_page_id' => '0',
						'registration_page_id' => '0',
						'redirect_backend_profile' => '0',
						'redirect_backend_registration' => '0',
						'redirect_registration_when_social' => '0',
						'redirect_backend_login' => '0',
						'paid_membership_currency' => 'USD',
						'paid_membership_symbol' => '$',						
						'uurofile_setting_display_photos' => 'public',
						
						'html_private_content' => __('User registration is currently not allowed.','users-ultra'),
						'messaging_welcome_email_client' => $this->get_email_template('new_account'),						
						'messaging_welcome_email_client_admin' => $this->get_email_template('new_account_noti_admin'),
						
						'messaging_paid_email_admin' => $this->get_email_template('new_account_noti_admin_paid'),
						
						'account_upgrade_email_client' => $this->get_email_template('account_upgrading_user'),						
						'account_upgrade_email_admin' => $this->get_email_template('account_upgrading_admin'),
						
						'messaging_welcome_email_with_activation_client' => $this->get_email_template('new_account_activation_link'),
						'messaging_welcome_email_with_activation_admin' => $this->get_email_template('new_account_activation_link_admin'),
						
						'messaging_admin_moderation_user' => $this->get_email_template('new_account_admin_moderation'),
						'messaging_admin_moderation_admin' => $this->get_email_template('new_account_admin_moderation_admin'),
						
						
						'messaging_re_send_activation_link' => $this->get_email_template('messaging_re_send_activation_link'),
						
						
						'account_verified_sucess_message_body' => $this->get_email_template('account_verified_sucess_message_body'),
						
						'message_friend_request' => $this->get_email_template('message_friend_request'),
						'follower_request' => $this->get_email_template('follower_request'),
						
						'messaging_welcome_email_admin' => __($this->get_email_template('new_account_admin'),'users-ultra'),
						'messaging_user_pm' => __($this->get_email_template('private_message_noti'),'users-ultra'),
						'messaging_user_pm_from_admin' => $this->get_email_template('private_message_noti_from_adm'),
						'reset_lik_message_body' => __($this->get_email_template('reset_lik_message_body'),'users-ultra'),
						'password_reset_confirmation' => $this->get_email_template('password_reset_confirmation'),
						
						'admin_account_active_message_body' => __($this->get_email_template('admin_account_active_message_body'),'users-ultra'),
						'admin_account_deny_message_body' => $this->get_email_template('admin_account_deny_message_body'),
						
						'messaging_send_from_name' => __('Users Ultra Plugin','users-ultra'),
						
						
				);
		
	}
	
	public function set_default_email_messages()
	{
		$line_break = "\r\n";
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Thanks for registering. Your account is now active.","xoousers") .  $line_break.$line_break;
		$email_body .= __("To login please visit the following URL:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{userl_ultra_login_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Your account password: (same used on registration)','users-ultra') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your password has been reset.","xoousers") .  $line_break.$line_break;
		$email_body .= __("To login please visit the following URL:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{userl_ultra_login_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','users-ultra') . $line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','users-ultra') . $line_break;
		//$email_body .= __('Your account password: (same used when registered)','users-ultra') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['password_reset_confirmation'] = $email_body;
		
		
				
		//notify admin new registration		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("A new user has been registered.","xoousers") .  $line_break.$line_break;
		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','users-ultra'). $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_noti_admin'] = $email_body;
		
		//notify admin new paid registration		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("A new paid user has been registered.","xoousers") .  $line_break.$line_break;
		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Package: {{userultra_user_package}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','users-ultra'). $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_noti_admin_paid'] = $email_body;
		
		//upgrading client
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Thanks for upgrading your account.","xoousers") .  $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['account_upgrading_user'] = $email_body;
		
		//upgrading admin
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("One if your users has just upgraded his/her account.","xoousers") .  $line_break.$line_break;
		$email_body .= __('Account e-mail: {{userultra_user_email}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['account_upgrading_admin'] = $email_body;
		
			
		
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Thanks for registering. Your account needs activation.","xoousers") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{user_ultra_activation_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','users-ultra') . $line_break.$line_break;
		$email_body .= __('Your account password: {{userultra_pass}}','users-ultra') . $line_break.$line_break;
		
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_activation_link'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("We are re-sending you the activation link.","xoousers") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{user_ultra_activation_url}}" . $line_break.$line_break;
	
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['messaging_re_send_activation_link'] = $email_body;
		
		//admin
		$email_body = __('Hi Admin,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("An account needs activation.","xoousers") .  $line_break.$line_break;		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','users-ultra') . $line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','users-ultra') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_activation_link_admin'] = $email_body;	
		
		//admin manually approved --06-20-2014
		$email_body = __('Hi Admin,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("An account needs approval.","xoousers") .  $line_break.$line_break;		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','users-ultra') . $line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','users-ultra') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','users-ultra') . $line_break.$line_break;
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_admin_moderation_admin'] = $email_body;
		
		//client manually approved --06-20-2014
		$email_body = __('Hi {{userultra_user_name}},' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account will be reviewed by the admin soon.","xoousers") .  $line_break.$line_break;		
		$email_body .= __('Best Regards!','users-ultra');
	    $this->notifications_email['new_account_admin_moderation'] = $email_body;
		
		
		$email_body = __('Hi,',"xoousers") . $line_break.$line_break;
		$email_body .= __("{{userultra_user_name}} has just created a new account at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;
		$email_body .= __("You can check his profile via the following link:","xoousers") .$line_break;
		$email_body .= "{{userl_ultra_login_url}}" .$line_break.$line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','users-ultra'). $line_break.$line_break;		
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['new_account_admin'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("{{userultra_user_name}} has just sent you a new private message at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;
		
		$email_body .= __("Subject: {{userultra_pm_subject}}","xoousers") . $line_break;
		$email_body .=__("Message: {{userultra_pm_message}}","xoousers") . $line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','users-ultra'). $line_break.$line_break;		
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['private_message_noti'] = $email_body;
		
		//private message from admin
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("{{userultra_user_name}} has just sent you a new private message at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;
		
		$email_body .= __("Subject: {{userultra_pm_subject}}","xoousers") . $line_break;
		$email_body .=__("Message: {{userultra_pm_message}}","xoousers") . $line_break;
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['private_message_noti_from_adm'] = $email_body;
		
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("You have a new friend request at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;				
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['message_friend_request'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("You have a new follower at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;				
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['follower_request'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Please use the following link to reset your password.","xoousers") . $line_break.$line_break;			
		$email_body .= "{{userultra_reset_link}}".$line_break.$line_break;
		$email_body .= __('If you did not request a new password delete this email.','users-ultra'). $line_break.$line_break;		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['reset_lik_message_body'] = $email_body;	
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account has been activated.","xoousers") . $line_break.$line_break;	
		$email_body .= __('Please use the link below to get in your account.','users-ultra'). $line_break.$line_break;	
		
		$email_body .=   "{{userl_ultra_login_url}}".$line_break.$line_break;	
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['admin_account_active_message_body'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account has been verified.","xoousers") . $line_break.$line_break;	
		$email_body .= __('Please use the link below to get in your account.','users-ultra'). $line_break.$line_break;	
		$email_body .=   "{{userl_ultra_login_url}}".$line_break.$line_break;	
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['account_verified_sucess_message_body'] = $email_body;	
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("We're sorry but your account has not been approved.","xoousers") . $line_break.$line_break;	
		$email_body .= __('This is an automated notification. No further action is needed.','users-ultra'). $line_break.$line_break;	
		$email_body .= __('Best Regards!','users-ultra') . $line_break.$line_break;		
	    $this->notifications_email['admin_account_deny_message_body'] = $email_body;		
		
	
	}
	
	public function get_email_template($key)
	{
		return $this->notifications_email[$key];
	
	}
	
	public function set_font_awesome()
	{
		        /* Store icons in array */
        $this->fontawesome = array(
                'cloud-download','cloud-upload','lightbulb','exchange','bell-alt','file-alt','beer','coffee','food','fighter-jet',
                'user-md','stethoscope','suitcase','building','hospital','ambulance','medkit','h-sign','plus-sign-alt','spinner',
                'angle-left','angle-right','angle-up','angle-down','double-angle-left','double-angle-right','double-angle-up','double-angle-down','circle-blank','circle',
                'desktop','laptop','tablet','mobile-phone','quote-left','quote-right','reply','github-alt','folder-close-alt','folder-open-alt',
                'adjust','asterisk','ban-circle','bar-chart','barcode','beaker','beer','bell','bolt','book','bookmark','bookmark-empty','briefcase','bullhorn',
                'calendar','camera','camera-retro','certificate','check','check-empty','cloud','cog','cogs','comment','comment-alt','comments','comments-alt',
                'credit-card','dashboard','download','download-alt','edit','envelope','envelope-alt','exclamation-sign','external-link','eye-close','eye-open',
                'facetime-video','film','filter','fire','flag','folder-close','folder-open','gift','glass','globe','group','hdd','headphones','heart','heart-empty',
                'home','inbox','info-sign','key','leaf','legal','lemon','lock','unlock','magic','magnet','map-marker','minus','minus-sign','money','move','music',
                'off','ok','ok-circle','ok-sign','pencil','picture','plane','plus','plus-sign','print','pushpin','qrcode','question-sign','random','refresh','remove',
                'remove-circle','remove-sign','reorder','resize-horizontal','resize-vertical','retweet','road','rss','screenshot','search','share','share-alt',
                'shopping-cart','signal','signin','signout','sitemap','sort','sort-down','sort-up','spinner','star','star-empty','star-half','tag','tags','tasks',
                'thumbs-down','thumbs-up','time','tint','trash','trophy','truck','umbrella','upload','upload-alt','user','volume-off','volume-down','volume-up',
                'warning-sign','wrench','zoom-in','zoom-out','file','cut','copy','paste','save','undo','repeat','text-height','text-width','align-left','align-right',
                'align-center','align-justify','indent-left','indent-right','font','bold','italic','strikethrough','underline','link','paper-clip','columns',
                'table','th-large','th','th-list','list','list-ol','list-ul','list-alt','arrow-down','arrow-left','arrow-right','arrow-up','caret-down',
                'caret-left','caret-right','caret-up','chevron-down','chevron-left','chevron-right','chevron-up','circle-arrow-down','circle-arrow-left',
                'circle-arrow-right','circle-arrow-up','hand-down','hand-left','hand-right','hand-up','play-circle','play','pause','stop','step-backward',
                'fast-backward','backward','forward','step-forward','fast-forward','eject','fullscreen','resize-full','resize-small','phone','phone-sign',
                'facebook','facebook-sign','twitter','twitter-sign','github','github-sign','linkedin','linkedin-sign','pinterest','pinterest-sign',
                'google-plus','google-plus-sign','sign-blank'
        );
        asort($this->fontawesome);
		
	
	
	}
	
		
	
	/*This Function Change the Profile Fields Order when drag/drop */	
	public function sort_fileds_list() 
	{
		global $wpdb;
	
		$order = explode(',', sanitize_text_field($_POST['order']));
		$counter = 0;
		$new_pos = 10;
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('usersultra_profile_fields');
			$fields_set_to_update ='usersultra_profile_fields';
		
		}
		
		$new_fields = array();
		
		$fields_temp = $fields;
		ksort($fields);
		
		foreach ($fields as $field) 
		{
			
			$fields_temp[$order[$counter]]["position"] = $new_pos;			
			$new_fields[$new_pos] = $fields_temp[$order[$counter]];				
			$counter++;
			$new_pos=$new_pos+10;
		}
		
		ksort($new_fields);		
		
		
		update_option($fields_set_to_update, $new_fields);		
		die(1);
		
    }
	/*  delete profile field */
    public function delete_profile_field() 
	{						
		
		if($_POST['_item']!= "")
		{
			//$fields = get_option('usersultra_profile_fields');
			
			//multi fields		
			$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
			
			if($custom_form!="")
			{
				$custom_form = 'usersultra_profile_fields_'.$custom_form;		
				$fields = get_option($custom_form);			
				$fields_set_to_update =$custom_form;
				
			}else{
				
				$fields = get_option('usersultra_profile_fields');
				$fields_set_to_update ='usersultra_profile_fields';
			
			}
			
			$pos = sanitize_text_field($_POST['_item']);
			
			unset($fields[$pos]);
			
			ksort($fields);
			print_r($fields);
			update_option($fields_set_to_update, $fields);
			
		
		}
	
	}
	
	
	 /* create new custom profile field */
    public function add_new_custom_profile_field() 
	{				
		
		
		if($_POST['_meta']!= "")
		{
			$meta = sanitize_text_field($_POST['_meta']);
		
		}else{
			
			$meta =sanitize_text_field( $_POST['_meta_custom']);
		}
		
		//if custom fields
		
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('usersultra_profile_fields');
			$fields_set_to_update ='usersultra_profile_fields';
		
		}
		
		$min = min(array_keys($fields)); 
		
		$pos = $min-1;
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => filter_var($_POST['_icon']),
				'type' => filter_var($_POST['_type']),
				'field' => filter_var($_POST['_field']),
				'meta' => filter_var($meta),
				'name' => filter_var($_POST['_name']),
				'ccap' => filter_var($_POST['_ccap']),
				'tooltip' => filter_var($_POST['_tooltip']),
				'help_text' => filter_var($_POST['_help_text']),
				'social' =>  filter_var($_POST['_social']),
				'is_a_link' =>  filter_var($_POST['_is_a_link']),				
				'can_edit' => filter_var($_POST['_can_edit']),
				'allow_html' => filter_var($_POST['_allow_html']),
				'can_hide' => filter_var($_POST['_can_hide']),				
				'private' => filter_var($_POST['_private']),
				'required' => filter_var($_POST['_required']),
				'show_in_register' => filter_var($_POST['_show_in_register']),
				'show_in_widget' => filter_var($_POST['_show_in_widget']),
				'predefined_options' => filter_var($_POST['_predefined_options']),				
				'choices' => filter_var($_POST['_choices']),												
				'deleted' => 0,
				'show_to_user_role' => $_POST['_show_to_user_role'],
                'edit_by_user_role' => $_POST['_edit_by_user_role']

			);
			
			// Save user roles which has permission for view and edit the field
            if (isset($_POST['_show_to_user_role_list']) )
			{
                    $fields[$pos]['show_to_user_role_list'] = sanitize_text_field($_POST['_show_to_user_role_list']);					
            }
           
		   if (isset($_POST['_edit_by_user_role_list']) ) 
		   {
                    $fields[$pos]['edit_by_user_role_list'] = sanitize_text_field($_POST['_edit_by_user_role_list']);
           }
				
			
			ksort($fields);
			print_r($fields);
			
		   update_option($fields_set_to_update, $fields);         


    }
	


    // save form
    public function save_fields_settings() 
	{		
		
		$pos = filter_var($_POST['pos']);
		
		if($_POST['_meta']!= "")
		{
			$meta = sanitize_text_field($_POST['_meta']);
		
		}else{
			
			$meta = sanitize_text_field($_POST['_meta_custom']);
		}
		
		//if custom fields
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('usersultra_profile_fields');
			$fields_set_to_update ='usersultra_profile_fields';
		
		}
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => $_POST['_icon'],
				'type' => filter_var($_POST['_type']),
				'field' => filter_var($_POST['_field']),
				'meta' => filter_var($meta),
				'name' => filter_var($_POST['_name']),
				'ccap' => filter_var($_POST['_ccap']),
				'tooltip' => filter_var($_POST['_tooltip']),
				'help_text' => filter_var($_POST['_help_text']),
				'social' =>  filter_var($_POST['_social']),
				'is_a_link' =>  filter_var($_POST['_is_a_link']),
				'can_edit' => filter_var($_POST['_can_edit']),
				'allow_html' => filter_var($_POST['_allow_html']),
				'can_hide' => filter_var($_POST['_can_hide']),				
				'private' => filter_var($_POST['_private']),
				'required' => filter_var($_POST['_required']),
				'show_in_register' => filter_var($_POST['_show_in_register']),
				'show_in_widget' => filter_var($_POST['_show_in_widget']),
				'predefined_options' => filter_var($_POST['_predefined_options']),				
				'choices' => filter_var($_POST['_choices']),												
				'deleted' => 0,
				'show_to_user_role' => $_POST['_show_to_user_role'],
                'edit_by_user_role' => $_POST['_edit_by_user_role']
			);
			
			
			// Save user roles which has permission for view and edit the field
            if (isset($_POST['_show_to_user_role_list']) )
			{
                    $fields[$pos]['show_to_user_role_list'] = sanitize_text_field($_POST['_show_to_user_role_list']);					
            }
           
		   if (isset($_POST['_edit_by_user_role_list']) ) 
		   {
                    $fields[$pos]['edit_by_user_role_list'] = sanitize_text_field($_POST['_edit_by_user_role_list']);
           }
			
			
			
			print_r($fields);
			
		    update_option($fields_set_to_update , $fields);
		
         


    }
	
	function uultra_duplicate_selected_form()	
	{
		global $xoouserultra, $uultra_form ;
		
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
		$uultra_custom_form_name = sanitize_text_field($_POST["uultra_custom_form_name"]);
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$fields;
			
		}else{
			
			$fields = get_option('usersultra_profile_fields');
			$fields_set_to_update =$fields;
		
		}
		
		if($uultra_custom_form_name!='')		
		//create form		
		{
			$slug = $uultra_form->genRandomString();
			$forms = get_option('usersultra_custom_forms_collection');
			
			$new_form[$slug] =  array('name' =>$uultra_custom_form_name, 'role' =>'');
			
			if(is_array($forms))
			{
				$new_forms = array_merge($forms, $new_form);	
			}else{
				
				$new_forms =  $new_form;				
			
			}
			
			ksort($new_forms);			
			update_option('usersultra_custom_forms_collection',$new_forms);
			
			//fields set			
			update_option('usersultra_profile_fields_'.$slug,$fields_set_to_update);		
			
			
			
			echo __("Form duplicated. Please wait ... ");
			
		}else{
			
			echo __("ERROR! Input a name. ");
		
		
		}
		die();
		
	
	}
	
	/*This load a custom field to be edited Implemented on 08-08-2014*/
	function uultra_reload_field_to_edit()	
	{
		global $xoouserultra;
		
		//get field
		$pos = $_POST["pos"];		
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('usersultra_profile_fields');
			$fields_set_to_update ='usersultra_profile_fields';
		
		}
		
		$array = $fields[$pos];
		
		
		extract($array); $i++;

		if(!isset($required))
		       $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';				
				
			if ($type == 'seperator' || $type == 'separator') {
			   
				$class = "separator";
				$class_title = "";
			} else {
			  
				$class = "profile-field";
				$class_title = "profile-field";
			}
		
		
		?>
		
		

				<p>
					<label for="uultra_<?php echo $pos; ?>_position"><?php _e('Position','users-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_position"
						type="text" id="uultra_<?php echo $pos; ?>_position"
						value="<?php echo $pos; ?>" class="small-text" /> <i
						class="uultra_icon-question-sign uultra-tooltip2"
						title="<?php _e('Please use a unique position. Position lets you place the new field in the place you want exactly in Profile view.','users-ultra'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_type"><?php _e('Field Type','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_type"
						id="uultra_<?php echo $pos; ?>_type">
						<option value="usermeta" <?php selected('usermeta', $type); ?>>
							<?php _e('Profile Field','users-ultra'); ?>
						</option>
						<option value="separator" <?php selected('separator', $type); ?>>
							<?php _e('Separator','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can create a separator or a usermeta (profile field)','users-ultra'); ?>"></i>
				</p> 
				
				<?php if ($type != 'separator') { ?>

				<p class="uultra-inputtype">
					<label for="uultra_<?php echo $pos; ?>_field"><?php _e('Field Input','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_field"
						id="uultra_<?php echo $pos; ?>_field">
						<?php
						global $xoouserultra;
						 foreach($xoouserultra->allowed_inputs as $input=>$label) { ?>
						<option value="<?php echo $input; ?>"
						<?php selected($input, $field); ?>>
							<?php echo $label; ?>
						</option>
						<?php } ?>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','users-ultra'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_meta"><?php _e('Choose Meta Field','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_meta"
						id="uultra_<?php echo $pos; ?>_meta">
						<option value="">
							<?php _e('Choose a user field','users-ultra'); ?>
						</option>
						<?php
						$current_user = wp_get_current_user();
						if( $all_meta_for_user = get_user_meta( $current_user->ID ) ) {
						    ksort($all_meta_for_user);
						    foreach($all_meta_for_user as $user_meta => $user_meta_array) {

						        ?>
						<option value="<?php echo $user_meta; ?>"
						<?php selected($user_meta, $meta); ?>>
							<?php echo $user_meta; ?>
						</option>
						<?php
						    }
						}
						?>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Choose from a predefined/available list of meta fields (usermeta) or skip this to define a new custom meta key for this field below.','users-ultra'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_meta_custom"><?php _e('Custom Meta Field','users-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>C"
						type="text" id="uultra_<?php echo $pos; ?>_meta_custom"
						value="<?php if (!isset($all_meta_for_user[$meta])) echo $meta; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','users-ultra'); ?>"></i>
				</p> <?php } ?>

				
                
                
                <p>
					<label for="uultra_<?php echo $pos; ?>_name"><?php _e('Label / Name','users-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_name" type="text"
						id="uultra_<?php echo $pos; ?>_name" value="<?php echo $name; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','users-ultra'); ?>"></i>
				</p>
                
                <p>
					<label for="uultra_<?php echo $pos; ?>_ccap"><?php _e('Custom Capabilities','users-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_ccap" type="text"
						id="uultra_<?php echo $pos; ?>_ccap" value="<?php echo $ccap; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You could type anything in this space. Any page could have access to those having that tag. Francetour,Spaintour. Please input comma separated tags','users-ultra'); ?>"></i>
				</p>

			<?php if ($type != 'separator' ) { ?>

				
				<p>
					<label for="uultra_<?php echo $pos; ?>_tooltip"><?php _e('Tooltip Text','users-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_tooltip" type="text"
						id="uultra_<?php echo $pos; ?>_tooltip"
						value="<?php echo $tooltip; ?>" /> <i
						class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('A tooltip text can be useful for social buttons on profile header.','users-ultra'); ?>"></i>
				</p> 
                
               <p>
               
               <label for="uultra_<?php echo $pos; ?>_help_text"><?php _e('Help Text','users-ultra'); ?>
                </label><br />
                    <textarea class="uultra-help-text" id="uultra_<?php echo $pos; ?>_help_text" name="uultra_<?php echo $pos; ?>_help_text" title="<?php _e('A help text can be useful for provide information about the field.','users-ultra'); ?>" ><?php echo $help_text; ?></textarea>
                    <i class="uultra-icon-question-sign uultra-tooltip2"
                                title="<?php _e('Show this help text under the profile field.','users-ultra'); ?>"></i>
                              
               </p> 
				
				
				<?php if ($field != 'password') { ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_social"><?php _e('This field is social','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_social"
						id="uultra_<?php echo $pos; ?>_social">
						<option value="0" <?php selected(0, $social); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $social); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','users-ultra'); ?>"></i>
				</p> <?php } ?> 
                
                
                <?php 
				if(!isset($is_a_link))
				    $is_a_link = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_is_a_link"><?php _e('Is a link?','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_is_a_link"
						id="uultra_<?php echo $pos; ?>_is_a_link">
						<option value="1" <?php selected(1, $is_a_link); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
						<option value="0" <?php selected(0, $is_a_link); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('This is a link type field','users-ultra'); ?>"></i>
				</p> 
                
				
				<?php 
				if(!isset($can_edit))
				    $can_edit = '1';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_can_edit"><?php _e('User can edit','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_can_edit"
						id="uultra_<?php echo $pos; ?>_can_edit">
						<option value="1" <?php selected(1, $can_edit); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
						<option value="0" <?php selected(0, $can_edit); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Users can edit this profile field or not.','users-ultra'); ?>"></i>
				</p> 
				
				<?php if (!isset($array['allow_html'])) { 
				    $allow_html = 0;
				} ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_allow_html"><?php _e('Allow HTML','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_allow_html"
						id="uultra_<?php echo $pos; ?>_allow_html">
						<option value="0" <?php selected(0, $allow_html); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $allow_html); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('If yes, users will be able to write HTML code in this field.','users-ultra'); ?>"></i>
				</p> <?php if ($private != 1) { 
				     
				    if(!isset($can_hide))
				        $can_hide = '0';
				    ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_can_hide"><?php _e('User can hide','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_can_hide"
						id="uultra_<?php echo $pos; ?>_can_hide">
						<option value="1" <?php selected(1, $can_hide); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
						<option value="0" <?php selected(0, $can_hide); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicly visible or not. Private fields are not affected by this option.','users-ultra'); ?>"></i>
				</p> <?php } ?> <?php 
				if(!isset($private))
				    $private = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_private"><?php _e('This field is private','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_private"
						id="uultra_<?php echo $pos; ?>_private">
						<option value="0" <?php selected(0, $private); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $private); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Make this field Private. Only admins can see private fields.','users-ultra'); ?>"></i>
				</p> <?php 
				if(!isset($required))
				    $required = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_required"><?php _e('This field is Required','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_required"
						id="uultra_<?php echo $pos; ?>_required">
						<option value="0" <?php selected(0, $required); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $required); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','users-ultra'); ?>"></i>
				</p> <?php } ?> <?php

				/* Show Registration field only when below condition fullfill
				1) Field is not private
				2) meta is not for email field
				3) field is not fileupload */
				if(!isset($private))
				    $private = 0;

				if(!isset($meta))
				    $meta = '';

				if(!isset($field))
				    $field = '';


				//if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				{
				    if(!isset($show_in_register))
				        $show_in_register= 0;
						
					 if(!isset($show_in_widget))
				        $show_in_widget= 0;
				    ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_show_in_register"><?php _e('Show on Registration Form','users-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_show_in_register"
						id="uultra_<?php echo $pos; ?>_show_in_register">
						<option value="0" <?php selected(0, $show_in_register); ?>>
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $show_in_register); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Show this profile field on the registration form','users-ultra'); ?>"></i>
				</p>
                
                 <p>
					<label for="uultra_<?php echo $pos; ?>_show_in_widget"><?php _e('Show on Widget','users-ultra'); ?>
					</label> 
                    <select name="uultra_<?php echo $pos; ?>_show_in_widget"
						id="uultra_<?php echo $pos; ?>_show_in_widget">
						<option value="0" <?php selected(0, $show_in_widget); ?> >
							<?php _e('No','users-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $show_in_widget); ?>>
							<?php _e('Yes','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Show this profile field on the user profiles','users-ultra'); ?>"></i>
				</p>
                
                
                 <p>                
                
    			<label for="uultra_<?php echo $pos; ?>_show_to_user_role"><?php _e('Display by User Role','users-ultra'); ?>
        		</label>
                
                <br />
        		<select name="uultra_<?php echo $pos; ?>_show_to_user_role" id="uultra_<?php echo $pos; ?>_show_to_user_role" class="uultra_show_to_user_role_edit" >
        				<option value="0" <?php selected(0, $show_to_user_role); ?>>
        					<?php _e('No','users-ultra'); ?>
        				</option>
        				<option value="1" <?php selected(1, $show_to_user_role); ?>>
        					<?php _e('Yes','users-ultra'); ?>
        				</option>
        		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
        			title="<?php _e('If no, this field will be displayed on profiles of all User Roles. Select yes to display this field only on profiles of specific User Roles.','users-ultra'); ?>"></i>
        		
            
            	</p>
                
                 <p style="display:" id="uultra_<?php echo $pos; ?>_show_to_user_role_list_div">
                 
                 
        		<label for="uultra_<?php echo $pos; ?>_show_to_user_role_list"><?php _e('Select User Roles','users-ultra'); ?>
        		</label>
                  <br />
        		<?php 
        			  $roles = 	$xoouserultra->role->uultra_get_available_user_roles();
					  
					  //get role list
					  
					  $curren_role_list = array();
        			  foreach($roles as $role_key => $role_display)
					  {
						  $curren_role_list = explode(",",$show_to_user_role_list);
						  
						  $checked_l = '';
						  
						  if(in_array($role_key,$curren_role_list)){
							   $checked_l = 'checked="checked"';
						  }
        		?>
        			  <input type='checkbox' name='uultra_<?php echo $pos; ?>_show_to_user_role_list[]' id='uultra_<?php echo $pos; ?>_show_to_user_role_list' value='<?php echo $role_key; ?>' class="uultra_<?php echo $pos; ?>_show_roles_ids"  <?php echo  $checked_l; ?>/>
        			  <label class='uultra-role-name'><?php echo $role_display; ?></label>
        		<?php
        			  }
        		?>
        		 <i class="uultra-icon-question-sign uultra-tooltip2"
        			title="<?php _e('This field will only be displayed on users of the selected User Roles.','users-ultra'); ?>"></i>
        		
                 
                  </p>
                  
                    <p >                
                
    			<label for="uultra_<?php echo $pos; ?>_edit_by_user_role"><?php _e('Editable by Users of Role','users-ultra'); ?>
        		</label>
                
                  <br />
        		<select name="uultra_<?php echo $pos; ?>_edit_by_user_role" id="uultra_<?php echo $pos; ?>_edit_by_user_role" class="uultra_edit_by_user_role_edit">
        				<option value="0" <?php selected(0, $edit_by_user_role); ?>>
        					<?php _e('No','users-ultra'); ?>
        				</option>
        				<option value="1" <?php selected(1, $edit_by_user_role); ?>>
        					<?php _e('Yes','users-ultra'); ?>
        				</option>
        		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
        			title="<?php _e('If yes, available user roles will be displayed for selection.','users-ultra'); ?>"></i>
        		
            
            	</p>
                
                
                 <p style="display:" id="uultra_<?php echo $pos; ?>_edit_by_user_role_list_div">
                 
                 
        		<label for="uultra_<?php echo $pos; ?>_show_to_user_role_list"><?php _e('Select User Roles','users-ultra'); ?>
        		</label>
                
                  <br />
        		<?php 
        			  $roles = 	$xoouserultra->role->uultra_get_available_user_roles('edit');
					  
					  //get role list
					  
					  $curren_role_list = array();
        			  foreach($roles as $role_key => $role_display)
					  {
						  $curren_role_list = explode(",",$edit_by_user_role_list);
						  
						  $checked_l = '';
						  
						  if(in_array($role_key,$curren_role_list)){
							   $checked_l = 'checked="checked"';
						  }
        		?>
        			  <input type='checkbox' name='uultra_<?php echo $pos; ?>_edit_by_user_role_list[]' id='uultra_<?php echo $pos; ?>_edit_by_user_role_list' value='<?php echo $role_key; ?>' class="uultra_<?php echo $pos; ?>_edit_roles_ids"  <?php echo  $checked_l; ?>/>
        			  <label class='uultra-role-name'><?php echo $role_display; ?></label>
        		<?php
        			  }
        		?>
        		 <i class="uultra-icon-question-sign uultra-tooltip2"
        			title="<?php _e('This field will only be displayed on users of the selected User Roles.','users-ultra'); ?>"></i>
        		
                 
                  </p>
                
               
                
                 <?php } ?>
                 
			<?php if ($type != 'seperator' || $type != 'separator') { ?>

		  <?php if (in_array($field, array('select','radio','checkbox')))
				 {
				    $show_choices = null;
				} else { $show_choices = 'uultra-hide';
				
				
				} ?>

				<p class="uultra-choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_choices"
						style="display: block"><?php _e('Available Choices','users-ultra'); ?> </label>
					<textarea name="uultra_<?php echo $pos; ?>_choices" type="text" id="uultra_<?php echo $pos; ?>_choices" class="large-text"><?php if (isset($array['choices'])) echo trim($choices); ?></textarea>
                    
                    <?php
                    
					if($xoouserultra->uultra_if_windows_server())
					{
						echo ' <p>'.__('<strong>PLEASE NOTE: </strong>Enter values separated by commas, example: 1,2,3. The choices will be available for front end user to choose from.').' </p>';					
					}else{
						
						echo ' <p>'.__('<strong>PLEASE NOTE:</strong> Enter one choice per line please. The choices will be available for front end user to choose from.').' </p>';
					
					
					}
					
					?>
                    <p>
                    
                    
                    </p>
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter one choice per line please. The choices will be available for front end user to choose from.','users-ultra'); ?>"></i>
				</p> <?php //if (!isset($array['predefined_loop'])) $predefined_loop = 0;
				
				if (!isset($predefined_options)) $predefined_options = 0;
				
				 ?>

				<p class="uultra_choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_predefined_options" style="display: block"><?php _e('Enable Predefined Choices','users-ultra'); ?>
					</label> 
                    <select name="uultra_<?php echo $pos; ?>_predefined_options"id="uultra_<?php echo $pos; ?>_predefined_options">
						<option value="0" <?php selected(0, $predefined_options); ?>>
							<?php _e('None','users-ultra'); ?>
						</option>
						<option value="countries" <?php selected('countries', $predefined_options); ?>>
							<?php _e('List of Countries','users-ultra'); ?>
						</option>
                        
                        <option value="age" <?php selected('age', $predefined_options); ?>>
							<?php _e('Age','users-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can enable a predefined filter for choices. e.g. List of countries It enables country selection in profiles and saves you time to do it on your own.','users-ultra'); ?>"></i>
				</p>

				<p>

					<span style="display: block; font-weight: bold; margin: 0 0 10px 0"><?php _e('Field Icon:','users-ultra'); ?>&nbsp;&nbsp;
						<?php if ($icon) { ?>
                        
                        <i class="fa fa-<?php echo $icon; ?>"></i>
                        
						<?php } else { 
						
						_e('None','users-ultra'); 
						
						} ?>
                        
                        &nbsp;&nbsp; <a href="#changeicon"
						class="button button-secondary uultra-inline-icon-uultra-edit"><?php _e('Change Icon','users-ultra'); ?>
					</a> </span> <label class="uultra-icons">
                    
                    <input type="radio"	name="uultra_<?php echo $pos; ?>_icon" value=""
						<?php checked('', $fonticon); ?> /> <?php _e('None','users-ultra'); ?> </label>
                        
                        
                        

					<?php 
					
					foreach($this->fontawesome as $fonticon) { 
					
					
					?>
					  
                      
                      <label class="uultra-icons"><input type="radio"	name="uultra_<?php echo $pos; ?>_icon" value="<?php echo $fonticon; ?>"
						<?php checked($fonticon, $icon); ?> />

                        <i class="fa fa-<?php echo $fonticon; ?> uultra-tooltip3"
						title="<?php echo $fonticon; ?>"></i> </label>
                        
                        
					<?php } //for each ?>
                    
                    

				</p>
				<div class="clear"></div> 
				
				<?php } ?>


  <div class="user-ultra-success uultra-notification" id="uultra-sucess-fields-<?php echo $pos; ?>"><?php _e('Success ','users-ultra'); ?></div>
				<p>
                
               
                 
				<input type="button" name="submit"	value="<?php _e('Update','users-ultra'); ?>"						class="button button-primary uultra-btn-submit-field"  data-edition="<?php echo $pos; ?>" /> 
                   <input type="button" value="<?php _e('Cancel','users-ultra'); ?>"
						class="button button-secondary uultra-btn-close-edition-field" data-edition="<?php echo $pos; ?>" />
				</p>
                
      <?php
	  
	  die();
		
	}
	
	public function xoousers_create_standard_form_fields ($form_name )	
	{		
	
		/* These are the basic profile fields */
		$fields_array = array(
			80 => array( 
			  'position' => '50',
				'type' => 'separator', 
				'name' => __('Profile Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'deleted' => 0
			),
			
			100 => array( 
			  'position' => '100',
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'first_name', 
				'name' => __('First Name','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0
			),
			120 => array( 
			  'position' => '101',
				'icon' => 0, 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'last_name', 
				'name' => __('Last Name','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0
			),
			
			130 => array( 
			  'position' => '130',
				'icon' => '0',
				'field' => 'select',
				'type' => 'usermeta',
				'meta' => 'age',
				'name' => __('Age','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'required' => 1,
				'private' => 0,
				'social' => 0,
				'predefined_options' => 'age',
				'deleted' => 0,				
				'allow_html' => 0
			),
			
			150 => array( 
			  'position' => '150',
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'display_name', 
				'name' => __('Display Name','users-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'private' => 0,
				'social' => 0,
				'required' => 1,
				'deleted' => 0
			),
			170 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'brief_description',
				'name' => __('Brief Description','users-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1
			),
			190 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'description',
				'name' => __('About / Bio','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1
			),
			200 => array( 
			  'position' => '200',
				'icon' => '0',
				'field' => 'select',
				'type' => 'usermeta',
				'meta' => 'country',
				'name' => __('Country','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'required' => 1,
				'private' => 0,
				'social' => 0,
				'predefined_options' => 'countries',
				'deleted' => 0,				
				'allow_html' => 0
			),
			
			230 => array( 
			  'position' => '250',
				'type' => 'separator', 
				'name' => __('Contact Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'deleted' => 0
			),
			
			
			430 => array( 
			  'position' => '400',
				'icon' => 'link', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_url', 
				'name' => __('Website','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'required' => 0,
				'private' => 0,
				'social' => 0,
				'deleted' => 0
			),
			470 => array( 
			  'position' => '450',
				'type' => 'separator', 
				'name' => __('Social Profiles','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'deleted' => 0
			),
			520 => array( 
			  'position' => '500',
				'icon' => 'facebook', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'facebook', 
				'name' => __('Facebook','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'required' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'private' => 0,
				'social' => 1,
				'tooltip' => __('Connect via Facebook','users-ultra'),
				'deleted' => 0
			),
			560 => array( 
			  'position' => '510',
				'icon' => 'twitter', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'twitter', 
				'name' => __('Twitter Username','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'required' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'private' => 0,
				'social' => 1,
				'tooltip' => __('Connect via Twitter','users-ultra'),
				'deleted' => 0
			),
			590 => array( 
			  'position' => '520',
				'icon' => 'google-plus', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'googleplus', 
				'name' => __('Google+','users-ultra'),
				'can_hide' => 1,
				'can_edit' => 1,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'private' => 0,
				'required' => 0,
				'social' => 1,
				'tooltip' => __('Connect via Google+','users-ultra'),
				'deleted' => 0
			),
			600 => array( 
			  'position' => '550',
				'type' => 'separator', 
				'name' => __('Account Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 0,
				'show_in_widget' =>0,
				'deleted' => 0
			),
			690 => array(
			  'position' => '600',
				'icon' => 'lock',
				'field' => 'password',
				'type' => 'usermeta',
				'meta' => 'user_pass',
				'name' => __('New Password','users-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'private' => 1,
				'social' => 0,
				'deleted' => 0
			),
			720 => array(
			  'position' => '700',
				'icon' => 0,
				'field' => 'password',
				'type' => 'usermeta',
				'meta' => 'user_pass_confirm',
				'name' => 0,
				'can_hide' => 0,
				'can_edit' => 1,
				'private' => 1,
				'social' => 0,
				'deleted' => 0
			)
		);
		
		/* Store default profile fields for the first time */
		if (!get_option($form_name))
		{
			if($form_name!="")
			{
				update_option($form_name,$fields_array);
			
			}
			
		}	
		
		
	}
	
	/*Loads all field list Implemented on 08-08-2014*/	
	function uultra_reload_custom_fields_set ()	
	{
		
		global $xoouserultra;
		
		$custom_form = sanitize_text_field($_POST["uultra_custom_form"]);		
		
		if($custom_form!="") //use a custom form
		{
			//check if fields have been added			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;
			
			if (!get_option($custom_form)) //we need to create a default field set for this form
			{
				
				$this->xoousers_create_standard_form_fields($custom_form);									
				$fields = get_option($custom_form);
				
			}else{
				
				//fields have been added to the custom form.				
				$fields = get_option($custom_form);
			
			
			}
			
		
		}else{ //use the default registration from
			
			$fields = get_option('usersultra_profile_fields');
			
		
		}
		
		ksort($fields);		
		
		$i = 0;
		foreach($fields as $pos => $array) 
		{
		    extract($array); $i++;

		    if(!isset($required))
		        $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';
				
				
			if ($type == 'seperator' || $type == 'separator') {
			   
				$class = "separator";
				$class_title = "";
			} else {
			  
				$class = "profile-field";
				$class_title = "profile-field";
			}
		    ?>
            
          <li class="uultra-profile-fields-row <?php echo $class_title?>" id="<?php echo $pos; ?>">
            
            
            <div class="heading_title  <?php echo $class?>">
            
            <h3>
            <?php
			
			if (isset($array['name']) && $array['name'])
			{
			    echo  $array['name'];
			}
			?>
            
            <?php
			if ($type == 'separator') {
				
			    echo __(' - Separator','users-ultra');
				
			} else {
				
			    echo __(' - Profile Field','users-ultra');
				
			}
			?>
            
            </h3>
            
            
              <div class="options-bar">
             
                 <p>                
                    <input type="submit" name="submit" value="<?php _e('Edit','users-ultra'); ?>"						class="button uultra-btn-edit-field button-primary" data-edition="<?php echo $pos; ?>" /> <input type="button" value="<?php _e('Delete','users-ultra'); ?>"	data-field="<?php echo $pos; ?>" class="button button-secondary uultra-delete-profile-field-btn" />
                    </p>
            
             </div>
            
            
          

            </div>
            
             
             <div class="user-ultra-success uultra-notification" id="uultra-sucess-delete-fields-<?php echo $pos; ?>"><?php _e('Success! This field has been deleted ','users-ultra'); ?></div>
            
           
        
          <!-- edit field -->
          
          <div class="user-ultra-sect-second uultra-fields-edition user-ultra-rounded"  id="uu-edit-fields-bock-<?php echo $pos; ?>">
        
          </div>
          
          
          <!-- edit field end -->

       </li>







	<?php
	
	}
		
		die();
		
	
	}
		
	// update settings
    function update_settings() 
	{
		foreach($_POST as $key => $value) 
		{
            if ($key != 'submit')
			{
				if (strpos($key, 'html_') !== false)
                {
                      //$this->userultra_default_options[$key] = stripslashes($value);
                }else{
					
					 // $this->userultra_default_options[$key] = esc_attr($value);
                 }
					
					//special rule for html 					
					if($key=="uultra_terms_and_conditions_text")
					{						
						//echo "Page : " . $value;
						$value=stripslashes($value);				 
						 
						 
					}
                
                    $value = sanitize_text_field($value);	
					  
					
					$this->userultra_set_option($key, $value) ; 
					
					//special setting for page
					if($key=="xoousersultra_my_account_page")
					{						
						//echo "Page : " . $value;
						 update_option('xoousersultra_my_account_page',$value);				 
						 
						 
					}  

            }
        }
		
		//get checks for each tab
		
		
		 if ( isset ( $_GET['tab'] ) )
		 {			 
			  $current = sanitize_text_field($_GET['tab']);
				
          } else {
               $current = sanitize_text_field($_GET['page']);
				
          }	
            
		$special_with_check = $this->get_special_checks($current);
         
        foreach($special_with_check as $key)
        {
           
            
                if(!isset($_POST[$key]))
				{			
                    $value= '0';
					
				 } else {
					 
					  $value= $_POST[$key];
				}	 	
         
			
			$this->userultra_set_option($key, $value) ;  
			
			
            
        }
         
      $this->options = get_option('userultra_options');

        echo '<div class="updated"><p><strong>'.__('Settings saved.','users-ultra').'</strong></p></div>';
    }
	
	public function get_special_checks($tab) 
	{
		$special_with_check = array();
		
		if($tab=="settings")
		{				
		
		 $special_with_check = array('hide_admin_bar', 'disable_default_lightbox', 'uultra_loggedin_activated', 'uultra_allow_guest_rating', 'uultra_allow_guest_like','private_message_system','redirect_backend_profile','redirect_backend_registration', 'redirect_registration_when_social','redirect_backend_login', 'social_media_fb_active', 'social_media_linked_active', 'social_media_yahoo', 'social_media_google', 'twitter_connect', 'instagram_connect', 'yammer_connect', 'twitter_autopost','mailchimp_active', 'mailchimp_auto_checked', 'media_allow_photo_uploading', 'membership_display_selected_only', 'uultra_password_1_letter_1_number' , 'uultra_password_one_uppercase' , 'uultra_password_one_lowercase' );
		 
		}elseif($tab=="gateway"){
			
			 $special_with_check = array('gateway_paypal_active', 'gateway_bank_active');
			 
		}elseif($tab=="customizer"){
			
			 $special_with_check = array('templates_flat_css', 'templates_remove_spikes' );
		
		}elseif($tab=="mail"){
			
			 $special_with_check = array('uultra_smtp_mailing_return_path', 'uultra_smtp_mailing_html_txt');
		 
		}
		
		
		if($tab=="uupro20-recaptcha")
		{				
		
			 $special_with_check = array('recaptcha_display_registration_native','recaptcha_display_loginform_native', 
			 'recaptcha_display_forgot_password_native','recaptcha_display_comments_native' ,
			 'recaptcha_display_registration','recaptcha_display_loginform');		
		 
		}
	
	return  $special_with_check ;
	
	}
	
	function admin_page_membership() 
	{
		//handle actions
		
		
		if (isset($_POST['update_settings']))
		{
            $this->update_settings();
        }
		
		
		

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		
		
		if (isset($_POST['woosync'])) {
			$this->woo_sync();
		}
		
		if (isset($_POST['woosync_del'])){
			$this->woo_sync_del();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
        
           
           
           <h2>USERS ULTRA</h2>
           
		
		            
            
            <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>
            
           
            
           

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->include_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }
	
	
	function include_tab_content() {
		$screen = get_current_screen();
		
		if( strstr($screen->id, $this->slug ) ) 
		{
			if ( isset ( $_GET['tab'] ) ) 
			{
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			
			//if($this->valid_c=="")
			//{
				
			
			
			//}else{
				
			//	$tab = "licence";				
			//	require_once (xoousers_path.'admin/tabs/'.$tab.'.php');
				
				
			//}
            
            
            require_once (xoousers_path.'admin/tabs/'.$tab.'.php');
			
			
		}
	}
	
	function create_uploader_folder ()
	{
		global $xoouserultra;
		
		$mediafolder = true;
		
		//get folder
		$media_folder = $xoouserultra->get_option('media_uploading_folder');
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		//wp-content path
		
		$wp_content_path  = ABSPATH."wp-content/";
		echo $wp_content_path;
		
		if(!is_dir($path_pics)) 
		{
			
			$f_perm = substr(decoct(fileperms($wp_content_path)),2,4);
			
			if($f_perm == 777)
			{
				$this->CreateDir($path_pics);				
				chmod($wp_content_path, 0755);
								
			
			}else{
				
				chmod($wp_content_path, 0777);
				$this->CreateDir($path_pics);				
				chmod($wp_content_path, 0755);
			
			
			}
			
					
			
			//echo "create: ". $path_pics;							   
		}
		
	}
	
	public function CreateDir($root)
	{

               if (is_dir($root))        {

                        $ret = "0";
                }else{

                        $oldumask = umask(0);
                        $valrRet = mkdir($root,0755);
                        umask($oldumask);


                        $re = "1";
                }

    }
	
	function checkUploadFolder()
	{
		global $xoouserultra;
		
		$mediafolder = true;
		
		//get folder
		$media_folder = $xoouserultra->get_option('media_uploading_folder');
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		
		$html = '';
		
		if(!is_dir($path_pics))
		{
			$mediafolder=false;
			
			$html .= ' <div ><div class="user-ultra-warning">'.__("Please create '".$media_folder."' folder with 0755 attribute. You can create it automatically by <a href='#' id='uultradmin-create-upload-folder'>clicking here</a>", 'users-ultra').'</div></div>';
			
		}else{
		
			$f_perm = substr(decoct(fileperms($path_pics)),2,4);
			
			if($f_perm !=755)
			{
				//$mediafolder=false;
				
		//		$html .= ' <div ><div class="user-ultra-warning">'.__("Change attributes TO 0755 for  '".$media_folder."' folder.", 'users-ultra').'</div></div>';	
			
			}
			
		}

		
		return $html;
	
	
	}

	
	


	function admin_page() 
	{
		global $xoouserultra;
		//handle actions
		
		
		
		if (isset($_POST['update_settings'])) {
            $this->update_settings();
        }
		
		
		if (isset($_POST['update_uultra_slugs']) && $_POST['update_uultra_slugs']=='uultra_slugs')
		{
           $xoouserultra->create_rewrite_rules();
           flush_rewrite_rules();
			echo '<div class="updated"><p><strong>'.__('Rewrite Rules were Saved.','users-ultra').'</strong></p></div>';
        }
		

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		
		
		if (isset($_POST['woosync'])) {
			$this->woo_sync();
		}
		
		if (isset($_POST['woosync_del'])){
			$this->woo_sync_del();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
        
           
           
           <h2>USERS ULTRA PRO 3.0<span class="uultra-go-pro-topbutton"><a href="?page=userultra&tab=pro" class="button button-secondary user-ultra-btn-red-pro" ><i class="uultra-icon-plus"></i>&nbsp;&nbsp;Why Go Pro?
</a></span></h2>
           
           <div id="icon-users" class="icon32"></div>
            <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>
            
            
            <?php
echo $xoouserultra->uultra_new_media_manager_check();
?>
            
            
             <?php
			  if($this->valid_c=="no"){?>
            
                      
            
            <?php }?>
            
             <?php
            
			//special settings check
			
			/*Check My Account  URL*/			
			if($xoouserultra->login->get_my_account_direct_link_checking()=="")
			{
				?>
                
                 <div class="uuultra-top-noti-admin "><div class="user-ultra-warning"><?php echo __("Please make sure that you've set the right My Account Page. Users Ultra couldn't find it and some important features such as password reset, account confirmation won't work properly.", 'users-ultra')?> <a href="?page=userultra&tab=permalinks"><?php echo __("Click here to set the My Account Page", 'users-ultra')?></a></div></div>
                
                <?php	
			
			}	
			
			?>

			<div id="userultra-admin-contain" class="<?php echo $this->slug; ?>-admin-contain">
            
              <?php echo $this->checkUploadFolder(); ?>
				
				<?php $this->include_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}
$key = "xoouseradmin";
$this->{$key} = new XooUserAdmin();