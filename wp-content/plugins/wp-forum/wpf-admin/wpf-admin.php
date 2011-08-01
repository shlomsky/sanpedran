<?php
//include("wpf_define.php");
class wpforumadmin{
		
		var $admin_tabs = array();
		var $cur_tab = "";

		function wpforumadmin(){
			$this->admin_page();
		}
		function output_filter($string){
			return "";
		}
		function admin_page(){
		global $wpdb, $table_prefix;
		$wpdb->show_errors();
			$this->admin_tabs = array(	"options" 		=> __("General Options", "wpforum"),
										"structure" 	=> __("Categories and forums", "wpforum"),
										"skins"			=> __("Skins", "wpforum"),
										"moderators"	=> __("Moderators", "wpforum"),
										"usergroups"	=> __("User Groups", "wpforum"),
										"about"			=> __("About", "wpforum")
										);
										
		if (isset($_REQUEST['wpforum_action']) && !empty($this->admin_tabs[$_REQUEST['wpforum_action']]))
			$cur_tab = $_REQUEST['wpforum_action'];
		else
			$cur_tab = "options";
			
			echo "<ul id='wpf-menu'>";
			$url = ADMIN_BASE_URL."/wpf-admin.php&amp;wpforum_action=";
			foreach ($this->admin_tabs as $tab => $name) {
				if ($cur_tab == $tab)
					echo "<li class='current'><a href=\"" . $url . $tab . "\">$name</a></li>";
				else
					echo "<li><a href=\"" . $url . $tab . "\">$name</a></li>";
			}

			echo "</ul>";
			
			switch($cur_tab){
				case "options": 	$this->options(); break;
				case "structure": 	$this->structure(); break;
				case "usergroups": 	$this->usergroups(); break;
				case "skins": 		$this->skins(); break;
				case "moderators":	$this->moderators(); break;
				case "about": 		$this->about(); break;
				/*case "users": 		$this->users(); break;*/			
			}
			
		}
		function delete_usergroups(){
			if (isset($_POST['delete_usergroups'])){
				global $wpdb, $table_prefix;
				$delete_usrgrp = $_POST['delete_usrgrp'];
				$groups = "";
				$count = count($delete_usrgrp);
				for($i = 0; $i < $count; $i++){
					$wpdb->query("DELETE FROM ".$table_prefix."forum_usergroups WHERE id = {$delete_usrgrp[$i]}");
					$wpdb->query("DELETE FROM ".$table_prefix."forum_usergroup2user WHERE `group` = {$delete_usrgrp[$i]}");

				}
				return true;
			}
			return false;
		}
		function add_usergroup(){
			if(isset($_GET['do']) && $_GET['do'] == "addusergroup" && !isset($_POST['add_usergroup'])){
				include("wpf-add-usergroup.php");
				return false;
			}

			global  $wpdb, $table_prefix;
			$name = $wpdb->escape($_POST['group_name']);
			$desc = $wpdb->escape($_POST['group_description']);
			if (isset($_POST['add_usergroup'])){
				if($_POST['group_name'] == null || $_POST['group_name'] == "")
					return __("You must specify a user group name.", "wpforum");
				else if($wpdb->get_var("SELECT id FROM ".$table_prefix."forum_usergroups WHERE name = '$name'"))
					return __("You have choosen a name that already exists in the database, please specify another", "wpforum");
				$wpdb->query("INSERT INTO ".$table_prefix."forum_usergroups (name, description) VALUES('$name', '$desc')");
				return __("User Group successfully added.", "wpforum");
			}
			return false;		
		}
		
		function add_user_togroup(){
			global $wpdb, $table_prefix, $wpforum;
			if(isset($_GET['do']) && $_GET['do'] == "add_user_togroup" && !isset($_POST['add_user_togroup'])){
				include("wpf-addusers.php");
				return false;
			}
			$warnings = 0;
			$errors = 0;
			$added = 0;
			if (isset($_POST['add_user_togroup'])){
				$users = explode(",", $_POST['togroupusers']);

				if($_POST['togroupusers'] == ""){
					return __("You haven't specified any user to add:", "wpforum");
				}
				$group =  $_POST['usergroup'];
				if($group == "add_user_null")
					return __("You must choose a user group", "wpforum");
					
				foreach($users as $user){
					if($user){
						trim($user);
						$id = username_exists($user);
						if(!$id){
							$msg .= "<strong>".__("Error", "wpforum")." - </strong> ".__("No such user:", "wpforum")." \"$user\"<br />";
							++$errors;
						}
						elseif($wpforum->is_user_ingroup($id, $group)){
							$msg .= "<strong>".__("Warning", "wpforum")." - </strong> ".__("User", "wpforum")." \"$user\" ".__("is already in this group", "wpforum")."<br />";
							++$warnings;
						}
						else{
							$msg .= __("User", "wpforum")." \"$user\" ".__("added successfully", "wpforum")."<br />";
							$sql = "INSERT INTO ".$table_prefix."forum_usergroup2user (user_id, `group`) VALUES('$id', '$group')";
							$wpdb->query($sql);
							++$added;
						}
					}
				}
				return 
					 __("Errors:","wpforum")." $errors,
					".__("Warnings:", "wpforum")." $warnings, 
					".__("Users added:", "wpforum")." $added 
					<br/>-------------------------------<br/> $msg";
			}
			return false;

		}
		
		function usergroups(){
			global $wpdb, $wpforum, $table_prefix;
			$usergroups = $wpforum->get_usergroups();

			echo "<div class='wrap'>";

			if($this->delete_usergroups())
					echo '<div id="message" class="updated fade"><p>' . __('User Group(s) successfully deleted.', 'wpforum') . '</p></div>';
			if($msg = $this->add_usergroup())
					echo "<div id='message' class='updated fade'><p>$msg</p></div>";
					
			if($msg = $this->add_user_togroup())
					echo "<div id='message' class='updated fade'><p>$msg</p></div>";
			if(isset($_GET['do']) && $_GET['do'] == "removemember"){
				$count = $wpdb->query("DELETE FROM ".$table_prefix."forum_usergroup2user WHERE user_id = {$_GET['memberid']} AND `group` = {$_GET['groupid']}");
				echo "<div id='message' class='updated fade'><p>" . __("Member successfully removed.", "wpforum") . "</p></div>";
			}
			if(isset($_GET['do']) && $_GET['do'] == "edit_usergroup"){
				include("wpf-usergroup-edit.php");
			}

					echo "<h2>".__("Manage User Groups", "wpforum")." <a href='".ADMIN_BASE_URL."usergroups&do=addusergroup'> (".__("add new", "wpforum").")</a></h2>";
			$usergroups = $wpforum->get_usergroups();

/*****************************************/
			if($usergroups){
				echo "<form method='post' name='delete_usergroups_form' action='".ADMIN_BASE_URL."usergroups'>";
				echo "<div class='tablenav'>
						<div class='alignleft'>
							<input type='submit' name='delete_usergroups' class='button-secondary delete' value='".__("Delete", "wpforum")."'/>
						</div>
						<br class='clear' />
					</div>
						<br class='clear' />";

				foreach($usergroups as $usergroup){
					echo "<table class='widefat'>
						<thead>
							<tr>
								<th  class='check-column'><input type='checkbox' value='$usergroup->id' name='delete_usrgrp[]' /></th>
								<th><a href='".ADMIN_BASE_URL."usergroups&do=edit_usergroup&usergroup_id=$usergroup->id'>".stripslashes($usergroup->name)."</th>
								<th>".stripslashes($usergroup->description)."</th>
							</tr>
						</thead>";

					/*echo "<tr class='alternate'>
							<th class='check-column'><input type='checkbox' value='$usergroup->id' name='delete_usrgrp[]' /></th>
							<td><a href='".ADMIN_BASE_URL."usergroups&do=edit_usergroup&usergroup_id=$usergroup->id'>$usergroup->name</a></td>
							<td>$usergroup->description</td>
							</tr>";*/
				
						$members = $wpforum->get_members($usergroup->id);
						if($members){
							echo "<tr>
									<td colspan='3'>
										<table class='wpf-wide'>
									<tr>
										<th>".__("Members", "wpforum")."</th>
										<th>Name</th>
										<th></th>
									</tr>";
							foreach($members as $member){
								$user = get_userdata($member->user_id);
								echo "<tr><td>".$user->user_login." <a href='".ADMIN_BASE_URL."usergroups&do=removemember&memberid=$member->user_id&groupid=$usergroup->id'> (".__("Remove", "wpforum").")</a></td>
									<td>".get_usermeta($member->user_id, "first_name")." ".get_usermeta($member->user_id, "last_name")."</td>
									<td><a href='".ADMIN_PROFILE_URL."$member->user_id'>".__("View profile", "wpforum")."</a></td>
									</tr>";
							}
							echo "<tr>
							<td colspan='3' align='right'><a href='".ADMIN_BASE_URL."usergroups&do=add_user_togroup'>".__("Add members", "wpforum")."</a></td>
						</tr></table>
						</td></tr>";	
						}
						else{
							echo "<tr><td colspan='3'>". __("No members in this group", "wpforum")."</tr></td>";
							echo "<tr><td align='right' colspan='3'><a href='".ADMIN_BASE_URL."usergroups&do=add_user_togroup'>".__("Add members", "wpforum")."</td></tr>";

						}
						echo "</table><br class='clear' />";
		
				}
				echo "</form>";
			}


			echo "</div>";
		}
		
		function activate_skin(){
			if (isset($_GET['action']) && $_GET['action'] == "activateskin"){
			$op = get_option('wpforum_options');

				$options = array( 'forum_posts_per_page' 		=> $op['forum_posts_per_page'],
								'forum_threads_per_page' 		=> $op['forum_threads_per_page'], 
								'forum_require_registration' 	=> $op['forum_require_registration'],
								'forum_date_format' 			=> $op['forum_date_format'],
								'forum_use_gravatar' 			=> $op['forum_use_gravatar'],
								'forum_skin'					=> $_GET['skin'],
								'forum_allow_post_in_solved' 	=> $op['forum_allow_post_in_solved'],
								'set_sort' 						=> $op['set_sort'],
								'forum_use_spam' 				=> $op['forum_use_spam'],
								'forum_use_bbcode' 				=> $op['forum_use_bbcode'],
								'forum_capcha' 					=> $op['forum_captcha'],
								'hot_topic'						=> $op['hot_topic'],
								'veryhot_topic'					=> $op['veryhot_topic']
								); 
								
				update_option('wpforum_options', $options);

				return true;
			}		
			return false;
		}
		function skins(){
			// Find all skins within directory
			// Open a known directory, and proceed to read its contents
			if($this->activate_skin())
					echo '<div id="message" class="updated fade"><p>' . __('Skin successfully activated.', 'wpforum') . '</p></div>';

			$op = get_option('wpforum_options');
			if (is_dir(SKINDIR)) {
			   if ($dh = opendir(SKINDIR)) {
				
				echo "<div class='wrap'><h2>".__("Skin options", "wpforum")."</h2><br class='clear' /><table class='widefat'>
						<thead>
							<tr>
								<th>".__("Screenshot", "wpforum")."</th>
								<th >".__("Name", "wpforum")."</th>
								<th >".__("Version", "wpforum")."</th>
								<th >".__("Description", "wpforum")."</th>
								<th >".__("Action", "wpforum")."</th>

							</tr>
						</thead>";
											
				   while (($file = readdir($dh)) !== false) {
						if(filetype(SKINDIR . $file) == "dir" && $file != ".." && $file != "." && substr($file, 0, 1) != "."){
							$p = file_get_contents(SKINDIR.$file."/style.css");
							$class = ($class == "alternate")?"":"alternate";

							echo "<tr class='$class'>
									<td>".$this->get_skinscreenshot($file)."</td>
									<td>".$this->get_skinmeta('Name', $p)."</td>
									<td>".$this->get_skinmeta('Version', $p)."</td>
									<td>".$this->get_skinmeta('Description', $p)."</td>";
									if($op['forum_skin'] == $file)
										echo "<td>In Use</td></tr>";
									else
										echo "<td><a href='".ADMIN_BASE_URL."skins&action=activateskin&skin=$file'>Activate</a></td></tr>";
						}
					}
				}
			}
			echo "</table></div>";

		}
		// PNG | JPG | GIF | only
		function get_skinscreenshot($file){
			$exts = array("png", "jpg", "gif");
			foreach($exts as $ext){
				if(file_exists(SKINDIR."$file/screenshot.$ext")){
					$image = SKINURL."$file/screenshot.$ext";
						return "<a href='$image'><img src='$image' width='100' height='100'></a>";
				}
			}
			return "<img src='".NO_SKIN_SCREENSHOT_URL."' width='100' height='100'>";
		}
		function get_skinmeta($field, $data){
			if (preg_match("|$field:(.*)|i", $data, $match)) {
				$match = $match[1];
			}
			return $match;
		}
		function about(){
			echo "<div class='wrap'>
			
				<h2>About WP Forum</h2>
				<p>Current Version: <strong>".$this->get_version()."</strong></p>
				<p>Discussion forum plugin for WordPress. With support for different skins, 3 included by default, changeable from the WP admin interface. Admin can choose if unregistered posting is allowed and Captcha (optional) is used for spam control. Tight interaction with Wordpress makes an easy to use and administer plugin. Support for different user groups allowing or disallowing members to view forums in a groups.</p>
				<ul>
					<li>Author: Fredrik Fahlstad</li>
					<li>Support Forum: <a href='http://www.fahlstad.se/wp-forum/'>www.fahlstad.se/forum</a></li>
					<li>Contact: <a href='http://www.fahlstad.se/contact/'>www.fahlstad.se/contact</a></li>
					<li>Thanks to <a href='http://www.lvlolvlo.net/'>lvlo</a> for keeping things going during my hiatus.</li>
				</ul>
			</div>";
		}
		
		function get_usercount(){
			global $wpdb, $table_prefix;
			return $wpdb->get_var("SELECT count(*) from ".$table_prefix."users");
		}
		function get_dbsize(){
			global $wpdb;
			$res = $wpdb->get_results("SHOW TABLE STATUS");
			foreach($res as $r)
				$size += $r->Data_length + $r->index_length;
				
			return $this->formatfilesize($size);
		}

		function formatfilesize( $data ) {
			// bytes
			if( $data < 1024 ) {
				return $data . " bytes";
			}
			// kilobytes
			else if( $data < 1024000 ) {
				return round( ( $data / 1024 ), 1 ) . "k";
			}
			// megabytes
			else {
				return round( ( $data / 1024000 ), 1 ) . " MB";
			}
		
		}
    
		function get_version(){
			$plugin_data = implode('', file(ABSPATH."wp-content/plugins/".WPFPLUGIN."/wpf-main.php"));
			if (preg_match("|Version:(.*)|i", $plugin_data, $version)) {
				$version = $version[1];
			}
			return $version;
		}
		function options(){
			if ($this->option_save())
				echo '<div id="message" class="updated fade"><p>' . __('Options successfully saved.', 'wpforum') . '</p></div>';
			global $wpforum;
			$op = get_option('wpforum_options');

			echo '<div class="wrap">
			<h2>'.__("Forum statistics", "wpforum").'</h2><br class="clear" />
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col">'.__("Statistic", "wpforum").'</th>
						<th scope="col">'.__("Value", "wpforum").'</th>
					</tr>
				</thead>
					<tr class="alternate">
						<td>'.__("Number of posts:", "wpforum").'</td>
						<td>'.$wpforum->num_posts_total().'</td>
					</tr>
					<tr>
						<td>'.__("Number of threads:", "wpforum").'</td>
						<td>'.$wpforum->num_threads_total().'</td>
					</tr>
					<tr class="alternate">
						<td>'.__("Number of users:", "wpforum").'</td>
						<td>'.$this->get_usercount().'</td>
					</tr>
					<tr>
						<td>'.__("Total database size:", "wpforum").'</td>
						<td>'.$this->get_dbsize().'</td>
					</tr>
					<tr class="alternate">
						<td>'.__("Database server:", "wpforum").'</td>
						<td>'.mysql_get_server_info().'</td>
					</tr>
					<tr>
						<td>'.__("WP Forum version:", "wpforum").'</td>
						<td>'.$this->get_version().'</td>
					</tr>
			</table>
			<h2>'.__("General Options", "wpforum").'</h2>';
			
			echo '<form id="wpforum_option_form" name="wpforum_option_form" method="post" action="">';
			
			if (function_exists('wp_nonce_field'))
				wp_nonce_field('wpforum-manage_option');
				
				echo "<table class='form-table'>
		<tr>
			<th>".__("Posts per page:", "wpforum")."</th>
			<td><input type='text' name='forum_posts_per_page' value='".$op['forum_posts_per_page']."' /></td>
		</tr>
		<tr>
			<th>".__("Threads per page:", "wpforum")."</th>
			<td><input type='text' name='forum_threads_per_page' value='".$op['forum_threads_per_page']."' /></td>
	
		</tr>
		
		<tr>
			<th>".__("Number of posts for hot Topic:", "wpforum")."</th>
			<td><input type='text' name='hot_topic' value='".$op['hot_topic']."' /></td>
		</tr>
		<tr>
			<th>".__("Number of posts for Very Hot Topic:", "wpforum")."</th>
			<td><input type='text' name='veryhot_topic' value='".$op['veryhot_topic']."' /></td>
		</tr>
		
				
		<tr>
			<th>".__("Show Avatars in the forum:", "wpforum")."</th>
		  	<td><input type='checkbox' name='forum_use_gravatar' value='true'";
		 if($op['forum_use_gravatar'] == 'true') 
			echo "checked='checked'";
		echo "/></td>
		</tr>
		
			<tr>
				<th>".__("Registration required to post:", "wpforum")."</th>
			  	<td><input type='checkbox' name='forum_require_registration' value='true'";
			 if($op['forum_require_registration'] == 'true') 
				echo "checked='checked'";
			echo "/></td></tr>";
			
			if (function_exists("gd_info")){
				$gd = gd_info();
				$status = ""; 
				$lib = "<br /><strong>".__("Installed version:", "wpforum")." {$gd['GD Version']}</strong>";
			}
			else {
				$status = "disabled";
				$lib = "<br /><strong>".__("GD Library is not installed", "wpforum")."</strong>";
			}
			echo "<tr>
				<th>".__("Use Captcha for unregistered users:", "wpforum")."</th>
			  	<td><input type='checkbox' name='forum_captcha' value='true' $status";
			 if($op['forum_captcha'] == 'true') 
				echo "checked='checked'";
			echo "/> (Requires <a href='http://www.libgd.org/Main_Page'>GD</a> library) $lib</td>
			</tr>";
									
			echo "<tr>
				<th valign='top'>".__("Date format:", "wpforum")."</th><td><input type='text' name='forum_date_format' value='".$op['forum_date_format']."'  /> <p>".__("Default date:", "wpforum")." \"F j, Y, H:i\". <br />Check <a href='http://www.php.net'>http://www.php.net</a> for date formatting.</p></td>
			</tr>
			<tr>
			<td></td>
			<td>
			<input type='submit' name='wpforum_option_save' value='". __("Save options", 'wpforum')."'  />
		</tr>
		</table>
		
		</form>";

		}
		
		function option_save(){
			if (isset($_POST['wpforum_option_save'])) {
			$op = get_option('wpforum_options');
				global $wpdb, $table_prefix;
				$options = array( 'forum_posts_per_page' 		=> $wpdb->escape($_POST['forum_posts_per_page']),
								'forum_threads_per_page' 		=> $wpdb->escape($_POST['forum_threads_per_page']), 
								'forum_require_registration' 	=> $_POST['forum_require_registration'],
								'forum_date_format' 			=> $wpdb->escape($_POST['forum_date_format']),
								'forum_use_gravatar' 			=> $_POST['forum_use_gravatar'],
								'forum_skin'					=> $op['forum_skin'],
								'forum_allow_post_in_solved' 	=> $_POST['forum_allow_post_in_solved'],
								'set_sort' 						=> $op['set_sort'],
								'forum_use_spam' 				=> $_POST['forum_use_spam'],
								'forum_use_bbcode' 				=> $_POST['forum_use_bbcode'],
								'forum_captcha' 				=> $_POST['forum_captcha'],
								'hot_topic' 					=> $_POST['hot_topic'],
								'veryhot_topic' 				=> $_POST['veryhot_topic']
								); 
								
				update_option('wpforum_options', $options);
								
				return true;
			}
			return false;

		}
		
	function delete_forum_group(){
		
		if(isset($_POST['delete_forum_groups'])){
		global $wpdb, $table_prefix;
		$msg = "";
			$table_forums = $table_prefix."forum_forums";
			$table_groups = $table_prefix."forum_groups";
			$table_threads = $table_prefix."forum_threads";
			$table_posts = $table_prefix."forum_posts";
			$thread_count = 0;
			$post_count = 0;
			$group_count = 0;
			$forum_count = 0;

			$groups = $_POST['delete_groups'];
			$forums = $_POST['delete_forums'];
			
			$forum_num = count($forums);
			$group_num = count($groups);

			// Delete marked groups
			for($i = 0; $i < $group_num; $i++){
			
				// Get all forums
				$forums = $wpdb->get_results("select id from $table_forums where parent_id = {$groups[$i]}");

				// Loop trough the forums
				foreach($forums as $forum){
				
					// Get all threads
					$threads = $wpdb->get_results("select id from $table_threads where parent_id = $forum->id");
					
					// Delete threads
					$thread_count += $wpdb->query("DELETE FROM $table_threads WHERE parent_id = $forum->id");
					
					// Loop through the threads
					foreach($threads as $thread){
						
						// Delete posts
						$post_count += $wpdb->query("DELETE FROM $table_posts WHERE parent_id = $thread->id");
					}
					// Delete forums
					$forum_count += $wpdb->query("DELETE FROM $table_forums WHERE parent_id = {$groups[$i]}");
				}
				// Delete the group
				$group_count += $wpdb->query("DELETE FROM $table_groups WHERE id = {$groups[$i]}");
			}
			
			// Delete marked forums			
			for($i = 0; $i < $forum_num; $i++){
			
				$threads = $wpdb->get_results("select id from $table_threads where parent_id = {$forums[$i]}");
				
				foreach($threads as $thread){

					$post_count += $wpdb->query("DELETE FROM $table_posts WHERE parent_id = $thread->id");
				}
				$thread_count += $wpdb->query("DELETE FROM $table_threads WHERE parent_id = {$forums[$i]}");

				
				$forum_count += $wpdb->query("DELETE FROM $table_forums WHERE id = {$forums[$i]}");
			}
			$msg .=  __("Groups deleted:", "wpforum")." ".$group_count."<br/>"
					.__("Forums deleted:", "wpforum")." ".$forum_count."<br/>"
					.__("Threads deleted:", "wpforum")." ".$thread_count."<br/>"
					.__("Posts deleted:", "wpforum")." ".$post_count."<br/>";
		
			return $msg;
		}
		return false;
	}
	
	function edit_forum_group(){
		global $wpforum;
		if(isset($_GET['do']) && $_GET['do'] == "editgroup"){
			include("wpf-edit-forum-group.php");
		}
		if(isset($_GET['do']) && $_GET['do'] == "editforum"){
			include("wpf-edit-forum-group.php");
		}
	}
	function add_group(){
		if(isset($_POST['add_group_submit'])){
			global $wpdb, $table_prefix;
			
			$add_group_description = $wpdb->escape($_POST['add_group_description']);
			$add_group_name = $wpdb->escape($_POST['add_group_name']);

			if($add_group_name == "")
				return __("You must enter a name", "wpforum");
			if($wpdb->get_var("SELECT id FROM ".$table_prefix."forum_groups WHERE name = '$add_group_name'"))
				return __("You have choosen a name that already exists in the database, please specify another", "wpforum");
			
			$max = $wpdb->get_var("SELECT MAX(sort) from ".$table_prefix."forum_groups") + 1;

			$wpdb->query("INSERT INTO ".$table_prefix."forum_groups (name, description, sort) 
				VALUES('$add_group_name', '$add_group_description', '$max')");
		
			return __("Category added successfully", "wpforum");
		}
		return false;
	}
	
	function add_forum(){
		if(isset($_POST['add_forum_submit'])){
			global $wpdb, $table_prefix;
			$add_forum_description = $wpdb->escape($_POST['add_forum_description']);
			$add_forum_name = $wpdb->escape($_POST['add_forum_name']);
			$add_forum_group_id = $_POST['add_forum_group_id'];
			if($_POST['add_forum_group_id'] == "add_forum_null")
				return __("You must select a category", "wpforum");
				
			if($_POST['add_forum_name'] == "")
				return __("You must enter a name", "wpforum");
				
			if($wpdb->get_var("select id from ".$table_prefix."forum_forums where name = '$add_forum_name' and parent_id = $add_forum_group_id"))
				return __("You have choosen a forum name that already exists in this group, please specify another", "wpforum");
			
			$max = $wpdb->get_var("SELECT MAX(sort) from ".$table_prefix."forum_forums WHERE parent_id = $add_forum_group_id") + 1;

			$wpdb->query("INSERT INTO ".$table_prefix."forum_forums (name, description, parent_id, sort) 
				VALUES('$add_forum_name', '$add_forum_description', '$add_forum_group_id', '$max')");
		
				return __("Forum added successfully", "wpforum");
			}
		return false;
	}
	
function structure(){
	global $wpforum;
	echo "<div class='wrap'>";

	if($msg = $this->delete_forum_group())
		echo "<div id='message' class='updated fade'><p>$msg</p></div>";
	if($msg = $this->move_up_down())
		echo "<div id='message' class='updated fade'><p>$msg</p></div>";
	if($msg = $this->add_group())
		echo "<div id='message' class='updated fade'><p>$msg</p></div>";
	if($msg = $this->add_forum())
		echo "<div id='message' class='updated fade'><p>$msg</p></div>";
		
	if(isset($_GET['do']) && $_GET['do'] == "addforum")
		include('wpf-add-forum.php');

	if(isset($_GET['do']) && $_GET['do'] == "addgroup")
		include('wpf-add-group.php');
		

	// Check if group/forum update is nessesrary
	$this->edit_forum_group();
		
	echo "<h2>".__("Manage categories and forums", "wpforum")." 
	<a href='".ADMIN_BASE_URL."structure&do=addgroup'>(".__("add category", "wpforum").")</a></h2>";
		
	
	$groups = $wpforum->get_groups();
	
	
	
	echo "<form method='post' name='delete_forum_groups_form' action='".ADMIN_BASE_URL."structure'>";
	echo "<div class='tablenav'>
			<div class='alignleft'>
				<input type='submit' name='delete_forum_groups' class='button-secondary delete' value='".__("Delete", "wpforum")."'/>
			</div>
			<br class='clear' />
		</div>
			<br class='clear' />";



				
		//echo "<tr><td><a href='$edit_link'>$group->sort $group->name</a></td><td><a href='$up_link'>&#x2191;</a> | <a href='$down_link'>&#x2193;</a></td>";

	foreach($groups as $group){
		$up_link 	= ADMIN_BASE_URL."structure&do=group_up&id=$group->id";
		$down_link 	= ADMIN_BASE_URL."structure&do=group_down&id=$group->id";
		$edit_link	= ADMIN_BASE_URL."structure&do=editgroup&groupid=$group->id";

		echo "<table class='widefat'>";
		echo "<thead>
					<th class='check-column'><input type='checkbox' value='$group->id' name='delete_groups[]' /></th>
					<th>".stripslashes($group->name)."<a href='$edit_link'> ".__("Modify", "wpforum")."</a></th>
					<th nowrap><a href='$up_link'>&#x2191;</a> | <a href='$down_link'>&#x2193;</a></th>
					<th></th>
					<th></th>

				</thead>";


					/*echo "<tr class='alternate'>
						<th class='check-column'><input type='checkbox' value='$group->id' name='delete_groups[]' /></th>
						<td>$group->name</td>
						<td></td>
						<td>$group->description</td>
						<td><a href='$up_link'>&#x2191;</a> | <a href='$down_link'>&#x2193;</a></td>
						<td><strong><a href='$edit_link'>".__("Modify", "wpforum")."</a></strong></td>
					</tr>";*/
				
		$forums = $wpforum->get_forums($group->id);
		
		if($forums){
			foreach($forums as $forum){
				$up_link 	= ADMIN_BASE_URL."structure&do=forum_up&id=$forum->id";
				$down_link 	= ADMIN_BASE_URL."structure&do=forum_down&id=$forum->id";
				$edit_link	= ADMIN_BASE_URL."structure&do=editforum&forumid=$forum->id";

				echo "<tr>
						<th class='check-column'><input type='checkbox' value='$forum->id' name='delete_forums[]' /></th>
						<td> -- ".stripslashes($forum->name)."</td>
						<th nowrap><a href='$up_link'>&#x2191;</a> | <a href='$down_link'>&#x2193;</a></th>
						<td>".stripslashes($forum->description)."</td>
						<td><a href='$edit_link'>".__("Modify", "wpforum")."</a></td>
					</tr>";



			} // foreach($forums as $forum)
		} // if($forums)
		echo "	<tr>
					<td colspan='5' align='right'><a href='".ADMIN_BASE_URL."structure&do=addforum&groupid=$group->id'>".__("Add forum", "wpforum")."</a></td>
				</tr>
			</table><br class='clear' />";
	
	} // foreach($groups as $group)
	


	echo "</form></div>";
	
}
	function move_up_down(){
		if(isset($_GET['do'])){
		global $wpdb, $table_prefix;
			switch($_GET['do']){
			
/*------------------------------------------------------------------------------------------------------------------------*/				
				case "group_down":
					$ginfo = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_groups WHERE id = '".($_GET['id']*1)."'", ARRAY_A);
					$above = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_groups WHERE sort < '".$ginfo['sort']."' ORDER BY sort DESC", ARRAY_A);
					if ($above['id']>0){
						$wpdb->query("UPDATE {$table_prefix}forum_groups SET sort = '".$above['sort']."' WHERE id = '".($_GET['id']*1)."'");
						$wpdb->query("UPDATE {$table_prefix}forum_groups SET sort = '".$ginfo['sort']."' WHERE id = '".$above['id']."'");
					}
				$msg = "Group Moved Down";
				break;
/*------------------------------------------------------------------------------------------------------------------------*/				
				case "forum_down":
					$ginfo = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_forums WHERE id = '".($_GET['id']*1)."'", ARRAY_A);
					$above = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_forums WHERE parent_id = '".$ginfo['parent_id']."' && sort < '".$ginfo['sort']."' ORDER BY sort DESC", ARRAY_A);
					if ($above['id']>0){
						$wpdb->query("UPDATE {$table_prefix}forum_forums SET sort = '".$above['sort']."' WHERE id = '".($_GET['id']*1)."'");
						$wpdb->query("UPDATE {$table_prefix}forum_forums SET sort = '".$ginfo['sort']."' WHERE id = '".$above['id']."'");
					}
					$msg = "Forum Moved Down";
					break;
/*------------------------------------------------------------------------------------------------------------------------*/						
				case "group_up":
					$ginfo = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_groups WHERE id = '".($_GET['id']*1)."'", ARRAY_A);
					$above = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_groups WHERE sort > '".$ginfo['sort']."' ORDER BY sort ASC", ARRAY_A);
					if ($above['id']>0){
						$wpdb->query("UPDATE {$table_prefix}forum_groups SET sort = '".$above['sort']."' WHERE id = '".($_GET['id']*1)."'");
						$wpdb->query("UPDATE {$table_prefix}forum_groups SET sort = '".$ginfo['sort']."' WHERE id = '".$above['id']."'");
					}
					$msg = "Group Moved Up";
					break;
/*------------------------------------------------------------------------------------------------------------------------*/				
				case "forum_up":
					$ginfo = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_forums WHERE id = '".($_GET['id']*1)."'", ARRAY_A);
					$above = $wpdb->get_row("SELECT * FROM {$table_prefix}forum_forums WHERE parent_id = '".$ginfo['parent_id']."' && sort > '".$ginfo['sort']."' ORDER BY sort ASC", ARRAY_A);
					if ($above['id']>0){
						$wpdb->query("UPDATE {$table_prefix}forum_forums SET sort = '".$above['sort']."' WHERE id = '".($_GET['id']*1)."'");
						$wpdb->query("UPDATE {$table_prefix}forum_forums SET sort = '".$ginfo['sort']."' WHERE id = '".$above['id']."'");
					}
					$msg = "Forum Moved Up";
					break;
/*------------------------------------------------------------------------------------------------------------------------*/				
				}
				return $msg;

			}
			return false;
		}
		function update_usergroups($new_groups, $group_id){
			global $wpdb, $table_prefix;
			$new_groups = maybe_serialize($new_groups);
			$wpdb->query("UPDATE ".$table_prefix."forum_groups SET usergroups = '$new_groups' WHERE id = $group_id");
		}
		
		function get_usersgroups_with_access_to_group($groupid){
			global $wpdb, $table_prefix;
			$string = $wpdb->get_var("select usergroups from ".$table_prefix."forum_groups where id = $groupid");
			return  maybe_unserialize( $string );

		}
		
		function edit_moderator(){
			if(isset($_POST['update_mod'])){
				
				$forums = $_POST['mod_forum_id'];
				$forums = maybe_unserialize($forums);
					
				$global = $_POST['mod_global'];
				$user_id = $_POST['update_mod_user_id'];
				if($global){
					update_usermeta($user_id, "wpf_moderator", "mod_global");
				}
				else
					update_usermeta($user_id, "wpf_moderator", $forums);

				/*echo $global." ".$_POST['update_mod_user_id'];*/
				$forums = maybe_serialize($forums);
				//$this->pre($forums);

				if(empty($forums))
					return __('Moderator successfully removed.', 'wpforum');
				else
					return __('Moderator successfully saved.', 'wpforum');
			}
			if(isset($_POST['delete_mod'])){
					$user_id = $_POST['update_mod_user_id'];
					if(delete_usermeta($user_id, "wpf_moderator"))
						return __('Moderator successfully removed.', 'wpforum');
					else
						return __('Moderator NOT removed.', 'wpforum');

			}

			return false;
		}
		function add_moderator(){
			if(isset($_POST['add_mod_submit'])){
				global $wpdb, $table_prefix;
				$user_id = $_POST['addmod_user_id'];
				$mod_forum_id = $_POST['mod_forum_id'];
				$global = $_POST['mod_global'];
				if($user_id == "add_mod_null")
					return __("You must select a user", "wpforum");
					
				if($global){
					update_usermeta($user_id, "wpf_moderator", "mod_global");
					return __("Global Moderator added successfully", "wpforum");
				}
				update_usermeta($user_id, "wpf_moderator", $mod_forum_id);
				return __("Moderator added successfully", "wpforum");
			}
			return false;
			
		}
		function moderators(){
			global $wpdb, $table_prefix, $wpforum;
			$users = get_users_of_blog();

			$forums = $wpforum->get_forums();
			$groups = $wpforum->get_groups();
			
			if($msg = $this->edit_moderator())
				echo "<div id='message' class='updated fade'><p>$msg</p></div>";
				
			if($msg = $this->add_moderator())
				echo "<div id='message' class='updated fade'><p>$msg</p></div>";
			echo "<div class='wrap'>";
			
			if(isset($_GET['do']) && $_GET['do'] == "add_moderator"){
				include('wpf-moderator.php');
			}
			$mods = $wpforum->get_moderators();

			echo "<h2>".__("Manage Moderators", "wpforum")." <a href='".ADMIN_BASE_URL."moderators&do=add_moderator'>(".__("add new", "wpforum").")</a></h2>";

			if($mods){

				foreach($mods as $mod){
					echo "<form name='update_mod_form-$mod->id' action='".ADMIN_BASE_URL."moderators' method='post'>
					<table class='widefat''>
						<thead>
							<tr>
								<th>$mod->user_login</th>
								<th>".__("Currently moderating", "wpforum")."</th>
							</tr>
						</thead>
							<tr>
								<td><input type='submit' name='update_mod' value='".__("Update", "wpforum")."' /><br />
								<input type='submit' name='delete_mod' value='".__("Remove", "wpforum")."' />
								</td>
								<td>";
								if(get_usermeta($mod->user_id, "wpf_moderator") == "mod_global")
									$global_checked = "checked='checked'";
								else
									$global_checked = "";

				echo "<p class='wpf-alignright'
					><input type='checkbox' onclick='invertAll(this, this.form, \"mod_forum_id\");' name='mod_global' id='mod_global' $global_checked value='mod_global'/> <strong>".__("Global moderator: (User can moderate all forums)", "wpforum")."</strong></p>";
						foreach($groups as $group){
							$forums = $wpforum->get_forums($group->id);
							echo "<p class='wpf-bordertop'><strong>".stripslashes($group->name)."</strong></p>";
							foreach($forums as $forum){
									if($wpforum->is_moderator($mod->user_id, $forum->id))
										$checked = "checked='checked'";
									else
										$checked = "";
										
									echo "<p class='wpf-indent'><input type='checkbox' onclick='uncheckglobal(this, this.form);' $checked name='mod_forum_id[]' id='mod_forum_id' value='$forum->id' /> $forum->name</p>
											<input type='hidden' name='update_mod_user_id' value='$mod->user_id' />";
							}
							
						}
						echo "</td>
							</tr>
							</form></table><br class='clear' />";
					}				
			}
			else
				echo "<p>".__("No moderators yet", "wpforum")."</p>";
			echo "</div>";

		}
		
		
		function convert_moderators(){
			global $wpdb, $table_prefix;
			if(!get_option('wpf_mod_option_vers')){
				$mods = $wpdb->get_results("SELECT user_id, user_login, meta_value FROM $wpdb->usermeta 
					INNER JOIN $wpdb->users ON $wpdb->usermeta.user_id=$wpdb->users.ID WHERE meta_key = 'moderator' AND meta_value <> ''");
				echo "<pre>";
				print_r($mods);
				echo "</pre>";
				
				foreach($mods as $mod){
					$string = explode(",", substr_replace($mod->meta_value, "", 0, 1));
				
					/*echo "<pre>";
					print_r($string);
					echo "</pre>";*/
					update_usermeta($mod->user_id, 'wpf_moderator', maybe_serialize($string));
					update_option('wpf_mod_option_vers', '2');
				}
			}
			else echo "Moderators updated";
		
		}
		
		
		function pre($array){
			echo "<pre>";
			print_r($array);
			echo "</pre";
		}

		
		
		
		
		
}// End class

// Startup the stuff
$wpfa = new wpforumadmin();












?>