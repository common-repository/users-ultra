<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;

        
?>

<h3><?php _e('Payment Gateways Settings','users-ultra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
    
    <?php 
    
      echo $xoouserultra->xoouseradmin->check_pro_version_message();
    ?>
  <h3><?php _e('PayPal','users-ultra'); ?></h3>
  
  <p><?php _e('Here you can configure PayPal if you wish to accept paid registrations','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_paypal_active',
                __('Activate PayPal','users-ultra'),
                '1',
                __('If checked, PayPal will be activated as payment method','users-ultra'),
                __('If checked, PayPal will be activated as payment method','users-ultra')
        ); 

$this->create_plugin_setting(
	'select',
	'uultra_send_ipn_to_admin',
	__('The Paypal IPN response will be sent to the admin','users-ultra'),
	array(
		'no' => __('No','users-ultra'), 
		'yes' => __('Yes','users-ultra'),
		),
		
	__("If 'yes' the admin will receive the whole Paypal IPN response. This helps to troubleshoot issues.",'users-ultra'),
  __("If 'yes' the admin will receive the whole Paypal IPN response. This helps to troubleshoot issues.",'users-ultra')
       );

$this->create_plugin_setting(
        'input',
        'gateway_paypal_email',
        __('PayPal Email Address','users-ultra'),array(),
        __('Enter email address associated to your PayPal account.','users-ultra'),
        __('Enter email address associated to your PayPal account.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_sandbox_email',
        __('Paypal Sandbox Email Address','users-ultra'),array(),
        __('This is not used for production, you can use this email for testing.','users-ultra'),
        __('This is not used for production, you can use this email for testing.','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_currency',
        __('Currency','users-ultra'),array(),
        __('Please enter the currency, example USD.','users-ultra'),
        __('Please enter the currency, example USD.','users-ultra')
);

$this->create_plugin_setting(
	'select',
	'gateway_paypal_mode',
	__('Mode','users-ultra'),
	array(
		1 => __('Production Mode','users-ultra'), 
		2 => __('Test Mode (Sandbox)','users-ultra')
		),
		
	__('.','users-ultra'),
  __('.','users-ultra')
       );




		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Bank Deposit/Cash Other','users-ultra'); ?></h3>
  
  <p><?php _e('Here you can configure an alternative payment method for your clients.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_bank_active',
                __('Activate Bank Deposit','users-ultra'),
                '1',
                __('If checked, Bank/Cash will be activated as payment method','users-ultra'),
                __('If checked, Bank/Cash will be activated as payment method','users-ultra')
        ); 


$this->create_plugin_setting(
        'input',
        'gateway_bank_label',
        __('Custom Label','users-ultra'),array(),
        __('Example: Bank Deposit , Cash, Wire etc.','users-ultra'),
        __('Example: Bank Deposit , Cash, Wire etc.','users-ultra')
);


$this->create_plugin_setting(
        'textarea',
        'gateway_bank_payment_information',
        __('Payment Details','users-ultra'),array(),
        __('Input here all the relevant information that will be sent to the client, for example: the bank account details.','users-ultra'),
        __('Input here all the relevant information that will be sent to the client, for example: the bank account details.','users-ultra')
);

		
?>
</table>

  
</div>



<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />
	
</p>

</form>