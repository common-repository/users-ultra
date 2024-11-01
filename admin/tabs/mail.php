<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;
	
?>
<h3><?php _e('Notifications Settings','users-ultra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Advanced Email Options','users-ultra'); ?></h3>  
  <p><?php _e('Here you can control how Users Ultra Pro will send the notification to your users.','users-ultra'); ?></p>
  
   <table class="form-table">
<?php 
$this->create_plugin_setting(
                'checkbox',
                'uultra_smtp_mailing_html_txt',
                __('Send as HTML','users-ultra'),
                '1',
                __('If checked the email format will be HTML. By default text/plain text format is used.','users-ultra'),
                __('If checked the email format will be HTML. By default text/plain text format is used.','users-ultra')
        ); 

$this->create_plugin_setting(
        'input',
        'messaging_send_from_name',
        __('Send From Name','users-ultra'),array(),
        __('Enter the your name or company name here.','users-ultra'),
        __('Enter the your name or company name here.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'messaging_send_from_email',
        __('Send From Email','users-ultra'),array(),
        __('Enter the email address to be used when sending emails.','users-ultra'),
        __('Enter the email address to be used when sending emails.','users-ultra')
);

$this->create_plugin_setting(
	'select',
	'uultra_smtp_mailing_mailer',
	__('Mailer:','users-ultra'),
	array(
		'mail' => __('Use the PHP mail() function to send emails','users-ultra'),
		'smtp' => __('Send all Users Ultra emails via SMTP','users-ultra'), 
		'mandrill' => __('Send all Users Ultra emails via Mandrill','users-ultra'),
		'third-party' => __('Send all Users Ultra emails via Third-party plugin','users-ultra'), 
		
		),
		
	__('Specify which mailer method Users Ultra should use when sending emails.','users-ultra'),
  __('Specify which mailer method Users Ultra should use when sending emails.','users-ultra')
       );
	   
$this->create_plugin_setting(
                'checkbox',
                'uultra_smtp_mailing_return_path',
                __('Return Path','users-ultra'),
                '1',
                __('Set the return-path to match the From Email','users-ultra'),
                __('Set the return-path to match the From Email','users-ultra')
        ); 
?>
 </table>
 <p> <strong><?php _e('This options should be set only if you have chosen to send email via SMTP','users-ultra'); ?></strong></p>
  <table class="form-table">
 <?php
$this->create_plugin_setting(
        'input',
        'uultra_smtp_mailing_host',
        __('SMTP Host:','users-ultra'),array(),
        __('Specify host name or ip address.','users-ultra'),
        __('Specify host name or ip address.','users-ultra')
); 

$this->create_plugin_setting(
        'input',
        'uultra_smtp_mailing_port',
        __('SMTP Port:','users-ultra'),array(),
        __('Specify Port.','users-ultra'),
        __('Specify Port.','users-ultra')
); 


$this->create_plugin_setting(
	'select',
	'uultra_smtp_mailing_encrytion',
	__('Encryption:','users-ultra'),
	array(
		'none' => __('No encryption','users-ultra'),
		'ssl' => __('Use SSL encryption','users-ultra'), 
		'tls' => __('Use TLS encryption','users-ultra'), 
		
		),
		
	__('Specify the encryption method.','users-ultra'),
  __('Specify the encryption method.','users-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_smtp_mailing_authentication',
	__('Authentication:','users-ultra'),
	array(
		'false' => __('No. Do not use SMTP authentication','users-ultra'),
		'true' => __('Yes. Use SMTP Authentication','users-ultra'), 
		
		),
		
	__('Specify the authentication method.','users-ultra'),
  __('Specify the authentication method.','users-ultra')
       );

$this->create_plugin_setting(
        'input',
        'uultra_smtp_mailing_username',
        __('Username:','users-ultra'),array(),
        __('Specify Username.','users-ultra'),
        __('Specify Username.','users-ultra')
); 

$this->create_plugin_setting(
        'input',
        'uultra_smtp_mailing_password',
        __('Password:','users-ultra'),array(),
        __('Input Password.','users-ultra'),
        __('Input Password.','users-ultra')
); 


 ?>
 
 </table>
 
 
 <p><strong><?php _e('This options should be set only if you have chosen to send email via Mandrill','users-ultra'); ?></strong></p>

  <table class="form-table">
 <?php
$this->create_plugin_setting(
        'input',
        'uultra_mandrill_api_key',
        __('Mandrill API Key:','users-ultra'),array(),
        __('Specify Mandrill API. Find out more info here: https://mandrillapp.com/api/docs/','users-ultra'),
        __('Specify Mandrill API.','users-ultra')
); 

?>
 
 </table>
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Custom Messages','users-ultra'); ?></h3>
  
  <p><?php _e('This message will be displayed in the User Panel Controls','users-ultra'); ?></p>
  
   <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'messaging_private_all_users',
        __('Message To Display','users-ultra'),array(),
        __('This message will be displayed in the User Panel Controls','users-ultra'),
        __('This message will be displayed in the User Panel Controls','users-ultra')
);

?>
  
 
 </table>

</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Welcome Email Address','users-ultra'); ?></h3>
  
  <p><?php _e('This is the welcome email that is sent to the client when registering a new account','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_client',
        __('Client Welcome Message','users-ultra'),array(),
        __('This message will be sent to the user.','users-ultra'),
        __('This message will be sent to the user.','users-ultra')
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_with_activation_client',
        __('Account Activation Message','users-ultra'),array(),
        __('This message will be sent to the user if they need to activate the account by using the activation link.','users-ultra'),
        __('This message will be sent to the user if they need to activate the account by using the activation link.','users-ultra')
);

//paid
$this->create_plugin_setting(
        'textarea',
        'account_upgrade_email_client',
        __('Client Account Upgrade Message','users-ultra'),array(),
        __('This message will be sent to the user when upgrading.','users-ultra'),
        __('This message will be sent to the user when upgrading.','users-ultra')
);

$this->create_plugin_setting(
        'textarea',
        'account_upgrade_email_admin',
        __('Admin Account Upgrade Message','users-ultra'),array(),
        __('This message will be sent to the admin when upgrading.','users-ultra'),
        __('This message will be sent to the admin when upgrading.','users-ultra')
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_admin',
        __('Admin Account Upgrade Message','users-ultra'),array(),
        __('This message will be sent to the admin when a user upgrades.','users-ultra'),
        __('This message will be sent to the admin when a user upgrades.','users-ultra')
);
//end paid

$this->create_plugin_setting(
        'textarea',
        'messaging_re_send_activation_link',
        __('Resend Activation Link','users-ultra'),array(),
        __('This message will be sent to the user when clicking the re-send activation option.','users-ultra'),
        __('This message will be sent to the user when clicking the re-send activation option.','users-ultra')
);

$this->create_plugin_setting(
        'textarea',
        'account_verified_sucess_message_body',
        __('Account Verified Message','users-ultra'),array(),
        __('This message will be sent to the users when they verify their accounts.','users-ultra'),
        __('This message will be sent to the users when they verify their accounts.','users-ultra')
);

$this->create_plugin_setting(
        'textarea',
        'password_reset_confirmation',
        __('Password Changed Confirmation','users-ultra'),array(),
        __('This message will be sent to the user when the password has been reset.','users-ultra'),
        __('This message will be sent to the user when the password has been reset.','users-ultra')
);


$this->create_plugin_setting(
        'textarea',
        'messaging_admin_moderation_user',
        __('Client Admin Approval Email','users-ultra'),array(),
        __('This message will be sent to the user if the account needs to be approved by the admin.','users-ultra'),
        __('This message will be sent to the user if the account needs to be approved by the admin.','users-ultra')
);
$this->create_plugin_setting(
        'textarea',
        'messaging_admin_moderation_admin',
        __('Admin New User Approval Email','users-ultra'),array(),
        __('This message will be sent to the admin if an account needs to be approved manually.','users-ultra'),
        __('This message will be sent to the admin if an account needs to be approved manually.','users-ultra')
);


$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_client_admin',
        __('Admin New User Message','users-ultra'),array(),
        __('This message will be sent to the admin.','users-ultra'),
        __('This message will be sent to the admin.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'messaging_paid_email_admin',
        __('Admin New Paid User Message','users-ultra'),array(),
        __('This message will be sent to the admin.','users-ultra'),
        __('This message will be sent to the admin.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_with_activation_admin',
        __('Admin Pending Activation Message','users-ultra'),array(),
        __('This message will be sent to the admin if the user needs manual activation.','users-ultra'),
        __('This message will be sent to the admin if the user needs manual activation.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'messaging_user_pm',
        __('User Private Message','users-ultra'),array(),
        __('This message will be sent to users when other users send a private message.','users-ultra'),
        __('This message will be sent to users when other users send a private message.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'messaging_user_pm_from_admin',
        __('Private Message From Admin','users-ultra'),array(),
        __('This message will be sent to users when the admin sends a message.','users-ultra'),
        __('This message will be sent to users when the admin sends a message','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'message_friend_request',
        __('Friend Request','users-ultra'),array(),
        __('This message is sent to the users when a friend request is sent','users-ultra'),
        __('This message is sent to the users when a friend request is sent','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'reset_lik_message_body',
        __('Password Reset','users-ultra'),array(),
        __('This message will be sent to users when requesting a new password.','users-ultra'),
        __('This message will be sent to users when requesting a new password.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'admin_account_active_message_body',
        __('Account Activation','users-ultra'),array(),
        __('This message is sent when the admin approves the user account.','users-ultra'),
        __('This message is sent when the admin approves the user account.','users-ultra')
		
);

$this->create_plugin_setting(
        'textarea',
        'admin_account_deny_message_body',
        __('Deny Account Activation','users-ultra'),array(),
        __('This message is sent when the admin does not approve the user account.','users-ultra'),
        __('This message is sent when the admin does not approve the user account.','users-ultra')
		
);




		
?>
</table>

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />

</p>

</form>