<?php global $xoouser, $uultra_badges; ?>

<p class="upadmin-highlight"><?php _e('You can find the fulfillment medallions that you have created and edit the rules of fulfillment from this page.','users-ultra'); ?></p>

<table class="wp-list-table widefat fixed">

	<thead>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Medallion','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Medallion Title','users-ultra'); ?></th>		
			<th scope='col' class='manage-column'><?php _e('Fulfillment Type','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Required','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Actions','users-ultra'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Medallion','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Medallion Title','users-ultra'); ?></th>		
			<th scope='col' class='manage-column'><?php _e('Fulfillment Type','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Required','users-ultra'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Actions','users-ultra'); ?></th>
		</tr>
	</tfoot>

	<?php
	$achievement = get_option('_uultra_badges');
	if (isset($achievement)){
	?>

		<?php if (is_array($achievement) ) {
			foreach($achievement as $k => $badge) { 
			
			if ( is_array($badge) && count($badge) > 1) {
			
			?>
			
			<?php foreach($badge as $n => $arr) { ?>
			<tr>
				<td valign="top"><img src="<?php echo $arr['badge_url']; ?>" alt="" /></td>
				<td valign="top"><?php echo $arr['badge_title']; ?></td>
				<td valign="top"><?php echo $k; ?></td>
				<td valign="top"><?php echo $n; ?></td>
				<td valign="top"><a href="<?php echo admin_url(); ?>admin.php?page=userultra-badges&tab=manage&btype=<?php echo $k; ?>&bid=<?php echo $n; ?>"><?php _e('Edit','users-ultra'); ?></a> | <a href="#" class="uultra-badge-remove" data-btype="<?php echo $k; ?>" data-bid="<?php echo $n; ?>"><?php _e('Remove','users-ultra'); ?></a></td>
			</tr>
			<?php } ?>
			
			<?php } else { ?>
			
			<tr>
				<?php foreach($badge as $n => $arr) { ?>
				<td valign="top"><img src="<?php echo $arr['badge_url']; ?>" alt="" /></td>
				<td valign="top"><?php echo $arr['badge_title']; ?></td>
				<td valign="top"><?php echo $k; ?></td>
				<td valign="top"><?php echo $n; ?></td>
				<td valign="top"><a href="<?php echo admin_url(); ?>admin.php?page=userultra-badges&tab=manage&btype=<?php echo $k; ?>&bid=<?php echo $n; ?>"><?php _e('Edit','users-ultra'); ?></a> | <a href="#" class="uultra-badge-remove" data-btype="<?php echo $k; ?>" data-bid="<?php echo $n; ?>"><?php _e('Remove','users-ultra'); ?></a></td>
				<?php } ?>
			</tr>
			
			<?php } ?>
			
		<?php }

		} ?>
		
	<?php
		}
	?>

</table>