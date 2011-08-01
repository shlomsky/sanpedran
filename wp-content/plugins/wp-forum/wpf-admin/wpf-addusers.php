<?php
			$usergroups = $wpforum->get_usergroups();

echo "<h2>".__("Add users", "wpforum")."</h2>
								<form name='add_usertogroup_form' action='".ADMIN_BASE_URL."usergroups' method='post'>
								<table class='form-table'>
									<tr>
										<th>User names:</th>
										<td>".__("Separate user names by comma sign ( , )", "wp.forum")."<br />
											<textarea name='togroupusers' ".ADMIN_ROW_COL."></textarea></td>
									</tr>
									<tr>
										<th>".__("User group:", "wpforum")."</th>
										<td>
											<select name='usergroup'>
												<option selected='selected' value='add_user_null'>".__("Select User group", "wpforum")."</option>";
												foreach($usergroups as $usergroup){
												
												echo "<option value='$usergroup->id'>
													$usergroup->name
												</option>";
												}
												
											echo "</select>
										</td>
									</tr>
								<tr>
										<th></th>
										<td><input name='add_user_togroup' type='submit' value='".__("Add users", "wpforum")."' /></td>
									</tr>

								</table>
								</form>";
?>