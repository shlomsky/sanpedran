<?php
/*
Plugin Name: Iframe Embedder
Plugin URI: http://de77.com/wordpress-iframe-embedder
Description: Lets you embed an iframe in a post
Version: 1.0
Author: de77.com
Author URI: http://de77.com
Licence: MIT
*/

function replacer($matches)
{	
	$temp = explode(' ', $matches[1]);
    $count = count($temp);
   	  
    $height = $temp[$count-1];
    $width = $temp[$count-2];
  
    unset($temp[$count-1]);
    unset($temp[$count-2]);
    $url = trim(implode(' ', $temp), '"');
    
    if (strpos($width, 'px') === false and strpos($width, '%') === false)
    {
    	$width .= 'px'; 
    }
    if (strpos($height, 'px') === false and strpos($height, '%') === false)
    {
    	$height .= 'px'; 
    }
	    
    return '<iframe src="' . $url . '" style="width: ' . $width . '; height: ' . $height . '"></iframe>';
}

function parse_iframe($text)
{
	return preg_replace_callback("@(?:<p>\s*)?\[iframe\s*(.*?)\](?:\s*</p>)?@", 'replacer', $text);
}

add_filter('the_content', 'parse_iframe');