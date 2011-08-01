<?php

class MnglUtils
{
  function get_user_id_by_email($email)
  {
    if(isset($email) and !empty($email))
    {
      global $wpdb;
      $query = "SELECT ID FROM {$wpdb->users} WHERE user_email=%s";
      $query = $wpdb->prepare($query, mysql_escape_string($email));
      return (int)$wpdb->get_var($query);
    }
    
    return '';
  }
  
  // load and cache user metadata
  function _load_user_metadata($user_id,$meta_key='',$meta_value='')
  {
    global $wpdb;

    // Cache the user_meta to cut down on DB calls
    static $user_meta;
    
    if(!isset($user_meta) or !is_array($user_meta))
      $user_meta = array();
    
    if(!isset($user_meta[$user_id]) or !is_array($user_meta[$user_id]))
    {
      $meta_sql     = $wpdb->prepare( "SELECT user_id,meta_key,meta_value FROM {$wpdb->usermeta} WHERE user_id=%d", $user_id );
      $meta_results = $wpdb->get_results( $meta_sql );
      $user_meta[$user_id] = array();
      
      foreach ($meta_results as $meta)   
        $user_meta[$user_id][$meta->meta_key] = maybe_unserialize($meta->meta_value);
    }
    
    // Replace override value in cache array
    if(!empty($meta_key) and !empty($meta_value))
    {
      // Strip dashes just like get_usermeta
      $meta_key = str_replace("-","",$meta_key);

      $user_meta[$user_id][$meta_key] = $meta_value;
    }

    return $user_meta[$user_id];
  }
  
  function is_image($filename)
  {
    if(!file_exists($filename))
      return false;

    $file_meta = getimagesize($filename);
    
    $image_mimes = array("image/gif", "image/jpeg", "image/png");
    
    return in_array($file_meta['mime'], $image_mimes);
  }
  
  function rewriting_on()
  {
    $permalink_structure = get_option('permalink_structure');
    
    return ($permalink_structure and !empty($permalink_structure) and !preg_match('#index.php#', $permalink_structure));
  }
  
  // Returns a list of just user data from the wp_users table
  function get_raw_users($where = '', $order_by = 'user_login')
  {
    global $wpdb;

    static $raw_users;
    
    if(!isset($raw_users))
    {
      $where    = ((empty($where))?'':" WHERE {$where}");
      $order_by = ((empty($order_by))?'':" ORDER BY {$order_by}");
      
      $query = "SELECT * FROM {$wpdb->users}{$where}{$order_by}";
      $raw_users = $wpdb->get_results($query);
    }
    
    return $raw_users;
  }

  /* We issue this check because we may want to use the username as a slug at some point */
  function username_is_available( $username )
  {
    global $wpdb, $mngl_blogurl;
  
    // Check username uniqueness against posts, pages and categories
    $query     = "SELECT post_name FROM {$wpdb->posts} WHERE post_name=$s";
    $query     = $wpdb->prepare($query,$username);
    $post_slug = $wpdb->get_var($query);
    
    $query     = "SELECT slug FROM {$wpdb->terms} WHERE slug=%s";
    $query     = $wpdb->prepare($query,$username);
    $term_slug = $wpdb->get_col($query);
  
    if( $post_slug == $username or $term_slug == $username )
      return false;
  
    // Check slug against files on the root wordpress install
    $root_dir = opendir(ABSPATH);
  
    while (($file = readdir($root_dir)) !== false)
    {
      $haystack = strtolower($file);
      if($haystack == $slug)
        return false;
    }
  
    // Check slug against other slugs in the prli links database.
    // We'll use the full_slug here because its easier to guarantee uniqueness.
    if(!function_exists('is_plugin_active'))
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if(is_plugin_active('pretty-link/pretty-link.php'))
    {
      global $prli_utils;
      return $prli_utils->slugIsAvailable($username);
    }
  
    return true;
  }
  
/* PLUGGABLE FUNCTIONS AS TO NOT STEP ON OTHER PLUGINS' CODE */
  function get_currentuserinfo()
  {
    MnglUtils::_include_pluggables('get_currentuserinfo');
    return get_currentuserinfo();
  }

  function get_userdata($id)
  {
    MnglUtils::_include_pluggables('get_userdata');
    return get_userdata($id);
  }

  function get_userdatabylogin($screenname)
  {
    MnglUtils::_include_pluggables('get_userdatabylogin');
    return get_userdatabylogin($screenname);
  }

  function wp_mail($recipient, $subject, $message, $header)
  {
    MnglUtils::_include_pluggables('wp_mail');
    return wp_mail($recipient, $subject, $message, $header);
  }

  function is_user_logged_in()
  {
    MnglUtils::_include_pluggables('is_user_logged_in');
    return is_user_logged_in();
  }

  function get_avatar( $id, $size )
  {
    MnglUtils::_include_pluggables('get_avatar');
    return get_avatar( $id, $size );
  }
  
  function wp_hash_password( $password_str )
  {
    MnglUtils::_include_pluggables('wp_hash_password');
    return wp_hash_password( $password_str );
  }
  
  function wp_generate_password( $length, $special_chars )
  {
    MnglUtils::_include_pluggables('wp_generate_password');
    return wp_generate_password( $length, $special_chars );
  }
  
  function wp_redirect( $location, $status=302 )
  {
    MnglUtils::_include_pluggables('wp_redirect');
    return wp_redirect( $location, $status );
  }
  
  function _include_pluggables($function_name)
  {
    if(!function_exists($function_name))
      require_once(ABSPATH . WPINC . '/pluggable.php');
  }
}
?>