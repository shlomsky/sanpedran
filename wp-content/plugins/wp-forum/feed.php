<?php
global $wpdb, $wpforum, $user_ID, $user_level;

$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
	} else {
	// before WP 2.6
	require_once($root.'/wp-config.php');
	}
	$wpforum->setup_links();		
	
	
	//if($_GET['topic'] != "all" || !is_numeric($_GET['topic']))
	//	return false;
		
	$topic = $_GET['topic'];
	
	if($topic == "all"){
		$posts = $wpdb->get_results("SELECT * FROM $wpforum->t_posts ORDER BY `date` DESC LIMIT 20 ");
		$title = get_bloginfo('name')."".__("Forum Feed", "wpforum")."";
		$description = __("Forum Feed", "wpforum");
	}
	else{
		$posts = $wpdb->get_results("SELECT * FROM $wpforum->t_posts WHERE parent_id = $topic ORDER BY `date` DESC LIMIT 20 ");
		$description = __("Forum Topic:", "wpforum")." - ".$wpforum->get_subject($topic);
		$title = get_bloginfo('name')." ".__("Forum", "wpforum")." - ".__("Topic: ", "wpforum")." ".$wpforum->get_subject($topic);
	}
	$link = $wpforum->home_url;

		header ("Content-type: application/rss+xml");  
  
		echo ("<?xml version=\"1.0\" encoding=\"".get_bloginfo('charset')."\"?>\n");
		?>
		<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
		<channel>
		<title><?php echo $title;?> </title>
		<description><?php bloginfo('name'); echo " $description";?></description>
		<link><?php echo $link;?></link>
		<language><?php bloginfo('language');?></language>
		<?php

			
		foreach($posts as $post){
		
			$link = $wpforum->get_threadlink($post->parent_id);
			
			$user = get_userdata($post->author_id);
			//$title = __("Topic:", "wpforum")." ".$wpforum->get_subject($post->parent_id);
			$title = $post->subject;
		echo "<item>\n
			<title>" . htmlspecialchars($title) . "</title>\n
			<description>".htmlspecialchars($wpforum->output_filter($post->text, ENT_NOQUOTES))."</description>\n
			<link>".htmlspecialchars($link)."</link>\n
			<author>feeds@r.us</author>\n
			<pubDate>".date("r", strtotime($post->date))."</pubDate>\n
			<guid>".htmlspecialchars($link."&guid=$post->id")."</guid>
			</item>\n\n";
		}
		?>
		</channel>
		</rss>