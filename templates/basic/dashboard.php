<?php
global $xoouserultra;

$module = "";
$custom_module = "";
$act= "";
$gal_id= "";
$page_id= "";
$view= "";
$reply= "";
$post_id ="";


if(isset($_GET["module"])){	$module = $_GET["module"];	}
if(isset($_GET["act"])){$act = $_GET["act"];	}
if(isset($_GET["gal_id"])){	$gal_id = $_GET["gal_id"];}
if(isset($_GET["page_id"])){	$page_id = $_GET["page_id"];}
if(isset($_GET["view"])){	$view = $_GET["view"];}
if(isset($_GET["reply"])){	$reply = $_GET["reply"];}
if(isset($_GET["post_id"])){	$post_id = $_GET["post_id"];}

if(isset($_GET["custom-module"])){	$custom_module = $_GET["custom-module"];	}

$current_user = $xoouserultra->userpanel->get_user_info();

$user_id = $current_user->ID;
$user_email = $current_user->user_email;
$howmany = 5;

//check account uprading
$force_updgrade=  $xoouserultra->userpanel->uultra_force_upgrade_check($user_id);

if($force_updgrade=='yes')
{
	$module = 'account';
	
}

//check if paid membership for customizing.
$xoouserultra->customizer->uultra_is_paid_user($user_id);


?>
<div class="usersultra-dahsboard-cont">

	<div class="usersultra-dahsboard-left"> 
   
    
      <div class="myavatar rounded">
        
          <div class="pic" id="uu-backend-avatar-section">
        
            <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, "", 'avatar', 'rounded', 'dynamic')?>
            
           
            </div>
            
            <div class="btnupload-common-avatar" id="uupro-see-profile-account">
             <a class="uultra-btn-upload-avatar" href="<?php echo $xoouserultra->userpanel->get_user_profile_permalink($user_id);?>"  ><span><i class="fa fa-user fa-2x"></i></span><?php _e('See Public Profile','users-ultra')?></a>
             </div>
            
             <div class="btnupload-common-avatar" id="uupro-upload-avatar-account">
             <a class="uultra-btn-upload-avatar" href="?module=uultra-user-avatar"  data-id="<?php echo $user_id?>"><span><i class="fa fa-camera fa-2x"></i></span><?php echo $custom_label_upload_avatar?></a>
             </div>
             
              <div class="btnupload-common-avatar" id="uupro-upload-background-account">
             <a class="uultra-btn-upload-avatar" href="?module=uultra-profile-background"  data-id="<?php echo $user_id?>"><span><i class="fa fa-image fa-2x"></i></span><?php _e('Upload Background','users-ultra')?></a>
             </div>
             
                      
                      
            
      </div>
      
      <?php echo $xoouserultra->customizer->uultra_internal_user_menu_options();?>
         
          
    </div>
    
    
	<div class="usersultra-dahsboard-center"> 
    
        <?php
        //cutom message		
		$message_custom = $xoouserultra->get_option('messaging_private_all_users');
		
		if($message_custom!="")
		{
			echo "<p><div class='uupublic-ultra-info'><p>".$message_custom."</p></div></p>";
		
		}
		
		?>
        
         <?php 
			
			//custom modules
			
			   if($custom_module!=""  ) 
			   {
				   //we handle custom link				   
				   echo $xoouserultra->customizer->get_custom_link_content_by_slug($custom_module);
				  
			   }
			?> 
    
    
            <?php 
			
			//dashboard
			
			   if(($module=="dashboard" || $module=="") && $custom_module==""  ) 
			   {
				   
				   //check avatar updates			   
				   $xoouserultra->userpanel->validate_if_user_has_gravatar($current_user->ID);
				   
				   
				   
				   
			?> 
            
               <h1> <?php echo _e("Hello ", 'users-ultra')?> <?php echo $current_user->display_name?>. <?php  _e('Welcome to your dashboard','users-ultra');?></h1>     
               
               <p style="text-align:right"><?php  _e('Account Status','users-ultra');?>: 	<?php echo $xoouserultra->userpanel->get_status($current_user->ID);?></p>   
               
               
               
               <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'dashboard');?>
               
               
                <?php if(!in_array("messages",$modules)){?>  
             
                  <div class="expandable-panel xoousersultra-shadow-borers" id="cp-1">
                                
                      <div class="expandable-panel-heading">
                              <h2><?php  _e('My Latest Messages','users-ultra');?><span class="icon-close-open"></span></h2>
                     </div>
                     
                      <div class="expandable-panel-content" >
                     
                       	<?php  $xoouserultra->mymessage->show_usersultra_latest_messages($howmany);?>
                     
                     
                     </div>                    
                     
                     
               </div>

   		        <?php }?>
                  
          <?php if(!in_array("photos",$modules)   && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'photos')){?>  
              <div class="expandable-panel xoousersultra-shadow-borers" id="cp-2">
                                
                      <div class="expandable-panel-heading">
                              <h2> <?php  _e('My Latest Photos','users-ultra');?><span class="icon-close-open"></span></h2>
                     </div>
                     
                      <div class="expandable-panel-content">
                     
                      <?php  echo $xoouserultra->photogallery->show_latest_photos_private(10);?>
                     
                     
                     </div>                    
                     
                     
               </div>
               
            <?php }?>    
              
              
        <?php }?>
        
         <?php
	   
	   //my friends
	   if($module=="friends" && !in_array("friends",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'friends')) 
	   {
		   
		   
	   
	   ?>
       
	<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Friends','users-ultra');?> </h2>
                       </div>
                       
                       
                       <p class="paneldesc"><?php echo _e('Here you can manage your friends. Your friends will be able to see private/restricted content','users-ultra')?></p>
                       
                       
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'friends');?>
                     
                      <div class="commons-panel-content" id="uultra-my-friends-request">
                                            
                          <?php  _e('loading ...','users-ultra');?>          
                       
                        
                       </div>
                       
                        <div class="commons-panel-content" id="uultra-my-friends-list">                        
                     
                                            
                                    <?php  _e('loading ...','users-ultra');?>
                       
                        
                       </div>
                       
                       
                       <script type="text/javascript">
						jQuery(document).ready(function($){
							
							
								$.post(ajaxurl, {
									   
									   action: 'show_friend_request'
												
										}, function (response){									
																			
											$("#uultra-my-friends-request").html(response);
											//alert	(response);
											show_all_friends();										
																	
									});							
									
									
									function show_all_friends()
									{
										$.post(ajaxurl, {
											   
											   action: 'show_all_my_friends'
														
												}, function (response){									
																					
													$("#uultra-my-friends-list").html(response);										
																			
											});
										
									
									}
									
									
									
								
						});				
						
						
						
				
                    
                 </script>
                        
                        
                  
                             
                     
                     
               </div>
               
              
       <?php }?>
       
        <?php
	   
	   //my posts
	   if($module=="posts" && !in_array("posts",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'posts')) 
	   {   
		   
	   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My '.$xoouserultra->publisher->mPostLabelPlural.'','users-ultra');?> </h2> 
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can manage your '.$xoouserultra->publisher->mPostLabelPlural.'. ','users-ultra')?></p>
                               
                             
                             
                              <div class="commons-panel-content" >  
                              
                                <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'posts');?>
                              
                              <?php echo $xoouserultra->publisher->show_my_posts();?>
                              
                               </div>
                                              
                      
                           
                       </div>
                            
					   <script type="text/javascript">						
						
						 var post_del_confirmation_message = '<?php echo _e( 'Are you totally sure that you want to delete this '.$xoouserultra->publisher->mPostLabelSingular.'', 'xoousers' ) ?>';
                    
              		   </script>
               
                <?php }?>
               
                 <?php  if($act=="add") {?>                  
                       
                    <?php echo do_shortcode('[usersultra_front_publisher]');?>                   
                        
                 <?php }?>
                 
                  <?php  if($act=="edit") {?>                  
                       
                    <?php echo  $xoouserultra->publisher->edit_post($post_id);?>                   
                        
                 <?php }?>
                   
               
              
       <?php }?>
       
       <?php    
	    if($module=="myorders" && !in_array("myorders",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'myorders')) 
	   {
		   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My Orders','users-ultra');?> </h2>
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can check your orders. ','users-ultra')?></p>
                               
                               <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'myorders');?>
                             
                              <div class="commons-panel-content" >  
                              
                              <?php echo $xoouserultra->order->show_my_latest_orders(10);?>
                              
                               </div>
                                              
                      
                           
                       </div>
               
                <?php }?>
               
                                 
               
              
       <?php }?>
       
       <?php    
	    if($module=="wootracker" && !in_array("wootracker",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'wootracker')) 
	   {
		   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My Purchases','users-ultra');?> </h2>
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can check your purchases. ','users-ultra')?></p>
                               
                               <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'wootracker');?>
                             
                              <div class="commons-panel-content" >  
                              
                              <?php echo $xoouserultra->woocommerce->show_my_latest_orders(10);?>
                              
                               </div>
                                              
                      
                           
                       </div>
               
                <?php }?>
               
                                 
               
              
       <?php }?>
       
       
       <?php
	   
	   //my photos
	   if($module=="photos" && !in_array("photos",$modules)  && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'photos')) 
	   {
		   
		   		   
		   
	   
	   ?>
       
<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Galleries','users-ultra');?> </h2>
                     </div>
                     
                      <div class="commons-panel-content">
                      
                      <p><?php  _e('Here you can manage your galleries and photos.','users-ultra');?></p>
                      
                      <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'photos');?>
                      
                                               
                         
                          <div class="btnupload-gall-photos">
             <a class="uultra-btn-upload-common" id="add_gallery" ><span><i class="fa fa-camera fa-2x"></i></span><?php echo _e("Create Gallery", 'users-ultra')?></a>
             </div>
                        <div class="gallery-list">
                        
                         <div class="add-new-gallery" id="new_gallery_div">
                         
                            <p><?php  _e('Name','users-ultra');?>
                            <br />
                            
                            <input type="hidden" name="xoouserultra_current_gal"  id="xoouserultra_current_gal" />
                           <input type="text" class="xoouserultra-input" name="new_gallery_name" id="new_gallery_name" value=""> 
                           <?php  _e('Description','users-ultra');?>
                           <br />
                            <textarea class="xoouserultra-input'" name="new_gallery_desc" id="new_gallery_desc" ></textarea>
                            </p>
                            
                            <div class="usersultra-btn-options-bar">
                           
                            
                             <button id="close_add_gallery"> <?php  _e('Cancel','users-ultra');?></button>
                            <button id="new_gallery_add"> <?php  _e('Submit','users-ultra');?></button>
                            
                            </div>
                        
                        
                         </div>
                        
                        
                                                  
                        <ul id="usersultra-gallerylist">
                                <?php  _e('loading ...','users-ultra');?> 
                              
                             
                         </ul>
                          
                          </div>
                     
                     
                     </div>                    
                     
                     
               </div>
               
               <script type="text/javascript">
				jQuery(document).ready(function($){
					
					
					var page_id_val =   $('#page_id').val(); 
               
					   $.post(ajaxurl, {
									action: 'reload_galleries', 'page_id': page_id_val
									
									}, function (response){									
																
									$("#usersultra-gallerylist").html(response);
									
														
							});
							
					
				});
				
				 
				   var gallery_delete_confirmation_message = '<?php echo _e( 'Delete this gallery?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
       
        
               <input type="hidden" value="<?php echo $page_id?>" name="page_id" id="page_id" />
       
        <?php
	   
	   //my photos
	   if($module=="photos-files") 
	   {
		   
		   //get selected gallery
		   $current_gal = $xoouserultra->photogallery->get_gallery($gal_id)
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Photos','users-ultra');?> / <?php echo $current_gal->gallery_name?></h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                      <p><?php  _e('Here you can manage your photos.','users-ultra');?></p>
                      
                                              
                         
                         
             
                        <div class="photo-list">                         
                        
                         <div class="res_sortable_container" id="resp_t_image_list">
						 
						 <?php $xoouserultra->photogallery->post_media_display($gal_id);?>                       
                         
                         </div>
                                                                                  
                                <ul id="usersultra-photolist" class="usersultra-photolist-private">
                                       <?php  _e('loading photos ...','users-ultra');?>
                                      
                                     
                                 </ul>
                          
                          </div>
                     
                     
                     </div>                    
                     
                     
               </div>
               
              
               
                <script type="text/javascript">
				jQuery(document).ready(function($){
					
					
               
					   $.post(ajaxurl, {
									action: 'reload_photos', 'gal_id': '<?php echo $gal_id?>'
									
									}, function (response){									
																
									$("#usersultra-photolist").html(response);
									
														
							});
							
							
							
					
				});
                    
                 </script>
               
                     
      <?php }?>
      
       <?php
	   
	   
	   if($module=="profile" && !in_array("profile",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'profile')) 
	   {
		   
		  
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('My Profile','users-ultra');?> </h2>
                       </div>
                       
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'profile');?>
                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->userpanel->edit_profile_form();?>
                                         
                      </div>
                     
                     
          </div>
               
                
               
                     
      <?php }?>
      
      
      <?php
	   
	   
	   if($module=="profile-customizer" && !in_array("profile-customizer",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'profile-customizer')) 
	   {
		   
		 $crop_image = $_POST['crop_image'];
		
		
		   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
       
            
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('Profile Customizer','users-ultra');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'profile-customizer');?>
                                            
                    
                      
                    <div class="uultra-block-pro-builder">
                    <p><?php _e('Please drag&drop the modules you would like to display in your profile then click the save button.','users-ultra'); ?></p>
                
                <p class="savebtn"> <span id="loading-animation-pro-builder">  <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php _e('Please wait ...','users-ultra'); ?> </span> &nbsp; <a class="uultra-btn-commm" href="#" id="uultra-btn-save-customizer-change" title="<?php echo __('Save changes','users-ultra')?>" ><span><i class="fa fa-save  fa-lg"></i></span> <?php echo __('SAVE','users-ultra')?></a></p>
					                    
                                        
				</div>
                      
                      
                          <div class="pr_col">
                                 
                             <?php echo $xoouserultra->customizer->get_customizer_columns_user();?>                         
                          </div>
                          
                          <div class="pr_col_element">
                          
                          <h3 class="colname_widget"><?php echo _e('Drag non-used widgets','users-ultra')?></h3>                             
                          
                          		<ul class="droptrue" id="uultra-prof-cust-elements">
                                
                                  <?php echo  $xoouserultra->customizer->get_profile_widgets();?>
                                
                                </ul>
                          
                          </div>
                      
                      
                          
                      
                      
                                         
                      </div>
                  
                     
          </div>
               
                
               
                     
      <?php 
	   
	  
	  }?>
      
      <?php
	   
	   
	   if($module=="uultra-user-avatar" ) 
	   {
		   
		 $crop_image = $_POST['crop_image'];
		
		
		   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
       
      <?php
	  
	  if( $crop_image=='crop_image') //displays image cropper
		{
			
			 $image_to_crop = $_POST['image_to_crop'];
			
			
			
		?>
        
                  
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('Crop your '.$avatar_is_called.'','users-ultra');?> </h2>
                       </div>
                       
                       
                        <div class="commons-panel-content">
                                                    
                              <div class="pr_tipb_be">
                              
                                     <?php echo $xoouserultra->customizer->uultra_display_avatar_image_to_crop($image_to_crop, $avatar_is_called);?>                          
                              
                                </div>
                        
                         </div>
                       
                       
       <?php 
	    
	   }else{ //display the common layout
      
	  
	  ?>      
       
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('Upload '.$avatar_is_called.'','users-ultra');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                      <div class="uultra-avatar-drag-drop-sector"  id="uultra-drag-avatar-section">
                        
                             <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, "", 'avatar', 'rounded', 'dynamic')?>

                                                    
                             <div class="uu-upload-avatar-sect">
                              
                                     <?php echo $xoouserultra->userpanel->avatar_uploader($avatar_is_called)?>  
                              
                             </div>
                             
                        </div>      
                        
                         </div>
           <?php }?>           
                     
          </div>
               
                
               
                     
      <?php 
	   
	  
	  }?>
      
      
       <?php if($module=="uultra-profile-background" ) 
	   { 
	   
	   $crop_image = $_POST['crop_image'];
	   ?>
       
       
        <div class="commons-panel xoousersultra-shadow-borers" >
        
        
         <?php
	  
			  if( $crop_image=='crop_image') //displays image cropper
				{
					
					 $image_to_crop = $_POST['image_to_crop'];
					
			
			
				?>
                
                
                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('Crop your background image','users-ultra');?> </h2>
                       </div>  
                       
                      
                       
                       
                        <div class="commons-panel-content">
                        
                         <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'profile-customizer');?>
                                                    
                              <div class="pr_tipb_be">
                              
                                     <?php echo $xoouserultra->customizer->uultra_display_bg_image_to_crop($image_to_crop);?>                          
                              
                                </div>
                        
                         </div>
										
							  
							 
			 <?php  }else{?>
             
             
             		 <div class="commons-panel-heading">
                          <h2> <?php  _e('Profile Customizer','users-ultra');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'profile-customizer');?>
                                            
                      <div class="pr_tipb_be">
                      
                     		 <?php echo $xoouserultra->customizer->get_bg_uploader();?>
                      
                      
                        </div>
                              
             
             
             <?php  }?>
        
        					 
		</div>
            
      <?php  }?>
      
      
       <?php if($module=="messages" && !in_array("messages",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'messages')) 
	   {	  
	   
			   ?>
               
               
			  
			   <div class="commons-panel xoousersultra-shadow-borers" >
										
							  <div class="commons-panel-heading">
								  <h2> <?php  _e('Received Messages','users-ultra');?> </h2>
							   </div>
							 
							  <div class="commons-panel-content">
                              
                               <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'messages');?>
							  
								   <div class="uultra-myprivate-messages">       
							  
									<?php  
									
									//send message to admin
									echo $xoouserultra->mymessage->uultra_user_contact_admin();
									
									
									if(!$view && !$reply) 
									{
										echo $xoouserultra->mymessage->show_usersultra_my_messages();									
									}
									
									if(isset($view) && $view>0) 
									{
										//display view box
										$xoouserultra->mymessage->show_view_my_message_form($view);								
									
									}
									
									?>
							  
								   </div>
												 
							  </div>
							 
							 
				  </div>
                  
                  
                              
                
               
                     
      <?php }?>
      
      <?php if($module=="messages_sent"  && !in_array("messages",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'messages')) 
	   {	  
	   
			   ?>
               
			   <div class="commons-panel xoousersultra-shadow-borers" >
										
							  <div class="commons-panel-heading">
								  <h2> <?php  _e('Sent Messages','users-ultra');?> </h2>
							   </div>
							 
							  <div class="commons-panel-content">
							  
								   <div class="uultra-myprivate-messages">       
							  
									<?php  									
									
										echo $xoouserultra->mymessage->show_usersultra_my_messages_sent();									
																		
									 ?>
							  
								   </div>
												 
							  </div>
							 
							 
				  </div>
                  
                     
      <?php }?>
      
     <?php
     
       //my account
	   if($module=="account" && !in_array("account",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'account')) 
	   {
		   
	   ?>
       
		<div class="commons-panel xoousersultra-shadow-borers" >
                        
                        
                        <div class="commons-panel-heading">
                              <h2> <?php  _e('My Account','users-ultra');?>  </h2>
                     </div>      
                     
                      
                        <?php
						
						if($force_updgrade=='yes')
						{
							$custom_upgrade_text = $xoouserultra->get_option("force_account_upgrading_text");
							
							if($custom_upgrade_text=='')
							{
								$custom_upgrade_text = __('Please upgrade your account', 'users-ultra');
							
							}
							
							echo '<div class="uupublic-ultra-warning"> '.$custom_upgrade_text.' </div>';
							
						}
						    
							//account upgrade 									
							 echo $xoouserultra->paypal->get_upgrade_packages();								
																		
						?>
                        
                        
                        
                        
                        <?php echo $xoouserultra->userpanel->get_change_role_my_account(); ?>
                        
                        
                      
                      
                                           
                                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'account');?>
                       <h2> <?php  _e('Remove Account','users-ultra');?>  </h2>
                      
                      <div class="uupublic-ultra-warning"> <?php  _e('WARNING! This action cannot be reverted.','users-ultra');?> </div>
                      
                      <p><?php  _e('Here you can remove your account.','users-ultra');?></p>
                       <form method="post" name="uultra-close-account" id="uultra-close-account">
                 			<input type="hidden" name="uultra-conf-close-account-post" value="ok" />
                             <p><input type="button" name="xoouserultra-register" id="xoouserultra-close-acc-btn" class="xoouserultra-button" value="<?php  _e('YES, CLOSE MY ACCOUNT','users-ultra');?>" /></p>
               		  </form>
                      
                                           
                     </div>
                     
                     
                                          
               </div>
               
               <script type="text/javascript">
		
				 
				   var delete_account_confirmation_mesage = '<?php echo _e( 'Are you totally sure that you want to close your account. This action cannot be reverted?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
       
        <?php
     
       //my settings
	   if($module=="settings" && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'settings')) 
	   {
	   
	   ?>
       
		<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('Settings','users-ultra');?>  </h2>
                               
                     </div>
                     
                    
                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'settings');?>
                       <h2> <?php  _e('Update Password','users-ultra');?>  </h2> 
                                           
                     
                       <form method="post" name="uultra-close-account" >
                       <p><?php  _e('Type your New Password','users-ultra');?></p>
                 			 <p><input type="password" name="p1" id="p1" /></p>
                            
                             <p><?php  _e('Re-type your New Password','users-ultra');?></p>
                 			 <p><input type="password"  name="p2" id="p2" /></p>
                            
                         <p><input type="button" name="xoouserultra-backenedb-eset-password" id="xoouserultra-backenedb-eset-password" class="xoouserultra-button" value="<?php  _e('CLICK HERE TO RESET PASSWORD','users-ultra');?>" /></p>
                         
                         <p id="uultra-p-reset-msg"></p>
               		  </form>
                      
                       <h2> <?php  _e('Update Email','users-ultra');?>  </h2>                                           
                     
                       <form method="post" name="uultra-change-email" >
                       <p><?php  _e('Type your New Email','users-ultra');?></p>
                 			 <p><input type="text" name="email" id="email" value="<?php echo $user_email?>" /></p>
                                                        
                         <p><input type="button" name="xoouserultra-backenedb-update-email" id="xoouserultra-backenedb-update-email" class="xoouserultra-button" value="<?php  _e('CLICK HERE TO UPDATE YOUR EMAIL','users-ultra');?>" /></p>                         
                         <p id="uultra-p-changeemail-msg"></p>
               		  </form>
                      
                                           
                     </div>
                   
                                          
               </div>
               
               <script type="text/javascript">
		
				 
				   var delete_account_confirmation_mesage = '<?php echo _e( 'Are you totally sure that you want to close your account. This action cannot be reverted?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
      
      
      <?php
	   
	   
	   if($module=="videos" && !in_array("videos",$modules) && $xoouserultra->customizer->user_module_menu_allowed($current_user->ID, 'videos')) 
	   {
		   
		  
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('My Videos','users-ultra');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">            
                      
                               
                      
                      
                      <p><?php  _e('Here you can manage your videos.','users-ultra');?></p>
                      
                      
                       <?php echo $xoouserultra->customizer->get_custom_module_text($current_user->ID, 'videos');?>
                      
                         <a  id="add_new_video"  href="#"> <?php  _e('Add Video','users-ultra');?></a>
                         
                          <div class="add-new-video" id="new_video_div">
                         
                            <p><?php  _e('Name','users-ultra');?>
                            <br />
                            
                           
                           <input type="text" class="xoouserultra-input" name="new_video_name" id="new_video_name" value=""> 
                           
                           </p>
                           <p>
                           
                           <?php  _e('Video ID / URL','users-ultra');?>
                           <br />
                            <input type="text" class="xoouserultra-input" name="new_video_unique_vid" id="new_video_unique_vid" value=""> 
                            </p>
                            
                           
                             <p>
                           
                           <?php  _e('Video Type','users-ultra');?>
                           <br />
                            <select  name="new_video_type" id="new_video_type" class="xoouserultra-input" >
                              <option value="youtube">Youtube</option>
                              <option value="vimeo">Vimeo</option>
                                <option value="embed"><?php  _e('Embed','users-ultra');?></option>
                              </select>
                            </p>
                            
                            <div class="usersultra-btn-options-bar">
                            <a class="buttonize" href="#" id="close_add_video"><?php  _e('Cancel','users-ultra');?></a>
                            <a class="buttonize green"  href="#" id="new_video_add_confirm"><?php  _e('Submit','users-ultra');?></a>
                            
                            </div>  
                            
                         </div>       
                        <div class="video-list">                         
                        
                                    
                                                                       
                                <ul id="usersultra-videolist" class="usersultra-video-private">
                                       <?php  _e('loading videos ...','users-ultra');?>
                                      
                                     
                                 </ul>
                          
                          </div>
                     
                     
                           
                                         
                      </div>         
                     
     
               
               <script type="text/javascript">
				jQuery(document).ready(function($){			
				
               
					   $.post(ajaxurl, {
									action: 'reload_videos'
									
									}, function (response){																
																
									$("#usersultra-videolist").html(response);
									
														
							});
							
					
				});				
				 
				  var video_delete_confirmation_message = '<?php echo _e( 'Delete this video?', 'xoousers' ) ?>';			
				  var video_empy_field_name= '<?php echo _e( 'Please input a name', 'xoousers' ) ?>';
				  var video_empy_field_id= '<?php echo _e( 'Please input video ID', 'xoousers' ) ?>';                                   
                    
                 </script>  
               
                     
      <?php }?>


    </div>
    
   


</div>