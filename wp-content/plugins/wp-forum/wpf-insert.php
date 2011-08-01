<?php 
session_start();
global $wpdb, $wpforum, $user_ID, $user_level;

$error = false;
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
	} else {
	// before WP 2.6
	require_once($root.'/wp-config.php');
	}
	$wpforum->setup_links();
	$options = get_option("wpforum_options");
	
	if($options['forum_captcha'] == true && !$user_ID){
   		if(($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code'])) ) {
    	  unset($_SESSION['security_code']);
  	 	}
		else {
			$error = true;
			$msg = __("Security code does not mach", "wpforum");
		}
	}
	 
	if(isset($_POST['add_topic_submit'])){
		$subject = $wpforum->input_filter($_POST['add_topic_subject']);
		$content = $wpforum->input_filter($_POST['message']);
		$forum_id = $wpforum->check_parms($_POST['add_topic_forumid']);
		
		if($subject == ""){
			$msg .= "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .= ("<div id='error'><p>".__("You must enter a subject", "wpforum")."</p></div>");
			$error = true;
		}
		elseif($content == ""){
			$msg .=  "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .=  ("<div id='error'><p>".__("You must enter a message", "wpforum")."</p></div>");
			$error = true;
		}
		else{
			$date = date("Y-m-d H:i:s", time());

			$sql_thread = "INSERT INTO $wpforum->t_threads 
					(last_post, subject, parent_id, `date`, status, starter) 
			 VALUES('$date', '$subject', '$forum_id', '$date', 'open', '$user_ID')";
			
			$wpdb->query($sql_thread);

		$id = mysql_insert_id();
			$sql_post = "INSERT INTO $wpforum->t_posts 
						(text, parent_id, `date`, author_id, subject)
				 VALUES('$content', '$id', '$date', '$user_ID', '$subject')";
			$wpdb->query($sql_post);
		}
		if(!$error){
			header("Location: ".html_entity_decode($wpforum->get_forumlink($forum_id)."0")); exit;}
		else	
			wp_die($msg);

	}
	if(isset($_POST['add_post_submit'])){
		$subject = $wpforum->input_filter($_POST['add_post_subject']);
		$content = $wpforum->input_filter($_POST['message']);
		$thread = $wpforum->check_parms($_POST['add_post_forumid']);
		
		if($subject == ""){
			$msg .= "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .= ("<div id='error'><p>".__("You must enter a subject", "wpforum")."</p></div>");
			$error = true;
		}
		elseif($content == ""){
			$msg .=  "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .=  ("<div id='error'><p>".__("You must enter a message", "wpforum")."</p></div>");
			$error = true;
		}
		else{
			$date = date("Y-m-d H:i:s", time());
			
			$sql_post = "INSERT INTO $wpforum->t_posts 
						(text, parent_id, `date`, author_id, subject)
				 VALUES('$content', '$thread', '$date', '$user_ID', '$subject')";
			$wpdb->query($sql_post);
			$wpdb->query("UPDATE $wpforum->t_threads SET last_post = '$date' WHERE id = $thread");
		}
			
		if(!$error){
			$wpforum->notify_starter($thread, $subject, $content, $date);
			header("Location: ".html_entity_decode($wpforum->get_threadlink($thread)."0")); exit;
		}
		else	
			wp_die($msg);

	}
	if(isset($_POST['edit_post_submit'])){
		$subject = $wpforum->input_filter($_POST['edit_post_subject']);
		$content = $wpforum->input_filter($_POST['message']);
		$thread = $wpforum->check_parms($_POST['thread_id']);
		$edit_post_id = $_POST['edit_post_id'];
		
		if($subject == ""){
			$msg .= "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .= ("<div id='error'><p>".__("You must enter a subject", "wpforum")."</p></div>");
			$error = true;
		}
		elseif($content == ""){
			$msg .=  "<h2>".__("An error occured", "wpforum")."</h2>";
			$msg .=  ("<div id='error'><p>".__("You must enter a message", "wpforum")."</p></div>");
			$error = true;
		}
		$sql = ("UPDATE $wpforum->t_posts SET text = '$content', subject = '$subject' WHERE id = $edit_post_id");		
		$wpdb->query($sql);
		
		if(!$error){
			header("Location: ".html_entity_decode($wpforum->get_threadlink($thread)."0")); exit;}
		else	
			wp_die($msg);
	}
	
?>








