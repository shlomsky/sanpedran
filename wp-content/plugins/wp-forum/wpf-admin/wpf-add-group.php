<?php 
/*************** wpf-add-forum.php *********************/	
	echo "<h2>".__("Add category", "wpforum")."</h2>";
	
	echo "<form name='add_group_form' method='post' id='add_group_form' action='".ADMIN_BASE_URL."structure'>";
	echo "<table class='form-table'>
			<tr>
				<th>".__("Name:", "wpforum")."</th>
				<td><input type='text' value='' name='add_group_name' /></td>
			</tr>
			<tr>
				<th>".__("Description:", "wpforum")."</th>
				<td><textarea name='add_group_description' ".ADMIN_ROW_COL."></textarea> </td>
			</tr>
			<tr>
				<th></th>
				<td><input type='submit' value='".__("Save category", "wpforum")."' name='add_group_submit' /></td>
			</tr>";
	
	echo "</table></form>";

/**********************************************************/

?>