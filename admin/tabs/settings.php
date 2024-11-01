<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $xoouserultra;
$profile_customizing = array();

?>
<h3><?php _e('General Settings','users-ultra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<?php
global $xoouserultra, $uultra_group;

$activate_groups = $xoouserultra->get_option('uultra_add_ons_groups');
 
?>


<div id="tabs-uultra" class="uultra-multi-tab-options">
    
    <?php 
    
      echo $xoouserultra->xoouseradmin->check_pro_version_features_message();
    ?>

<ul class="nav-tab-wrapper uultra-nav-pro-features">
<li class="nav-tab uultra-pro-li"><a href="#tabs-1" title="<?php _e('General','users-ultra'); ?>"><?php _e('General','users-ultra'); ?></a></li>
<li class="nav-tab uultra-pro-li"><a href="#tabs-social-media" title="<?php _e('Social Media','users-ultra'); ?>"><?php _e('Social Media','users-ultra'); ?> </a></li>
<li class="nav-tab uultra-pro-li"><a href="#tabs-registration" title="<?php _e('Registration','users-ultra'); ?>"><?php _e('Registration','users-ultra'); ?> </a></li>

<li class="nav-tab uultra-pro-li"><a href="#tabs-redirections" title="<?php _e('Redirections','users-ultra'); ?>"><?php _e('Redirections','users-ultra'); ?> </a></li>

<li class="nav-tab uultra-pro-li"><a href="#tabs-front-end-publisher" title="<?php _e('Front-End Publisher','users-ultra'); ?>"><?php _e('Front-End Publisher','users-ultra'); ?> </a></li>

<li class="nav-tab uultra-pro-li"><a href="#tabs-activity-wall" title="<?php _e('Activity Wall','users-ultra'); ?>"><?php _e('Activity Wall','users-ultra'); ?> </a></li>

<li class="nav-tab uultra-pro-li"><a href="#tabs-privacy" title="<?php _e('Privacy','users-ultra'); ?>"><?php _e('Privacy','users-ultra'); ?> </a></li>


<?php 

if($activate_groups=='yes' || $activate_groups == '')
{
?>
<li class="nav-tab uultra-pro-li"><a href="#tabs-groups" title="<?php _e('Groups','users-ultra'); ?>"><?php _e('Groups','users-ultra'); ?> </a></li>

<?php }?>

<li class="nav-tab uultra-pro-li"><a href="#tabs-add-ons" title="<?php _e('Add-ons','users-ultra'); ?>"><?php _e('Add-ons','users-ultra'); ?> </a></li>



</ul>


<div id="tabs-1">
<div class="user-ultra-sect ">
  <h3><?php _e('Miscellaneous  Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
                'checkbox',
                'hide_admin_bar',
                __('Hide WP Admin Tool Bar','users-ultra'),
                '1',
                __('If checked, User will not see the WP Admin Tool Bar','users-ultra'),
                __('If checked, User will not see the WP Admin Tool Bar.','users-ultra')
        ); 
		
		 $data = array(
		 				'm/d/Y' => date('m/d/Y'),
                        'm/d/y' => date('m/d/y'),
                        'Y/m/d' => date('Y/m/d'),
                        'dd/mm/yy' => date('d/m/Y'),
                        'Y-m-d' => date('Y-m-d'),
                        'd-m-Y' => date('d-m-Y'),
                        'm-d-Y' => date('m-d-Y'),
                        'F j, Y' => date('F j, Y'),
                        'j M, y' => date('j M, y'),
                        'j F, y' => date('j F, y'),
                        'l, j F, Y' => date('l, j F, Y')
                    );
		
		
		$this->create_plugin_setting(
            'select',
            'uultra_date_format',
            __('Date Format:','users-ultra'),
            $data,
            __('Select the date format to be used on Users Ultra','users-ultra'),
            __('Select the date format to be used on Users Ultra','users-ultra')
    );
	
	
		$this->create_plugin_setting(
	'select',
	'notify_admin_when_profile_updated',
	__('Notify Admin When Profile is Updated','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", the admin will be notified if the users update their profiles','users-ultra'),
  __('If you select "yes", the admin will be notified if the users update their profiles','users-ultra')
       );
	
	$this->create_plugin_setting(
	'select',
	'uultra_override_avatar',
	__('Use Users Ultra Avatar','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", Users Ultra will override the default WordPress Avatar','users-ultra'),
  __('If you select "yes", Users Ultra will override the default WordPress Avatar','users-ultra')
       );
	   
	 $this->create_plugin_setting(
	'select',
	'uultra_use_facebook_avatar',
	__('Use Facebook Avatar','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, Users Ultra will retreive the Facebook Avatar of the users. It will be used as Avatar in the users' profile",'users-ultra'),
  __("If you select yes, Users Ultra will retreive the Facebook Avatar of the users. It will be used as Avatar in the users' profile",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_use_facebook_avatar_activate',
	__('Allow Users To Deactivate Facebook Avatar','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, Your users will be able to deactivate this feature and the Gravatar will be used.",'users-ultra'),
  __("If you select yes, Users Ultra will retreive the Facebook Avatar of the users. It will be used as Avatar in the users' profile",'users-ultra')
       );
	   
	   
	    $this->create_plugin_setting(
	'select',
	'uultra_hide_empty_fields',
	__('Hide Empty Fields','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, empty fields will be automatically hidden from user's profile and the directory.",'users-ultra'),
  __("If you select yes, empty fields will be automatically hidden from user's profile and the directory.",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
        'input',
        'uultra_custom_profile_links_text',
        __('Text for links on profile page:','users-ultra'),array(),
        __('This text will be displayed for the links in the profile fields, you can use "click here". Leave it empty if you want to display the link as text.','users-ultra'),
        __('This text will be displayed for the links in the profile fields, you can use "click here". Leave it empty if you want to display the link as text.','users-ultra')
);	
	   
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_rotation_fixer',
	__('Auto Rotation Fixer','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select 'yes', Users Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'users-ultra'),
  __("If you select 'yes', Users Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'users-ultra')
       );
	   
	   
	   $this->create_plugin_setting(
                'checkbox',
                'uultra_allow_guest_rating',
                __('Allow Guests to use the rating system','users-ultra'),
                '1',
                __('If checked, users will be able to leave rates without being logged in','users-ultra'),
                __('If checked, User will not see the WP Admin Tool Bar.','users-ultra')
        ); 
		
		$this->create_plugin_setting(
                'checkbox',
                'uultra_allow_guest_like',
                __('Allow Guests to like other users ','users-ultra'),
                '1',
                __('If checked, users will be able to like users without being logged in','users-ultra'),
                __('If checked, users will be able to like users without being logged in','users-ultra')
        ); 
		
	   
	     $this->create_plugin_setting(
	'select',
	'uultra_force_cache_issue',
	__('Force Cache Refresh','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select 'yes', Users Ultra will force cache refresh caused by some plugins such as WP Super Cache. This will affect the 'images' only",'users-ultra'),
  __("If you select 'yes', Users Ultra will force cache refresh caused by some plugins such as WP Super Cache. This will affect the 'images' only",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
                'checkbox',
                'disable_default_lightbox',
                __('Disable Ligthbox','users-ultra'),
                '1',
                __("If checked, the default Ligthbox files included in the plugin won't be loaded",'users-ultra'),
                __("If checked, the default Ligthbox files included in the plugin won't be loaded",'users-ultra')
        ); 
		
		
	
	$this->create_plugin_setting(
	'select',
	'uultra_allow_users_contact_admin',
	__('Allow Users To Contact Admin','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select 'yes', the users will be see a button in the messaging link that will let them contact the administrator by sending a private message.",'users-ultra'),
  __("If you select 'yes', the users will be see a button in the messaging link that will let them contact the administrator by sending a private message.",'users-ultra')
       );
	   
	   
	    $this->create_plugin_setting(
                                        'checkbox_list',
                                        'uultra_allow_users_contact_admin_list',
                                        __('Choose Admins that will be notified', 'users-ultra'),
                                       $xoouserultra->userpanel->uultra_get_administrators_list(),
                                        __('Selected admin users will receive an email once a user contacts an admin. You can choose more than one admin.', 'users-ultra'),
                                        __('Selected admin users will receive an email once a user contacts an admin.', 'users-ultra')
                                );
								
								
	
	  
		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Password Strength Settings','users-ultra'); ?></h3>
  
  <p><?php _e("You can help protect your users' accounts by managing and monitoring the strength of their passwords.",'users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'uultra_password_lenght',
        __('Minimum password length:','users-ultra'),array(),
        __('By default a Password must be at least 7 characters long','users-ultra'),
        __('By default a Password must be at least 7 characters long','users-ultra')
);

   
$this->create_plugin_setting(
                'checkbox',
                'uultra_password_1_letter_1_number',
                __('Must contain at least one number and one letter','users-ultra'),
                '1',
                __('The password must contain at least one number and one letter','users-ultra'),
                __('The password must contain at least one number and one letter','users-ultra')
        ); 

$this->create_plugin_setting(
                'checkbox',
                'uultra_password_one_uppercase',
                __('Must contain at least one upper case character','users-ultra'),
                '1',
                __('The password must contain at least one upper case character','users-ultra'),
                __('The password must contain at least one upper case character','users-ultra')
        );

$this->create_plugin_setting(
                'checkbox',
                'uultra_password_one_lowercase',
                __('Must contain at least one lower case character','users-ultra'),
                '1',
                __('The password must contain at least one lower case character','users-ultra'),
                __('The password must contain at least one lowercase character','users-ultra')
        );
		
		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Membership Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
                'checkbox',
                'membership_display_selected_only',
                __('Display Only Selected Package','users-ultra'),
                '1',
                __('If checked, Only the Selected package will be displayed in the payment form. <strong>PLEASE NOTE: </strong>This setting is used only if you are using the pricing tables feature.','users-ultra'),
                __('If checked, Only the Selected package will be displayed in the payment form','users-ultra')
        ); 
$this->create_plugin_setting(
        'input',
        'membership_display_zero',
        __('Text for free membership:','users-ultra'),array(),
        __('This text will be displayed for the free membership rather than showing <strong>"$0.00"<strong>. Please input some text like: "Free"','users-ultra'),
        __('This text will be displayed for the free membership rather than showing <strong>"$0.00"<strong>. Please input some text like: "Free"','users-ultra')
);		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('IP Number Defender','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		

$this->create_plugin_setting(
	'select',
	'uultra_ip_defender',
	__('Activate IP Blocking','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", Users Ultra Defender will be activated and you will able to block IP numbers. Blocked IP number will not be able to register.','users-ultra'),
  __('If you select "yes", Users Ultra Defender will be activated and you will able to block IP numbers. Blocked IP number will not be able to register','users-ultra')
       );
	   
	   $this->create_plugin_setting(
            'select',
            'uultra_ip_defender_redirect_page',
            __('Redirect Page:','users-ultra'),
            $this->get_all_sytem_pages(),
            __('Select the page you would like to take blocked users.','users-ultra'),
            __('Select the page you would like to take blocked users.','users-ultra')
    );
	   
	   
	                                                      

                              

		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Media Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'media_uploading_folder',
        __('Upload Folder:','users-ultra'),array(),
        __('This is the folder where the user photos will be stored in. Please make sure to assing 755 privileges to it. The default folder is <strong>wp-content/usersultramedia</strong>','users-ultra'),
        __('This is the folder where the user photos will be stored in. Please make sure to assing 755 privileges to it. The default folder is <strong>wp-content/usersultramedia</strong>','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_mini_width',
        __('Mini Thumbnail Width','users-ultra'),array(),
        __('Width in pixels','users-ultra'),
        __('Width in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_mini_height',
        __('Mini Thumbnail Height','users-ultra'),array(),
        __('Height in pixels','users-ultra'),
        __('Height in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_thumb_width',
        __('Thumbnail Width','users-ultra'),array(),
        __('Width in pixels','users-ultra'),
        __('Width in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_thumb_height',
        __('Thumbnail Height','users-ultra'),array(),
        __('Height in pixels','users-ultra'),
        __('Height in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_large_width',
        __('Large Photo Max Width','users-ultra'),array(),
        __('Width in pixels','users-ultra'),
        __('Width in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_photo_large_height',
        __('Large Photo Max Height','users-ultra'),array(),
        __('Height in pixels','users-ultra'),
        __('Height in pixels','users-ultra')
);
		
?>
</table>

  
</div>




<div class="user-ultra-sect ">
  <h3><?php _e('Terms & Conditions','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		

$this->create_plugin_setting(
	'select',
	'uultra_terms_and_conditions',
	__('Allows Terms & Conditions Text Before Registration','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", users will have to accept terms and conditions when registering.','users-ultra'),
  __('If you select "yes", users will have to accept terms and conditions when registering.','users-ultra')
       );
	   
	   
	     $this->create_plugin_setting(
                                        'input',
                                        'uultra_terms_and_conditions_text',
                                        __('Terms & Conditions Label', 'users-ultra'), array(),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra'),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra')
                                );
		 $this->create_plugin_setting(
                                        'textarea',
                                        'uultra_terms_and_conditions_text_large',
                                        __('Terms & Conditions Text/HTML', 'users-ultra'), array(),
                                        __('Enter extened text to display.', 'users-ultra'),
                                        __('Enter extened text to display.', 'users-ultra')
                                );
								
		$this->create_plugin_setting(
					'select',
					'uultra_terms_and_conditions_mandatory_1',
					__('Is Mandatory?','users-ultra'),
					array(
						'yes' => __('Yes','users-ultra'), 
						'no' => __('No','users-ultra'),
						),
						
					__('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra'),
				  __('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra')
					   );
	   
								
								
		 $this->create_plugin_setting(
                                        'input',
                                        'uultra_terms_and_conditions_text_2',
                                        __('Terms & Conditions Text/HTML 2', 'users-ultra'), array(),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra'),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra')
                                );
								
		 $this->create_plugin_setting(
                                        'textarea',
                                        'uultra_terms_and_conditions_text_large_2',
                                        __('Terms & Conditions Text/HTML 2', 'users-ultra'), array(),
                                        __('Enter extended text to display', 'users-ultra'),
                                        __('Enter extended text to display', 'users-ultra')
                                );
								
			$this->create_plugin_setting(
					'select',
					'uultra_terms_and_conditions_mandatory_2',
					__('Is Mandatory?','users-ultra'),
					array(
						'yes' => __('Yes','users-ultra'), 
						'no' => __('No','users-ultra'),
						),
						
					__('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra'),
				  __('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra')
					   );
			
		
		$this->create_plugin_setting(
                                        'input',
                                        'uultra_terms_and_conditions_text_3',
                                        __('Terms & Conditions Text/HTML 3', 'users-ultra'), array(),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra'),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra')
                                );
								
								
		 $this->create_plugin_setting(
                                        'textarea',
                                        'uultra_terms_and_conditions_text_large_3',
                                        __('Terms & Conditions Text/HTML 3', 'users-ultra'), array(),
                                        __('Enter extended text to display.', 'users-ultra'),
                                        __('Enter extended text to display.', 'users-ultra')
                                );
								
								
				$this->create_plugin_setting(
					'select',
					'uultra_terms_and_conditions_mandatory_3',
					__('Is Mandatory?','users-ultra'),
					array(
						'yes' => __('Yes','users-ultra'), 
						'no' => __('No','users-ultra'),
						),
						
					__('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra'),
				  __('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra')
					   );
		
		
		
		 $this->create_plugin_setting(
                                        'input',
                                        'uultra_terms_and_conditions_text_4',
                                        __('Terms & Conditions Text/HTML 4', 'users-ultra'), array(),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra'),
                                        __('Enter text to display, example "I agree to the Terms & Conditions".', 'users-ultra')
                                );
		
		 $this->create_plugin_setting(
                                        'textarea',
                                        'uultra_terms_and_conditions_text_large_4',
                                        __('Terms & Conditions Text/HTML 4', 'users-ultra'), array(),
                                        __('Enter extended text to display', 'users-ultra'),
                                        __('Enter extended text to display', 'users-ultra')
                                );



$this->create_plugin_setting(
					'select',
					'uultra_terms_and_conditions_mandatory_4',
					__('Is Mandatory?','users-ultra'),
					array(
						'yes' => __('Yes','users-ultra'), 
						'no' => __('No','users-ultra'),
						),
						
					__('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra'),
				  __('If you select "yes", user will have to accept the terms and conditions by checking the checkbox.','users-ultra')
					   );
                                                    

                              

		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Avatar Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'media_avatar_width',
        __('Avatar Width:','users-ultra'),array(),
        __('Width in pixels','users-ultra'),
        __('Width in pixels','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_avatar_height',
        __('Avatar Height','users-ultra'),array(),
        __('Height in pixels','users-ultra'),
        __('Height in pixels','users-ultra')
);

		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('MailChimp Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'mailchimp_api',
        __('MailChimp API Key','users-ultra'),array(),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','users-ultra'),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'mailchimp_list_id',
        __('MailChimp List ID','users-ultra'),array(),
        __('Fill out this field your list ID.','users-ultra'),
        __('Fill out this field your list ID.','users-ultra')
);

$this->create_plugin_setting(
                'checkbox',
                'mailchimp_active',
                __('Activate/Deactivate MailChimp','users-ultra'),
                '1',
                __('If checked, Users will be asked to subscribe through MailChimp','users-ultra'),
                __('If checked, Users will be asked to subscribe through MailChimp','users-ultra')
        );

$this->create_plugin_setting(
                'checkbox',
                'mailchimp_auto_checked',
                __('Auto Checked MailChimp','users-ultra'),
                '1',
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','users-ultra'),
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','users-ultra')
        );
$this->create_plugin_setting(
        'input',
        'mailchimp_text',
        __('MailChimp Text','users-ultra'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','users-ultra'),
        __('Please input the text that will appear when asking users to get periodical updates.','users-ultra')
);

	$this->create_plugin_setting(
        'input',
        'mailchimp_header_text',
        __('MailChimp Header Text','users-ultra'),array(),
        __('Please input the text that will appear as header when mailchip is active.','users-ultra'),
        __('Please input the text that will appear as header when mailchip is active.','users-ultra')
);
	
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('bbPress Integration Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'uulltra_bbp_status',
	__('Activate bbPress Compatibility','users-ultra'),
	array(
		1 => __('Yes','users-ultra'), 
		0=> __('No','users-ultra'),
		),
		
	__("By activation this option two new links will be added to the user profiles. One of Topics started by the user and another one for the user's replies",'users-ultra'),
  __("By activation this option two new links will be added to the user profiles. One of Topics started by the user and another one for the user's replies",'users-ultra')
       );
    
$this->create_plugin_setting(
        'input',
        'uulltra_bbp_modules',
        __('Options','users-ultra'),array(),
        __('Options that will be displayed in your bbPress separated by commas: <strong>Available options:</strong> badges,country,like,rating,social','users-ultra'),
        __('Options that will be displayed in your bbPress separated by commas: <strong>Available options:</strong> badges,country,like,rating,social','users-ultra')
);
  
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Online/Offline Status Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'modstate_online',
	__('Online/Offline Status','users-ultra'),
	array(
		1 => __('Activate','users-ultra'), 
		0=> __('Deactivate','users-ultra'),
		),
		
	__('Activate the online offline feature','users-ultra'),
  __('Activate the online offline feature','users-ultra')
       );
    
$this->create_plugin_setting(
	'select',
	'modstate_showoffline',
	__('Show Offline Icon','users-ultra'),
	array(
		1 => __('Yes','users-ultra'), 
		0=> __('No','users-ultra'),
		),
		
	__('.','users-ultra'),
  __('.','users-ultra')
       );
  
?>
</table>

  
</div>








<div class="user-ultra-sect ">
  <h3><?php _e('User Profiles Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'uprofile_setting_display_name',
	__('Profile Display Name: ','users-ultra'),
	array(
		'username' => __('Display User Name','users-ultra'), 
		'display_name' => __('Use the Display Name set by the User in the Profile','users-ultra'),
		'user_nicename' => __('Use the Nice Name set by the User in the Profile','users-ultra')),
		
	__('Set how the users ultra will make the user name.','users-ultra'),
  __('Set how the users ultra will make the user name.','users-ultra')
       );

    
    
?>
</table>

  
</div>




</div>

<div id="tabs-redirections">


<div class="user-ultra-sect ">
  <h3><?php _e('Redirections Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  <table class="form-table">
    <?php 
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_profile',
                __('Redirect Backend Profiles','users-ultra'),
                '1',
                __('If checked, non-admin users who try to access backend WP profiles will be redirected to Users Ultra Profile Page specified above.','users-ultra'),
                __('Checking this option will send all users to the front-end Users Ultra Profile Page if they try to access the default backend profile page in wp-admin. The page can be selected in the Users Ultra System Pages settings.','users-ultra')
        );
        
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_login',
                __('Redirect Backend Login','users-ultra'),
                '1',
                __('If checked, non-admin users who try to access backend login form will be redirected to the front end Users Ultra Login Page specified above.','users-ultra'),
                __('Checking this option will send all users to the front-end Users Ultra Login Page if they try to access the default backend login form. The page can be selected in the Users Ultra System Pages settings.','users-ultra')
        );
        
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_registration',
                __('Redirect Backend Registrations','users-ultra'),
                '1',
                __('If checked, non-admin users who try to access backend registration form will be redirected to the front end Users Ultra Registration Page specified above.','users-ultra'),
                __('Checking this option will send all users to the front-end Users Ultra Registration Page if they try to access the default backend registration form. The page can be selected in the Users Ultra System Pages settings.','users-ultra')
        );
		
		
		    $this->create_plugin_setting(
            'select',
            'redirect_after_registration_login',
            __('After Registration','users-ultra'),
            $this->get_all_sytem_pages(),
            __('The user will be taken to this page after registration if the account activation is set to automatic ','users-ultra'),
            __('The user will be taken to this page after registration if the account activation is set to automatic ','users-ultra')
    );
	
	
	 $this->create_plugin_setting(
                'checkbox',
                'redirect_registration_when_social',
                __('Redirect When Social Registration','users-ultra'),
                '1',
                __('If checked, the users will be redirected to the page specified below. when they sign in by using social media regitration method','users-ultra'),
                __('If checked, the users will be redirected to the page specified below. when they sign in by using social media regitration method','users-ultra')
        );
	
	$this->create_plugin_setting(
            'select',
            'redirect_after_registration_login_social',
            __('After Registration (Social Features)','users-ultra'),
            $this->get_all_sytem_pages(),
            __('The user will be taken to this page after registration if the account activation is set to automatic and if the user uses some of the <strong>social media options</strong> ','users-ultra'),
            __('The user will be taken to this page after registration if the account activation is set to automatic and if the user uses some of the <strong>social media options</strong> ','users-ultra')
    );
	
	
	 $this->create_plugin_setting(
	'select',
	'uultra_auto_redirect_loggedin_user',
	__('Redirect Users To My Account - Login Page','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, the user will be redirected to the <strong>my account</strong> page when clicking on the <strong>login link</strong>. Otherwise, the user will see the standard login box. WARNING: Do not set it to 'yes' if you are using the login shortcode in WP side widgets. <br><br><strong>PLEASE NOTE:</strong> If redirect_to option is set in the login shortcode then the user will be take to the specified URL instead the <strong>my account </strong>page.",'users-ultra'),
  __("If you select yes, the user will be redirected to the my account page. Otherwise, the user will see the standard login box. WARNING: Do not set it to 'yes' if you are using the login shortcode in WP side widgets",'users-ultra')
       );
	   
	 $this->create_plugin_setting(
	'select',
	'uultra_auto_redirect_loggedin_user_registration',
	__('Redirect Users To My Account - Registration Page','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, the user will be redirected to the <strong>my account</strong> page when clicking on the <strong>registration link</strong>. Otherwise, the user will see the standard login box. WARNING: Do not set it to 'yes' if you are using the registration shortcode in WP side widgets.",'users-ultra'),
  __("If you select yes, the user will be redirected to the <strong>my account</strong> page when clicking on the <strong>registration link</strong>. Otherwise, the user will see the standard login box. WARNING: Do not set it to 'yes' if you are using the registration shortcode in WP side widgets.",'users-ultra')
       );
	   
	   
	   
        
    ?>
</table>
  
  
  
</div>


</div>


<div id="tabs-registration">


<div class="user-ultra-sect ">
  <h3><?php _e('Registration Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
 
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'registration_rules',
	__('Registration Type','users-ultra'),
	array(
		1 => __('Login automatically after registration','users-ultra'), 
		2 => __('E-mail Activation -  A confirmation link is sent to the user email','users-ultra'),
		3 => __('Manual Activation - The admin approves the accounts manually','users-ultra'),
		4 => __('Paid Activation - Enables the Membership Features','users-ultra')),
		
	__('Please note: Paid Activation does not work with social connects at this moment.','users-ultra'),
  __('Please note: Paid Activation does not work with social connects at this moment.','users-ultra')
       );
	   
	   
	   $this->create_plugin_setting(
                        'input',
                        'force_domain_only',
                        __('Allow Only from this domain', 'users-ultra'), array(),
                        __("This option will give you the capabilitiy to accept registrations only from a particular domain. Leave blank if you don't wish to apply this restriction. You can use multiple domain names separated by commas. Example: 'school.edu', 'school2.edu', 'school3.edu'", 'users-ultra'),
                        __("This option will give you the capabilitiy to accept registrations only from a particular domain. Leave blank if you don't wish to apply this restriction.", 'users-ultra')
                );
	   
	     
	   $this->create_plugin_setting(
                        'select',
                        'social_login_activation_type',
                        __('Activate Accounts When Using Social', 'users-ultra'),
                        array(
                            'yes' => __('YES', 'users-ultra'),
                            'no' => __('NO', 'users-ultra'),
                            
                        ),
                        __('If YES, the account will be activated automatically when using social login options. ', 'users-ultra'),
                        __('If YES, the account will be activated automatically when using social login options. ', 'users-ultra')
                );
	   
	   $this->create_plugin_setting(
                        'select',
                        'allow_registering_only_with_email',
                        __('Allow Email as User Name', 'users-ultra'),
                        array(
                            'no' => __('NO', 'users-ultra'),
                            'yes' => __('YES', 'users-ultra'),
                            
                        ),
                        __('If YES, the user name field will be removed from the registration form. This means that the user will be able to use the email address as username. <br /> ', 'users-ultra'),
                        __('If YES, the user name field will be removed from the registration form. This means that the user will be able to use the email address as username.', 'users-ultra')
                );
				
				
		
$this->create_plugin_setting(
	'select',
	'set_password',
	__('User Selected Passwords','users-ultra'),
	array(
		1 => __('Enabled, allow users to set password','users-ultra'), 
		0 => __('Disabled, email a random password to users','users-ultra')),
	__('Enable/disable setting a user selected password at registration','users-ultra'),
  __('If enabled, users can choose their own password at registration. If disabled, WordPress will email users a random password when they register.','users-ultra')
        );
		
		$this->create_plugin_setting(
	'select',
	'set_email_retype',
	__('User Forced to retype Email?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("By selecting 'yes' the users will forced to re-type the email.",'users-ultra'),
  __("By selecting 'yes'  the users will forced to re-type the email.",'users-ultra')
       );
		
		
		 $this->create_plugin_setting(
                        'select',
                        'allow_account_upgrading',
                        __('Allow Users To Upgrade Account?', 'users-ultra'),
                        array(
                            'no' => __('NO', 'users-ultra'),
                            'yes' => __('YES', 'users-ultra'),
                            
                        ),
                        __('If YES, the user will be able to upgrade his/her account. ', 'users-ultra'),
                        __('If YES, the user will be able to upgrade his/her account. ', 'users-ultra')
                );
				
				
		 $this->create_plugin_setting(
                        'select',
                        'allow_account_downgrade',
                        __('Allow Users To Downgrade Account?', 'users-ultra'),
                        array(
                            'no' => __('NO', 'users-ultra'),
                            'yes' => __('YES', 'users-ultra'),
                            
                        ),
                        __('If YES, the user will be able to downgrade his/her account. ', 'users-ultra'),
                        __('If YES, the user will be able to downgrade his/her account. ', 'users-ultra')
                );
				
		 $this->create_plugin_setting(
                        'select',
                        'force_account_upgrading',
                        __('Force Account Upgrading?', 'users-ultra'),
                        array(
                            'no' => __('NO', 'users-ultra'),
                            'yes' => __('YES', 'users-ultra'),
                            
                        ),
                        __('If YES, the user will not be able to use any feature until the account has been upgraded. <br> The user will be still be able to login. ', 'users-ultra'),
                        __('If YES, the user will not be able to use any feature until the account has been upgraded. <br> The user will be still be able to login.', 'users-ultra')
                );
				
			
		$this->create_plugin_setting(
                        'input',
                        'force_account_upgrading_text',
                        __('Force Upgrade Text', 'users-ultra'), array(),
                        __("This message will be displayed to the users when they are asked to upgrade their accounts.", 'users-ultra'),
                        __("This message will be displayed to the users when they are asked to upgrade their accounts.", 'users-ultra')
                );
	   
	   

                $this->create_plugin_setting(
                        'textarea',
                        'msg_register_success',
                        __('Register success message', 'users-ultra'),
                        null,
                        __('Show a text message when users complete the registration process.', 'users-ultra'),
                        __('This message will be shown to users after registration is completed.', 'users-ultra')
                );

                $this->create_plugin_setting(
                        'textarea',
                        'html_register_success_after',
                        __('Text/HTML below the Register Success message.', 'users-ultra'),
                        null,
                        __('Show a text/HTML content under success message when users complete the registration process.', 'users-ultra'),
                        __('This message will be shown to users under the success message after registration is completed.', 'users-ultra')
                );
    
    
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Registration Role Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		

$this->create_plugin_setting(
	'select',
	'uultra_roles_actives_registration',
	__('Allows users to select role','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", users will be able to select their user role at registration. If you do not understand what this means, select "no".','users-ultra'),
  __('If you select "yes", users will be able to select their user role at registration. If you do not understand what this means, select "no".','users-ultra')
       );   
	   
	   $this->create_plugin_setting(
                                        'input',
                                        'label_for_registration_user_role',
                                        __('Select Role Label', 'users-ultra'), array(),
                                        __('Enter text which you want to show as the label for drop/down list that displays the roles.', 'users-ultra'),
                                        __('Enter text which you want to show as the label for drop/down list that displays the roles.', 'users-ultra')
                                );
								
								 $this->create_plugin_setting(
                                        'input',
                                        'label_for_registration_user_role_1',
                                        __('Role', 'users-ultra'), array(),
                                        __('Enter text which you want to show as the label for User Role selection.', 'users-ultra'),
                                        __('Enter text which you want to show as the label for User Role selection.', 'users-ultra')
                                );

                                                    

                                $this->create_plugin_setting(
                                        'checkbox_list',
                                        'choose_roles_for_registration',
                                        __('Choose User Roles for Registration', 'users-ultra'),
                                       $xoouserultra->role->uultra_available_user_roles_registration(),
                                        __('Selected user roles will be available for users to choose at registration. The default role for new users will be always available, you can change the default role in WordPress general settings.', 'users-ultra'),
                                        __('User roles selected in this section will appear on the registration form. Be aware that some user roles will give posting and editing access to your site, so please be careful when using this option.', 'users-ultra')
                                );

$this->create_plugin_setting(
	'select',
	'uultra_roles_actives_backend',
	__('Allows users to select role on their account?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", users will be able to change their role at their account. If you do not understand what this means, select "no".','users-ultra'),
  __('If you select "yes", users will be able to change their role at their account. If you do not understand what this means, select "no".','users-ultra')
       );  

$this->create_plugin_setting(
                        'textarea',
                        'uultra_roles_actives_backend_text',
                        __('Custom Text/HTML for role selection on user backend', 'users-ultra'),
                        null,
                        __('Show a text/HTML content in the section where the user will be able to change her/his role.', 'users-ultra'),
                        __('Show a text/HTML content in the section where the user will be able to change her/his role.', 'users-ultra')
                );
								
								
$this->create_plugin_setting(
	'select',
	'uultra_roles_automatic_set',
	__('Common Registration - Assign Role Automatically','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", a specified role will be assigned on registration. This is applied only to common registrations <strong>NOT Social registrations</strong>.','users-ultra'),
  __('If you select "yes", a specified role will be assigned on registration. This is applied only to common registrations NOT Social registrations.','users-ultra')
       );
								
$this->create_plugin_setting(
	'select',
	'uultra_roles_automatic_set_role',
	__('Role to assign on Common Registration','users-ultra'),
	$xoouserultra->role->uultra_available_user_roles_registration(),
		
	__('This role will be automatically assigned when the users register by using the common registration form.','users-ultra'),
  __('This role will be automatically assigned when the user register by using the common registration form.','users-ultra')
       );
	   
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_roles_automatic_set_social',
	__('Social Registration - Assign Role Automatically','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", a specified role will be assigned on registration when the users register by using any Social media such as: facebook, facebok etc. This option will be applied only to <strong>Social Media Registrations</strong>.','users-ultra'),
  __('If you select "yes", a specified role will be assigned on registration when the users register by using any Social media such as: facebook, facebok etc. This option will be applied only to Social Media Registrations.','users-ultra')
       );   
	   
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_roles_automatic_set_role_social',
	__('Role to assign on Social Registration','users-ultra'),
	$xoouserultra->role->uultra_available_user_roles_registration(),
		
	__('This role will be automatically assigned when the users register by using any of the social registration options','users-ultra'),
  __('This role will be automatically assigned when the users register by using  any of the social registration options','users-ultra')
       );
								
								

		
?>
</table>

  
</div>

<?php if(isset($uultra_group)) {?>
<div class="user-ultra-sect ">
  <h3><?php _e('Registration Groups Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
    <table class="form-table">
<?php             
						
								
$this->create_plugin_setting(
	'select',
	'uultra_groups_automatic_set',
	__('Common Registration - Assign Group Automatically','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select yes, the user will be added to the specified group. This is applied only to common registrations <strong>NOT Social registrations</strong>.','users-ultra'),
  __('If you select yes, the user will be added to the specified group. This is applied only to common registrations <strong>NOT Social registrations</strong>.','users-ultra')
       );
								
$this->create_plugin_setting(
	'select',
	'uultra_groups_automatic_set_group',
	__('Group to assign on Common Registration','users-ultra'),
	$uultra_group->uultra_available_user_groups_registration(),
		
	__('The user will be automatically added to this group when the users register by using the common registration form.','users-ultra'),
  __('The user will be automatically added to this group when the users register by using the common registration form.','users-ultra')
       );
	   
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_groups_automatic_set_social',
	__('Social Registration - Assign Group Automatically','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('If you select "yes", the user will be added to the specified group if they register by using any Social media such as: facebook, facebok etc. This option will be applied only to <strong>Social Media Registrations</strong>.','users-ultra'),
  __('If you select yes, the user will be added to the specified group if they register by using any Social media such as: facebook, facebok etc. This option will be applied only to <strong>Social Media Registrations</strong>.','users-ultra')
       );   
	   
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_groups_automatic_set_group_social',
	__('Group to assign on Social Registration','users-ultra'),
	$uultra_group->uultra_available_user_groups_registration(),
		
	__('The users will be added to this group when they register by using any of the social registration options','users-ultra'),
  __('The users will be added to this group when they register by using any of the social registration options','users-ultra')
       );								

		
?>
</table>

</div>

<?php }?>

</div>

<div id="tabs-front-end-publisher">


<div class="user-ultra-sect ">
  <h3><?php _e('Frontend Publishing  Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'uultra_front_publisher_default_amount',
        __('Max Posts Per User:','users-ultra'),array(),
        __('Please set 9999 for unlimited posts. This value is used for free and general users','users-ultra'),
        __('Please set 9999 for unlimited posts. This value is used for free and general users','users-ultra')
);


 $this->create_plugin_setting(
        'input',
        'uultra_user_profile_post_widgets_how_many',
        __("Quantity of Posts on Widgets:",'users-ultra'),array(),
        __("Please set how many posts will be displayed in the profile's widgets. <strong>10 posts is the default value if you leave it empty.</strong>. ",'users-ultra'),
        __("Please set how many posts will be displayed in the profile's widgets. <strong>10 posts is the default value.</strong>",'users-ultra') 
);

$this->create_plugin_setting(
	'select',
	'uultra_front_publisher_default_status',
	__('Default Status','users-ultra'),
	array(
		'pending' => __('Pending','users-ultra'), 
		'publish' => __('Publish','users-ultra'),
		),
		
	__('This is the status of the post when the users submit new posts through Users Ultra.','users-ultra'),
  __('This is the status of the post when the users submit new posts through Users Ultra.','users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_front_publisher_allows_category',
	__('Allows users to select category','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__('If "yes" authors will be able to select the category, if "no" is set the default category will be used to save the post.','users-ultra'),
  __('If "yes" authors will be able to select the category, if "no" is set the default category will be used to save the post.','users-ultra')
       );
	   
	   
	    $this->create_plugin_setting(
                        'select',
                        'enable_post_edit',
                        __('Users can edit post?', 'users-ultra'),
                        array(
                            'yes' => __('YES', 'users-ultra'),
                            'no' => __('NO', 'users-ultra'),
                            
                        ),
                        __('Users will be able to edit their own posts.', 'users-ultra'),
                        __('Users will be able to edit their own posts.', 'users-ultra')
                );
	 $this->create_plugin_setting(
                        'select',
                        'enable_post_del',
                        __('User can delete post?', 'users-ultra'),
                        array(
                            'yes' => __('YES', 'users-ultra'),
                            'no' => __('NO', 'users-ultra'),
                            
                        ),
                        __('Users will be able to delete their own posts.', 'users-ultra'),
                        __('Users will be able to delete their own posts.', 'users-ultra')
                );
				
	   
   $this->create_plugin_setting(
            'select',
            'uultra_front_publisher_default_category',
            __('Default Category','users-ultra'),
            $this->get_all_sytem_cagegories(),
            __('The category if authors are not allowed to select a custom category.','users-ultra'),
            __('The category if authors are not allowed to select a custom category.','users-ultra')
    );
	
	 $this->create_plugin_setting(
            'select',
            'uultra_front_publisher_post_type',
            __('Set Post Type','users-ultra'),
            $xoouserultra->publisher->uultra_get_available_post_types(),
            __('By default the publisher will let users create "posts" only. Here you can set the a different post type.','users-ultra'),
            __('By default the publisher will let users create "posts" only. Here you can set the a different post type.','users-ultra')
    );
	
	
	$this->create_plugin_setting(
        'input',
        'uultra_front_publisher_post_type_label_singular',
        __('Post Label Singular','users-ultra'),array(),
        __('This can be used when using a different post type. For example. Book','users-ultra'),
        __('This can be used when using a different post type. For example. Book','users-ultra')
);

	$this->create_plugin_setting(
        'input',
        'uultra_front_publisher_post_type_label_plural',
        __('Post Label Plural','users-ultra'),array(),
        __('This can be used when using a different post type. For example. Books','users-ultra'),
        __('This can be used when using a different post type. For example. Books','users-ultra')
);

		
?>
</table>

  
</div>



</div>
<div id="tabs-social-media">


<div class="user-ultra-sect ">
  <h3><?php _e('Social Media Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
   
$this->create_plugin_setting(
                'checkbox',
                'social_media_fb_active',
                __('Facebook Connect','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Facebook.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Facebook.','users-ultra')
        );
		
$this->create_plugin_setting(
        'input',
        'social_media_facebook_app_id',
        __('Facebook App ID','users-ultra'),array(),
        __('Obtained at Facebook','users-ultra'),
        __('Obtained at Facebook','users-ultra')
);



$this->create_plugin_setting(
        'input',
        'social_media_facebook_secret',
        __('Facebook App Secret','users-ultra'),array(),
        __('Facebook settings','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);

$this->create_plugin_setting(
                'checkbox',
                'social_media_linked_active',
                __('LinkedIn Connect','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through LinkedIn.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through LinkedIn.','users-ultra')
        );
    
$this->create_plugin_setting(
        'input',
        'social_media_linkedin_api_public',
        __('LinkedIn API Key Public','users-ultra'),array(),
        __('Obtained when you created your application.','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'social_media_linkedin_api_private',
        __('LinkedIn API Key Private','users-ultra'),array(),
        __('<br><br> VERY IMPORTANT: Set OAuth 1.0 Accept Redirect URL to "?uultralinkedin=1". Example: http://yourdomain.com/?uultralinkedin=1','users-ultra'),
        __('Set OAuth 1.0 Accept Redirect URL to "?uultralinkedin=1"','users-ultra')
);  


$this->create_plugin_setting(
                'checkbox',
                'social_media_yahoo',
                __('Yahoo Sign up','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Yahoo.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Yahoo.','users-ultra')
        );
$this->create_plugin_setting(
                'checkbox',
                'social_media_google',
                __('Google Sign up','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Google.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Google.','users-ultra')
        ); 

$this->create_plugin_setting(
        'input',
        'google_client_id',
        __('Google Client ID','users-ultra'),array(),
        __('Paste the client id that you got from Google API Console','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);  

$this->create_plugin_setting(
        'input',
        'google_client_secret',
        __('Google Client Secret','users-ultra'),array(),
        __('Set the client secret','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'google_redirect_uri',
        __('Google Redirect URI','users-ultra'),array(),
        __('Paste the redirect URI where you given in APi Console. You will get the Access Token here during login success. Find more information here https://developers.google.com/console/help/new/#console. <br><br> VERY IMPORTANT: Your URL should end with "?uultraplus=1". Example: http://yourdomain.com/?uultraplus=1','users-ultra'),
        __('Paste the redirect URI where you given in APi Console. You will get the Access Token here during login success. ','users-ultra')
);

//instagram

$this->create_plugin_setting(
                'checkbox',
                'instagram_connect',
                __('Instagram Sign up','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Instagram.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Instagram.','users-ultra')
        );
$this->create_plugin_setting(
        'input',
        'instagram_client_id',
        __('Instagram Client ID','users-ultra'),array(),
        __('Paste the client id that you got from Instagram API Console','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);  

$this->create_plugin_setting(
        'input',
        'instagram_client_secret',
        __('Instagram Client Secret','users-ultra'),array(),
        __('Set the client secret','users-ultra'),
        __('Obtained when you created your application.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'instagram_redirect_uri',
        __('Instagram Redirect URI','users-ultra'),array(),
        __('Paste the redirect URI where you given in APi Console. You will get the Client ID and Client Secret here http://instagram.com/developer/clients/register/#. <br><br> VERY IMPORTANT: Your Redirect URI should end with "?instagram=1". Example: http://yourdomain.com/?instagram=1','users-ultra'),
        __('Paste the redirect URI where you given in APi Console. You will get the Access Token here during login success. ','users-ultra')
);

/// add to array
$this->create_plugin_setting(
                'checkbox',
                'twitter_connect',
                __('Twitter Sign up','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Twitter.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Twitter.','users-ultra')
        );
		

$this->create_plugin_setting(
        'input',
        'twitter_consumer_key',
        __('Consumer Key','users-ultra'),array(),
        __('Paste the Consumer Key','users-ultra'),
        __('Obtained when you created the application.','users-ultra')
);  

$this->create_plugin_setting(
        'input',
        'twitter_consumer_secret',
        __('Consumer Secret','users-ultra'),array(),
        __('Paste the Consumer Secret','users-ultra'),
        __('Obtained when you created the application.','users-ultra')
);

$this->create_plugin_setting(
                'checkbox',
                'twitter_autopost',
                __('Twitter Auto Post','users-ultra'),
                '1',
                __('If checked, Users Ultra will post a message automatically to the user twitter timeline when registering.','users-ultra'),
                __('If checked, Users Ultra will post a message automatically to the user twitter timeline when registering.','xoousers','users-ultra')
        );

$this->create_plugin_setting(
        'input',
        'twitter_autopost_msg',
        __('Message','users-ultra'),array(),
        __('Input the message that will be posted right after user registration','users-ultra'),
        __('Input the message that will be posted right after user registration','users-ultra')
);	


/// yammer
$this->create_plugin_setting(
                'checkbox',
                'yammer_connect',
                __('Yammer Sign up','users-ultra'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Yammer.','users-ultra'),
                __('If checked, User will be able to Sign up & Sign in through Yammer.','users-ultra')
        );
		

$this->create_plugin_setting(
        'input',
        'yammer_client_id',
        __('Client Id','users-ultra'),array(),
        __('Paste the Yammer Client ID','users-ultra'),
        __('Obtained at Yammer','users-ultra')
);  

$this->create_plugin_setting(
        'input',
        'yammer_client_secret',
        __('Client Secret','users-ultra'),array(),
        __('Paste the Yammer Client Secret','users-ultra'),
        __('Obtained at Yammer','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'yammer_redir_url',
        __('Redirect URL','users-ultra'),array(),
        __('Paste the Yammer Client Secret','users-ultra'),
        __('<br><br> VERY IMPORTANT: Your URL should end with "?uultryammer=1". Example: http://yourdomain.com/?uultryammer=1','users-ultra')
);
?>
</table>

  
</div>


</div>



<div id="tabs-activity-wall">


<div class="user-ultra-sect ">
  <h3><?php _e("Activity Wall Settings",'users-ultra'); ?></h3>
  
  <p><?php _e("In this section you can manage the user's wall settings.",'users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'uultra_user_wall_make_link_clickable',
	__('Make links clickable in activity wall?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' URL will be converted to clickable links automatically.",'users-ultra'),
  __("If 'yes' URL will be converted to clickable links automatically.",'users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_user_wall_allow_to_start_an_update',
	__('Allow users to leave an update on Site-Wide Activity Wall?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'no' user won't be able to leave an update on the activty wall.",'users-ultra'),
  __("If 'no' user won't be able to leave an update on the activty wall.",'users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_user_wall_allow_to_start_an_update_on_profile',
	__("Allow users to leave an update on User's Wall?",'users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'no' user won't be able to leave an update on the activty wall.",'users-ultra'),
  __("If 'no' user won't be able to leave an update on the activty wall.",'users-ultra')
       );
$this->create_plugin_setting(
	'select',
	'uultra_user_wall_allow_to_leave_comments',
	__('Allow users to leave comments on Site-Wide Activity Wall?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'no' user won't be able to leave comments on the activty wall.",'users-ultra'),
  __("If 'no' user won't be able to leave comments on the activty wall.",'users-ultra')
       );

$this->create_plugin_setting(
	'select',
	'uultra_user_wall_profile_allow_to_leave_comments',
	__("Allow users to leave comments on User's  Wall?",'users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'no' user won't be able to leave comments on the user's wall.",'users-ultra'),
  __("If 'no' user won't be able to leave comments on the user's wall.",'users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_user_wall_make_link_clickable_new_window',
	__('Open links on new windows?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If 'yes' URL will be have the '_parent' attribute. Which means the link will be opened on a new tab.",'users-ultra'),
  __("If 'yes' URL will be have the '_parent' attribute. Which means the link will be opened on a new tab.",'users-ultra')
       );
   
		
	   
$this->create_plugin_setting(
	'select',
	'uultra_user_wall_enable_new_post',
	__('Enable New Posts Notifications?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' the new post notification will be displayed in the user's wall and site-wide. This setting will block both: admin and common users notifications",'users-ultra'),
  __("If 'yes' the new post notification will be displayed in the user's wall and site-wide.",'users-ultra')
       );
	   
	  	   
	 $this->create_plugin_setting(
	'select',
	'uultra_user_wall_enable_photo',
	__('Enable New Photos Notifications?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' a 'new photo' notification will be displayed in the user's wall.",'users-ultra'),
  __("If 'yes' a 'new photo' notification will be displayed in the user's wall.",'users-ultra')
       );
	   
	 
	  $this->create_plugin_setting(
	'select',
	'uultra_user_wall_enable_photo_sharing',
	__('Enable User To Share Photos?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' the users will be able to upload & share photos in the site-wide activity wall.",'users-ultra'),
  __("If 'yes' the users will be able to upload & share photos in the site-wide activity wall.",'users-ultra')
       );
	   
	     $this->create_plugin_setting(
	'select',
	'uultra_site_wide_facebook_sharing_options',
	__('Enable Facebook Like & Share for Posts on Site-Wide Wall?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' the Facebook Like and Share buttons will be enabled. This feature will let users to share or like posts in the site-wide activity wall.",'users-ultra'),
  __("If 'yes' the Facebook Like and Share buttons will be enabled. This feature will let users to share or like posts in the site-wide activity wall.",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_site_wide_visible_for_loggedin_users',
	__("Profile's Activity wall visible for non logged in users?",'users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'no' the activity widget in the user profile will be visible only for logged in users only",'users-ultra'),
  __("If 'no' the activity widget in the user profile will be visible only for logged in users only",'users-ultra')
       );
	   
	   
	   
	    	   
	 $this->create_plugin_setting(
	'select',
	'uultra_wal_new_user_notification',
	__('Enable New Users Notifications?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' a message will be displayed in the activity wall every time a new user is registered.",'users-ultra'),
  __("If 'yes' a message will be displayed in the activity wall every time a new user is registered.",'users-ultra')
       );
	   
	   
	    
	   $this->create_plugin_setting(
	'select',
	'uultra_user_wall_enable_new_post_as_admin',
	__('Enable New Posts Notifications As Admin?','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' all the posts created by the admin will be displayed on the admin's wall and site-wide wall.",'users-ultra'),
  __("If 'yes' all the posts created by the admin will be displayed on the admin's wall and site-wide wall.",'users-ultra')
       );
	   
	   
	   
	   $this->create_plugin_setting(
        'input',
        'uultra_user_wall_how_many',
        __("Quantity of Messages In User's wall:",'users-ultra'),array(),
        __("Please set how many messages the User's wall should display. <strong>Five messages is the default value if you leave it empty.</strong>. ",'users-ultra'),
        __("Please set how many messages the User's wall should display. <strong>Five messages is the default value.</strong>",'users-ultra') 
);



 $this->create_plugin_setting(
        'input',
        'uultra_site_wide_wall_how_many',
        __("Quantity of Messages In Site-Wide Wall:",'users-ultra'),array(),
        __("Please set how many messages will be displayed in the site-wide activity wall. <strong>10 messages is the default value if you leave it empty.</strong>. ",'users-ultra'),
        __("Please set how many messages the User's wall should display. <strong>Five messages is the default value.</strong>",'users-ultra') 
);


$this->create_plugin_setting( 
        'input',
        'wall_image_share_width',
        __('Image Width:','users-ultra'),array(),
        __('Width in pixels of the image that users will be able to share either in the site-wide wall or the user wall. <strong>Do not use the "px" input only the "number". <strong> <strong>PLEASE NOTE: If you leave this value empty the default width of the image will be "600px" </strong>','users-ultra'),
        __('Width in pixels of the image that users will be able to share either in the site-wide wall or the user wall.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'wall_image_share_height',
        __('Image Height','users-ultra'),array(),
        __('Height in pixels of the image that users will be able to share either in the site-wide wall or the user wall.','users-ultra'),
        __('Height in pixels of the image that users will be able to share either in the site-wide wall or the user wall.','users-ultra')
);	   
	   
	  
		
?>
</table>

  
</div>




</div>


<?php 

if($activate_groups=='yes' || $activate_groups == '')
{
?>
<div id="tabs-groups">


<div class="user-ultra-sect ">
  <h3><?php _e("Groups Settings",'users-ultra'); ?></h3>
  
  <p><?php _e("In this section you can manage Groups module settings.",'users-ultra'); ?></p>
  
  
   <h4><?php _e("Set up the behaviour of locked posts.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_complete_post',
	__('Hide Complete Posts?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will hide posts if the user has no access",'users-ultra'),
  __("By selecting 'yes' will hide posts if the user has no access",'users-ultra')
       );

$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_post_title',
	__('Hide Post Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra')
       );
	   
$this->create_plugin_setting( 
        'input',
        'uultra_groups_post_title',
        __('Post Title:','users-ultra'),array(),
        __('This will be the displayed text as post title if user has no access.','users-ultra'),
        __('This will be the displayed text as post title if user has no access.','users-ultra')
);  


$this->create_plugin_setting(
	'select',
	'uultra_groups_post_content_before_more',
	__('Show post content before &lt;!--more--&gt; tag?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-ultra'),
  __('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-ultra')
       );


$this->create_plugin_setting(
        'textarea',
        'uultra_groups_post_content',
        __('Post Content','users-ultra'),array(),
        __('This content will be displayed if user has no access. ','users-ultra'),
        __('This content will be displayed if user has no access. ','users-ultra')
);


$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_post_comments',
	__('Hide Post Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-ultra')
       );
	  
$this->create_plugin_setting( 
        'input',
        'uultra_groups_post_comment_content',
        __('Post Comment Text:','users-ultra'),array(),
        __('This will be displayed text as post comment text if user has no access.','users-ultra'),
        __('This will be displayed text as post comment text if user has no access.','users-ultra')
);  
$this->create_plugin_setting(
	'select',
	'uultra_groups_allow_post_comments',
	__('Allows Post Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' allows users to comment on locked posts",'users-ultra'),
  __("By selecting 'yes' allows users to comment on locked posts",'users-ultra')
       );	  
		
?>
</table>


   <h4><?php _e("Set up the behaviour of locked pages.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_complete_page',
	__('Hide Complete Pages?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will hide pages if the user has no access",'users-ultra'),
  __("By selecting 'yes' will hide pages if the user has no access",'users-ultra')
       );

$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_page_title',
	__('Hide Page Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-ultra')
       );
	   
$this->create_plugin_setting( 
        'input',
        'uultra_groups_page_title',
        __('Page Title:','users-ultra'),array(),
        __('This will be the displayed text as page title if user has no access.','users-ultra'),
        __('This will be the displayed text as page title if user has no access.','users-ultra')
);  


$this->create_plugin_setting(
        'textarea',
        'uultra_groups_page_content',
        __('Page Content','users-ultra'),array(),
        __('This content will be displayed if user has no access. ','users-ultra'),
        __('This content will be displayed if user has no access. ','users-ultra')
);


$this->create_plugin_setting(
	'select',
	'uultra_groups_hide_page_comments',
	__('Hide Page Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-ultra')
       );
	  
	  
	  	  
$this->create_plugin_setting( 
        'input',
        'uultra_groups_page_comment_content',
        __('Page Comment Text:','users-ultra'),array(),
        __('This will be displayed text as page comment text if user has no access.','users-ultra'),
        __('This will be displayed text as page comment text if user has no access.','users-ultra')
);  
$this->create_plugin_setting(
	'select',
	'uultra_groups_allow_page_comments',
	__('Allows Page Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' allows users to comment on locked pages",'users-ultra'),
  __("By selecting 'yes' allows users to comment on locked pages",'users-ultra')
       );	 
  
		
?>
</table>

<h4><?php _e("Other Settings.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 



$this->create_plugin_setting(
	'select',
	'uultra_groups_protect_feed',
	__('Hide Post Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra')
       );
	   
  
		
?>
</table>
  
</div>
</div>


<?php }?>

<div id="tabs-privacy">

<div class="user-ultra-sect ">
  <h3><?php _e("Privacy Settings",'users-ultra'); ?></h3>
  
  <p><?php _e("In this section you can manage the privacy settings.",'users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php


$this->create_plugin_setting(
	'select',
	'uurofile_setting_display_photos',
	__('Display Photos: ','users-ultra'),
	array(
		'private' => __('Only for registered/logged in users','users-ultra'), 
		'public' => __('All visitor can see the user photos without registration','users-ultra')
		),
		
	__('.','users-ultra'),
  __('.','users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'users_can_view',
	__('Logged-in user viewing of other profiles','users-ultra'),
	array(
		1 => __('Enabled, logged-in users may view other user profiles','users-ultra'), 
		0 => __('Disabled, users may only view their own profile','users-ultra')),
	__('Enable or disable logged-in user viewing of other user profiles. Admin users can always view all profiles.','users-ultra'),
  __('If enabled, logged-in users are allowed to view other user profiles. If disabled, logged-in users may only view their own profile.','users-ultra')
        );

$this->create_plugin_setting(
	'select',
	'guests_can_view',
	__('Guests viewing of profiles','users-ultra'),
	array(
		1 => __('Enabled, make profiles publicly visible','users-ultra'), 
		0 => __('Disabled, users must login to view profiles','users-ultra')),
	__('Enable or disable guest and non-logged in user viewing of profiles.','users-ultra'),
  __('If enabled, profiles will be publicly visible to non-logged in users. If disabled, guests must log in to view profiles.','users-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_display_not_confirmed_profiles',
	__('Display Inactive User Profiles','users-ultra'),
	array(
		1 => __('Enabled, Yes. Display Inactive User Profils ','users-ultra'), 
		0 => __('Disabled, Do Not Display Inactive User Profiles.','users-ultra')),
	__('The user profiles are visible by default it does not matter if the user is active or not. You can switch this setting off here.','users-ultra'),
  __('The user profiles are visible by default it does not matter if the user is active or not. You can deactivate this function here.','users-ultra')
       );
	   
	   
	   $this->create_plugin_setting(
        'textarea',
        'uultra_display_not_confirmed_profiles_message',
        __('Custom Message:','users-ultra'),array(),
        __('This message will be displayed and a visitor is viwing an inactive profile. Example: The profile is not active, yet.','users-ultra'),
        __('This message will be displayed and a visitor is viwing an inactive profile. Example: The profile is not active, yet. ','users-ultra')
);


 $this->create_plugin_setting(
	'select',
	'uultra_block_whole_website',
	__('Block Whole Website?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If you select yes, the user will be redirected to the <strong>registration</strong> page when clicking on the <strong>registration link</strong>. <br><br><strong>VERY IMPORTANT: </strong>The Login and Registration page should be set correctly. Otherwise your website <strong>might be blocked</strong>.  <br><br><strong>WARNING USE WITH PRECAUTION: </strong> Your website will be blocked and only logged in users will be able to see the Blog's pages.",'users-ultra'),
  __("If you select yes, the user will be redirected to the <strong>my account</strong> page when clicking on the <strong>registration link</strong>. <br><br><strong>VERY IMPORTANT: </strong>The Login and Registration page should be set correctly. Otherwise your website will be blocked.  <br><br><strong>WARNING USE WITH PRECAUTION: </strong> Your website will be blocked and only logged in users will be able to see the Blog's pages.",'users-ultra')
       );

?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e("Logged In Users Pages and Posts Protection Settings",'users-ultra'); ?></h3>
  
  <p><?php _e("In this section you can manage Posts & Pages Protection module settings.",'users-ultra'); ?></p>
   <p><?php _e("This module will let you block pages and any post types and make them visible only to logged in users.",'users-ultra'); ?></p>
  
  
   <h4><?php _e("Set up the behaviour of locked posts.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 


$this->create_plugin_setting(
                'checkbox',
                'uultra_loggedin_activated',
                __('Activate Logged in Post Protection','users-ultra'),
                '1',
                __('If checked, You will be able to protect pages and post bassed on logged in users.','users-ultra'),
                __('If checked, You will be able to protect pages and post bassed on logged in users.','users-ultra')
        ); 


$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_complete_post',
	__('Hide Complete Posts?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will hide posts if the user has no access.  <strong>Please note: </strong> a 404 error message will be displayed since the post will be completely locked out.",'users-ultra'),
  __("By selecting 'yes' will hide posts if the user has no access",'users-ultra')
       );

$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_post_title',
	__('Hide Post Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra')
       );
	   
$this->create_plugin_setting( 
        'input',
        'uultra_loggedin_post_title',
        __('Post Title:','users-ultra'),array(),
        __('This will be the displayed text as post title if user has no access.','users-ultra'),
        __('This will be the displayed text as post title if user has no access.','users-ultra')
);  


$this->create_plugin_setting(
	'select',
	'uultra_loggedin_post_content_before_more',
	__('Show post content before &lt;!--more--&gt; tag?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-ultra'),
  __('By selecting "Yes"  will display the post content before the &lt;!--more--&gt; tag and after that the defined text at "Post content". If no &lt;!--more--&gt;  is set he defined text at "Post content" will shown.','users-ultra')
       );


$this->create_plugin_setting(
        'textarea',
        'uultra_loggedin_post_content',
        __('Post Content','users-ultra'),array(),
        __('This content will be displayed if user has no access. ','users-ultra'),
        __('This content will be displayed if user has no access. ','users-ultra')
);


$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_post_comments',
	__('Hide Post Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post comment text' if user has no access.",'users-ultra')
       );
	  
$this->create_plugin_setting( 
        'input',
        'uultra_loggedin_post_comment_content',
        __('Post Comment Text:','users-ultra'),array(),
        __('This will be displayed text as post comment text if user has no access.','users-ultra'),
        __('This will be displayed text as post comment text if user has no access.','users-ultra')
);  
$this->create_plugin_setting(
	'select',
	'uultra_loggedin_allow_post_comments',
	__('Allows Post Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' allows users to comment on locked posts",'users-ultra'),
  __("By selecting 'yes' allows users to comment on locked posts",'users-ultra')
       );	  
		
?>
</table>


   <h4><?php _e("Set up the behaviour of locked pages.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_complete_page',
	__('Hide Complete Pages?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will hide pages if the user has no access. <strong>Please note: </strong> a 404 error message will be displayed since the page will be completely locked out.",'users-ultra'),
  __("By selecting 'yes' will hide pages if the user has no access",'users-ultra')
       );

$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_page_title',
	__('Hide Page Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Page title' if user has no access.",'users-ultra')
       );
	   
$this->create_plugin_setting( 
        'input',
        'uultra_loggedin_page_title',
        __('Page Title:','users-ultra'),array(),
        __('This will be the displayed text as page title if user has no access.','users-ultra'),
        __('This will be the displayed text as page title if user has no access.','users-ultra')
);  


$this->create_plugin_setting(
        'textarea',
        'uultra_loggedin_page_content',
        __('Page Content','users-ultra'),array(),
        __('This content will be displayed if user has no access. ','users-ultra'),
        __('This content will be displayed if user has no access. ','users-ultra')
);


$this->create_plugin_setting(
	'select',
	'uultra_loggedin_hide_page_comments',
	__('Hide Page Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Page comment text' if user has no access.",'users-ultra')
       );
	  
	  
	  	  
$this->create_plugin_setting( 
        'input',
        'uultra_loggedin_page_comment_content',
        __('Page Comment Text:','users-ultra'),array(),
        __('This will be displayed text as page comment text if user has no access.','users-ultra'),
        __('This will be displayed text as page comment text if user has no access.','users-ultra')
);  
$this->create_plugin_setting(
	'select',
	'uultra_loggedin_allow_page_comments',
	__('Allows Page Comments?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' allows users to comment on locked pages",'users-ultra'),
  __("By selecting 'yes' allows users to comment on locked pages",'users-ultra')
       );	 
  
		
?>
</table>

<h4><?php _e("Other Settings.",'users-ultra'); ?></h4>
  <table class="form-table">
<?php 



$this->create_plugin_setting(
	'select',
	'uultra_loggedin_protect_feed',
	__('Hide Post Title?','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra'),
  __("By selecting 'yes' will show the text which is defined at 'Post title' if user has no access.",'users-ultra')
       );
	   
  
		
?>
</table>
  
</div>
</div>


<div id="tabs-add-ons">

<div class="user-ultra-sect ">
  <h3><?php _e("Add-ons Settings",'users-ultra'); ?></h3>
  
  <p><?php _e("In this section you can manage the user's wall settings.",'users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
	'select',
	'uultra_add_ons_medallions',
	__('Medallions & Fulfillments','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' The Medallions & Fulfillments Add-on will be activated.",'users-ultra'),
  __("If 'yes' The Medallions & Fulfillments Add-on will be activated.",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_add_ons_ip_defender',
	__('I.P. Defender','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' The I.P. Defender Add-on will be activated.",'users-ultra'),
  __("If 'yes' The I.P. Defender Add-on will be activated.",'users-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'uultra_add_ons_groups',
	__('Groups','users-ultra'),
	array(
		'yes' => __('Yes','users-ultra'), 
		'no' => __('No','users-ultra'),
		),
		
	__("If 'yes' The Groups Add-on will be activated.",'users-ultra'),
  __("If 'yes' The Groups Add-on will be activated.",'users-ultra')
       );
   

	  
		
?>
</table>

  
</div>
</div>


</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />
</p>

</form>