<?php
/*
Plugin Name: AutoBlogged
Plugin URI: http://www.autoblogged.com
Description: Automatically creates posts and builds content from RSS or ATOM feeds, Blog searches, and other sources.
Author: Autoblogged.com (support@autoblogged.com)
Author URI: http://www.autoblogged.com
Version: 2.5.74
*/


define("AB_VERSION", "2.5.74");
define("DB_SCHEMA_AB_VERSION", "2.3.107");
define("AB_BETA", false);

//////////////////////////////////////////////////////////////////////////////////////
// additional options not available on the admin page that you can customize here

define("EXTRA_IMAGE_FIELDS", false);

// Set the weight given to different post elements
// e.g., a value of 10 would be the same as that word/phrase appearing 10 extra times in the
// article context. A value of 0 means no extra weight given (which will speed up processing).
define("META_KEYWORDS_WEIGHT", "15");
define("H1_WEIGHT", "12");
define("H2_WEIGHT", "10");
define("H3_WEIGHT", "6");
define("REL_TAGS_WEIGHT", "3"); // gives bonus to the last word in the link url if rel="tag" is set
define("LINK_TEXT_WEIGHT", "4");
define("ALT_TAGS_WEIGHT", "3");
define("URL_TAGS_WEIGHT", "3"); // gives bonus to words in the url that follow "tag", "category", or "wiki" even if rel="tag" isn't set
define("LINK_TITLE_WEIGHT", "3");
define("BOLD_WORD_WEIGHT", "4");
define("TAGS_TXT_WEIGHT", "500"); // Bonus for words found in tags.txt
define("YAHOO_TAGS_WEIGHT", "3");


// SimplePie by default filters out certain HTML tags for security purposes. You can override this by changing
// the following settings. Use these settings with caution. 
define("ALLOW_OBJECT_AND_EMBED_TAGS", false);  // Allows object, embed, param
define("ALLOW_FORM_TAGS", false);  // Allows form, input
define("ALLOW_FRAME_TAGS", false); // Allows frame, iframe, frameset
define("ALLOW_SCRIPT_TAGS", false); // Allows class, expr, script, noscript, onclick, onerror, onfinish, onmouseover, onmouseout, onfocus, onblur 

// This turns off all HTML tag and attribute filtering.
define("ALLOW_ALL_TAGS", false);

// Set the next line to true if you want HTML tags encoded rather than stripped out
define("ENCODE_INSTEAD_OF_STRIP", false);


// If SimplePie doesn't recognize a malformed feed, set the following to true to force processing anyway
define("FORCE_FEED", false);




/////////////////////////////////////////////////////////////////////////////////////
// Do not edit below this line


// Constants for combo boxes
define("RANDOM_AUTHOR", "(Use random author)");
define("AUTHOR_FROM_FEED", "(Use author from feed)");
define("ADD_AUTHOR", "(Create new author)");
define("SKIP_POST", "(Skip the post)");

// Other contstants
define("AB_MANUAL_UPDATES", 2);
define("AB_EVERY_X_UPDATES", 1);

define("AB_ITEM_MAX_POSTS", 1);
define("AB_ITEM_PERCENT_POSTS", 2);

define("AB_TITLE_TRUNCATE", 0);
define("AB_TITLE_SKIP", 1);

define("AB_WP_27", "2.7-RC0");
define("AB_WP_28", "2.8");


$ab_options = array();
$feedtypes = array();
$autoblogged =  object;
$rss = object;


if (!class_exists('autoblogged')) {
	class autoblogged	{
		var $db_table_name = '';
		var $tags = array();
		var $keywords = array();
		var $rssmodules = array();

		var $exclude_domains = array();
		var $exclude_words = array();
		var $global_extra_tags = '';
		var $categories = array();
		var $bookmarks = array();
		var $own_domain = '';
		var $filtered_tags = array();
		var $upload_dir = '';
		var $upload_url = '';

		var $current_feed = array();
		var $current_item = array();
		var $postinfo = array();
		var $logger = object;
		
		var $show_output = false;
		var $debug = true;
		
		//---------------------------------------------------------------
		function autoblogged() {
			$this->__construct();
		}
		
		
		//---------------------------------------------------------------
		function __construct() {
			global $wpdb, $ab_options, $feedtypes;

			// WordPress hooks
			add_filter('xmlrpc_methods', 'autoblogged_xmlrpc');
			add_action("admin_menu", array(&$this,"ab_addAdminPages"));
			add_action('shutdown', array(&$this,'ab_shutdownIntercept'));
			add_action('wp_footer', array(&$this,'ab_wpfooterIntercept'));
			add_action('akismet_spam_caught', array(&$this,'ab_akismetntercept'));
			register_activation_hook(__FILE__,"ab_installOnActivation");

			// Load common functions
			require_once(dirname(__FILE__).'/ab-functions.php');

			// Load php4 compatibility functions if needed
			if (version_compare(PHP_VERSION, '5.0.0', '<')) require_once(dirname(__FILE__).'/php4-compat.php');
			$ab_options = ab_getOptions();

			$feedtypes = array(
			"1" => "RSS Feed",
			"2" => "Google Blog Search",
			"3" => "Technorati Search",
			"4" => "BlogDigger Search",
			"5" => "Blogpulse Search",
			"6" => "MSN Spaces Search",
			"7" => "Yahoo! News Search",
			"8" => "Flickr Tag Search",
			"9" => "YouTube Tag Search",
			"10" => 'Yahoo! Video Search',
			);
		}


		//---------------------------------------------------------------
		function ab_addAdminPages(){

			add_menu_page(__FILE__, 'AutoBlogged', 10, 'AutoBlogged', 'ab_FeedsPage');
			add_submenu_page('AutoBlogged', 'AutoBlogged Feeds', 'Feeds', 10, 'AutoBlogged', 'ab_FeedsPage');
			add_submenu_page('AutoBlogged', 'AutoBlogged Tag Options', 'Tag Options', 10, 'AutoBloggedTagOptions', 'ab_TagOptionsPage');
			add_submenu_page('AutoBlogged', 'AutoBlogged Filtering Options', 'Filtering', 10, 'AutoBloggedFiltering', 'ab_FilteringPage');
			add_submenu_page('AutoBlogged', 'AutoBlogged Settings', 'Settings', 10, 'AutoBloggedSettings', 'ab_SettingsPage');
			add_submenu_page('AutoBlogged', 'AutoBlogged Support', 'Support', 10, 'AutoBloggedSupport', 'ab_SupportPage');

			// load the scripts we will need
			if (stristr($_REQUEST['page'], 'AutoBlogged')) {
				wp_enqueue_script('post');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('postbox');
				//wp_enqueue_script('admin-categories');
				wp_enqueue_script('admin-tags');
			}
		}


		//---------------------------------------------------------------
		function ab_errorHandler($code, $message, $file, $line)
		{
			if (stristr($file, 'wp-includes')) return;

			switch ($code) {
				case E_WARNING:
				case E_USER_WARNING:
				$priority = PEAR_LOG_WARNING;
				break;

				case E_NOTICE:
				case E_USER_NOTICE:
				//$priority = PEAR_LOG_NOTICE;
				return;
				break;

				case E_ERROR:
				case E_USER_ERROR:
				$priority = PEAR_LOG_ERR;
				break;

				default:
				//$priority = PEAR_LOG_INFO;
				return;
			}

			$this->ab_logMsg($message . ' in ' . $file . ' at line ' . $line,	$priority);
		}

		//---------------------------------------------------------------
		// main feed processing procedure
		
		function ab_processFeeds($fid = '', $manual_update = false)
		{
			global $wpdb, $ab_options, $rss;
			@set_time_limit(300);
			$box_not_closed = false;
			
			kses_remove_filters();

			// Logging, debugging, and error handling
			if (isset($autoblogged)) {
				$this->ab_initlogger();
			}

			set_error_handler(array(&$this, 'ab_errorHandler'));
			if ($manual_update || $fid) {
				$type = 'manual';
			} else {
				$type = 'scheduled';
			}
			
			if ($ab_options['running'] == false && $type == 'scheduled') {
				$this->ab_logMsg('Feed procesing paused.', 'debug');
				return;
			}
			$this->ab_logMsg('=== v'.AB_VERSION.' ==============================================', 'debug');
			$this->ab_logMsg('Starting '.$type.' feed processing', 'debug');

			// Includes
			$this->ab_logMsg('Loading SimplePie module...', 'debug');
			if (!class_exists('SimplePie'))
			
			{
				require_once(ab_plugin_dir().'/simplepie.php');
			}
			
			$this->ab_logMsg('Loading RSS namespace definitions...', 'debug');
			require_once(ab_plugin_dir().'/modules.php');

			$this->show_output =  (bool)(is_admin() && $manual_update);

			// Set last updated time
			$this->ab_logMsg('Updating timestamp...', 'debug');
			$ab_options['lastupdate'] = time();
			$ab_options['interval'] = rand($ab_options['mintime'], $ab_options['maxtime']);
			ab_saveOptions();
	
			// Get the feed info from the db
			$this->ab_logMsg('Loading feed details...', 'debug');
			$sql = "SELECT * FROM " . ab_tableName();
			if (strlen($fid))	{
				$manual_update = true;
				$sql .= ' WHERE id = '.$wpdb->escape($fid);
			}
			$feeds = $wpdb->get_results($sql, 'ARRAY_A');

			// Get some global settings
			$this->exclude_domains = ab_splitList(strtolower($ab_options['domains_blacklist']));
			$this->exclude_words = ab_splitList($ab_options['keywords_blacklist']);
			$this->global_extra_tags = $ab_options['tags'];
			$this->categories = get_categories('orderby=name&hide_empty=0');
			$this->bookmarks = get_bookmarks();
			$this->own_domain = str_ireplace("http://", "", get_option('siteurl'));
			$this->filtered_tags = ab_splitList($ab_options['notags']);
			$uploaddir_t =  wp_upload_dir();
			$this->upload_dir = $uploaddir_t['path'];
			$this->upload_url = $uploaddir_t['url'];


			// Populate list of authors
			$this->ab_logMsg('Loading authors...', 'debug');
			$this->userlist =  array();
		  $users = $wpdb->get_results("SELECT display_name FROM $wpdb->users ORDER BY display_name");
			if (is_array($users)) {
				foreach ($users as $user) {
					$this->userlist[] = $user->display_name;
				}
			}

			// Output for manual processing
			if (count($feeds) < 1) {
				ab_logMsg('There are no feeds to process.', 'warn');
				return;
			}

			if ($this->show_output) {
				if (count($feeds) > 1) $plural = 's';
				echo '<div id="message" class="updated fade"><p>Processing '.count($feeds).' feed'.$plural.'...</p></div><br />';
				echo '<style type="text/css">.divHide{display:none;}.divShow{display:block;}</style>';
				echo '<script type="text/javascript" language="javascript">function showhide(obj) {';
				echo 'var el=document.getElementById(obj);';
				echo '	if (el.className == "divHide") {';
				echo '	el.className = "divShow";';
				echo '	} else {';
				echo '	el.className = "divHide";';
				echo '	}}</script>';
				echo '<div class="wrap"><div id="poststuff"><div class="post-body">';
			}

			$this->ab_logMsg('Processing feeds...', 'debug');

			//---------------------------
			// Import feeds - main loop
			foreach ($feeds as $feed) {
				$this->current_feed = $feed;
				$this->ab_logMsg('-------------------------------------------', 'debug');
				
				if ($box_not_closed == true) {
					if ($this->show_output) echo '</div></div><br />';
				}
				$box_not_closed = false;
				$this->ab_logMsg('Checking feed schedule...', 'debug');

				// Check to see if we are manually running one feed
				// if not, check the schedule
				if (!$fid) {
					if (!$this->ab_checkFeedSchedule($feed)) continue;
				}

				// And make sure the feed is enabled
				if ($this->current_feed['enabled'] == false && (!$fid)) continue;

				$this->ab_logMsg('Updating feed timestamp...', 'debug');
				$sql ='UPDATE ' . ab_tableName() . ' SET `last_updated`='.time().' WHERE id='.$wpdb->escape($this->current_feed['id']).';';
				$ret = $wpdb->query($sql);

				//// Initialize feed settings

				// load custom fields for this feed
				if (count(ab_unserialize($this->current_feed['customfield'])) > 0 && count(ab_unserialize($this->current_feed['customfieldvalue'])) > 0) {
					$this->current_feed['customFields'] = @array_combine(ab_unserialize($this->current_feed['customfield']),ab_unserialize($this->current_feed['customfieldvalue']));
				}

				// Load other feed-level settings
				$this->current_feed['feed_extra_tags'] =ab_unserialize($this->current_feed['tags']);
				$this->current_feed['nowords'] = ab_splitList($this->current_feed['includenowords']);
				$this->current_feed['allwords'] = ab_splitList($this->current_feed['includeallwords']);
				$this->current_feed['anywords'] = ab_splitList($this->current_feed['includeanywords']);
				$this->current_feed['phrase'] = ab_splitList($this->current_feed['includephrase']);

				if (strlen($this->current_feed['customplayer']) == 0) {
					$this->current_feed['customplayer']= ab_pluginURL() . '/mediaplayer.swf';
				}

				if (is_array(ab_unserialize($this->current_feed['searchfor']))) $this->current_feed['search'] = array_merge(ab_unserialize($this->current_feed['searchfor']));
				$this->current_feed['replace'] =ab_unserialize($this->current_feed['replacewith']);


				// Retrieve the feed now
				$this->ab_grabFeed($feed, $items);


				// Output for manual processing
				if ($this->show_output) {
					echo '<div id="feeddiv" class="postbox "><h3>'.stripslashes($rss->get_title()).'';
					echo '<br />&nbsp;&nbsp;<a href="'.$rss->subscribe_url().'" target="_blank"><img src="'.ab_pluginURL().'/img/feed.png" />'.$rss->subscribe_url().'</a></h3><div class="inside">';
					$this->ab_logMsg('Processing feed: '.$rss->subscribe_url(), 'debug');
					$box_not_closed = true;
				}

				if (count($items) < 1) {
					$this->ab_logMsg('Feed returned no items.', 'warn');
					continue;
				}

				//--------------------------------------
				// Loop through each item in the feed
				$this->current_feed['post_count'] = 0;
				$itemid = 0;

				$this->ab_logMsg('Feed has '.count($items).' item(s).<br />', 'view');


				// Temp placeholders for [[ and ]]
				$this->current_feed['templates'] = str_replace('[[', '~~@-$', $this->current_feed['templates']);
				$this->current_feed['templates'] = str_replace(']]', '$-@~~', $this->current_feed['templates']);
	
				if (is_array($this->current_feed['customFields'])) {
					foreach ($this->current_feed['customFields'] as $customField) {
							$customField = str_replace('[[', '~~@-$', $customField);
							$customField = str_replace(']]', '$-@~~', $customField);
					}
				}

				if ($this->current_feed['replace']) {
					foreach ($this->current_feed['replace'] as $pattern) {
						$pattern = str_replace('[[', '~~@-$', $pattern);
						$pattern = str_replace(']]', '$-@~~', $pattern);
					}
				}

				foreach ($items as $item) {
					$this->ab_logMsg('', 'debug');
					$this->current_item = $item;
					$this->postinfo = array();
					
					$this->postinfo['feed_title'] = $this->current_feed['title'];
					
					if ($this->show_output) echo '<div style="border-style: dotted none none none;border-width: thin;border-color: #A0A0A0;margin-bottom: 5px;margin-top: 5px;">&nbsp;</div>';
					$this->ab_logMsg('Getting Item Link...', 'debug');
					
					if (!$this->ab_itemGetLink()) continue;

					$this->ab_logMsg('Getting item title...', 'debug');
					if (!$this->ab_itemGetTitle()) continue;

					// Check to make sure we haven't hit max_posts
					if (($this->current_feed['post_processing'] == AB_ITEM_MAX_POSTS) && ($this->current_feed['post_count'] >= $this->current_feed['max_posts'])) {
						$this->ab_logMsg('Maximum posts reached for this feed.', 'stop');
						continue 2;
					}
					
					if (($this->current_feed['post_processing'] == AB_ITEM_PERCENT_POSTS) && (rand(100,0) > $this->current_feed['posts_ratio'])) {
						$this->ab_logMsg('Randomly skipping '.$this->current_feed['posts_ratio'].'% of this feed\'s posts.', 'skip');
						continue;
					}

					$this->ab_logMsg('Dupecheck...', 'debug');
					if (!$this->ab_itemDupeCheck()) continue;

					$this->ab_logMsg('Getting content...', 'debug');
					if (!$this->ab_itemGetContent()) continue;

					$this->ab_logMsg('Filtering...', 'debug');
					if (!$this->ab_itemFilter()) continue;

					$this->ab_logMsg('Making excerpt...', 'debug');
					if (!$this->ab_itemGetExcerpt()) continue;

					$this->ab_logMsg('Getting date...', 'debug');
					if (!$this->ab_itemGetDate()) continue;

					$this->ab_logMsg('Getting author...', 'debug');
					if (!$this->ab_itemGetAuthor()) continue;

					if (!$this->ab_itemGetCopyright()) continue;

					$this->ab_logMsg('Getting item source...', 'debug');
					if (!$this->ab_itemGetSource()) continue;

					$this->ab_logMsg('Checking for attachments...', 'debug');
					if (!$this->ab_itemGetAttachments()) continue;

					$this->ab_logMsg('Getting categories and tags...', 'debug');
					if (!$this->ab_itemGetCategoriesAndTags()) continue;

					$this->ab_logMsg('Getting custom fields...', 'debug');
					if (!$this->ab_itemGetCustomFields()) continue;
			
					// Build the post content based on a randomly selected template
					$this->ab_logMsg('Post templates...', 'debug');
					$this->postinfo['post'] = $this->ab_applyTemplate($this->current_feed['templates']);

					$this->ab_itemDoSearchReplace();

					// Put back the replaced double brackets
					$this->postinfo['post'] = str_replace('~~@-$', '[', $this->postinfo['post']);
					$this->postinfo['post'] = str_replace('$-@~~', ']', $this->postinfo['post']);

					// Print out feed info if we are doing a visible run
					if ($this->show_output) {
						echo $this->postinfo['post'];
						$itemid++;
						$extra_info = '';

						// Process now but print out later
						
						$extra_info = '<div style="border-style: dotted none none none;border-width: thin;border-color: #E0E0E0;margin-top: 5px;">&nbsp;</div>';
						$extra_info .= '&nbsp;&nbsp;<a style="text-decoration: none;" href="#item'.$itemid.'" onclick="showhide(\'itemid'.$itemid.'\')"><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/plus.gif" />&nbsp;<b>All feed items</b></a>';
						$extra_info .= '<div id="itemid'.$itemid.'" class="divHide"><table>';
						$this->postinfo = array_merge($this->postinfo);
						ksort($this->postinfo);
						
						foreach (array_keys($this->postinfo) as $field) {
							if ($this->postinfo[$field]) {
								if (is_array($this->postinfo[$field]) && $field) {
									$extra_info .= '<tr><td><b>'.$field.':</b></td><td>'.implode('&nbsp;', $this->postinfo[$field]).'</td></tr>';
								} else {
									//echo '<tr><td><b>'.$field.':</b></td><td>'.stripslashes($this->postinfo[$field]).'</td></tr>';
									$extra_info .= '<tr><td><b>'.$field.':</b></td><td><textarea name="TextArea1" style="width: 877px; height: 150px">';
									$extra_info .= htmlentities2($this->postinfo[$field]);
									$extra_info .= '</textarea></td></tr>';
								}
							}
						}
						$extra_info .= '</table></div><br />';

						$extra_info .= '&nbsp;&nbsp;<a style="text-decoration: none;" href="#orig'.$itemid.'" onclick="showhide(\'orig'.$itemid.'\')"><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/plus.gif" />&nbsp;<b>Source page HTML</b></a>';
						$extra_info .= '<div id="orig'.$itemid.'" class="divHide"><table>';
						$extra_info .= '<textarea name="TextArea1" style="width: 877px; height: 250px">';
						$extra_info .= htmlentities2($this->postinfo['page_content']);
						$extra_info .= '</textarea></table></div>';
					}

					if (!$this->ab_itemAddPost()) continue;
					if ($this->show_output) echo $extra_info;

				} // foreach ($rss->items as $item)
				//if ($this->show_output) echo '</div></div>';

				if ($this->show_output) echo '</div></div><br />';
				$box_not_closed = false;
			} // foreach ($feeds as $feed)

			if ($this->show_output) echo '</div></div><br /><br /><br /><br /><br /><br />';

			if (isset($rss)) {
				@$rss->__destruct();
				unset($rss);
			}

			if (is_object($this->logger)) {
				$this->logger->flush();
				$this->logger->close();
			}

		} // end function


		//---------------------------------------------------------------
		function ab_checkFeedSchedule(&$feed) {
			global  $ab_options, $wpdb;
			// action: check_schedule

			switch ($this->current_feed['schedule']) {
				case AB_MANUAL_UPDATES:
				$this->ab_logMsg('Feed configured for manual updates only.', 'debug');
				return false;
				break;

				case AB_EVERY_X_UPDATES:
				if ($this->current_feed['update_countdown'] > 0) {
					// Decrement the counter
					$sql ='UPDATE ' . ab_tableName() . ' SET `update_countdown`='.($this->current_feed['update_countdown']-1).' WHERE id='.$wpdb->escape($this->current_feed['id']).';';
					$ret = $wpdb->query($sql);
					// debug: $ret
					$this->ab_logMsg('Feed not scheduled for updating.', 'debug');
					return false;
				} else {
					// Reset the counter
					$sql ='UPDATE ' . ab_tableName() . ' SET `update_countdown`='.$this->current_feed['updatefrequency'].' WHERE id='.$wpdb->escape($this->current_feed['id']).';';
					$ret = $wpdb->query($sql);
					// debug: $ret
					return true;
				}
				break;

				default:
				// Always update this feed
				return true;
				break;
			}
		} // end function


		//---------------------------------------------------------------
		function ab_grabFeed(&$feed, &$items) {
			global  $ab_options, $rss;
			//action: grab_feed

			// Initialize SimplePie
			$this->ab_logMsg('Initializing SimplePie...', 'debug');
			$rss = new SimplePie();

			// Get URL and handle variations of feed uri
			$feedurl = ab_getFeedURL($this->current_feed['type'], $this->current_feed['url']);
			$feedurl = str_replace("feed://", "http://", $feedurl);
			$feedurl = str_replace("feed:http", "http", $feedurl);
			$rss->set_feed_url($feedurl);

			// Special handling for Yahoo! pipes if they just enter the Pipe URL itself
			if (stristr($feedurl, 'pipes.yahoo') && (!strstr($feedurl, 'rss'))) $feedurl .= '&_render=rss';

			// Cache settings
			$rss->enable_cache(true);
			$rss->set_cache_location(ab_plugin_dir() . '/cache');
			$rss->set_cache_duration($ab_options['rss_cache_timeout']);

			// Autodiscovery settings
			$rss->set_autodiscovery_level(SIMPLEPIE_LOCATOR_ALL);
			$rss->set_autodiscovery_cache_duration(1209600); // 2 weeks
			$rss->set_max_checked_feeds(10);

			// Other settings
			$rss->enable_order_by_date(false);
			$rss->set_useragent($ab_options['useragent'].' (' . mt_rand().')');
			$rss->set_item_limit(50);
			$rss->set_url_replacements(array('a' => 'href', 'img' => 'src'));

			// Timeout
			if (stristr($feedurl, 'pipes')) {
				$rss->set_timeout(60);
			} else {
				$rss->set_timeout(20);
			}

			// HTML tag and attribute stripping
			$strip_htmltags = $rss->strip_htmltags;
	
			if (ALLOW_ALL_TAGS) {
				$strip_htmltags = array();
				$rss->strip_attributes(false);

			} else {
				if (ALLOW_OBJECT_AND_EMBED_TAGS) {
					unset($strip_htmltags[array_search('object', $strip_htmltags)]);
					unset($strip_htmltags[array_search('embed', $strip_htmltags)]);
					unset($strip_htmltags[array_search('param', $strip_htmltags)]);
				}
				
				if (ALLOW_FORM_TAGS) {
					unset($strip_htmltags[array_search('form', $strip_htmltags)]);
					unset($strip_htmltags[array_search('input', $strip_htmltags)]);
				}
				
				if (ALLOW_FRAME_TAGS) {
					unset($strip_htmltags[array_search('frame', $strip_htmltags)]);
					unset($strip_htmltags[array_search('iframe', $strip_htmltags)]);
					unset($strip_htmltags[array_search('frameset', $strip_htmltags)]);
				}
				
				if (ALLOW_SCRIPT_TAGS) {
					unset($strip_htmltags[array_search('class', $strip_htmltags)]);
					unset($strip_htmltags[array_search('expr', $strip_htmltags)]);
					unset($strip_htmltags[array_search('script', $strip_htmltags)]);
					unset($strip_htmltags[array_search('noscript', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onclick', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onerror', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onfinish', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onmouseover', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onmouseout', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onfocus', $strip_htmltags)]);
					unset($strip_htmltags[array_search('onblur', $strip_htmltags)]);
				}
			}

			$strip_htmltags = array_values($strip_htmltags);
			$rss->strip_htmltags($strip_htmltags);
			
			
			if (ENCODE_INSTEAD_OF_STRIP) {
				$rss->encode_instead_of_strip(true);
			}
			
			// Force feed handling with unrecognized or malformed feeds
			if (FORCE_FEED) {
				$rss->force_feed(true);
			}


			// Retrieve the feed
			$this->ab_logMsg('Retrieving feed...', 'debug');
			$rss->init();

			$this->ab_logMsg('Checking feed results...', 'debug');

			// Handle errors
			if ($rss->error()) {
				$this->ab_logMsg('Error occurred retrieving feed.', 'stop');
				// Special handling for urls that aren't really feeds
				if (stristr($rss->error(), 'syntax error at line')) {
					$this->ab_logMsg('Error occurred retrieving feed or feed is invalid.<br />Feed URI: '.$rss->subscribe_url(), 'stop');
				} else {
					$this->ab_logMsg('Error occurred processing feed: '.$rss->error().'.<br />Feed URI: '.$rss->subscribe_url(), 'stop');
				}
				return false;
			}

			// Grab the feed items
			$items = $rss->get_items();

			//filter: the_feed_items
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetLink() {
			global $ab_options;
			// action: filter_link
			$link = urldecode($this->current_item->get_link());

			// Skip if the link is empty
			if (empty($link)) {
				$this->ab_logMsg('Skipping post with empty link.', 'skip');
				return false;
			}

			// Skip the blog's own domain
			if (stristr($link, $this->own_domain)) {
				$this->ab_logMsg('Skipping post from own domain.', 'skip');
				return false;
			}

			// Check for blacklisted domains and url sequences in the link
			$this->ab_logMsg('Checking for blacklisted urls...', 'debug');
			if (is_array($this->exclude_domains)) {
				foreach ($this->exclude_domains as $domain) {
					if (stristr($link, $domain)) {
						$this->ab_logMsg('Skipping post with blacklisted domain or URL sequence: "'.$domain.'"', 'skip');
						return false;
					}
				} // end for
			}
			$this->postinfo['link'] = $link;

			// filter: link
			$this->ab_logMsg('Link: '.$link, 'debug');
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetTitle() {
			global  $ab_options;
			// action: get title

			$title = strip_tags($this->current_item->get_title());

			if ($this->show_output) echo '<p style="font-size:12pt"><b><a target="_blank" href="'.$this->postinfo['link'].'">'.$title.'</a></b></p>';
			
			$this->ab_logMsg('Processing item: '.$title, 'debug');
			
			// Check for multiple punctuation marks
			if ($ab_options['skipmultiplepunctuation'] && preg_match("/[!$%&*?]{2,}/", $title)) {
				$this->ab_logMsg('Skipping post with multiple punctuation marks in title.', 'skip');
				return false;
			}

			// Check for all-caps titles
			if ($ab_options['skipcaps'] && $title == strtoupper($title)) {
				$this->ab_logMsg('Skipping post in all caps.', 'skip');
				return false;
			}

			// Title filtering
			If (strlen($title) > $ab_options['maxtitlelen']) {
				if ($ab_options['longtitlehandling'] == AB_TITLE_TRUNCATE) {
					// Truncate
					$this->ab_logMsg('Truncating title', 'debug');
					$lines = explode("\n", wordwrap($title, $ab_options['maxtitlelen'], "\n", true));
					$title = $lines[0].'...';
				} else {
					// Skip
					$this->ab_logMsg('Skipping post with long title.', 'skip');
					return false;
				}
			}

			$this->postinfo['title'] = $title;
			// filter: title
			return true;
		}

		//---------------------------------------------------------------
		function ab_itemDupeCheck() {
			global  $ab_options, $wpdb;
			//action:  Dupe check

			// Check for duplicate title
			$titledupesfound = false;
			$wpdb->flush;
			if ($ab_options['filterbytitle'] == true) {
				$this->ab_logMsg('Checking for duplicate title...', 'debug');
				$checktitle = @mysql_real_escape_string($this->postinfo['title']);
				if (empty($checktitle)) {
					if (function_exists('mysql_escape_string'))  $checktitle = mysql_escape_string($this->postinfo['title']);
				}				
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_name = '" . sanitize_title_with_dashes($this->postinfo['title']). "' OR post_title = '".$checktitle."'";

				$titledupesfound = $wpdb->query($sql);

				if ($titledupesfound === false) {
					$this->ab_logMsg('Error connecting to database to check for duplicate titles.', 'stop');
					return false;
				}

				if ($titledupesfound > 0) {
					$this->ab_logMsg('Skipping post with duplicate title.', 'skip');
					return false;
				} else {
					// Secondary check
					$post_name_check = $wpdb->get_var($wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1", sanitize_title($this->postinfo['title'])));
					if ($post_name_check) {
						$this->ab_logMsg('Skipping post with duplicate title.', 'skip');
						return false;
					}
				}
			}

			// Check for dupe link
			$linkdupesfound = false;
			if ($ab_options['filterbylink'] == true) {
				$this->ab_logMsg('Checking for duplicate link...', 'debug');

				$sql = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'link' AND meta_value = '" . addslashes($this->postinfo['link']) . "'";
				
				$linkdupesfound = $wpdb->query($sql);

				if ($linkdupesfound === false) {
					$this->ab_logMsg('Error connecting to database to check for duplicate links.', 'stop');
					return false;
				}
				if ($linkdupesfound > 0) {
					$this->ab_logMsg('Skipping post with duplicate link.', 'skip');
					return false;
				}
			}
			
			// Otherwise, let the post through
			$this->ab_logMsg('No duplicate posts found.', 'debug');
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetContent() {
			global  $ab_options;
			$page_content = '';

			//action: get content

			// Get the content of the feed item
			$content = ($this->current_item->get_content());
			$this->ab_logMsg('Extracted content from feed.', 'debug');
			
			if (empty($content)) {
				$this->ab_logMsg('Post content is empty, trying description...', 'debug');
				$content = $this->current_item->get_description();
			}
			
			// Handle encoded content
			if (!ENCODE_INSTEAD_OF_STRIP) {
				$content = html_entity_decode($content);
			}
				
			$this->postinfo['content'] = $content;

			$this->postinfo['description'] = $this->current_item->get_description();
			
			
			// We only need to grab the original page if we are going to
			// be getting tags from it
			if ($ab_options['posttags'] == true) {
				$this->ab_logMsg('Fetching the original post...', 'debug');
				$result = ab_httpFetch($this->postinfo['link']);

				if (strlen($result['error'])) {
					// Warn but don't stop
					$this->ab_logMsg('Unable to retrieve the original post: '. $result['error'], 'warn');
				}

				if ($result['http_code'] >= 400) {
					// Warn but don't stop
					$this->ab_logMsg('Cannot retrieve URL to get tags: '.$this->postinfo['link'].' ('.$result['http_code'].')', 'warn');
				}

				// Fall back to using the content from the feed
				if (strlen($result['contents'])) {
					$page_content = $result['contents'];
				} else {
					$this->ab_logMsg('Using content from feed itself.', 'debug');
					$page_content = htmlentities2($this->current_item->get_content());
				}
			} else {
				$this->ab_logMsg('Not grabbing original feed.', 'debug');
			}
			
			$this->postinfo['page_content'] = $page_content;
			// filter: content
			$this->ab_logMsg('Content variable set.', 'debug');
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemFilter() {
			global $ab_options;
			// action: filter item

			// Check for globally blacklisted words
			$this->ab_logMsg('Checking for blacklisted words...', 'debug');

			foreach ($this->exclude_words as $word) {
				if (!stristr($this->postinfo['page_content'], $word) === false) {
					$this->ab_logMsg('Skipping post with blacklisted word: '.$word, 'skip');
					return false;
				}
			}

			// Perform per-feed filtering
			$this->ab_logMsg('Feed-level filtering...', 'debug');
			$filterpass = true;

			// None of these words
			if (strlen($this->current_feed['includenowords'])) {
				$filterpass = (ab_countItemsFound($this->postinfo['content'].' '.$this->postinfo['title'], $this->current_feed['nowords']) == 0);
				if (!$filterpass) {
					$this->ab_logMsg('Skipping post due to "None of these words" filter.', 'skip');
					return false;
				}
			}

			// All of these words
			if (strlen($this->current_feed['includeallwords']) && $filterpass = true) {
				$filterpass = (ab_countItemsFound($this->postinfo['content'].' '.$this->postinfo['title'], $this->current_feed['allwords']) >= count($allwords));
				if (!$filterpass) {
					$this->ab_logMsg('Skipping post due to "All of these words" filter.', 'skip');
					return false;
				}
			}

			// Any of these words
			if (strlen($this->current_feed['includeanywords']) && $filterpass = true) {
				$filterpass = (ab_countItemsFound($this->postinfo['content'].' '.$this->postinfo['title'], $this->current_feed['anywords']) > 0);
				if (!$filterpass) {
					$this->ab_logMsg('Skipping post due to "Any of these words" filter.', 'skip');
					return false;
				}
			}

			// The exact phrase
			if (strlen($this->current_feed['includephrase']) && $filterpass = true) {
				$filterpass = (ab_countItemsFound($this->postinfo['content'].' '.$this->postinfo['title'], $this->current_feed['phrase']) > 0);
				if (!$filterpass) {
					$this->ab_logMsg('Skipping post due to "Exact phrase" filter.', 'skip');
					return false;
				}
			}

			// We passed all filters
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetExcerpt() {
			global  $ab_options;

			// action: Get excerpt

			// Make a text-only excerpt from the description
			$excerpt_delim = array('/([\s,-;:]+)/', '/([\.\?]\s)/', '/\r\n/');
			$content = $this->postinfo['content'];



			//cleanup
			$content = str_replace('>', '> ', $content);
			$content = strip_tags($content);

			$content = str_replace('[...]', '', $content);
			$content = preg_replace('/\\s+/',' ', $content);

			if (strlen($content)) {
				$words = preg_split($excerpt_delim[$ab_options['excerpt_type']], $content, -1, PREG_SPLIT_DELIM_CAPTURE+PREG_SPLIT_NO_EMPTY);
				$wordcount = count($words);
				$words = array_slice($words, 0, rand($ab_options['minexcerptlen']*2, $ab_options['maxexcerptlen']*2)); //doubled because we are capturing delims
				$excerpt = implode($excerpt_delim[$ab_options['excerpt_type']], $words);

				if ($ab_options['excerpt_type'] == 0 && $wordcount > $ab_options['maxexcerptlen']*2) {
					$words[] = '...';
				}

				$excerpt = implode('', $words);
				$this->postinfo['excerpt'] = $excerpt;
			}
			// filter: the excerpt
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetDate() {
			
			if ($this->current_feed['usefeeddate']) {
				// Date
				$date = strtotime($this->current_item->get_date('Y-m-d H:i:s'));
				if ($this->current_item->get_date('Y') <= 1990) $date = date('Y-m-d H:i:s', time());
				if (is_numeric($date)) $date = date('Y-m-d H:i:s', $date);
				$this->ab_logMsg('Using date from feed: '. $date, 'debug');
				$this->postinfo['date'] = $date;
			} else {
				$date = date('Y-m-d H:i:s', time());
				$this->ab_logMsg('Using current date: '. $date, 'debug');
			}
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemGetAuthor() {
			global  $ab_options, $wpdb;

			switch ($this->current_feed['author']) {

				case AUTHOR_FROM_FEED:
					$this->ab_logMsg('Getting author from the feed.', 'debug');
					$feed_author = $this->current_item->get_author();
				
					if (empty($feed_author)) {
						$this->ab_logMsg('Feed item did not return an author.', 'debug');
						// Use default author
					} else {
						$this->postinfo['author'] = $wpdb->escape($feed_author->get_name());
						$this->postinfo['author_email'] = $wpdb->escape($feed_author->get_email());
						$this->postinfo['author_url'] = $wpdb->escape($feed_author->get_link());
						$this->ab_logMsg('Feed author: '. $this->postinfo['author'], 'debug');
						
						// Find the author in WordPress
						$user = $this->ab_findAuthor('', $this->postinfo['author_email'], $this->postinfo['author_url']);
						if ($user) {

							// User exists in WordPress
							$this->ab_logMsg('Feed author found in WordPress', 'debug');
							$this->postinfo['author_id'] = $user['ID'];
							if ($this->current_feed['useauthorinfo']) {
								// Override feed data with that stored in WordPress user table
								$this->postinfo['author_display_name'] = $user['display_name'];
								$this->postinfo['author'] = $user['display_name'];
								$this->postinfo['author_email'] = $user['user_email'];
								$this->postinfo['author_url'] = $user['user_url'];
								$this->postinfo['author_bio'] = $user['user_description'];
							}
							
						} else {

							// User not found
							$this->ab_logMsg('Feed author not found in WordPress', 'debug');
							switch ($this->current_feed['alt_author']) {
								case SKIP_POST:
									$this->ab_logMsg('Skipping post from unrecognized author.', 'skip');
									return false;
									break;

								case ADD_AUTHOR:
								$this->ab_logMsg('Adding author...', 'debug');
								$adduser = array(
									'user_login'	=> $this->postinfo['author'],
									'user_nicename' => $this->postinfo['author'],
									'user_email'	=> $this->postinfo['author_email'],
									'user_url'	=> $this->postinfo['author_url'],
									'role' => 'Subscriber',
									'user_pass' => md5(uniqid(mt_rand(), true))
									);

									if (function_exists(wp_insert_user)) {
										$uid = wp_insert_user($adduser);
									} else {
										$this->ab_logMsg('Unable to insert user, incompatible WordPress version', 'warn');
									}
									$this->postinfo['author_id'] = $uid;
									break;

								case RANDOM_AUTHOR:
									$user = $this->ab_findAuthor();
									$this->postinfo['author_id'] = $user['ID'];
									$this->postinfo['author_display_name'] = $user['display_name'];
									$this->postinfo['author'] = $user['display_name'];
									$this->postinfo['author_email'] = $user['user_email'];
									$this->postinfo['author_url'] = $user['user_url'];
									$this->postinfo['author_bio'] = $user['user_description'];
									$this->ab_logMsg('Using random author: '.$this->postinfo['author'], 'debug');
									break;

								default:
									$user = $this->ab_findAuthor($this->current_feed['alt_author']);
									$this->postinfo['author_id'] = $user['ID'];
									$this->postinfo['author_display_name'] = $user['display_name'];
									$this->postinfo['author'] = $user['display_name'];
									$this->postinfo['author_email'] = $user['user_email'];
									$this->postinfo['author_url'] = $user['user_url'];
									$this->postinfo['author_bio'] = $user['user_description'];
									break;
							}
						}
					}
					break;
								
				case RANDOM_AUTHOR:
					$user = $this->ab_findAuthor();
					$this->postinfo['author_id'] = $user['ID'];
					$this->postinfo['author_display_name'] = $user['display_name'];
					$this->postinfo['author'] = $user['display_name'];
					$this->postinfo['author_email'] = $user['user_email'];
					$this->postinfo['author_url'] = $user['user_url'];
					$this->postinfo['author_bio'] = $user['user_description'];
					$this->ab_logMsg('Using random author: '.$this->postinfo['author'], 'debug');
					break;

				default:
					$user = $this->ab_findAuthor($this->current_feed['author']);
					$this->postinfo['author_id'] = $user['ID'];
					$this->postinfo['author_display_name'] = $user['display_name'];
					$this->postinfo['author'] = $user['display_name'];
					$this->postinfo['author_email'] = $user['user_email'];
					$this->postinfo['author_url'] = $user['user_url'];
					$this->postinfo['author_bio'] = $user['user_description'];
					$this->ab_logMsg('Using author: '.$this->postinfo['author'], 'debug');
					break;
			}
			return true;
		}

		//---------------------------------------------------------------
		function ab_findAuthor($login = null, $email = null, $uri = null) {
			global $wpdb;
			
			// If all parameters are empty, return a random author
			if (empty($login) && empty($email) && empty($uri)) {
				$sql = "SELECT * FROM $wpdb->users ORDER BY rand() LIMIT 1";
			} else {
			
				$sql = "SELECT * FROM $wpdb->users WHERE ";
				
				if (!empty($login)) {
					$where = " `user_login` = '$login' OR `user_nicename` = '$login' OR `display_name` = '$login' ";
				}
				
				if (!empty($email)) {
					if (!empty($where)) $where .= 'OR ';
					$where .= "`user_email` - '$email' ";
				}
				
				if (!empty($uri)) {
					if (!empty($where)) $where .= 'OR ';
					$where .= "`user_url` = '$uri' ";
				}
			}
			
			// Execute the query
			$user = $wpdb->get_row($sql.$where, ARRAY_A);
			if (empty($user)) {
				return false;
			} else {
				return $user;
			}
		}

		//---------------------------------------------------------------
		function ab_itemGetCopyright() {
			// Copyright
			$copyright = $this->current_item->get_copyright();
			if (is_object($copyright)) {
				$this->postinfo['copyright'] = $copyright->get_attribution();
				$this->postinfo['copyright_url'] = $copyright->get_url();
			}
			return true;
		}

		//---------------------------------------------------------------
		function ab_itemGetSource() {
			global $rss;
			$this->postinfo['source'] = $rss->get_title();
			$this->postinfo['source_url'] = $rss->get_permalink();
			$this->postinfo['source_description'] = $rss->get_description();

			$this->postinfo['icon'] = $rss->get_favicon();
			$this->postinfo['logo_url'] = $rss->get_image_url();
			$this->postinfo['logo_link'] = $rss->get_image_link();
			$this->postinfo['logo_title'] = $rss->get_image_title();
							
			// Pull extra info from blogroll if that option is selected
			if ($this->current_feed['uselinkinfo']) {
				foreach ($this->bookmarks as $bookmark) {

					if (stristr($this->postinfo['link'], str_replace("http://", "", $bookmark->link_url))) {
						if (strlen($bookmark->link_url) > strlen($linkmatch->link_url)) $linkmatch = $bookmark;
					}
					if (stristr(str_replace("http://", "", $bookmark->link_url), str_replace("http://", "", $this->postinfo['link']))) {
						if (strlen($bookmark->link_url) > strlen($linkmatch->link_url)) $linkmatch = $bookmark;
					}
				}

				if ($linkmatch) {
					$this->postinfo['source_url'] = $this->postinfo['link'];
					$this->postinfo['source'] = $linkmatch->link_name;
					$this->postinfo['logo_url'] = $linkmatch->link_image;
					$this->postinfo['source_description'] = $linkmatch->link_description;
				}
			}
			return true;
		}

		//---------------------------------------------------------------
		function ab_itemGetAttachments() {
			global  $ab_options;
			// Images and video
			$this->ab_logMsg('Processing images and video...', 'debug');
			$enclosures = $this->current_item->get_enclosures();
			$image_urls = array();
			$enclosure_tags = array();
			
			$this->ab_logMsg('Attachments found: '. count($enclosures), 'debug');
			
			// get images from all fields
			$this->ab_logMsg('Searching all fields for images...', 'debug');
			require_once ABSPATH.'/wp-admin/includes/image.php';
			foreach (array_keys($this->postinfo) as $field) {
				if (is_array($this->postinfo[$field])) {
					preg_match_all('%http://[^"<:]{5,255}\.(?:jpg|jpeg|gif|png)%', htmlspecialchars_decode(implode(' ', $this->postinfo[$field])), $extractedimageurls);
				} else {
					preg_match_all('%http://[^"<:]{5,255}\.(?:jpg|jpeg|gif|png)%', htmlspecialchars_decode($this->postinfo[$field]), $extractedimageurls);
				}

				if (is_array($extractedimageurls)) $image_urls = array_merge($image_urls, $extractedimageurls[0]);
			}
			
			// Add any media:thumbnail elements
			$elements = $this->current_item->get_item_tags('http://search.yahoo.com/mrss/', 'group');
			$thumbnails = $elements[0]['child']['http://search.yahoo.com/mrss/']['thumbnail'];
			if (is_array($thumbnails)) {
				foreach($thumbnails as $thumbnail) {
					$media_thumbnails[] = $thumbnail['attribs']['']['url'];
				}
			}
			
			if (is_array($media_thumbnails)) $image_urls = array_merge($image_urls, $media_thumbnails);

			if (is_array($enclosures)) {
				$j=0;
				foreach ($enclosures as $enclosure) {
					$j++;
					//Get additional tags from each enclosure
					if ($ab_options['feedtags']) {
						$kw = $enclosure->get_keywords();
						if (is_array($kw)) $enclosure_tags =  array_merge($enclosure_tags, $kw);
						$enc_cats = $enclosure->get_categories();
						if (is_array($enc_cats)) {
							foreach ($enc_cats as $enc_cat) {
								$enclosure_tags[] = $enc_cat->get_label();
							}
						}
						if (is_array($enclosure_tags)) array_unique($enclosure_tags);
					}
					$enc_link = $enclosure->get_link();
					$enc_type = $enclosure->get_type();
					
					if (stristr($enc_type, "image")) {					
						$image_urls[] = $enc_link;
					} else {
						$this->ab_logMsg('Adding video attachment: '.$enc_link, 'debug');
						$vid_embed = ab_getEmbeddedVideo($enc_link, $this->current_feed['playerwidth'], $this->current_feed['playerheight'], $enclosure->get_handler());
						if ($j==1) $this->postinfo['video'] = $vid_embed.' ';

						$this->postinfo['videos'][] = $vid_embed;
						if (!empty($enc_link)) $this->postinfo['video_urls'][] = $enc_link;
					}
					$this->postinfo['video_url'] = $this->postinfo['video_urls'][0];
				}
			}

			// Add image attachments if there are any
			$this->ab_logMsg('Adding image attachments...', 'debug');
			if (is_array($image_urls)) {
				$image_urls = array_unique($image_urls);
					foreach ($image_urls as $image) {

						// Skip these images
						if (stristr($image, 'icn_star')) continue; // YouTube star icon
						if ($image == $this->postinfo['logo_url']) continue; // Skip the feed's logo image
						if (strlen($image) > 255) continue;  // Very long image paths

						// Only need to do this if we are saving images or creating thumbs
						if ($this->current_feed['saveimages'] || $this->current_feed['createthumbs']) {
					
						$upload = array();
						$this->ab_logMsg('Processing image: '.$image, 'debug');

						// First check to see if we already have the image cached
						$imageurl = parse_url($image);
						$pathinfo = pathinfo($imageurl['path']);
						$filehash = substr(md5($image), -10).sanitize_file_name(substr(basename($imageurl['path']),-10)).'.'.$pathinfo['extension'];  // This should be unique enough for our purposes
						$this->ab_logMsg('Cache filename: '.$filehash, 'debug');
						
						if (file_exists($this->upload_dir.'/'.$filehash)) {
							$this->ab_logMsg('File exists in cache.', 'debug');
							$the_url = $this->upload_url.'/'.$filehash;
							$the_file = $this->upload_dir.'/'.$filehash;
							$this->postinfo['content'] = str_replace($image, $the_url, $this->postinfo['content']);

						} else {

							// Grab the original image
							unset($upload);	
							$upload = ab_httpFetch($image);

							// Make sure we actually got something
							if ($upload['headers']['status'] >= 400) {
									$this->ab_logMsg('Unable to retrieve image ('.$upload['headers']['status'].'): '. $image, 'warn');
									continue;
							}

							// Special handling for blogger.com, blogspot.com, wikipedia.com, etc.
							if (stristr($upload['headers']['content-type'], 'text')) {

								$this->ab_logMsg('Searching for image in '.$upload['headers']['content-type'].' content...', 'debug');
								if (preg_match('/<img[^>]*src="([^"]*)"/i', $upload['content'], $matches)) {

									// If we found an image in the text, try it again
									$this->ab_logMsg('Retrying URL: '.$matches[1], 'debug');
									$urlParsed = parse_url($matches[1]);
									$upload = ab_httpFetch($matches[1], $urlParsed['host']);
								} else {
									$this->ab_logMsg('Server did not return valid image for '.$image, 'warn');
									continue;
								}
							}

							// Check again to make sure we are dealing with an image
							if (!empty($upload['headers']['content-type']) && !stristr($upload['headers']['content-type'], 'image')) {
								$this->ab_logMsg('Server did not return valid image type ('.$upload['headers']['content-type'].') for '.$image, 'warn');
								continue;
							}

							// Save the image locally
							//   Create an empty placeholder file in the upload dir
							//   returns array with 'file', 'url', and 'error'
							$result = wp_upload_bits($filehash, 0, '');

							if ($result['error']) {
								$this->ab_logMsg('Unable to write to upload directory: '.$result['error'], 'warn');
								$this->postinfo['error'] .= "Unable to write to upload directory.\r\n";
								$the_url = $image;
								continue;
							}

							// Create a handle to the destination file
							$fp = @fopen($result['file'], 'w');
							if (!$fp) {
								$this->ab_logMsg('Unable to save image to upload directory.', 'warn');
								$this->postinfo['error'] .= "Unable to save image to upload directory.\r\n";
								$the_url = $image;
								continue;
							}

							// Write the file
							fwrite($fp, $upload['content']);
							@fclose($fp);

							$this->ab_logMsg('Image saved locally at '.$result['url'], 'debug');
							if ($this->current_feed['saveimages']) {
								$the_url = $result['url'];						
								$this->postinfo['content'] = str_replace($image, $result['url'], $this->postinfo['content']);
							} else {
								$the_url = $image;
							}
							$the_file = $result['file'];
						}

						$this->postinfo['images'][] = '<img src="'.$the_url.'" />';
						$this->postinfo['image_urls'][] = $the_url;

						$parse_url = parse_url($the_url);
						$this->postinfo['image_paths'][] = $parse_url['path'];

						// Now create a thumbnail for it and get the thumbnail's path
						if ($this->current_feed['createthumbs']) {
							$this->ab_logMsg('Creating thumbnail for '.$the_file.'...', 'debug');												
							$thumbpath = image_resize($the_file, get_option('thumbnail_size_w'), get_option('thumbnail_size_h'));
							if ($thumbpath) {
								if (is_string($thumbpath)) {
									$postdata['guid'] = str_replace(basename($the_file), basename($thumbpath), $result['url']);

									// Kill the original file if the option is not set to save
									if (!$this->current_feed['saveimages']) @unlink($the_file);
								} else {
									if (is_wp_error($thumbpath)) $this->ab_logMsg('WordPress error: '. $thumbpath->get_error_message(), 'debug');
									// use the image itself as the url if we have an error here
									$thumbpath = $the_url;
									$postdata['guid'] = $the_url;
								}
							} else {
								// The image is small enough to be its own thumbnail
								$thumbpath = $the_url;
								$postdata['guid'] = $the_url;
							}
							$this->postinfo['thumbnails'][] = '<img src="'.$postdata['guid'].'" />';
							$this->postinfo['thumbnail_urls'][] = $postdata['guid'];
							
							$url_parsed = parse_url($thumbpath);
							$this->postinfo['thumbnail_paths'][] = stristr($url_parsed['path'], '/wp-content');
						}
						$post_id = wp_insert_attachment($postdata, $thumbpath);
						wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $thumbpath));
						} else {
							$this->postinfo['images'][] = '<img src="'.$image.'" />';;
							$this->postinfo['image_urls'][] = $image;
						}
					}  // foreach ($image_urls as $image)

					$this->postinfo['image'] = $this->postinfo['images'][0];
					$this->postinfo['image_path'] = $this->postinfo['image_paths'][0];
					$this->postinfo['image_url'] = $this->postinfo['image_urls'][0];
					$this->postinfo['thumbnail'] = $this->postinfo['thumbnails'][0];
					$this->postinfo['thumbnail_path'] = $this->postinfo['thumbnail_paths'][0];
					$this->postinfo['thumbnail_url'] = $this->postinfo['thumbnail_urls'][0];

			}
			return true;
		}
		
		
		//---------------------------------------------------------------
		function ab_itemGetCategoriesAndTags() {
			global  $ab_options, $notags;

			$this->ab_logMsg('Building categories and tags...', 'debug');
			if(!empty($this->global_extra_tags)) $this->ab_logMsg('Feed tags list: '.implode(', ', $this->global_extra_tags), 'debug');
			$this->ab_logMsg('Global tags list: '.implode(', ', $this->current_feed['feed_extra_tags']), 'debug');


			// Clear out any keywords and tags from previous item
			$feed_tags = array();
			$original_post_tags = array();
			$more_categories = array();
			$keywords = array();
			$enclosure_tags = array();
			$this->current_feed['tags_list'] = array();

			// Grab tags from feed
			if (count($this->current_item->get_categories()) > 0) {
				if ($ab_options['feedtags']) {
					foreach ($this->current_item->get_categories() as $cat) {
						$feed_tags[] = $cat->get_label();
					}
				}
			}
			if (count($feed_tags) > 0) $this->ab_logMsg('Tags from feed: ' . implode(', ', $feed_tags), 'debug');

			// Add categories from original source
			$original_categories = array();
			if ($this->current_feed['usepostcats'] == 1) {
				$source_cats = $this->current_item->get_categories();
				if (count($source_cats)) {
					foreach ($source_cats as $category) {
						if (strlen($category->get_label()) < $ab_options['maxtaglen']) {
							if ($this->current_feed['addpostcats'] == 0) {
								if (is_term($category->get_label(), 'category') == 0) continue;
							}
							$original_categories[] = $category->get_label();
						}
					}
				}
			}

			if (count($original_categories) > 0) $this->ab_logMsg('Categories from feed: ' . implode(', ', $original_categories), 'debug');

			// Add all or random categories set by user
			$newcategories = array();
			$feedcategory_ids =  ab_unserialize($this->current_feed['category']);
			if (is_array($feedcategory_ids)) {
				shuffle($feedcategory_ids);
			} else {
				$feedcategory_ids[0] = get_option('default_category');
			}
			
			$newcategories[] = get_term_field('name', $feedcategory_ids[0], 'category');
			$this->ab_logMsg('Main category: ' . $newcategories[0] , 'debug');
			if (count($feedcategory_ids) > 0) {
				for ($i = 1; $i <= count($feedcategory_ids)-1; $i++) {
					if ($this->current_feed['randomcats'] == 0 || (rand(0,2) == 0)) {
						$newcategories[] = get_term_field('name', $feedcategory_ids[$i], 'category');
						$this->ab_logMsg('Additional category: ' . $newcategories[count($newcategories)-1] , 'debug');
					}
				}
			}

			// Add blog categories as tags or additional categories if they exist in the post
			if (($this->current_feed['addcatsastags'] == true) || ($this->current_feed['addothercats'] == true)) {
				$more_categories = array();

				foreach ($this->categories as $cat) {
					if ($cat->name) {
						if ((stristr($this->postinfo['page_content'],$cat->name)) || (stristr($this->postinfo['content'],$cat->name))) {
							if ($this->current_feed['addcatsastags'] == true) {
								$feed_tags[] = $cat->name;
							}
							if ($this->current_feed['addothercats'] == true) {
								$more_categories[] = $cat->name;
							}
						}
					}
				} // end foreach
			}
	
			// Put them all together
			$this->current_feed['feedcategories'] = array_merge($original_categories, $newcategories, $more_categories);
			
			// Temporary hack
			$object_item = array('Object');
			$this->current_feed['feedcategories'] = array_diff($this->current_feed['feedcategories'], $object_item);

			// randomly add additional tags from global and per-feed lists
			$num = rand(0, min((count($this->global_extra_tags) + count($this->current_feed['feed_extra_tags'])/2), 4));
			for ($i = 0; $i <= $num; $i++) {
				if (is_array($this->global_extra_tags)) $feed_tags[] = $this->global_extra_tags[array_rand($this->global_extra_tags)];
			}
			for ($i = 0; $i <= $num; $i++) {
				if ($this->current_feed['feed_extra_tags']) $feed_tags[] = $this->current_feed['feed_extra_tags'][array_rand($this->current_feed['feed_extra_tags'], 1)];
			}

			$feed_tags = array_unique($feed_tags);
			if (count($feed_tags) > 0) $this->ab_logMsg('Tags after adding global tags: ' . implode(', ', $feed_tags), 'debug');

			// Add tags based on the original post
			if ($ab_options['taggingengine']) {
				$original_post_tags = $this->ab_getKeywords($this->postinfo['page_content']);
			}

			// Grab Yahoo Tags
			$yhkeywords = array();
			if ($ab_options['yahootags']) {
				$getyahootags = $this->ab_getYahooTags($this->postinfo['content']);
				if ($getyahootags) $this->ab_matchKeywords($yhkeywords, $getyahootags, '#\w*#', YAHOO_TAGS_WEIGHT, 0);
				arsort($yhkeywords);
				$yhkeywords = array_slice($yhkeywords, 0, $ab_options['maxtags'] * 1.5);
				$yahootags = array_keys($yhkeywords);

			}

			if (count($original_post_tags) > 0) $this->ab_logMsg('Tags from original post: ' . implode(', ', $original_post_tags), 'debug');
			if (count($original_post_tags) > 0) $feed_tags = array_merge($feed_tags, $original_post_tags, $enclosure_tags);
			if (count($yahootags) > 0) $feed_tags = array_merge($feed_tags, $yahootags);
			
			// load notags.txt if not already loaded
			if (count($notags) == 0) {
				// load notags
				$fd = fopen(dirname(__FILE__) . "/notags.txt", "r");
				if ($fd) {
					$notags1 = explode("\n", fread($fd, filesize((dirname(__FILE__) . "/notags.txt"))));
					fclose($fd);

					foreach ($notags1 as $tag) {
						if (strlen($tag) == 0 || substr($tag, 0, 1) == "#") continue;
						$notags[]=$tag;
					}
				}
			}
		
			if (count($feed_tags) > 0) $this->ab_logMsg('Tags before cleanup: ' . implode(', ', $feed_tags), 'debug');

			// Clean up the tags
			$this->ab_logMsg('Tag cleanup...', 'debug');
			if (is_array($feed_tags)) {
				foreach ($feed_tags as $post_tag) {
					$flagged = false;
					$i = 0;
					if (in_array($post_tag, $this->filtered_tags)) {
						continue;
					} else {
							if (strlen($post_tag) < $ab_options['mintaglen']) {
								$flagged = true;
								continue;
							}
							if (strlen($post_tag) > $ab_options['maxtaglen']) {
								$flagged = true;
								continue;
							}
						foreach ($notags as $pattern) {
							$i++;
							if (preg_match('/' . $pattern . '/ism', $post_tag)) {
								$flagged = true;
								//$this->ab_logMsg('Tag "'.$post_tag.'" rejected based on notags.txt', 'debug');
								continue 2; 
							}
						} // end foreach
					} // end if
	
					if ($flagged == false) {
						$this->current_feed['tags_list'][] = strtolower($post_tag);
					}
				} // end foreach
			}
			
			if (is_array($this->current_feed['tags_list'])) {
				shuffle($this->current_feed['tags_list']);
				$this->current_feed['tags_list'] = array_slice($this->current_feed['tags_list'], 0, $ab_options['maxtags'] - rand(0, $ab_options['maxtags']/2));
				$this->ab_logMsg('Final tags list: ' . implode(', ', $this->current_feed['tags_list']), 'debug');
			}
			return true;
		}
		

		//---------------------------------------------------------------
		function ab_itemGetCustomFields() {
			// Custom Fields
			$this->customfields = array();
			$this->ab_logMsg('Custom fields...', 'debug');
			if (is_array($this->current_feed['customFields'])) {
				foreach (array_keys($this->current_feed['customFields']) as $fieldItem) {
					$this->customfields[$fieldItem] = $this->ab_applyTemplate($this->current_feed['customFields'][$fieldItem]);
				}
			}
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemDoSearchReplace() {
		
			// Search and replace
			$this->ab_logMsg('Search and replace...', 'debug');
			if ($this->current_feed['search']) {
				foreach (array_keys($this->postinfo) as $postfield) {
					$i=0;
					foreach ($this->current_feed['search'] as $pattern) {

						$ret = @preg_replace('«'.stripslashes($pattern).'«i', $this->current_feed['replace'][$i], $this->postinfo[$postfield]);
						if ($ret) {
							if (is_array($ret)) {
								foreach ($ret as $retitem) {
									$retitem = $this->ab_applyTemplate($retitem);
								}
							} else {
								$ret = $this->ab_applyTemplate($ret);
							}
							$this->postinfo[$postfield] = $ret;
						} else {
							// $this->ab_logMsg('Error: Invalid regular expression: '.htmlentities(stripslashes($pattern)), 'stop');
						}
						$i++;
					}
				}
			}
			return true;
		}


		//---------------------------------------------------------------
		function ab_itemAddPost() {
			$this->ab_logMsg('Adding post...', 'debug');
			
			// Override all fields with any custom fields set by the user
			if (is_array($this->customfields)) {
				foreach (array_keys($this->customfields) as $field) {
					$this->postinfo[$field] = $this->customfields[$field];
				}
			}

//			if (function_exists(mb_convert_encoding)) {
//				 mb_convert_encoding($post['post_content'], 'ISO-8859-2', 'UTF-8');
//				 mb_convert_encoding($post['post_title'], 'ISO-8859-2', 'UTF-8');
//			}


			// Add fields to post array
			$post = array();

			$post['post_content'] =  $this->postinfo['post'];
			$post['post_title'] = $this->postinfo['title'];
			$post['post_excerpt'] = $this->postinfo['excerpt'];
			$post['post_date'] = $this->postinfo['date'];
			$post['post_status'] = $this->current_feed['poststatus'];

			// Set the author
			$post['post_author'] = $this->postinfo['author_id'];

			foreach (array_keys($this->postinfo) as $postfield) {
				if (strlen($postfield)) {
					if (stristr('post_author|post_date|post_date_gmt|post_content|post_title|post_category|post_excerpt|post_status|comment_status|ping_status|post_password|post_name|to_ping|pinged|post_modified|post_modified_gmt|post_content_filtered|post_parent|guid|menu_order|post_type|post_mime_type', $postfield)) {
						// Special handling for author field
						if ($postfield == 'author') {
							$user = get_userdatabylogin($this->postinfo['author']);
							$uid = $user->ID;
							if ($uid) {
								$userdata = get_userdata($uid);
								$post['post_author'] = $uid;
							} else {
								// Add the user?
							}
						} else {
							$post[$postfield] = $this->postinfo[$postfield];
						}
					}
				}
			}

			//---------------------------------------------------------------------
			// Customization for specific themes

			$theme = get_current_theme();	
			switch ($theme) {
		
				// === Colorlabs Project
				case 'Arthemia Premium':
					if (stristr($this->postinfo['image_path'], 'wp-content')) $this->postinfo['Image'] = $this->postinfo['image_path'];
					break;

				// === WPThemesmarket
				case 'MagazineNews':
					$this->postinfo['image'] = $this->postinfo['image_url'];
					break;

				// === WooThemes
				case 'Ambience':
				case 'BlogTheme':
				case 'Busy Bee':
				case 'Flash News':
				case 'Fresh Folio':
				case 'Fresh News':
				case 'Gazette Edition':
				case 'Geometric':
				case 'Gotham News':
				case 'Live Wire':
				case 'NewsPress':
				case 'OpenAir':
				case 'Over Easy':
				case 'Papercut':
				case 'Original Premium News':
				case 'ProudFolio':
				case 'Snapshot':
				case 'THiCK':
				case 'Typebased':
				case 'Vibrant CMS':
				//cushy
				//wootube
				//forward thinking
				//abstract

					$this->postinfo['image'] = $this->postinfo['image_url'];
					$this->postinfo['preview'] = $this->postinfo['image_url'];

					$this->postinfo['thumb'] = $this->postinfo['thumbnail_urls'][0];
					$this->postinfo['url'] = $this->postinfo['link'];
					
					// Specific theme settings
					if (stristr("Gotham News", $theme)) {
						if (!isset($this->postinfo['post_thumbnail_value'])) $this->postinfo['thumb'] = $this->postinfo['thumbnail_urls'][0];
					}
					
					if (stristr("OpenAir", $theme)) {
						// Videos
						if (!isset($this->postinfo['video'])) {
							$this->postinfo['url'] = $this->postinfo['video'];
							$this->postinfo['video'] = $this->postinfo['title'];
						}
					}
					
					if (stristr("Snapshot", $theme)) {
						if (!isset($this->postinfo['image'])) $this->postinfo['large-image'] = $this->postinfo['image_url'];
					}				
					break;
				
			// === Press75
			case 'Video Elements':
				$post['post_excerpt'] = $this->postinfo['image'];
				$this->postinfo['videolink'] = $this->postinfo['video_urls'][0];
				$this->postinfo['videowidth'] = $this->current_feed['playerwidth'];
				$this->postinfo['videoheight'] = $this->current_feed['playerheight'];
				$this->postinfo['videoembed'] = $this->postinfo['video'];
				$this->postinfo['thumbnail'] = $this->postinfo['thumbnail_urls'][0];
			  break;

			case 'On Demand':
				$post['post_excerpt'] = $this->postinfo['image'];
				$this->postinfo['videoembed'] = $this->postinfo['video'];
				$this->postinfo['thumbnail'] = $this->postinfo['thumbnail_urls'][0];
			  break;

			default:
				$this->postinfo['Image'] = $this->postinfo['image_url'];
				$this->postinfo['Images'] = $this->postinfo['image_urls'];
				
				// Capitalized for Revolution and Options and other themes
				$this->postinfo['Thumbnail'] = $this->postinfo['thumbnail_urls'][0];
				$this->postinfo['Thumbnails'] = $this->postinfo['thumbnail_urls'];
				$this->postinfo['Video'] = $this->postinfo['video_urls'][0];  // Capitalized for Revolution theme
				$this->postinfo['Videos'] = $this->postinfo['video_urls'];
		}

			// we don't want these saved as post metadata
			unset($this->postinfo['author']);
			unset($this->postinfo['author_display_name']);
			unset($this->postinfo['author_email']);
			unset($this->postinfo['author_url']);
			unset($this->postinfo['source']);
			unset($this->postinfo['source_url']);
			unset($this->postinfo['logo_url']);
			unset($this->postinfo['author_id']);
			unset($this->postinfo['content']);
			unset($this->postinfo['post']);
			unset($this->postinfo['title']);
			unset($this->postinfo['excerpt']);
			unset($this->postinfo['date']);
			unset($this->postinfo['poststatus']);
			unset($this->postinfo['category']);
			unset($this->postinfo['categories']);
			//unset($this->postinfo['image']);
			//unset($this->postinfo['images']);
			//unset($this->postinfo['thumbnail']);
			unset($this->postinfo['thumbnails']);
			unset($this->postinfo['video']);
			unset($this->postinfo['videos']);
			unset($this->postinfo['page_content']);
			unset($this->postinfo['description']);
			unset($this->postinfo['tags']);
			unset($this->postinfo['image_url']);
			unset($this->postinfo['image_urls']);
			unset($this->postinfo['thumbnail_url']);
			unset($this->postinfo['thumbnail_urls']);


			//-----------------
			//  Add the post
			if ($_GET['preview'] <> '1') {

				$pid = wp_insert_post($post);

				$this->current_feed['post_count']++;

				// Add categories and tags for this post
				$res = wp_set_object_terms($pid, $this->current_feed['feedcategories'], 'category');
				wp_set_object_terms($pid, $this->current_feed['tags_list'], 'post_tag');
				// Add all other info as custom fields
				foreach (array_keys($this->postinfo) as $itemfield) {
					if (is_array($this->postinfo[$itemfield])) {
						if (EXTRA_IMAGE_FIELDS) {
							for ($j = 0; $j <= 1; $j++) {
								add_post_meta($pid, $itemfield.'_'.$j, $this->postinfo[$itemfield][$j]);
							}
						}
					} else {
						if (strlen($this->postinfo[$itemfield])) {
							if (is_string($this->postinfo[$itemfield])) {
								add_post_meta($pid, $itemfield, $this->postinfo[$itemfield]);
							}
						}
					}
				}
				$editlink = '<a href="'.get_option('siteurl').'/wp-admin/post.php?action=edit&post='.$pid.'" target="_blank">Edit</a>';
				$viewlink = '<a href="'.get_option('siteurl').'/?p='.$pid.'" target="_blank">View</a>';
				$this->ab_logMsg('Post added.&nbsp;&nbsp;&nbsp;&nbsp;'.$editlink.' | '.$viewlink.'<br />', 'added');

			}
			return true;
		}


		//---------------------------------------------                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                mplates)];
		function ab_applyTemplate($templates)
		{

			// Split multiple templates if there are any
			if (preg_match('/<!--\s*template\s*-->/', $templates)) {
				$working_templates = preg_split('/<!--\s*template\s*-->/',$templates);
				$this->ab_logMsg(count($working_templates) . ' templates parsed.', 'debug');
			} else {
				$working_templates[0] = $templates;
			}
		
			$post_template = $working_templates[array_rand($working_templates)];
		
			//if (empty($post_template)) $this->ab_logMsg('Post template is empty.', 'warn');

			////
			// Functions
			//			preg_match_all('/%now\\(([^\\)]*)\\)%/', $post_template, $matches);
			//			$i=0;
			//			foreach ($matches[0] as $match) {
			//				if (empty($matches[1][$i])) {
			//					$post_template = str_replace($match, date(), $post_template);
			//				} else {
			//					$post_template = str_replace($match, date($matches[1][$i]), $post_template);
			//				}
			//				$i++;
			//			}
		
			////
			// Conditional tags - %if:image% <img src=%image%/> %endif%
			preg_match_all('/%if:([^%]+)%(.*)%endif:\\1%/si', $post_template, $matches);
			$i=0;
			foreach ($matches[0] as $match) {
				if (empty($this->postinfo[$matches[1][$i]])) {
					$post_template = str_replace($match, '', $post_template);
				} else {
					$post_template = str_replace($match, $matches[2][$i], $post_template);
				}
				$i++;
			}
		
			////
			// Random sets - [one|two|three]
			preg_match_all("/\[[^\]]+\]/s", $post_template, $matches);
			foreach ($matches as $matchset) {
				foreach ($matchset as $match) {
					$tmp = preg_split("/[\[|\]]/", $match, -1, PREG_SPLIT_NO_EMPTY);
					$selected = $tmp[array_rand($tmp)];
					$post_template = str_replace($match, $selected, $post_template);
				}
			}
		
			////
			// First available sets - {%content%|%description%|%excerpt%}
			preg_match_all("/{[^}]+}/s", $post_template, $matches);
			foreach ($matches as $matchset) {
				foreach ($matchset as $match) {
					$tmp = preg_replace("/{\|*/", "", $match);
					$selected = preg_replace("/\|[^}]*}/", "", $tmp);
					$selected = str_replace("}", "", $selected);
					$post_template = str_replace($match, $selected, $post_template);
				}
			}
		
			////
			// Loops - %foreach:images%  <img src="%images%" /> %endfor:images%
			preg_match_all('/%foreach:([^%]+)%(.*)%endfor:\\1%/si', $post_template, $matches);
			$i=0;
		
			foreach ($matches[0] as $match) {
				$var = $matches[1][$i];
				$subtemplate = $matches[2][$i];
				$values = array();
				$replacement = '';
		
				if (!is_array($this->postinfo[$var])) {
					$values[0] = $this->postinfo[$var];
				} else {
					$values =  $this->postinfo[$var];
				}
		
				foreach ($values as $value) {
					$replacement .= str_ireplace('%'.$var.'%', $value, $subtemplate);
				}
				$i++;
				$post_template = str_replace($match, $replacement, $post_template);
			}


		  ////
			// Namespace elements
			// Examples:  
			//   %gd:rating%
			//   %http://schemas.google.com/g/2005:rating%
			//   %media:group/media:category%
			//   %http://schemas.google.com/g/2005:rating%
			//   %gd:rating@test%
			//   %http://schemas.google.com/g/2005:rating@test%
			//   %media:group/category@test%

			// Grab variable placeholders for this pattern
			preg_match_all("/%((?:http:\/\/[^:]*)?\w*):([^@%]*)@?(\w*)%/s", $post_template, $matches);
			

			$placeholders = $matches[0];
			$namespaces = $matches[1];
			$elements = $matches[2];
			$attributes = $matches[3];


			// Loop through each placeholder
			$i=0;
			if (count($placeholders)) {
				foreach ($placeholders as $placeholder) {

					// Get the primary (first) namespace
					if (stristr($placeholder, 'http://')) {						
						$namespace = $namespaces[$i];
					} else {
						$namespace = $this->rssmodules[strtolower($namespaces[$i])];
					}
							
					
					// Get the element
					If (!strstr($elements[$i], '/')) {
						// Simple element: %media:content%
						$element = $elements[$i];
					} else {
						// Element with subelements: 
						// group/media:content
						// or group/content
						// or group/http://namespace.com/ns-definition:content
						
						// Parse elements into subnamespaces/subelements
						preg_match('/([^\/]*)\/((?:http:\/\/[^:]*)?\w*:)?(\w*)/i', $elements[$i], $elems_parsed);
						$element = $elems_parsed[1];
						$sub_ns = rtrim($elems_parsed[2], ':');
						$sub_elem = $elems_parsed[3];
						
						if (!stristr($sub_ns, 'http://')) {
							$sub_ns = $this->rssmodules[strtolower($sub_ns)];
						}
					}

				
					// Get the attribute if there is one
						$attribute = $attributes[$i];
						
					// Call get_item_tags on the feed item
					$item_tags = $this->current_item->get_item_tags($namespace, $element);

					
					// Parse out the data we need

					if (empty($sub_elem)) {
						// If there is only a simple element (i.e., media:content)
						if (empty($attribute)) {
							// e.g. %media:content%
							$the_data = $item_tags[0]['data'];
						} else {
							// e.g. %media:content@url%
							$the_data = $item_tags[0]['attribs'][''][$attribute];						
						}
						
					} else {
						
						// If there are subelements
						if (empty($attribute)) {
							// e.g. %media:group/media:content%
							$the_data = $item_tags[0]['child'][$sub_ns][$sub_elem][0]['data'];
						} else {
							// e.g. %media:group/media:content@url%
							$the_data = $item_tags[0]['child'][$sub_ns][$sub_elem][0]['attribs'][''][$attribute];
						}
					}

					// Do the replacement
					$post_template = str_ireplace($placeholder, $the_data, $post_template);
					$i++;
				}
			}


			// Replace all remaining variables with the actual values
			foreach (array_keys($this->postinfo) as $variable) {
				if (!empty($this->postinfo[$variable])) {
					if (is_array($this->postinfo[$variable])) {
						$this->postinfo[$variable] = array_merge($this->postinfo[$variable]);
						$delim = '&nbsp;';
						$var = implode($delim, $this->postinfo[$variable]);
					} else {
						$var = $this->postinfo[$variable];
					}
					$post_template = str_ireplace('%'.$variable.'%', $var, $post_template);
				} else {
				}
			}
		
			// Remove any remaining unmatched variables
			if (preg_match('/%[^%\s]{3,}%/', $post_template)) $this->ab_logMsg('Unmatched variables found in post template.', 'debug');
			//$post_template = preg_replace('/%[^%\s]*%/', '',$post_template);
		

			return $post_template;
		}


		//---------------------------------------------------------------
		// extract significant keywords from the given content
		function ab_getKeywords($content)
		{

			global $ab_options;
			$keywords = array();
			if ($content) {

				$content = str_replace("8217", "", $content);
				$content = str_replace("8220", "", $content);

				// Make the content a bit smaller to work with
				$content = preg_replace("#\s+#", " ", $content);

				// And strip off any footer junk
				$content = preg_replace('/(<div\\sid\\s?=\\s?"footer">.*)/sm', '', $content);

				//  Search for keywords //

				// Meta tags
				$this->ab_matchKeywords($keywords, $content, '#<meta\s+name[^=]*=[^"]*"keywords"\s+content[^=]*=[^"]*"([^"]+)"#', META_KEYWORDS_WEIGHT, 1);
				$this->ab_matchKeywords($keywords, $content, '#<meta\s+content[^=]*=[^"]*"([^"]+)"\s+name[^=]*=[^"]*"keywords"#im', META_KEYWORDS_WEIGHT, 1);

				// H1, H2, and H3 headings
				$this->ab_matchKeywords($keywords, $content, '#<h1>(.*?)</h1>#s', H1_WEIGHT, 1);
				$this->ab_matchKeywords($keywords, $content, '#<h2>(.*?)</h2>#s', H2_WEIGHT, 1);
				$this->ab_matchKeywords($keywords, $content, '#<h3>(.*?)</h3>#s', H3_WEIGHT, 1);

				// rel tags
				$this->ab_matchKeywords($keywords, $content, '#<a\s+href[^=]*=[^"]*"[^"]*\/([^"\/?]*)"\s+rel[^=]*=[^"]*"tag"[^>]*>([^<]*)<\/a>#', REL_TAGS_WEIGHT, 1);
				// link text
				$this->ab_matchKeywords($keywords, $content, '#\<a[^\>]*\>(.*?)\</a\>#', LINK_TEXT_WEIGHT, 1);
				// URL tags
				$this->ab_matchKeywords($keywords, $content, '#\/(category|wiki|tags?)\/([^\/=\s\"\'<]*)#', URL_TAGS_WEIGHT, 2);
				// Link title
				$this->ab_matchKeywords($keywords, $content, '#<a[^>]*title="([^"]*)"#', LINK_TITLE_WEIGHT, 1);
				// Alt tags
				$this->ab_matchKeywords($keywords, $content, '#\<img\s.*alt="([^"]*)"#', ALT_TAGS_WEIGHT, 1);
				// Bold words
				$this->ab_matchKeywords($keywords, $content, '#\<img\s.*alt="([^"]*)"#', BOLD_WORD_WEIGHT, 1);

				$content = strip_tags($content);
				$content = preg_replace("#\s+#", " ", $content);

				// Other misc matches that produce good results //

				// Single words or two 3-10 char words next to each other
				// This gets the bulk of the keywords on the page
				$this->ab_matchKeywords($keywords, $content, '#[a-z0-9\.-]{3,10}|\W([a-z0-9\.-]{3,10}\s[a-z0-9\.-]{3,20})#', 1, 1);

				// 3-5 char upper-case words
				$this->ab_matchKeywords($keywords, $content, '#\b[A-Z]{3,5}\b#', 1, 0);

				// Capitalized words
				$this->ab_matchKeywords($keywords, $content, '#[^\.\?!]\s([A-Z][[a-z0-9\.-]{4,20})#', 2, 1);

				// Words in quotes
				$this->ab_matchKeywords($keywords, $content, '#\"([a-zA-Z\s-]{3,25})\"#', 3, 1);

				// ly+word
				$this->ab_matchKeywords($keywords, $content, '#\w{4,15}ly\s\w{8,20}#', 3, 0);

				// Two capitalized words next to each other
				$this->ab_matchKeywords($keywords, $content, '#[A-Z][[a-z0-9\.-]{4,8}\s[A-Z][a-z0-9\.-]{3,20}#', 3, 0);

				// Word plus some common conjunctions/prepositions, then another word
				$this->ab_matchKeywords($keywords, $content, '#(\w{4,10}\s(?:a(?:[ts])|o(?:ff|[nr]|ut)|the|i[ft]|by|[fn]like|yet){1,2}\s\w{3,20})#', 2, 1);

				// Words that follow the, my, our, her, his, their
				$this->ab_matchKeywords($keywords, $content, '#(?:the|my|our|her|his|their)\s(\w{4,20})#', 4, 1);

				// "a word word"
				$this->ab_matchKeywords($keywords, $content, '#a\s[a-z0-9\.-]{3,9}\s[a-z0-9\.-]{3,12}#', 4, 0);


				// Add weight to words that are common tags (disable to make script faster)

				$wordslist = array_keys($keywords);
				if ($wordslist) {
					if (count($tags) == 0) {

						// load tags
						$fd = @fopen(dirname(__FILE__) . "/tags.txt", "r");
						if ($fd) {
							while (!feof($fd)) {
								$buffer = fgets($fd, 20);
								$tags[] = trim($buffer);
							}
							fclose($fd);
						}
					}

					foreach ($wordslist as $word) {
						foreach ($tags as $tag) {
							if ($word == $tag) {
								$keywords[$word] += TAGS_TXT_WEIGHT;
							}					
						}
					}
				}
			
				arsort($keywords);

				// Trim it down but leave extras there to work with
				$keywords = array_slice($keywords, 0, $ab_options['maxtags'] * 1.5);

				return array_keys($keywords);
			}
		}


		//---------------------------------------------------------------
		function ab_matchKeywords(&$keywords, $content, $regex, $weight, $matchindex)
		{
			global $ab_options;
			if ($weight == 0) return;
			$matches = array();

			// Check the content for matches
			preg_match_all($regex, $content, $matches);
			if (count($matches) > 0) {
				foreach ($matches[$matchindex] as $key => $match) {
					$words = explode(',', strip_tags($match));
					foreach ($words as $word) {
						// Clean up keywords and format to follow a common format
						// We are using hyphens between keywords here
						$word = preg_replace("#[\+_\s\.\,\\\/\&]#", "-", trim(strip_tags($word)));
						$word = preg_replace('#[^a-zA-Z0-9-]#', '', $word);
						$word = str_replace("---", "-", $word);
						$word = str_replace("--", "-", $word);
						$word = htmlentities(strtolower($word));
						if (!is_numeric($word)) {
							if (strlen($word) >= 3) {
								// Add as a keyword
								$word = trim(preg_replace('/[^a-zA-Z0-9\.,-]/', '', $word));
								if (!array_key_exists($word, $keywords)) {
									$ln = strlen($word);
									if ($ln <= $ab_options['maxtaglen']) {
										// Give bonus for longer tags, unless they are over 15 characters
										$lengthbonus = 1;
										if ($ln  <= 15)
										$lengthbonus = ($ln / 2);
										// Add word
										$keywords[$word] = $weight + $lengthbonus;
									}
								} else {
									$keywords[htmlentities(strtolower($word))] += $weight;
								}
							} // end if
						} // end if
					} // end foreach
				} // end foreach
			}// end if
		} // end function


		//---------------------------------------------------------------
		// Returns a comma-separated list of tags
		function ab_getYahooTags($content){
			global $ab_options;
			
			if (empty($content)) {
				$this->ab_logMsg('Empty content sent to Yahoo Taging API.', 'debug');
				return;
			}
			$stripped = array();
			$url = "http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);	
			curl_setopt($ch, CURLOPT_POSTFIELDS, "appid=". $ab_options['yahooappid'] ."&query=null&context=" . $content);
			$result = curl_exec ($ch);
			curl_close ($ch);
		
			if (stristr($result, 'errors were detected')) {
					$this->ab_logMsg('Yahoo Taging API error: ' .$result, 'debug');
					$stripped = '';
			} else {
				$stripped = strip_tags($result,'<result>');
				$stripped = preg_replace("/<\/result>/i",",",$stripped);
				$stripped = str_replace(" ", "-", $stripped);
				$stripped = str_replace(",", " ", $stripped);
			}
			return $stripped;
		}


		//---------------------------------------------------------------
		function ab_logMsg($message, $icon = '') {
			global $ab_options;
			$icon = strtolower($icon);
			
			// Debug messages
			if ($icon == 'debug') {
				if ($ab_options['showdebug']) {
					if ($ab_options['logging']) {
						$this->ab_initlogger();
						$this->logger->log(strip_tags($message), PEAR_LOG_DEBUG);
					}
					echo '<br /><font color="gray">'.$message.'</font>';
				}
			} else {

				// Normal messages
				if ($this->show_output) {
					if (isset($icon)) {
						echo '<br /><img style="vertical-align: middle;" src="'.ab_pluginURL().'/img/'.$icon.'.png" />&nbsp;&nbsp;';
					}
					echo $message;
				}

				// Log normal messages in the debug log as notices if enabled
				$this->ab_initlogger();
				if ($ab_options['logging']) $this->logger->log(strip_tags($message), PEAR_LOG_DEBUG);
			}
		}
		
		
		function ab_initlogger() {
			if (!is_object($autoblogged->logger)) {
				
				if (file_exists(ab_plugin_dir().'/log.php')) {
					require_once(ab_plugin_dir().'/log.php');
				} else {
					require_once(ab_plugin_dir().'/Log.php');
				}
				$conf = array('mode' => 0600, 'timeFormat' => '%X %x');
				$this->logger = &Log::singleton('file', dirname(__FILE__).'/_debug.log', '', $conf);
			}
			
		}
			

		
		function ab_wpfooterIntercept()
		{
			$this->ab_shutdownIntercept();
		
		}


		function ab_akismetntercept()
		{
			$this->ab_shutdownIntercept();
		}
		
		
		//---------------------------------------------------------------
		// Used to trigger the scheduler
		function ab_shutdownIntercept()
		{
			$this->ab_logMsg('Checking schedule at ' . date('d/m/yy G:i:s'), 'debug');
			global $ab_options;
			$ab_options = ab_getOptions();
			if (time() >= $ab_options['lastupdate'] + ($ab_options['interval'] * 100)) {
				$this->ab_processFeeds();
			}
		} // end function

	} // end class
} // end if


// Used to intercept XMLRPC pings
//  Request format:

//	<methodCall>
//	<methodName>weblogUpdates.ping</methodName>
//	<params>
//	  <param>
//	    <value><string>BLOG TITLE HERE</string></value>
//	  </param>
//	  <param>
//	    <value><string>http://www.BLOG-URL-HERE.com</string></value>
//	  </param>
//	</params>
//	</methodCall>
//


//---------------------------------------------------------------
if (!function_exists('autoblogged_xmlrpc')) {
	function autoblogged_xmlrpc ($args = array ())
	{
		$args['weblogUpdates.ping'] = 'autoblogged_ping';
		return $args;
	}
}


//---------------------------------------------------------------
if (!function_exists('autoblogged_ping')) {
	function autoblogged_ping ($args)
	{
		$max_xmlrpc_interval = 3600;
		if ($ab_options['accept_xmlrpc_pings']) {
			$sql = 'SELECT id, last_ping FROM '.ab_tableName() .' WHERE url LIKE '.$args[1].'%';
			$feeds = $wpdb->get_results($sql, 'ARRAY_A');

			if (count($feeds)) {
				foreach ($feeds as $feed) {
					if (time() > $this->current_feed['last_update'] + ($max_xmlrpc_interval * 100)) {
						$this->ab_processFeeds($this->current_feed['id'], true);
						return array('flerror' => false, 'message' => 'Thanks for the ping.');
					} else {
						return array('flerror' => true, 'message' => 'Not enough time has passed since your last ping.');
					}
				}
			} else {
				return array('flerror' => true, 'message' => 'Your blog is not registered with our service. Please contact the administrator for details.');
			}
		}
	}
}


//---------------------------------------------------------------
function ab_createClass() {
	// Create class instance
	if (class_exists('autoblogged')) {
		global $autoblogged;
		global $wp_version;
		
		$autoblogged = new autoblogged();
		// Upgrade Check
		$installed_ver = get_option( "autoblogged_db_version" );
		if ($installed_ver < 2) {
			ab_installOnActivation();
		}

		if ($wp_version >= AB_WP_28) {
			$adminpage = 'ab-admin';
		} elseif ($wp_version >= AB_WP_27) {
			$adminpage = 'ab-admin27';
		} else {
			$adminpage = 'ab-admin25';
		}
		
		if (is_admin()) require_once(dirname(__FILE__).'/'.$adminpage.'.php');
	}
}


//---------------------------------------------------------------
// Main page
if (!function_exists('ab_FeedsPage')) {
	function ab_FeedsPage() {
		global $wp_version;
		ab_createClass();

		if ($wp_version >= AB_WP_28) {
			$adminpage = 'ab-admin';
		} elseif ($wp_version >= AB_WP_27) {
			$adminpage = 'ab-admin27';
		} else {
			$adminpage = 'ab-admin25';
		}
		
		switch ($_GET['action']) {

			case 'edit':
			require_once(dirname(__FILE__).'/'.$adminpage.'.php');
			ab_showEditFeedPage();
			break;

			case 'run':
			if (is_numeric($_GET['_fid'])) $feed_id = $_GET['_fid'];
			global $autoblogged;
			$autoblogged->ab_processFeeds($feed_id, true);
			break;
			default:
			ab_showFeedsPage();
			break;
		}
		return;
	}
}


//---------------------------------------------------------------
// Tag options
if (!function_exists('ab_TagOptionsPage')) {
	function ab_TagOptionsPage() {
		ab_createClass();
		ab_showTagOptionsPage();
	}
}

//---------------------------------------------------------------
// Filtering
if (!function_exists('ab_FilteringPage')) {
	function ab_FilteringPage() {
		ab_createClass();
		ab_showFilteringPage();
	}
}

//---------------------------------------------------------------
// Settings
if (!function_exists('ab_SettingsPage')) {
	function ab_SettingsPage() {
		ab_createClass();
		ab_showSettingsPage();
	}
}

//---------------------------------------------------------------
// Support
if (!function_exists('ab_SupportPage')) {
	function ab_SupportPage() {
		ab_createClass();
		ab_showSupportPage();
	}
}

ab_createClass();
?>