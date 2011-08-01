<?php

class MnglAppController
{
  function MnglAppController()
  {
    add_filter('the_content', array( $this, 'page_route' ), 100);
    add_action('wp_enqueue_scripts', array($this, 'load_scripts'), 1);
    add_action('admin_enqueue_scripts', array($this,'load_admin_scripts'));
    register_activation_hook(MNGL_PATH."/mingle.php", array( $this, 'install' ));
    
    // Used to process standalone requests (make sure mingle_init comes before parse_standalone_request)
    add_action('init', array($this,'mingle_init'));
    add_action('init', array($this,'parse_standalone_request'));
    add_filter('request', array($this,'parse_pretty_profile_url'));
  }

  function setup_menus()
  {
    add_action('admin_menu', array( $this, 'menu' ));
  }
  
  /********* INSTALL PLUGIN ***********/
  function install()
  {
    global $wpdb;
    $db_version = 5; // this is the version of the database we're moving to
    $old_db_version = get_option('mngl_db_version');

    $friends_table           = $wpdb->prefix . "mngl_friends";
    $friend_requests_table   = $wpdb->prefix . "mngl_friend_requests";
    $board_posts_table       = $wpdb->prefix . "mngl_board_posts";
    $board_comments_table    = $wpdb->prefix . "mngl_board_comments";

    $charset_collate = '';
    if( $wpdb->has_cap( 'collation' ) )
    {
      if( !empty($wpdb->charset) )
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
      if( !empty($wpdb->collate) )
        $charset_collate .= " COLLATE $wpdb->collate";
    }

    if($db_version != $old_db_version)
    {
      $this->before_migration($old_db_version);
      
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      /* Create/Upgrade Friends Table */
      $sql = "CREATE TABLE {$friends_table} (
                id int(11) NOT NULL auto_increment,
                user_id int(11) NOT NULL,
                friend_id int(11) NOT NULL,
                status varchar(255) DEFAULT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY user_id (user_id),
                KEY friend_id (friend_id),
                KEY status (status)
              ) {$charset_collate};";
    
      dbDelta($sql);
      
      /* Create/Upgrade Friend Requests Table */
      $sql = "CREATE TABLE {$friend_requests_table} (
                id int(11) NOT NULL auto_increment,
                user_id int(11) NOT NULL,
                friend_id int(11) NOT NULL,
                friend_record_a_id int(11) NOT NULL,
                friend_record_b_id int(11) NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY user_id (user_id),
                KEY friend_id (friend_id),
                KEY friend_record_a_id (friend_record_a_id),
                KEY friend_record_b_id (friend_record_b_id)
              ) {$charset_collate};";
    
      dbDelta($sql);

      /* Create/Upgrade Board Posts Table */
      $sql = "CREATE TABLE {$board_posts_table} (
                id int(11) NOT NULL auto_increment,
                owner_id int(11) NOT NULL,
                author_id int(11) NOT NULL,
                message text DEFAULT NULL,
                type varchar(255) DEFAULT 'post',
                source varchar(255) DEFAULT NULL,
                visibility varchar(255) DEFAULT 'public',
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY owner_id (owner_id),
                KEY author_id (author_id),
                KEY type (type),
                KEY source (source),
                KEY visibility (visibility)
              ) {$charset_collate};";
    
      dbDelta($sql);
      
      /* Create/Upgrade Board Comments Table */
      $sql = "CREATE TABLE {$board_comments_table} (
                id int(11) NOT NULL auto_increment,
                author_id int(11) NOT NULL,
                message text DEFAULT NULL,
                board_post_id int(11) NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY author_id (author_id),
                KEY board_post_id (board_post_id)
              ) {$charset_collate};";
    
      dbDelta($sql);

      $this->after_migration($old_db_version);
    }

    /***** SAVE DB VERSION *****/
    delete_option('mngl_db_version');
    add_option('mngl_db_version',$db_version);
  }
  
  function before_migration($curr_db_version)
  {
    // Nothing here yet
  }
  
  function after_migration($curr_db_version)
  {
    if(isset($curr_db_version) and !empty($curr_db_version))
    {
      if($curr_db_version < 4)
      {
        global $mngl_options;
        
        if($mngl_options->display_name_type == 'fullname')
        {
          global $wpdb;

          $query = "SELECT ID FROM {$wpdb->prefix}users";
          $user_ids = $wpdb->get_col($query,0);
          
          foreach ($user_ids as $user_id)
          {
            $profile = MnglUser::get_stored_profile_by_id($user_id);
            if($profile)
            {
              if( isset($profile->first_name) and !empty($profile->first_name) and
                  isset($profile->last_name) and !empty($profile->last_name) )
              {
                $profile->full_name = "{$profile->first_name} {$profile->last_name}";
                $profile->store(true);
              }
              else if( isset($profile->first_name) and !empty($profile->first_name) )
              {
                $profile->full_name = $profile->first_name;
                $profile->store(true);
              }
              else if( isset($profile->last_name) and !empty($profile->last_name) )
              {
                $profile->full_name = $profile->last_name;
                $profile->first_name = $profile->last_name;
                $profile->store(true);
              }
            }
          }
        }
      }
    }
  }
  
  function menu()
  {
    global $mngl_options_controller, $mngl_update;
  
    add_menu_page(__('Mingle', 'mingle'), __('Mingle', 'mingle'), 8, 'mingle-options', array($mngl_options_controller,'route'), MNGL_URL . "/images/mingle_16.png");
    //add_submenu_page( 'mingle-options', __('Mingle Plus'), __('Mingle Plus'), 8, 'mingle-pro-settings', array( $mngl_update, 'pro_cred_form') );
  }
  
  // Routes for wordpress pages -- we're just replacing content here folks.
  function page_route($content)
  {
    global $post, 
           $mngl_options, 
           $mngl_profiles_controller, 
           $mngl_boards_controller,
           $mngl_friends_controller, 
           $mngl_users_controller,
           $mngl_board_post;
    
    switch( $post->ID )
    {  
      case $mngl_options->activity_page_id:
        // Start output buffering -- we want to return the output as a string
        ob_start();
        $mngl_profiles_controller->activity();
        // Pull all the output into this variable
        $content = ob_get_contents();
        // End and erase the output buffer (so we control where it's output)
        ob_end_clean();
        break;
      case $mngl_options->profile_page_id:
        ob_start();
        if($this->get_param('mbpost'))
          $mngl_boards_controller->display_board_post($mngl_board_post->get_one($this->get_param('mbpost'),true));
        else
          $mngl_profiles_controller->profile($this->get_param('u'));
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->directory_page_id:
        ob_start();
        $mngl_profiles_controller->directory($this->get_param('mdp'));
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->profile_edit_page_id:
        ob_start();
        $mngl_profiles_controller->edit();
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->friends_page_id:
        ob_start();
        $mngl_friends_controller->list_friends($this->get_param('mdp'), $this->get_param('u'));
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->friend_requests_page_id:
        ob_start();
        $mngl_friends_controller->list_friend_requests();
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->login_page_id:
        ob_start();
        $mngl_users_controller->display_login_form();
        $content = ob_get_contents();
        ob_end_clean();
        break;
      case $mngl_options->signup_page_id:
        ob_start();
        $mngl_users_controller->display_signup_form();
        $content = ob_get_contents();
        ob_end_clean();
        break;
    }
    
    return $content;
  }  

  function load_scripts()
  {
    $this->enqueue_mngl_scripts();
  }
  
  function load_admin_scripts()
  {
    $admin_pages = apply_filters('mngl_admin_pages',array('mingle-options'));
    
    $curr_page = $_GET['page'];

    if(in_array($curr_page,$admin_pages))
      $this->enqueue_mngl_scripts();
  }
  
  function enqueue_mngl_scripts()
  {
    if(MnglUtils::rewriting_on())
      $mngl_js = $mngl_blogurl . '/mingle-js/mingle.js';
    else
      $mngl_js = $mngl_blogurl . '/index.php?mingle_js=mingle';

    wp_enqueue_style( 'mingle',  MNGL_CSS_URL . '/mingle.css' );
    wp_enqueue_script( 'jquery-elastic', MNGL_JS_URL . '/jquery.elastic.js', array('jquery') );
    wp_enqueue_script( 'jquery-qtip', MNGL_JS_URL . '/jquery.qtip-1.0.0-rc3.min.js', array('jquery') );
    wp_enqueue_script( 'mingle', $mngl_js, array('jquery','jquery-elastic','jquery-qtip') );

    do_action('mngl_enqueue_scripts');
  }
  
  function mingle_js()
  {
    header('Content-type: application/javascript');
    require_once( MNGL_JS_PATH . '/mingle.js.php' );
  }

  // The tight way to process standalone requests dogg...
  function parse_standalone_request()
  {
    global $mngl_users_controller;

    $plugin     = $this->get_param('plugin');
    $action     = $this->get_param('action');
    $controller = $this->get_param('controller');
    $mingle_js  = $this->get_param('mingle_js');

    if( !empty($plugin) and $plugin == 'mingle' and 
        !empty($controller) and !empty($action) )
    {
      $this->standalone_route($controller, $action);
      exit;
    }
    else if( MnglUtils::rewriting_on() and preg_match("#/mingle-js/(.+)\.js.*#", $_SERVER['REQUEST_URI'], $matches) )
    {
      $this->standalone_route('js', $matches[1]);
      exit;
    }
    else if( !MnglUtils::rewriting_on() and !empty($mingle_js) )
    {
      $this->standalone_route('js', $mingle_js);
      exit;
    }
    else if( isset( $_POST ) and isset( $_POST['mngl_process_login_form'] ) )
      $mngl_users_controller->process_login_form();
  }
  
  function parse_pretty_profile_url($query_vars)
  {
    global $mngl_options, $mngl_blogurl;

    if( MnglUtils::rewriting_on() and $mngl_options->pretty_profile_urls )
    {
      require_once(ABSPATH . WPINC . '/registration.php');
      if( isset($query_vars['name']) and
          !empty($query_vars['name']) and
          username_exists( $query_vars['name'] ) )
      {
        // figure out the pagename var
        $pagename = get_permalink($mngl_options->profile_page_id);
        $pagename = str_replace( $mngl_blogurl, '', $pagename);
        $pagename = preg_replace( '#^/#', '', $pagename);
        $pagename = preg_replace( '#/$#', '', $pagename);

        // Resolve the pagename to the profile page
        $query_vars['pagename'] = $pagename;
        
        // Artificially set the GET variable
        $_GET['u'] = $query_vars['name'];
        
        // Unset the indeterminate query_var['name'] now that we have a pagename
        unset($query_vars['name']);
      }
    }
    
    return $query_vars;
  }
  
  // Routes for standalone / ajax requests
  function standalone_route($controller, $action)
  {
    global $mngl_friends_controller, $mngl_boards_controller, $mngl_profiles_controller, $mngl_options_controller;
    
    if($controller=='friends')
    {
      if($action=='friend_request')
        $mngl_friends_controller->friend_request($this->get_param('user_id'), $this->get_param('friend_id'));
      if($action=='delete_friend')
        $mngl_friends_controller->delete_friend($this->get_param('user_id'), $this->get_param('friend_id'));
      else if($action=='accept_friend')
        $mngl_friends_controller->accept_friend($this->get_param('request_id'));
      else if($action=='ignore_friend')
        $mngl_friends_controller->ignore_friend($this->get_param('request_id'));
      else if($action=='search')
        $mngl_friends_controller->list_friends($this->get_param('mdp'),$this->get_param('u'),true,$this->get_param('q'));
    }
    else if($controller=='boards')
    {
      if($action=='post')
        $mngl_boards_controller->post($this->get_param('owner_id'), $this->get_param('author_id'), $this->get_param('message'));
      else if($action=='comment')
        $mngl_boards_controller->comment($this->get_param('board_post_id'), $this->get_param('author_id'), $this->get_param('message'));
      else if($action=='delete_post')
        $mngl_boards_controller->delete_post($this->get_param('board_post_id'));
      else if($action=='delete_comment')
        $mngl_boards_controller->delete_comment($this->get_param('board_comment_id'));
      else if($action=='older_posts')
        $mngl_boards_controller->show_older_posts($this->get_param('u'),$this->get_param('mdp'),$this->get_param('loc'));
    }
    else if($controller=='activity')
    {
      if($action=='post')
        $mngl_boards_controller->post($this->get_param('owner_id'), $this->get_param('author_id'), $this->get_param('message'),true);
      else if($action=='comment')
        $mngl_boards_controller->comment($this->get_param('board_post_id'), $this->get_param('author_id'), $this->get_param('message'),true);
      else if($action=='delete_post')
        $mngl_boards_controller->delete_post($this->get_param('board_post_id'),true);
      else if($action=='delete_comment')
        $mngl_boards_controller->delete_comment($this->get_param('board_comment_id'),true);
    }
    else if($controller=='profile')
    {  
      if($action=='delete_avatar')
        $mngl_profiles_controller->delete_avatar($this->get_param('user_id'));
      else if($action=='search')
        $mngl_profiles_controller->directory($this->get_param('mdp'),true,$this->get_param('q'));
    }
    else if($controller=='options')
    {
      if($action=='add_default_user')
        $mngl_options_controller->display_default_friend_drop_down();
    }
    else if($controller=='js')
    {
      if($action=='mingle')
        $this->mingle_js();
    }
  }
  
  function load_language()
  {
    $path_from_plugins_folder = str_replace( ABSPATH, '', MNGL_PATH ) . '/i18n/';
    
    load_plugin_textdomain( 'mingle', $path_from_plugins_folder );
  }
  
  function mingle_init()
  {
  	add_filter('get_avatar', array($this,'override_avatar'), 10, 4);
    add_filter('get_comment_author_url', array($this,'override_author_url'));
  }
  
  function override_author_url($url)
  {
    global $comment;
    
    $user = MnglUser::get_stored_profile_by_id($comment->user_id, false);
    
    if($user)
      return $user->get_profile_url();
    else
      return $url;
  }
  
  function override_avatar($avatar, $id_or_email, $size, $default)
  {
    $user_id = false;

    if( is_object($id_or_email) and $id_or_email->comment_author_email )
      $user_id = (int)MnglUtils::get_user_id_by_email($id_or_email->comment_author_email);
    else if( is_numeric($id_or_email) )
      $user_id = (int)$id_or_email;
    else if( is_string($id_or_email) )
      $user_id = (int)MnglUtils::get_user_id_by_email($id_or_email);
    
    if(!$user_id or empty($user_id))
      return $avatar;

    $avatar = MnglAppHelper::get_avatar_img_by_id($user_id, $size, $avatar);
    return $avatar;
  }

  // Utility function to grab the parameter whether it's a get or post
  function get_param($param)
  {
    return (isset($_POST[$param])?$_POST[$param]:$_GET[$param]);
  }
  
  function get_param_delimiter_char($link)
  { 
    return ((preg_match("#\?#",$link))?'&':'?');
  }
}
?>
