<?php
$quote = "";
global $wpdb, $wpforum;

if($user_ID || $this->allow_unreg()){

if(isset($_GET['quote'])){
	$quote_id = $this->check_parms($_GET['quote']);
	$text = $wpdb->get_row("SELECT text, author_id, `date` FROM $this->t_posts WHERE id = $quote_id");
	
	$user = get_userdata($text->author_id);
	$q = "[quote][b]".__("Quote from", "wpforum")." $user->user_login ".__("on", "wpforum")." ".$wpforum->format_date($text->date)."[/b]\n" .htmlentities($text->text, ENT_QUOTES, get_bloginfo('charset'))."[/quote]";
}
if(($_GET['wpforumaction'] == "postreply")){

	$options = get_option("wpforum_options");
	$this->current_view = POSTREPLY;
	$thread = $this->check_parms($_GET['thread']);
		$out .= $this->header();

	$out .= "<form action='".WPFURL."wpf-insert.php' name='addform' method='post'>";
	$out .= "<table class='wpf-table' width='100%'>
			<tr>
				<th colspan='2'>".__("Post Reply", "wpforum")."</th>
			</tr>
			<tr>	
				<td>".__("Subject:", "wpforum")."</tf>
				<td><input type='text' name='add_post_subject' value='Re: ".$this->get_subject($thread)."'/></td>
			</tr>
			<tr>	
				<td valign='top'>".__("Message:", "wpforum")."</td>
				<td>";
						$out .= $this->form_buttons();

					$out .= "<br /><textarea ".ROW_COL." name='message' >".stripslashes($q)."</textarea>";
				$out .= "</td>
			</tr>";
			
				$out .= $this->get_captcha();
			
			$out .= "<tr>
				<td></td>
				<td><input type='submit' name='add_post_submit' value='".__("Submit", "wpforum")."' /></td>
				<input type='hidden' name='add_post_forumid' value='".$this->check_parms($thread)."'/>

			</tr>

			</table></form>";
		$this->o .= $out;
	}


if(($_GET['wpforumaction'] == "editpost")){

	$this->current_view = EDITPOST;

	$id = $wpdb->escape($_GET['id']);
	$thread = $this->check_parms($_GET['t']);

		$out .= $this->header();

	$post = $wpdb->get_row("SELECT * FROM $wpforum->t_posts WHERE id = $id");
	
	$out .= "<form action='".WPFURL."wpf-insert.php' name='addform' method='post'>";
	$out .= "<table class='wpf-table' width='100%'>
			<tr>
				<th colspan='2'>".__("Edit Post", "wpforum")."</th>
			</tr>
			<tr>	
				<td>".__("Subject:", "wpforum")."</tf>
				<td><input type='text' name='edit_post_subject' value='".stripslashes($post->subject)."'/></td>
			</tr>
			<tr>	
				<td valign='top'>".__("Message:", "wpforum")."</td>
				<td>";
						$out .= $wpforum->form_buttons();

					$out .= "<br /><textarea ".ROW_COL." name='message' >".stripslashes($post->text)."</textarea>";
				$out .= "</td>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' name='edit_post_submit' value='".__("Save Post", "wpforum")."' /></td>
				<input type='hidden' name='edit_post_id' value='".$post->id."'/>
				<input type='hidden' name='thread_id' value='".$thread."'/>

			</tr>

			</table></form>";
		$this->o .= $out;
	}
}


























	else
		wp_die(__("Sorry. you don't have permission to post.", "wpforum"));

?>

