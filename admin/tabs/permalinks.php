<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;
	
?>
<h3><?php _e('Permalinks','users-ultra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />
<input type="hidden" name="update_uultra_slugs"  value="uultra_slugs"/>


<div class="user-ultra-sect ">
  <h3><?php _e('Users Ultra Pages Setting','users-ultra'); ?></h3>
  
  <p><?php _e('The following pages are automatically created when Users Ultra Plugin
   is activated. You can leave them as they are or change to custom pages here.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
            'select',
            'xoousersultra_my_account_page',
            __('Users Ultra My Account','users-ultra'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_my_account]</code> shortcode on this page.','users-ultra'),
            __('This page is where users will view their account.','users-ultra')
    );
	
    $this->create_plugin_setting(
            'select',
            'profile_page_id',
            __('Users Ultra Profile Page','users-ultra'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_profile]</code> shortcode on this page.','users-ultra'),
            __('This page is where users will view their own profiles, or view other user profiles from the member directory if allowed.','users-ultra')
    );
    
    $this->create_plugin_setting(
            'select',
            'login_page_id',
            __('Users Ultra Login Page','users-ultra'),
            $this->get_all_sytem_pages(),
            __('If you wish to change default Users Ultra login page, you may set it here. Make sure you have the <code>[usersultra_login]</code> shortcode on this page.','users-ultra'),
            __('The default front-end login page.','users-ultra')
    );
    
    $this->create_plugin_setting(
            'select',
            'registration_page_id',
            __('Users Ultra Registration Page','users-ultra'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_registration]</code> shortcode on this page.','users-ultra'),
            __('The default front-end Registration page where new users will sign up.','users-ultra')
    );
	
	$this->create_plugin_setting(
            'select',
            'directory_page_id',
            __('Users Ultra Directory Page','users-ultra'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_directory]</code> shortcode on this page.','users-ultra'),
            __('The default front-end Registration page where new users will sign up.','users-ultra')
    );
	
	
	
	 
    
    
?>
</table>

  
</div>



<div class="user-ultra-sect ">

  
  <h3><?php _e('Users Ultra offers you the ability to create a custom URL structure for the main pages of your website','users-ultra'); ?></h3>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'usersultra_slug',
        __('Profile Slug','users-ultra'),array(),
        __('Enter your custom Slug for the profile page.','users-ultra'),
        __('Enter your custom Slug for the profile page.','users-ultra')
);



$this->create_plugin_setting(
	'select',
	'usersultra_permalink_type',
	__('Profile Link Field','users-ultra'),
	array(
	    'ID' => __('The Profile Link is built by using the User ID','users-ultra'),
		'username' => __('The Profile Link is built by using the Username','users-ultra') 
		
		
		),
		
	__('Please note: This option is used for permalinks. You can choose what field will be used in the Users Profile Link','users-ultra'),
  __('Please note: This option is used for permalinks. You can choose what field will be used in the Users Profile Link','users-ultra')
       );
    

$this->create_plugin_setting(
        'input',
        'usersultra_login_slug',
        __('Login Slug','users-ultra'),array(),
        __('Enter your custom Slug for the Login Page.','users-ultra'),
        __('Enter your custom Slug for the Login Page.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'usersultra_registration_slug',
        __('Registration Slug','users-ultra'),array(),
        __('Enter your custom Slug for the Registration Page.','users-ultra'),
        __('Enter your custom Slug for the Registration Page.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'usersultra_my_account_slug',
        __('My Account Slug','users-ultra'),array(),
        __('Enter your custom Slug for the "My Account" Page.','users-ultra'),
        __('Enter your custom Slug for the "My Account" Page.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'usersultra_directory_slug',
        __('Users Directory Slug','users-ultra'),array(),
        __('Enter your custom Slug for the Users Directory Page.','users-ultra'),
        __('Enter your custom Slug for the Users Directory Page.','users-ultra')
);


$this->create_plugin_setting(
        'input',
        'uultra_custom_module_slug',
        __('Custom Module Slug','users-ultra'),array(),
        __('Enter your custom Slug for the Dashboard Modules. Use this option only if there is a conflict with other plugins using the GET parameter with the name "module". Example "?module". <strong>WARNING!! </strong> Do not fill this field out if you are not sure about how to use it.','users-ultra'),
        __('Enter your custom Slug for the Dashboard Modules.','users-ultra')
);

		
?>
</table>

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />
	
</p>

</form>