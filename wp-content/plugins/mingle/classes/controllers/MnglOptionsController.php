<?php

class MnglOptionsController
{ 
  function MnglOptionsController()
  {
  }

  function route()
  {
    $action = (isset($_POST['action'])?$_POST['action']:$_GET['action']);
    if($action=='process-form')
      return $this->process_form();
    else if($action=='add_default_friends_to_all_users')
      $this->add_default_friends_to_all_users();
    else
      return $this->display_form();
  }

  function display_form()
  {
    global $mngl_options, $mngl_app_helper;
    
    if(MnglUser::is_logged_in_and_an_admin())
    {    
      if(!$mngl_options->setup_complete)
        require_once(MNGL_VIEWS_PATH . '/shared/must_configure.php');
      
      require_once(MNGL_VIEWS_PATH . '/mngl-options/form.php');
    }
  }

  function process_form()
  {
    global $mngl_options, $mngl_app_helper;
    
    if(MnglUser::is_logged_in_and_an_admin())
    {
      $errors = array();
      
      $errors = $mngl_options->validate($_POST,$errors);
      
      $mngl_options->update($_POST);
      
      if( count($errors) > 0 )
        require(MNGL_VIEWS_PATH . '/shared/errors.php');
      else
      {
        $mngl_options->store();
        require_once(MNGL_VIEWS_PATH . '/mngl-options/options_saved.php');
      }
      
      if(!$mngl_options->setup_complete)
        require_once(MNGL_VIEWS_PATH . '/shared/must_configure.php');
      
      require_once(MNGL_VIEWS_PATH . '/mngl-options/form.php');
    }
  }
  
  function display_default_friend_drop_down($default_friend='')
  {
    global $mngl_options;
    
    if(MnglUser::is_logged_in_and_an_admin())
      require(MNGL_VIEWS_PATH . '/mngl-options/default_friend.php');
  }
  
  function add_default_friends_to_all_users()
  {
    global $mngl_friends_controller;
    
    $mngl_friends_controller->add_default_friends_to_all_users();
    
    require(MNGL_VIEWS_PATH . '/mngl-options/default_friends_added.php');
      
    $this->display_form();
  }
}
?>
