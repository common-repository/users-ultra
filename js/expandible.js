if(typeof $ == 'undefined'){
	var $ = jQuery;
}
(function($) {
    jQuery(document).ready(function () { 
	
	   "use strict";
        /*-------------------- EXPANDABLE PANELS ----------------------*/
        var panelspeed = 500; //panel animate speed in milliseconds
        var totalpanels = 3; //total number of collapsible panels   
        var defaultopenpanel = 1; //leave 0 for no panel open   
        var accordian = false; //set panels to behave like an accordian, with one panel only ever open at once      
 
        var panelheight = new Array();
        var currentpanel = defaultopenpanel;
        var iconheight = parseInt(jQuery('.icon-close-open').css('height'));
        var highlightopen = true;
         
        //Initialise collapsible panels
        function panelinit() {
                for (var i=1; i<=totalpanels; i++) {
                    panelheight[i] = parseInt($('#cp-'+i).find('.expandable-panel-content').css('height'));
                    jQuery('#cp-'+i).find('.expandable-panel-content').css('margin-top', -panelheight[i]);
                    if (defaultopenpanel == i) {
                        jQuery('#cp-'+i).find('.icon-close-open').css('background-position', '0px -'+iconheight+'px');
                        jQuery('#cp-'+i).find('.expandable-panel-content').css('margin-top', 0);
                    }
                }
        }
 
       jQuery('.expandable-panel-heading').click(function() {           
            var obj = jQuery(this).next();
            var objid = parseInt(jQuery(this).parent().attr('ID').substr(3,2));  
            currentpanel = objid;
            if (accordian == true) {
                resetpanels();
            }
             
            if (parseInt(obj.css('margin-top')) <= (panelheight[objid]*-1)) {
                obj.clearQueue();
                obj.stop();
                obj.prev().find('.icon-close-open').css('background-position', '0px -'+iconheight+'px');
                obj.animate({'margin-top':0}, panelspeed);
                if (highlightopen == true) {
                    jQuery('#cp-'+currentpanel + ' .expandable-panel-heading').addClass('header-active');
                }
            } else {
                obj.clearQueue();
                obj.stop();
                obj.prev().find('.icon-close-open').css('background-position', '0px 0px');
                obj.animate({'margin-top':(panelheight[objid]*-1)}, panelspeed); 
                if (highlightopen == true) {
                    jQuery('#cp-'+currentpanel + ' .expandable-panel-heading').removeClass('header-active');   
                }
            }
        });
         
        function resetpanels() {
            for (var i=1; i<=totalpanels; i++) {
                if (currentpanel != i) {
                    $('#cp-'+i).find('.icon-close-open').css('background-position', '0px 0px');
                    $('#cp-'+i).find('.expandable-panel-content').animate({'margin-top':-panelheight[i]}, panelspeed);
                    if (highlightopen == true) {
                        $('#cp-'+i + ' .expandable-panel-heading').removeClass('header-active');
                    }
                }
            }
        }
		
		//add new gallery
		
		jQuery('#add_gallery').click(function() {
			
			
			jQuery( "#new_gallery_div" ).slideDown( "slow", function() {			
				// Animation complete.
			
			});			
					 
			 return false; 
    		e.preventDefault();

				
        });
		
		//$('.display_gallery_pictures').live('click', function(event) {
		jQuery('body').on('click', '.display_gallery_pictures', function() {	
			
			//alert("a");
			var gal_id =  jQuery(this).attr("data-id");
			var page_id_val =   $('#page_id').val(); 		
					
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "reload_photos", "gal_id": gal_id  , "page_id": page_id_val },
				
				success: function(data){					
					$('#xoouserultra_current_gal').text(gal_id);					
					$("#usersultra-gallerylist").html(data);				
					
					
					}
			});
		});
		
		
		

		
		jQuery('#close_add_gallery').click(function() {
			
			
			jQuery( "#new_gallery_div" ).slideUp( "slow", function() {			
				// Animation complete.
			
			});			
					 
			 return false; 
    		e.preventDefault();

				
        });
		
		jQuery('#close_add_video').click(function() {
			
			
			jQuery( "#new_video_div" ).slideUp( "slow", function() {			
				// Animation complete.
				
							
			});			
					 
			 return false; 
    		e.preventDefault();

				
        });
		
		
		
		jQuery('#add_new_video').click(function() {			
			
			jQuery( "#new_video_div" ).slideDown( "slow", function() {			
				// Animation complete.
			
			});			
					 
			 return false; 
    		e.preventDefault();
				
        });  
		jQuery('#add_new_files').click(function() {			
			
			jQuery( "#resp_t_image_list" ).slideDown( "slow", function() {			
				// Animation complete.
			
			});			
					 
			 return false; 
    		e.preventDefault();
				
        });  
		
		
		
				
		jQuery('#new_gallery_add').click(function() {
			
	
			var gall_name = jQuery('#new_gallery_name').val();
			var gall_desc = jQuery('#new_gallery_desc').val();
		
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "add_new_gallery", "gall_name": gall_name , "gall_desc": gall_desc },
				
				success: function(data){
					
					jQuery('#new_gallery_name').text("");
					jQuery('#new_gallery_desc').text("");
					
					reload_gallery_list();					
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
			
		jQuery('body').on('click', '#new_video_add_confirm', function(e) {
			
			e.preventDefault();
			
			
			
	
			var video_name = jQuery('#new_video_name').val();
			var video_id = jQuery('#new_video_unique_vid').val();
			var video_type = jQuery('#new_video_type').val();
			
			if(video_name==""){alert(video_empy_field_name);return}
			if(video_id==""){alert(video_empy_field_id);return}
		
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "add_new_video", "video_name": video_name , "video_id": video_id , "video_type": video_type },
				
				success: function(data){
					
					jQuery('#new_video_name').text("");
					jQuery('#new_video_unique_vid').text("");
					
					reload_video_list();					
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		function reload_gallery_list ()
		{
			
			var page_id_val =   jQuery('#page_id').val(); 
			 jQuery.post(ajaxurl, {
							action: 'reload_galleries', 'page_id':  page_id_val 
									
							}, function (response){
									
																
							jQuery("#usersultra-gallerylist").html(response);
									
									
					
			});
			
			
			
		}
		
		function reload_photo_list (gal_id)
		{
			var page_id_val =   jQuery('#page_id').val(); 	
			
			 jQuery.post(ajaxurl, {
							action: 'reload_photos', 'gal_id':  gal_id,  'page_id':  page_id_val 
									
							}, function (response){									
																
							jQuery("#usersultra-photolist").html(response);
									
									
					
			});
			
		}
		
		
		
		function reload_video_list ()
		{
			
			 jQuery.post(ajaxurl, {
									action: 'reload_videos'
									
									}, function (response){																
																
									jQuery("#usersultra-videolist").html(response);
									
														
							});
			
			
			
		}
		
			
		//$(document).on("click", "a[href='#resp_del_photo']", function(e) {
			
		jQuery('body').on('click',"a[href='#resp_del_photo']", function(e) {
			
						
			e.preventDefault();
			
			var photo_id =  jQuery(this).attr("id");
			var gal_id =  jQuery(this).attr("data-id");	
			
								
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "delete_photo", "photo_id": photo_id },
				
				success: function(data){
					reload_photo_list(gal_id);
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//delete account		
		$('body').on('click',"#xoouserultra-change-user-role-backend", function(e) {
		
		   var u_role =jQuery("#reg_uultra_custom_user_role").val();
				
			jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "uultra_user_change_role", 
							 "role": u_role },
							
							success: function(data){			
										
							
								jQuery("#uultra-change-role-confmsg").html(data);
								jQuery("#uultra-change-role-confmsg").slideDown();							
								
								
								}
						});
			
			return false;
				
        });
		
		//delete account		
		$('body').on('click',"#xoouserultra-close-acc-btn", function(e) {
		
		   
			e.preventDefault();
			
			var doIt = false;
			
			doIt=confirm(delete_account_confirmation_mesage);
		  
			if(doIt)
			{
				
				$('#uultra-close-account').submit();
				
						
			}
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//delete video
		
		$('body').on('click',"a[href='#resp_del_video']", function(e) {
		
		   
			e.preventDefault();
			
			var doIt = false;
			
			doIt=confirm(video_delete_confirmation_message);
		  
			if(doIt)
			{
				
				var video_id =  jQuery(this).attr("data-id");	
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "delete_video", "video_id": video_id },
					
					success: function(data){
						//reload_photo_list(gal_id);
						
						reload_video_list();
						
						
						}
				});
			
			
			}
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		
		
		//delete post
		$('body').on('click',".uultra-del-user-postlink", function(e) {
		
		   
			e.preventDefault();
			
			var doIt = false;
			
			doIt=confirm(post_del_confirmation_message);
		  
			if(doIt)
			{
				
				var post_id =  jQuery(this).attr("data-id");	
				
						
				jQuery('#uultra-delete-post').val('uultra-del-post-conf');
				jQuery('#post_id').val(post_id);
				jQuery('#uultra-form-publisher').submit();				
						
			
			
			}
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		//close edit box
		//$(document).on("click", ".btn-photo-close", function(e) {
			
		$('body').on('click',".btn-photo-close", function(e) {
			
			e.preventDefault();				
			var photo_id =  jQuery(this).attr("data-id");						
			$( "#photo-edit-div-"+photo_id ).slideUp();								
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		$("body").on("click", ".btn-photo-conf", function(e) {
			
			e.preventDefault();		
			
			
				var photo_id =  jQuery(this).attr("data-id");	
				var photo_name= $("#uultra_photo_name_edit_"+photo_id).val()	;
				var photo_desc =  $("#uultra_photo_desc_edit_"+photo_id).val();
				
				var photo_tags =  $("#uultra_photo_tags_edit_"+photo_id).val();				
				var photo_category =  $("#uultra_photo_category_edit_"+photo_id).val();
				
				
				
				
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_photo_confirm", "photo_id": photo_id , "photo_name": photo_name , "photo_desc": photo_desc , "photo_tags": photo_tags , "photo_category": photo_category },
					
					success: function(data){					
						
												
						$( "#photo-edit-div-"+photo_id ).slideUp();
						//reload_gallery_list();
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//edit photo
		//$(document).on("click", "a[href='#resp_edit_photo']", function(e) {
			
		$('body').on('click',"a[href='#resp_edit_photo']", function(e) {
			
			e.preventDefault();
			
			
				
				var photo_id =  jQuery(this).attr("data-id");	
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_photo", "photo_id": photo_id },
					
					success: function(data){
						
						
						$("#photo-edit-div-"+photo_id).html(data);						
						$( "#photo-edit-div-"+photo_id ).slideDown();
						
						
						}
				});
			
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//edit video
		
		$('body').on("click", "a[href='#resp_edit_video']", function(e) {
			
		//$('body').on('click',"a[href='#resp_edit_photo']", function(e) {
			
			e.preventDefault();
			
			
				
				var video_id =  jQuery(this).attr("data-id");	
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_video", "video_id": video_id },
					
					success: function(data){
												
						$("#video-edit-div-"+video_id).html(data);						
						$( "#video-edit-div-"+video_id ).slideDown();
						
						
						}
				});
			
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		
		//close video edit box
		$("body").on("click", ".btn-video-close-conf", function(e) {
			
			e.preventDefault();				
			var p_id =  jQuery(this).attr("data-id");
									
			$( "#video-edit-div-"+p_id ).slideUp();			
			return false;
    		e.preventDefault();
			 
				
        });
		
		
		
		//edit gallery
		jQuery("body").on("click", "a[href='#resp_edit_gallery']", function(e) {
			
			e.preventDefault();
			
			
				
				var gal_id =  jQuery(this).attr("data-id");	
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_gallery", "gal_id": gal_id },
					
					success: function(data){
						
						
						jQuery("#gallery-edit-div-"+gal_id).html(data);						
						jQuery( "#gallery-edit-div-"+gal_id ).slideDown();
						
						
						}
				});
			
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//edit gallery confirm					
		jQuery("body").on("click", ".btn-gallery-conf", function(e) {
			
			e.preventDefault();		
			
			
				var gal_id =  jQuery(this).attr("data-id");	
				var gal_name= jQuery("#uultra_gall_name_edit_"+gal_id).val()	;
				var gal_desc =  jQuery("#uultra_gall_desc_edit_"+gal_id).val();
				var gal_visibility =  jQuery("#uultra_gall_visibility_edit_"+gal_id).val();
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_gallery_confirm", "gal_id": gal_id , "gal_name": gal_name , "gal_desc": gal_desc , "gal_visibility": gal_visibility },
					
					success: function(data){					
						
												
						jQuery( "#gallery-edit-div-"+gal_id ).slideUp();
						reload_gallery_list();
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//close gallery edit box
		$(document).on("click", ".btn-gallery-close-conf", function(e) {
			
			e.preventDefault();				
			var p_id =  jQuery(this).attr("data-id");
									
			$( "#gallery-edit-div-"+p_id ).slideUp();			
			return false;
    		e.preventDefault();
			 
				
        });
		
		
		//edit video confirm					
		jQuery(document).on("click", ".btn-video-edit-conf", function(e) {
			
			e.preventDefault();		
			
			
				var video_id =  jQuery(this).attr("data-id");	
				var video_name= jQuery("#uultra_video_name_edit_"+video_id).val()	;
				var video_unique_id =  jQuery("#uultra_video_id_edit_"+video_id).val();
				var video_type =  jQuery("#uultra_video_type_edit_"+video_id).val();
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_video_confirm", "video_id": video_id , "video_name": video_name , "video_unique_id": video_unique_id , "video_type": video_type },
					
					success: function(data){					
						
												
						jQuery( "#video-edit-div-"+video_id ).slideUp();
						reload_video_list();
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		
		$(document).on("click", "a[href='#resp_del_gallery']", function(e) {
			
			e.preventDefault();
			
			var doIt = false;
			
			doIt=confirm(gallery_delete_confirmation_message);
		  
			if(doIt)
			{
				
				var gal_id =  jQuery(this).attr("data-id");	
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "delete_gallery", "gal_id": gal_id },
					
					success: function(data){
						//reload_photo_list(gal_id);
						
						reload_gallery_list();
						
						
						}
				});
			
			
			}
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		$(document).on("click", "a[href='#resp_set_main']", function(e) {
		
			
			e.preventDefault();
			
			var photo_id =  jQuery(this).attr("id");
			var gal_id =  jQuery(this).attr("data-id");	
			
								
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "set_as_main_photo", "photo_id": photo_id , "gal_id": gal_id },
				
				success: function(data){
					reload_photo_list(gal_id);
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		jQuery(document).on("click", "#uultra-btn-save-customizer-change", function(e) {
			
			e.preventDefault();		
			
			 jQuery('#loading-animation-pro-builder').show(); 	
			
			
			var widegetList1 = jQuery('#uultra-prof-customizar-1');
			var widegetList2 = jQuery('#uultra-prof-customizar-2');
			var widegetList3 = jQuery('#uultra-prof-customizar-3');
			
			//var widegetList1_str =  widegetList1.$sortable('toArray').toString();
			//var widegetList2_str =  widegetList2.$sortable('toArray').toString();
			//var widegetList3_str =  widegetList3.$sortable('toArray').toString();
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				async: true,
                cache: false,
                dataType: 'json',
				data: {action: 'arrange_widgets', // Tell WordPress how to handle this ajax request
                    order_col_1: widegetList1.sortable('toArray').toString(),
					order_col_2: widegetList2.sortable('toArray').toString(),
					order_col_3: widegetList3.sortable('toArray').toString()
                 },
				
				success: function(response){					
										
					 jQuery('#loading-animation-pro-builder').hide(); // Hide the loading animation
                   
					
					
					}
			});
			
						 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		
		/* 	WIDGETS CUSTOMIZER -  Close Open Widget */
		jQuery('.uultra-btn-widget-elements, .uultra-expandable-panel-heading-widgets').on('click',function(e)
		{
			
			e.preventDefault();
			var widget_id =  jQuery(this).attr("widget-id");		
			var iconheight = 20;
			
			//alert(widget_id);
			if(jQuery("#uultra-widget-adm-cont-id-"+widget_id).is(":visible")) 
			{
				
				jQuery("#uultra-widgets-icon-close-open-id-"+widget_id).css('background-position', '0px 0px');
				
			}else{
				
				jQuery("#uultra-widgets-icon-close-open-id-"+widget_id).css('background-position', '0px -'+iconheight+'px');			
			 }
			
			
			jQuery("#uultra-widget-adm-cont-id-"+widget_id).slideToggle();	
						
			return false;
		});
		
		
		
		
		//save user settings			
		jQuery(document).on("click", "#uultra-update-widget-custom-user-information", function(e) {
			
			e.preventDefault();
			
			var widget_id =  jQuery(this).attr("widget-id");
			
			var widget_custom_text =  jQuery("#uultra_user_widget_cont_edition_"+widget_id).val();
			
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "uultra_update_user_widget_customization", "widget_custom_text": widget_custom_text, "widget_id": widget_id },
					
					success: function(data){
												
												
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//save user settings			
		jQuery(document).on("click", ".uultra-edit-widget-content-html-editor", function(e) {
			
			e.preventDefault();
			
			var widget_id =  jQuery(this).attr("widget-id");
				
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "uultra_edit_widget_content",  "widget_id": widget_id },
					
					success: function(data){					
						
						$("#uultra-plugin-settings-editor" ).dialog( "open" );	
						tinyMCE.activeEditor.setContent('');					
						tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, data );						
						$("#uultra-current-selected-widget-to-edit" ).val(widget_id );
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		
		
		 jQuery( "#uultra-plugin-settings-editor" ).dialog({
			autoOpen: false,
			height: 560,																				
			width: 650,
			modal: true,
			buttons: {
			"Update": function() {
				
								
				var widget_id = jQuery("#uultra-current-selected-widget-to-edit" ).val();
				var widget_text = tinymce.editors.uultra_widget_html_editor_content_.getContent();;
				uultra_save_user_widget_data(widget_id, widget_text);
				$( this ).dialog( "close" );
		
			//custom function here
			},
			Cancel: function() {
			$( this ).dialog( "close" );
			}
			},
			close: function() {
			
			
			}
			});
		
		
			
		jQuery( "ul.droptrue" ).sortable({

		  connectWith: "ul"
	
		});
		
		
		function uultra_save_user_widget_data (widget_id, widget_data)
		{
			
			 jQuery.post(ajaxurl, {
							action: 'uultra_edit_widget_content_confirm',  "widget_id": widget_id ,  "widget_data": widget_data}, function (response){									
																
							//$("#uu-backend-avatar-section").html(response);
							//$("#uultra-bg-img-bg-id").html(response);
									
					
			});
			
		}
		
		
		jQuery(document).on("click", "#btn-delete-user-avatar", function(e) {
			
			e.preventDefault();
			
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "delete_user_avatar" },
					
					success: function(data){
												
						refresh_my_avatar();
						//$( "#uu-upload-avatar-box" ).slideUp("slow");
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//remove profile bg
		jQuery(document).on("click", "#uultra-remove-profile-bg", function(e) {
			
			e.preventDefault();
			
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "uultra_delete_user_profile_bg" },
					
					success: function(data){
												
						refresh_profile_bg();
												
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		function refresh_profile_bg ()
		{
			
			 $.post(ajaxurl, {
							action: 'get_profile_bg_refresh'}, function (response){									
																
							jQuery("#uu-backend-avatar-section").html(response);
							jQuery("#uultra-bg-img-bg-id").html(response);
							
									
									
					
			});
			
		}
		
		function refresh_my_avatar ()
		{
			
			 jQuery.post(ajaxurl, {
							action: 'refresh_avatar'}, function (response){									
																
							jQuery("#uu-backend-avatar-section").html(response);
							//$( "#uu-upload-avatar-box" ).slideUp("slow");
									
									
					
			});
			
		}
		
	
		   
 
        jQuery(window).load(function() {
 			panelinit();
        }); //END LOAD
    }); //END READY
})(jQuery);

//-------USERS PHOTO SORTABLE

function sortable_list ()
{
	 var itemList = jQuery('#usersultra-photolist');

    itemList.sortable({
        update: function(event, ui) {
           // $('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'sort_photo_list', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                  //  alert(e);
                    // alert('There was an error saving the updates');
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
	
}

//-------USERS GALLERY SORTABLE
function sortable_gallery_list ()
{
	 var itemList = jQuery('#usersultra-gallerylist');

    itemList.sortable({
        update: function(event, ui) {
           // $('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'sort_gallery_list', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                   // alert(e);
                    // alert('There was an error saving the updates');
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
	
}

jQuery(document).ready(function($) 
{ 
   if ($('#usersultra-photolist').length > 0) {
	    sortable_list();
	}
	
	if ($('#usersultra-gallerylist').length > 0) {
	    sortable_gallery_list();
	}
  
   
});