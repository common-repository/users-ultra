<?php
/*
Plugin Name: Users Ultra Pro 3.0
Plugin URI: https://usersultra.com
Description: This is a powerful user profiles plugin for WordPress. This versatile plugin allows you to create user communities in few minutes. It comes with tons of useful shortcodes which give you the capability to customize any WordPress Theme.
Tested up to: 5.8
Version: 3.1.0
Author: Users Ultra Pro
Author URI: https://usersultra.com/users-pro.html
Domain Path: /languages
Text Domain: users-ultra
*/
define('xoousers_url',plugin_dir_url(__FILE__ ));
define('xoousers_path',plugin_dir_path(__FILE__ ));
define('xoousers_template','basic');
define('MY_PLUGIN_SETTINGS_URL',"?page=userultra&tab=pro");

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$plugin_path = '';

// Get plugin version from header
function xoousersultra_get_plugin_version()
{
    $default_headers = array( 'Version' => 'Version' );
    $plugin_data = get_file_data( __FILE__, $default_headers, 'plugin' );
    return $plugin_data['Version'];
}


$plugin = plugin_basename(__FILE__);

// Auto updates
if (is_admin())
{
	
// add_action('init', '__wuultra__wpuultra_pluign_au');
 
}

function __wuultra__wpuultra_pluign_au()
{
	require_once ('wp_autoupdate.php');
	
	$va = get_option('uultra_c_key');
	$wptuts_plugin_current_version = '1.3.4';
	$wptuts_plugin_remote_path = 'https://www.usersultra.com/upgrades/uultra-plugin-update.php?serial_n='.$va;
	
	$wptuts_plugin_slug = plugin_basename(__FILE__);
	new __wuultra__wpuultrapro_plugin_auto_update ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}

/* Loading Function */
require_once (xoousers_path . 'functions/functions.php');

/* Init */
define('uultraxoousers_pro_url','https://usersultra.com/');

function xoousers_load_textdomain() 
{     	   
	   $locale = apply_filters( 'plugin_locale', get_locale(), 'users-ultra' );	   
       $mofile = xoousers_path . "languages/users-ultra-$locale.mo";
			
		// Global + Frontend Locale
		load_textdomain( 'users-ultra', $mofile );
		load_plugin_textdomain( 'users-ultra', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}

/* Load plugin text domain (localization) */
add_action('init', 'xoousers_load_textdomain');	
		
add_action('init', 'xoousers_output_buffer');
function xoousers_output_buffer() {
		ob_start();
}

/* Master Class  */
require_once (xoousers_path . 'xooclasses/xoo.userultra.class.php');

// Helper to activate a plugin on another site without causing a fatal error by

register_activation_hook( __FILE__, 'uultra_activation');
 
function  uultra_activation( $network_wide ) 
{
	$plugin = "users-ultra-pro/xoousers.php";	
	
	if ( is_multisite() && $network_wide ) // See if being activated on the entire network or one blog
	{ 
		activate_plugin($plugin_path,NULL,true);
			
		
	} else { // Running on a single blog		   	
			
		activate_plugin($plugin_path,NULL,false);		
		
	}
    
    add_option('uultra_plugin_do_activation_redirect_pro', true);
}



$xoouserultra = new XooUserUltra();
$xoouserultra->plugin_init();
/* load addons */
require_once xoousers_path . 'addons/photocategories/index.php';

$activate_badges = $xoouserultra->get_option('uultra_add_ons_medallions');
if($activate_badges=='yes' || $activate_badges == '')
{
	require_once xoousers_path . 'addons/badges/index.php';
}

$activate_ip_defender = $xoouserultra->get_option('uultra_add_ons_ip_defender');
if($activate_ip_defender=='yes' || $activate_ip_defender == '')
{
	require_once xoousers_path . 'addons/defender/index.php';
}

$activate_groups = $xoouserultra->get_option('uultra_add_ons_groups');
if($activate_groups=='yes' || $activate_groups == '')
{
	require_once xoousers_path . 'addons/groups/index.php';
	
}

require_once xoousers_path . 'addons/maintenance/index.php';
require_once xoousers_path . 'addons/forms/index.php';
require_once xoousers_path . 'addons/wall/index.php';

add_action('admin_init', 'uultra_my_plugin_redirect_pro');
	

function uultra_my_plugin_redirect_pro() 
{
	if (get_option('uultra_plugin_do_activation_redirect_pro', false)) {
		delete_option('uultra_plugin_do_activation_redirect_pro');
		wp_redirect(MY_PLUGIN_SETTINGS_URL);
   }
}