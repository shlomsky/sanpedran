<?php
	global $wpforum;
	if($user_ID || $this->allow_unreg()){
	$options = get_option("wpforum_options");
	$this->current_view = NEWTOPIC;
	$out .= $this->header();
	
	$out .= "<form action='".WPFURL."wpf-insert.php' name='addform' method='post'>";
	$out .= "<table class='wpf-table' border='1' width='100%'>
			<tr>
				<th colspan='2'>".__("Post new Topic", "wpforum")."</th>
			</tr>
			<tr>	
				<td>".__("Subject:", "wpforum")."</td>
				<td><input type='text' name='add_topic_subject' /></td>
			</tr>
			<tr>	
				<td valign='top'>".__("Message:", "wpforum")."</td>
				<td>
					".$this->form_buttons()."

					<br /><textarea ".ROW_COL." name='message' ></textarea>
				</td>
			</tr>";
			
				$out .= $this->get_captcha();

			$out .= "<tr>
				<td></td>
				<td><input type='submit' name='add_topic_submit' value='".__("Submit", "wpforum")."' /></td>
				<input type='hidden' name='add_topic_forumid' value='".$this->check_parms($_GET['forum'])."'/>
			</tr>

			</table></form>";
		$this->o .= $out;
	}
	else
		wp_die(__("Sorry. you don't have permission to post.", "wpforum"))
?>