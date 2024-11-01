<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$fields = get_option('usersultra_profile_fields');

ksort($fields);



global $xoouserultra, $uultra_form;



$forms = $uultra_form->get_all();





$last_ele = end($fields);

$new_position = $last_ele['position']+1;



$meta_custom_value = "";

$qtip_classes = 'qtip-light ';

?>

<h3>

	<?php _e('Profile Fields Customizer','users-ultra'); ?>

</h3>

<p>

	<?php _e('Organize profile fields, add custom fields to profiles, control privacy of each field, and more using the following customizer. You can drag and drop the fields to change the order in which they are displayed on profiles and the registration form.','users-ultra'); ?>

</p>





<p >

<div class='user-ultra-success uultra-notification' id="fields-mg-reset-conf"><?php _e('Profile fields have been restored','users-ultra'); ?></div>



</p>

<a href="#uultra-add-field-btn" class="button button-secondary"  id="uultra-add-field-btn"><i

	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Click here to add new field','users-ultra'); ?>

</a>





<a href="#uultra-add-field-btn" class="button button-secondary user-ultra-btn-red"  id="uultra-restore-fields-btn"><i

	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Click here to restore default fields','users-ultra'); ?>

</a> 



<div class="user-ultra-sect-second user-ultra-rounded" >



<label for="uultra__custom_form"><?php _e('Custom Form:','users-ultra'); ?> </label>



<select name="uultra__custom_registration_form" id="uultra__custom_registration_form">

				<option value="" selected="selected">

					<?php _e('Default Registration Form','users-ultra'); ?>

				</option>

                

                <?php foreach ( $forms as $key => $form )

				{?>

				<option value="<?php echo $key?>">

					<?php echo $form['name']; ?>

				</option>

                

                <?php }?>

		</select>

        

        <input type="text" id="uultra_custom_registration_form_name" name="uultra_custom_registration_form_name" />

        <a href="#uultra-duplicate-form-btn" class="button button-secondary"  id="uultra-duplicate-form-btn"><i

	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Duplicate Current Form','users-ultra'); ?>

</a>





</div>





<div class="user-ultra-sect-second user-ultra-rounded" id="uultra-add-new-custom-field-frm" >



<table class="form-table uultra-add-form">



	



	<tr valign="top">

		<th scope="row"><label for="uultra_type"><?php _e('Type','users-ultra'); ?> </label>

		</th>

		<td><select name="uultra_type" id="uultra_type">

				<option value="usermeta">

					<?php _e('Profile Field','users-ultra'); ?>

				</option>

				<option value="separator">

					<?php _e('Separator','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('You can create a separator or a usermeta (profile field)','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_field"><?php _e('Editor / Input Type','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_field" id="uultra_field">

				<?php  foreach($xoouserultra->allowed_inputs as $input=>$label) { ?>

				<option value="<?php echo $input; ?>">

					<?php echo $label; ?>

				</option>

				<?php } ?>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_meta"><?php _e('Existing Meta Key / Field','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_meta" id="uultra_meta">

				<option value="">

					<?php _e('Choose a Meta Key','users-ultra'); ?>

				</option>

				<optgroup label="--------------">

					<option value="1">

						<?php _e('New Custom Meta Key','users-ultra'); ?>

					</option>

				</optgroup>

				<optgroup label="-------------">

					<?php

					$current_user = wp_get_current_user();

					if( $all_meta_for_user = get_user_meta( $current_user->ID ) ) {



					    ksort($all_meta_for_user);



					    foreach($all_meta_for_user as $user_meta => $array) {

					        ?>

					<option value="<?php echo $user_meta; ?>">

						<?php echo $user_meta; ?>

					</option>

					<?php

					    }

					}

					?>

				</optgroup>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Choose from a predefined/available list of meta fields (usermeta) or skip this to define a new custom meta key for this field below.','users-ultra'); ?>"></i>

		</td>

	</tr>



	



	<tr valign="top" >

		<th scope="row"><label for="uultra_meta_custom"><?php _e('New Custom Meta Key','users-ultra'); ?>

		</label></th>

		<td><input name="uultra_meta_custom" type="text" id="uultra_meta_custom"

			value="<?php echo $meta_custom_value; ?>" class="regular-text" /> <i

			class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','users-ultra'); ?>"></i>

		</td>

	</tr>

    

    <tr valign="top">

		<th scope="row"><label for="uultra_ccap"><?php _e('Custom Capabilities','users-ultra'); ?> </label>

		</th>

		<td><input name="uultra_ccap" type="text" id="uultra_ccap"

			value="<?php if (isset($_POST['uultra_ccap']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_ccap']; ?>"

			class="regular-text" /><a class="<?php echo $qtip_classes ?> uultra-tooltip" title="<?php _e('You could type anything in this space. Any page could have access to those having that tag. Francetour,Spaintour. Please input comma separated tags','users-ultra'); ?>" <?php echo $qtip_style?>> <i class="fa fa-info-circle reg_tooltip"></i></a>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_name"><?php _e('Label','users-ultra'); ?> </label>

		</th>

		<td><input name="uultra_name" type="text" id="uultra_name"

			value="<?php if (isset($_POST['uultra_name']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_name']; ?>"

			class="regular-text" /> <i

			class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_tooltip"><?php _e('Tooltip Text','users-ultra'); ?>

		</label></th>

		<td><input name="uultra_tooltip" type="text" id="uultra_tooltip"

			value="<?php if (isset($_POST['uultra_tooltip']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_tooltip']; ?>"

			class="regular-text" /> <i

			class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('A tooltip text can be useful for social buttons on profile header.','users-ultra'); ?>"></i>

		</td>

	</tr>

    

    

     <tr valign="top">

                <th scope="row"><label for="uultra_help_text"><?php _e('Help Text','users-ultra'); ?>

                </label></th>

                <td>

                    <textarea class="uultra-help-text" id="uultra_help_text" name="uultra_help_text" title="<?php _e('A help text can be useful for provide information about the field.','users-ultra'); ?>" ><?php if (isset($_POST['uultra_help_text']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_help_text']; ?></textarea>

                    <i class="uultra-icon-question-sign uultra-tooltip2"

                                title="<?php _e('Show this help text under the profile field.','users-ultra'); ?>"></i>

                </td>

            </tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_social"><?php _e('This field is social','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_social" id="uultra_social">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','users-ultra'); ?>"></i>

		</td>

	</tr>

    

    <tr valign="top">

		<th scope="row"><label for="uultra_is_a_link"><?php _e('Is a Link?','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_is_a_link" id="uultra_is_a_link">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('A link field is automatically converted to a link.','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_can_edit"><?php _e('User can edit','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_can_edit" id="uultra_can_edit">

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Users can edit this profile field or not.','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_allow_html"><?php _e('Allow HTML Content','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_allow_html" id="uultra_allow_html">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('If yes, users will be able to write HTML code in this field.','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_can_hide"><?php _e('User can hide','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_can_hide" id="uultra_can_hide">

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicly visible or not. Private fields are not affected by this option.','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_private"><?php _e('This field is private','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_private" id="uultra_private">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Make this field Private. Only admins can see private fields.','users-ultra'); ?>"></i>

		</td>

	</tr>





	<tr valign="top">

		<th scope="row"><label for="uultra_private"><?php _e('This field is required','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_required" id="uultra_required">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','users-ultra'); ?>"></i>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"><label for="uultra_show_in_register"><?php _e('Show on Registration form','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_show_in_register" id="uultra_show_in_register">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Show this field on the registration form? If you choose no, this field will be shown on edit profile only and not on the registration form. Most users prefer fewer fields when registering, so use this option with care.','users-ultra'); ?>"></i>

		</td>

        

        

	</tr>

    

     <tr valign="top">

		<th scope="row"><label for="uultra_show_in_widget"><?php _e('Show on Widget','users-ultra'); ?>

		</label></th>

		<td><select name="uultra_show_in_widget" id="uultra_show_in_widget">

				<option value="0">

					<?php _e('No','users-ultra'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-ultra'); ?>

				</option>

		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

			title="<?php _e('Show this field on the user profile widgets?.','users-ultra'); ?>"></i>

		</td>

        

        

	</tr>

    

    	<tr valign="top">

        		<th scope="row"><label for="uultra_show_to_user_role"><?php _e('Display by User Role','users-ultra'); ?>

        		</label></th>

        		<td><select name="uultra_show_to_user_role" id="uultra_show_to_user_role">

        				<option value="0" selected="selected">

        					<?php _e('No','users-ultra'); ?>

        				</option>

        				<option value="1">

        					<?php _e('Yes','users-ultra'); ?>

        				</option>

        		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

        			title="<?php _e('If no, this field will be displayed on profiles of all User Roles. Select yes to display this field only on profiles of specific User Roles.','users-ultra'); ?>"></i>

        		</td>

        	</tr>

            

    <tr valign="top" style="display:none" id="uultra_show_to_user_role_div" >

        		<th scope="row"><label for="uultra_show_to_user_role_list"><?php _e('Select User Roles','users-ultra'); ?>

        		</label></th>

        		<td>

        		<?php 

        			  $roles = 	$xoouserultra->role->uultra_get_available_user_roles();

        			  foreach($roles as $role_key => $role_display)

					  {

        		?>

        			  <input type='checkbox' name='uultra_show_to_user_role_list[]' id='uultra_show_to_user_role_list' value='<?php echo $role_key; ?>' class="uultra_show_roles_ids" />

        			  <label class='uultra-role-name'><?php echo $role_display; ?></label>

        		<?php

        			  }

        		?>

        		 <i class="uultra-icon-question-sign uultra-tooltip2"

        			title="<?php _e('This field will only be displayed on users of the selected User Roles.','users-ultra'); ?>"></i>

        		</td>

        	</tr>

            

     <tr valign="top" >

        		<th scope="row"><label for="uultra_edit_by_user_role"><?php _e('Editable by Users of Role','users-ultra'); ?>

        		</label></th>

        		<td><select name="uultra_edit_by_user_role" id="uultra_edit_by_user_role">

        				<option value="0" selected="selected">

        					<?php _e('No','users-ultra'); ?>

        				</option>

        				<option value="1">

        					<?php _e('Yes','users-ultra'); ?>

        				</option>

        		</select> <i class="uultra-icon-question-sign uultra-tooltip2"

        			title="<?php _e('If yes, available user roles will be displayed for selection.','users-ultra'); ?>"></i>

        		</td>

        	</tr>

            

    <tr valign="top" style="display:none" id="uultra_edit_by_user_role_id" >

        		<th scope="row"><label for="uultra_edit_by_user_role_list"><?php _e('Select Roles that can Edit.','users-ultra'); ?>

        		</label></th>

        		<td>

        		<?php  $roles = $xoouserultra->role->uultra_get_available_user_roles('edit');

        			  foreach($roles as $role_key => $role_display){

        		?>

        			  <input type='checkbox' name='uultra_edit_by_user_role_list[]' id='uultra_edit_by_user_role_list' value='<?php echo $role_key; ?>' class="uultra_edit_roles_ids"  />

        			  <label class='uultra-role-name'><?php echo $role_display; ?></label>

        		<?php

        			  }

        		?>

        		 <i class="uultra-icon-question-sign uultra-tooltip2"

        			title="<?php _e('Selected user roles will have the permission to edit this field.','users-ultra'); ?>"></i>

        		</td>

        	</tr>

    

   



	<tr valign="top" class="uultra-icons-holder">

		<th scope="row"><label><?php _e('Icon for this field','users-ultra'); ?> </label>

		</th>

		<td><label class="uultra-icons"><input type="radio" name="uultra_icon"

				value="0" /> <?php _e('None','users-ultra'); ?> </label> 

				<?php foreach($this->fontawesome as $icon) { ?>

			<label class="uultra-icons"><input type="radio" name="uultra_icon"

				value="<?php echo $icon; ?>" />

                <i class="fa fa-<?php echo $icon; ?> uultra-tooltip3" title="<?php echo $icon; ?>"></i> </label>            <?php } ?>

		</td>

	</tr>



	<tr valign="top">

		<th scope="row"></th>

		<td>

          <div class="user-ultra-success uultra-notification" id="uultra-sucess-add-field"><?php _e('Success ','users-ultra'); ?></div>

        <input type="submit" name="uultra-add" 	value="<?php _e('Submit New Field','users-ultra'); ?>"

			class="button button-primary" id="uultra-btn-add-field-submit" /> 

            <input type="button"class="button button-secondary " id="uultra-close-add-field-btn"	value="<?php _e('Cancel','users-ultra'); ?>" />

		</td>

	</tr>



</table>





</div>





<!-- show customizer -->

<ul class="user-ultra-sect user-ultra-rounded" id="uu-fields-sortable" >

		

  </ul>

  

           <script type="text/javascript">  

		

		      var custom_fields_del_confirmation ="<?php _e('Are you totally sure that you want to delete this field?','users-ultra'); ?>";

			  

			  var custom_fields_reset_confirmation ="<?php _e('Are you totally sure that you want to restore the default fields?','users-ultra'); ?>";

			   

			  var custom_fields_duplicate_form_confirmation ="<?php _e('Please input a name','users-ultra'); ?>";

		 

		 uultra_reload_custom_fields_set();

		 </script>

         

         <div id="uultra-spinner" class="uultra-spinner" style="display:">

            <span> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','users-ultra')?>

	</div>

         

        