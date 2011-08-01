<?php
{
global $wpdb, $table_prefix, $wpforum;

		$mods = $wpforum->get_moderators();
		$forums = $wpforum->get_forums();
		$users = $wpforum->get_users();
		$groups = $wpforum->get_groups();

echo "<h2>".__("Add moderator", "wpforum")."</h2>

<form name='add_mod_form' method='post' action='".ADMIN_BASE_URL."moderators'>
<table class='form-table'>
	<tr>
		<th>".__("User:", "wpforum")."</th>
		<td>
			<select name='addmod_user_id'><option selected='selected' value='add_mod_null'>".__("Select user", "wpforum")."</option";
				foreach($users as $user)
					//if(!$wpforum->is_moderator($user->ID))
						echo "<option value='$user->ID'>$user->user_login ($user->ID)</option>";
			echo "</select>";
		echo "</td>
	</tr>
	<tr>
		<th>".__("Moderate:", "wpforum")."</th>
		<td>";
		
		echo "<p class='wpf-alignright'><input type='checkbox'  id='mod_global' name='mod_global' onclick='invertAll(this, this.form, \"mod_forum_id\");' value='true' /> <strong>".__("Global moderator: (User can moderate all forums)", "wpforum")."</strong></p>";
						foreach($groups as $group){
							$forums = $wpforum->get_forums($group->id);
								echo "<p class='wpf-bordertop'><strong>".stripslashes($group->name)."</strong></p>";
								foreach($forums as $forum){
									echo "<p class='wpf-indent'><input type='checkbox' name='mod_forum_id[]' onclick='uncheckglobal(this, this.form);' id='mod_forum_id' value='$forum->id' /> $forum->name</p>";

								}
									
						}
									
					echo "</td>
							<tr>
								<th></th>
								<td><input type='submit' name='add_mod_submit' value='".__("Add moderator", "wpforum")."'</td>

							</tr>
				</tr>
			</table>";




}
?>