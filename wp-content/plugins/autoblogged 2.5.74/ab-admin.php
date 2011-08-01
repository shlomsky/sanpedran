<?PHP
/*
AutoBlogged ab-admin.php

*/


//--------------------------------------------------------------------------------------------------
// Admin options page

function ab_adminPageHeader(){

	global $wpdb, $feedtypes, $ab_options, $autoblogged;
	if (!current_user_can('manage_options')) {
		die(__('Warning: Access denied.'));
	}

////// Debug code
//echo '<style type="text/css">.divHide{display:none;}.divShow{display:block;}</style>';
//					echo '<script type="text/javascript" language="javascript">function showhide(obj) {';
//					echo 'var el=document.getElementById(obj);';
//					echo '	if (el.className == "divHide") {';
//					echo '	el.className = "divShow";';
//					echo '	} else {';
//					echo '	el.className = "divHide";';
//					echo '	}}</script>';
//echo '&nbsp;&nbsp;<a href="#debug1" onclick="showhide(\'debugdiv\')"><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/plus.png" />&nbsp;debug info</a>';
//echo '<div id="debugdiv" class="divHide"><pre>';
//echo "$_POST: ";
//print_r($_POST);
//echo "$_GET: ";
//print_r($_GET);
//echo '</pre></div>';
/////

//echo get_current_theme();


	// First do DB version check
	$installed_ver = get_option( "autoblogged_db_version" );
	
	if ($installed_ver != DB_SCHEMA_AB_VERSION) {
		ab_installOnActivation();
	}
	
	

	// Check to see if we are disabling or enabling a feed
	if ($_GET['action'] == 'enable' && isset($_REQUEST['_fid'])) {
		$sql ='UPDATE ' . ab_tableName() . ' SET `enabled`=1 WHERE id='.$wpdb->escape($_GET['_fid']).';';
		$ret = $wpdb->query($sql);
	}

	if ($_GET['action'] == 'disable' && isset($_REQUEST['_fid'])) {
		$sql ='UPDATE ' . ab_tableName() . ' SET `enabled`=0 WHERE id='.$wpdb->escape($_GET['_fid']).';';
		$ret = $wpdb->query($sql);
	}

	if ($_GET['action'] == 'run') check_admin_referer('autoblogged-nav');

	// Save any options submitted via a form post
	if (array_key_exists('_submit_check', $_POST) || ( $_GET['action'] == 'add')) {
		ab_validateFormInput();

		// Handle feed edits
		if (isset($_POST['_fid']) || $_GET['action'] == 'add') {

			// Special handling for checkboxes
			$_POST['addothercats'] = intval(isset($_POST['addothercats']));
			$_POST['addcatsastags'] = intval(isset($_POST['addcatsastags']));
			$_POST['saveimages'] = intval(isset($_POST['saveimages']));
			$_POST['createthumbs'] = intval(isset($_POST['createthumbs']));
			$_POST['usepostcats'] = intval(isset($_POST['usepostcats']));
			$_POST['addpostcats'] = intval(isset($_POST['addpostcats']));
			$_POST['usefeeddate'] = intval(isset($_POST['usefeeddate']));

			// Special handling for feed type to get the ID
			if (isset($_POST['type'])) {
				$_POST['type'] = array_search($_POST['type'], $feedtypes);
			}

			// Special handling for tags
			if (isset($_POST['tags_input'])) {
				$_POST['tags'] = explode(',', $_POST['tags_input']);
			}

			// Special handling for categories
			if (isset($_POST['post_category'])) {
				$_POST['category'] = $_POST['post_category'];
			}


			// Extra post stuff we won't be saving in the DB
			unset($_POST['post_category'], $_POST['tags_input'], $_POST['newtag'], $_POST['newcat'], $_POST['newcat_parent']);

			// Insert new record or update existing record
			if (empty($_POST['_fid'])) {
				unset($_POST['_fid']);
				$sql = "INSERT INTO " . ab_tableName();
			} else {
				$sql = "UPDATE " . ab_tableName();
			}

			$i=0;
			$sql .= " SET ";
			foreach (array_keys($_POST) as $postitem) {
				if (substr($postitem, 0, 1)<> '_') {
					$i++;
					if ($i > 1) {
						$sql .=',';
					}
					if (is_array($_POST[$postitem])) {
						$_POST[$postitem] = ab_arrayEncode($_POST[$postitem]);
						$_POST[$postitem] =ab_serialize($_POST[$postitem]);
					}
					$sql .= ' '.$postitem."='". $wpdb->escape($_POST[$postitem])."'";
				} // endif
			} // end foreach

			if (isset($_POST['_fid'])) {
				$sql .= " WHERE id=" . $wpdb->escape($_POST['_fid']).";";
			}
			$ret = $wpdb->query($sql);
			if ($ret == 0) echo '<!--'.$sql.'-->';

			// Handle other page updates
		} else {

			// Handle checkboxes and other special items for each page
			if ($_GET['p'] == 'Settings') {
				$_POST['running'] = intval(isset($_POST['running']));
				$_POST['uselinkinfo'] = intval(isset($_POST['uselinkinfo']));
				$_POST['useauthorinfo'] = intval(isset($_POST['useauthorinfo']));
				$_POST['updatecheck'] = intval(isset($_POST['updatecheck']));
			}

			if ($_GET['p'] == 'Tag Options') {
				$_POST['feedtags'] = intval(isset($_POST['feedtags']));
				$_POST['posttags'] = intval(isset($_POST['posttags']));
				$_POST['yahootags'] = intval(isset($_POST['yahootags']));
				$_POST['taggingengine'] = intval(isset($_POST['taggingengine']));
				if (isset($_POST['tags_input'])) $_POST['tags'] = explode(',', $_POST['tags_input']);
			}

			if ($_GET['p'] == 'Filtering') {
				$_POST['filterbytitle'] = intval(isset($_POST['filterbytitle']));
				$_POST['filterbylink'] = intval(isset($_POST['filterbylink']));
				$_POST['skipcaps'] = intval(isset($_POST['skipcaps']));
				$_POST['skipmultiplepunctuation'] = intval(isset($_POST['skipmultiplepunctuation']));
			}
			
			if ($_GET['p'] == 'Support') {
				$_POST['logging'] = intval(isset($_POST['logging']));
				$_POST['showdebug'] = intval(isset($_POST['showdebug']));
			}


			foreach (array_keys($_POST) as $postitem) {
				if (substr($postitem, 0, 1) <> '_') {
					if (is_array($_POST[$postitem])) {
						$_POST[$postitem] = ab_arrayEncode($_POST[$postitem]);
						$_POST[$postitem] =ab_serialize($_POST[$postitem]);
					}
					$ab_options[$postitem] = $_POST[$postitem];
				}
			} // foreach
		} // endif
	} // endif

	
	// ANTIPIRACY NOTICE
	
	// Note that we do not spend our valuable development resources trying to come up with a 
	// foolproof antipiracy scheme. We would rather spend that time adding new features or
	// improving the quality of our software and we would rather provide our customers with 
	// full access to unencrypted source code. 
  //
  // We realize that this will make it easier to bypass any restrictions we have in place
  // to use this software without paying or distribute it to others. We don't expect to stop  
  // piracy of our software but we do ask that if you are using an unlicensed copy that you at
  // least give back in some way through a link to our site, a review on a blog or forum 
  // somewhere, offer to do beta testing for us, write up a tutorial, or sign up for 
  // our affiliate program and generate some sales for us.
  //
  // Of course, we do prefer that you always purchase a license and in exchange you will get
  // excellent support, access to our customer forums, and we will always make sure you are
  // up-to-date with the latest release. 
  //
  // To encourage honesty, integrity, and fairness we are offering the coupon code NONPIRATED 
  // that you can use at checkout to get $10 off a single site license or $30 off an unlimited 
  // sites license. Please share this coupon code with anyone you want, especially if you 
  // are distributing unauthorized copies of our software!
  
	
	// Make sure they have entered their serial number as a soft nag, all features still enabled
	if ($_GET['action'] <> 'doreg') {
		if (strlen($ab_options['sn']) == 0) {
			if (function_exists('wp_nonce_url')) {
				$settingslink = wp_nonce_url($_SERVER['PHP_SELF'].'?page=AutoBloggedSettings', 'autoblogged-nav');
				$settingslink .= '&amp;p=Settings&amp;action=doreg';
			}
		}
	}

	// Check for updates
	if ($ab_options['updatecheck']) {
		// Only check once every 12 hours
		if (time() > $ab_options['last_update_check'] + (43200)) {
			$ab_options['last_update_check'] =  time();
			
			$result = ab_httpFetch('http://nick-parker.com/version.htm?regnum='.urlencode($ab_options['sn']));
			if (!$result['error']) {
				if (version_compare($result['contents'], AB_VERSION, ">")) {
					echo '<div id="update-nag">AutoBlogged version '.$result['contents'].' is now available. You can get this update using your original download link.</div>';
				}
			}
		}
	}
	
	ab_saveOptions();

	// Check permissions on _debug.log file
	if ($_GET['p'] == 'Support' && $ab_options['showdebug'] == true) {	
		$fp = @fopen(dirname(__FILE__).'/_debug.log', 'a');
		if (!$fp) {
			echo '<div id="sn-warning" class="updated fade"><p><strong>'.__("Error: ").'</strong>AutoBlogged cannot write to or create the file _debug.log. Check the permissions on the AutoBlogged plugin directory.</div>';
		}
	}
	
	// Admin options page header
	echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-includes/js/thickbox/thickbox.css" type="text/css" media="all" /> ';
	echo '<link rel="stylesheet" type="text/css" href="'.ab_pluginURL().'/admin.css" />'."\r\n";
	
	?>

	<SCRIPT language="JavaScript">
		<!--
		function d(delurl)
		{ if (confirm("Do you really want to delete this feed?")== true) { window.location=delurl; }}
		//-->
	</SCRIPT>
	<?PHP

} // end function

//--------------------------------------------------------------------------------------------------
// Feeds summary admin page
function ab_showFeedsPage()
{
	global $wpdb, $ab_options, $feedtypes;
	
	ab_adminPageHeader();
	
	// First check to see if we are deleting a feed
	if ($_GET['action'] == 'del' && isset($_REQUEST['_fid'])) {
		$sql = 'DELETE FROM '.ab_tableName().' WHERE id='.$wpdb->escape($_GET['_fid']).' LIMIT 1;';
		$ret = $wpdb->query($sql);
	}
	
	// Load feeds list from DB
	$sql = "SELECT id, title, type, url, enabled FROM " . ab_tableName() .';';

	$feeds = $wpdb->get_results($sql, 'ARRAY_A');
	$categories = get_categories('orderby=name&hide_empty=0');
	echo '<div class="wrap"><h2>Source Feeds</h2>';
	echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
	
	$sidebars = array('Tag Options' => 'ab_MetaFeedsPageSidebar', 'Links' => 'ab_MetaLinksSidebar');
	
	ab_doSideBar($sidebars, 'autoblogged-feeds');
	ab_OpenMainSection();


	if (function_exists('wp_nonce_url')) $baselink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-feeds-edit');
	if (sizeof($feeds) > 0) {
		// Loop through each feed
		foreach ($feeds as $feed) {
			$feed = ab_arrayStripSlashes($feed);
			echo '<div class="feedswrap">';

			if (empty($feed['title'])) {
				$feedurl = $feed['url'];
				if (strlen($feedurl) > 40) $feedurl = substr($feed['url'], 0, 40).'...';
				if ($feed['type'] > 1) {
					$feedtitle = $feedurl ;
				} else {
					$feedtitle = $feedurl;
				}
			} else {
				$feedtitle = $feed['title'];
			}
			


			echo '<div class="feedheader"> <div class="'.strtolower(str_replace("!", "", str_replace(" ", "", $feedtypes[$feed['type']]))).'">&nbsp;'.$feedtypes[$feed['type']].'&nbsp;</div>';
			if (!$feed['enabled']) {
				echo '&nbsp;<font color="gray"><span style="text-decoration: line-through;">'.$feedtitle.'</span></font>';
			} else {
				echo '&nbsp;&nbsp;'.$feedtitle.'';
			}

			echo '<div class="feedurl"><a href="'.ab_getFeedURL($feed['type'], $feed['url'], 255).'" target="_blank" style="text-decoration: none; font-size: 7pt;">'.ab_getFeedURL($feed['type'], $feed['url'], 95).'</a></div>';
			if ($feed['enabled']) {
				$action = "disable";
			} else {
				$action = "enable";
			}

			echo '<div class="feedurl"><a href="'.$baselink.'&amp;p=&amp;action='.$action.'&_fid='.$feed['id'].'"><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/'.$action.'.png" >&nbsp;'.ucfirst($action).' this feed</a>&nbsp;';
			echo '| <a href="#" onclick="d(\''.$baselink.'&amp;p=Feeds&_fid='.$feed['id'].'&amp;action=del\')">Delete feed</a><br/></div></div>';

			echo '<div class="editfeed"><a href="'.$baselink.'&amp;action=edit&_fid='.$feed['id'].'"><img style="vertical-align: middle;" alt="Edit..." src="'.ab_pluginURL().'/img/bedit.png"></a> ';

			echo '&nbsp;&nbsp;<a target="_blank" href="http://hiderefer.com/?http://viewer.autoblogged.com/?feed='.urlencode(ab_getFeedURL($feed['type'], $feed['url'])).'&type=htm&amp;TB_iframe=true&amp;height=600&width=800" class="thickbox"><img style="vertical-align: middle;" alt="Feed Viewer" src="'.ab_pluginURL().'/img/bview.png"></a> ';

			echo '&nbsp;<a href="'.$baselink.'&amp;p=&amp;action=run&_fid='.$feed['id'].'"><img style="vertical-align: middle;" alt="Process Now" src="'.ab_pluginURL().'/img/bprocess.png" /></a>&nbsp;';
			echo '<br/></div></div>';
		} // foreach
		echo '<br/><br/><br/><br/>';
	} else {
		echo '<div class="feedswrap" "><div ><div class="feedheader">Getting Started</div><font color="gray">You currently do not have any feeds set up. AutoBlogged Automatically adds posts to your blog based on the RSS feeds or searches you set up. To get started adding your first feed, <a href="'.$baselink.'&amp;action=edit">click here</a><br /><br /></font></div></div>';
	} //end if		
	
	echo '</div></div></div></div></div></div>';
}

function ab_MetaFeedsPageSidebar() 
{
	global $ab_options;
		if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');

		echo '<div id="feedssidebar">';
		echo '<div id="major-publishing-actions">';
		echo '<div id="previewview"><a href="'.$navlink.'&amp;action=edit"><img src="'.ab_pluginURL().'/img/add.png"/> Add New Feed</a></div>';
		echo '</div><br />';
		
		echo '<a href="'.$navlink.'&amp;p=&amp;action=run" ><img src="'.ab_pluginURL().'/img/processall.png" />&nbsp;&nbsp;Process all feeds now</a><br />';
		echo '<a href="'.$navlink.'&amp;p=&amp;action=run&preview=1"><img src="'.ab_pluginURL().'/img/preview.png" />&nbsp;&nbsp;Preview feed processing</a><br /><br />';
		echo '<p class="curtime">Current server time: <b>'.date("g:i a").'</b><br />';
		echo 'Feeds last processed: <b>'.date("g:i a", (int)$ab_options['lastupdate']).'</b><br />';
		echo 'Next scheduled run: <b>'.date("g:i a", (int)$ab_options['lastupdate'] + (int)($ab_options['interval'] * 60)).'</b></p><br />';
		
		echo '<p class="submit">';
		echo 'AutoBlogged Version: '. AB_VERSION;	
		echo '<!-- Theme: '. get_current_theme(). '-->';
		echo '</p></div>';

}



//--------------------------------------------------------------------------------------------------
// Edit feed admin options page
function ab_showEditFeedPage()
{
	global $wpdb;
	global $feedtypes;
	
	ab_adminPageHeader();
	
	
	if (array_key_exists('_submit_check', $_POST)) check_admin_referer('autoblogged-feeds-edit');
	if (empty($_REQUEST['_fid'])) {
		
		// Load defaults if we are adding a new feed
		require(ab_plugin_dir().'/defaults.php');
		$feeds = Array();
		$feeds[] = Array();
		$feeds[0] = array("id" => '',
		"enabled" => $enabled,
		"type" => $feed_type,
		"url" => $keywords_or_feed_url,
		"title" => $title,
		"poststatus" => $default_status,
		"category" => $assign_posts_to_this_category,
		"addothercats" => $add_additional_categories,
		"addcatsastags" => $add_categories_as_tags,
		"tags" => $additional_tags,
		"saveimages" => $save_full_images,
		"createthumbs" => $create_thumbnails,
		"playerwidth" => $video_width,
		"playerheight" => $video_height,
		"includeallwords" => $all_these_words,
		"includeanywords" => $any_of_these_words,
		"includephrase" => $the_exact_phrase,
		"includenowords" => $none_of_these_words,
		"customfield" => $custom_fields,
		"customfieldvalue" => $custom_values,
		"templates" => $feed_post_template,
		"searchfor" => $search_for_patterns,
		"replacewith" => $replace_with_patterns,
		"uselinkinfo" => $use_link_info,
		"useauthorinfo" => $use_author_info,
		"customplayer" => $custom_player_url,
		"randomcats" => $randomly_add_selected_categories,
		"usepostcats" => $use_categories_from_original,
		"addpostcats" => $add_categories_from_original,
		"author" => $author,
		"alt_author" => $alternate_author_if_doesnt_exist,
		"schedule" => $feed_processing_schedule,
		"updatefrequency" => $feed_processing_every_x_updates,
		"post_processing" => $post_processing,
		"max_posts" => $max_posts_per_update,
		"posts_ratio" => $randomly_include_x_percent_of_posts,
		"last_updated" => '',
		"update_countdown" => '',
		"last_ping" => '',
		"usefeeddate" => $use_date_from_feed,
		);	

	} else {
		// Load the specified feed
		$sql = "SELECT * FROM " . ab_tableName() .' WHERE id='.$wpdb->escape($_REQUEST['_fid']).';';
		$feeds = $wpdb->get_results($sql, 'ARRAY_A');

	}
	$categories = Array();
	$blogcategories = get_categories('orderby=name&hide_empty=0');
	foreach ($blogcategories as $cat) {
		$categories[] = $cat->cat_name;
	}


	// There should only be one feed in this loop
	foreach ($feeds as $feed) {
		echo '<div class="wrap"><h2>Feed Settings</h2>';
		echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
		
		if (empty($_GET['_fid'])) {
			echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Feeds&amp;action=add" method="post">';
		} else {
			echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Feeds&_fid='.$feed['id'].'" method="post">';
			$feed = ab_arrayStripSlashes($feed);
		}
		

		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-feeds-edit');
		echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;
		echo '<input type="hidden" name="_fid" value="'.$feed['id'].'"/>';
		echo '<input type="hidden" name="enabled" value="'.$feed['enabled'].'"/>';

	  $sidebars = array('Feed' => 'ab_MetaEditFeedSidebar', 'Links' => 'ab_MetaLinksSidebar');
	
		ab_doSideBar($sidebars, 'autoblogged-feeds', $feed);
		ab_OpenMainSection();
		
		// General Settings
		echo ab_makeBoxStart("General Settings");
		echo '<table class="editform">';
		echo ab_makeSelect('type', $feedtypes, $feedtypes[$feed['type']], 'Feed Type', '');
		echo ab_makeTextInput('url', htmlentities($feed['url']), 60, 'Search Keywords<br />or feed URL', 'If Feed Type is RSS Feed, enter the feed URL here, otherwise enter search keywords.');
		echo '<tr><td>&nbsp;</td></tr>';
		echo ab_makeTextInput('title', $feed['title'], 60, 'Title (Optional)', 'This is an optional name you can assign to help manage your feeds. Leave blank to have a title automatically assigned.');
		echo ab_makeSelect('poststatus', Array("publish", "pending", "draft", "private"), $feed['poststatus'], 'Status for new posts', '');
		echo ab_makeCheckBox('usefeeddate', $feed['usefeeddate'], "Post date", "Use original post date", '');
		echo '</table>';
		echo ab_makeBoxClose();

		// Post processing
		echo ab_makeBoxStart("Feed Processing");
		echo '<table>';
		$options = array('With every scheduled update',
		'After every <input name="updatefrequency" type="text" style="width: 30px" value="'.stripslashes(attribute_escape($feed['updatefrequency'])).'"/>&nbsp;scheduled updates',
		'Manually or when notified via XML-RPC ping');
		echo '<tr valign="top"><td>Process this feed:</td><td>';
		echo ab_makeRadioOnly('schedule', $feed['schedule'], $options, '');
		echo '</td></tr><tr><td>&nbsp;</td></tr><tr valign="top"><td>Post processing:&nbsp;&nbsp;</td><td>';
		$options = array('Include all posts',
		'Only include the first <input name="max_posts" type="text" style="width: 30px" value="'.stripslashes(attribute_escape($feed['max_posts'])).'"/> posts',
		'Randomly include <input name="posts_ratio" type="text" style="width: 40px" value="'.stripslashes(attribute_escape($feed['posts_ratio'])).'"/></label>% of all posts');
		echo ab_makeRadioOnly('post_processing', $feed['post_processing'], $options, '');
		echo '</tr></td></table>'.ab_makeBoxClose();

		// Tags
		echo ab_makeBoxStart("Tags");;
		echo '<p>AutoBlogged will randomly add one or more of the following tags to each post in this feed:</p>';

		$tags = ab_unserialize($feed['tags']);
		if (!is_array($tags)) $tags=array();
//		echo '<p id="jaxtag"><input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="'.implode(",", $tags).'" /></p>';
//		echo '<div id="tagchecklist"></div>';
		?>
		
		<div class="tagsdiv" id="post_tag">
			<div class="jaxtag">
				<div class="nojs-tags hide-if-js">
					<p>Add or remove tags</p>
					<textarea name="tags_input" class="the-tags" id="tags_input"><?PHP echo implode(",", $tags); ?></textarea>
				</div>

				<span class="ajaxtag hide-if-no-js">
					<label class="screen-reader-text" for="new-tag-post_tag">Post Tags</label>
					<input type="text" id="new-tag-post_tag" name="newtag[post_tag]" class="newtag form-input-tip" size="16" autocomplete="off" value="Add new tag" />
					<input type="button" class="button tagadd" value="Add" tabindex="3" />
				</span>
			</div>
			<p class="howto">Separate tags with commas.</p>
			<div class="tagchecklist">
			</div>
		</div>

		<p class="tagcloud-link hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-post_tag">Choose from the most used tags in Post Tags</a></p>

		
		<?php
		echo 'See also: <a href="'.$navlink.'&amp;p=Tag Options" target-"_blank">'.$pages['Tag Options'].'Global tag options</a>';
		echo ab_makeBoxClose();

		// Categories
		echo ab_makeBoxStart("Categories", "categorydiv");
		echo 'Assign posts to these categories:';


		?>
		
		<ul id="category-tabs">
		<li class="tabs"><a href="#categories-all" tabindex="3"><?php _e( 'All Categories' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#categories-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
		</ul>
		
		<div id="categories-pop" class="tabs-panel" style="display: none;">
			<ul id="categorychecklist-pop" class="categorychecklist form-no-clear" >
		<?php $popular_ids = wp_popular_terms_checklist('category'); ?>
			</ul>
		</div>
		
		<div id="categories-all" class="tabs-panel">
			<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
				
				
				
						<?php
				if (function_exists('wp_category_checklist')) {
					wp_category_checklist('', false, ab_unserialize($feed['category']), $popular_ids);
				} else {
					global $checked_categories;
					$cats = array();
					$checked_categories = ab_unserialize($feed['category']);
					dropdown_categories();
				}
				?>
				
				
				
				
			</ul>
		</div>
		
		<?php if ( current_user_can('manage_categories') ) : ?>
		<div id="category-adder" class="wp-hidden-children">
			<h4><a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a></h4>
			<p id="category-add" class="wp-hidden-child">
			<label class="screen-reader-text" for="newcat"><?php _e( 'Add New Category' ); ?></label><input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php esc_attr_e( 'New category name' ); ?>" tabindex="3" aria-required="true"/>
			<label class="screen-reader-text" for="newcat_parent"><?php _e('Parent category'); ?>:</label><?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
			<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php esc_attr_e( 'Add' ); ?>" tabindex="3" />
		<?php	wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
			<span id="category-ajax-response"></span></p>
		</div>
		<?php
		endif;
		?>


			<?PHP
			echo '<table><tr><td>&nbsp;</td></tr>';
			echo '<tr valign="top"><td scope="row">With the selected categories:</td><td>';

			echo ab_makeRadioOnly('randomcats', $feed['randomcats'], array('Add all to each post', 'Randomly add one or more to each post'), '');

			echo '<tr><td>&nbsp;</td></tr>';

			echo '<tr valign="top"><td scope="row">If unselected blog categories<br/>appear in the post content:</td><td>';
			echo ab_makeCheckBoxOnly('addothercats', $feed['addothercats'], 'Also add them to the post', '').'<br />';
			echo ab_makeCheckBoxOnly('addcatsastags', $feed['addcatsastags'], 'Add them as post tags', '').'</td></tr>';
			echo '<tr><td colspan="2"><h2>&nbsp;</h2></td></tr>';

			echo '<tr valign="top"><td scope="row">With original feed categories:</td><td>';
			echo ab_makeCheckBoxOnly('usepostcats', $feed['usepostcats'], 'Include categories from the original post (if they exist)', '').'<br />';
			echo ab_makeCheckBoxOnly('addpostcats', $feed['addpostcats'], 'Add these categories to your blog if they don\'t exist', '').'</td></tr>';
			echo '<tr><td>&nbsp;</td></tr>';
			echo '</table>'.ab_makeBoxClose();

			// Authors
			$userlist =  array();
			$users = $wpdb->get_results("SELECT display_name FROM $wpdb->users ORDER BY display_name");
			if (is_array($users)) :
			foreach ($users as $user) :
			$userlist[] = $user->display_name;
			endforeach;
			endif;
			echo ab_makeBoxStart("Authors");
			echo '<table>';
			echo ab_makeSelect('author', array_merge(array(RANDOM_AUTHOR, AUTHOR_FROM_FEED), $userlist), $feed['author'], 'Set author for new posts','', '');
			echo ab_makeSelect('alt_author', array_merge(array('', ADD_AUTHOR, SKIP_POST, RANDOM_AUTHOR), $userlist), $feed['alt_author'], 'If using author from feed<br />and that author doesn\'t exist in blog','', true);
			echo '</td></tr></table><br /></div></div>';

			// Images
			echo ab_makeBoxStart("Images");
			echo '<table>';
			echo '<tr valign="top"><td scope="row">Image Options:</td><td>';
			echo ab_makeCheckBoxOnly('saveimages', $feed['saveimages'], 'Save local copies of all images in the feed', '').'<br />';
			echo ab_makeCheckBoxOnly('createthumbs', $feed['createthumbs'], 'Create local thumbnails for each image', 'Note that if you save full-size images locally autoblogged will always create thumbnails.').'</td></tr>';

			echo '</table><br />';
			echo '<a href="'.get_option('siteurl').'/wp-admin/options-media.php">Click here</a> to configure WordPress thumbnail settings.'.ab_makeBoxClose();

			// Video
			echo ab_makeBoxStart("Embedded Video Player");
			echo '<table>';
			echo ab_makeTextInput('playerwidth', $feed['playerwidth'], 10, 'Video width', '');
			echo ab_makeTextInput('playerheight', $feed['playerheight'], 10, 'Video height', '');
			echo '<tr><td>&nbsp;</td></tr>';
			echo ab_makeTextInput('customplayer', $feed['customplayer'], 50, 'Custom FLV player URL', 'You may specify a custom player URL for playing .flv and .mp3 files.');
			echo '</table>'.ab_makeBoxClose();

			// Filtering
			echo ab_makeBoxStart("Filtering", '', true);
			echo '<h4>Include posts that contain (separate words with commas):</h4>';
			echo '<table>';
			echo ab_makeTextInput('includeallwords', $feed['includeallwords'], 70, 'All these words', '');
			echo ab_makeTextInput('includeanywords', $feed['includeanywords'], 70, 'Any of these words', '');
			echo ab_makeTextInput('includephrase', $feed['includephrase'], 70, 'The exact phrase', '');
			echo ab_makeTextInput('includenowords', $feed['includenowords'], 70, 'None of these words', '');
			echo '</table>'.ab_makeBoxClose();
			echo '</div></div></div><br /><br /><div class="wrap"><div id="poststuff"><h2>Advanced Settings</h2>';

			// Custom Fields
			echo ab_makeBoxStart("Custom Fields", '', true);
			echo 'Set these additional custom fields to use in feed-specific or global post templates.';
			echo '<table>';
			echo ab_makeValuePairTable(array("Custom Field", "Custom Field Value"),ab_unserialize($feed['customfield']),ab_unserialize($feed['customfieldvalue']));
			echo '</table>'.ab_makeBoxClose();

			// Post Templates
			echo ab_makeBoxStart("Post Templates", '', true);
			echo 'Templates determine how the feed will appear in the blog posts. For more information on post templates, see the <a href="http://hiderefer.com/?http://autoblogged.com/online-help/advanced-usage/post-template-reference/" target="_blank">Template Reference</a>. You can also test your post templates using <a href="http://hiderefer.com/?http://autoblogged.com/docs/template-test.php" target="_blank">this tool</a><br />';
			echo '<table class="form-table">';
			echo '<tr><td colspan="2"><textarea name="templates" rows="20" style="width: 100%" >'.$feed['templates'].'</textarea></td></tr>';
			echo '</table>'.ab_makeBoxClose();

			// Search and Replace
			echo ab_makeBoxStart("Search and Replace", '', true);
			echo 'Here you can add any strings or regular expressions to search and replace in feed items before adding them as a post. You can use this to change words, replace affiliate IDs, rewrite URLs, fix invalid feeds, etc. Seach items may contain regular expressions and replace items may refer to search matches such as $1, $2, etc.';

			echo ab_makeValuePairTable(array("Search for", "Replace with"),ab_unserialize($feed['searchfor']),ab_unserialize($feed['replacewith']));
			echo '</table>'.ab_makeBoxClose();
		}
		echo '</table></form></div></div></div></div>';
	}
	
	function ab_MetaEditFeedSidebar($feed = '') 
	{
			global $ab_options;
			if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');
				echo '<div id="feedsidebar">';
				echo '<div id="major-publishing-actions">';
				if (!empty($_REQUEST['_fid'])) {
					echo '<a href="http://hiderefer.com/?http://viewer.autoblogged.com/?feed='.urlencode(ab_getFeedURL($feed['type'], $feed['url'])).'&type=htm&amp;TB_iframe=true&amp;height=600&width=800" class="thickbox" target="_blank">Feed Viewer</a>&nbsp;&nbsp;';
					echo '<a href="http://hiderefer.com/?http://viewer.autoblogged.com/?feed='.urlencode(ab_getFeedURL($feed['type'], $feed['url'])).'&type=xml&amp;TB_iframe=true&amp;height=600&width=800" class="thickbox" target="_blank">View Feed XML</a>';
				}

			echo '</div><br/><a href="'.$navlink.'&amp;p=&amp;action=run&_fid='.$feed['id'].'"><img src="'.ab_pluginURL().'/img/process.png" />&nbsp;&nbsp;Process this feed now</a><br />';
			echo '<a href="'.$navlink.'&amp;p=&amp;action=run&preview=1&_fid='.$feed['id'].'"><img src="'.ab_pluginURL().'/img/preview.png" />&nbsp;&nbsp;Preview this feed now</a><br />';
			echo '<a href="'.$navlink.'&amp;p=&amp;action=run"><img src="'.ab_pluginURL().'/img/processall.png" />&nbsp;&nbsp;Process all feeds now</a><br /><br />';
	
			if ($feed['enabled']) {
				$action = "disable";
			} else {
				$action = "enable";
			}
	
			echo '<a href="'.$navlink.'&amp;p=&amp;action='.$action.'&_fid='.$feed['id'].'"><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/'.$action.'.png" /> '.ucfirst($action).' this feed</a><br />';
			echo '<a href="#" onclick="d(\''.$baselink.'&amp;p=Feeds&_fid='.$feed['id'].'&amp;action=del\')"><img style="vertical-align: middle;"  src="'.ab_pluginURL().'/img/del.png" /> Delete this feed</a><br />';
	
			echo '<p class="curtime">Current server time: <b>'.date("g:i a").'</b><br />';
			echo 'Feed last processed: <b>'.date("g:i a", (int)$feed['last_updated']).'</b><br />';
			echo 'Next scheduled run: <b>'.date("g:i a", $ab_options['lastupdate'] + ($ab_options['interval'] * 100)).'</b></p><br />';
			echo '<div class="clear"></div><div id="major-publishing-actions">';
			echo '<input type="submit" name="_Submit" id="publish" value="Save Changes" tabindex="4" class="button-primary" />';
			echo '</div></div>';
			//echo '</div>';
		}
		
	function ab_MetaOptionPagesSidebar() {
		echo '<div id="major-publishing-actions">';
		echo '<div class="clear"></div><div id="major-publishing-actions">';
		echo '<input type="submit" name="_Submit" id="publish" value="Save Changes" tabindex="4" class="button-primary" />';
		echo '</div></div>';
	}

	//--------------------------------------------------------------------------------------------------
	function ab_showTagOptionsPage()
	{
		global $ab_options;

		ab_adminPageHeader();

	
		echo '<div class="wrap"><h2>Tag Options</h2>';
		echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
		
		if (array_key_exists('_submit_check', $_POST)) check_admin_referer('autoblogged-tag-options');

		echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Tag Options'.$feed['id'].'" method="post">';
		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-tag-options');
		echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;

		if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');

		
	
		$sidebars = array('Feeds' => 'ab_MetaOptionPagesSidebar', 'Links' => 'ab_MetaLinksSidebar');
		
		ab_doSideBar($sidebars, 'autoblogged-feeds');
		ab_OpenMainSection();


		// General Settings
		echo ab_makeBoxStart("General Settings");
		echo '<table>';
		echo ab_makeTextInput('mintaglen', $ab_options['mintaglen'], 10, 'Minimum Tag Length', '');
		echo ab_makeTextInput('maxtaglen', $ab_options['maxtaglen'], 10, 'Maximum Tag Length', '');
		echo ab_makeTextInput('maxtags', $ab_options['maxtags'], 10, 'Maximum Tags per Post', '');
		echo '</table>'.ab_makeBoxClose();

		// Tag Sources
		echo ab_makeBoxStart("Tag Sources");
		echo '<table>';
		echo '<tr valign="top"><td>Sources for post tags:</td><td>';
		echo ab_makeCheckBoxOnly('feedtags', $ab_options['feedtags'], 'Use original tags from feed', '').'<br />';
		echo ab_makeCheckBoxOnly('taggingengine', $ab_options['taggingengine'], 'Use internal tagging engine to add tags from content', '').'<br />';
		echo ab_makeCheckBoxOnly('posttags', $ab_options['posttags'], 'Visit source URL to extract additional tags', '').'<br />';
		echo ab_makeCheckBoxOnly('yahootags', $ab_options['yahootags'], 'Get tags using Yahoo! API (requires Application ID)', '').'</td></tr>';

		echo '<tr><td>&nbsp;</td></tr>';
		echo ab_makeTextInput('yahooappid', $ab_options['yahooappid'], 70, 'Yahoo! Application ID', 'If you don\'t have an application ID, you can get one <a target="_blank" href="http://developer.yahoo.com/wsregapp/index.php">here</a>');
		echo '</table>'.ab_makeBoxClose();

		// Tags
		echo ab_makeBoxStart("Tags");;
		echo '<p>AutoBlogged will randomly add one or more of the following tags to each post in this feed:</p>';

		$tags = $ab_options['tags'];

		if (!is_array($tags)) { 
			$tags=ab_unserialize($ab_options['tags']);
		} else {
			$tags = $ab_options['tags'];
		}

		?>
		
		<div class="tagsdiv" id="post_tag">
			<div class="jaxtag">
				<div class="nojs-tags hide-if-js">
					<p>Add or remove tags</p>
					<textarea name="tags_input" class="the-tags" id="tags_input"><?PHP echo implode(",", $tags); ?></textarea>
				</div>

				<span class="ajaxtag hide-if-no-js">
					<label class="screen-reader-text" for="new-tag-post_tag">Post Tags</label>
					<input type="text" id="new-tag-post_tag" name="newtag[post_tag]" class="newtag form-input-tip" size="16" autocomplete="off" value="Add new tag" />
					<input type="button" class="button tagadd" value="Add" tabindex="3" />
				</span>
			</div>
			<p class="howto">Separate tags with commas.</p>
			<div class="tagchecklist">
			</div>
		</div>

		<p class="tagcloud-link hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-post_tag">Choose from the most used tags in Post Tags</a></p>

		
		<?php
		
		echo ab_makeBoxClose();

		// Tag filtering
		echo ab_makeBoxStart("Tag Filtering");
		echo ab_makeWideTextArea('notags', $ab_options['notags'], 5, '', 'Do not use any of the following words as tags.  If you see tags appearing in your posts that you do not want, add those tags here', 'Add one per line or on a single line separated by commas. Note that this will not delete existing tags assigned to posts.').ab_makeBoxClose();
		echo '</table></form></div></div></div></div>';
	}

	//--------------------------------------------------------------------------------------------------
	function ab_showSettingsPage()
	{
		global $ab_options;


		ab_adminPageHeader();

		if ($_GET['action'] == 'purgecache') {
			$cachedir = ab_plugin_dir() . '/cache';
			chmod($cachedir, 0777);
				
	    if(!$dh = @opendir($cachedir)) {
	    	echo '<div id="message" class="updated fade"><p>Unable to purge RSS cache.</p></div>';
	    } else {
		    while (false !== ($obj = readdir($dh))) {
		        if($obj=='.' || $obj=='..' || $obj=='index.php') continue;

		        unlink($cachedir.'/'.$obj);
		    }
		    closedir($dh);
		    echo '<div id="message" class="updated fade"><p>RSS cache purged.</p></div>';
			}
			if (count(glob("*.spc"))) {
				echo '<div id="message" class="updated fade"><p>Unable to purge RSS cache.</p></div>';
			}
			chmod($cachedir, 0765);
		}
		
		echo '<div class="wrap"><h2>Settings</h2>';
		echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
		if (array_key_exists('_submit_check', $_POST)) check_admin_referer('autoblogged-settings');
		echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Settings'.$feed['id'].'" method="post">';
		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-settings');
		echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;

		if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');

		$sidebars = array('Settings' => 'ab_MetaOptionPagesSidebar', 'Links' => 'ab_MetaLinksSidebar');
		
		ab_doSideBar($sidebars, 'autoblogged-feeds');
		ab_OpenMainSection();


		// General Options
		echo ab_makeBoxStart("General Options");
		echo '<table>';
		echo ab_makeCheckBox('running', $ab_options['running'], 'AutoBlogged Enabled', 'Uncheck this if you wish to pause AutoBlogged.', '');
		echo ab_makeTextInput('mintime', $ab_options['mintime'], 10, 'Minimum time between updates', '(minutes)');
		echo ab_makeTextInput('maxtime', $ab_options['maxtime'], 10, 'Maximum time between updates', '(minutes)');
		echo '</table>'.ab_makeBoxClose();

		// Excerpts
		echo ab_makeBoxStart("Excerpts");
		echo '<table>';
		echo '<tr colspan="2"><td>When making excerpts:<br /> Randomly use between the first ';
		echo '<input name="minexcerptlen" type="text" size="3" value="'.stripslashes(attribute_escape($ab_options['minexcerptlen'])).'" /> and ';
		echo '<input name="maxexcerptlen" type="text" size="3" value="'.stripslashes(attribute_escape($ab_options['maxexcerptlen'])).'" />&nbsp;</td></tr>';
		echo '<tr><td>'.ab_makeRadioOnly('excerpt_type', $ab_options['excerpt_type'], array("Words", "Sentences", "Paragraphs"), '');
		echo '</td></tr></table>'.ab_makeBoxClose();

		// WordPress integration
		echo ab_makeBoxStart("WordPress Integration");
		echo '<table>';
		echo ab_makeCheckBox('uselinkinfo', $ab_options['uselinkinfo'], 'WordPress Links', 'Use stored link info if site already appears in links list.', '');
		echo ab_makeCheckBox('useauthorinfo', $ab_options['useauthorinfo'], 'WordPress Authors', 'Use stored author info if author is a registered user.', '');
		echo '</table>'.ab_makeBoxClose();

		// HTTP Options
		echo ab_makeBoxStart("HTTP Options");
		echo '<table>';
		echo ab_makeTextInput('referer', $ab_options['referer'], 40, 'HTTP Referrer', 'All requests made to external sites will use this as the <i>referer</i> string');
		echo ab_makeTextInput('useragent', $ab_options['useragent'], 40, 'HTTP User-Agent', 'All requests made to external sites will use this as the <i>User-Agent</i>. <a href="http://www.user-agents.org/">Click here</a> for a list of User-Agent strings.');
		echo '</table>'.ab_makeBoxClose();
		
		// RSS Options
		echo ab_makeBoxStart("RSS Retrieval Options");
		echo '<table>';
		echo ab_makeTextInput('rss_cache_timeout', $ab_options['rss_cache_timeout'], 10, 'Cache Timeout', 'Cache RSS requests for this many seconds.');
		if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');

		echo '<div id="previewview"><a href="'.$navlink.'&amp;action=purgecache">Purge RSS cache files now</a></div><br/>';
		echo '</table>'.ab_makeBoxClose();

		echo '</div></form></div></div></div>';
	}

	//--------------------------------------------------------------------------------------------------
	function ab_showFilteringPage()
	{
		global $ab_options;
		ab_adminPageHeader();

		echo '<div class="wrap"><h2>Filtering Options</h2>';
		echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
		
		if (array_key_exists('_submit_check', $_POST)) check_admin_referer('autoblogged-filtering');

		echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Filtering'.$feed['id'].'" method="post">';
		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-filtering');
		echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;

		if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page='. $_GET['page'], 'autoblogged-nav');
		
		$sidebars = array('Filtering' => 'ab_MetaOptionPagesSidebar', 'Links' => 'ab_MetaLinksSidebar');
		
		ab_doSideBar($sidebars, 'autoblogged-feeds');
		ab_OpenMainSection();


		// Duplicate Matching
		echo ab_makeBoxStart("Duplicate Posts");
		echo '<table>';
		echo '<tr valign="top"><td>Duplicate filtering:&nbsp;</td><td>';
		echo ab_makeCheckBoxOnly('filterbytitle', $ab_options['filterbytitle'], 'Match duplicates based on post title', '').'<br />';
		echo ab_makeCheckBoxOnly('filterbylink', $ab_options['filterbylink'], 'Match duplicates based on link', '').'</td></tr></table><br />'.ab_makeBoxClose();

		// Title Filtering
		echo ab_makeBoxStart("Title Filtering");
		echo '<table>';
		echo ab_makeTextInput('maxtitlelen', $ab_options['maxtitlelen'], 2, 'Maximum post title length&nbsp;&nbsp;<br />(characters)', '');
		$options = array('Truncate to the nearest word', 'Skip the post');
		echo '<tr valign="top"><td>When title is too long:</td><td>';
		echo ab_makeRadioOnly('longtitlehandling', $ab_options['longtitlehandling'], $options, '');
		echo '<tr><td>&nbsp;</td></tr>';
		echo ab_makeCheckBox('skipcaps', $ab_options['skipcaps'], 'Title filtering', 'Skip titles in all caps', '');
		echo ab_makeCheckBox('skipmultiplepunctuation', $ab_options['skipmultiplepunctuation'], '', 'Skip titles with multiple consecutive punctuation marks', 'Skip titles such as "What is this?!" or "Amazing!!!!"');
		echo '</td></tr></table>'.ab_makeBoxClose();

		// Blacklists
		echo ab_makeBoxStart("Blacklists");
		echo '<table>';
		echo ab_makeHalfWidthTextArea('domains_blacklist', $ab_options['domains_blacklist'], 10, 'URL Blacklist', 'Reject posts from any of the domains or URL sequences listed here. <br />Add one per line or on a single line separated by commas.');
		echo ab_makeHalfWidthTextArea('keywords_blacklist', $ab_options['keywords_blacklist'], 10, 'Keywords Blacklist', 'Reject posts that contain any of these keywords. <br />Add one per line or on a single line separated by commas.');
		echo '</table>';
		echo '</table></form>'.ab_makeBoxClose().'</div></div></div></div>';
	}

	function ab_showSupportPage()
	{
		global $ab_options, $wpdb, $wp_version;
		ab_adminPageHeader();

		// Handle forced DB upgrade
		if ($_GET['upgrade_db'] == '1') {
			update_option("autoblogged_db_version", '');
			ab_installOnActivation(true);
		}

		// Handle support request e-mail
		if ($_POST['SubmitMessage'] == 'Submit') {
			check_admin_referer('autoblogged-support');
			
			if (empty($_POST['msg'])) {
				echo '<div id="message" class="updated fade"><p>Error: Message field is empty, e-mail message was NOT sent.</p></div>';
			} else {
				$email = "";
				$name = $_POST['na'];
				$message = $_POST['msg'];
				$message_headers = 'From: "'.$name.'"<'.$_POST['em'].'>';
				if (intval(isset($_POST['attachinfo']))) {
					$message .= "\n\n\n".'---SYSTEM CONFIGURATION---';
					$message .= "\n".'AutoBlogged Version: '.AB_VERSION;
					$message .= "\n".'PHP Version: '.phpversion();
					$message .= "\n".'WordPress Version: '.$wp_version;
					$message .= "\n".'MySQL Version: '.mysql_get_server_info();
				}
				if (intval(isset($_POST['attachconfig']))) {
					$message .= "\n\n\n".'---AUTOBLOGGED CONFIGURATION---';
					$message .= "\n\n".var_export($ab_options, true); 
					
					$message .= "\n\n\n".'---FEEDS CONFIGURATION---';
					$sql = "SELECT * FROM " . ab_tableName();
					$feeds = $wpdb->get_results($sql, 'ARRAY_A');
					$message .= "\n\n".var_export($feeds, true);
				}
		
				if (wp_mail($email, $_POST['su'], $message, $message_headers)) {
					echo '<div id="message" class="updated fade"><p>Support message sent. You should receive an automated response in the next few minutes. If you do not get a response, we may have not properly received the message.</p></div>';
				} else {
					echo '<div id="message" class="updated fade"><p>Error: messages could not be sent. Please visit <a href="http://support.autoblogged.com">support.autoblogged.com</a></p></div>';
				}
			}
		}
	
		echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Support'.$feed['id'].'" method="post">';
		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-tag-options');
		echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;
		echo '<div class="wrap"><h2>Technical Support</h2>';
		echo '<div id="poststuff"><div class="post-body">';
		echo ab_makeBoxStart('Diagnostic tools');
		echo '<br />&nbsp;&nbsp;<a href="'.ab_pluginURL().'/diag.php?height=450&width=550" class="thickbox">View AutoBlogged diagnostics...</a><br />';
		echo '<br />&nbsp;&nbsp;<a href="'.ab_pluginURL().'/diag.php?type=info&height=550&width=650" class="thickbox">Show PHPInfo...</a><br/><br/><br/>';
		echo ab_makeCheckBox('logging', $ab_options['logging'], '', 'Enable logging to file when processing feeds.', '');
		echo '<br />';
		echo ab_makeCheckBox('showdebug', $ab_options['showdebug'], '', 'Show verbose debug info.', '');
		echo '<br /><br /><input name="Submit" type="submit" value="Save" />';
		
	  echo '<br /><br /><br />If the AutoBlogged tables were not properly created upon installation, you may manually <a href="admin.php?page=' . $_GET['page'] . '&amp;upgrade_db=1">create them now</a><br/>';
		
		echo ab_makeBoxClose();
		

		echo ab_makeBoxStart('Submit Support Request');
		echo 'For technical support visit <a href="http://support.autoblogged.com" target="_blank">http://support.autoblogged.com</a>, send an e-mail to <a href="mailto:"></a> or use the form below.<br /><br />';
		echo '<form action="admin.php?page=' . $_GET['page'] . '&amp;p=Support&action=sendmsg" method="post">';
		echo 'Your Name:<br /><input name="na" type="text" /><br />';
		echo 'Your E-Mail Address:<br /><input name="em" type="text" value="'.get_option('admin_email').'" /><br />';
		
		echo 'Subject:<br /><input name="su" type="text" /><br />';
		echo 'Message:<br />';
		echo '<textarea name="msg" style="width: 700px; height: 163px"></textarea><br/>';
		echo '<input name="attachinfo" type="checkbox" checked="checked" value="1" />Attach system version info&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input name="attachconfig" type="checkbox" />Attach entire AutoBlogged configuration';
		echo '<br /><br /><input name="SubmitMessage" type="submit" value="Submit" />';
		if ( function_exists('wp_nonce_field') )	$wpnonce = wp_nonce_field('autoblogged-support');
		Echo '<input type="hidden" name="_submit_check" value="1" />'.$wpnonce;
		echo '</form>';
		echo '<br /><br /></div></div>';
		echo ab_makeBoxClose();
		echo '<br /><br />';
	}


	
	// Show the sidebar links
	function ab_MetaLinksSidebar()
	{
		$links = array(
		"Online Documentation" => "http://autoblogged.com/online-help/",
		"Support Forums" => "http://autoblogged.zendesk.com/forums/",
		"AutoBlogged Website" => "http://autoblogged.com", 
		"Host And Upload Files" => "http://angnetwork.com/upfile/",
		//"Become an Affiliate" => "http://autoblogged.com/affiliate-program/" );
        "AngNetwork Website" => "http://angnetwork.com/");

		$feeds = array(
		"Announcements Feed" =>  "http://autoblogged.zendesk.com/",
		//"Latest Forum Posts" => "http://autoblogged.zendesk.com/");
        "Latest Autoblogged Download" => "http://angnetwork.com/autoblogged/");
		

		$url = ab_pluginURL();
		$html = '<ul>';
		foreach (array_keys($links) as $link) {
			$html .= '<li><a href="'.$links[$link].'" target="_blank">'.$link.'</a></li>';
		}

		$html .= '</ul><br /><ul>';
		foreach (array_keys($feeds) as $feed) {
			$html .= '<li><a href="'.$feeds[$feed].'" target="_blank">'.$feed.'&nbsp;<img src="'.$url.'/img/rss.png" /></a></li>';
		}
		$html .= '</ul><br /><br /><br />';

		echo $html;
		echo '<br /><br /><br /><br /><br />';
	}
	
	
	function ab_makeBoxStart($title, $div = '', $closed = false)
	{
		if (!isset($div)) $div = str_replace(' ', '', $title).'div';
		$html = '<div id="'.$div.'" class="postbox ';
		if ($closed) $html .= 'if-js-closed';
		$html .= '"><h3>'.$title.'</h3><div class="inside">';
		$html .= "\r\n";
		return $html;
	}

	function ab_makeBoxClose()
	{
		$html = '</div></div>';
		return $html;
	}

	function ab_makeCheckBox($field, $val, $title, $label, $help)
	{
		if (strlen($title)) $title .= ': ';
		$html = '<tr valign="top"><td scope="row">'.$title.'</td><td><label for="'.$field.'"><input name="'.$field.'" id="'.$field.'" type="checkbox" ';

		if ($val == true) {
			$html .= 'checked="checked" value="checked"';
		}
		$html .= '/> '.$label."</label>";
		if (!empty($help)) $html .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1" color="SlateGray">'.$help.'</font>';

		$html .= "</td></tr>\r\n";
		return $html;
	}

	function ab_makeCheckBoxOnly($field, $val, $label, $help)
	{
		$html = '<label for="'.$field.'"><input name="'.$field.'" id="'.$field.'" type="checkbox" ';
		if ($val == true) {
			$html .= 'checked="checked" value="checked"';
		}
		$html .= '/> '.$label.'</label>';

		return $html;
	}

	// $label is an array of options, $val is the index of the selected option
	function ab_makeRadioOnly($field, $val, $label, $help)
	{
		if (is_array($label)) {
			$i=0;
			foreach ($label as $itemlabel) {
				$html .= '<label for="'.$field.'"><input name="'.$field.'" id="'.$field.$i.'" type="radio" ';
				if ($val == $i) {
					$html .= 'checked value="'.$i.'"';
				} else {
					$html .= 'value="'.$i.'"';
				}
				$html .= '/> '.$itemlabel.'</label><br />';
				$i++;
			}

			return $html;
		} else {
			// Why use a radio if there's only one option?
		}
	}


	function ab_makeSelect($field, $values, $selected, $title, $help, $allowblank = true)
	{
		$html = '<tr valign="top"><td scope="row">'.$title.':</td><td><select name="'.$field.'">';
		if ($allowblank) echo '<option></option>';
		foreach ($values as $value) {
			$html .= '<option';
			if (strcasecmp($value, $selected) == 0) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.stripslashes(attribute_escape($value)).'</option>';
		}
		$html .= "</select></td></tr>\r\n";
		return $html;
	}

	function ab_makeTextInput($field, $value, $defaultWidth, $title, $help, $backcolor='')
	{
		$html = '<tr valign="top"><td scope="row">'.$title.':</td><td><input ';
		if (strlen($backcolor)) $html.= 'style="background-color: '.$backcolor.';" ';
		$html .= 'name="'.$field.'" value="'.stripslashes(attribute_escape($value)).'"';
		if (!empty($defaultWidth)) {
			$html .= ' size="'.$defaultWidth.'"';
		}
		$html .= ' />';
		if (!empty($help)) {
			$html .= '<br /><font size="1" color="SlateGray">'.$help.'</font>';
		}
		$html .= "</td></tr>\r\n";
		return $html;
	}

	function ab_makeWideTextArea($field, $value, $rows, $title, $caption, $help)
	{
		//$html = '<h3>'.$title.'</h3><table>';
		$html = '<table>';
		$html .= '<tr valign="top"><td>'.$caption.':</td></tr><tr><td><textarea name="'.$field.'" rows="'.$rows.'" style="width: 100%">'.stripslashes(attribute_escape($value)).'</textarea>';
		if (!empty($help)) {
			$html .= '<br /><font size="1" color="SlateGray">'.$help.'</font>';
		}
		$html .= "</td></tr>\r\n</table><br /><br />";
		return $html;
	}

	function ab_makeHalfWidthTextArea($field, $value, $rows, $title, $help)
	{
		$html .= '<tr valign="top"><td scope="row">'.$title.'</td><td><textarea name="'.$field.'" rows="'.$rows.'" style="width: 50%">'.stripslashes(attribute_escape($value)).'</textarea>';
		if (!empty($help)) {
			$html .= '<br /><font size="1" color="SlateGray">'.$help.'</font>';
		}
		$html .= "</td></tr>\r\n";
		return $html;
	}


	// $items is a 2-dimensional array of values like this:
	//     [0] => Array {
	//            [Search] => this
	//            [Replace] => that
	//        )
	// Keys are the column headings, must include at least one value to use as a template
	function ab_makeValuePairTable($headings, $colOneItems, $colTwoItems)
	{

		$html = '<table><tr>';
		foreach ($headings as $heading) {
			$html .= '<td width="50%"><b>'.$heading.'</b></td>';
		}
		$html .= '</tr>';
		$i = 0;
		if (is_array($colOneItems)) {
			foreach ($colOneItems as $item) {
				if (!empty($colOneItems[$i])) {

					$html .= '<tr>';
					$html .= '<td><input name="'.strtolower(str_replace(" ", "", $headings[0])).'['.$i.']" value="'.stripslashes(attribute_escape($colOneItems[$i])).'" size="50"></td>';
					$html .= '<td><input name="'.strtolower(str_replace(" ", "", $headings[1])).'['.$i.']" value="'.stripslashes(attribute_escape($colTwoItems[$i])).'" size="50"></td></tr>';
					$i++;
				}
			}
		}

		// Add a couple blank lines
		for ($k = 0; $k <= 1; $k++) {
			$html .= '<tr><td><input name="'.strtolower(str_replace(" ", "", $headings[0])).'['.(int)($k + $i).']" size="50"></td>';
			$html .= '<td><input name="'.strtolower(str_replace(" ", "", $headings[1])).'['.(int)($k + $i).']" size="50"></td></tr>';
		}


		$html .= "</table>\r\n";
		return $html;
	}
	
	function ab_validateFormInput()
	{
		//if ($_POST['price'] != strval(floatval($_POST['price']))) {
		//$errors[] = 'Please enter a valid price.';
	}

	// $sidebars is an array of titles as keys, functions as values
	function ab_doSideBar($sidebars, $page, $feed = '')
	{
	echo '<div class="submitbox" id="submitlink">';
			
			foreach (array_keys($sidebars) as $sidebar) {
				add_meta_box(str_replace(' ', '', $sidebar).'div', __($sidebar), $sidebars[$sidebar], $page, 'side', 'core');
			}

			echo '<div id="side-info-column" class="inner-sidebar">';

			$side_meta_boxes = do_meta_boxes( $page, 'side', $feed);

			echo '</div></div>';

	}
	
	function ab_OpenMainSection()
	{
			echo '<div id="post-body" class="has-sidebar">';
			echo '<div id="post-body-content" class="has-sidebar-content">';
			//echo '<div id="sourcefeedsdiv" class="stuffbox"><div class="inside">';
	}
?>