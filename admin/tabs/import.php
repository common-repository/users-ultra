<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;
	
?>



 <div class="user-ultra-sect ">
        
        <h3><?php _e('Synchronize Existing Users','users-ultra'); ?></h3>
        <p><?php _e('This feature allows you to synchronize the already users in your WordPress website so they can be recognized by Users Ultra ','users-ultra'); ?></p>
        
        
        <p class="submit">
	<input type="submit" name="submit" id="uultra-btn-sync-btn" class="button button-primary " value="<?php _e('Start Sync Now','users-ultra'); ?>"  />
	
       </p>
       
       <p id='uultra-sync-results'>
       
       </p>
                     
       
    
</div>  

        
 <div class="user-ultra-sect ">
        
        <h3><?php _e('Import Users','users-ultra'); ?></h3>
        <p><?php _e('This feature lets you import users easily into the Users Ultra System. The file must be CSV format. Delimited Comma-separated Values ','users-ultra'); ?></p>
        
        <div >  <?php _e('File must contain at least <strong>5 columns:  username, email, display name, first name and last name</strong>. These should be the first five columns and it should be placed <strong>in this order: username, email, display name, first name and last name</strong>. If there are more columns, Users Ultra Pro will manage it automatically.','users-ultra'); ?>
        </div>
        
      
             
                
       
   <form action=""  name="uultra-form-cvs-form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="uultra-form-cvs-form-conf" />
                   
          
           <p class="submit">
	<input type="file" name="file_csv" class="" value="<?php _e('Choose File','users-ultra'); ?>"  /><?php _e(' <b>ONLY CSV EXTENSIONS ALLOWED: </b>  ','users-ultra'); ?>
	
     </p>
       
     <h4><?php _e('Account Activation:','users-ultra'); ?></h4>
       
     <p>
       <input name="uultra-send-welcome-email" type="checkbox" id="uultra-send-welcome-email" value="1" checked="checked" />  <?php _e('Send welcome email with new password.','users-ultra'); ?><br />
         
         
          <label>
           <input name="uultra-activate-account" type="radio" id="RadioGroup1_1" value="active" checked="checked" />
            <?php _e('Activate account automatically.','users-ultra'); ?></label>
       
    <br />
       
       
 <input type="radio" name="uultra-activate-account" value="pending" id="RadioGroup1_0" />
            <?php _e('Send Activation Link.','users-ultra'); ?></label>
           <?php _e('<strong> PLEASE NOTE</strong>: the account status will be &quot;pending&quot; until the user clicks on the activation link.','users-ultra'); ?><br />
     </p>
     
     
      <h4><?php _e('Default Account Package:','users-ultra'); ?></h4>
      
       <p><?php _e('Users will be assinged the following package:','users-ultra'); ?></p>
       <p><?php echo $xoouserultra->paypal->get_all_package_list_box();?></p>
      
       
     <p class="submit">
	<input type="submit" name="submit"  class="button button-primary " value="<?php _e('Start Importing','users-ultra'); ?>"  />
	
       </p>
       
        <p>
        
        <?php echo $xoouserultra->userpanel->messages_process;?>
	    </p>
       
       
            
             
         </form>
    
</div>

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Auto Sync with WooCommerce','users-ultra'); ?></h3>
        
 <?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
        <p><?php _e('Syncing with WooCommerce will automatically add WooCommerce customer profile fields to your Users Ultra Plugin. A quick way to have a WooCommerce account page integrated with Users Ultra. Just click the following button and Users Ultra will do the work for you.','users-ultra'); ?></p>
        
        
              
       <p><a href="<?php echo add_query_arg( array('sync' => 'woocommerce') ); ?>" class="button button-secondary"><?php _e('Sync and keep existing fields','users-ultra'); ?></a> 
<a href="<?php echo add_query_arg( array('sync' => 'woocommerce_clean') ); ?>" class="button button-secondary"><?php _e('Sync and delete existing fields','users-ultra'); ?></a></p>
       
       <p id='uultra-sync-woo-results'>
       
       </p>
                     
 <?php } else { ?>

<p><?php _e('Please install WooCommerce plugin first.','users-ultra'); ?></p>

<?php } ?>      
    
</div> 

<script>
var message_sync_users = "<?php echo _e('Please wait, this process may take several minutes','users-ultra')?>"
var message_upgrade_media = "<?php echo _e('Are you totally sure that you want to upgrade?','users-ultra')?>"

</script>
