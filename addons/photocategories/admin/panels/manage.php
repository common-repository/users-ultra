<?php
global $uultra_photo_category;

$categories = $uultra_photo_category->get_photo_categories();
?>
<div class="user-ultra-sect ">
        
      
<form action="" method="post" id="uultra-userslist">
          <input type="hidden" name="add-category" value="add-cate" />
        
        <div class="user-ultra-success uultra-notification"><?php _e('Success ','users-ultra'); ?></div>
        
    <h3> <?php _e('Add New Category ','users-ultra'); ?></h3>
         
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="6%"><?php _e('Name ','users-ultra'); ?>:</td>
             <td width="94%"><input type="text" id="photo_cat_name"  name="photo_cat_name"  /></td>
           </tr>
          </table>
          
           <p>
           <input name="submit" type="submit"  class="button-primary" value="<?php _e('Confirm','users-ultra'); ?>"/>
          
    </p>
          
   
        </form>
        
                 <?php
			
			
				
				if (!empty($categories)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="4%" style="color:# 333"><?php _e('#', 'users-ultra'); ?></th>
                    <th width="12%"><?php _e('Name', 'users-ultra'); ?></th>
                    <th width="24%">&nbsp;</th>
                     <th width="19%">&nbsp;</th>
                    <th width="13%">&nbsp;</th>
                    <th width="8%">&nbsp;</th>
                    <th width="20%"><?php _e('Actions', 'users-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			
				foreach ( $categories as $c )
				{
					
			?>
              

                <tr  id="uu-edit-cate-row-<?php echo $c->photo_cat_id; ?>">
                    <td><?php echo $c->photo_cat_id; ?></td>
                    <td  id="uu-edit-cate-row-name-<?php echo $c->photo_cat_id; ?>"><?php echo $c->photo_cat_name; ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                     <td>&nbsp;</td>
                   <td> <a href="#" class="button uultra-photocat-del"  id="" data-gal="<?php echo $c->photo_cat_id; ?>"><i class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Delete','users-ultra'); ?>
                   </a>  <a href="#" class="button-primary button-secondary uultra-photocat-edit"  id="" data-gal="<?php echo $c->photo_cat_id; ?>"><i class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Edit','users-ultra'); ?>
</a> </td>
                </tr>
                
                
                <tr>
                
                 <td colspan="7" ><div id='uu-edit-cate-box-<?php echo $c->photo_cat_id; ?>'></div> </td>
                
                </tr>
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no categories yet.','users-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
             

</div>