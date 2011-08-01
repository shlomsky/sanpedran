<?php

class MnglUsersController
{
  function display_login_form()
  {
    global $mngl_options, $mngl_blogurl;

    extract($_POST);
    
    $redirect_to = ( (isset($redirect_to) and !empty($redirect_to) )?$redirect_to:get_permalink( $mngl_options->activity_page_id ) );
    $redirect_to = apply_filters( 'mngl-login-redirect-url', $redirect_to );
      
    if(!empty($mngl_options->login_page_id) and $mngl_options->login_page_id > 0)
      $login_url = get_permalink($mngl_options->login_page_id);
    else
      $login_url = $mngl_blogurl . '/wp-login.php';
    
    if(!empty($mngl_options->signup_page_id) and $mngl_options->signup_page_id > 0)
      $signup_url = get_permalink($mngl_options->signup_page_id);
    else
      $signup_url = $mngl_blogurl . '/wp-login.php?action=register';
    
    if(MnglUser::is_logged_in_and_visible())
      require( MNGL_VIEWS_PATH . '/shared/already_logged_in.php' );
    else
    {
      if( !empty($mngl_process_login_form) and !empty($errors) )
        require( MNGL_VIEWS_PATH . "/shared/errors.php" );

      require( MNGL_VIEWS_PATH . '/shared/login_form.php' );
    }
  }
  
  function process_login_form()
  {
    global $mngl_options, $mngl_profiles_controller;

    $errors = MnglUser::validate_login($_POST,array());
    
    $errors = apply_filters('mngl-validate-login', $errors);

    extract($_POST);
    
    if(empty($errors))
    {
      $creds = array();
      $creds['user_login'] = $log;
      $creds['user_password'] = $pwd;
      $creds['remember'] = $rememberme;

      if(!function_exists('wp_signon'))
        require_once(ABSPATH . WPINC . '/user.php');
      
      wp_signon($creds);

      $redirect_to = ((!empty($redirect_to))?$redirect_to:get_permalink($mngl_options->activity_page_id));

      MnglUtils::wp_redirect($redirect_to);
      exit;
    }
    else
      $_POST['errors'] = $errors;
  }
  
  function display_signup_form()
  {
    global $mngl_options, $mngl_blogurl;
    
    $process = MnglAppController::get_param('mngl-process-form');
    
    if(empty($process))
    {
      if(MnglUser::is_logged_in_and_visible())
        require( MNGL_VIEWS_PATH . '/shared/already_logged_in.php' );
      else
        require( MNGL_VIEWS_PATH . '/shared/signup_form.php' );
    }
    else
      $this->process_signup_form();
  }
  
  function process_signup_form()
  {
    $errors = MnglUser::validate_signup($_POST,array());
    
    $errors = apply_filters('mngl-validate-signup', $errors);
    
    extract($_POST);
    
    if(empty($errors))
    {
      $new_password = apply_filters('mngl-create-signup-password', MnglUtils::wp_generate_password( 12, false ));
      $user_id = wp_create_user( $user_login, $new_password, $user_email );
      $user = MnglUser::get_stored_profile_by_id($user_id);
      
      if($user)
      {
        if(isset($user_first_name) and !empty($user_first_name))
          $user->first_name = $user_first_name;
        
        if(isset($user_last_name) and !empty($user_last_name))
          $user->last_name = $user_last_name;
          
        $user->sex = $mngl_user_sex;
        
        $user->store(true);
        
        $user->send_account_notifications($new_password);
        
        do_action('mngl-proceess-signup',$user_id);
        
        global $mngl_blogname;
        require( MNGL_VIEWS_PATH . "/mngl-users/signup_thankyou.php" );
      }
      else
        require( MNGL_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( MNGL_VIEWS_PATH . "/shared/errors.php" );
      require( MNGL_VIEWS_PATH . '/shared/signup_form.php' );
    }
  }
}
?>
