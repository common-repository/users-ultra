<?php
class UsersUltraMessagingAdmin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userultra';
		$this->subslug = 'userultra-messaging';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( uultra_mess_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
		
	}
	
	function admin_init() 
	{
	
		$this->tabs = array(
			'manage' => __('Messages','users-ultra'),
			'send' => __('Send Message','users-ultra')
			
		);
		$this->default_tab = 'manage';		
		
	}
	
	/* Get all roles */
    public function uultra_get_roles(){
        global $wp_roles;
        $user_roles = array();

        if ( ! isset( $wp_roles ) ) 
            $wp_roles = new WP_Roles(); 

       
        foreach( $wp_roles->role_names as $role => $name ) {
           
                $user_roles[$role] = $name;
           
        }

        return $user_roles;
    }
	
	
	
	public function get_all_roles ()
	{
		global $wpdb, $xoouserultra;
		
		$roles =  $this->uultra_get_roles();
				
		$html = '';
		
		foreach($roles  as $role)
		{
			$html .= ' <label>
           <input type="checkbox" name="uultra_roles[]" value="'.$role.'" id="CheckboxGroup1_0" />
           '.$role.'</label>
         <br />';
		
		}
		
		return $html;	
	}
	/**
	 * Inbox page
	 */
	function show_usersultra_my_messages()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $user_id. '"  AND `deleted` != "2"   ORDER BY `date` DESC' );
		
		
		?>
	<div class="tablenav">
                
               <span style="float:left"><a href="?module=messages">Inbox</a> | <a href="?module=messages_sent">Sent</a></span>
					                    
                   
		<?php
		if ( !empty( $status ) )
		{
			echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			echo '<p>', __( 'You have no items in inbox.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $msgs );
			$num_unread = 0;
			foreach ( $msgs as $msg )
			{
				if ( !( $msg->readed ) )
				{
					$num_unread++;
				}
			}
			
			
			echo '<span style="float:right">', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread)', $n, 'xoousers' ), $n, $num_unread ), '</span>'     ;    
				
				?>
				</div>
		
						
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						<th class="manage-column "><input type="checkbox" /></th>
                        <th class="manage-column check-column"><?php _e( 'Pic', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Date', 'xoousers' ); ?></th>
                        <th class="manage-column" ><?php _e( 'Action', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							$user_id = $msg->sender;
							
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
							//main conversation id		
							
							$message_id =		$msg->id;			
							if($msg->parent=='-1')
							{
								$conversa_id = $msg->id;
							
							
							}else{
								
								$conversa_id = $msg->parent;
								
								
							}
							
							$message_status = "";
			
							if($msg->readed==1)
							{
								$message_status ='fa-eye 2x';	
								$message_status_text = __('Mark as Unread', 'users-ultra');	
								$new_status = 0;		
							
							}else{
								
								$message_status ='fa-eye-slash 2x';
								$message_status_text = __('Mark as Read', 'users-ultra');		
								$new_status = 1;
							
							
							}
							
							$read_class="";
							if($msg->readed==0)
							{
								
								$read_class="class='uultra-unread-message'";
								
							}
						
							
							?>
						<tr <?php echo $read_class?>>
							<td ><input type="checkbox" name="message_id[]" value="<?php echo $msg->id; ?>" />							<span></span></td>
                            
                            <td><span class="uultra-u-avatar"><?php echo $xoouserultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null) ?></span></td>
							<td><?php echo $msg->sender; ?></td>
							<td>
								<?php

								?>
                                
                                <a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>"><b><?php  echo stripcslashes( $msg->subject ) ?></b></a>
								<div class="row-actions">
								<span>
									<a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>"><?php _e( 'View', 'xoousers' ); ?></a>
								</span>
																		
								<span class="reply">
									| <a class="reply"
									href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>"><?php _e( 'Reply', 'xoousers' ); ?></a>
								</span>
								</div>
							</td>
							<td><?php echo $msg->date; ?></td>
                            
                            <td> <a class="uultra-btn-deletemessage  uu-private-message-change-status" href="#" id="" title="<?php echo $message_status_text?>" message-id="<?php echo $message_id?>" message-status="<?php echo $new_status?>" ><span><i class="fa <?php echo $message_status?>"></i></span></a><a class="uultra-btn-deletemessage  uu-private-message-delete" href="#" id="" message-id="<?php echo $message_id?>" ><span><i class="fa fa-times 2x"></i></span></a></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		

	
	}
	
	/* Grab Members list */
	function uultra_get_users()
	{
		
		$users = get_users();
		
		return $users;
	}
	
	
	
	
	
	function admin_head(){

	}

	function add_styles(){
	
		wp_register_script( 'uultra_messaging_js', uultra_mess_url . 'admin/scripts/admin.js', array( 
			'jquery'
		) );
		wp_enqueue_script( 'uultra_messaging_js' );
	
		wp_register_style('uultra_messaging_css', uultra_mess_url . 'admin/css/admin.css');
		wp_enqueue_style('uultra_messaging_css');
		
	}
	
	function add_menu()
	{
		add_submenu_page( 'userultra', __('Messaging Ultra','users-ultra'), __('Messaging Ultra','users-ultra'), 'manage_options', 'userultra-messaging', array(&$this, 'admin_page') );
		
		//do_action('userultra_admin_menu_hook');
		
		
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once uultra_mess_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	
	function uultra_send_to_users()
	{
		$users = $_POST['listo_to_users'];
		
		foreach($users as $user)
		{
			//notify user
			$this->send_private_message($user);		
			
		}
		
		echo '<div class="updated"><p><strong>'.__('Message Sent!.','users-ultra').'</strong></p></div>';
		
	}
	
	function uultra_send_to_users_with_role()
	{
		global $wpdb,  $xoouserultra;
		
		$roles = $_POST['uultra_roles'];		
		$blog_id = get_current_blog_id();
		
		//get users by roles
		
		$query['meta_query'] = array('relation' => 'AND' );
		
		/*This is applied only if we have to filter certain roles*/
		if (isset($roles))
		{
						
			if (count($roles) >= 2)
			{
				$query['meta_query']['relation'] = 'or';
			}
			
			foreach($roles as $subrole)
			{				
				$query['meta_query'][] = array(
				'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
				'value' => $subrole,
				'compare' => 'like'
				);
			}
		}
		
		
		$wp_user_query = new WP_User_Query($query);		
		$users = $wp_user_query->results;	
			
		
		foreach($users as $user)
		{
			//notify user
			$this->send_private_message($user->ID);		
			
			//echo "USER: " . $user->ID. "<br>";
			
		}
		
		echo '<div class="updated"><p><strong>'.__('Message Sent!.','users-ultra').'</strong></p></div>';
		
	}
	
	public function send_private_message($receiver_id)
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$receiver_id =  sanitize_text_field($receiver_id);
		$uu_subject =   sanitize_text_field($_POST["uu_subject"]);
		$uu_message =   sanitize_text_field($_POST["uu_message"]);
		
		//get receiver
		
		$receiver = get_user_by('id',$receiver_id);		
		$sender = get_user_by('id',$logged_user_id);		
	
		//store in the db
		
		if($receiver->ID >0 && $uu_subject!="" && $uu_message!="")
		{
			
			$new_message = array(
						'id'        => NULL,
						'subject'   => $uu_subject,						
						'content'   => $uu_message,
						'sender'   => $logged_user_id,
						'recipient'   => $receiver_id,
						
						'date'=> date('Y-m-d H:i:s'),
						'readed'   => 0,
						'deleted'   => 0
						
					);
					
					// insert into database
					$wpdb->insert( $wpdb->prefix . 'users_ultra_pm', $new_message, array( '%d', '%s', '%s', '%s',  '%s', '%s', '%s' , '%s' ));
					
			
			$xoouserultra->messaging->send_private_message_user_from_admin($receiver ,$sender->display_name,  $uu_subject,$uu_message);
			
			
		
		}
		
				
		
	}
	
	function admin_page() {
	
		
		if (isset($_POST['uultra-send-messages-to-users']) && $_POST['uultra-send-messages-to-users']=='uultra-send-messages-to-users')
			
		{
			if($_POST['uu_subject']=="" || $_POST['uu_message']=="")
			{
			
				echo '<div class="error"><p><strong>'.__('ERROR: All fields are required.','users-ultra').'</strong></p></div>';
			
			
			}else{
			
				if(isset ( $_POST['listo_to_users'] ) && !isset ( $_POST['uultra_roles'] ) ) 
				{
					//only selected users
					$this->uultra_send_to_users();
				}
				
				if(isset ( $_POST['uultra_roles'] ) && !isset ( $_POST['listo_to_users'] ) ) 
				{
					//only with certain roles
					$this->uultra_send_to_users_with_role();
				
					
				}
			
			}
			
		}

		
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
			
               <h2>USERS ULTRA PRO - ADMINISTRATOR MESSAGING SYSTEM</h2>
           
           <div id="icon-users" class="icon32"></div>
						
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$uultra_messaging_admin = new UsersUltraMessagingAdmin();