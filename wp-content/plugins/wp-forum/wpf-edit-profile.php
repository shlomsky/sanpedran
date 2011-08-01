<?php
global $user_ID, $user_level;

if(isset($_POST['edit_user_submit'])){

	$opt = get_option($user_ID, "wpf_useroptions");
		

	$options = array(	"allow_profile" => $_POST['allow_profile'],
						"notify" 		=> $_POST['notify'],
						"notify_topics" 	=>  $opt['notify_topics']
					);
	update_usermeta($user_ID, "wpf_useroptions", $options);
	
}

$user_id = $_GET['user_id'];

if(!is_numeric($user_id))
	wp_die(__("No such user", "wpforum"));
	

if($user_ID == $user_id or user_level > 8){


$this->header();
	
	$options = get_usermeta($user_ID, "wpf_useroptions");
	$notify_v 			= ($options['notify'] == true)?"checked":"";
	$allow_profile_v 	= ($options['allow_profile'] == true)?"checked":"";
	//$this->pre($options);
	$topics = $options['notify_topics'];
	$tops .= "<ul>";
	foreach((array)$topics as $t){
		$tops .= "<li><a href='".$this->get_threadlink($t)."'>". $this->get_subject($t)."</a></li>";
	}
	$tops .= "</ul>";
	
	$options = maybe_unserialize($options);
	$out .= "<form name='user_edit_form' method='post' action=''>
			<table class='wpf-table' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<th>".__("Edit forum options", "wpforum")."</th>
				</tr>
				<tr>
					 <td  valign='top'>
					 	<p>
					 		<input type='checkbox' name='allow_profile' value='true' $allow_profile_v /> ".__("Allow others to view my profile?", "wpforum")."<br />
					 		<input type='checkbox' name='notify' value='true'  $notify_v /> ".__("Activate email notifications for topics?", "wpforum")."
					 	</p>
					 </td>
					 </tr>
					 <tr>
					 	<td><strong>".__("You have email notifications for these topics:", "wpforum")."</strong><br /><p>$tops</p></td>
					 </tr>
					 <tr>
					 	<td><input type='submit' name='edit_user_submit' value='".__("Save options", "wpforum")."'</td>
					 </tr>

				</table></form>";
							
			$this->o .= $out;
}
else
	wp_die(__("Cheating, are we?", "wpforum"));

?>
