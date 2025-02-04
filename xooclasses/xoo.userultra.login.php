<?php
class XooUserLogin {
	
	
	var $mIsSocialLogin;
	
	var $ajax_p = 'uupro';
	
	function __construct() 
	{
		 add_action( 'wp_login', array($this, 'uultra_handle_after_login' ),99,2 );
		 add_action( 'init', array($this, 'handle_init' ) );
		 
		 add_action( 'wp_ajax_'.$this->ajax_p.'_handle_social_facebook',  array( &$this, 'handle_social_facebook' ));
		 add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_handle_social_facebook',  array( &$this, 'handle_social_facebook' ));	
		 
		 
		 	
		
	}	
	
	/*Handle after login actions*/
	function uultra_handle_after_login($user_login, $user) 
	{
	    global $xoouserultra;
	    
		require_once(ABSPATH . 'wp-includes/user.php');
		
		//update last login
		$user = get_user_by( 'login', $user_login );
		$current_time = date("Y-m-d H:i:s"); 
		update_user_meta($user->ID, 'uultra_last_login', $current_time);		
		
	
	}

	function handle_init() 
	{
		/*-----------Referece Social Users Types*/
		/* 1 - Facebook, 2 - LinkedIn,  3- Yahoo, 4 - Google, 5 - Twitter , 6 - Yammer , 7 - Instagram */
		
		$this->mIsSocialLogin = false;
		/*------------------------------*/
		
		if (isset($_GET['uultrasocialsignup'])) 
		{
			$this->mIsSocialLogin = true;
			
			session_start();
			$_SESSION['google_token']  = NULL;
			/* get social links */
			$this->social_login_links_oauth();
			
				
		}
		
		
		if( isset( $_GET['code'] ) && isset($_REQUEST['uultraplus']) && $_REQUEST['uultraplus'] == '1' ) 
		{
			$this->mIsSocialLogin = true;			
			/* authorize google*/
			$this->google_authorize();
		}
		
		if ( isset( $_REQUEST['oauth_verifier'] ) && isset( $_REQUEST['oauth_token'] ) && !isset($_REQUEST['uultralinkedin'])  ) 
		{
			$this->mIsSocialLogin = true;
			
			/* authorize twitter*/
			$this->twitter_authorize();
		}
		
		if( isset( $_GET['code'] ) && isset($_REQUEST['uultryammer']) && $_REQUEST['uultryammer'] == '1' ) 
		{
			$this->mIsSocialLogin = true;
			
			/* authorize yammer*/
			$this->yammer_authorize();
		}
		
		if( isset( $_GET['code'] ) && isset($_REQUEST['instagram']) && $_REQUEST['instagram'] == '1' ) 
		{
			$this->mIsSocialLogin = true;
			
			/* authorize yammer*/
			$this->instagram_authorize();
		}
		
	
		
		/*Handle PayPal Login*/
		if (isset($_GET['usersultraipncall'])) 
		{
			$this->errors = false;			
			/* */
			$this->handle_paypal_login();

		}
		
		/*Handle PayPal Upgrade*/
		if (isset($_GET['usersultraipncall'])) 
		{
			//$this->errors = false;			
			/* */
			//$this->handle_paypal_login();

		}
		
		/*Handle Acctoun Verification */
		if (isset($_GET['act_link'])) 
		{					
			/* */
			$this->handle_account_conf_link();

		}
						
		if (isset($_GET['code']) && !isset($_REQUEST['uultraplus']) && !isset($_REQUEST['uultryammer'])) 
		{
			
			$this->mIsSocialLogin = true;
						
			// Setting default to false;
			$this->errors = false;
						
			/* */
			//$this->handle_social_facebook();

		}
		
		//yahooo and google
		if (isset($_GET["openid_ns"])) 
		{
			$this->mIsSocialLogin = true;
			
			// Setting default to false;
			$this->errors = false;			
			/* */
			$this->handle_social_google();

		}
		
		if(!isset($_REQUEST["oauth_token"]))
		{
			$_SESSION['linkedinoauthstate'] = NULL;
			$_REQUEST['oauth_token'] = NULL;
			$_REQUEST['oauth_verifier'] = NULL;
			
		}
		
		//linkedin
		if (isset($_GET['oauth_token']) && isset($_GET['uultralinkedin']) ) 
		{
			$this->mIsSocialLogin = true;
						
			// Setting default to false;
			$this->errors = false;			
			/* */
			$this->handle_linkedin_authorization();

		}
		
			
		
		/*Form is fired*/
		if (isset($_POST['xoouserultra-login'])) {
						
			/* Prepare array of fields */
			$this->prepare( $_POST );
			
			// Setting default to false;
			$this->errors = false;
			
			/* Validate, get errors, etc before we login a user */
			$this->handle();

		}

	}
	
	function social_login_hook($user_id, $social_method) 
	{
		
		if (function_exists('uultra_social_registration_hook')) 
		{		
			uultra_social_registration_hook($user_id, $social_method );	
		
		}	
		
    }
	
	/*Prepare user meta*/
	function prepare ($array ) 
	{
		foreach($array as $k => $v) {
			if ($k == 'xoouserultra-login') continue;
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle commons sigun ups*/
	function handle() 
	{
	    global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
	    
		require_once(ABSPATH . 'wp-includes/user.php');
		
		if ( empty( $GLOBALS['wp_rewrite'] ) )
		{
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }
		
		$noactive = false;
		foreach($this->usermeta as $key => $value) 
		{
		
			if ($key == 'user_login') 
			{
				if (sanitize_user($value) == '')
				{
					$this->errors[] = __('<strong>ERROR:</strong> The username field is empty.','users-ultra');
				}
			}
			
			if ($key == 'user_pass')
			{
				if (esc_attr($value) == '') 
				{
					$this->errors[] = __('<strong>ERROR:</strong> The password field is empty.','users-ultra');
				}
			}
		}
		
		
		// Check captcha first
		if(!is_in_post('no_captcha','yes'))
		{
		   // if(!$xoousersultra_captcha_loader->validate_captcha(post_value('captcha_plugin')))
		   // {
		      //  $this->errors[] = __('<strong>ERROR:</strong> Please complete Captcha Test first.','users-ultra');
		    //}
		}
		
	
			/* attempt to signon */
			if (!is_array($this->errors)) 
			{
				
				$creds = array();
				
				// Adding support for login by email
				if(is_email($_POST['user_login']))
				{
				    $user = get_user_by( 'email', $_POST['user_login'] );
				    
				    if(isset($user->data->user_login))
					{
				        $creds['user_login'] = $user->data->user_login;
						
				    }else{
						
				        $creds['user_login'] = '';
					
					}
					
					// check if active					
					$user_id =$user->ID;				
					if(!$this->is_active($user_id))
					{
						$noactive = true;
						
					}
				
				}else{
					
					// User is trying to login using username					
					$user = get_user_by('login',$_POST['user_login']);
					
					// check if active and it's not an admin		
					if(isset($user))	
					{
						$user_id =$user->ID;	
						
					
					}else{
						
						$user_id ="";
						
					}		
					if(!$this->is_active($user_id) && !is_super_admin($user_id))
					{
						$noactive = true;
						
					}				
					
					$creds['user_login'] = sanitize_user($_POST['user_login']);			
				
				}
				
				$creds['user_password'] = $_POST['login_user_pass'];
				$creds['remember'] = $_POST['rememberme'];					
				
				
				if(!$noactive)
				{								
					$user = wp_signon( $creds, false );			
					
						//print_r($user );	
	
					if ( is_wp_error($user) ) 
					{
						
						//echo "TTEES here" ;
						if ($user->get_error_code() == 'invalid_username') {
							$this->errors[] = __('<strong>ERROR:</strong> Invalid Username was entered.','users-ultra');
						}
						if ($user->get_error_code() == 'incorrect_password') {
							$this->errors[] = __('<strong>ERROR:</strong> Incorrect password was entered.','users-ultra');
						}
						
						if ($user->get_error_code() == 'empty_password') {
							$this->errors[] = __('<strong>ERROR:</strong> Please provide Password.','users-ultra');
						}
						
						
											
					}else{	
						
						
						$this->uuultra_auto_login($user->user_login);						
						$this->login_registration_afterlogin();
					
					}
					
					//print_r($user );	
				
				}else{
					
					//not active
					$this->errors[] = __('<strong>ERROR:</strong> Your account is not active.','users-ultra');
				 
				}
			}
		
		
		
		
	}
	
	
	/* Auto login user */
	function uuultra_auto_login( $username, $remember=true ) 
	{
		ob_start();
		if ( !is_user_logged_in() ) {
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $user->user_login, $user );
		} else {
			wp_logout();
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $user->user_login, $user );
		}
		ob_end_clean();
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
	
	//special feature for yahoo and google	
	public function social_login_links_oauth()
	{
		global $xoouserultra, $blog_id;
		
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$web_url = site_url()."/";
		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="google") 
		{		
			$auth_url_google = $this->get_google_auth_url();
			wp_redirect($auth_url_google);
			exit;
			
			
		}
		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="twitter") 
		{		
			$auth_twitter_ = $this->get_twitter_auth_url();			
			wp_redirect($auth_twitter_);
			exit;
			
		}
		
		//instagram
		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="instagram") 
		{		
			$auth_instagram_ = $this->get_instagram_auth_url();
			wp_redirect($auth_instagram_);
			exit;
			
		}
		
		//linked in		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="linkedin") 
		{
			if (!isset($_REQUEST['oauth_token']))
			{
				$requestlink = $xoouserultra->get_linkein_auth_link();	
			}						
			
			wp_redirect($requestlink);
			exit;
			
			
		}
		
		if (isset($_GET['uultrasocialsignup']) && $_GET['uultrasocialsignup']=="yammer") 
		{
			$client_id = 	$xoouserultra->get_option('yammer_client_id') ;	
			$client_secret = 	$xoouserultra->get_option('yammer_client_secret') ;	
			$redir_uri = 	$xoouserultra->get_option('yammer_redir_url') ;	
			
			$auth_yammer = "https://www.yammer.com/dialog/oauth?client_id=".$client_id."&redirect_uri=".$redir_uri."";
			wp_redirect($auth_yammer);
			exit;
			
		}
	
	}
	
	 /******************************************
	We Load twitter
	******************************************/
	function load_twitter()
	{
		global $xoouserultra, $blog_id;
		
		if ( $xoouserultra->get_option('twitter_connect') == 1 && $xoouserultra->get_option('twitter_consumer_key') && $xoouserultra->get_option('twitter_consumer_secret') ) 
		
		{
		
			if (!session_id()){
				session_start();
			}
			if (!class_exists('TwitterOAuth'))
			{
				
				require_once(xoousers_path."libs/twitterapi/twitteroauth.php");
			}
			
			$this->twitter = new TwitterOAuth(  $xoouserultra->get_option('twitter_consumer_key') , $xoouserultra->get_option('twitter_consumer_secret') );
			
		}
	}
	
	/******************************************
	Twitter redirection url after connect
	******************************************/
	function twitter_redirect_url()
	{
		$curent_page_url = remove_query_arg( array( 'oauth_token', 'oauth_verifier', 'uultrasocialsignup' ), $this->get_current_page_url() );
		return $curent_page_url;
	}
	
	/******************************************
	Get current page URL
	******************************************/
	function get_current_page_url()
	{
    	global $post;
		if ( is_front_page() ) :
			$page_url = home_url();
			else :
			$page_url = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" )
			$page_url .= "s";
				$page_url .= "://";
				if ( $_SERVER["SERVER_PORT"] != "80" )
			$page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				else
			$page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			endif;
			
		return esc_url( $page_url ) ;
	}
	
	/******************************************
	Instagram auth url
	******************************************/
	function get_instagram_auth_url() {
			
		global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
		require_once(ABSPATH . 'wp-admin/includes/user.php' );
		
		require_once(xoousers_path."libs/instagram/instagram.class.php");
		
		$YOUR_APP_KEY = $xoouserultra->get_option('instagram_client_id');
		$YOUR_APP_SECRET = $xoouserultra->get_option('instagram_client_secret');
		$YOUR_APP_CALLBACK = $xoouserultra->get_option('instagram_redirect_uri');
		
		$instagram = new Instagram(array(
			'apiKey'      => $YOUR_APP_KEY,
			'apiSecret'   => $YOUR_APP_SECRET,
			'apiCallback' => $YOUR_APP_CALLBACK
		));
		
		// create login URL
		$loginUrl = $instagram->getLoginUrl(array(
		'basic',
		'likes',
		'relationships'
		)); 
		
		return $loginUrl;	
		
		
	}
	
	/******************************************
	Twitter auth url
	******************************************/
	function get_twitter_auth_url() {
			
		global $post;
		
		//if (!get_option('get_twitter_auth_url')){
			
			$this->load_twitter();
			$red_url =  $this->twitter_redirect_url();
			//echo "$red_url ";
		
			$request_token = $this->twitter->getRequestToken($red_url  ); // user will be redirected here
			
			switch( $this->twitter->http_code ) {
				case 200:
					$_SESSION['twt_oauth_token'] = $request_token['oauth_token'];
					$_SESSION['twt_oauth_token_secret'] = $request_token['oauth_token_secret'];

					$token = $request_token['oauth_token'];
					$this->twitter_url = $this->twitter->getAuthorizeURL( $token, true );
							
					break;
				default:
					$this->twitter_url = '';
			}
			update_option('get_twitter_auth_url', $this->twitter_url);
			return $this->twitter_url;
		
		//} else {
		//<}
	}
	
	/******************************************
	Twitter auth ($_REQUEST)
	******************************************/
	function twitter_authorize()
	{		
		global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
		require_once(ABSPATH . 'wp-admin/includes/user.php' );
		
			
		if ( $xoouserultra->get_option('twitter_connect') == 1 && $xoouserultra->get_option('twitter_consumer_key') && $xoouserultra->get_option('twitter_consumer_secret') )
		{
				
			//when user is going to logged in in twitter and verified successfully session will create
			if ( isset( $_REQUEST['oauth_verifier'] ) && isset( $_REQUEST['oauth_token'] ) ) 
			{
				
				//load twitter class
				$this->load_twitter();
				
				$oauth_token = $_SESSION['twt_oauth_token'];
				$oauth_token_secret = $_SESSION['twt_oauth_token_secret'];

				if( isset( $oauth_token )) {
											

					$this->twitter = new TwitterOAuth( $xoouserultra->get_option('twitter_consumer_key') , $xoouserultra->get_option('twitter_consumer_secret'), $oauth_token, $oauth_token_secret );
					
					// Request access tokens from twitter
					$tw_access_token = $this->twitter->getAccessToken($_REQUEST['oauth_verifier']);
					
					//session create for access token & secrets		
					$_SESSION['twt_oauth_token'] = $tw_access_token['oauth_token'];
					$_SESSION['twt_oauth_token_secret'] = $tw_access_token['oauth_token_secret'];
					$verifier['oauth_verifier'] = $_REQUEST['oauth_verifier'];
					$_SESSION[ 'twt_user_cache' ] = $verifier;
					
					//getting user data from twitter
					$user_info = $this->twitter->get('account/verify_credentials');
					$user_info = (array)$user_info;
					
				//	print_r($user_info);
					
					//if user data get successfully
					if (isset($user_info['id'])){
						
												
						$data['user'] = $user_info;
						
						//all data will assign to a session
						$_SESSION['twt_user_cache'] = $data;

						$users = get_users(array(
							'meta_key'     => 'xoouser_ultra_twitter_oauth_id',
							'meta_value'   => $user_info['id'],
							'meta_compare' => '='
						));
						if (isset($users[0]->ID) && is_numeric($users[0]->ID) ){
							$returning = $users[0]->ID;
							$returning_user_login = $users[0]->user_login;
						} else {
							$returning = '';
						}
						
						// Authorize user
						if (is_user_logged_in()) 
						{
							//create basic widgets
							$xoouserultra->customizer->set_default_widgets_layout($user_id);
							update_user_meta ($user_id, 'xoouser_ultra_twitter_oauth_id', $user_info['id']);	
							update_user_meta ($user_id, 'xoouser_ultra_social_signup', 5);						
							$this->login_registration_afterlogin();
							
							
						} else 
						{
							if ( $returning != '' ) 
							{
								
								
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$returning_user_login);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );
									do_action('wp_login', $user->user_login, $user);	
											
											
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
						
								//redirect user
								$this->login_registration_afterlogin();
							
							} else if ($user_info['screen_name'] != '' && username_exists($user_info['screen_name'])) {	
								///new
								
								//user email exists then we have to sync								
								$user_id = username_exists( $user_info['screen_name'] );
								$user = get_userdata($user_id);
								update_user_meta ($user_id, 'xoouser_ultra_twitter_oauth_id', $user_info['id']);
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 5);
								
								$u_user = $user->user_login;
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$u_user);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );
									do_action('wp_login', $user->user_login, $user);			
											
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
								
								//redirect user
								$this->login_registration_afterlogin();
								
							
							} else {

								///new
								//echo "new client";
								
								//this is a new client we have to create the account								
								 $u_name = $this->get_social_services_name('twitter', $user_info);													
								 $u_email = $user_info['id'];
								 
								//generat random password
								 $user_pass = wp_generate_password( 12, false);								 
								
								 $user_login = $this->unique_user('twitter', $user_info);
								 $user_login = sanitize_user ($user_login, true);	
								
								 //Build user data
								 $user_data = array (
												'user_login' => $user_login,
												'display_name' => $u_name,
												'user_email' => $u_email,																				
												'user_pass' => $user_pass
											);
											
														
								// Create a new user
								$user_id = wp_insert_user ($user_data);
								
								$this->social_login_hook($user_id, 5);
								
																
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 5);
								update_user_meta ($user_id, 'xoouser_ultra_twitter_oauth_id', $user_info['id']);
								
								update_user_meta ($user_id, 'xoouser_ultra_twitter_oauth_token', $_SESSION['twt_oauth_token']);
								update_user_meta ($user_id, 'xoouser_ultra_twitter_oauth_token_secret', $_SESSION['twt_oauth_token_secret']);
								
								update_user_meta ($user_id, 'first_name', $u_name);
								update_user_meta ($user_id, 'display_name', $u_name);
								
																
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);	
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($user_login);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;							
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									do_action('wp_login', $user->user_login, $user);
									
								}
								
								if ($xoouserultra->get_option('twitter_autopost') && $xoouserultra->get_option('twitter_autopost_msg') ) 
								{
									$this->twitter->post('statuses/update', array('status' => $xoouserultra->get_option('twitter_autopost_msg') ) );
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
																
								//redirect user
								$this->login_registration_afterlogin();
								
								
							}
						}
					}
				}
			}
		}
	}
	
	
	/*This function loads basic google libraries*/
	
	public function load_google()
	{
		global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
		
		if ( $xoouserultra->get_option('social_media_google') == 1 && $xoouserultra->get_option('google_client_id') && $xoouserultra->get_option('google_client_secret') && $xoouserultra->get_option('google_redirect_uri') ) 
		{
			
			require_once(xoousers_path."libs/google/src/Google/Client.php");
			require_once(xoousers_path."libs/google/src/Google/Service/Plus.php");
			require_once(xoousers_path."libs/google/src/Google/Service/Oauth2.php");		
			session_start();
			
			$this->google = new Google_Client();
			$this->google->setApplicationName("Authentication"); // Set your applicatio name
			$this->google->setScopes('email'); // set scope during user login
			
						
			$this->google->setClientId($xoouserultra->get_option('google_client_id')); // paste the client id which you get from google API Console
			$this->google->setClientSecret($xoouserultra->get_option('google_client_secret')); // set the client secret
			$this->google->setRedirectUri($xoouserultra->get_option('google_redirect_uri')); // paste the redirect URI where you given in APi Console. You will get the Access Token here during login success
			
			$this->google->setApprovalPrompt('auto');
			$this->google->setAccessType("online");
			
			$this->googleplus       = new Google_Service_Plus($this->google);
			$this->googleoauth2     = new Google_Service_Oauth2($this->google); // Call the OAuth2 class for get email address
			
			if (isset($_SESSION['google_token'])) 
			{
				$this->google->setAccessToken($_SESSION['google_token']);
			}
		}		
		
	}
	
	/*******************
	Google auth url
	********************/
	public function get_google_auth_url()
	{
		//load google class
		$google = $this->load_google();
			
		$url = $this->google->createAuthUrl();
		$authurl = isset( $url ) ? $url : '';			
		return $authurl;
	}
	
	/******************************************
	Yammer auth 
	******************************************/
	function yammer_authorize()
	{
		global  $xoouserultra, $blog_id;
		require_once(ABSPATH . 'wp-admin/includes/user.php' );
		
		$client_id = 	$xoouserultra->get_option('yammer_client_id') ;	
		$client_secret = 	$xoouserultra->get_option('yammer_client_secret') ;	
		$redir_uri = 	$xoouserultra->get_option('yammer_redir_url') ;
			
		
		if ( $xoouserultra->get_option('yammer_connect') == 1 && $client_id && $client_secret && $redir_uri ) 
		{
			
			if( isset( $_GET['code'] ) && isset($_REQUEST['uultryammer']) && $_REQUEST['uultryammer'] == '1' ) 
			{
				//get auth token
				$code =$_GET['code'];
				
				$url = "https://www.yammer.com/oauth2/access_token.json";		
								
				$response = wp_remote_get(
					$url,
					array(
						'body' => array(
							'client_id'   => $client_id,
							'client_secret'     => $client_secret,
							'code' => $code,
														
						)
					)
				);
				
				
				$response = json_decode($response["body"]);
				//print_r($response);
				
				//if user data get successfully
				if (isset($response->{'access_token'}->{'user_id'}))
				{
					$id =$response->{'access_token'}->{'user_id'};					
					$token =$response->{'access_token'}->{'token'}; 						
					$fullname = $response->{'user'}->{'full_name'}; 
					
					$email = $response->{'user'}->{'contact'}->{'email_addresses'}; 
					$email = $email->{'address'};
					//echo "USER EMAIL: " . print_r($email );
						
						
						//check if

						$users = get_users(array(
							'meta_key'     => 'xoouser_ultra_yammer',
							'meta_value'   => $id,
							'meta_compare' => '='
						));
						
						if (isset($users[0]->ID) && is_numeric($users[0]->ID) )
						{
							$returning = $users[0]->ID;
							$returning_user_login = $users[0]->user_login;
							
						} else {
							
							$returning = '';
						}
						
						// Authorize user
						if (is_user_logged_in()) 
						{
														
							update_user_meta ($user_id, 'xoouser_ultra_yammer', $id );							
							$this->login_registration_afterlogin();
						
						} else {
							
							//the user is NOT logged in							
							if ( $returning != '' ) 
							{
								
							
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$returning_user_login);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );
									do_action('wp_login', $user->user_login, $user);			
											
								}
						
								//redirect user
								$this->login_registration_afterlogin();
							
							} else if ($user_info['email'] != '' && email_exists($user_info['email'])) {
								
								//user email exists then we have to sync								
								$user_id = email_exists( $user_info['email'] );
								$user = get_userdata($user_id);
								update_user_meta ($user_id, 'xoouser_ultra_yammer', $id);
								
								$u_user = $user->user_login;
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$u_user);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );	
									do_action('wp_login', $user->user_login, $user);		
											
								}
								
								//redirect user
								$this->login_registration_afterlogin();
						
							
							} else {
								
																
								//this is a new client we have to create the account								
								 $u_name = $this->get_social_services_name('yammer', $fullname);													
								 $u_email = $user_info['email'];
								 
								//generat random password
								 $user_pass = wp_generate_password( 12, false);								 
								
								 $user_login = $this->unique_user('yammer', $fullname);
								 $user_login = sanitize_user ($user_login, true);	
								
								 //Build user data
								 $user_data = array (
												'user_login' => $user_login,
												'display_name' => $u_name,
												'user_email' => $u_email,																				
												'user_pass' => $user_pass
											);
											
																						
														
								// Create a new user
								$user_id = wp_insert_user ($user_data);
								
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 6);
								update_user_meta ($user_id, 'xoouser_ultra_yammer', $id);
								update_user_meta ($user_id, 'first_name', $u_name);
								update_user_meta ($user_id, 'display_name', $u_name);
								
																
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);	
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($user_login);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;							
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									do_action('wp_login', $user->user_login, $user);
									
								}
																
								//redirect user
								$this->login_registration_afterlogin();
								
								
							}
						}
					}
				
				
				
				
				
				
			
			}
		
		
		}
		
		
	}
	
	/******************************************
	Instagram auth 
	******************************************/
	function instagram_authorize()
	{
		global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-admin/includes/user.php' );
		
		require_once(xoousers_path."libs/instagram/instagram.class.php");
		
		$YOUR_APP_KEY = $xoouserultra->get_option('instagram_client_id');
		$YOUR_APP_SECRET = $xoouserultra->get_option('instagram_client_secret');
		$YOUR_APP_CALLBACK = $xoouserultra->get_option('instagram_redirect_uri');
		
		
		
		
		if ( $xoouserultra->get_option('instagram_connect') == 1 && $YOUR_APP_KEY && $YOUR_APP_SECRET && $YOUR_APP_CALLBACK ) 
		{
			
			if( isset( $_GET['code'] ) && isset($_REQUEST['instagram']) && $_REQUEST['instagram'] == '1' ) {
				
				
				$instagram = new Instagram(array(
					'apiKey'      => $YOUR_APP_KEY,
					'apiSecret'   => $YOUR_APP_SECRET,
					'apiCallback' => $YOUR_APP_CALLBACK
				));
				
				$code = $_GET['code'];
				
				
				// Receive OAuth token object
			    $data = $instagram->getOAuthToken($code);
			  // Take a look at the API response
			  
			 // print_r($data);
			 // exit;
			
				
			
				
			$user=$data->user->username;
			$fullname=$data->user->full_name;
			$bio=$data->user->bio;
			$website=$data->user->website;
			$id=$data->user->id;	
			
			$access_token=$data->access_token;
			
				
				
											
				
				//check access token is set or not
				if ( !empty( $user ) ) 
				{
						
					//if user data get successfully
					if (isset($id)){
						
						//$data['user'] = $user_info;
						
					
						//check if
						$users = get_users(array(
							'meta_key'     => 'xoouser_ultra_instagram_id',
							'meta_value'   => $id,
							'meta_compare' => '='
						));
						
						if (isset($users[0]->ID) && is_numeric($users[0]->ID) )
						{
							$returning = $users[0]->ID;
							$returning_user_login = $users[0]->user_login;
							
						} else {
							
							$returning = '';
						}
							
						
						// Authorize user
						if (is_user_logged_in()) 
						{
																				
							update_user_meta ($user_id, 'xoouser_ultra_instagram_id', $id);
							update_user_meta ($user_id, 'xoouser_ultra_social_signup', 7);
							
							//create basic widgets
							$xoouserultra->customizer->set_default_widgets_layout($user_id);															
							$this->login_registration_afterlogin();
						
						
						
						} else {
							
													
							
							//the user is NOT logged in							
							if ( $returning != '' ) 
							{
								
							
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$returning_user_login);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );	
									do_action('wp_login', $user->user_login, $user);		
											
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
						
								//redirect user
								$this->login_registration_afterlogin();
							
							} else if ($user != '' && username_exists($user)) {
								
								//user email exists then we have to sync								
								$user_id = username_exists( $user );
								$user = get_userdata($user_id);
								
								//$u_name = $this->get_social_services_name('instagram', $data->user);	
								
								update_user_meta ($user_id, 'xoouser_ultra_instagram_id', $id);
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 7);
								
								$u_user = $user->user_login;
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$u_user);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );
									do_action('wp_login', $user->user_login, $user);
									
									
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
																
								//redirect user
								$this->login_registration_afterlogin();
						
							
							} else { //user is new we have to create it
								
								//this is a new client we have to create the account								
								 $u_name = $this->get_social_services_name('instagram', $data->user);													
								 $u_email = $id;
								 
								//generat random password
								 $user_pass = wp_generate_password( 12, false);								 
								
								 $user_login = $this->unique_user('instagram', $data->user);
								 $user_login = sanitize_user ($user_login, true);	
								
								 //Build user data
								 $user_data = array (
												'user_login' => $user_login,
												'display_name' => $u_name,
												'user_email' => $u_email,																				
												'user_pass' => $user_pass
											);
											
														
								// Create a new user
								$user_id = wp_insert_user ($user_data);
								
								
								$this->social_login_hook($user_id, 7);
								
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 7);
								update_user_meta ($user_id, 'xoouser_ultra_instagram_id', $id);
								update_user_meta ($user_id, 'first_name', $u_name);
								update_user_meta ($user_id, 'display_name', $u_name);
								
																
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);	
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($user_login);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;							
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									do_action('wp_login', $user->user_login, $user);
									
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
																
								//redirect user
								$this->login_registration_afterlogin();
								
								
							}
						}
					}
					
				}
			
			}
		}
	}
	
		
	/******************************************
	Google auth 
	******************************************/
	function google_authorize()
	{
		global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;
		require_once(ABSPATH . 'wp-admin/includes/user.php' );
		
		if ( $xoouserultra->get_option('social_media_google') == 1 && $xoouserultra->get_option('google_client_id') && $xoouserultra->get_option('google_client_secret') && $xoouserultra->get_option('google_redirect_uri') ) 
		{
			
			if( isset( $_GET['code'] ) && isset($_REQUEST['uultraplus']) && $_REQUEST['uultraplus'] == '1' ) {
			
				//load google class
				$google = $this->load_google();

				if (isset($_SESSION['google_token'])) 
				{
					$gplus_access_token = $_SESSION['google_token'];
					
				} else {
					
					$google_token = $this->google->authenticate($_GET['code']);
					$_SESSION['google_token'] = $google_token;
					$gplus_access_token = $_SESSION['google_token'];
					
				}
				
				
											
				
				//check access token is set or not
				if ( !empty( $gplus_access_token ) ) 
				{
						
					// capture data
					$user_info = $this->googleplus->people->get('me');
					
					//print_r($user_info );
					$user_email = $this->googleoauth2->userinfo->get(); // to get email
					$user_info['email'] = $user_email['email'];					
					
					
					//if user data get successfully
					if (isset($user_info['id'])){
						
						$data['user'] = $user_info;
						
						//all data will assign to a session
						$_SESSION['google_user_cache'] = $data;
						
						//check if

						$users = get_users(array(
							'meta_key'     => 'xoouser_ultra_google_id',
							'meta_value'   => $user_info['id'],
							'meta_compare' => '='
						));
						
						if (isset($users[0]->ID) && is_numeric($users[0]->ID) )
						{
							$returning = $users[0]->ID;
							$returning_user_login = $users[0]->user_login;
							
						} else {
							
							$returning = '';
						}
						
						
							
						
						// Authorize user
						if (is_user_logged_in()) 
						{
							//	echo "HERE 1" . print_r($user_info) ;
						//	exit;
							
																				
							update_user_meta ($user_id, 'xoouser_ultra_google_id', $user_info['id']);
							
							//create basic widgets
							$xoouserultra->customizer->set_default_widgets_layout($user_id);															
							$this->login_registration_afterlogin();
					
						
						
						} else {
							
													
							
							//the user is NOT logged in							
							if ( $returning != '' ) 
							{
															
								$noactive = false;
								/*If alreayd exists*/
								$user = get_user_by('login',$returning_user_login);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );	
									do_action('wp_login', $user->user_login, $user);		
											
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
						
								//redirect user
								$this->login_registration_afterlogin();
							
							} else if ($user_info['email'] != '' && email_exists($user_info['email'])) {
								
								//user email exists then we have to sync								
								$user_id = email_exists( $user_info['email'] );
								$user = get_userdata($user_id);
								
								$u_name = $this->get_social_services_name('google', $user_info);													
								$u_email = $user_info['email'];
								 
								update_user_meta ($user_id, 'xoouser_ultra_google_id', $user_info['id']);
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 4);								
								update_user_meta ($user_id, 'first_name', $u_name);
								update_user_meta ($user_id, 'display_name', $u_name);
								
								wp_update_user( array( 'ID' => $user_id, 'display_name' => esc_attr($u_name) ) );								
																
								$u_user = $user->user_login;
								$noactive = false;
								
								/*If alreayd exists*/
								$user = get_user_by('login',$u_user);
								$user_id =$user->ID;
								
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
											
								}
								
								if(!$noactive)
								{
									 $secure = "";		
									//already exists then we log in
									wp_set_auth_cookie( $user_id, true, $secure );
									do_action('wp_login', $user->user_login, $user);
									
									
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
								
								//redirect user
								$this->login_registration_afterlogin();
						
							
							} else {
								
								//this is a new client we have to create the account								
								 $u_name = $this->get_social_services_name('google', $user_info);													
								 $u_email = $user_info['email'];
								 
								//generat random password
								 $user_pass = wp_generate_password( 12, false);								 
								
								 $user_login = $this->unique_user('google', $user_info);
								 $user_login = sanitize_user ($user_login, true);	
								
								 //Build user data
								 $user_data = array (
												'user_login' => $user_login,
												'display_name' => $u_name,
												'user_email' => $u_email,																				
												'user_pass' => $user_pass
											);
											
														
								// Create a new user
								$user_id = wp_insert_user ($user_data);
								
								$this->social_login_hook($user_id, 4);
								
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', 4);
								update_user_meta ($user_id, 'xoouser_ultra_google_id', $user_info['id']);
								update_user_meta ($user_id, 'first_name', $u_name);
								update_user_meta ($user_id, 'display_name', $u_name);
								
																
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);	
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($user_login);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;							
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									do_action('wp_login', $user->user_login, $user);
									
								}
								
								//create basic widgets
								$xoouserultra->customizer->set_default_widgets_layout($user_id);
																
								//redirect user
								$this->login_registration_afterlogin();
								
								
							}
						}
					}
					
				}
			
			}
		}
	}
	
	function get_social_services_name($service=null,$form=null)
	{
		if ($service)
		{
			if ($service == 'google')
			{
				//print_r($form);
				if (isset($form['name']) && is_array($form['name'])) 
				{
					$name = $form['name']['givenName'] . ' ' . $form['name']['familyName'];
					$username = $name;
					
				} elseif ( isset($form['displayName']) && !empty($form['displayName']) ) {
					
					$username = $form['displayName'];
					
				} else {
					
					$username = $form['id'];
				}
			}
			if ($service == 'twitter') {
				if (isset($form['screen_name']) && !empty($form['screen_name']) ) {
					$username = $form['screen_name'];
				}
			}
			
			if ($service == 'instagram') {
				if (isset($form->full_name) && !empty($form->full_name) ) 
				{
					$username = $form->full_name;
					
				}else{
					
					$username = $form->username;				
				}
			}
			if ($service == 'vk') {
				if (isset($form['screen_name']) && !empty($form['screen_name']) ) {
					$username = $form['screen_name'];
				} else {
					$username = $form['uid'];
				}
			}
			
			if ($service == 'yammer') {
				if (isset($form)  ) {
					$username = $form;
				}
			}
			
		}
		
		return $username;	
	
	
	}
	
	/******************************************
	friendly username
	******************************************/
	function clean_user($string){
		$string = strtolower($string);
		//$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//$string = preg_replace("/[\s-]+/", " ", $string);
		//$string = preg_replace("/[\s_]/", "_", $string);
		return $string;
	}
	
	/******************************************
	Make display_name unique
	******************************************/
	function unique_display_name($display_name){
		$r = str_shuffle("0123456789");
		$r1 = (int) $r[0];
		$r2 = (int) $r[1];
		$display_name = $display_name . $r1 . $r2;
		return $display_name;
	}
	
	/******************************************
	Make username unique
	******************************************/
	function unique_user($service=null,$form=null){
		if ($service)
		{
			if ($service == 'google') 
			{
				if (isset($form['name']) && is_array($form['name'])) 
				{
					$name = $form['name']['givenName'] . ' ' . $form['name']['familyName'];					
					$username = $this->clean_user($name);
					
				} elseif ( isset($form['displayName']) && !empty($form['displayName']) ) {
					
					//$username = $this->clean_user($form['displayName']);
					$username = $form['displayName'];
					
				} else {
					$username = $form['id'];
				}
				
			}
			
			if ($service == 'facebook') {
				if (isset($form['name'])) {
					$name = $form['name'];
					$username = $this->clean_user($name);
				} elseif ( isset($form['display_name']) ) {
					$username = $this->clean_user($form['display_name']);
				} else {
					$username = $form['id'];
				}
			}
			
			if ($service == 'twitter') {
				if (isset($form['screen_name']) && !empty($form['screen_name']) ) {
					$username = $form['screen_name'];
				}
			}
			
			if ($service == 'instagram') {
				if (isset($form->username) && !empty($form->username) ) {
					$username = $form->username;
				}
			}
			if ($service == 'vk') {
				if (isset($form['screen_name']) && !empty($form['screen_name']) ) {
					$username = $form['screen_name'];
				} else {
					$username = $form['uid'];
				}
			}
		}
		
		// make sure username is unique
		if (username_exists($username)){
			$r = str_shuffle("0123456789");
			$r1 = (int) $r[0];
			$r2 = (int) $r[1];
			$username = $username . $r1 . $r2;
		}
		/*if (username_exists($username)){
			$r = str_shuffle("0123456789");
			$r1 = (int) $r[0];
			$r2 = (int) $r[1];
			$username = $username . $r1 . $r2;
		}*/
		return $username;
	}
	
	 /*Handle PayPal Login*/
	public function handle_paypal_login() 
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		//get user with key		
		$key = $_GET['usersultraipncall'];
		
		$order = $xoouserultra->order->get_order_confirmed($key);
		
		if($order->order_user_id!="")
		{
			$account_page_id = get_option('xoousersultra_my_account_page');
		    $my_account_url = get_permalink($account_page_id);
			wp_set_auth_cookie( $order->order_user_id, true, $secure );
			wp_redirect($my_account_url);			
			exit;
			
		}
		
	
	}
	
	 /*Auto redirect login*/
	public function auto_redirection_on_login($atts) 
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		extract( shortcode_atts( array(	
			
			'redirect_to' => '', //any web URL							
			
		), $atts ) );
		
		
		if($redirect_to!='')		
		{
			$my_account_url = $redirect_to;		
		
		}else{		
			
			$account_page_id = get_option('xoousersultra_my_account_page');
			$my_account_url = get_permalink($account_page_id);
		
		}		
		
		wp_redirect($my_account_url);			
		exit;

	
	}
	
	 /*Handle LinkedIn Sign up*/
	public function handle_linkedin_authorization() 
	{
		session_start();
		
		global $xoouserultra ;
		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(xoousers_path."libs/linkedin/oauth/linkedinoauth.php"); 
		
			 
		 //get oauttokens
		 $temp_user_session_id = session_id();			
				
		 $oauthstate =  get_option('uultra_linkedin_'.$temp_user_session_id);
		 
		 //get access token and access token secret		 
		 $oauthstate = $xoouserultra->get_linkedin_access_token($oauthstate);	
		 
		 	 
	 
	
		if (!$oauthstate)
		{
			echo "empty ";
		
		}else{
		
		
			// We've been given some access tokens, so try and use them to make an API call, and
			// display the results.
			
			$accesstoken = $oauthstate['access_token'];
			$accesstokensecret = $oauthstate['access_token_secret'];
			
			//print_r($oauthstate);
	
			$to = new LinkedInOAuth(
				$xoouserultra->get_option('social_media_linkedin_api_public'),
				$xoouserultra->get_option('social_media_linkedin_api_private'),
				$accesstoken,
				$accesstokensecret
			);
			
			$find_person = ':(id,first-name,last-name,email-address)';
			$profile_result = $to->oAuthRequest('http://api.linkedin.com/v1/people/~'.$find_person);
			$profile_data = simplexml_load_string($profile_result);
			
			
			//print htmlspecialchars(print_r($profile_data, true));
		   
			   
			$profile_data = json_decode( json_encode($profile_data) , 1);
			
									
			$u_name = $profile_data["first-name"] ;
			$lname = $profile_data["last-name"] ;
			$u_email = $profile_data["email-address"] ;
			$u_linked_in_id = $profile_data["id"] ;
			
			$u_user = $u_name."-".$lname;
			
			
			//print_r($profile_data);
			//exit();
			//Sanitize Login
			 $user_login = str_replace ('.', '-', $u_user);
			 $user_login = sanitize_user ($user_login, true);
			 
			
			//check if already registered
			$exists = email_exists($u_email);
			if(!$exists)
			{
				  //lets create
				  
				 //generat3 random password
				 $user_pass = wp_generate_password( 12, false);
				 
				 //Build user data
				 $user_data = array (
								'user_login' => $user_login,
								'display_name' => (!empty ($u_name) ? $u_name : $u_user),
								'user_email' => $u_email,																				
								'user_pass' => $user_pass
							);
	
				// Create a new user
				$user_id = wp_insert_user ($user_data);
				
				$this->social_login_hook($user_id, 2);
				
				
				if ( ! $user_id ) 
				{
				
				}else{
					    
						$verify_key = $this->get_unique_verify_account_id();					
						update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);					
						update_user_meta ($user_id, 'xoouser_ultra_social_signup', 2);						
						update_user_meta ($user_id, 'xoouser_linked_in_id', $u_linked_in_id);
						
						update_user_meta ($user_id, 'first_name', $u_name);
						update_user_meta ($user_id, 'last_name', $lname);
						
						//set account status
						$this->user_account_status($user_id);
						
						//notify depending on status
						$this->user_account_notify($user_id, $u_email, $user_login, $user_pass);
						
						
				}
				
				
			}else{
				
				//if user already created then try to login automatically				
				//$user = get_user_by('login',$user_login);				
				//$user_id =$user->ID;
				
				
				//if user already created then try to login automatically				
				$users = get_users(array(
							'meta_key'     => 'xoouser_linked_in_id',
							'meta_value'   => $u_linked_in_id,
							'meta_compare' => '='
				));
				
				
				if (isset($users[0]->ID) && is_numeric($users[0]->ID) )
				{
					$returning = $users[0]->ID;
					$user_login = $users[0]->user_login;
					
					$user = get_user_by('login',$user_login);				
					$user_id =$user->ID;
					
					
				} else {
					
					//get by using email, we already know the user exists at this point
					$user = get_user_by('email',$u_email);				
					$user_id =$user->ID;
					
					update_user_meta ($user_id, 'xoouser_ultra_social_signup', 2);
					update_user_meta ($user_id, 'xoouser_linked_in_id', $u_linked_in_id);
					
					//set account status
					$this->user_account_status($user_id);				
					
					$returning = '';
				}
				
				
				
				
				
				if(is_active($user_id))
				{
					//is active then login
					wp_set_auth_cookie( $user_id, true, $secure );	
					do_action('wp_login', $user->user_login, $user);		
				
				}else{
					
					$this->errors[] = __('<strong>ERROR:</strong> YOUR ACCOUNT IS NOT ACTIVE YET.','users-ultra');
					
				}
				
			
			}
			
		//create basic widgets
		$xoouserultra->customizer->set_default_widgets_layout($user_id);
			
		//redirect
		$this->login_registration_afterlogin();
		
				
	  }
	
	}
	
	public function login_registration_afterlogin_facebook()
	{
		global $xoouserultra, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/load.php');
		
		
		if (isset($_REQUEST['redirect_to']))
		{
			$url = $_REQUEST['redirect_to'];
				
		} elseif (isset($_POST['redirect_to'])) {
		
			$url = $_POST['redirect_to'];
				
		} else {		
			
				
				$redirect_registration_when_social = $xoouserultra->get_option('redirect_registration_when_social');
			    $redirect_social_page = $xoouserultra->get_option('redirect_after_registration_login_social');
				
			
				if($this->mIsSocialLogin && $redirect_registration_when_social==1 && $redirect_social_page!='') //special rules for social login
				{	
					
							
					$url = get_page_link($redirect_social_page);		
					
					if($url=="")
					{						
						$url=$this->get_my_account_direct_link_checking();					
					}					
				
				
				}else{ //this is a common login method NOT social
				
					
					$redirect_custom_page = $xoouserultra->get_option('redirect_after_registration_login');				
					$url = get_page_link($redirect_custom_page);
					
					if($url=='' || $redirect_custom_page=='')
					{						
						//check redir		
						$account_page_id = get_option('xoousersultra_my_account_page');				
						$my_account_url = get_page_link($account_page_id);				
						
						if($my_account_url=="")
						{
							$url = $_SERVER['REQUEST_URI'];
						
						}else{
							
							$url = $my_account_url;				
						
						}
					
					}						
				
				
				}			
				
		}		
		
		return $url;
	
	}
	
	public function login_registration_afterlogin()
	{
		global $xoouserultra, $wp_rewrite, $blog_id ; 
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/load.php');
		
		
		if (isset($_REQUEST['redirect_to']))
		{
			$url = $_REQUEST['redirect_to'];
				
		} elseif (isset($_POST['redirect_to'])) {
		
			$url = $_POST['redirect_to'];
				
		} else {		
			
				
				$redirect_registration_when_social = $xoouserultra->get_option('redirect_registration_when_social');
			    $redirect_social_page = $xoouserultra->get_option('redirect_after_registration_login_social');
				
			
				if($this->mIsSocialLogin && $redirect_registration_when_social==1 && $redirect_social_page!='') //special rules for social login
				{	
					
							
					$url = get_page_link($redirect_social_page);		
					
					if($url=="")
					{						
						$url=$this->get_my_account_direct_link_checking();					
					}					
				
				
				}else{ //this is a common login method NOT social
				
					
					$redirect_custom_page = $xoouserultra->get_option('redirect_after_registration_login');				
					$url = get_page_link($redirect_custom_page);
					
					if($url=='' || $redirect_custom_page=='')
					{						
						//check redir		
						$account_page_id = get_option('xoousersultra_my_account_page');				
						$my_account_url = get_page_link($account_page_id);				
						
						if($my_account_url=="")
						{
							$url = $_SERVER['REQUEST_URI'];
						
						}else{
							
							$url = $my_account_url;				
						
						}
					
					}						
				
				
				}			
				
		}		
		
		wp_redirect( $url );
		exit();
	
	}
	
	public function get_my_account_direct_link_checking()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');		
		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);				
		return $my_account_url;
	
	}
	
		
	public function get_my_account_direct_link()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		//$my_account_url = "";
		if($my_account_url =="")
		{
			$web_url = site_url()."/";
			//get my account slug
			$my_account_slug = 	$xoouserultra->get_option('usersultra_my_account_slug');
			$my_account_url = $web_url.$my_account_slug ."/";
		
		}
		
		
		return $my_account_url;
	
	}
	
	public function get_login_page_direct_link()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
		//$account_page_id = get_option('xoousersultra_my_account_page');
		$account_page_id = $xoouserultra->get_option('login_page_id');
		
		$my_account_url = get_permalink($account_page_id);
		
		//tweak added on 11-16-2014		
		//$my_account_url = "";
		if($my_account_url =="")
		{
			$web_url = site_url()."/";
			//get login slug
			$my_account_slug = 	$xoouserultra->get_option('usersultra_login_slug');
			$my_account_url = $web_url.$my_account_slug ."/";		
		}
		
		
		
		
		return $my_account_url;
	
	}
		
	 /*Handle Facebook Sign up*/
	public function handle_social_facebook() 
	{
		global $xoouserultra ;	
	
		 $u_name = $_POST['name'];
		 $u_email = $_POST['email'];
		 $u_fb_id = $_POST['id'];
		 
		 $this->mIsSocialLogin = true;
						
		 // Setting default to false;
		 $this->errors = false;
			 
		if($u_name !='' && $u_email !='' && $u_fb_id !='' ) 
		{								
		
			 			 
			 $fbid = $u_fb_id;			
			
			 $u_user = $this->unique_user('facebook', $u_name);
		    
			 
			 //Sanitize Login
			 $user_login = sanitize_user ($u_user, true);				 
			 
			 //check if already registered
			  $exists = email_exists($u_email);
			  if(!$exists)
			  {
				  //lets create
				  
				 //generat random password
				 $user_pass = wp_generate_password( 12, false);
				 
				 //Build user data
				 $user_data = array (
								'user_login' => $user_login,
								'display_name' => (!empty ($u_name) ? $u_name : $u_user),
								'user_email' => $u_email,																				
								'user_pass' => $user_pass
							);
	
				// Create a new user
				$user_id = wp_insert_user ($user_data);				
				
				$this->social_login_hook($user_id, 1); 
				
				
				if ( ! $user_id ) 
				{
					
									
				}else{		
					
						update_user_meta ($user_id, 'xoouser_ultra_social_signup', 1);
						update_user_meta ($user_id, 'xoouser_ultra_facebook_id', $u_fb_id);	
						
						$verify_key = $this->get_unique_verify_account_id();					
						update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);						
						update_user_meta ($user_id, 'first_name', $u_name);											
						
						//set account status
						$this->user_account_status($user_id);
						
						//notify depending on status
						$this->user_account_notify($user_id, $u_email, $user_login, $user_pass);
						
						$creds['user_login'] = sanitize_user($user_login);				
						$creds['user_password'] = $user_pass;
						$creds['remember'] = 1;							
								
						$noactive = false;
						if(!$this->is_active($user_id) && !is_super_admin($user_id))
						{
							$noactive = true;
									
						}
						
												
						if(!$noactive)
						{
							$user = wp_signon( $creds, false );
							do_action('wp_login', $user->user_login, $user);
									
						}
						
						//create basic widgets						
						$xoouserultra->customizer->set_default_widgets_layout($user_id);
						
						//redirect user
						$redir_url = $this->login_registration_afterlogin_facebook();			
						
						
				}
				
				
			}else{
				
				//if user already created then try to login automatically				
				$users = get_users(array(
							'meta_key'     => 'xoouser_ultra_facebook_id',
							'meta_value'   => $u_fb_id,
							'meta_compare' => '='
				));
				
				if (isset($users[0]->ID) && is_numeric($users[0]->ID) )
				{
					$returning = $users[0]->ID;
					$user_login = $users[0]->user_login;
					
					$user = get_user_by('login',$user_login);				
					$user_id =$user->ID;
					
					
				} else {
					
					//get by using email, we already know the user exists at this point
					$user = get_user_by('email',$u_email);				
					$user_id =$user->ID;
					
					update_user_meta ($user_id, 'xoouser_ultra_social_signup', 1);
					update_user_meta ($user_id, 'xoouser_ultra_facebook_id', $u_fb_id);
					
					//set account status
					$this->user_account_status($user_id);				
					
					$returning = '';
				}
				
				
				
				if($this->is_active($user_id))
				{
					//is active then login
					wp_set_auth_cookie( $user_id, true, $secure );	
					do_action('wp_login', $user->user_login, $user);		
				
				}else{
					
					$this->errors[] = __('<strong>ERROR:</strong> YOUR ACCOUNT IS NOT ACTIVE YET.','users-ultra');
					
				}
				
				//create basic widgets
				$xoouserultra->customizer->set_default_widgets_layout($user_id);
				
				//redirect user
				$redir_url = $this->login_registration_afterlogin_facebook();		
			
			
			
			}
				
			
		}else{ //end if fb user is not vallid
			
			//error from facebook side
			
			$this->errors[] = __('<strong>ERROR:</strong> SOME PROBLEM HAPPENED WHILE CONNECTING FACEBOOK PLEASE LOGIN AGAIN.','users-ultra');
		
		}
		
		
				
		//redirect user
		echo $redir_url ;
		die();
	
	
			
  }
  
  public function get_unique_verify_account_id()
  {
	  $rand = $this->genRandomString(8);
	  $key = session_id()."_".time()."_".$rand;
	  
	  return $key;
	  
	 
  }
  
  public function genRandomString($length) 
  {
		
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
  
  /*---->> Set Account Status  ****/  
  public function user_account_status($user_id) 
  {
	  global $xoouserultra, $uultra_group;
	  
	  //check if login automatically
	  $activation_type= $xoouserultra->get_option('registration_rules');
	  
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
	  
	  
	  $activation_type_social_rule= $xoouserultra->get_option('social_login_activation_type');	  
	  
	  if($activation_type_social_rule=='yes' || $activation_type_social_rule=='')
	  {		  
		  //special rule for social registration added on 08-08-2014	  
		  if($this->mIsSocialLogin)
		  {
			  //automatic activation
			  update_user_meta ($user_id, 'usersultra_account_status', 'active');
			  
		  }
	  }
	  
	  //special rule for social registration
	  if($xoouserultra->get_option('uultra_roles_automatic_set_social')=='yes')
	  {
		   if($this->mIsSocialLogin)
		   {
			   //get custom role for social
			   $new_role = $xoouserultra->get_option('uultra_roles_automatic_set_role_social');
							
			   //set custom role for this user
			   if($new_role!="")
			   {
					$user = new WP_User( $user_id );
					$user->set_role( $new_role );						
			   }
			
			}					
	   }	   
	   
	   //special rule for social registration for groups
	  if($xoouserultra->get_option('uultra_groups_automatic_set_social')=='yes' && isset($uultra_group))
	  {
		   if($this->mIsSocialLogin)
		   {
			  //set custom group.					
			 $group_to_assign = $xoouserultra->get_option('uultra_groups_automatic_set_group_social');	
							
			   //set custom role for this user
			   if($group_to_assign!="")
			   {				   
				   $uultra_group->save_user_group_rel($user_id, $group_to_assign);		
											
			   }
			
			}					
	   }  
	   
	   
	   //set visitor ip
	   //update visitor ip 11/17/2014
		$visitor_ip = $_SERVER['REMOTE_ADDR'];
		update_user_meta($user_id, 'uultra_user_registered_ip', $visitor_ip);
	  
	  
	
  }
  
  
  
   /*---->> re send activation link ****/  
  public function user_resend_activation_link($user_id, $u_email, $user_login) 
  {
	  global $xoouserultra;
	  
	  //require_once(ABSPATH . 'wp-includes/pluggable.php');
	  require_once(ABSPATH . 'wp-includes/link-template.php');
	  
	     //email activation link				  
		  $web_url =$this->get_my_account_direct_link();	
		  		  
		  $current_url =$_SERVER['REQUEST_URI'];		  
		  $pos = strpos($current_url, "page_id");		  
		  
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
		  $xoouserultra->messaging->re_send_activation_link($u_email, $user_login, $activation_link);
		  
   }
  
  /*---->> Notify User ****/  
  public function user_account_notify($user_id, $u_email, $user_login, $user_pass) 
  {
	  global $xoouserultra;
	  
	  //require_once(ABSPATH . 'wp-includes/pluggable.php');
	  require_once(ABSPATH . 'wp-includes/link-template.php');
	  
	  //check if login automatically
	  $activation_type= $xoouserultra->get_option('registration_rules');
	  
	  if($activation_type==1)
	  {
		  //automatic activation
		  $xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
		  						
	  
	  }elseif($activation_type==2){
		  
		  //email activation link				  
		  $web_url =$this->get_my_account_direct_link();
		  		  
		  //get uri
		  $current_url =$_SERVER['REQUEST_URI'];
		  
		  $pos = strpos($current_url, "page_id");	
		  $unique_key = get_user_meta($user_id, 'xoouser_ultra_very_key', true);

			  
		  if ($pos === false) // this is a tweak that applies when not Friendly URL is set. NOT found
		  {
				$activation_link = $web_url."?act_link=".$unique_key;
					
		  } else {
			  
			 // found then we're using seo links					 
			 $activation_link = $web_url."&act_link=".$unique_key;
					
		  }
		  
		  //send link to user
		  $xoouserultra->messaging->welcome_email_with_activation($u_email, $user_login, $user_pass, $activation_link);
		  
	  
	  }elseif($activation_type==3){	
	  	  
		  //admin approval
		   $xoouserultra->messaging->welcome_email_with_admin_activation($u_email, $user_login, $user_pass, $activation_link);
		  
		  
		  
		 
	  
	  
	  }
	
  }
  
   /*Handle Account Email Confirmation*/
	public function handle_account_conf_link() 
	{
		global $xoouserultra ;
			
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$act_link = $_GET["act_link"];
		
		if(isset($act_link) && $act_link!="")
		{
			//get user with meta
			$user = $this->get_user_with_key($act_link);
			
			if($user!="error")
			{
				$secure ="";
				//activate user
				$user_id = $user->ID;
				$user_email = $user->user_email;
				
				update_user_meta ($user_id, 'usersultra_account_status', 'active');
				
				//notify				
				$xoouserultra->messaging->confirm_verification_sucess($user_email);
			
				//login user and take them to account				
				wp_set_auth_cookie( $user_id, true, $secure );				
				$this->login_registration_afterlogin();
			
			
			}else{
				
				//wrong key, display message at the screen
				
			
			
			}
		
		
		}
		
	
	}
	
	//get user with kewy - used for confirmation link only
	public function get_user_with_key( $uniquekey )
	{
		global  $wpdb,  $xoouserultra;
		
		$args = array(
		
			'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'usersultra_account_status',
						'value' => 'pending',
						'compare' => '='
					),
					array(
						'key' => 'xoouser_ultra_very_key',
						'value' => $uniquekey ,
						'compare' => '='
					),
			
			)
		);


		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
		
		if(!empty($res)) 
		{
			
			foreach ( $res as $user )
			{
				return $user;
			
			
			}
		
		
		}else{
			
			return "error";
			
		}
			
		
	}
    
   /*Handle Yahoo*/
	public function handle_social_google() 
	{
		global $xoouserultra ;
			
		//require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(xoousers_path."libs/openid/openid.php");
		
		
		 
		 $web_url = site_url();
		 
		 $openid = new LightOpenID($web_url);		 
		  
		 
		 if ($openid->mode) 
		 {
						  
			  $data = $openid->getAttributes();
			
			  if ($openid->mode == 'cancel') 
			  {
			   
			  }elseif($data["contact/email"]!="") {
				  
				  $openid->validate();
				  
				  $redir_url ="";
				  
				  
				   //authentication authorized
				  
				   $data = $openid->getAttributes();
				   $email = $data['contact/email'];
				  
				   $a =  $openid->identity ;
				   
				   //validate
					$type = 4; //google
					
					if (strpos($a,'yahoo') !== false) 
					{
						$first = $data['namePerson'];
						$type = 3; //yahoo
						
						$user_full_name = trim ($first);
											
					
					}else{
						 $first = $data['namePerson/first'];
				  		 $last_n = $data['namePerson/last'];		   
						
						$user_full_name = trim ($first." ".$last_n);
						
					}
					
					
					
					//save
					 $u_user = $user_full_name;
					 $u_name = $first;
					 $u_email = $email;					
					 
					 			 //check if already registered
					  $exists = email_exists($u_email);
					  if(!$exists)
					  {
					 
						 //generat random password
						 $user_pass = wp_generate_password( 12, false);
						 
						 //Sanitize Login
						 $user_login = str_replace ('.', '-', $u_user);
						 $user_login = sanitize_user ($u_user, true);	 
						
						 //Build user data
						 $user_data = array (
										'user_login' => $user_login,
										'display_name' => (!empty ($u_name) ? $u_name : $u_user),
										'user_email' => $u_email,																				
										'user_pass' => $user_pass
									);
									
												
						// Create a new user
						$user_id = wp_insert_user ($user_data);
						
						if ( ! $user_id ) 
						{
						
						}else{
								
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', $type);
								
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);								
								
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($u_user);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;		
								
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									do_action('wp_login', $user->user_login, $user);
									
								}
							
								
						}
						
						
					}else{
						
						$noactive = false;
						/*If alreayd exists*/
						$user = get_user_by('login',$u_user);
						$user_id =$user->ID;
						
						if(!$this->is_active($user_id) && !is_super_admin($user_id))
						{
							$noactive = true;
									
						}
						
						if(!$noactive)
						{
							 $secure = "";		
							//already exists then we log in
							wp_set_auth_cookie( $user_id, true, $secure );	
							do_action('wp_login', $user->user_login, $user);		
									
						}
									
					
					
					}
							
							
					   
			  }
			
		 
		 
		 }
		 
		 //create basic widgets
		$xoouserultra->customizer->set_default_widgets_layout($user_id);
		 
		 $this->login_registration_afterlogin();
		
			
  }
	
	
	
	/*Get errors display*/
	function get_errors()
	 {
		global $xoouserultra;
		
		$display = null;
		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="xoouserultra-errors">';
		
			foreach($this->errors as $newError) 
			{
				
				$display .= '<span class="xoouserultra-error xoouserultra-error-block"><i class="xoouserultra-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		
		
		} else {
			
			if (isset($_REQUEST['redirect_to']))
			{
				$url = $_REQUEST['redirect_to'];
				
			} elseif (isset($_POST['redirect_to'])) {
				
				$url = $_POST['redirect_to'];
				
			} else {
				
				$url = $_SERVER['REQUEST_URI'];
			}
			wp_redirect( $url );
		}
		return $display;
	}

}
$key = "login";
$this->{$key} = new XooUserLogin();