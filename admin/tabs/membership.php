<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra, $uultra_form, $uultra_group;
$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');

$forms = $uultra_form->get_all();
?>
<?php 
    
      echo $xoouserultra->xoouseradmin->check_pro_version_message();
    ?>


<?php



if(isset($_GET['settings']) && $_GET['settings']=='layout' && $_GET['package']!='' )
{
	
	//get package
	$package = $xoouserultra->paypal->get_package($_GET['package']);
	
	
	?>
    
   
    
    
    <input type="hidden" name="package_id" id="package_id" value="<?php echo $_GET['package']?>" />
 <div class="user-ultra-sect ">   
     
          
    
     <h2><?php _e("Package: ",'users-ultra'); ?><?php echo $package->package_name?></h2>
    <p><?php _e("Here you can customize the widgets and module for this package.",'users-ultra'); ?></p>
    
      <p><?php _e("Please select the Profile's Template. All the users under this membership package will use the above template.",'users-ultra'); ?></p>
            
            
            <?php 
			
			
			//template
			$profile_templates = $xoouserultra->customizer->mTemplatesList;
			
			
			$xoouserultra->customizer->mIsPaidMembership = $_GET['package'];		
			
			$current_template = $xoouserultra->customizer->get_default_profile_template();
            
			foreach($profile_templates as $template )
			{
				
				//check if selected
				$checked="";
				if($current_template==$template["template_id"])
				{
					$checked = 'style';
				
				
				}
				
				
				
			?>

                <div class="uultra_template_block"> 
                
              
                
                <span class="uultra-template-active">
                
                  <?php if($checked!=""){?>
                
                 		<i class="fa fa-check-square-o fa-2"></i> 
                 
                  <?php }?> 
                 
                 
                 </span>
      
                 <h4><?php echo $template["title"] ?></h4>  
                 
                 <img src="<?php echo xoousers_url?>/admin/images/templates/<?php echo $template["snapshot"] ?>"  />
                 <div class="uultra_temp_desc">
                 
                 <p><?php echo $template["description"] ?></p>
                   
                 </div>
                 
                  <div class="uultra_temp_opt">
                  
                  <?php if($checked==""){?>
                                    
                      <p class="btn-find">                  
                         <a href="#" class="uultra-template-user-activate" data-rel="<?php echo $template["template_id"]?>"><?php echo __('Click to Activate', 'users-ultra');?> </a>                
                      </p>
                  
                  <?php }else{?>
                  
                  	  <p class="uultra-btn-act">
                      
                      <i class="fa fa-check-square-o fa-2"></i>                  
                         <?php echo __('Active', 'users-ultra');?>             
                      </p>
                 
                  
                  <?php }?> 
                  
                 </div>
                </div>
            
            <?php }?> 
            
            
</div>

    
     <div class="user-ultra-sect ">

 <h3><?php _e("Available Widgets  ",'users-ultra'); ?></h3>
 <p>  <?php _e("These widgets are visible within the user's dashboard and they can be used by the users.  ",'users-ultra'); ?></p>
 
 <p class="submit">
	<input type="button" name="submit"  class="button button-primary uultra-widgets-add-new"  data-widget="1" value="<?php _e('Add New Widget ','users-ultra'); ?>"  /> <span id="uultra-add-w-message"></span>

</p>
 <div id="uultra-add-widget-cont" style="display:none">
 </div>
 
    <div id="uultra-add-widget-cont-cols" class="uultra-admin-available-widgets-cols " >
    
            
    
    </div>
    
    
    
    <div class="uultra-default-widgets-tool-bar">		
<p class="submit">


<a class="qtip-light  uultra-tooltip" data-hasqtip="1" oldtitle="<?php _e("PLEASE NOTE: If you don't see the default widgets please clear cache and browser files and click on the 'Restore Default Widgets' button.",'users-ultra'); ?>" title=" <?php _e("PLEASE NOTE: If you don't see the default widgets please clear cache and browser files and click on the 'Restore Default Widgets' button.",'users-ultra'); ?>" aria-describedby="qtip-1"><i class="fa fa-info-circle reg_tooltip"></i></a><input type="button" name="ultra-apply-def-widget-layout-membership" id="uultra-restore-default-widgets"  class="button button-secondary user-ultra-btn-red" value="<?php _e("Restore Default Widgets",'users-ultra'); ?>"  />

	<input type="button" name="ultra-apply-def-widget-layout-membership" id="uultra-apply-def-widget-layout-membership"  class="button button-secondary" value="<?php _e("Apply layout to all users with this membership",'users-ultra'); ?>"  />
    
    


</p>



 <div id="uultra-app-default-widget-to-u" style="display:none">
 </div>

</div>
    
     <h3><?php _e("Unavailable Widgets  ",'users-ultra'); ?></h3>
     <p>  <?php _e("Please drag&drop the widgets you would like to disable here.  ",'users-ultra'); ?></p>
  
  
        <ul id="uultra-profile-widgets-unavailable" class="uultra-connectedSortable uultra-admin-unavailable-widgets">
        
        </ul>
 
 </div>
 
 
 
<div class="user-ultra-sect "  style=" display:">

 <h3><?php _e("Modules Activation & Deactivation ",'users-ultra');  ?></h3>
  
  <p><?php _e("By default all the modules are active on Users Ultra PRO. You can use this section to activate/deactivate user's functionalities.",'users-ultra'); ?></p>
  
   <div class="uultra_modules_acvitation_block_left">
   
   <h4><strong><?php _e("Deactivate the following checked modules:",'users-ultra'); ?></strong></h4>
   
   		 <ul class="" id="uultra-user-mod-list">
  
   		</ul>
        
        <p class="submit">
        	<input type="button" name="submit"  class="button button-secondary " id="uultradmin-reset-modules-setting"  data-widget="1" value="<?php _e('Rebuild Default Links','users-ultra'); ?>"  />
            
	<input type="button" name="submit"  class="button button-primary " id="uultradmin-save-modules-setting-membership"  data-widget="1" value="<?php _e('Save Changes','users-ultra'); ?>"  />&nbsp;<span id="loading-animation-users-module" class="loading-animation-ajax"> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php _e('Saving Changes ...','users-ultra'); ?></span> 

</p>
  
   </div>
  
   <div class="uultra_modules_acvitation_block_right">
   
   <h4><strong><?php _e("User's navigator, drag&drop available:",'users-ultra'); ?></strong></h4>
     <p><?php _e("Here you can manage the links that are displayed in the user's dashboard. Spaces are allowed in the Title. Please only input characters, numbers, dash or underscore for the slug.",'users-ultra'); ?></p>
   
   <p class="submit">
	<input type="button" name="submit"  class="button button-primary uultra-links-add-new"  data-widget="1" value="<?php _e('Add New Link ','users-ultra'); ?>"  /> <span id="uultra-add-newlink-message"></span>

</p>


 <div id="uultra-add-links-cont" style="display:none"> 
  <?php echo $xoouserultra->customizer->uultra_new_links_add_form();?>
 </div>

     
   		 <ul class="" id="uultra-user-menu-option-list">
  
   		</ul>
        
       
  <?php echo $xoouserultra->customizer->uultra_link_content_editor_html();?>      
        
  
  </div>
  
           

</div>
  

 
 
 <script type="text/javascript">
var msg_loading_widgets = "<?php _e('Please wait, loading ... ','users-ultra'); ?>";
var msg_adding_widget = "<?php _e('Please wait, adding ... ','users-ultra'); ?>";
var msg_updating_message = "<?php _e('Please wait ... ','users-ultra'); ?>";
var msg_updating_widgets = "<?php _e('Please wait, updating user profiles ... ','users-ultra'); ?>";
var msg_adding_widget_done = "<?php _e('Done! ','users-ultra'); ?>";
var msg_link_deletion = "<?php _e('Are you totally sure that you want to delete it? ','users-ultra'); ?>";
var msg_widget_deletion = "<?php _e('Are you totally sure that you want to delete this widget? ','users-ultra'); ?>";
var msg_widget_rebuild = "<?php _e('Are you totally sure that you want to restore the default widgets? ','users-ultra'); ?>";

var msg_link_rebuild = "<?php _e('Are you totally sure that you want to rebuild the links? ','users-ultra'); ?>";
uultra_reload_user_modules_membership();
uultra_reload_user_menu_customizer_membership();
uultra_reload_all_active_widgets_package_setting();
</script>


<div id="uultra-spinner" class="uultra-spinner" style="display:">
            <span> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','users-ultra')?>
	</div>



<?php
}else{
?>

<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Paid Membership Settings','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
        'input',
        'paid_membership_currency',
        __('Currency','users-ultra'),array(),
        __('The default symbol for PayPal payments is USD','users-ultra'),
        __('The default symbol for PayPal payments is USD','users-ultra')
);

$this->create_plugin_setting(
        'input',
        'paid_membership_symbol',
        __('Currency Symbol','users-ultra'),array(),
        __('Input the currency symbol: Example: $','users-ultra'),
        __('Input the currency symbol: Example: $','users-ultra')
);


		
?>
</table>
<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />

</p>
</div>

  </form>      
        <div class="user-ultra-sect ">
        
        <h3> <?php _e('Membership Packages','users-ultra'); ?></h3>
        
        <p>
        <a href="#" class="button-primary uultra-add-new-package" ><?php _e('Add New Plan','users-ultra'); ?></a>
        </p>
        
        <div class="user-ultra-success uultra-notification"><?php _e('Success ','users-ultra'); ?></div>
        
        <div class="user-ultra-sect-second user-ultra-rounded" id="uultra-add-package">
        
         <h3> <?php _e('Add New Package ','users-ultra'); ?></h3>
         
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="24%"> <?php _e('Name: ','users-ultra'); ?></td>
             <td width="76%"><input type="text" id="p_name"  /></td>
           </tr>
           <tr>
             <td> <?php _e('Description: ','users-ultra'); ?></td>
             <td> <textarea  cols=""  name="p_desc" id="p_desc" style="height:80px; width:50%;"></textarea></td>
           </tr>
           <tr>
             <td> <?php _e('Price: ','users-ultra'); ?></td>
             <td><input type="text" name="p_price" id="p_price" /></td>
           </tr>
            <tr>
             <td> <?php _e('Setup Price: ','users-ultra'); ?></td>
             <td><input type="text" name="p_price_setup" id="p_price_setup" /> - Example: $300.00 USD for the first month
Then $250.00 USD for each month</td>
           </tr>
           <tr>
             <td> <?php echo _e('Every:','users-ultra')?>:</td>
             <td>
             <select name="p_every" id="p_every">
             <option value="1" selected="selected">1</option>
              <?php
			  
			  $i = 2;
              
			  while($i <=31){
			  ?>
              
                 <option value="<?php echo $i?>"><?php echo $i?></option>
               
               
               <?php 
			    $i++;
			   }?>
             </select></td>
           </tr>
           <tr>
             <td> <?php echo _e('Billing Period:','users-ultra')?></td>
             <td><label for="p_period"></label>
               <select name="p_period" id="p_period">
                 <option value="M"><?php _e('Months: ','users-ultra'); ?></option>
                 <option value="W"><?php _e('Weeks: ','users-ultra'); ?></option>
                 <option value="D"><?php _e('Days: ','users-ultra'); ?></option>
                  <option value="Y"><?php _e('Years: ','users-ultra'); ?></option>
               </select></td>
           </tr>
           <tr>
             <td> <?php echo _e('Type: ','users-ultra')?></td>
             <td><select name="p_type" id="p_type">
               <option value="recurring" selected="selected"> <?php _e('Recurring ','users-ultra'); ?></option>
               <option value="onetime"> <?php _e('One-Time ','users-ultra'); ?></option>
             </select></td>
           </tr>
           <tr>
             <td><?php echo _e('Requires Admin Moderation: ','users-ultra')?></td>
             <td><select name="p_moderation" id="p_moderation">
               <option value="yes"> <?php _e('Yes','users-ultra'); ?></option>
               <option value="no" selected="selected"> <?php _e('No','users-ultra'); ?></option>
             </select></td>
           </tr>
           
           <tr>
             <td><?php echo _e('Role To Assign:','users-ultra')?></td>
             <td><?php echo $xoouserultra->role->get_package_roles($selected_package);?></td>
           </tr>
           
          <?php if(isset($forms)){ ?>
            <tr>
             <td><?php echo _e('Custom Form To Assign:','users-ultra')?></td>
             <td><select name="p_custom_registration_form" id="p_custom_registration_form">
				<option value="" selected="selected">
					<?php _e('Default Registration Form','users-ultra'); ?>
				</option>
                
                <?php foreach ( $forms as $key => $form )
				{?>
				<option value="<?php echo $key?>">
					<?php echo $form['name']; ?>
				</option>
                
                <?php }?>
		</select></td>
           </tr>
           
            <?php }?>
           
           <?php if(isset($uultra_group)) {?>
           
            <tr>
             <td><?php echo _e('Group To Assign:','users-ultra')?></td>
             <td><select name="p_user_group" id="p_user_group">
				<option value="" selected="selected">
					<?php _e("Don't assign any group",'users-ultra'); ?>
				</option>
                
                <?php 
				
				$groups = $uultra_group->get_all();
				foreach ( $groups as $group )
				{?>
				<option value="<?php echo $group->group_id?>">
					<?php echo $group->group_name?>
				</option>
                
                <?php }?>
		</select></td>
           </tr>
           
           <?php }?>

           
           
          </table>
          
          <h3><?php echo _e('Pricing Table Customization','users-ultra')?></h3>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
                <td width="24%"> <?php echo _e('Name/Price Font Color','users-ultra')?></td>
                <td width="76%"><input name="p_price_color" type="text" id="p_price_color" value="" class="color-picker" data-default-color=""/> 
         </td>
              </tr>
              <tr>
                <td> <?php echo _e('Name/Price Background Color','users-ultra')?></td>
                <td><input name="p_price_bg_color" type="text" id="p_price_bg_color" value="" class="color-picker"  data-default-color="" /> 
               </td>
              </tr>
              
               <tr>
                <td> <?php echo _e('Sign Up Button Text Color','users-ultra')?></td>
                <td><input name="p_signup_color" type="text" id="p_signup_color" value="" class="color-picker"  data-default-color="" /> 
               </td>
              </tr>
              
              <tr>
                <td> <?php echo _e('Sign Up Button Background Color','users-ultra')?></td>
                <td><input name="p_signup_bg_color" type="text" id="p_signup_bg_color" value="" class="color-picker"  data-default-color="" /> 
               </td>
              </tr>
            
            </table>
          
          <h3> <?php echo _e('Package Limits:','users-ultra')?></h3>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
                <td width="24%"> <?php echo _e('Max Upload Photos:','users-ultra')?></td>
                <td width="76%"><input name="p_max_photos" type="text" id="p_max_photos" value="9999"  /> 
          -  <?php echo _e('9999 for unlimited photos','users-ultra')?></td>
              </tr>
              <tr>
                <td> <?php echo _e('Max Galleries:','users-ultra')?></td>
                <td><input name="p_max_gallery" type="text" id="p_max_gallery" value="9999"  /> 
                -  <?php echo _e('9999 for unlimited galleries','users-ultra')?></td>
              </tr>
              <tr>
                <td> <?php echo _e('Max Posts','users-ultra')?></td>
                <td><input name="p_max_posts" type="text" id="p_max_posts" value="9999"  />
                  -  <?php echo _e('9999 for unlimited posts','users-ultra')?></td>
              </tr>
              
            </table>
            
            
              <h3> <?php echo _e('Pay per Read:','users-ultra')?></h3>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">

              
               <tr>
                <td width="24%"> <?php echo _e('Allowed Posts to Read','users-ultra')?> <?php echo _e('Pay per Read:','users-ultra')?></td>
                <td width="76%"><input name="p_max_posts_read" type="text" id="p_max_posts_read" value=""  />
                  -  <?php echo _e('Post IDs separated by commas: 1,4,5','users-ultra')?> </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
              <h3> <?php echo _e('Credits/Points:','users-ultra')?></h3>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">

              
               <tr>
                <td width="24%"><?php echo _e('Quantity:','users-ultra')?></td>
                <td width="76%"><input name="p_credits" type="text" id="p_credits" value="0"  />
                  -  <?php echo _e('decimals allowed. Example 9,10','users-ultra')?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
          <p>
          <a href="#" class="button uultra-close-new-package" ><?php _e('Cancel','users-ultra'); ?></a>
           <a href="#" class="button-primary uultra-add-new-package-confirm" ><?php _e('Confirm','users-ultra'); ?></a>
        </p>
        </div>
        
        <div id="usersultra-data_list">
        
        <?php echo _e('loading ...','users-ultra'); ?>
        
        </div>
        


        
        
        
        
        </div>
        
         <script type="text/javascript">
		  
		 var package_error_message_name = "<?php _e('Please, input a name ','users-ultra'); ?>";
		 var package_error_message_desc = "<?php _e('Please, input a description ','users-ultra'); ?>";
		 var package_error_message_price = "<?php _e('Please, input a price ','users-ultra'); ?>";
		  var package_confirmation = "<?php _e('Are you totally sure that you want to delete this package? ','users-ultra'); ?>";
		 
		 </script>
         
          <script type="text/javascript">
				jQuery(document).ready(function($){
               
					   $.post(ajaxurl, {
									action: 'get_packages_ajax'
									
									}, function (response){									
																
									$("#usersultra-data_list").html(response);
									
														
							});
							
					
				});
                    
                 </script>
                 
  <?php }?>
        
