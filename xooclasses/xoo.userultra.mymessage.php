<?php
class XooUserMyMessage {

	public $allowed_extensions;

	function __construct() 
	{
		//handle request
		if(isset($_GET["message_status"]) && $_GET["message_status"]>0)
		{
			//$this->change_message_status();
			
		}
		
		$this->ini_messagingsystem();
		
		add_action( 'wp_ajax_send_private_message',  array( $this, 'send_private_message' ));
		add_action( 'wp_ajax_send_private_message_to_admin',  array( $this, 'send_private_message_to_admin' ));
		add_action( 'wp_ajax_reply_private_message',  array( $this, 'reply_private_message' ));
		add_action( 'wp_ajax_message_change_status',  array( $this, 'message_change_status' ));
		add_action( 'wp_ajax_message_delete',  array( $this, 'message_delete' ));
		add_action( 'wp_ajax_show_my_messages_msg_box_to_user',  array( $this, 'show_my_messages_msg_box_to_user' ));
		add_action( 'wp_ajax_show_my_messages_msg_box_to_user_directory',  array( $this, 'show_my_messages_msg_box_to_user_directory' ));
		
		
		
		

	}
	
	public function ini_messagingsystem()
	{
		global $wpdb;

			// Create table
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'users_ultra_pm (
				`id` bigint(20) NOT NULL auto_increment,
				`parent` bigint(20) NOT NULL default -1,
				`subject` text NOT NULL,
				`content` text NOT NULL,
				`sender` int(11) NOT NULL,
				`recipient` int(11) NOT NULL,
				`date` datetime NOT NULL,
				`readed` tinyint(1) NOT NULL,
				`deleted` tinyint(1) NOT NULL,
				PRIMARY KEY (`id`)
			) COLLATE utf8_general_ci;';
	
		// Note: deleted = 1 if message is deleted by sender, = 2 if it is deleted by recipient
	
		$wpdb->query( $query );
		
		$this->update_table();
		
	}
	
	function update_table()
	{
		global $wpdb;
		
		//2014-03-08 			
		$sql ='SHOW columns from ' . $wpdb->prefix . 'users_ultra_pm where field="parent" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	//photo_desc
			$sql = 'Alter table  ' . $wpdb->prefix . 'users_ultra_pm add column parent bigint (20) default -1 ; ';
			$wpdb->query($sql);
		}
		
		//2014-05-02 			
		$sql ='SHOW columns from ' . $wpdb->prefix . 'users_ultra_pm where field="deleted_sender" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'users_ultra_pm add column deleted_sender tinyint (1) default 0 ; ';
			$wpdb->query($sql);
		}
		
		
		
		
	}
	
	
	
	public function send_private_message()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$receiver_id =  sanitize_text_field($_POST["receiver_id"]);
		$uu_subject =   sanitize_text_field($_POST["uu_subject"]);
		$uu_message =   implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["uu_message"] )) );
		
		//get receiver
		
		$receiver = get_user_by('id',$receiver_id);		
		$sender = get_user_by('id',$logged_user_id);
		
		//store in the db
		
		if($receiver->ID >0)
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
					
			
			$xoouserultra->messaging->send_private_message_user($receiver ,$sender->display_name,  $uu_subject,$_POST["uu_message"]);
			
			
		
		}
		
		echo "<div class='uupublic-ultra-success'>".__(" Message sent ", 'users-ultra')."</div>";
		die();
		
		
		
	}
	
	public function send_private_message_to_admin()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		
		$uu_subject =   sanitize_text_field($_POST["uu_subject"]);
		$uu_message =   implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["uu_message"] )) );
		
		
		$sender = get_user_by('id',$logged_user_id);		
		//get receivers users		
		$admin_l_array = array();
		$admin_list = $xoouserultra->get_option('uultra_allow_users_contact_admin_list');
		
		//echo "admin list " .print_r($admin_list);
		
		
		if ( !empty( $admin_list ) && is_array( $admin_list ) ) 
		{			
		
			foreach ($admin_list as $receiver_id)
			{
				
				$receiver = get_user_by('id',$receiver_id);						
				
				//store in the db
				
				if($receiver->ID >0)
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
							
					
					$xoouserultra->messaging->send_private_message_user($receiver ,$sender->display_name,  $uu_subject,$_POST["uu_message"]);
					
					
				
				}
				
				
			
			
			} //end for each
			
			
			echo "<div class='uupublic-ultra-success'>".__(" Message sent ", 'users-ultra')."</div>";
		
		
		}else{
			
			
			echo "<div class='uupublic-ultra-error'>".__(" Message wasn't sent ", 'users-ultra')."</div>";
			
			
		
		} //end if
		
		
		die();
		
		
		
	}
	
	public function reply_private_message()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
				
		
		$message_id =  sanitize_text_field($_POST["message_id"]);				
		//$uu_message =   sanitize_text_field($_POST["uu_message"]);
		$uu_message =   implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST["uu_message"] )) );
		
		$message = $this->get_one($message_id, $logged_user_id);
		
		$uu_subject =   __("Reply: ", 'users-ultra')." ".$message->subject;
		
		//check if reply equal to sender
		$receiver_id = $message->sender;
		
		if($receiver_id==$logged_user_id)
		{
			
			$receiver_id = $message->recipient;
		
		
		}
		
		//get receiver
		
		$receiver = get_user_by('id',$receiver_id);		
		$sender = get_user_by('id',$logged_user_id);
		
		//store in the db
		
		if($receiver->ID >0)
		{
			
			$new_message = array(
						'id'        => NULL,
						'subject'   => $uu_subject,						
						'content'   => $uu_message,
						'sender'   => $logged_user_id,
						'recipient'   => $receiver_id,	
						'parent'   => $message->id,						
						'date'=> date('Y-m-d H:i:s'),
						'readed'   => 0,
						'deleted'   => 0
						
					);
					
					// insert into database
					$wpdb->insert( $wpdb->prefix . 'users_ultra_pm', $new_message, array( '%d', '%s', '%s', '%s',  '%s', '%s', '%s', '%s' , '%s' ));
					
			
			$xoouserultra->messaging->send_private_message_user($receiver ,$sender->display_name,  $uu_subject,$_POST["uu_message"]);
			
			
		
		}
		
		echo "<div class='uupublic-ultra-success'>".__(" Reply sent ", 'users-ultra')."</div>";
		die();
		
		
		
	}
	
	
	public function get_send_form($receiver_user_id)
	{
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		
		$html = "";
		
		
		if($logged_user_id>0 && $logged_user_id != "")
		{
			//is logged in.
			if($logged_user_id==$receiver_user_id)
			{
				$html .= "<p>".__("You cannot send a private message to yourself", 'users-ultra')."</p>";
			
			
			}else{
				
				$html .= ' <p>'.__("Subject", 'users-ultra').'</p>          
				  <p><input name="uu_subject"  id="uu_subject" type="text" /></p>
				  
				  <p>'. __("Message", 'users-ultra').'</p>          
				  <p><textarea name="uu_message" id="uu_message" cols="" rows=""></textarea></p>          
				  <p><a class="uultra-btn-email" href="#" id="uu-close-private-message-box" data-id="'.$receiver_user_id.'"><span><i class="fa fa-times"></i></span>'. __("Close", 'users-ultra').'</a> <a class="uultra-btn-email" href="#" id="uu-send-private-message-confirm" data-id="'.$receiver_user_id.'"><span><i class="fa fa-check"></i></span>'. __("Send", 'users-ultra').'</a></p>
				  ';
				  
				  $html .='';
				  
				  $html .='<script type="text/javascript">
				  
				  var uu_subject_empty = "'.__("Please input a subject", 'users-ultra').'";
				   var uu_message_empty = "'.__("Please write a message", 'users-ultra').'";
								
				
                    
                 </script>';
				
			
			}
			
		  
		}else{
			
			$html .= "<p>".__("You have to be logged in to send private messages", 'xoousers'. "</p>");
			
		
		}
		  
		  
		  echo $html;
		
		
	
	
	}
	
	public function get_unread_messages_amount($user_id) 
	{
		global $wpdb, $xoouserultra;
		
		$total = 0;
		

		$messages = $wpdb->get_results( 'SELECT  count(*) as total  FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = '.$user_id.'  AND  `readed` ="0"  AND `deleted` <> "2" ' );
		
		foreach ( $messages as $message )
		{
			$total= $message->total;
							
		}
		
		return $total;
		
	
	}
	
	public function get_one($id, $receiver_id) 
	{
		global $wpdb, $xoouserultra;
		

		$messages = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `id` = ' . (int)$id . ' AND (`recipient` = '.$receiver_id.' OR `sender` = '.$receiver_id.')  ' );
		
		foreach ( $messages as $message )
		{
			return $message;
							
		}
		
	
	}
	
	public function message_delete()
	{
		
		global $wpdb,  $xoouserultra;
		
		$message_id = $_POST["message_id"];
		$logged_user_id = get_current_user_id();
		
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `deleted` = '2' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		echo "<div class='uupublic-ultra-success'>".__(" The message has been deleted. Please refresh your screen.", 'users-ultra')."</div>";
		die();
	
	}
	
	public function message_change_status()
	{
		
		global $wpdb,  $xoouserultra;
		
		$message_id = $_POST["message_id"];
		$message_status = $_POST["message_status"];
		
		$logged_user_id = get_current_user_id();
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '".$message_status."' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '".$message_status."' WHERE `parent` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		echo "<div class='uupublic-ultra-success'>".__(" Status has been changed", 'users-ultra')."</div>";
		die();
	
	
	}
	
	//this mark as read parent and all replies	
	function update_read_status($message_id)
	{
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();		
				
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '1' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";		
		$wpdb->query($sql);
		
		//update all replies		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '1' WHERE `parent` = '$message_id' AND  `recipient` = '".$logged_user_id."'";		
		$wpdb->query($sql);
		
		
	}
	
	
	
	
	
	/**
	 * Allows user to contct admin
	 */
	function uultra_user_contact_admin()
	{
		global   $xoouserultra;
		
		$html  = '';
		$send_admin =$xoouserultra->get_option('uultra_allow_users_contact_admin');
		
		//is admin
		
		if ( current_user_can( 'manage_options' ) ) 
		{
			/* A user with admin privileges won't see it*/
			
		} else {
			
			
				if($send_admin=='yes' )
				{
					$html .='<div class="uultra-send-adm-msg-div">';			
					$html .= '<a href="#" id="uultra-send-private-message-box-to-admin"  title="'.__('Contact Admin','users-ultra').'" ><i class="fa fa-lg fa-envelope uultra-icon-menu"></i><span class="uultra-top-nav-labels-resp">'.__(' Contact Admin','users-ultra').'</span></a> ';
					
					$html .=  $xoouserultra->userpanel->contact_admin_form_internal();
					
					$html .='</p>';
				
				
				}
		}
		
		
		
		return $html;
	
	}
	
	
	/**
	 * Inbox page
	 */
	function show_view_my_message_form($message_id)
	{
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		
		//chech if this is one of my messages		
		$message = $this->get_one($message_id, $logged_user_id);
		
		$message_sender_id = $message->sender;
		
		 $pic_boder_type = "";
		
				
		
		if($message != "")
		{
			$message_status = "";
			
			if($message->readed==1)
			{
				$message_status = __('Mark as Unread', 'users-ultra');	
				$new_status = 0;		
			
			}else{
				
				$message_status = __('Mark as Read', 'users-ultra');	
				$new_status = 1;
			
			
			}
			
			//mark as read
		
			$this->update_read_status($message_id);
		
			
			//date
			$orig_msg_date = date("F j, Y, g:i a", strtotime($message->date));
						
			$content = stripslashes($message->content);
			$content = nl2br($content);
			$html  = ' <div class="uu-private-messaging-backend rounded" >';
			
						
			//main message
			$html .= '<ul class="replylist">'	;
			
			
			$html .= '<li class="rounded">'	;
			$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $message_sender_id, 80, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
			
			$html .= '<div class="uultra-msg-content-box">
			
			<div class="uultra-msg-date">
			'.$orig_msg_date.'
			</div>
		     '.$content .''	;
			 
			$html .= '</div>'	;			
			$html .= '</li>'	;
			
			//replies
			
			$replies = $this->get_parent_messages($message->id);
			//print_r($replies);
			
			foreach ( $replies as $reply )
			{
				$receiver_id = $reply->recipient	;
				$sender_id = $reply->sender	;
				$reply_msg_date = date("F j, Y, g:i a", strtotime($reply->date));
				
				$content = stripslashes($reply->content);
				$content = nl2br($content);
				
				$html .= '<li class="rounded replybox">'	;
				$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $sender_id, 80, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
				
				$html .= '<div class="uultra-msg-content-box">
				
				<div class="uultra-msg-date">
				'.$reply_msg_date.'
				</div>
				 '.$content .''	;
				 
				$html .= '</div>'	;			
				$html .= '</li>'	;
			
			}		
					
			 
			$html .='<li class="mbsbox rounded"><div class="uultra-reply-box"><textarea name="uu_message" class="uultra-reply-box-st" id="uu_message" cols="" rows="7" placeholder="'. __("Type your reply here...", 'users-ultra').'" ></textarea></div></li>';
				
			$html .= '</ul>'		;	  
					  				  
			$html .='	           
					   
					  
					          
					  <p><a class="uultra-btn-email" href="?module=messages"  data-id="'.$receiver_id.'"><span><i class="fa fa-chevron-left"></i></span>'. __("Back", 'users-ultra').'</a>
					   <a class="uultra-btn-email" href="#" id="uu-reply-private-message-confirm" message-id="'.$message->id.'"><span><i class="fa fa-reply"></i></span>'. __("Send Reply", 'users-ultra').'</a>
					   
					 					  
			</p>
					  ';
					  
					  
			 
			 		 
			 $html .='<div id="uu-message-noti-id"></div>';
			 
			 $html  .= '</div>';
				  
			 $html .='<script type="text/javascript">
					  
					 	   var uu_message_empty = "'.__("Please write a message", 'users-ultra').'";
						
					 </script>';
		   
		}else{
			
			$html .= 'Error'; 
	   
		}
		
		 echo $html;
		
		
	}
	
	function get_parent_messages($message_id)
	{
		global $wpdb, $current_user, $xoouserultra;
		
		//current user login
		$user_id = get_current_user_id();
					
		// show all parent messages
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE  `parent` = "'.$message_id.'" ORDER BY `date` ASC' );
		
		return $msgs;
	
	}
	
		/**
	 * Lates messages at send message box 11/03/2014
	 */
	function show_my_messages_msg_box_to_user_directory()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();		
		$recipient_id = $_POST["recipient_id"];
		
		$html ='<ul>';
		
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $recipient_id. '" AND `sender` =  "' . $user_id. '" AND `deleted` != "2"   ORDER BY `date` DESC' );
		
		
		if ( empty( $msgs ) )
		{
			
		}else{
			
			foreach ( $msgs as $msg )
			{
				$receiver_id = $msg->recipient	;
				$sender_id = $msg->sender	;
				$reply_msg_date = $this->nicetime($msg->date);
				
				$content = stripslashes(nl2br($msg->content));
				
				$content = $xoouserultra->userpanel->parse_emoticons($content);
				
				$html .='<li>';
				$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $sender_id, 30, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
				
				$html .= '<div class="uultra-msg-content-box">
				
				<div class="uultra-msg-date">
				'.$reply_msg_date.'
				</div>
				 '.$content .''	;
				 
				$html .= '</div>'	;				
				$html .='</li>';
				
				$html .='<input type="hidden" name="uultra-msg-selected-receiver-id" value="'.$recipient_id.'" />';
				
								
			}
		
		}
		
		$html .='</ulr>';
		
		
		echo  $html;
		die();
		
		
	}
	
	
	
	/**
	 * Latest messages at send message box 08/09/2014
	 */
	function show_my_messages_msg_box_to_user()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();		
		$recipient_id = $_POST["recipient_id"];
		
		$html ='<ul>';
		
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE (`recipient` = "' . $recipient_id. '" AND `sender` =  "' . $user_id. '" AND `deleted` != "2")  OR (`recipient` = "' . $user_id. '" AND `sender` =  "' . $recipient_id. '" AND `deleted` != "2")   ORDER BY `date` DESC' );
		
		
		if ( empty( $msgs ) )
		{
			
		}else{
			
			foreach ( $msgs as $msg )
			{
				$receiver_id = $msg->recipient	;
				$sender_id = $msg->sender	;
				$reply_msg_date = $this->nicetime($msg->date);
				
				$content = stripslashes(nl2br($msg->content));
				$content = $xoouserultra->userpanel->parse_emoticons($content);
				
				$html .='<li>';
				$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $sender_id, 30, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
				
				$html .= '<div class="uultra-msg-content-box">
				
				<div class="uultra-msg-date">
				'.$reply_msg_date.'
				</div>
				 '.$content .''	;
				 
				$html .= '</div>'	;				
				$html .='</li>';
				
				
			}
		
		}
		
		$html .='</ulr>';
		
		
		echo  $html;
		die();
		
		
	}
	
	function nicetime($date)
	{
		if(empty($date)) {
			return "No date provided";
		}
	   
		$periods         = array( __("second", 'users-ultra'), __("minute", 'users-ultra'), __("hour", 'users-ultra'), __("day", 'users-ultra'),__("week", 'users-ultra'), __("month", 'users-ultra'), __("year", 'users-ultra'), __("decade", 'users-ultra'));
		$lengths         = array("60","60","24","7","4.35","12","10");
	   
		$now             = time();
		$unix_date         = strtotime($date);
	   
		   // check validity of date
		if(empty($unix_date)) {   
			return "Bad date";
		}
	
		// is it future date or past date
		if($now > $unix_date) {   
			$difference     = $now - $unix_date;
			$tense         = __("ago", 'users-ultra');
		   
		} else {
			$difference     = $unix_date - $now;
			$tense         = __("from now", 'users-ultra');
		}
	   
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
	   
		$difference = round($difference);
	   
		if($difference != 1) {
			$periods[$j].= "s";
		}
		
		if($difference == 0) {
			$difference=1;
		}
		
		
	   
		return "$difference $periods[$j] {$tense}";
	}
	
	/**
	 * Inbox page
	 */
	function show_usersultra_my_messages()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
		$html = '';
		
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $user_id. '"  AND `deleted` != "2"   ORDER BY `date` DESC' );
		
		
		
	    $html .= '<div class="tablenav">
                
               <span style="float:left"><a href="?module=messages">'.__("Inbox", 'users-ultra').'</a> | <a href="?module=messages_sent">'.__("Sent", 'users-ultra').'</a></span> ';
					                    
                   
		
		if ( !empty( $status ) )
		{
			$html .= '<div id="message" class="updated fade"><p>'. $status. '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			$html .= '<p>'. __( 'You have no items in inbox.', 'xoousers' ). '</p>';
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
			
			
			$html .= '<span style="float:right">'.  __( 'You have '.$n.' private message ('.$num_unread.' unread).', 'xoousers' ). '</span>'     ;    
				
				
				$html .= '</div>';
		
						
			$html .= '<form action="" method="get">
				'. wp_nonce_field( 'usersultra-bulk-action_inbox' ).'
				<input type="hidden" name="page" value="usersultra_inbox" /> ';
	
				
	
			$html .= '	<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						<th class="manage-column "><input type="checkbox" /></th>
                        <th class="manage-column check-column">'.__( 'Pic', 'xoousers' ).'</th>
						<th class="manage-column" >'.__( 'Sender', 'xoousers' ).'</th>
						<th class="manage-column">'.__( 'Subject', 'xoousers' ).'</th>
						<th class="manage-column" >'.__( 'Date', 'xoousers' ).'</th>
                        <th class="manage-column" >'.__( 'Action', 'xoousers' ).'</th>
					</tr>
					</thead>
					<tbody>';
					
						
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
								$message_status ='fa-eye';	
								$message_status_text = __('Mark as Unread', 'users-ultra');	
								$new_status = 0;		
							
							}else{
								
								$message_status ='fa-eye-slash';
								$message_status_text = __('Mark as Read', 'users-ultra');		
								$new_status = 1;
							
							
							}
							
							$read_class="";
							if($msg->readed==0)
							{
								
								$read_class="class='uultra-unread-message'";								
							}
						
							
							
						$html .= '<tr '.$read_class.'>
							<td ><input type="checkbox" name="message_id[]" value="'.$msg->id.'" />							<span></span></td>';
                            
                        $html .='<td><span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null).'</span></td>
							<td>'.$msg->sender.'</td>
							<td>';
								
                                
                        $html .=  '<a href="'.$xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) .'"><b>'.stripcslashes( $msg->subject ).'</b></a>
								<div class="row-actions">
								<span>
									<a href="'.$xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) .'">'.__( 'View', 'xoousers' ).'</a>
								</span>';
																		
							 $html .='	<span class="reply">
									| <a class="reply"
									href="'. $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) .'">'. __( 'Reply', 'xoousers' ).'</a>
								</span>
								</div>
							</td>
							<td>'. $msg->date.'</td>';
                            
                          $html .=  '<td> <a class="uultra-btn-deletemessage  uu-private-message-change-status" href="#" id="" title="'.$message_status_text.'" message-id="'.$message_id.'" message-status="'.$new_status.'" ><span><i class="fa '.$message_status.'"></i></span></a><a class="uultra-btn-deletemessage  uu-private-message-delete" href="#" id="" message-id="'.$message_id.'" ><span><i class="fa fa-times"></i></span></a></td>
						</tr>';
							
						}
						
					$html .='</tbody>
					
				</table>
			</form>';
			
	
		}
		
		return $html;		

	
	}
	
	/**
	 * Outbox page
	 */
	function show_usersultra_my_messages_sent()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
		$html = '';
			
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `sender` = "' . $user_id. '"  AND `deleted` != "2"   ORDER BY `date` DESC' );
		
	
     $html .= '<div class="tablenav">
                
               <span style="float:left"><a href="?module=messages">'.__("Inbox", 'users-ultra').'</a> | <a href="?module=messages_sent">'.__("Outbox", 'users-ultra').'</a></span>';
					                    
                   
		
		if ( !empty( $status ) )
		{
			$html .=  '<div id="message" class="updated fade"><p>'. $status. '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			$html .=  '<p>'. __( 'You have no items in sent box.', 'xoousers' ). '</p>';
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
			
			
			$html .=  '<span style="float:right">'. __( 'You have sent '.$n.' private message(s) ', 'xoousers' ). '</span> </div>'     ;    
				
				
				
		$html .='	<form action="" method="get">
				'.wp_nonce_field( 'usersultra-bulk-action_inbox' ).'
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						<th class="manage-column check-column">'.__( 'Pic', 'xoousers' ).'</th>
						<th class="manage-column" >'.__( 'Receiver', 'xoousers' ).'</th>
						<th class="manage-column">'.__( 'Subject', 'xoousers' ).'</th>
						<th class="manage-column" >'.__( 'Date', 'xoousers' ).'</th>
					</tr>
					</thead>
					<tbody>';
						
						foreach ( $msgs as $msg )
						{
							$user_id = $msg->recipient;
							
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->recipient'" );
							
							//main conversation id							
							if($msg->parent=='-1')
							{
								$conversa_id = $msg->id;
							
							
							}else{
								
								$conversa_id = $msg->parent;
								
								
							}
							
							
						$html .='<tr>
							                            
                            <td><span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null) .'</span></td>
							<td>'.$msg->sender.'</td>
							<td>
								                                
                                <a href="'.$xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id).'"><b>'.stripcslashes( $msg->subject ) .'</b></a>
								<div class="row-actions">
								<span>
									<a href="'. $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id).'">'.__( 'View', 'xoousers' ).'</a>
								</span>';
																	
								
						$html .='</div>
							</td>
							<td>'.$msg->date.'</td>
						</tr>';
							
	
						}
						
					$html .='</tbody>
					
				</table>
			</form>';
			
	
		}
		
		return $html ;
		

	
	}
	
	/**
	 * Inbox page
	 */
	function show_usersultra_latest_messages($howmany)
		
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT  * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $user_id. '"  AND `deleted` != "2"  ORDER BY `date` DESC LIMIT  '.$howmany.'  '  );
		
		
		
		?>
	
		
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
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				<div class="tablenav">
					                    
                    <?php echo '<span style="float:right">', sprintf( _n( 'You have %d private message (%d unread)', 'You have %d private messages (%d unread)', $n, 'xoousers' ), $n, $num_unread ), '</span>'; ?>
                    
				</div>
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                        <th class="manage-column" ><?php _e( 'Pic', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							$user_id = $msg->sender;
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
							
							//main conversation id							
							if($msg->parent=='-1')
							{
								$conversa_id = $msg->id;
							
							
							}else{
								
								$conversa_id = $msg->parent;
								
								
							}
							
							$read_class="";
							if($msg->readed==0)
							{
								
								$read_class="class='uultra-unread-message'";
								
							}
							
							?>
						<tr <?php echo $read_class?> >
							
                             <td>
							 
							 <span class="uultra-u-avatar"><?php echo $xoouserultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null) ?></span>						 
							</td>
                             
							<td><?php echo $msg->sender; ?></td>
							<td>
								<?php
								
									echo '<a href="'. $xoouserultra->userpanel->get_internal_pmb_links("messages","view",$conversa_id).'">'. stripcslashes( $msg->subject ). '</a>';
								
								?>
								
							</td>
							<td><?php echo $msg->date; ?></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}
	
	/**
	 * Inbox page
	 */
	function show_usersultra_inbox()
	{
		global $wpdb, $current_user;
		
		$user_id = get_current_user_id();
	
	// if view message
		if ( isset( $_GET['action'] ) && 'view' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			check_admin_referer( "usersultra-view_inbox_msg_$id" );
	
			// mark message as read
			$wpdb->update( $wpdb->prefix . 'pm', array( 'readed' => 1 ), array( 'id' => $id ) );
	
			// select message information
			$msg = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pm WHERE `id` = "' . $id . '" LIMIT 1' );
			$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
			?>
		<div class="xoouserultra-main">
			<h2><?php _e( 'Inbox \ View Message', 'xoousers' ); ?></h2>
	
			<p><a href="?page=usersultra_inbox"><?php _e( 'Back to inbox', 'xoousers' ); ?></a></p>
			<table class="widefat fixed" cellspacing="0">
				<thead>
				<tr>
					<th class="manage-column" width="20%"><?php _e( 'Info', 'xoousers' ); ?></th>
					<th class="manage-column"><?php _e( 'Message', 'xoousers' ); ?></th>
					<th class="manage-column" width="15%"><?php _e( 'Action', 'xoousers' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php printf( __( '<b>Sender</b>: %s<br /><b>Date</b>: %s', 'xoousers' ), $msg->sender, $msg->date ); ?></td>
					<td><?php printf( __( '<p><b>Subject</b>: %s</p><p>%s</p>', 'xoousers' ), stripcslashes( $msg->subject ) , nl2br( stripcslashes( $msg->content ) ) ); ?></td>
					<td>
							<span class="delete">
								<a class="delete"
									href="<?php echo wp_nonce_url( "?page=usersultra&action=delete&id=$msg->id", 'usersultra-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Delete', 'xoousers' ); ?></a>
							</span>
							<span class="reply">
								| <a class="reply"
								href="<?php echo wp_nonce_url( "?page=usersultra_send&recipient=$msg->sender&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'usersultra-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Reply', 'xoousers' ); ?></a>
							</span>
					</td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th class="manage-column" width="20%"><?php _e( 'Info', 'xoousers' ); ?></th>
					<th class="manage-column"><?php _e( 'Message', 'xoousers' ); ?></th>
					<th class="manage-column" width="15%"><?php _e( 'Action', 'xoousers' ); ?></th>
				</tr>
				</tfoot>
			</table>
		</div>
		<?php
	// don't need to do more!
			return;
		}
	
		// if mark messages as read
		if ( isset( $_GET['action'] ) && 'mar' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			if ( !is_array( $id ) )
			{
				check_admin_referer( "usersultra-mar_inbox_msg_$id" );
				$id = array( $id );
			}
			else
			{
				check_admin_referer( "usersultra-bulk-action_inbox" );
			}
			$n = count( $id );
			$id = implode( ',', $id );
			if ( $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'pm SET `readed` = "1" WHERE `id` IN (' . $id . ')' ) )
			{
				$status = _n( 'Message marked as read.', 'Messages marked as read', $n, 'xoousers' );
			}
			else
			{
				$status = __( 'Error. Please try again.', 'xoousers' );
			}
		}
	
		// if delete message
		if ( isset( $_GET['action'] ) && 'delete' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			if ( !is_array( $id ) )
			{
				check_admin_referer( "usersultra-delete_inbox_msg_$id" );
				$id = array( $id );
			}
			else
			{
				check_admin_referer( "usersultra-bulk-action_inbox" );
			}
	
			$error = false;
			foreach ( $id as $msg_id )
			{
				// check if the sender has deleted this message
				$sender_deleted = $wpdb->get_var( 'SELECT `deleted` FROM ' . $wpdb->prefix . 'pm WHERE `id` = "' . $msg_id . '" LIMIT 1' );
	
				// create corresponding query for deleting message
				if ( $sender_deleted == 1 )
				{
					$query = 'DELETE from ' . $wpdb->prefix . 'pm WHERE `id` = "' . $msg_id . '"';
				}
				else
				{
					$query = 'UPDATE ' . $wpdb->prefix . 'pm SET `deleted` = "2" WHERE `id` = "' . $msg_id . '"';
				}
	
				if ( !$wpdb->query( $query ) )
				{
					$error = true;
				}
			}
			if ( $error )
			{
				$status = __( 'Error. Please try again.', 'xoousers' );
			}
			else
			{
				$status = _n( 'Message deleted.', 'Messages deleted.', count( $id ), 'xoousers' );
			}
		}
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT `id`, `sender`, `subject`, `readed`, `date` FROM ' . $wpdb->prefix . 'pm WHERE `recipient` = "' . $user_id. '" AND `deleted` != "2" ORDER BY `date` DESC' );
		
		?>
	<div class="wrap">
		<h2><?php _e( 'Inbox', 'xoousers' ); ?></h2>
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
			echo '<p>', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread).', $n, 'xoousers' ), $n, $num_unread ), '</p>';
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				<div class="tablenav">
					<select name="action">
						<option value="-1" selected="selected"><?php _e( 'Bulk Action', 'xoousers' ); ?></option>
						<option value="delete"><?php _e( 'Delete', 'xoousers' ); ?></option>
						<option value="mar"><?php _e( 'Mark As Read', 'xoousers' ); ?></option>
					</select> <input type="submit" class="button-secondary" value="<?php _e( 'Apply', 'xoousers' ); ?>" />
				</div>
	
				<table class="widefat fixed" cellspacing="0">
					<thead>
					<tr>
						<th class="manage-column check-column"><input type="checkbox" /></th>
						<th class="manage-column" width="10%"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column" width="20%"><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
							?>
						<tr>
							<th class="check-column"><input type="checkbox" name="id[]" value="<?php echo $msg->id; ?>" />
							</th>
							<td><?php echo $msg->sender; ?></td>
							<td>
								<?php
								if ( $msg->readed )
								{
									echo '<a href="', wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ), '">', stripcslashes( $msg->subject ), '</a>';
								}
								else
								{
									echo '<a href="', wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ), '"><b>', stripcslashes( $msg->subject ), '</b></a>';
								}
								?>
								<div class="row-actions">
								<span>
									<a href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ); ?>"><?php _e( 'View', 'xoousers' ); ?></a>
								</span>
									<?php
									if ( !( $msg->readed ) )
									{
										?>
										<span>
									| <a href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=mar&id=$msg->id", 'usersultra-mar_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Mark As Read', 'xoousers' ); ?></a>
								</span>
										<?php
	
									}
									?>
									<span class="delete">
									| <a class="delete"
										href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=delete&id=$msg->id", 'ulsersultra-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Delete', 'xoousers' ); ?></a>
								</span>
								<span class="reply">
									| <a class="reply"
									href="<?php echo wp_nonce_url( "?page=usersultra_send&recipient=$msg->sender&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'usersultra-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Reply', 'xoousers' ); ?></a>
								</span>
								</div>
							</td>
							<td><?php echo $msg->date; ?></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					<tfoot>
					<tr>
						<th class="manage-column check-column"><input type="checkbox" /></th>
						<th class="manage-column"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</tfoot>
				</table>
			</form>
			<?php
	
		}
		?>
	</div>
	<?php
	}
	

}
$key = "mymessage";
$this->{$key} = new XooUserMyMessage();