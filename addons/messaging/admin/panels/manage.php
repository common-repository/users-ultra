<?php
global $uultra_messaging_admin;

?>
<div class="user-ultra-sect ">

<h2><?php _e('My Messages ','users-ultra'); ?></h2>
        
      
<form action="" method="post" id="uultra-userslist">
          <input type="hidden" name="add-category" value="add-cate" />
        
        
        <div class="uultra-myprivate-messages">
            <div class="user-ultra-success uultra-notification"><?php _e('Success ','users-ultra'); ?></div>
            
            <?php
            
            $uultra_messaging_admin->show_usersultra_my_messages();
            ?>
        </div>
             
</form>
</div>