<?php
//session_start();
class XooUserUltra
{
	public $classes_array = array();
	public $registration_fields;
	public $login_fields;
	public $fields;
	public $allowed_inputs;
	public $use_captcha = "no";
	public $badges_url = "";
	
	var $_aPostableTypes = array(
        'post',
        'page',
        'attachment',
    );
	
		
	public function __construct()
	{
		
		
		$this->logged_in_user = 0;
		$this->login_code_count = 0;
		$this->current_page = $_SERVER['REQUEST_URI'];
		
				
		
		
		add_action( 'init', array($this, 'handle_init' ) );
				
		$this->badges_url = xoousers_url."templates/".xoousers_template."/img/";
		
    }
	
	function handle_init() 
	{
		if (isset($_GET['uultrasocialsignup'])) 
		{
			session_start();
			$_SESSION['google_token']  = NULL;
			/* get social links */
			$this->social_login_links_openid();			
				
		}
		
	
	}
	
	public function plugin_init() 
	{
		
		
		/*Load Amin Classes*/		
		if (is_admin()) 
		{
			$this->set_admin_classes();
			$this->load_classes();					
		
		}else{
			
			/*Load Main classes*/
			$this->set_main_classes();
			$this->load_classes();
			
		
		}
		
		//ini settings
		$this->intial_settings();
		
		
	}
	
 
	public function set_main_classes()
	{
		 $this->classes_array = array( "commmonmethods" =>"xoo.userultra.common" ,
		  "ticket" =>"xoo.userultra.ticket",
		  "imagecrop" =>"xoo.userultra.cropimage",
		  "captchamodule" =>"xoo.userultra.captchamodules",
		  "adminshortcode" =>"xoo.userultra.adminshortcodes" ,
		  "defender" =>"xoo.userultra.defender" ,
		  "badge" =>"xoo.userultra.badge" ,
		  "changelog" =>"xoo.userultra.changelog" ,
		  "bbpress" =>"xoo.userultra.bbpress" ,
		  "role" =>"xoo.userultra.role" ,
		  "wall" =>"xoo.userultra.wall",  
		  "group" =>"xoo.userultra.group" ,
		  "customizer" =>"xoo.userultra.customizer" ,
		  "credit" =>"xoo.userultra.credit" ,		
		  "htmlbuilder" =>"xoo.userultra.htmlbuilder" ,
		  "publisher" =>"xoo.userultra.publisher" ,
		  "activity" =>"xoo.userultra.activity" ,
		  "messaging" =>"xoo.userultra.messaging" ,  
		  "recaptchalib" =>"xoo.ulserultra.recaptchalib",  
		  "order" =>"xoo.userultra.order",
		  "subscribe" =>"xoo.userultra.newslettertool",
		  "paypal" =>"xoo.userultra.payment.paypal"  ,
		  "social" =>"xoo.userultra.socials", 
		  "shortocde" =>"xoo.userultra.shorcodes" , 
		  "login" =>"xoo.userultra.login" ,  
		  "register" =>"xoo.userultra.register",
		  "mymessage" =>"xoo.userultra.mymessage" , 
		  "rating" =>"xoo.userultra.rating" , 
		  "statistc" =>"xoo.userultra.stat" ,		
		  "photogallery" =>"xoo.userultra.photos"  , 
		  "woocommerce" =>"xoo.userultra.woocommerce"  ,		  
		  "userpanel" =>"xoo.userultra.user" ,
		  "api" =>"xoo.userultra.api"
		   ); 	
	
	}
	
	public function set_admin_classes()
	{
				 
		 $this->classes_array = array( "commmonmethods" =>"xoo.userultra.common" ,
		 "ticket" =>"xoo.userultra.ticket",
		 "imagecrop" =>"xoo.userultra.cropimage",
		"captchamodule" =>"xoo.userultra.captchamodules",		
		 "defender" =>"xoo.userultra.defender" ,		
		 "xooadmin" =>"xoo.userultra.admin",	
		 "changelog" =>"xoo.userultra.changelog" ,
		 "bbpress" =>"xoo.userultra.bbpress" ,
		 "role" =>"xoo.userultra.role" ,
		 "group" =>"xoo.userultra.group" ,		
		 "wall" =>"xoo.userultra.wall" ,
		 "badge" =>"xoo.userultra.badge" ,
		 "activity" =>"xoo.userultra.activity" ,
		 "customizer" =>"xoo.userultra.customizer" ,	
		 "credit" =>"xoo.userultra.credit",	 			
		  "htmlbuilder" =>"xoo.userultra.htmlbuilder" ,
		  "publisher" =>"xoo.userultra.publisher" ,		
		  "order" =>"xoo.userultra.order",
		  "subscribe" =>"xoo.userultra.newslettertool",
		  "paypal" =>"xoo.userultra.payment.paypal"  , 
		  "social" =>"xoo.userultra.socials", 
		  "shortocde" =>"xoo.userultra.shorcodes" , 
		  "adminshortcode" =>"xoo.userultra.adminshortcodes" ,
		  "messaging" =>"xoo.userultra.messaging" ,
		  "login" =>"xoo.userultra.login" , 
		  "register" =>"xoo.userultra.register",
		  "mymessage" =>"xoo.userultra.mymessage" , 	
		  "rating" =>"xoo.userultra.rating" , 	  
		  "statistc" =>"xoo.userultra.stat" ,		 
		  "photogallery" =>"xoo.userultra.photos"  ,
		  "woocommerce" =>"xoo.userultra.woocommerce"  , 	   
		  "userpanel" =>"xoo.userultra.user",
		  "api" =>"xoo.userultra.api"
		  
		   ); 	
		 
		
	}
	
	public function pluginname_ajaxurl() 
	{
		echo '<script type="text/javascript">var ajaxurl = "'. admin_url("admin-ajax.php") .'";
</script>';
	}
	
	
	public function intial_settings()
	{
		
		add_action( 'admin_notices', array(&$this, 'uultra_display_custom_message'));
		add_action( 'wp_ajax_create_default_pages_auto', array( $this, 'create_default_pages_auto' ));	
		add_action( 'wp_ajax_uultra_upgrade_to_media_confirm', array( $this, 'uultra_upgrade_to_media_confirm' ));	
        
        add_action( 'wp_ajax_uultra_remove_30_message', array( $this, 'remove_30_message' ));	
        
        
				
			 			 
		$this->include_for_validation = array('text','fileupload','textarea','select','radio','checkbox','password');
			
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 9); 
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles_scripts'), 9);
		
		/* Remove bar except for admins */
		add_action('init', array(&$this, 'userultra_remove_admin_bar'), 9);	
		
		/* Create Standar Fields */		
		add_action('init', array(&$this, 'xoousers_create_standard_fields'));
		add_action('admin_init', array(&$this, 'xoousers_create_standard_fields'));		
				
		/*Create a generic profile page*/
		add_action( 'init', array(&$this, 'create_initial_pages'), 9);
		
		/*Setup redirection*/
		add_action( 'wp_loaded', array(&$this, 'xoousersultra_redirect'), 9);
		add_action( 'wp_head', array(&$this, 'pluginname_ajaxurl'));
		add_action( 'wp_head', array(&$this, 'add_custom_css_style'));		
		//add_action( 'mce_css', array(&$this, 'uultra_my_theme_add_editor_styles'));			
		
		add_action( 'save_post',  array( &$this, 'uultra_custom_save_post_feature' ), 87);
		add_action ('template_redirect',   array( &$this, 'uultra_redirect_block_site' ));
		
		$this->uultra_post_protection_logged_in();
		
	}
	
	// post protection by logged in users	
	function uultra_post_protection_logged_in() 
	{
        
        if($this->check_is_pro_version()){
            
            if($this->get_option('uultra_loggedin_activated')=='1')
            {				

                add_action('save_post',  array( &$this, 'uultra_save_post_logged_in_protect' ), 93);	
                add_filter('the_posts', array(&$this, 'uultra_showPost'), 89);	
                add_filter('get_pages', array(&$this, 'uultra_showPage'),77);			
                add_action('add_meta_boxes', array(&$this, 'uultra_post_protection_add_meta_box' ));	

            }
            
        }
		
	}
	
	 /**
     * The function for the get_pages filter.
     * 
     * @param array $aPages The pages.
     * 
     * @return array
     */
    public function uultra_showPage($aPages = array())
    {
		global $xoouserultra;
		
        $aShowPages = array(); 
				    
       
        foreach ($aPages as $oPage) 
		{
            if ($xoouserultra->get_option('uultra_loggedin_hide_complete_page') == 'yes'   ) 
			{				          
				
				if ($this->checkAccessToPost($oPage->ID)) 
				{					
				 
					// $oPage->post_title =$xoouserultra->get_option('uultra_loggedin_page_title');
					// $oPage->post_content = $xoouserultra->get_option('uultra_loggedin_page_content') ;
					
					$aShowPages[] = $oPage;
				
				}
				
				
               
				
				
            } else {
				
				
				
                if (!$this->checkAccessToPost($oPage->ID)) 
				{
					if ($xoouserultra->get_option('uultra_loggedin_hide_page_title') == 'yes') 
					{
						$oPage->post_title =$xoouserultra->get_option('uultra_loggedin_page_title');
					}

                    $oPage->post_content = $xoouserultra->get_option('uultra_loggedin_page_content') ;
					//$oPage->post_content =  "called yes" ;
                }

               // $oPage->post_title .= $this->adminOutput($oPage->post_type, $oPage->ID);
                $aShowPages[] = $oPage;
            }
			
			
        }
        
        $aPages = $aShowPages;
        
        return $aPages;
    }
	
	 /**
     * The function for the the_posts filter.
     * 
     * @param array $aPosts The posts.
     * 
     * @return array
     */
    public function uultra_showPost($aPosts = array())
    {
		global $xoouserultra;
        $aShowPosts = array();		
       
        if (!is_feed() || ($this->get_option('uultra_loggedin_protect_feed') == 'yes'  && is_feed())) 
		{
			//echo "HERE ";
            foreach ($aPosts as $iPostId)
			 {
                if ($iPostId !== null) 
				{
                    $oPost = $this->_getPost($iPostId);

                    if ($oPost !== null)
					{
                        $aShowPosts[] = $oPost;
                    }
                }
            }

            $aPosts = $aShowPosts;
        }
        
        return $aPosts;
    }
	
	
	 /**
     * Modifies the content of the post by the given settings.
     * 
     * @param object $oPost The current post.
     * 
     * @return object|null
     */
    protected function _getPost($oPost)
    {
		global $xoouserultra;
		
       
        $sPostType = $oPost->post_type;

		if ($sPostType != 'post' && $sPostType != 'page')
		{			
			   $sPostType = 'post';
			
        } elseif ($sPostType != 'post' && $sPostType != 'page') 
		{
            return $oPost;
        }
        
        if ($xoouserultra->get_option('uultra_loggedin_hide_complete_'.$sPostType.'') == 'yes' ) 
		{         
			
			if ($this->checkAccessToPost($oPost->ID)) 
			{
         		
				 return $oPost;
				 
					// $oPost->post_title =$xoouserultra->get_option('uultra_loggedin_'.$sPostType.'_title');
					// $oPost->post_content =  $xoouserultra->get_option('uultra_loggedin_'.$sPostType.'_content');
			
							
			}
			
			
            
			
        } else {
			
            if (!$this->checkAccessToPost($oPost->ID)) 
			{
                $oPost->isLocked = true;
                
                $uultraPostContent = $xoouserultra->get_option('uultra_loggedin_'.$sPostType.'_content');
                
                if ($xoouserultra->get_option('uultra_loggedin_hide_'.$sPostType.'_title') == 'yes') 
				{
                    $oPost->post_title =$xoouserultra->get_option('uultra_loggedin_'.$sPostType.'_title');
                }
                
                if ($xoouserultra->get_option('uultra_loggedin_allow_'.$sPostType.'_comments') == 'no')
				{
                    $oPost->comment_status = 'close';
                }

                if ($xoouserultra->get_option('uultra_loggedin_post_content_before_more') == 'yes'
                    && $sPostType == "post" && preg_match('/<!--more(.*?)?-->/', $oPost->post_content, $aMatches)
                ) 
				{
                    $oPost->post_content = explode($aMatches[0], $oPost->post_content, 2);
                    $uultraPostContent = $oPost->post_content[0] . " " . $uultraPostContent;
                }

                $oPost->post_content = stripslashes($uultraPostContent);
            }

            
            return $oPost;
        }
		
		
        
        return null;
    }
	
	public function checkAccessToPost($post_id) 
	{
		global $xoouserultra;
		
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		
		$res = true;
		
		$uultra_protect_logged_in = get_post_meta( $post_id, 'uultra_protect_logged_in' , true);
		
		if ($uultra_protect_logged_in == 'yes' ) 
		{
			if(!is_user_logged_in())
			{
				$res = false;
				
			}
			
		}else{
			
				
				$res = true;		
		
		}
		
		return $res;
		
	
	}
	
	function uultra_save_post_logged_in_protect( $post_id ) 
	{	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
			
				 
		 $post = get_post($post_id);
        if($post->post_status == 'trash' ){
                return $post_id;
        }
		
		$aFormData = array();
		
		
		 if (isset($_POST['uultra_update_logged_in_access'])) {
            $aFormData = $_POST;
			
        } elseif (isset($_GET['uultra_update_logged_in_access'])) {
			
            $aFormData = $_GET;
        }
				
		
		if (isset($aFormData['uultra_update_logged_in_access']))
		{
			$is_protected = $aFormData['uultra_protect_logged_in'];
			update_post_meta($post_id, 'uultra_protect_logged_in', $is_protected);		 
						
		}			
	}
	
	function uultra_post_protection_add_meta_box() 
	{
		$this->_aPostableTypes = array_merge($this->_aPostableTypes, get_post_types(array('publicly_queryable' => true), 'names'));
        $this->_aPostableTypes = array_unique($this->_aPostableTypes);
	
		$aPostableTypes = $this->getPostableTypes();
                
        foreach ($aPostableTypes as $sPostableType) 
		{
			add_meta_box('uultra_post_access_logged_in', 'Users Ultra Post Protection', array(&$this, 'editPostContent'), $sPostableType, 'side');
			
        }
		
	}
	
	public function getPostableTypes()
    {
        return $this->_aPostableTypes;
    }
	
	public function editPostContent($oPost)
    {
		
        $iObjectId = $oPost->ID;
	   
		if (isset($_GET['attachment_id'])) {
				$iObjectId = $_GET['attachment_id'];
		} elseif (!isset($iObjectId)) {
				$iObjectId = 0;
		}
			
		$oPost = get_post($iObjectId);
		$sObjectType = $oPost->post_type;
		
		$uultra_protect_logged_in = get_post_meta( $iObjectId, 'uultra_protect_logged_in' , true);
		
		$html = '';
		
		$html .= '<div class="uultra-protect-group-options">	';				
		$html .= '<input type="hidden" name="uultra_update_logged_in_access" value="true" />	';			
			
				 
		if ($uultra_protect_logged_in=='yes')
		{
			$checked = 'checked="checked"';
		}
				 
		$html .= ' <li>';				
		$html .= '<input type="checkbox" id="uultra_protect_logged_in" value="yes" name="uultra_protect_logged_in" '.$checked.' /> ';
                 
       $html .= ' <label for="uultra_protect_logged_in" class="selectit" style="display:inline;" >
            '.__('Only Logged in Users','users-ultra').'
        </label>';
				
		$html .= ' </li>';		
		$html .= ' </div>';
		
		
		echo $html;	

    }	
	
	function uultra_custom_save_post_feature( $post_id ) 
	{	
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave($post_id) )
			return;
			
		 // stop on autosave
		 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		 }
		 
		 $post = get_post($post_id);
		 
        if($post->post_status == 'trash' || $post->post_status == 'draft' || $post->post_status == 'auto-draft' || $post->post_status == 'pending' || $post->post_type == 'page' || $post->post_type == 'product'){
                return $post_id;
        }
		
		if( $post->post_type != 'post'){
                return $post_id;
        }
		
		//check if is admin	post should be included in wall	
		$admin_post=$this->get_option('uultra_user_wall_enable_new_post_as_admin');		
		$is_admin = current_user_can( 'manage_options' );
				
		$logged_user_id = get_current_user_id();
					
		if($admin_post=='no' && $is_admin && $logged_user_id== $post->post_author)
		{
			//return $post_id;
		
		}else{			
			
			//check if new posts should be included in the activity module		
			$site_wide_post=$this->get_option('uultra_user_wall_enable_new_post');
			
			if($site_wide_post=='no')
			{
				//return $post_id;
			 
			}else{
				
				// Add to activity wall.		
				$this->wall->wall_save_activity($post_id, 'post')	;			
			
			}
		
		}
		
			
	}
	
	/******************************************
	Check if user exists by ID
	******************************************/
	function user_exists( $user_id ) 
	{
		$aux = get_userdata( $user_id );
		if($aux==false){
			return false;
		}
		return true;
	}
	
	
	
	public function add_custom_css_style () 
	{
		$custom_css = $this->get_option('xoousersultra_custom_css');
		$html = "";
		
		$default_widgets = get_option('userultra_default_user_tabs');
		foreach($default_widgets as $key => $widget)
		{
			if(isset($widget['native']) && $widget['native']==0)
			{
				$type ="custom" ;				
				
			}else{
				
				$type = "default";			
			
			}
			$custom_css .= $this->customizer->get_widget_bg_inline_arrows_css($key, $type  );
		
		
		}
		
		//flat styles		
		$flat_css = $this->get_option('templates_flat_css');
		$remove_spikes = $this->get_option('templates_remove_spikes');
		
		if($flat_css==1 )
		{
			
			$custom_css .= '.uultra-prof-cont h3 {-webkit-border-radius: 0px !important;
      -moz-border-radius: 0px !important;
      border-radius: 0px !important ;} .widget-ultra {-webkit-border-radius: 0px !important;
      -moz-border-radius: 0px !important;
      border-radius: 0px !important ;}';		
		
		}
		
		if($remove_spikes==1 )
		{
			
			//$custom_css .= '.default-bg{display:none;}';		
		
		}
				
		if($custom_css!="" )
		{
			$html .= ' <style type="text/css">';
			$html .= $custom_css;
			$html .= ' </style>';			
		}
		
		echo $html;		
		
	}
	
	public function create_default_pages_auto () 
	{
		update_option('xoousersultra_auto_page_creation',1);
		
	}
	
	public function uultra_display_custom_message () 
	{
				
		//default pages created?
		$my_account_page = get_option('xoousersultra_my_account_page');
		$fresh_page_creation  = get_option( 'xoousersultra_auto_page_creation' );
		
		if($my_account_page=="" && $fresh_page_creation =="")
		//if($fresh_page_creation =="")
		{
			$message = __('Thanks for installing Users Ultra Pro. Do you need help?. Users Ultra Pro can create the initial pages automatically. You just need to <a href="#"  id="uultradmin-create-basic-fields">CLICK HERE</a> to start using Users Ultra Pro. ', 'users-ultra');
			$this->uultra_fresh_install_message($message);		
		
		}
		
		//chekc my account link
		$acc_link = $this->login->get_my_account_direct_link();
		
		if($acc_link=="" )
		{
			echo '<div id="message" class="error"><p><strong>'.__("Users Ultra might be working wrong. We couldn't find the 'My Account' shortcode. Please click on settings tab and make sure that the My Account page has been set correctly. Then click on the 'save' button ","xoousers").'</strong></p></div>';		
		
		}
		
		if(!is_plugin_active('users-ultra/xoousers.php'))
		{	
			/*$message .= '<div id="message" class="updated uultra-message-red wc-connect">				
			
				<p><strong>'.__("IMPORTANT: Users Ultra PRO 2.0:",'exoousers').'</strong> – '.__("It's very important that you install or activate Users Ultra Lite Core Plugin. This plugin is vital for thew new Users Ultra Pro 2.0",'users-ultra').'</p>
				
				<p class="submit">
					
					<a href="plugin-install.php?s=users+ultra&tab=search&type=term" class="button-secondary" > '.__('Click here to download and activate it for free','users-ultra').'</a>
				</p>
	      </div>'; */
			
			//echo $message;
		
		
		}
        
        $is_first_30_active= get_option('xoousersultra_30_active');
        
        if($is_first_30_active=="")
		{	
			$message .= '<div id="message" class="updated uultra-message-yellow-30 wc-connect">				
			
				<p><strong>'.__("IMPORTANT: USERS ULTRA PRO 3.0:",'exoousers').'</strong> – '.__("The Plugin has been rebuilt. The upgrading is vital to start using all the new amazing features for free. If you're already using the latest version ignore this message.",'users-ultra').'</p>                
                				
				<p class="submit">
					
					<a href="admin.php?page=userultra&tab=upgrade" class="button-primary" > '.__('Click here to learn more','users-ultra').'</a> <a href="#" id="uultra-pro-remove-30-message" class="button-secondary" > '.__('Click here to remove this message','users-ultra').'</a>
				</p>
	      </div>';
			
			echo $message;
		
		
		}
        
        
		
		
		
	}
	
	//display message
	public function uultra_fresh_install_message ($message) 
	{
		
			
			echo '<div id="message" class="updated fade">';
		
	
		    echo "<p><strong>$message</strong></p></div>";
	
	}
    
   
        
   function remove_30_message()
	{
       
       update_option('xoousersultra_30_active', "yes" );
       
    }
    
    
    function check_is_pro_version()
	{
        $return = true;
        
        $serial=  get_option('uultra_c_key' );
        $uu_version = get_option('uultra_c_version');
        
        if($uu_version=="" || $serial ==""){            
            
            $return  = false;
            
        }      
        
        return $return;
    }
	
	public function uultra_uninstall () 
	{
		
		global $wpdb;
		
		$thetable = $wpdb->prefix."usersultra_stats_raw";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_stats";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_friends";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_likes";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_ajaxrating_vote";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_ajaxrating_votesummary";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_galleries";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_photos";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_photo_categories";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_photo_cat_rel";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_videos";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_packages";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		$thetable = $wpdb->prefix."usersultra_orders";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");			
		
		$thetable = $wpdb->prefix."users_ultra_pm";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		$thetable = $wpdb->prefix."usersultra_activity";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		
		//wall
		$thetable = $wpdb->prefix."usersultra_wall";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");	
		$thetable = $wpdb->prefix."usersultra_wall_replies";		
	    $wpdb->query("DROP TABLE IF EXISTS $thetable");
		
		//delete meta info		
		delete_option( 'usersultra_profile_fields' );
		delete_option( 'userultra_default_user_tabs' );
		delete_option( 'xoousersultra_my_account_page' );
		delete_option( 'xoousersultra_auto_page_creation' );
		delete_option( 'userultra_options' );		
		
		
	}
	
	function userultra_remove_admin_bar() 
	{
		if (!current_user_can('manage_options') && !is_admin())
		{
			
			if ($this->get_option('hide_admin_bar')==1) 
			{
				
				show_admin_bar(false);
			}
		}
	}
	
	function userultra_convert_date($date) 
	{
		
		$custom_date_format = $this->get_option('uultra_date_format');
			
		if ($custom_date_format) 
		{
			$date = date($custom_date_format, strtotime($date));
		}
		
		
		return $date;
	}
	
	public function get_logout_url ()
	{
		
		/*$defaults = array(
		            'redirect_to' => $this->current_page
		    );
		$args = wp_parse_args( $args, $defaults );
		
		extract( $args, EXTR_SKIP );*/
		
		$redirect_to = $this->current_page;
			
		return wp_logout_url($redirect_to);
	}
	
	
	public function custom_logout_page ($atts)
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		extract( shortcode_atts( array(	
			
			'redirect_to' => '', 		
							
			
		), $atts ) );
		
		
		/*$defaults = array(
		            'redirect_to' => $this->current_page
		    );
		$args = wp_parse_args( $args, $defaults );
		
		extract( $args, EXTR_SKIP );*/
		
		//check redir		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		if($redirect_to=="")
		{
				$redirect_to =$my_account_url;
		
		}
		$logout_url = wp_logout_url($redirect_to);
		
		//quick patch =
		
		$logout_url = str_replace("amp;","",$logout_url);
	
		wp_redirect($logout_url);
		exit;
		
	}
	
	public function get_redirection_link ($module)
	{
		$url ="";
		
		if($module=="profile")
		{
			//get profile url
			$url = $this->get_option('profile_page_id');			
		
		}
		
		return $url;
		
	}
	
		
	function uultra_redirect_block_site()
	{		
		
		if($this->get_option('uultra_block_whole_website') == 'yes')
        {
			$page_id = get_the_ID();
		
			if( ! is_user_logged_in() )
			{
				$login_page_id = $this->get_option('login_page_id');
				$my_login_url = get_permalink($login_page_id);
				
				$registration_page_id = $this->get_option('registration_page_id');
				$my_registration_url = get_permalink($registration_page_id);
				
				if($page_id!=$login_page_id  && $page_id!=$registration_page_id && $login_page_id!='' && $registration_page_id!='') //do redirect
				{
					wp_redirect( $my_registration_url );
					//echo "REgistratoin ID: " .$my_registration_url;
					exit();
				
				}		
				
			}
		
		}
	}
	
	
	/*Setup redirection*/
	public function xoousersultra_redirect() 
	{
		global $pagenow;

		/* Not admin */
		if (!current_user_can('administrator')) 
		{
			
		    $option_name = '';
			
			// Check if current page is profile page
			if('profile.php' == $pagenow)
			{
				// If user have selected to redirect backend profile page            
				if($this->get_option('redirect_backend_profile') == '1')
				{
					$option_name = 'profile_page_id';
				}
			}  
            

			// Check if current page is login or not
			if('wp-login.php' == $pagenow && !isset($_REQUEST['action']))
			{
				if($this->get_option('redirect_backend_login') == '1')
				{
					$option_name = 'login_page_id';
				}
			}

			if('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'register')
			{
				if($this->get_option('redirect_backend_registration') == '1')
				{
					$option_name = 'registration_page_id';
				}
			}
		
        
			if($option_name != '')
			{
				if($this->get_option($option_name) > 0)
				{
					// Generating page url based on stored ID
					$page_url = get_permalink($this->get_option($option_name));
					
					// Redirect if page is not blank
					if($page_url != '')
					{
						if($option_name == 'login_page_id' && isset($_GET['redirect_to']))
						{
							$url_data = parse_url($page_url);
							$join_code = '/?';
							if(isset($url_data['query']) && $url_data['query']!= '')
							{
								$join_code = '&';
							}
							
							$page_url= $page_url.$join_code.'redirect_to='.$_GET['redirect_to'];
						}			
						
						
						wp_redirect($page_url);
						exit;
					}
				}    
			}
		
		
		} //end if administrator
		

	}
	
	public function uultra_upgrade_to_media_confirm ()
	{
		global $wpdb;
		
		$html = '';
		$count_gal = 0;
		$count_photos = 0;		
		
		$path_pics = ABSPATH.$this->get_option('media_uploading_folder');
		
		//get all galleries		
		$sql = ' SELECT *  FROM ' . $wpdb->prefix . 'usersultra_galleries  ' ;		  
		$rows = $wpdb->get_results($sql);
		
		if ( !empty( $rows ) )
		{
			//create taxonomies			
			
			
			foreach ( $rows as $gallery ) //loop through all galleries
			{
				$count_gal++;
				$gal_id = $gallery->gallery_id;
				//let's create the gallery as post type				
				$new = array(
				  'post_title'    =>$gallery->gallery_name,
				  'post_type'     => 'gallery',
				  'post_name'     => $gallery->gallery_name,			 
				  'post_content'  => $gallery->gallery_desc,
				  'post_status'   => 'publish',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'menu_order' =>$gallery->gallery_order,
				  'post_author' => $gallery->gallery_user_id
				);
				
				$new_gallery_id = wp_insert_post( $new, FALSE );	
							
				//update gallery metadata							
				update_post_meta($new_gallery_id, 'gallery_private', 0);
				update_post_meta($new_gallery_id, 'gallery_only_friend', 0);			
				
				$html .= 'Gallery ID : '.$new_gallery_id.'--------- USER ID: '.$gallery->gallery_user_id.'<br>';
				
				//get all photos in gallery					
				$sql = ' SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '"  ' ;		  
				$rows_photos = $wpdb->get_results($sql);
				
				if ( !empty( $rows_photos ) )
				{
					foreach ( $rows_photos as $photo ) //loop through all photos
					{
						$count_photos++;
						// Check the type of tile. We'll use this as the 'post_mime_type'.
						$pathBig = $path_pics."/". $gallery->gallery_user_id."/". $photo->photo_large;
						
						$filetype = wp_check_filetype( $pathBig, null );
							
						$new_photo = array(
							  'post_title'    =>$photo->photo_name,
							  'post_type'     => 'attachment',
							  'post_parent'     => $new_gallery_id,
							  'post_mime_type' => $filetype['type'],
							  'post_name'     => $photo->photo_name,			 
							  'post_content'  => '',
							  'post_status'   => 'inherit',
							  'comment_status' => 'closed',
							  'ping_status' => 'closed',
							  'menu_order' =>$photo->photo_order,
							  'post_author' => $gallery->gallery_user_id
							);
							
							$photo_id = wp_insert_post( $new_photo, FALSE );								
							//update different metadata for this
							update_post_meta($photo_id, 'photo_large', $photo->photo_large);
							update_post_meta($photo_id, 'photo_thumb', $photo->photo_thumb);
							update_post_meta($photo_id, 'photo_mini', $photo->photo_mini);
							update_post_meta($photo_id, 'photo_main', $photo->photo_main);							
							
							$html .= 'Photo ID : '.$photo_id.'----- GALLERY ID: '.$new_gallery_id.'<br>';
							
							
							
							
					}
				
				
				}
				
					
				
					
			
			}
		
		}else{
			
			$total = 0;	
			
	    }
		
		$html .= 'Total Galleries: '.$count_gal.'----- <br>';
		$html .= 'Total Photos: '.$count_photos.'----- <br>';	
		
		
		//videos
		
		
		
		echo $html;
		die();
		
		
	}
	
	public function uultra_new_media_manager_check ()
	{
		$html = '';
		
		if (!get_option('uultra_new_media_feature_pro'))  // use the new media method 06-10-2014
		{				
			$html .= '<div class="user-ultra-sect ">';			
			$html .= '<h3>'.__('UPGRADE NOW!','users-ultra').'</h3>';
			$html .= '<div class="user-ultra-warning"><p>'.__('Users Ultra has new media features. <strong>Your attention</strong> is needed to <strong>upgrade</strong> and be able to use them!. ','users-ultra').'</p></div>';
			
			$html .= '<p>'.__(" We've modified the way Users Ultra handles the media files such as <strong>audio, video and photo</strong>. Now, they are being handled as post types which will make Users Ultra much more scalable and easy to integrate with other plugins. ",'users-ultra').'</p>';
		    
			$html .= '<p>'.__(" We <strong>highly recommend</strong> this upgrade so that you can start using the new amazing media features. ",'users-ultra').'</p>';      
			
			$html .= '	<p class="submit">
		<input type="submit" name="submit" id="uultra-btn-upgrade-features-btn" class="button button-primary " value="'.__('Start Upgrading Now','users-ultra').'"  />	
		   </p>
		   
		   <div id="uultra-sync-new-media-feature">
		   
		   </div> ';		   
		   $html .='</div>  ';              
       
    
		}
		
		$html = ''; //remove on production
		
		return $html;
	
	}
	
	
	
	public function create_initial_pages ()
	{
		$fresh_page_creation  = get_option( 'xoousersultra_auto_page_creation' );
		
		if($fresh_page_creation==1) //user wants to recreate pages
		{
			
			//create profile page
			$login_page_id  = $this->create_login_page();
			
			//create registration page		
			$login_page_id  = $this->create_register_page();
			
			//Create Main Page
			$main_page_id = $this->create_main_page();		
	
			//create profile page
			$profile_page_id  = $this->create_profile_page($main_page_id);	
			
			//directory page
			$directory_page_id  = $this->create_directory_page($main_page_id);	
			
			 //pages created
			 update_option('xoousersultra_auto_page_creation',0);
			 
			 
			$slug = $this->get_option("usersultra_slug"); // Profile Slug
			$slug_login = $this->get_option("usersultra_login_slug"); //Login Slug		
			$slug_registration = $this->get_option("usersultra_registration_slug"); //Registration Slug		
			$slug_my_account = $this->get_option("usersultra_my_account_slug"); //My Account Slug
			
			// this rule is used to display the registration page
			add_rewrite_rule("$slug/$slug_registration",'index.php?pagename='.$slug.'/'.$slug_registration, 'top');
					
			//this rules is for displaying the user's profiles
			add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[1]', 'top');
			
			//this rules is for displaying the user's profiles
			add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[2]', 'top');
			
			flush_rewrite_rules();
		
		}else{
			
			
				
			$slug = $this->get_option("usersultra_slug"); // Profile Slug
			$slug_login = $this->get_option("usersultra_login_slug"); //Login Slug		
			$slug_registration = $this->get_option("usersultra_registration_slug"); //Registration Slug		
			$slug_my_account = $this->get_option("usersultra_my_account_slug"); //My Account Slug
			
			// this rule is used to display the registration page
			add_rewrite_rule("$slug/$slug_registration",'index.php?pagename='.$slug.'/'.$slug_registration, 'top');	
				
			//this rules is for displaying the user's profiles
			add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[1]', 'top');
			
			//this rules is for displaying the user's profiles
			add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[2]', 'top');
		
		}
		
		//$this->create_rewrite_rules();
		
		/* Setup query variables */
		 add_filter( 'query_vars',   array(&$this, 'userultra_uid_query_var') );	
			
		
	
	}
	
	public function create_rewrite_rules() 
	{
		
		$slug = $this->get_option("usersultra_slug"); // Profile Slug
		$slug_login = $this->get_option("usersultra_login_slug"); //Login Slug		
		$slug_registration = $this->get_option("usersultra_registration_slug"); //Registration Slug		
		$slug_my_account = $this->get_option("usersultra_my_account_slug"); //My Account Slug
		
		// this rule is used to display the registration page
		add_rewrite_rule("$slug/$slug_registration",'index.php?pagename='.$slug.'/'.$slug_registration, 'top');		
		//this rules is for displaying the user's profiles
		add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[1]', 'top');
		
		//this rules is for displaying the user's profiles
		add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_username=$matches[2]', 'top');
		
		
		//this rules is for photos
		//add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&uu_photokeyword=$matches[1]', 'top');
		
		flush_rewrite_rules();
	
	
	}
	
	
	
	/*Create profile page */
	public function create_profile_page($parent) 
	{
		if (!$this->get_option('profile_page_id')) 
		{
			$slug = $this->get_option("usersultra_slug");
			
			$new = array(
			  'post_title'    => __('View Profile','users-ultra'),
			  'post_type'     => 'page',
			  'post_name'     => $slug,			 
			  'post_content'  => '[usersultra_profile]',
			  'post_status'   => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_author' => 1
			);
			$new_page = wp_insert_post( $new, FALSE );
			
			
			if (isset($new_page))
			{
				
			  $current_option = get_option('userultra_options');
			  $page_data = get_post($new_page);

			
				if(isset($page_data->guid))
				{
					//update settings
					$this->userultra_set_option('profile_page_id',$new_page);
					
				}
				
			}
		}
	}
	
	/*Create Directory page */
	public function create_directory_page($parent) 
	{
		if (!$this->get_option('directory_page_id')) 
		{
			$slug = $this->get_option("usersultra_directory_slug");
			
			$new = array(
			  'post_title'    => __('Members Directory','users-ultra'),
			  'post_type'     => 'page',
			  'post_name'     => $slug,			 
			  'post_content'  =>"[usersultra_searchbox filters='country,age' ]

[usersultra_directory list_per_page=8 optional_fields_to_display='friend,social,country,description' pic_boder_type='rounded']",
			  'post_status'   => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_author' => 1
			);
			$new_page = wp_insert_post( $new, FALSE );
			
			
			if (isset($new_page))
			{
				
			  $current_option = get_option('userultra_options');
			  $page_data = get_post($new_page);

			
				if(isset($page_data->guid))
				{
					//update settings
					$this->userultra_set_option('directory_page_id',$new_page);
					
				}
				
			}
		}
	}
	
	/*Create login page */
	public function create_login_page() 
	{
		if (!$this->get_option('login_page_id')) {
			
			
			$slug = $this->get_option("usersultra_login_slug");
			
			$new = array(
			  'post_title'    => __('Login','users-ultra'),
			  'post_type'     => 'page',
			  'post_name'     => $slug,
			 
			  'post_content'  => '[usersultra_login]',
			  'post_status'   => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_author' => 1
			);
			$new_page = wp_insert_post( $new, FALSE );
			
			
			if (isset($new_page))
			{
				$page_data = get_post($new_page);

				
				if(isset($page_data->guid))
				{
					//update settings
					$this->userultra_set_option('login_page_id',$new_page);
					
				}
				
			}
		}
	}
	
	/*Create register page */
	public function create_register_page() 
	{
		if (!$this->get_option('registration_page_id')) {
			
			//get slug
			$slug = $this->get_option("usersultra_registration_slug");
			
			$new = array(
			  'post_title'    => __('Sign up','users-ultra'),
			  'post_type'     => 'page',
			  'post_name'     => $slug,
			  			 
			  'post_content'  => '[usersultra_registration]',
			  'post_status'   => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_author' => 1
			);
			$new_page = wp_insert_post( $new, FALSE );
			
			
			if (isset($new_page))
			{
				$page_data = get_post($new_page);

				if(isset($page_data->guid))
				{
					//update settings
					$this->userultra_set_option('registration_page_id',$new_page);
					
				}
				
			}
		}
	}
	
	
	
	/*Create My Account Page */
	public function create_main_page() 
	{
		if (!get_option('xoousersultra_my_account_page')) 
		{
			//get slug
			$slug = $this->get_option("usersultra_my_account_slug");
			
				
			
			$new = array(
				  'post_title'    => __('My Account','users-ultra'),
				  'post_type'     => 'page',
				  'post_name'     => $slug,
				  'post_content'  => '[usersultra_my_account]',
				  'post_status'   => 'publish',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'post_author' => 1
				);
			
			$new_page = wp_insert_post( $new, FALSE );
			update_option('xoousersultra_my_account_page',$new_page);
		
		}else{
			
			$new_page=get_option('xoousersultra_my_account_page');
			
			
		
		}
		return $new_page;	
		
	}
	
	
	public function userultra_uid_query_var( $query_vars )
	{
		$query_vars[] = 'uu_username';
		$query_vars[] = 'searchuser';
		return $query_vars;
	}
	
	public function userultra_set_option($option, $newvalue)
	{
		$settings = get_option('userultra_options');
		$settings[$option] = $newvalue;
		update_option('userultra_options', $settings);
	}
	
	
	public function get_fname_by_userid($user_id) 
	{
		$f_name = get_user_meta($user_id, 'first_name', true);
		$l_name = get_user_meta($user_id, 'last_name', true);
		
		$f_name = str_replace(' ', '_', $f_name);
		$l_name = str_replace(' ', '_', $l_name);
		$name = $f_name . '-' . $l_name;
		return $name;
	}
	
	public function xoousers_create_standard_fields ()	
	{
		
		/* Allowed input types */
		$this->allowed_inputs = array(
			'text' => __('Text','users-ultra'),
			'fileupload' => __('Image Upload','users-ultra'),
			'textarea' => __('Textarea','users-ultra'),
			'select' => __('Select Dropdown','users-ultra'),
			'radio' => __('Radio','users-ultra'),
			'checkbox' => __('Checkbox','users-ultra'),
			'password' => __('Password','users-ultra'),
		  'datetime' => __('Date Picker','users-ultra')
		);
		
		/* Core registration fields */
		$set_pass = $this->get_option('set_password');
		if ($set_pass) 
		{
			$this->registration_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_login', 
				'name' => __('Username', 'users-ultra'),
				'required' => 1
			),
			100 => array( 
				'icon' => 'envelope', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','users-ultra'),
				'required' => 1,
				'can_hide' => 1,
			),
			150 => array( 
				'icon' => 'lock', 
				'field' => 'password', 
				'type' => 'usermeta', 
				'meta' => 'user_pass',
				'name' => __('Password','users-ultra'),
				'required' => 1,
				'can_hide' => 0,
				'help' => __('Password must be at least 7 characters long. To make it stronger, use upper and lower case letters, numbers and symbols.','users-ultra')
			),
			200 => array( 
				'icon' => 0, 
				'field' => 'password', 
				'type' => 'usermeta', 
				'meta' => 'user_pass_confirm', 
				'name' => __('Confirm Password','users-ultra'),
				'required' => 1,
				'can_hide' => 0,
				'help' => __('Type your password again.','users-ultra')
			),
			250 => array(
				'icon' => 0,
				'field' => 'password_indicator',
				'type' => 'usermeta'
			)
		);
		} else {
			
		$this->registration_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_login', 
				'name' => __('Username','users-ultra'),
				'required' => 1
			),
			100 => array( 
				'icon' => 'envelope', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','users-ultra'),
				'required' => 1,
				'can_hide' => 1,
				'help' => __('A password will be e-mailed to you.','users-ultra')
			)
		);
		}
		
		/* Core login fields */
		$this->login_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_login', 
				'name' => __('Username or Email','users-ultra'),
				'required' => 1
			),
			100 => array( 
				'icon' => 'lock', 
				'field' => 'password', 
				'type' => 'usermeta', 
				'meta' => 'login_user_pass', 
				'name' => __('Password','users-ultra'),
				'required' => 1
			)
		);
		
		/* These are the basic profile fields */
		$this->fields = array(
			80 => array( 
			  'position' => '50',
				'type' => 'separator', 
				'name' => __('Profile Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'deleted' => 0,
				'show_to_user_role' => 0
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'allow_html' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'allow_html' => 1,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'allow_html' => 1,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'allow_html' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
			),
			
			230 => array( 
			  'position' => '250',
				'type' => 'separator', 
				'name' => __('Contact Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 1,
				'deleted' => 0,
				'show_to_user_role' => 0
                
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
			),
			470 => array( 
			  'position' => '450',
				'type' => 'separator', 
				'name' => __('Social Profiles','users-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'show_in_widget' => 0,
				'deleted' => 0,
				'show_to_user_role' => 0
                
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
			),
			600 => array( 
			  'position' => '550',
				'type' => 'separator', 
				'name' => __('Account Info','users-ultra'),
				'private' => 0,
				'show_in_register' => 0,
				'show_in_widget' =>0,
				'deleted' => 0,
				'show_to_user_role' => 0
               
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
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
				'deleted' => 0,
				'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
				'help_text' => ''
			)
		);
		
		/* Store default profile fields for the first time */
		if (!get_option('usersultra_profile_fields'))
		{
			update_option('usersultra_profile_fields', $this->fields);
		}	
		
		
	}
	
	public function xoousers_update_field_value($option, $newvalue) 
	{
		$fields = get_option('usersultra_profile_fields');
		$fields[$option] = $newvalue;
		update_option('usersultra_profile_fields', $settings);
	
	}
	
	
	
	public function xoousers_load_textdomain() 
	{
		//load_plugin_textdomain( 'xoousers', false, xoousers_path.'/languages/');
    }
	
	function get_the_guid( $id = 0 )
	{
		$post = get_post($id);
		return apply_filters('get_the_guid', $post->guid);
	}
	   	
	function load_classes() 
	{	
		
		foreach ($this->classes_array as $key => $class) 
		{
			if (file_exists(xoousers_path."xooclasses/$class.php")) 
			{
				require_once(xoousers_path."xooclasses/$class.php");
						
					
			}
				
		}	
	}
	
	
	
	
	function uultra_my_theme_add_editor_styles( $mce_css ) 
	{
	  if ( !empty( $mce_css ) )
		$mce_css .= ',';
		$mce_css .=  xoousers_url.'templates/'.xoousers_template.'/css/editor-style.css';
		return $mce_css;
	  }
	  
	  
	  /* register admin scripts */
	public function add_styles_scripts()
	{	
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker' );
		
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');	
		
		/*Users JS*/		
		wp_register_script( 'uultra-front_js', xoousers_url.'js/uultra-front.js',array('jquery'),  null);
		wp_enqueue_script('uultra-front_js');
		
		wp_register_script( 'form-validate-lang', xoousers_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));
			
		wp_enqueue_script('form-validate-lang');			
		wp_register_script( 'form-validate', xoousers_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('form-validate');		
	}
	
	/* register styles */
	public function add_front_end_styles()
	{	
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker' );				

		/* Font Awesome */
		wp_register_style( 'xoouserultra_font_awesome', xoousers_url.'css/css/font-awesome.min.css');
		wp_enqueue_style('xoouserultra_font_awesome');
		
		//----MAIN STYLES
				
		/* Custom style */		
		wp_register_style( 'xoouserultra_style', xoousers_url.'templates/'.xoousers_template.'/css/default.css');
		wp_enqueue_style('xoouserultra_style');				
				
		/* Profile */
		wp_register_style( 'xoouserultra_profile_advance', xoousers_url.'templates/'.xoousers_template.'/css/style-profile.css');
		wp_enqueue_style('xoouserultra_profile_advance');
		
		
		//front end style		
		if (!is_admin()) 
		{		
			wp_register_style( 'xoouserultra_frontend_css', xoousers_url.'/templates/'.xoousers_template."/css/".'front-styles.css');
			wp_enqueue_style('xoouserultra_frontend_css');
			
		}
			
		
		//----END MAIN STYLES		
		
		
		/* Jquery UI style */		
		
		// Add the styles first, in the <head> (last parameter false, true = bottom of page!)
		wp_enqueue_style('qtip', xoousers_url.'js/qtip/jquery.qtip.min.css' , null, false, false);
		
		// Using imagesLoaded? Do this.
		wp_enqueue_script('imagesloaded',  xoousers_url.'js/qtip/imagesloaded.pkgd.min.js' , null, false, true);
		wp_enqueue_script('qtip',  xoousers_url.'js/qtip/jquery.qtip.min.js', array('jquery', 'imagesloaded'), false, true);	
			
		/*Expandible*/		
		wp_register_script( 'xoouserultra_expandible_js', xoousers_url.'js/expandible.js',array('jquery', 'jquery-ui-dialog'), null);
		wp_enqueue_script('xoouserultra_expandible_js');
		
		/*Users JS*/		
		wp_register_script( 'uultra-front_js', xoousers_url.'js/uultra-front.js',array('jquery'),  null);
		wp_enqueue_script('uultra-front_js');
		
		
		
		/*uploader*/			
		
		wp_enqueue_script('jquery-ui');	
		
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');		
		 
	
		if($this->get_option('disable_default_lightbox')!=1)
		{			
			//lightbox
			wp_register_style( 'xoouserultra_lightbox_css', xoousers_url.'js/lightbox/css/lightbox.css');
			wp_enqueue_style('xoouserultra_lightbox_css');			
			wp_register_script( 'xoouserultra_lightboxjs', xoousers_url.'js/lightbox/js/lightbox-2.6.min.js',array('jquery'));
			wp_enqueue_script('xoouserultra_lightboxjs');
		
		
		}
		
		
		/*Validation Engibne JS*/		
			
		wp_register_script( 'form-validate-lang', xoousers_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));
			
		wp_enqueue_script('form-validate-lang');			
		wp_register_script( 'form-validate', xoousers_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('form-validate');		
		
		$uult_date_format= $this->get_option('uultra_date_format');
		if($uult_date_format=='')
		{			
			$uult_date_format = 'mm/dd/yy';
		
		}
		
		$date_picker_array = array(
		            'closeText' => 'Done',
		            'prevText' => 'Prev',
		            'nextText' => 'Next',
		            'currentText' => 'Today',
		            'monthNames' => array(
		                        'Jan' => 'January',
    		                    'Feb' => 'February',
    		                    'Mar' => 'March',
    		                    'Apr' => 'April',
    		                    'May' => 'May',
    		                    'Jun' => 'June',
    		                    'Jul' => 'July',
    		                    'Aug' => 'August',
    		                    'Sep' => 'September',
    		                    'Oct' => 'October',
    		                    'Nov' => 'November',
    		                    'Dec' => 'December'
		                    ),
		            'monthNamesShort' => array(
		                        'Jan' => 'Jan',
    		                    'Feb' => 'Feb',
    		                    'Mar' => 'Mar',
    		                    'Apr' => 'Apr',
    		                    'May' => 'May',
    		                    'Jun' => 'Jun',
    		                    'Jul' => 'Jul',
    		                    'Aug' => 'Aug',
    		                    'Sep' => 'Sep',
    		                    'Oct' => 'Oct',
    		                    'Nov' => 'Nov',
    		                    'Dec' => 'Dec'
		                    ),
		            'dayNames' => array(
		                        'Sun' => 'Sunday',
    		                    'Mon' => 'Monday',
    		                    'Tue' => 'Tuesday',
    		                    'Wed' => 'Wednesday',
    		                    'Thu' => 'Thursday',
    		                    'Fri' => 'Friday',
    		                    'Sat' => 'Saturday'
		                    ),
		            'dayNamesShort' => array(
		                        'Sun' => 'Sun',
    		                    'Mon' => 'Mon',
    		                    'Tue' => 'Tue',
    		                    'Wed' => 'Wed',
    		                    'Thu' => 'Thu',
    		                    'Fri' => 'Fri',
    		                    'Sat' => 'Fri'
		                    ),
		            'dayNamesMin' => array(
		                        'Sun' => 'Su',
    		                    'Mon' => 'Mo',
    		                    'Tue' => 'Tu',
    		                    'Wed' => 'We',
    		                    'Thu' => 'Th',
    		                    'Fri' => 'Fr',
    		                    'Sat' => 'Sa'
		                    ),
		            'weekHeader' => 'Wk',
					 'dateFormat' => $uult_date_format
		        );				
				
				       
				
		wp_localize_script('xoouserultra_date_picker_js', 'XOOUSERULTRA', $date_picker_array);
		
		
		
	}
	
	/* Display Front End Directory*/
	public function show_users_directory( $atts ) 
	{						
		return $this->userpanel->show_users_directory($atts);	
		
	
	}
	
	/* Display Front End Mini Directory*/
	public function show_users_directory_mini( $atts ) 
	{						
		return $this->userpanel->show_users_directory_mini($atts);	
		
	
	}
	
	/* Custom WP Query*/
	public function get_results( $query ) 
	{
		$wp_user_query = new WP_User_Query($query);						
		return $wp_user_query;
		
	
	}
	
		/* Password Reset */
	public function password_reset( $args=array() ) {

		global $xoouserultra;
		
		// Increasing Counter for Shortcode number
		$this->login_code_count++;
		
		// Check if redirect to is not set and redirect to is availble in URL
		$default_redirect = $this->current_page;
		if(isset($_GET['redirect_to']) && $_GET['redirect_to']!='')
		    $default_redirect = $_GET['redirect_to'];
		
		/* Arguments */
		$defaults = array(
		        'use_in_sidebar' => null,
		        'redirect_to' => $default_redirect
		);		

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		// Default set to no captcha
		$this->captcha = 'no';
		
		
		
		$sidebar_class = null;
		if ($use_in_sidebar) $sidebar_class = 'xoouserultra-sidebar';
		
		$display = null;
		$display .= '<div class="xoouserultra-wrap xoouserultra-login '.$sidebar_class.'">
					<div class="xoouserultra-inner xoouserultra-login-wrapper">';
		
		$display .= '<div class="xoouserultra-head">';
		    $display .='<div class="xoouserultra-left">';
		        $display .='<div class="xoouserultra-field-name xoouserultra-field-name-wide login-heading" id="login-heading-'.$this->login_code_count.'">'.__('Password Reset','').'</div>';
		    $display .='</div>';
		    $display .='<div class="xoouserultra-right"></div><div class="xoouserultra-clear"></div>';
		$display .= '</div>';
						
						$display .='<div class="xoouserultra-main">';
						
						/*Display errors*/
						if (isset($_GET['resskey']) && $_GET['resskey']!="")
						{
							
							//check if valid 
							$valid = $xoouserultra->userpanel->get_user_with_key($_GET['resskey']);
							
							if($valid)
							{
								$display .= $this->show_password_reset_form( $sidebar_class,  $args, $_GET['resskey']);
							
							}else{
								
								$display .= '<p>'.__('Oops! The link is not correct! ', 'users-ultra').'</p>';
							
							
							}
							
							
						}
						
						
						
						

						$display .= '</div>
						
					</div>
				</div>';

		return $display;
		
	}
	
	/* Show login forms */
	public function show_password_reset_form( $sidebar_class=null, $args, $key) 
	{
		global $xoousers_login, $xoousers_captcha_loader;
		
		$display = null;		
		$display .= '<form action="" method="post" id="xoouserultra-passwordreset-form">';
		$display .= '<input type="hidden" class="xoouserultra-input" name="uultra_reset_key" id="uultra_reset_key" value="'.$key.'"/>';
		
		
		$meta="preset_password";
		$meta_2="preset_password_2";
		$placeholder = "";
		$login_btn_class = "";
		
		//field 1
		$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';		
		$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">'; 		 
		$display .= '<span>'.__('Type New Password:', 'users-ultra').'</span></label>';		
		$display .= '<div class="xoouserultra-field-value">';		
		$display .= '<input type="password" class="xoouserultra-input" name="'.$meta.'" id="'.$meta.'" value="" '.$placeholder.' />';		
		$display .= '</div>';		
		$display .= '</div><div class="xoouserultra-clear"></div>';
		
		//field 2
		$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';		
		$display .= '<label class="xoouserultra-field-type" for="'.$meta_2.'">'; 		 
		$display .= '<span>'.__('Re-type Password:', 'users-ultra').'</span></label>';		
		$display .= '<div class="xoouserultra-field-value">';		
		$display .= '<input type="password" class="xoouserultra-input" name="'.$meta_2.'" id="'.$meta_2.'" value="" '.$placeholder.' />';		
		$display .= '</div>';		
		$display .= '</div><div class="xoouserultra-clear"></div>';
		
		$display .= '<div class="xoouserultra-clear"></div>';
		
		
		$display .= '<input type="submit" name="xoouserultra-login" class="xoouserultra-button xoouserultra-reset-confirm'.$login_btn_class.'" value="'.__('Reset Password','users-ultra').'" id="xoouserultra-reset-confirm-pass-btn" />';
		
		$display .= '</br></br>';	
		
		$display.='<div class="xoouserultra-signin-noti-block" id="uultra-reset-p-noti-box"> </div>';	
		
		$display .= '</form>';		
		
		return $display;
	}

	/* Login Form on Front end */
	public function login( $args=array() ) {

		global $xoousers_login;
		
		// Increasing Counter for Shortcode number
		$this->login_code_count++;
		
		// Check if redirect to is not set and redirect to is availble in URL
		$default_redirect = $this->current_page;
		if(isset($_GET['redirect_to']) && $_GET['redirect_to']!='')
		    $default_redirect = $_GET['redirect_to'];
		
		/* Arguments */
		$defaults = array(
		        'use_in_sidebar' => null,
		        'redirect_to' => $default_redirect,
				'form_header_text' => __('Login','users-ultra'),
				'custom_text' => '',
				'custom_registration_link' => '',
				'disable_registration_link' => 'no'
		);		

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		// Default set to no captcha
		$this->captcha = 'no';
		
		if(isset($captcha))
		    $this->captcha = $captcha;

		
		$sidebar_class = null;
		if ($use_in_sidebar) $sidebar_class = 'xoouserultra-sidebar';
		
		$display = null;
		$display .= '<div class="xoouserultra-wrap xoouserultra-login '.$sidebar_class.'">
					<div class="xoouserultra-inner xoouserultra-login-wrapper">';
		
		$display .= '<div class="xoouserultra-head">';
		    $display .='<div class="xoouserultra-left">';
		        $display .='<div class="xoouserultra-field-name xoouserultra-field-name-wide login-heading" id="login-heading-'.$this->login_code_count.'">'.$form_header_text.'</div>';
		    $display .='</div>';
		    $display .='<div class="xoouserultra-right"></div><div class="xoouserultra-clear"></div>';
		$display .= '</div>';
						
						$display .='<div class="xoouserultra-main">';
						
						$display .=  $custom_text;
						
						/*Display errors*/
						if (isset($_POST['xoouserultra-login']))
						{
							$display .= $this->login->get_errors();
						}
						
						$display .= $this->show_login_form( $sidebar_class, $redirect_to , $args);

						$display .= '</div>
						
					</div>
				</div>';

		return $display;
		
	}
	
	/* Show login forms */
	public function show_login_form( $sidebar_class=null, $redirect_to=null, $args) 
	{
		global $xoousers_login, $xoousers_captcha_loader;
		
		
		$atts = $args;
		extract( shortcode_atts( array(
			'custom_registration_link' => '',
			'disable_registration_link' => 'no'		
			
		), $atts ) );
		
		$display = null;		
		$display .= '<form action="" method="post" id="xoouserultra-login-form-'.$this->login_code_count.'">';
		
		
		//get social sign up methods
		$display .='<div class="uultra-pro-socialsigunuptions">';
		$display .= $this->get_social_buttons(__("Sign in with ",'xoousers' ),$args);
		$display .='</div>';
		
		$display .='<h2 class="uultra-forms-header">'.__("Sign in with ",'xoousers' ).'</h2>';	

		foreach($this->login_fields as $key=>$field) 
		{
			extract($field);
			
			if ( $type == 'usermeta') {
				
				$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				
				
				/* Show the label */
				$placeholder = '';
				$icon_name = '';
				$input_ele_class='';
				
				    if (isset($this->login_fields[$key]['name']) && $name) 
					{
				        $display .= '<label class="xoouserultra-field-type" for="'.$meta.'">'; 
						
						//icon
						if (isset($this->login_fields[$key]['icon']) && $icon) 
						{
							$display .= '<i class="fa fa-'.$icon.'"></i>';
						} else {
							$display .= '<i class="fa fa-none"></i>';
						}
					
						     
				        $display .= '<span>'.$name.'</span></label>';
				    
					} else {
						
				        $display .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				    } 
								
				
				
				$display .= '<div class="xoouserultra-field-value">';
					
				$display .=$icon_name;
				
					switch($field) {
						case 'textarea':
							$display .= '<textarea class="xoouserultra-input'.$input_ele_class.'" name="'.$meta.'" id="'.$meta.'" '.$placeholder.'>'.$this->get_post_value($meta).'</textarea>';
							break;
						case 'text':
							$display .= '<input type="text" class="xoouserultra-input'.$input_ele_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'" '.$placeholder.' />';
							
							if (isset($this->login_fields[$key]['help']) && $help != '') {
								$display .= '<div class="xoouserultra-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							}
							
							break;
						case 'password':
							$display .= '<input type="password" class="xoouserultra-input'.$input_ele_class.'" name="'.$meta.'" id="'.$meta.'" value="" '.$placeholder.' />';
							break;
					}
					
					if ($field == 'password') {
						
					}
					
					
					
				$display .= '</div>';

				$display .= '</div><div class="xoouserultra-clear"></div>';
			}
						
		}		
		
		
		$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show" style="clear:both; float:none">
						<label class="xoouserultra-field-type xoouserultra-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="xoouserultra-field-value">';
		
		if (isset($_POST['rememberme']) && $_POST['rememberme'] == 1)
		 {
		    $class = 'xoouserultra-icon-check';
		} else {
			
			 $class = 'xoouserultra-icon-check-empty';
		}
		
		
		// this is the Forgot Pass Link
		$forgot_pass = '<a href="#uultra-forgot-link" id="xoouserultra-forgot-pass-'.$this->login_code_count.'" class="xoouserultra-login-forgot-link" title="'.__('Forgot Password?','users-ultra').'">'.__('Forgot Password?','users-ultra').'</a>';
		
		// this is the Register Link
		$register_link = site_url('/wp-login.php?action=register');
		
		if ($this->get_option('register_redirect') != '') 
		    $register_link =  $this->get_option('register_redirect');
			
		$register_link_url='';
		if($disable_registration_link!='yes')
		{			
			$register_link_url = ' | <a href="'.$register_link.'" class="xoouserultra-login-register-link">'.__('Register','users-ultra').'</a>';
		}
		
		if($disable_registration_link!='yes' && $custom_registration_link!='')
		{			
			$register_link_url = ' | <a href="'.$custom_registration_link.'" class="xoouserultra-login-register-link">'.__('Register','users-ultra').'</a>';
		}
    		
		$remember_me_class = '';
		$login_btn_class = '';
		
		if($sidebar_class != null)
		{
		    $login_btn_class = ' in_sidebar';
		    $remember_me_class = ' in_sidebar_remember';
		}
		    
		
		$display .= '<div class="xoouserultra-rememberme'.$remember_me_class.'">
		
		<input type="checkbox" name="rememberme-1" id="rememberme_'.$this->login_code_count.'" value="0" /> <label for="rememberme_'.$this->login_code_count.'"><span></span>'.__('Remember me','users-ultra').'</label>
		
		</div>
		
		
		
		<input type="submit" name="xoouserultra-login" class="xoouserultra-button xoouserultra-login'.$login_btn_class.'" value="'.__('Log In','users-ultra').'" /><br />'.$forgot_pass.$register_link_url;
		
		
		$display .= ' </div>
					</div><div class="xoouserultra-clear"></div>';
		
		$display .= '<input type="hidden" name="redirect_to" value="'.$redirect_to.'" />';
		
		$display .= '</form>';
		
		
		
		
		// this is the forgot password form
		$forgot_pass = '';
		
		$forgot_pass .= '<div class="xoouserultra-forgot-pass" id="xoouserultra-forgot-pass-holder">';
		
		
		$forgot_pass .= "<div class='notimessage'>";
		
		$forgot_pass .= "<div class='uupublic-ultra-warning'>".__(" A quick access link will be sent to your email that will let you get in your account and change your password. ", 'users-ultra')."</div>";
		
		$forgot_pass .= "</div>";
		
		
		$forgot_pass .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		
		
		
		$forgot_pass .= '<label class="xoouserultra-field-type" for="user_name_email-'.$this->login_code_count.'"><i class="fa fa-user"></i><span>'.__('Username or Email','users-ultra').'</span></label>';
		$forgot_pass .= '<div class="xoouserultra-field-value">';
		
		
		$forgot_pass .= '<input type="text" class="xoouserultra-input" name="user_name_email" id="user_name_email" value=""></div>';
		$forgot_pass .= '</div>';
		    
		$forgot_pass.='<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
		$forgot_pass.='<label class="xoouserultra-field-type xoouserultra-blank-lable">&nbsp;</label>';
		$forgot_pass.='<div class="xoouserultra-field-value">';
		$forgot_pass.='<div class="xoouserultra-back-to-login">';
		$forgot_pass.='<a href="javascript:void(0);" title="'.__('Back to Login','users-ultra').'" id="xoouserultra-back-to-login-'.$this->login_code_count.'" class="xoouserultra-login-forgot-link-close">'.__('Back to Login','users-ultra').'</a> ';
		
		$forgot_pass.='</div>';
		
	
		            
		            $forgot_pass.='<input type="button" name="xoouserultra-forgot-pass" id="xoouserultra-forgot-pass-btn-confirm" class="xoouserultra-button xoouserultra-login" value="'.__('Send me Password','users-ultra').'">';
					
					$forgot_pass.='<div class="xoouserultra-signin-noti-block" id="uultra-signin-ajax-noti-box"> ';
		
		$forgot_pass.='</div>';
		            
		        $forgot_pass.='</div>';
				
					
		
		
		    $forgot_pass.='</div>';
		    
		    
		    
		$forgot_pass .= '</div>';	
		
		$display.=$forgot_pass;
		
		
		return $display;
	}
	
	/* Show registration form */
	function show_registration_form( $args=array() )
	{

		global $post, $xoousers_register, $uupro20_recaptcha;		
		
		// Loading scripts and styles only when required
		 /* Tipsy script */
        if (!wp_script_is('uultra_tipsy')) {
            wp_register_script('uultra_tipsy', xoousers_url . 'js/jquery.tipsy.js', array('jquery'));
            wp_enqueue_script('uultra_tipsy');
        }

        /* Tipsy css */
        if (!wp_style_is('uultra_tipsy')) {
            wp_register_style('uultra_tipsy', xoousers_url . 'css/tipsy.css');
            wp_enqueue_style('uultra_tipsy');
        }
		
		/* Password Stregth Checker Script */
		if(!wp_script_is('form-validate'))
		{			
			
		    
        $validate_strings = array(
            'ajaxurl'  => admin_url( 'admin-ajax.php' ),
            'ErrMsg'   => array(
                        'similartousername' => __('Your password is too similar to your username.','users-ultra'),
                        'mismatch' => __('Both passwords do not match.','users-ultra'),
                        'tooshort' => __('Your password is too short.','users-ultra'),
                        'veryweak' => __('Your password strength is too weak.','users-ultra'),
                        'weak' => __('Your password strength weak.','users-ultra'),
                        'usernamerequired' => __('Please provide username.','users-ultra'),
                        'emailrequired' => __('Please provide email address.','users-ultra'),
                        'validemailrequired' => __('Please provide valid email address.','users-ultra'),
                        'usernameexists' => __('That username is already taken, please try a different one.','users-ultra'),
                        'emailexists' => __('The email you entered is already registered. Please try a new email or log in to your existing account.','users-ultra')
                    ),
            'MeterMsg' => array(
                        'similartousername' => __('Your password is too similar to your username.','users-ultra'),
                        'mismatch' => __('Both passwords do not match.','users-ultra'),
                        'tooshort' => __('Your password is too short.','users-ultra'),
                        'veryweak' => __('Your password strength is too weak.','users-ultra'),
                        'weak' => __('Your password strength weak.','users-ultra'),
                        'good' => __('Good','users-ultra'),
                        'strong' => __('Strong','users-ultra')
                    ),
            'Err'     => __('ERROR','users-ultra')
        );

        wp_localize_script( 'form-validate', 'Validate', $validate_strings );
		}
		

		
		
		/* Arguments */
		$defaults = array(
        'use_in_sidebar' => null,
        'redirect_to' => null,
		'form_header_text' => __('Sign Up','users-ultra'),
		'custom_text' => '',
		'disable_social' => '',
		'custom_form' => null	
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		$pic_class = 'xoouserultra-pic';
		if(is_safari())
		    $pic_class = 'xoouserultra-pic safari';
		
		// Default set to blank
		$this->captcha = '';
		$captpcha_status = $this->get_option("captcha_plugin");
		
		if($captpcha_status!="")
		{
		    $this->captcha = $captpcha_status;
		}	
		
		
		$sidebar_class = null;
		if ($use_in_sidebar) $sidebar_class = 'xoouserultra-sidebar';
		
		$display = null;
		
		
		if(get_option('users_can_register') == '1')
		{
		
		    $display .= '<div class="xoouserultra-wrap xoouserultra-registration '.$sidebar_class.'">
					<div class="xoouserultra-inner">
						
						<div class="xoouserultra-head">
							
							<div class="xoouserultra-left">
								<div class="'.$pic_class.'">';
								
								if (isset($_POST['xoouserultra-register']) && $_POST['user_email'] != '' ) {
									//$display .= $this->pic($_POST['user_email'], 50);
								} else {
									//$display .= $this->pic('john@doe.com', 50);
								}
								
								$display .= '</div>';
								
								$display .= '<div class="xoouserultra-name">
								
												<div class="xoouserultra-field-name xoouserultra-field-name-wide">';
												
												
								$display .=  $form_header_text;
											
												
								$display .= '</div>';
													
												
								$display .= '</div>';
								
							$display .= '</div>';
							
							
							$display .= '<div class="xoouserultra-right">';								
							$display .= '</div><div class="xoouserultra-clear"></div>
							
						</div>
						
						<div class="xoouserultra-main">';
						
						
						$display .=  $custom_text;
							
						$display .='<div class="xoouserultra-errors" style="display:none;" id="pass_err_holder">
							    <span class="xoouserultra-error xoouserultra-error-block" id="pass_err_block">
							        <i class="xoouserultra-icon-remove"></i><strong>ERROR:</strong>'.__(' Please enter a username.', 'users-ultra').'
							    </span>
							</div>
							';
							
						/*Display errors*/
						if (isset($_POST['xoouserultra-register-form'])) 
						{
							$display .= $this->register->get_errors();
						}
						
						$display .= $this->display_the_registeration_form( $sidebar_class, $redirect_to, $args, $custom_form );

						$display .= '</div>
						
					</div>
				</div>';
		}else{
			
			//the registration is disabled
			
		    $display .= '<div class="xoouserultra-wrap xoouserultra-registration '.$sidebar_class.'"><div class="xoouserultra-inner"><div class="xoouserultra-head">';
		    if($this->get_option('html_registration_disabled') != '')
			{
				
		        $display.=$this->get_option('html_registration_disabled');
				
			}else{
				
		        $display.=__('User registration is currently not allowed.','users-ultra');
			
			}
		    $display .= '</div></div></div>';
		}
		
		return $display;
		
	}
	
	/* This is the Registration Form */
	function display_the_registeration_form( $sidebar_class=null, $redirect_to=null , $args, $custom_form)
	{
		global $xoousers_register, $predefined, $uupro20_recaptcha;
		$display = null;
		
		//check if retype-email
		$email_retype = $this->get_option("set_email_retype");
		
				
		// Optimized condition and added strict conditions
		if (!isset($xoousers_register->registered) || $xoousers_register->registered != 1)
		{
		
		$display .= '<form action="" method="post" id="xoouserultra-registration-form" enctype="multipart/form-data">';
		
		$display .= '<input type="hidden" name="uultra-custom-form-id"  id="uultra-custom-form-id" value="'.$custom_form.'">';	
            
       	$display .= wp_nonce_field('uultra_reg_action', 'uultra_csrf_token',true,false);	
		
		
		if ($args['disable_social'] != 'yes')
		{
            if($this->check_is_pro_version()){
		
                //get social sign up methods
                $display .='<div class="uultra-pro-socialsigunuptions">';
                $display .= $this->get_social_buttons(__("Sign up with",'users-ultra'), $args);	
                $display .='</div>';
                
            }
		
		}
		
		$display .= '<div class="xoouserultra-field xoouserultra-seperator-requiredfields xoouserultra-edit xoouserultra-edit-show">'.__('Fields with (*) are required','users-ultra').'</div>';	
		
		$display .= '<div class="xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.__('Account Info','users-ultra').'</div>';
			
		/* These are the basic registrations fields */
		
		foreach($this->registration_fields as $key=>$field) 
		{
			extract($field);
			
			//check if exclude user from registration.
			
			$include_username =  true;
			
			if($this->get_option('allow_registering_only_with_email')=='yes')
			{
				if($meta=='user_login')
				{
					$include_username =  false;
				
				}
			
			}
			
			
			
			if ( $type == 'usermeta' && $include_username) {
				
				$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				if(!isset($required))
				    $required = 0;
				
				$required_class = '';				
				$required_text = '';
				
				if($required == 1 && in_array($field, $this->include_for_validation))
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
				}
				
				/* Show the label */
				if (isset($this->registration_fields[$key]['name']) && $name) 
				{
					$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';
					
					if (isset($this->registration_fields[$key]['icon']) && $icon)
					 {
						$display .= '<i class="fa fa-'.$icon.'"></i>';
					} else {
						$display .= '<i class="fa fa-none"></i>';
					}
					
					$display .= '<span>'.$name.' '.$required_text.'</span></label>';
					
				} else {
					$display .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				}
				
				
				
				$display .= '<div class="xoouserultra-field-value">';				
				
					
					switch($field) {					
						
						case 'textarea':
							$display .= '<textarea placeholder="'.$name.'" class="'.$required_class.' xoouserultra-input xoouserultra-input-text-area" name="'.$meta.'" id="reg_'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'">'.$this->get_post_value($meta).'</textarea>';
							break;
						
						case 'text':
							$display .= '<input type="text" placeholder="'.$name.'" class="'.$required_class.' xoouserultra-input" name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'"/>';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								$display .= '<div class="xoouserultra-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							}
							
							break;
							
							case 'datetime':						
							
							    
								
							    
							    $display .= '<input type="text" class="'.$required_class.' xoouserultra-input xoouserultra-datepicker" name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'"/>';						
								
							    
							    if (isset($this->registration_fields[$key]['help']) && $help != '') {
							        $display .= '<div class="xoouserultra-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							    }
								
								
								
							    break;							
					   
							
						case 'password':

							$display .= '<input type="password" class="'.$required_class.' xoouserultra-input password" name="'.$meta.'" id="reg_'.$meta.'" value="" autocomplete="off" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'" />';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								$display .= '<div class="xoouserultra-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							}

							break;												
							
							
						case 'password_indicator':
							$display .= '<div class="password-meter"><div class="password-meter-message" id="password-meter-message">&nbsp;</div></div>';
							break;
							
					}
					
									
					
					
					
				$display .= '</div>';
				$display .= '</div><div class="xoouserultra-clear"></div>';
				
				
				//re-type password
				
				if($meta=='user_email' && $email_retype!='no')
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
					
					$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
					
									
					$display .= '<label class="xoouserultra-field-type" for="user_email_2">';
					$display .= '<i class="fa fa-envelope"></i>';	
					$display .= '<span>'.__('Re-type your email', 'users-ultra').' '.$required_text.'</span></label>';
					
					$display .= '<div class="xoouserultra-field-value">';
				
					$display .= '<input placeholder="'.__('Re-type your email', 'users-ultra').'" type="text" class="'.$required_class.' xoouserultra-input" name="user_email_2" id="reg_user_email_2" value="'.$this->get_post_value('user_email_2').'" title="Re-type your email." data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'"/>';
					
					
					$display .= '</div>';
					$display .= '</div><div class="xoouserultra-clear"></div>';
				
				}
				
				
			}
			
								
		}
		
		
         
		// $custom_form= '';
		
		/* Get end of array */
			
		if($custom_form!="" || isset($_GET["form_id"] ))
		{
			//do we have a pre-set value in the get?			
			if(isset($_GET["form_id"] ) && $_GET["form_id"] !="")
			{
				$custom_form =$_GET["form_id"];			
			}
			
			$custom_form = 'usersultra_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$array = get_option('usersultra_profile_fields');
			$fields_set_to_update ='usersultra_profile_fields';
		
		}
		
		if(!is_array($array)) {$array= array();}
		

		foreach($array as $key=>$field) 
		{
		     
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
		    
			if (isset($array[$array_end]['type']) && $array[$array_end]['type'] == 'seperator') 
			{
				if(isset($array[$array_end]))
				{
					unset($array[$array_end]);
				}
			}
		}
		
		
		/*Display custom profile fields added by the user*/		
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
			$required_text = '';
			//if($required == 1 && in_array($field, $this->include_for_validation))
			if($array[$key]['required'] == 1 && in_array($field, $this->include_for_validation))
			{				
			    $required_class = 'validate[required] ';
				$required_text = '(*)';				
			}
			
			
			/* This is a Fieldset seperator */
						
			/* separator */
            if ($type == 'separator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
                   $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$name.'</div>';
				   
            }
			
						
			if ($type == 'seperator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
                   $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$name.'</div>';
				   
            }
			
			//check if display emtpy
				
				
			if ($type == 'usermeta' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
								
				$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) 
					{
						
                            $display .= '<i class="fa fa-' . $icon . '"></i>';
                    } else {
                            $display .= '<i class="fa fa-icon-none"></i>';
                    }
					
					
											
					$tooltipip_class = '';					
					if (isset($array[$key]['tooltip']) && $tooltip)
					{
						$qtip_classes = 'qtip-light ';	
						$qtip_style = '';					
					
						 $tooltipip_class = '<a class="'.$qtip_classes.' uultra-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
					} 
					
											
					$display .= '<span>'.$name. ' '.$required_text.' '.$tooltipip_class.'</span></label>';
					
					
				} else {
					$display .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				}
				
				$display .= '<div class="xoouserultra-field-value">';
					
					switch($field) {
					
						case 'textarea':
							$display .= '<textarea placeholder="'.$name.'" class="'.$required_class.' xoouserultra-input " rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'">'.$this->get_post_value($meta).'</textarea>';
							break;
							
						case 'text':
							$display .= '<input type="text" placeholder="'.$name.'" class="'.$required_class.'xoouserultra-input"  name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'"/>';
							break;							
							
						case 'datetime':
						
						
								$uult_date_format= $this->get_option('uultra_date_format');
								if($uult_date_format=='' || $uult_date_format=='m/d/Y' )
								{			
									$uult_date_format = 'mm/dd/yy';
								
								}
						
						    $display .= '<input type="text" class="'.$required_class.' xoouserultra-input xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
							
							$display .= '<input type="hidden" name="uultra_date_format" id="uultra_date_format" value="'.$uult_date_format.'" />';
							
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $this->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
															
								$loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								 	
								
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="'.$required_class.' xoouserultra-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'">';
								
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
						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] radio ";
							}
						
							if (isset($array[$key]['choices']))
							{				
													
								
								 $loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								
							}
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim(stripslashes($option));
									$display .= '<input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="uultra_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> <label for="uultra_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label>';
									
									$counter++;
									
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] checkbox ";
							}						
						
							if (isset($array[$key]['choices'])) 
							{
																
								 $loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								
								
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim(stripslashes($option));
								  
								  $display .= '<div class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="uultra_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> <label for="uultra_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> </div>';
									
									
									$counter++;
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						
						case 'fileupload':
						
						    if ($meta == 'user_pic')
							{
								
									
								$display .= '<input type="file" class="'.$required_class.'xoouserultra-input uultra-fileupload-field"  name="'.$meta.'" style="display:block;" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','users-ultra').'"/>';
							
							} //end if meta

							break;
							
						case 'password':
						
							$display .= '<input type="password" class="xoouserultra-input'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'" />';
							
							if ($meta == 'user_pass') 
							{
								
							$display .= '<div class="xoouserultra-help">'.__('If you would like to change the password type a new one. Otherwise leave this blank.','users-ultra').'</div>';
							
							} elseif ($meta == 'user_pass_confirm') {
								
							$display .= '<div class="xoouserultra-help">'.__('Type your new password again.','users-ultra').'</div>';
							
							}
							break;
							
					}
					
					
					if (isset($array[$key]['help_text']) && $help_text != '') 
					{
						$display .= '<div class="xoouserultra-help">'.$help_text.'</div><div class="xoouserultra-clear"></div>';
					}
							
					
					/*User can hide this from public*/
					if (isset($array[$key]['can_hide']) && $can_hide == 1)
					{
						
						$display .= '<div class="xoouserultra-hide-from-public">
										<input type="checkbox" name="hide_'.$meta.'" id="hide_'.$meta.'" value="" /> <label for="hide_'.$meta.'"><span></span>'.__('Hide from Public','users-ultra').'</label>
									</div>';

					} elseif ($can_hide == 0 && $private == 0) {
					   
					}
					
									
					
				$display .= '</div>';
				$display .= '</div><div class="xoouserultra-clear"></div>';
			}
		}
		
	
		
		/*Roles*/
        if($this->check_is_pro_version()){
            
            if($this->get_option('uultra_roles_actives_registration')=='yes')
            {
                //text to display
                $label_for_role = $this->get_option('label_for_registration_user_role');
                $label_for_role_1 = $this->get_option('label_for_registration_user_role_1');

                if($label_for_role =="")
                {
                    $label_for_role = __('Select your Role','users-ultra');			
                }

                if($label_for_role_1 =="")
                {
                    $label_for_role_1 = __('Role','users-ultra');

                }				

                //
                 $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$label_for_role.'</div>';

                $display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
                $display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
                $display .= '<span>'.$label_for_role_1.' '.$required_text.'</span></label>';
                $display .= '<div class="xoouserultra-field-value">';
                $display .= $this->role->get_public_roles_registration();	
                $display .= '</div>';

                $display .= '</div>';

                $display .= '<div class="xoouserultra-clear"></div>';


            } //end role
            
        }
            
            
		
		if($this->check_is_pro_version()){
            
            /*If we are using Paid Registration*/		
            if($this->get_option('registration_rules')==4)
            {


                $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.__('Payment Information','users-ultra').'</div>';


                $display .= '<div class="xoouserultra-package-list">';			
                $display .= $this->paypal->get_packages( $custom_form);			
                $display .= '</div>';

                //payment methods list
                $display .= '<div class="uultra-payment-options-div" id="uultra-payment-options-div">';


                $required_class = ' validate[required]';

                //payment methods			
                 $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.__('Select Payment Method', 'users-ultra').'</div>';

                /*Paypal*/		
                if($this->get_option('gateway_paypal_active')=='1')
                {
                    $paypal_logo = xoousers_url.'admin/images/paypal-logo.jpg';
                    $display_payment_method = '<input type="radio" class="'.$required_class.'" title="" name="uultra_payment_method" id="uultra_payment_method_paypal" value="paypal" /> <label for="uultra_payment_method_paypal"><span></span><img align="absmiddle"  src="'.$paypal_logo.'"  style="top:-5px; position:absolute; width:100px; max-width:100px"></label>';



                    $display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
                    $display .= '<label class="xoouserultra-field-type" for="uultra_payment_method_paypal">';			
                    $display .= '<span>'.$display_payment_method.' </span></label>';
                    $display .= '<div class="xoouserultra-field-value">';
                    //$display .= $this->role->get_public_roles_registration();	
                    $display .= '</div>';				
                    $display .= '</div>';				
                    $display .= '<div class="xoouserultra-clear"></div>';


                }
            
            }
			
			/*Bank*/		
			if($this->get_option('gateway_bank_active')=='1')
			{
				//custom label
				
				$custmom_label = $this->get_option('gateway_bank_label');
				if($custmom_label=='')
				{
					$custmom_label = __('Bank','users-ultra');
				
				}
				
				$display_payment_method = '<input type="radio" class="'.$required_class.'" title="" name="uultra_payment_method" id="uultra_payment_method_bank" value="bank" /> <label for="uultra_payment_method_bank"><span></span>'.$custmom_label.'</label>';
													 
				$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				$display .= '<label class="xoouserultra-field-type" for="uultra_payment_method_bank">';			
				$display .= '<span>'.$display_payment_method.' </span></label>';
				$display .= '<div class="xoouserultra-field-value">';
				$display .= '</div>';				
				$display .= '</div>';				
				$display .= '<div class="xoouserultra-clear"></div>';
			
			
			}
			
			
			//$display .= '</div>';
		
		} //end if bank
		
		
		if($this->check_is_pro_version()){		
            /*If mailchimp*/		
            if($this->get_option('mailchimp_active')==1 && $this->get_option('mailchimp_api')!="")
            {

                //new mailchimp field			
                $mailchimp_text = stripslashes($this->get_option('mailchimp_text'));
                $mailchimp_header_text = stripslashes($this->get_option('mailchimp_header_text'));

                if($mailchimp_header_text==''){

                    $mailchimp_header_text = __('Receive Daily Updates ', 'users-ultra');				
                }			


                //

                $mailchimp_autchecked = $this->get_option('mailchimp_auto_checked');

                $mailchimp_auto = '';
                if($mailchimp_autchecked==1){

                    $mailchimp_auto = 'checked="checked"';				
                }

                 $display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$mailchimp_header_text.'</div>';

                $display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
                $display .= '<div class="xoouserultra-clear"></div>'; 

                $display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
                $display .= '<span>&nbsp;</span></label>';
                //$display .= '</label>';
                $display .= '<div class="xoouserultra-field-value">';
                $display .= '<input type="checkbox"  title="'.$mailchimp_header_text.'" name="uultra-mailchimp-confirmation"  id="uultra-mailchimp-confirmation" value="1"  '.$mailchimp_auto.' > <label for="uultra-mailchimp-confirmation"><span></span>'.$mailchimp_text.'</label></div>' ;

                $display .= '<div class="xoouserultra-clear"></div>';	


            }
            
        } //end if mailchimp
		
		//terms and conditions				
		if($this->get_option('uultra_terms_and_conditions')=='yes')
		{
			$display.= $this->uultra_get_terms_and_conditions();		
		}
			
		
				
		
		//recaptcha			
		if(isset($uupro20_recaptcha) && $this->get_option('recaptcha_site_key')!='' && $this->get_option('recaptcha_secret_key')!='' && $this->get_option('recaptcha_display_registration')=='1'){	
		
			$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
			$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">&nbsp;</label>';
			$display .= '<div class="xoouserultra-field-value">';
			$display .= $uupro20_recaptcha->recaptcha_field();
			$display .= '</div>';
			
			$display .= '</div>';
					
		}
		
		
		$display .= '<div class="xoouserultra-clear">&nbsp;</div>';
		
		
		$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">
						<label class="xoouserultra-field-type xoouserultra-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="xoouserultra-field-value">
						    <input type="hidden" name="xoouserultra-register-form" value="xoouserultra-register-form" />
							<input type="submit" name="xoouserultra-register" id="xoouserultra-register-btn" class="xoouserultra-button" value="'.__('Register','users-ultra').'" />
						</div>
					</div>
					
					<div class="xoouserultra-clear"></div>';
					
					
		if ($redirect_to != '' )
		{
			$display .= '<input type="hidden" name="redirect_to" value="'.$redirect_to.'" />';
		}
		
		
		
		} 
        
        
        $display .= '</form>';
		
		
		return $display;
	}
	
	public function uultra_get_terms_and_conditions()
	{
		
		$display = '';
		
				
		$text_terms = stripslashes($this->get_option('uultra_terms_and_conditions_text'));
		$text_terms_large = stripslashes(nl2br($this->get_option('uultra_terms_and_conditions_text_large')));	
		
		$text_terms_1_mandatory = stripslashes($this->get_option('uultra_terms_and_conditions_mandatory_1'));		
		$required = '';		
		if($text_terms_1_mandatory=='yes' || $text_terms_1_mandatory==''){$required = 'validate[required]';}
			
			//
		$display .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.__('Terms & Conditions ', 'users-ultra').'</div>';
		 
		$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
			$display .= '<div class="xoouserultra-clear"></div>'; 
			
			$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
			$display .= '<span>&nbsp;</span></label>';
		
			$display .= '<div class="xoouserultra-field-value">';
			$display .= '<input type="checkbox"  title="'.__('Terms & Conditions ', 'users-ultra').'" name="uultra-terms-and-conditions-confirmation"  id="uultra-terms-and-conditions-confirmation" value="1" class="'.$required.'" > <label for="uultra-terms-and-conditions-confirmation"><span></span>'.$text_terms.'</label><br>';
			
		
		$display .= '<div class="uuultra-terms-scrollable-box">'.$text_terms_large .'</div>';
			
		
		$display .= '</div>';			
		
		$display .= '</div>';
		
		$display .= '<div class="xoouserultra-clear"></div>';
		
		//2
		$text_terms = stripslashes($this->get_option('uultra_terms_and_conditions_text_2'));
		$text_terms_large = stripslashes(nl2br($this->get_option('uultra_terms_and_conditions_text_large_2')));
		
		$text_terms_2_mandatory = stripslashes($this->get_option('uultra_terms_and_conditions_mandatory_2'));		
		$required = '';		
		if($text_terms_2_mandatory=='yes' || $text_terms_2_mandatory==''){$required = 'validate[required]';}
		
		
		if($text_terms!='')
		{
			
			$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
			$display .= '<div class="xoouserultra-clear"></div>'; 
			
			$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
			$display .= '<span>&nbsp;</span></label>';
			
			$display .= '<div class="xoouserultra-field-value">';
			$display .= '<input type="checkbox"  title="'.__('Terms & Conditions ', 'users-ultra').'" name="uultra-terms-and-conditions-confirmation-2"  id="uultra-terms-and-conditions-confirmation-2" value="1" class="'.$required.'" > <label for="uultra-terms-and-conditions-confirmation-2"><span></span>'.$text_terms.'</label><br>' ;
			
			$display .= '<div class="uuultra-terms-scrollable-box">'.$text_terms_large .'</div>';
			
			$display .= '</div>';
			
			
			$display .= '<div class="xoouserultra-clear"></div>';		
		
		}
		
		//3
		$text_terms = stripslashes($this->get_option('uultra_terms_and_conditions_text_3'));
		$text_terms_large = stripslashes($this->get_option('uultra_terms_and_conditions_text_large_3'));
		
		$text_terms_3_mandatory = stripslashes($this->get_option('uultra_terms_and_conditions_mandatory_3'));		
		$required = '';		
		if($text_terms_3_mandatory=='yes' || $text_terms_3_mandatory==''){$required = 'validate[required]';}
		
		if($text_terms!='')
		{
			
			$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
			$display .= '<div class="xoouserultra-clear"></div>'; 
			
			$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
			$display .= '<span>&nbsp;</span></label>';
			
			$display .= '<div class="xoouserultra-field-value">';
			$display .= '<input type="checkbox"  title="'.__('Terms & Conditions ', 'users-ultra').'" name="uultra-terms-and-conditions-confirmation-3"  id="uultra-terms-and-conditions-confirmation-3" value="1" class="'.$required.'" > <label for="uultra-terms-and-conditions-confirmation-3"><span></span>'.$text_terms.'</label><br>';
			
			
			$display .= '<div class="uuultra-terms-scrollable-box">'.$text_terms_large .'</div>';			
			$display .= '</div>';
			
			$display .= '<div class="xoouserultra-clear"></div>';		
		
		}
		
		//4
		$text_terms = stripslashes($this->get_option('uultra_terms_and_conditions_text_4'));
		$text_terms_large = stripslashes($this->get_option('uultra_terms_and_conditions_text_large_4'));
		
		$text_terms_4_mandatory = stripslashes($this->get_option('uultra_terms_and_conditions_mandatory_4'));		
		$required = '';		
		if($text_terms_4_mandatory=='yes' || $text_terms_4_mandatory==''){$required = 'validate[required]';}
		
		if($text_terms!='')
		{
			
			$display .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
			$display .= '<div class="xoouserultra-clear"></div>'; 
			
			$display .= '<label class="xoouserultra-field-type" for="'.$meta.'">';			
			$display .= '<span>&nbsp;</span></label>';
			
			$display .= '<div class="xoouserultra-field-value">';
			$display .= '<input type="checkbox"  title="'.__('Terms & Conditions ', 'users-ultra').'" name="uultra-terms-and-conditions-confirmation-4"  id="uultra-terms-and-conditions-confirmation-4" value="1" class="'.$required.'" > <label for="uultra-terms-and-conditions-confirmation-4"><span></span>'.$text_terms.'</label>'.$text_terms_large.'</div>' ;
			
			$display .= '<div class="xoouserultra-clear"></div>';		
		
		}
		
		
		
		
			
		
		return $display;
	
	}
	
	/**
	 * This has been added to avoid the window server issues
	 */
	public function uultra_one_line_checkbox_on_window_fix($choices)
	{		
		
		if($this->uultra_if_windows_server()) //is window
		{
			$loop = array();		
			$loop = explode(",", $choices);
		
		}else{ //not window
		
			$loop = array();		
			$loop = explode(PHP_EOL, $choices);	
			
		}	
		
		
		return $loop;
	
	}
	
	public function uultra_if_windows_server()
	{
		$os = PHP_OS;
		$os = strtolower($os);			
		$pos = strpos($os, "win");	
		
		if ($pos === false) {
			
			//echo "NO, It's not windows";
			return false;
		} else {
			//echo "YES, It's windows";
			return true;
		}			
	
	}
	
	/**
	 * Users Dashboard
	 */
	public function show_usersultra_my_account($atts )
	{
		global $wpdb, $current_user;		
		$user_id = get_current_user_id();
		
		extract( shortcode_atts( array(	
			
			'disable' => '',
			'custom_label_upload_avatar' => __("Upload Avatar", 'users-ultra')	,
			'avatar_is_called' => __("Avatar", 'users-ultra')	
						
			
		), $atts ) );
		
		$modules = array();
		$modules  = explode(',', $disable);
		
		//turn on output buffering to capture script output
        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/uupro/dashboard.php"))
		{
			
			include($theme_path."/uupro/dashboard.php");
		
		}else{
			
			include(xoousers_path.'/templates/'.xoousers_template."/dashboard.php");
		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;
		  
	}
	
	/**
	 * Display Minified Profile
	 */
	public function show_minified_profile($atts)
	{
		 return $this->userpanel->show_minified_profile($atts);		
			
	}	
	
	/**
	 * Display Front Publisher
	 */
	public function show_front_publisher($atts)
	{
		 return $this->publisher->show_front_publisher($atts);		
			
	}	
	
	/**
	 * Top Rated Photos
	 */
	public function show_top_rated_photos($atts)
	{
		 return $this->photogallery->show_top_rated_photos($atts);		
			
	}
	
	/**
	 * Top Rated Photos
	 */
	public function show_latest_photos($atts)
	{
		 return $this->photogallery->show_latest_photos($atts);		
			
	}
	
	/**
	 * Photo Grid
	 */
	public function show_photo_grid($atts)
	{
		 return $this->photogallery->show_photo_grid($atts);		
			
	}
	
	/**
	 * Featured Users
	 */
	public function show_featured_users($atts)
	{
		 return $this->userpanel->show_featured_users($atts);		
			
	}
	
	
	/**
	 * Promoted Users
	 */
	public function show_promoted_users($atts)
	{
		 return $this->userpanel->show_promoted_users($atts);		
			
	}
	
	/**
	 * Promoted Photos
	 */
	public function show_promoted_photos($atts)
	{
		 return $this->photogallery->show_promoted_photos($atts);		
			
	}
	
	/**
	 * Latest Users
	 */
	public function show_latest_users($atts)
	{
		 return $this->userpanel->show_latest_users($atts);		
			
	}
	
	/**
	 * Featured Users
	 */
	public function show_top_rated_users($atts)
	{
		 return $this->userpanel->show_top_rated_users($atts);		
			
	}
	
	/**
	 * Top Most Visited Users
	 */
	public function show_most_visited_users($atts)
	{
		 return $this->userpanel->show_most_visited_users($atts);		
			
	}
	
	/**
	 * Public Profile
	 */
	public function show_pulic_profile($atts)
	{
		 return $this->userpanel->show_public_profile($atts);		
			
	}
	
	/**
	 * Get Templates
	 */
	
	public function usersultra_get_template($template)
	{
		$display = "";
		$display .= require_once(xoousers_path.'/templates/'.xoousers_template."/".$template.".php");	
	
	}
	
	public function get_social_buttons_short_code ($atts)
	{
		require_once(xoousers_path."libs/fbapi/src/facebook.php");
		
		$display ="";
		
		extract( shortcode_atts( array(
			'provider' => '',
			
		), $atts ) );
		
		$socials = explode(',', $provider); ;	
		
		
		$FACEBOOK_APPID = $this->get_option('social_media_facebook_app_id');  
			$FACEBOOK_SECRET = $this->get_option('social_media_facebook_secret');
							
			$config = array();
			$config['appId'] = $FACEBOOK_APPID;
			$config['secret'] = $FACEBOOK_SECRET;
			
			$web_url = site_url()."/"; 
			
			$action_text = __('Connect with ','users-ultra');
			
			
			$atleast_one = false;
			
			
			if(in_array('facebook', $socials)) 
			{
				$atleast_one = true;
				$facebook = new Facebook($config);			
				
								
				$params = array(
						  'scope' => 'public_profile, email',						  				  
						  'redirect_uri' => $web_url
						);
						
				$loginUrl = $facebook->getLoginUrl($params);
			
				//Facebook
				$display .='<div class="txt-center FacebookSignIn">
				
				       	               	
						<a href="'.$loginUrl.'" class="btnuultra-facebook" >
							<span class="uultra-icon-facebook"> <img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/facebook.png" ></span>'.$action_text.' Facebook </a>
					
					</div>';
					
			}
			
			if(in_array('yahoo', $socials)) 
			{
			
				$auth_url_yahoo = $web_url."?uultrasocialsignup=yahoo";			
				
				$atleast_one = true;
			
				//Yahoo
				$display .='<div class="txt-center YahooSignIn">	               	
							<a href="'.$auth_url_yahoo.'" class="btnuultra-yahoo" >
							<span class="uultra-icon-yahoo"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yahoo.png" ></span>'.$action_text.' Yahoo </a>
					
					</div>';
		     }
			 
			if(in_array('google', $socials)) 
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=google";
			
				$atleast_one = true;
			
				//Google
				$display .='<div class="txt-center GoogleSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-google" >
							<span class="uultra-icon-google"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/googleplus.png" ></span>'.$action_text.' Google </a>
					
					</div>';
			}
			
			if(in_array('twitter', $socials)) 
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=twitter";
			
				$atleast_one = true;
			
				//Google
				$display .='<div class="txt-center TwitterSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-twitter" >
							<span class="uultra-icon-twitter"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/twitter.png" ></span>'.$action_text.' Twitter </a>
					
					</div>';
			}
			
			if(in_array('yammer', $socials)) 
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=yammer";
			
				$atleast_one = true;
			
				//Google
				$display .='<div class="txt-center YammerSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-yammer" >
							<span class="uultra-icon-yammer"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yammer.png" ></span>'.$action_text.' Yammer </a>
					
					</div>';
			}
			
			if(in_array('linkedin', $socials)) 
			{
				$atleast_one = true;
				
							
				$requestlink = $web_url."?uultrasocialsignup=linkedin";
				
				
				//LinkedIn
				$display .='<div class="txt-center LinkedSignIn">	               	
							<a href="'.$requestlink.'" class="btnuultra-linkedin" >
								<span class="uultra-icon-linkedin"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/linkedin.png" ></span>'.$action_text.' LinkedIn </a>
					
					</div>';
			}	
			
				
		
		
	return $display;
		
	}
	
	function get_facebook_sdk($api)
	{
		// Turn on the output buffer
		ob_start();
		
		require_once(xoousers_path."libs/fbsdkjs/fbscript.php");
		
		// Store the contents of the buffer in a variable
		$html = ob_get_clean();
		
		// Return the content you want to the calling function
		return $html;

	
	
	}
	
	

	
	public function get_social_buttons ($action_text, $atts)
	{
		
		
		$display ="";
		
		extract( shortcode_atts( array(
			'social_conect' => '',
			'display_style' => 'default', //default, minified
			'rounded_border' => 'no', //no, yes
			
		), $atts ) );
		
		$rounded_class = '';
		
		if($rounded_border=='yes')
		{
			$rounded_class = 'btnuultraminico-rounded';
		
		}
		
		
		if($this->get_option('registration_rules')!=4) // Social media is not able when using paid registrations
		{
		
			
			$web_url = site_url()."/"; 
			
			
			$atleast_one = false;
			
			
			if($this->get_option('social_media_fb_active')==1)
			{
				if (!function_exists('curl_init')) {
				  echo 'Facebook needs the CURL PHP extension.';
				}
				if (!function_exists('json_decode')) {
				  echo 'Facebook needs the JSON PHP extension.';
				}

				$FACEBOOK_APPID = $this->get_option('social_media_facebook_app_id');  
				$FACEBOOK_SECRET = $this->get_option('social_media_facebook_secret');
				
				
				$atleast_one = true;
				
				
				
				if($display_style=='minified')
				{					
					//Facebook
					$display .='			       	               	
						<a href="#" class="btnuultramini-facebook '.$rounded_class.'" id="uupro-facebook-login-bt" >
						<span class="uultra-icon-facebook"> <img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/facebook.png" ></span></a>				
					';			
				
				}else{
					
					//Facebook
					$display .='<div class="txt-center FacebookSignIn">				       	               	
						<a href="#" class="btnuultra-facebook" id="uupro-facebook-login-bt" >
						<span class="uultra-icon-facebook"> <img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/facebook.png" ></span>'.$action_text.' Facebook </a>
					
					</div>';
						
				}
				
				$display .= $this->get_facebook_sdk($FACEBOOK_APPID);
				
				
				
				
					
			}
			
			if($this->get_option('social_media_yahoo')==1)
			{
			
				$auth_url_yahoo = $web_url."?uultrasocialsignup=yahoo";					
				$atleast_one = true;		
				
				if($display_style=='minified')
				{
					
					//Yahoo
					$display .='	               	
							<a href="'.$auth_url_yahoo.'" class="btnuultramini-yahoo '.$rounded_class.'" >
							<span class="uultra-icon-yahoo"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yahoo.png" ></span></a>
					
					';
				
				}else{
					
					//Yahoo
					$display .='<div class="txt-center YahooSignIn">	               	
							<a href="'.$auth_url_yahoo.'" class="btnuultra-yahoo" >
							<span class="uultra-icon-yahoo"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yahoo.png" ></span>'.$action_text.' Yahoo </a>
					
					</div>';
				
				}
			
				
		     }
			 
			if($this->get_option('social_media_google')==1)
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=google";
			
				$atleast_one = true;
				
				if($display_style=='minified')
				{
					//Google
					$display .='              	
						<a href="'.$auth_url_google.'" class="btnuultramini-google '.$rounded_class.'" >
							<span class="uultra-icon-google"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/googleplus.png" ></span> </a>
					
					';
					
				
				}else{
					
					//Google
					$display .='<div class="txt-center GoogleSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-google" >
							<span class="uultra-icon-google"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/googleplus.png" ></span>'.$action_text.' Google </a>
					
					</div>';
					
				}
			
				
			}
			
			if($this->get_option('twitter_connect')==1)
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=twitter";
			
				$atleast_one = true;
				
				
				if($display_style=='minified')
				{
					//Google
					$display .='              	
						<a href="'.$auth_url_google.'" class="btnuultramini-twitter '.$rounded_class.'" >
							<span class="uultra-icon-twitter"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/twitter.png" ></span></a>
					
				';
					
				
				}else{
					
					//Google
					$display .='<div class="txt-center TwitterSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-twitter" >
							<span class="uultra-icon-twitter"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/twitter.png" ></span>'.$action_text.' Twitter </a>
					
					</div>';
					
				
				}
			
				
			}
			
			//instagram
			if($this->get_option('instagram_connect')==1)
			{
				//instagram			
				$auth_url_google = $web_url."?uultrasocialsignup=instagram";			
				$atleast_one = true;			
				
				if($display_style=='minified')
				{
					//Google
					$display .='              	
						<a href="'.$auth_url_google.'" class="btnuultramini-instagram '.$rounded_class.'" >
							<span class="uultra-icon-instagram"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/instagram-icon.png" ></span></a>
					
				';				
				
				}else{
					
					//Instagram
					$display .='<div class="txt-center InstagramSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-instagram" >
							<span class="uultra-icon-instagram"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/instagram-icon.png" ></span>'.$action_text.' Instagram </a>
					
					</div>';
				
				}
			}
			
			if($this->get_option('yammer_connect')==1)
			{
				//google
			
				$auth_url_google = $web_url."?uultrasocialsignup=yammer";
			
				$atleast_one = true;
				
				
				if($display_style=='minified')
				{
					
					//Google
					$display .='               	
						<a href="'.$auth_url_google.'" class="btnuultramini-yammer '.$rounded_class.'" >
							<span class="uultra-icon-yammer"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yammer.png" ></span> </a>
					
					';
					
				
				}else{
					
					//Google
					$display .='<div class="txt-center YammerSignIn">	               	
						<a href="'.$auth_url_google.'" class="btnuultra-yammer" >
							<span class="uultra-icon-yammer"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/yammer.png" ></span>'.$action_text.' Yammer </a>
					
					</div>';
				
				}
			
				
			}
			
			if($this->get_option('social_media_linked_active')==1)
			{
				$atleast_one = true;
				
				$auth_url_g = $web_url."?uultrasocialsignup=linkedin";
				
								
				if($display_style=='minified')
				{					
					//LinkedIn
					$display .='               	
							<a href="'.$auth_url_g.'" class="btnuultramini-linkedin '.$rounded_class.'" >
								<span class="uultra-icon-linkedin"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/linkedin.png" ></span> </a>
					
					';
					
				
				}else{
					
						//LinkedIn
					$display .='<div class="txt-center LinkedSignIn">	               	
							<a href="'.$auth_url_g.'" class="btnuultra-linkedin" >
								<span class="uultra-icon-linkedin"><img src="'.xoousers_url.'templates/'.xoousers_template.'/img/socialicons/linkedin.png" ></span>'.$action_text.' LinkedIn </a>
					
					</div>';
				
				}
				
				
			
			}	
			
			if($atleast_one)
			{
				$display .='<div class="xoouserultra-or-divider">	<div>'.__("or", 'users-ultra').'</div>	</div>';
			
			}
		
		
		}
	return $display;
		
	}
	

	
	 /*---->> Set Account Status  ****/  
 	 public function user_account_status($user_id) 
  	{
	 // global $xoouserultra;
	  
	  //check if login automatically
	  $activation_type= $this->get_option('registration_rules');
	  
	  if($activation_type==1)
	  {
		  //automatic activation
		  update_user_meta ($user_id, 'usersultra_account_status', 'active');							
	  
	  }elseif($activation_type==2){
		  
		  //email activation link
		  update_user_meta ($user_id, 'usersultra_account_status', 'pending');	
	  
	  }elseif($activation_type==3){
		  
		  //manually approved
		  update_user_meta ($user_id, 'usersultra_account_status', 'pending_admin');
	  
	  
	  }
	
  }
  
 
	
	//special feature for yahoo and google	
	public function social_login_links_openid()
	{
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$web_url = site_url()."/";
		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="yahoo") 
		{						
				
			require_once(xoousers_path."libs/openid/openid.php");				
			$openid_yahoo = new LightOpenID($web_url);
			//yahoo
					
			$openid_yahoo->identity = 'https://me.yahoo.com';
			$openid_yahoo->required = array(
					  'namePerson',
					  'namePerson/first',
					  'namePerson/last',
					  'contact/email',
					);
					
			$openid_yahoo->returnUrl = $web_url;
			$auth_url_yahoo = $openid_yahoo->authUrl();
			wp_redirect($auth_url_yahoo);
			exit;
		}
		
		
		
		
	}
	
	
	
	public function get_linkein_auth_link ()
	{
		$requestlink ="";
		
		//LinkedIn lib
		 require_once(xoousers_path."libs/linkedin/oauth/linkedinoauth.php");		 
		 
		 $oauthstate = $this->get_linkedin_oauth_token();		 
		 $tokenpublic =  $oauthstate['request_token'];		 
		 
		 $to = new LinkedInOAuth($this->get_option('social_media_linkedin_api_public'), $this->get_option('social_media_linkedin_api_private'));
		 $requestlink = $to->getAuthorizeURL($tokenpublic, $this->get_current_url());
		 	 
		 return $requestlink;
				
	
	
	}
	
	//used only once we've got a oauth_token and oauth_verifier	
	function get_linkedin_access_token($oauthstate)
	{
		require_once(xoousers_path."libs/linkedin/oauth/linkedinoauth.php");
		
		$requesttoken = $oauthstate['request_token'];
		$requesttokensecret = $oauthstate['request_token_secret'];
		
		$urlaccessverifier = $_REQUEST['oauth_verifier'];
	
		error_log("Creating API with $requesttoken, $requesttokensecret");			
		
		$to = new LinkedInOAuth(
				$this->get_option('social_media_linkedin_api_public'), 
				$this->get_option('social_media_linkedin_api_private'),
				$requesttoken,
				$requesttokensecret
		);
			
		$tok = $to->getAccessToken($urlaccessverifier);
		
		//print_r($tok);
		
		$accesstoken = $tok['oauth_token'];
		$accesstokensecret = $tok['oauth_token_secret'];
		
		$oauthstate['access_token'] =  $accesstoken;
		$oauthstate['access_token_secret'] =  $accesstokensecret;
		
		return $oauthstate;
			
	
	}
	
	function get_linkedin_oauth_token()
	{
		session_start();
		
		require_once(xoousers_path."libs/linkedin/oauth/linkedinoauth.php");
		$oauthstate = $this->get_linkedin_oauth_state();
		
			//echo "not set aut state";
			error_log("No OAuth state found");
	
			$to = new LinkedInOAuth($this->get_option('social_media_linkedin_api_public'), $this->get_option('social_media_linkedin_api_private'));
			
			// This call can be unreliable for some providers if their servers are under a heavy load, so
			// retry it with an increasing amount of back-off if there's a problem.
			$maxretrycount = 1;
			$retrycount = 0;
			while ($retrycount<$maxretrycount)
			{		
				$tok = $to->getRequestToken();
				if (isset($tok['oauth_token'])&&
					isset($tok['oauth_token_secret']))
					break;
				
				$retrycount += 1;
				sleep($retrycount*5);
			}
			
			$tokenpublic = $tok['oauth_token'];
			$tokenprivate = $tok['oauth_token_secret'];
			$state = 'start';
			
			// Create a new set of information, initially just containing the keys we need to make
			// the request.
			$oauthstate = array(
				'request_token' => $tokenpublic,
				'request_token_secret' => $tokenprivate,
				'access_token' => '',
				'access_token_secret' => '',
								
				'state' => $state,
			);
			
			//SET IN DB TEMP TOKEN
			$temp_user_session_id = session_id();			
			update_option('uultra_linkedin_'.$temp_user_session_id, $oauthstate);				
			$oauthstate =  get_option('uultra_linkedin_'.$temp_user_session_id);
	
			$this->set_linkedin_oauth_state($oauthstate);
			
			
			
			return $oauthstate;
	
	}
	
	function get_linkedin_oauth_state()
	{
		if (empty($_SESSION['linkedinoauthstate']))
			return null;
			
		$result = $_SESSION['linkedinoauthstate'];
	
		error_log("Found state ".print_r($result, true));
		
		//print_r($_SESSION);
		
			
		return $result;
	}
	
	// Updates the information about the user's progress through the oAuth process.
	function set_linkedin_oauth_state($state)
	{
		error_log("Setting OAuth state to - ".print_r($state, true));
		$_SESSION['linkedinoauthstate'] = $state;		
		
	}
	
		
	public function get_current_url()
	{
		$result = 'http';
		$script_name = "";
		if(isset($_SERVER['REQUEST_URI'])) 
		{
			$script_name = $_SERVER['REQUEST_URI'];
		} 
		else 
		{
			$script_name = $_SERVER['PHP_SELF'];
			if($_SERVER['QUERY_STRING']>' ') 
			{
				$script_name .=  '?'.$_SERVER['QUERY_STRING'];
			}
		}
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') 
		{
			$result .=  's';
		}
		$result .=  '://';
		
		if($_SERVER['SERVER_PORT']!='80')  
		{
			$result .= $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$script_name;
		} 
		else 
		{
			$result .=  $_SERVER['HTTP_HOST'].$script_name;
		}
	
		return $result;
	}
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('userultra_options');
		if (isset($settings[$option])) 
		{
			if(is_array($settings[$option]))
			{
				return $settings[$option];
			
			}else{
				
				return stripslashes($settings[$option]);
			}
			
		}else{
			
		    return '';
		}
		    
	}
	
	/* Get post value */
	function uultra_admin_post_value($key, $value, $post){
		if (isset($_POST[$key])){
			if ($_POST[$key] == $value)
				echo 'selected="selected"';
		}
	}
	
	/*Post value*/
	function get_post_value($meta) {
				
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
	
		

}
?>