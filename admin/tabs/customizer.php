<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;
$profile_customizing = array();
$profile_customizing = $xoouserultra->customizer->get_profile_customizing();

//template
$profile_templates = $xoouserultra->customizer->mTemplatesList;
?>

<div class="user-ultra-sect ">
    
     <?php 
    
      echo $xoouserultra->xoouseradmin->check_pro_version_message();
    ?>

 <h3><?php _e("Customizer ",'users-ultra'); //widget ID: 1 ?></h3>    
  
<div id="tabs-uultra" class="uultra-multi-tab-options">
<ul class="nav-tab-wrapper uultra-nav-pro-features">
<li class="nav-tab uultra-pro-li"><a href="#tabs-1" title="<?php _e('Profile Templates','users-ultra'); ?>"><?php _e('Profile Templates','users-ultra'); ?></a></li>
<li class="nav-tab uultra-pro-li"><a href="#tabs-2" title="<?php _e('General Customization','users-ultra'); ?>"><?php _e('General Customization','users-ultra'); ?> </a></li>
<li class="nav-tab uultra-pro-li"><a href="#tabs-3" title="<?php _e('Widgets & Modules','users-ultra'); ?>"><?php _e('Widgets & Modules','users-ultra'); ?> </a></li>

<li class="nav-tab uultra-pro-li"><a href="#tabs-5" title="<?php _e('CSS','users-ultra'); ?>"><?php _e('CSS','users-ultra'); ?> </a></li>
</ul>



<div id="tabs-1">

  <p><?php _e("Please select the Profile's Template.",'users-ultra'); ?></p>
            
            
            <?php 
			
			
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
            
            
      
            
            
<div class="user-ultra-sect ">

<form method="post" action="">
<input type="hidden" name="update_settings" />
  <h3><?php _e('Template Styles','users-ultra'); ?></h3>
  
  <p><?php _e('.','users-ultra'); ?></p>
  
  <table class="form-table">
<?php 
   
		$this->create_plugin_setting(
                'checkbox',
                'templates_flat_css',
                __('Use Flat Styles','users-ultra'),
                '1',
                __('If checked, the profile will not include rounded borders, the widgets will look flat','users-ultra'),
                __('If checked, the profile will not include rounded borders, the widgets will look flat','users-ultra')
        ); 
		
		$this->create_plugin_setting(
                'checkbox',
                'templates_remove_spikes',
                __('Remove Widget Spikes','users-ultra'),
                '1',
                __('Spikes on widgets will be removed. This helps to make the profile looks much more flat.','users-ultra'),
                __('Spikes on widgets will be removed. This helps to make the profile looks much more flat.','users-ultra')
        ); 
		
		
		
		
		?>
		
</table>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />

</p>

</form>
            
</div>

            
</div>



<div id="tabs-2">

 <h3><?php _e("User's Profile Customizer - General Customization ",'users-ultra'); //widget ID: 1 ?></h3>

  <p><?php _e("Use this section to customize the main structure of the user's profile.",'users-ultra'); ?></p>
  
    <div class="left_widget_customizer"> 
  
  <h4><?php _e("Main Profile Container ",'users-ultra');  ?></h4>  
  
<table width="100%" border="0" cellspacing="0" cellpadding="0">

			
              
          <tr>
                <td width="50%"> <?php echo _e('Background Color','users-ultra')?></td>
                <td width="50%"><input name="uultra_profile_bg_color" type="text" id="uultra_profile_bg_color" value="<?php echo $profile_customizing['uultra_profile_bg_color'];?>" class="color-picker" data-default-color=""/> 
         </td>
              </tr>
              
               
              <tr>
                <td> <?php echo _e('Background Transparent','users-ultra')?></td>
                <td><select name="uultra_profile_bg_color_transparent" id="uultra_profile_bg_color_transparent">
               <option value="yes" <?php if($profile_customizing['uultra_profile_bg_color_transparent']=="yes"){ echo 'selected="selected"';}?> > <?php echo _e('Yes','users-ultra')?></option>
               <option value="no" <?php if($profile_customizing['uultra_profile_bg_color_transparent']=="no"){ echo 'selected="selected"';}?>> <?php echo _e('No','users-ultra')?></option>
             </select>
               </td>
              </tr>
                          
            </table>
            
            
            

</div>

            <div class="left_widget_customizer"> 
  
  <h4><?php _e("Inferior Profile Container ",'users-ultra');  ?></h4>  
  
<table width="100%" border="0" cellspacing="0" cellpadding="0">

			
              
          <tr>
                <td width="50%"> <?php echo _e('Background Color','users-ultra')?></td>
                <td width="50%"><input name="uultra_profile_inferior_bg_color" type="text" id="uultra_profile_inferior_bg_color" value="<?php echo $profile_customizing['uultra_profile_inferior_bg_color'];?>" class="color-picker" data-default-color=""/> 
         </td>
              </tr>
              
               
              <tr>
                <td> <?php echo _e('Background Transparent','users-ultra')?></td>
                <td><select name="uultra_profile_inferior_bg_color_transparent" id="uultra_profile_inferior_bg_color_transparent">
                <option value="yes" <?php if($profile_customizing['uultra_profile_inferior_bg_color_transparent']=="yes"){ echo 'selected="selected"';}?> ><?php echo _e('Yes','users-ultra')?></option>
               <option value="no" <?php if($profile_customizing['uultra_profile_inferior_bg_color_transparent']=="no"){ echo 'selected="selected"';}?>><?php echo _e('No','users-ultra')?></option>
             </select>
               </td>
              </tr>
                          
            </table>
            
            
            

</div>

 <div class="left_widget_customizer"> 
  
  <h4><?php _e("Image Background Color",'users-ultra');  ?></h4>  
  
<table width="100%" border="0" cellspacing="0" cellpadding="0">

			
              
          <tr>
                <td width="50%"> <?php echo _e('Background Color','users-ultra')?></td>
                <td width="50%"><input name="uultra_profile_image_bg_color" type="text" id="uultra_profile_image_bg_color" value="<?php echo $profile_customizing['uultra_profile_image_bg_color'];?>" class="color-picker" data-default-color=""/> 
         </td>
         </tr>
         
         <tr>
                <td  colspan="2"> <?php echo _e("If you set a color the default image won't be displayed.",'users-ultra')?></td>
                
         </tr>
         
          
               
                                       
            </table>
            
            
              <h4><?php _e("Default Background Image",'users-ultra');  ?></h4> 
              
              <p id="uultra-u-p-bgimage"><?php echo $xoouserultra->customizer->get_custom_bg_for_user_profile_admin();?></p> 
  
   <form action=""  name="uultra-form-custom-profile-bg-form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="uultra-form-custom-profile-bg" />
                   
          
           <p class="submit">
	<input type="file" name="uultra_profile_bg_image" class="" value="<?php _e('Choose File','users-ultra'); ?>"  />
    <br /><?php _e(' <b>ONLY JPG, JPEG, PNG EXTENSIONS ALLOWED: </b>  ','users-ultra'); ?>
    
    <br /><?php _e(' <b>RECOMMENDED SIZE: 1170 x 450 pixels </b>  ','users-ultra'); ?>
	
     </p>
     
     <p class="submit">
	<input type="submit" name="submit"  class="button button-primary " value="<?php _e('Upload Image','users-ultra'); ?>"  />
	
       </p>
     </form>
        
        
        
              <h4><?php _e("Default User Avatar",'users-ultra');  ?></h4> 
              
              <p id="uultra-u-p-customuavatar"><?php echo $xoouserultra->customizer->get_custom_user_avatar_admin();?></p> 
  
   <form action=""  name="uultra-form-custom-user-avatar-form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="uultra-form-custom-user-avatar" />
                   
          
           <p class="submit">
           <?php
           
		   $avatar_w = $xoouserultra->get_option('media_avatar_width');
		   $avatar_h = $xoouserultra->get_option('media_avatar_height');
		   ?>
	<input type="file" name="uultra_custom_user_avatar" class="" value="<?php _e('Choose File','users-ultra'); ?>"  />
    <br /><?php _e(' <b>ONLY JPG, JPEG, PNG EXTENSIONS ALLOWED: </b>  ','users-ultra'); ?>
    
    <br /><?php _e(' <b>RECOMMENDED SIZE: '.$avatar_w.' x '.$avatar_h.' pixels </b>  ','users-ultra'); ?>. <?php _e('Please note: The avatar will be resized automatically if you upload a larger image ','users-ultra'); ?>
	
     </p>
     
     <p class="submit">
	<input type="submit" name="submit"  class="button button-primary " value="<?php _e('Upload Image','users-ultra'); ?>"  />
	
       </p>
     </form>
         
       
               
                                       
            </table>

</div>

           
<p class="submit">
	<input type="button" name="submit"  class="button button-primary uultra-profile-customizer-save-style"  data-widget="1" value="<?php _e('Save Changes','users-ultra'); ?>"  /> <span id="uultra-prof-custom-basic-message"></span>

</p>
 
 

</div> 

<div id="uultra-spinner" class="uultra-spinner" style="display:">
            <span> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','users-ultra')?>
	</div>

<div id="tabs-3">

<?php 

$ready_module =  true;
if($ready_module){?>

 <div class="user-ultra-sect ">
 
 
 <h3><?php _e("Membership Widgets & Modules Settings  ",'users-ultra'); ?></h3>
 <p>  <?php _e("This will allow you to set different layouts and activate different modules for your users based on their membership package. ",'users-ultra'); ?></p>
 
 <?php echo $xoouserultra->paypal->get_packages_customizer();?>
 
 </div>
 
 <?php }?>

 <div class="user-ultra-sect ">

 <h3><?php _e("Available Widgets  ",'users-ultra'); ?></h3>
 <p>  <?php _e("These widgets are visible in the user's dashboard and they can be used by the users. You can set the default widgets and set the default display order to all common users. ",'users-ultra'); ?></p>
 
  <p> <strong> <?php _e("Please note: ",'users-ultra'); ?> </strong> <?php _e(" These changes will be applied to new users only. ",'users-ultra'); ?></p>
 
<p class="submit">
	<input type="button" name="submit"  class="button button-primary uultra-widgets-add-new"  data-widget="1" value="<?php _e('Add New Widget ','users-ultra'); ?>"  /> <span id="uultra-add-w-message"></span>

</p>
 <div id="uultra-add-widget-cont" style="display:none">
 </div>
 

    <div id="uultra-add-widget-cont-cols" class="uultra-admin-available-widgets-cols " >
    
            
    
    </div>
    
     
 
<div class="uultra-default-widgets-tool-bar">		
<p class="submit">

<a class="qtip-light  uultra-tooltip" data-hasqtip="1" oldtitle="<?php _e("PLEASE NOTE: If you don't see the default widgets please clear cache and browser files and click on the 'Restore Default Widgets' button.",'users-ultra'); ?>" title=" <?php _e("PLEASE NOTE: If you don't see the default widgets please clear cache and browser files and click on the 'Restore Default Widgets' button.",'users-ultra'); ?>" aria-describedby="qtip-1"><i class="fa fa-info-circle reg_tooltip"></i></a>
<input type="button" name="ultra-apply-def-widget-layout-membership" id="uultra-restore-default-widgets"  class="button button-secondary user-ultra-btn-red" value="<?php _e("Restore Default Widgets",'users-ultra'); ?>"  />

	<input type="button" name="uultra-apply-def-widget-layout" id="uultra-apply-def-widget-layout"  class="button button-secondary" value="<?php _e("Apply layout to common users",'users-ultra'); ?>"  />
   
    


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
            
	<input type="button" name="submit"  class="button button-primary " id="uultradmin-save-modules-setting"  data-widget="1" value="<?php _e('Save Changes','users-ultra'); ?>"  />&nbsp;<span id="loading-animation-users-module" class="loading-animation-ajax"> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php _e('Saving Changes ...','users-ultra'); ?></span> 

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
  

</div>




<div id="tabs-5">

<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Custom CSS','users-ultra'); ?></h3>
  
  <p><?php _e('Use this section to add custom CSS styles.','users-ultra'); ?></p>
  
   <table class="form-table">
<?php 

$this->create_plugin_setting(
        'textarea',
        'xoousersultra_custom_css',
        __('Custom CSS Style','users-ultra'),array(),
        __('You can write some custom CSS style coding here','users-ultra'),
        __('You can write some custom CSS style coding here','users-ultra')
);

?>
  
 
 </table>

  
</div>




<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','users-ultra'); ?>"  />

</p>

</form>


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
uultra_reload_user_modules();
uultra_reload_user_menu_customizer();
uultra_reload_all_active_widgets();
</script>