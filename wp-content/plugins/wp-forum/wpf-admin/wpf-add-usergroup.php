<?php
/************************ wpf-add-usergroup.php ************************************/

			echo "<h2>".__("Add User Group", "wpforum")."</h2>";

		echo '<form id="usergroupadd" name="usergroupadd" method="post" action="">';
			
			if (function_exists('wp_nonce_field'))
				wp_nonce_field('wpforum-add_usergroup');
				
					echo "<table class='form-table'>
						<tr>
							<th>".__("Name:", "wpforum")."</th>
							<td><input type='text' value='' name='group_name' /></td>
						</tr>
						<tr>
							<th>".__("Description:", "wpforum")."</th>
							<td><input type='text' value='' name='group_description' /></td>
						</tr>
						<tr>
							<th></th>
							<td><input type='submit' name='add_usergroup' value='".__("Save user group", "wpforum")."'/></td>
						</tr>
					</table>
					</form>";
/*********************************************************************/
?>