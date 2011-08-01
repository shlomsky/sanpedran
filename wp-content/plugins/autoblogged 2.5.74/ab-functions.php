<?PHP

/*
AutoBlogged ab-functions.php

*/

// Get options from DB
	function ab_getOptions() {
		global $ab_options;
		if (count($ab_options) == 0) {
			require_once(ab_plugin_dir().'/defaults.php');
			$autoblogged_options = array("tags" => $randomly_add_these_tags,
			"interval" => "5000",
			"lastupdate" => time(),
			"useragent" => $http_user_agent,
			"referer" => $http_referrer,
			"keywords_blacklist" => $keywords_blacklist,
			"domains_blacklist" => $url_blacklist,
			"mintaglen" => $minimum_tag_length,
			"maxtaglen" => $maximum_tag_length,
			"maxtags" => $maximum_tags_per_post,
			"feedtags" => $use_original_tags_from_feed,
			"posttags" => $visit_source_url,
			"yahootags" => $get_yahoo_tags,
			"yahooappid" => $yahoo_app_id,
			"notags" => $do_not_use_these_as_tags,
			"running" => $autoblogged_enabled,
			"mintime" => $minimum_time_between_updates,
			"maxtime" => $maximum_time_between_updates,
			"filterbylink" => $match_link,
			"filterbytitle" => $match_title,
			"taggingengine" => $use_internal_tagging_engine,
			"sn" => $serial_number,
			"updatecheck" => $check_for_updates,
			"minexcerptlen" => $minimum_excerpt_length,
			"maxexcerptlen" => $maximum_excerpt_length,
			"excerpt_type" => $excerpt_type,
			"maxtitlelen" => $maximum_title_length,
			"longtitlehandling" => $long_title_handling,
			"skipcaps" => $skip_titles_in_all_caps,
			"skipmultiplepunctuation" => $skip_titles_with_multiple_punctuation_marks,
			"rss_cache_timeout" => $rss_cache_timeout,
			"showdebug" => $show_debug,
			"logging" => $logging,
			"last_update_check" => '',
			);
			$savedOptions = get_option('autoblogged_options');
			if (!empty($savedOptions)):
			foreach ($savedOptions as $key => $option) {
				if (is_string($option)) {
					if (strstr($option, ":{")) {
						$option =ab_unserialize($option);
					}
				}
				$autoblogged_options[$key] = $option;
			}
			endif;
			update_option('autoblogged_options', $autoblogged_options);
			$ab_options = ab_arrayStripSlashes($autoblogged_options);
		} 
		return $ab_options;
	}


	// Save options to DB
	function ab_saveOptions(){
		global $ab_options;
		update_option('autoblogged_options', $ab_options);
	}
	
// Activate the plugin and create/upgrade the table as necessary
function ab_installOnActivation($force_upgrade = false) {
	global $wpdb;
	$installed_ver = get_option('autoblogged_db_version');

	// Run if installing for the first time or if upgrading from a previous version
	if ($installed_ver != DB_SCHEMA_AB_VERSION || $force_upgrade == true) {
		$sql = "CREATE TABLE " . ab_tableName() . " (
		`id` mediumint(9) NOT NULL auto_increment,
		`title` varchar(75) NOT NULL,
		`type` tinyint(4) NOT NULL,
		`url` text NOT NULL,
		`category` text,
		`enabled` tinyint(1),
		`addothercats` tinyint(1) NOT NULL,
		`addcatsastags` tinyint(1) NOT NULL,
		`tags` varchar(255) NOT NULL,
		`includeallwords` varchar(255) NOT NULL,
		`includeanywords` varchar(255) NOT NULL,
		`includephrase` varchar(255) NOT NULL,
		`includenowords` varchar(255) NOT NULL,
		`searchfor` text NOT NULL,
		`replacewith` text NOT NULL,
		`templates` text NOT NULL,
		`poststatus` varchar(10) NOT NULL,
		`customfield` longtext NOT NULL,
		`customfieldvalue` longtext NOT NULL,
		`saveimages` tinyint(1) NOT NULL,
		`createthumbs` tinyint(1) NOT NULL,
		`playerwidth` smallint(6) NOT NULL,
		`playerheight` smallint(6) NOT NULL,
		`uselinkinfo` tinyint(1) NOT NULL,
		`useauthorinfo` tinyint(1) NOT NULL,
		`customplayer` varchar(255) NOT NULL,
		`taggingengine` tinyint(1) NOT NULL,
		`randomcats` tinyint(1) NOT NULL,
		`usepostcats` tinyint(1) NOT NULL,
		`addpostcats` tinyint(1) NOT NULL,
		`author` text NOT NULL,
		`alt_author` text NOT NULL,
		`schedule` tinyint(1) NOT NULL,
		`updatefrequency` tinyint(4) NOT NULL,
		`post_processing` tinyint(1) NOT NULL,
		`max_posts` tinyint(4) NOT NULL,
		`posts_ratio` tinyint(4) NOT NULL,
		`last_updated` date NOT NULL,
		`update_countdown` tinyint(4) NOT NULL,
		`last_ping` date NOT NULL,
		`stats` text default NULL,
		`usefeeddate` tinyint(1) NOT NULL,
		UNIQUE KEY `id` (`id`)
		);";
		
		require_once(ABSPATH . "wp-admin/upgrade-functions.php");
		$alterations = dbDelta($sql);
		//											echo "<ol>\n";
		//											foreach($alterations as $alteration) echo "<li>$alteration</li>\n";
		//											echo "</ol>\n";

		//if (!empty($wpdb->last_error)) __d($wpdb->last_error, 'Last SQL error', 'blue');

		if (count($alterations) == 0) {
			if ($force_upgrade == true) {
				echo '<div id="dbupgrade" class="updated fade"><p><strong>'.__("Database Upgrade: ").'</strong>Your AutoBlogged database is already up-to-date.</div>';
			}
		} else {
			$sql = 'ALTER TABLE `'.ab_tableName().'` CHANGE `url` `url` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL';
			$ret = $wpdb->query($sql);
			//if (!empty($wpdb->last_error)) __d($wpdb->last_error, 'Last SQL error', 'purple');

			$sql = 'ALTER TABLE `'.ab_tableName().'` CHANGE `category` `category` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL';
			$ret = $wpdb->query($sql);
			//if (!empty($wpdb->last_error)) __d($wpdb->last_error, 'Last SQL error', 'orange');

			$sql = 'ALTER TABLE `'.ab_tableName().'` CHANGE `searchfor` `searchfor` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL';
			$ret = $wpdb->query($sql);
			
			$sql = 'ALTER TABLE `'.ab_tableName().'` CHANGE `replacewith` `replacewith` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL';
			$ret = $wpdb->query($sql);
			
			
			//Now do a check to make sure the update succeeded
			foreach ($wpdb->get_col("DESC ".ab_tableName(), 0) as $column ) {
				if ($column == 'last_ping') { // using a new column name from the latest upgrade
					update_option("autoblogged_db_version", DB_SCHEMA_AB_VERSION);
					echo '<div id="dbupgrade" class="updated fade"><p><strong>'.__("Database Upgraded: ").'</strong>Your AutoBlogged database has just been upgraded to version '.DB_SCHEMA_AB_VERSION.'.</div>';
					$upgraded=true;
					continue;
				}
			}
			if (!$upgraded) {
				//if (function_exists('wp_nonce_url')) $navlink = wp_nonce_url($_SERVER['PHP_SELF'].'?page=AutoBloggedSupport&upgrade_db=1', 'autoblogged-nav');
				echo '<div id="sn-warning" class="error"><p><strong>'.__("Database Error: ").'</strong>Warning: Unable to install or upgrade the database. Please contact technical support or <a href="'.$navlink.'">click here</a> to try again.</div>';
			}

			//change permissions of cache dir
			@chmod(ab_plugin_dir() . '/cache', 0765);
		}
	} // end if
} // end function

function ab_splitList($data)
{
	$quoted = array();

	// Pull out any quoted strings
	preg_match_all('/(?:[\x22])([^\x22\r\n]*)(?:[\x22])/', $data, $matches);
	// Pull all the matches out of the original string
	foreach ($matches[0] as $match) {
		$data = str_replace($match, "", $data);
	}

	// save all the matches
	foreach ($matches[1] as $match) {
		$quoted[] = preg_replace("/\x22\x0A\x0D/","", $match);
	}

	// Replace commas with newlines
	$data= preg_replace('/[,]+/', "\n", $data);

	// Split by line
	$keywords = preg_split("/[\r\n]+/", $data, -1, PREG_SPLIT_NO_EMPTY);
	$keywords = array_merge((array)$keywords, (array)$quoted);
	$keywords = array_unique(ab_arrayTrim($keywords));

	return $keywords;
}

function ab_getFeedURL($type, $data, $maxlen=500)
{
	// Adjust these queries to fine-tune your search, use different languages, etc.
	if ($type <> 1) {
		$data = urlencode($data);
		switch ($type) {
			case 2:
			$url = 'blogsearch.google.com/blogsearch_feeds?q=' . $data . '&num=20&output=rss&safe=active&hl=en&lr=lang_en';
			break;
			case 3:
			$url = 'feeds.technorati.com/search/' . $data . '?authority=authority&language=en';
			break;
			case 4:
			$url = 'www.blogdigger.com/search?q=' . $data . '&sortby=date&type=rss';
			break;
			case 5:
			$url = 'blogpulse.com/rss?query=' . $data . '&sort=date&operator=';
			break;
			case 6:
			$url = 'search.live.com/results.aspx?q='.$data.'20site:spaces.live.com%20%20meta:search.market(en-US)%20&mkt=en-US&format=rss';
			break;
			case 7:
			$url = 'news.search.yahoo.com/news/rss?p='.$data;
			break;
			case 8:
			$data = str_replace(' ', ',', $data);
			$url = 'api.flickr.com/services/feeds/photos_public.gne?tags='.$data.'&lang=en-us&format=atom';
			break;
			case 9:
			$url = 'gdata.youtube.com/feeds/api/videos?vq='.$data.'&max-results=20&lr=en';
			break;
			case 10:
			$url = 'video.yahoo.com/rss/video/search?p='.$data;
			break;
		}
		$url = 'http://' . $url;
	} else {
		$url = $data;
	}
	if (strlen($url) > $maxlen) $url = substr($url, 0, $maxlen).'...';
	$url = stripslashes($url);
	return $url;
}




function ab_pluginURL()
{
	return dirname(get_option('siteurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__));
}

function ab_plugin_dir()
{
	return dirname(__FILE__);
}

// Returns the number of items in $needlearray that appear in $data
function ab_countItemsFound($data, $needlearray)
{
	foreach ($needlearray as $needle) {
		$counter += preg_match_all('/\\b'.$needle.'\\b/i', $data, $matches);
	}
	return $counter;
}

function ab_arrayStripSlashes($value)
{
	if(isset($value)) {
		if (is_array($value)) {
			$value = array_map( __FUNCTION__, $value);
		} else {
			$value = stripslashes($value);
		}

		return $value;
	}
}

function ab_arrayTrim($data) {
	if (is_array($data))
	return array_map(__FUNCTION__, $data);
	if (is_string($data))
	return trim($data);
	return $data;
}

function ab_serialize($object) {
	if (is_array($object)) $object = array_values(array_filter($object, 'strlen'));
	$serialized = serialize(ab_arrayEncode($object));
	return $serialized;
}

function ab_unserialize($string) {
	$unserialized = ab_arrayDecode(unserialize($string));
	return $unserialized;
}

function ab_arrayEncode($data) {
	if (is_array($data))	return array_map("ab_arrayEncode", $data);
	if (is_string($data))	$data = base64_encode($data);
	return $data;
}
function ab_arrayDecode($data) {
	if (is_array($data))	return array_map("ab_arrayDecode", $data);
	if (is_string($data))	$data = base64_decode(base64_decode($data));
	return $data;
}

function ab_tableName() {
	global $wpdb;
	return $wpdb->prefix . "autoblogged";
}
	

function __d($val, $label = null, $color = "Gray") {
	global $debuglog, $ab_options;
	$debuglog[] = $val;
	$ab_options['showdebug'] == false;
	//error_log($val);

	if ($label == 'Debug') $color = '#6B8E23';
	if (true) {
		echo '<br><font color="'.$color.'">';
		if (isset($label)) echo $label.': ';
		if (is_array($val)) {
			echo '<pre>';
			print_r($val);
			echo '</pre>';
		} else {
			echo $val;
		}
		echo '</font>';
		echo "\n";
	}
}

// Returns the contents of the url or, if $save is set, saves to a file in the
// upload dir.
// Returns array:
//   file - File path if saved as a file
//   url - URL to saved file
//   error - Error message if any
//   contents - The contents retrieved from the URL
//   http_code - The HTTP result code

function ab_httpFetch($url, $referer = '', $timeout = 15) {
	global $ab_options;
	static $use_curl;
	static $depth;
	
	$depth++;
	if ($depth > 5) {
		$result['error'] = 'Too many redirects.';
		$depth = 0;
		return FALSE;
	}
	if (!isset($referer)) $referer = $ab_options['referer'];
	// Determine whether to use cURL or not
	if (!isset($use_curl)) {
		$use_curl = (bool)(in_array('curl', get_loaded_extensions()) && strlen(ini_get('open_basedir'))==0 && ini_get('safe_mode') == false);
	}

	// Security check the URL to ensure we are only grabbing via http or https
	if (!stristr($url, 'http://')) {
		$result['error'] = "Invalid URL: ".$url;
		$depth = 0;
		return $result;
	}
	
	$urlParsed = parse_url($url);
    
  // Handle SSL connection request
  if ($urlParsed['scheme'] == 'https') {
    $port = 443;
  } else {
    $port = 80;
  }

	if ($use_curl) {
		// Initialize cURL
		$ch = curl_init();

		@curl_setopt($ch, CURLOPT_HEADER,         true );
		@curl_setopt($ch, CURLOPT_NOBODY,         false );

		@curl_setopt($ch, CURLOPT_TIMEOUT,        $timeout );
		@curl_setopt($ch, CURLOPT_USERAGENT,      $ab_options['useragent'] );
		@curl_setopt($ch, CURLOPT_URL,            $url );
		@curl_setopt($ch, CURLOPT_REFERER,        $referer );

		@curl_setopt($ch, CURLOPT_VERBOSE,        false );
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		@curl_setopt($ch, CURLOPT_MAXREDIRS,      5 );
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

		// Get the target contents
		$content = curl_exec($ch);
		$contents = explode("\r\n\r\n", $content);

		// Get the request info
		$status  = curl_getinfo($ch);

		// Store the contents
		$result['content'] = $contents[count($contents) - 1];
		
		// Parse the headers
		$result['headers'] = ab_parseHeaders($contents[count($contents) - 2]);

		// Store the error (if any)
		$result['error'] = curl_error($ch);

		// Close PHP cURL handle
		curl_close($ch);

	} else {
		
		// Get a file pointer
		$fp = @fsockopen($urlParsed['host'], $port, $errorNumber, $errorString, $timeout);
	
		if (!$fp) {
			$result['error'] = 'Unable to open socket connection: ' . $errorString . ' (' . $errorNumber . ')';
			$depth = 0;
			return $result;
		}

		// Set http header
		$requestHeader  = "GET " . $url . "  HTTP/1.1\r\n";
		$requestHeader .= "Host: " . $urlParsed['host'] . "\r\n";
		$requestHeader .= "User-Agent: " . $ab_options['useragent'] . "\r\n";
		$requestHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$requestHeader .= "Referer: " . $referer . "\r\n";

		$requestHeader .= "Connection: close\r\n\r\n";

		fwrite($fp, $requestHeader);
		$responseHeader = '';
		$responseContent = '';

		do {
			$responseHeader .= fread($fp, 1);
		} while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));

		// Parse the headers
		$headers = ab_parseHeaders($responseHeader);

		// Handle redirects
		if ($headers['status'] == '301' || $headers['status'] == '302')
		{
			if ($curRedirect < 5)
			{
				// Get the new target URL
				$newUrlParsed = parse_url($headers['location']);

				if ($newUrlParsed['host'])
				{
					$newTarget = $headers['location'];
				}
				else
				{
					$newTarget = $urlParsed['schema'] . '://' . $urlParsed['host'] . '/' . $headers['location'];
				}

				// Increase the redirect counter
				$curRedirect++;
				$result = ab_httpFetch($newTarget);

			} else {
				$result['error'] = 'Too many redirects.';
				$depth = 0;
				return FALSE;
			}
		} else {
			// Get remaining contents
			if ($headers['transfer-encoding'] != 'chunked')
			{
				while (!feof($fp)) {
					$responseContent .= fgets($fp, 128);
				}
			} else {
				// Get the contents (chunked)
				while ($chunkLength = hexdec(fgets($fp))) {
					$responseContentChunk = '';
					$readLength = 0;

					while ($readLength < $chunkLength) {
						$responseContentChunk .= fread($fp, $chunkLength - $readLength);
						$readLength = strlen($responseContentChunk);
					}

					$responseContent .= $responseContentChunk;
					fgets($fp);
				} // end while
			} // end if

			// Store the target contents
			$result['content'] = chop($responseContent);
		}
		$result['headers'] = $headers;
	}
	$depth = 0;
	return $result;
}

function ab_parseHeaders($headers) {
	//$headers = explode("\r\n", $headers);

	// Validate headers
	if(!eregi($match = "^http/[0-9]+\\.[0-9]+[ \t]+([0-9]+)[ \t]*(.*)\$", $headers, $matches)) return false;

	// Set the status header
	$return_headers['status'] = $matches[1];
	
	// Location header
	if (preg_match('/location:\\s*(.*)/i', $headers, $matches)) {
		$return_headers['location'] = $matches[1];
	}
	
	// Content-type header
	if (preg_match('/content-type:\\s*(.*)/i', $headers, $matches)) {
		$return_headers['content-type'] = $matches[1]; 
	}
		
	$return_headers['raw_headers'] = $headers;
	return $return_headers;
}

function ab_getEmbeddedVideo($link, $width, $height, $type) {
	switch ($type) {
		case 'flash':
		case 'fmedia':
			$embed = '<object type="application/x-shockwave-flash" data="'.$link.'" width="'.$width.'" height="'.$height.'">';
			$embed .= '<param name="movie" value="'.$link.'" />';
			$embed .= '<a href="'.$link.'">'.$link.'</a>';
			$embed .= '</object>';
			break;
			
		case 'mp3':
			$embed = '<object type="audio/mpeg" data="'.$link.'" width="'.$width.'" height="'.$height.'">';
  		$embed .= '<param name="src" value="'.$link.'">';
  		$embed .= '<param name="autoplay" value="false">';
  		$embed .= '<a href="'.$link.'">'.$link.'</a>';
			$embed .= '</object>';
			break;
		
		case 'quicktime':
			$embed .= '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
			$embed .= 'width="'.$width.'" height="'.$height.'">';
			$embed .= '<param name="src" value="'.$link.'" />';
			$embed .= '<param name="controller" value="true" /><param name="autoplay" value="false" />';
			$embed .= '<!--[if gte IE 7]> <!-->';
			$embed .= '<object type="video/quicktime" data="'.$link.'" width="'.$width.'" height="'.$height.'">';
			$embed .= '  <param name="controller" value="true" /><param name="autoplay" value="false" />';
			$embed .= 'alt : <a href="'.$link.'">'.$link.'</a>';
			$embed .= '<a href="'.$link.'>'.$link.'</a>';
			$embed .= '</object><!--<![endif]-->';
			$embed .= '<!--[if lt IE 7]>';
			$embed .= '<a href="'.$link.'">'.$link.'</a>';
			$embed .= '<![endif]--></object>';
			break;
		
		case 'wmedia':
			$embed .= '<object type="video/x-ms-wmv" data="'. $link .'"';
			$embed .= ' width="'.$width.'" height="'.$height.'">';
			$embed .= ' <param name="src" value="'. $link .'" />';
			$embed .= ' <param name="autoStart" value="0" />';
			$embed .= '<a href="'.$link.'>'.$link.'</a>';
			$embed .= ' </object>';
		break;

	}
	
	return $embed;
}


//function ab_getVersion() {
//	global $wp_version;
//		if (version_compare($wp_version, AB_WP_27) === 1) return AB_WP_27;
//	
//	return 0;
//}
?>