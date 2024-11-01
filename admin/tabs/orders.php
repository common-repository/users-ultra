<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $xoouserultra;
$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
$orders = $xoouserultra->order->get_all();

$howmany = "";
$year = "";
$month = "";
$day = "";

if(isset($_GET["howmany"]))
{
	$howmany = $_GET["howmany"];		
}

if(isset($_GET["month"]))
{
	$month = $_GET["month"];		
}

if(isset($_GET["day"]))
{
	$day = $_GET["day"];		
}

if(isset($_GET["year"]))
{
	$year = $_GET["year"];		
}
		
?>

<?php if($_GET["action_order"]=='edit' && $_GET["order_id"] !='' ){
	
	
	//get order
	
	$order = $xoouserultra->order->get_order($_GET["order_id"])
	?>

 <div class="user-ultra-sect ">
     
      <?php 
    
      echo $xoouserultra->xoouseradmin->check_pro_version_features_message();
    ?>
        
        <h3><?php _e('Edit Order:','users-ultra'); ?></h3>  
        
        
          <form action="" method="get">
             <input type="hidden" name="page" value="userultra" />
              <input type="hidden" name="tab" value="orders" />  
              <input type="hidden" name="order_id" value="<?php echo $_GET["order_id"]?>" />
              
              <p><?php _e('Transaction ID','users-ultra'); ?>: </p> 
              <p> <input type="text" name="transaction_id" id="transaction_id" value="<?php echo $order->order_txt_id ?>" /></p> 
              
              <p><?php _e('Status','users-ultra'); ?>: </p>
            <p><select name="p_order_status">
                <option value="pending" <?php if($order->order_status=='pending'){echo 'selected="selected"';}?> ><?php _e('pending','users-ultra'); ?></option>
                <option value="confirmed" <?php if($order->order_status=='confirmed'){echo 'selected="selected"';}?>><?php _e('confirmed','users-ultra'); ?></option>
            </select> </p>
                
              <p><?php _e('Payment Method','users-ultra'); ?>: </p> 
              <p>
              <input type="button" value="Close" class="button " name="submit">
              
              <input type="button" value="Submit " data-order="<?php echo $_GET["order_id"]?>" class="button button-primary " name="uu-edit-order-conf" id="uu-edit-order-conf"> </p>          
          
          </form>
          
        
 </div>

 
 <?php }else{?>       
        <div class="user-ultra-sect ">
        
        <h3><?php _e('Orders','users-ultra'); ?></h3>     
       
       
        <form action="" method="get">
         <input type="hidden" name="page" value="userultra" />
          <input type="hidden" name="tab" value="orders" />
        
        <div class="user-ultra-success uultra-notification"><?php _e('Success ','users-ultra'); ?></div>
        
        <div class="user-ultra-sect-second user-ultra-rounded" >
        
         <h3> <?php _e('Search Transactions ','users-ultra'); ?></h3>
         
        
         
        
         
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="17%"><?php _e('Keywords: ','users-ultra'); ?></td>
             <td width="5%"><?php _e('Month: ','users-ultra'); ?></td>
             <td width="5%"><?php _e('Day: ','users-ultra'); ?></td>
             <td width="52%"><?php _e('Year: ','users-ultra'); ?></td>
             <td width="21%">&nbsp;</td>
           </tr>
           <tr>
             <td><input type="text" name="keyword" id="keyword" placeholder="<?php _e('write some text here ...','users-ultra'); ?>" /></td>
             <td><select name="month" id="month">
               <option value="" selected="selected"><?php _e('All','users-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="day" id="day">
               <option value="" selected="selected"><?php _e('All','users-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="year" id="year">
               <option value="" selected="selected"><?php _e('All','users-ultra'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo $i?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td>&nbsp;</td>
           </tr>
          </table>
         
         <p>
         
         <button><?php _e('Filter','users-ultra'); ?></button>
        </p>
        
       
        </div>
        
        
          <p> <?php _e('Total: ','users-ultra'); ?> <?php echo $xoouserultra->order->total_result;?> | <?php _e('Displaying per page: ','users-ultra'); ?>: <select name="howmany" id="howmany">
               <option value="20" <?php if(20==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>20</option>
                <option value="40" <?php if(40==$howmany ) echo 'selected="selected"';?>>40</option>
                 <option value="50" <?php if(50==$howmany ) echo 'selected="selected"';?>>50</option>
                  <option value="80" <?php if(80==$howmany ) echo 'selected="selected"';?>>80</option>
                   <option value="100" <?php if(100==$howmany ) echo 'selected="selected"';?>>100</option>
               
          </select></p>
        
         </form>
         
         <div class="uupagination">              
         
            <?php echo $xoouserultra->order->pages;?>
         
         </div>
        
         <?php
			
			
				
				if (!empty($orders)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="3%"><?php _e('#', 'users-ultra'); ?></th>
                    <th width="11%"><?php _e('Date', 'users-ultra'); ?></th>
                     <th width="11%"><?php _e('Expiration', 'users-ultra'); ?></th>
                    
                    <th width="23%"><?php _e('User', 'users-ultra'); ?></th>
                     <th width="18%"><?php _e('User Email', 'users-ultra'); ?></th>
                    <th width="16%"><?php _e('Transaction ID', 'users-ultra'); ?></th>
                    <th width="11%"><?php _e('Plan', 'users-ultra'); ?></th>
                     <th width="9%"><?php _e('Method', 'users-ultra'); ?></th>
                     <th width="9%"><?php _e('Status', 'users-ultra'); ?></th>
                    <th width="9%"><?php _e('Amount', 'users-ultra'); ?></th>
                    <th width="9%"><?php _e('Action', 'users-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			foreach($orders as $order) {
					
			?>
              

                <tr>
                    <td><?php echo $order->order_id; ?></td>
                    <td><?php echo  date("m/d/Y", strtotime($order->order_date)); ?></td>
                     <td><?php echo  date("m/d/Y", strtotime($order->order_expiration)); ?></td>
                    <td><?php echo $order->display_name; ?> (<?php echo $order->user_login; ?>)</td>
                    <td><?php echo $order->user_email; ?> </td>
                    <td><?php echo $order->order_txt_id; ?></td>
                     <td><?php echo $order->package_name; ?></td>
                      <td><?php echo $order->order_method_name; ?></td>
                      <td><?php echo $order->order_status; ?></td>
                   <td> <?php echo $currency_symbol.$order->order_amount; ?></td>
                   <td> <a class="uultra-btn-deletemessage " href="?page=userultra&tab=orders&action_order=edit&order_id=<?php echo  $order->order_key?>" id="" title="<?php _e('Edit Order','users-ultra'); ?>" order-id="<?php echo $order->order_id?>" ><span><i class="fa fa-edit fa-lg"></i></span></a></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no transactions yet.','users-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
        </div>
        
 <?php }?> 