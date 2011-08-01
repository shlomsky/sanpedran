<?php
/*
	Plugin Name: WP-Forum
	Plugin Author: Fredrik Fahlstad
	Plugin URI:
	Author URI:
	Version: 2.4
*/
/*  Copyright 2008  Fredrik Fahlstad  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


//$plugin_dir = basename(dirname(__FILE__)); 
//load_plugin_textdomain( 'wpforum', ABSPATH.'wp-content/plugins/'. $plugin_dir.'/', $plugin_dir.'/' ); 
include_once("wpf.class.php");

// Short and sweet :)
$wpforum = new wpforum();

// Activating?
register_activation_hook(__FILE__ ,array(&$wpforum,'wp_forum_install'));

add_action("the_content", array(&$wpforum, "go"));
add_action('init', array(&$wpforum,'set_cookie'));
add_filter("wp_title", array(&$wpforum, "set_pagetitle"));
function latest_activity($num = 5){
	global $wpforum;
	return $wpforum->latest_activity($num);
}

?>