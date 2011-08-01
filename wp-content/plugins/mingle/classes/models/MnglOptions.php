<?php
class MnglOptions
{
  // Page Setup Variables
  var $profile_page_id;
  var $profile_edit_page_id;
  var $friends_page_id;
  var $friend_requests_page_id;
  var $activity_page_id;
  var $directory_page_id;
  var $login_page_id;
  var $signup_page_id;
  
  var $profile_page_id_str;
  var $profile_edit_page_id_str;
  var $friends_page_id_str;
  var $friend_requests_page_id_str;
  var $activity_page_id_str;
  var $directory_page_id_str;
  var $login_page_id_str;
  var $signup_page_id_str;
  
  // Field Display Variables
  var $show_url;
  var $show_location;
  var $show_sex;
  var $show_bio;
  
  var $show_url_str;
  var $show_location_str;
  var $show_sex_str;
  var $show_bio_str;
  
  // Is the setup sufficiently completed for mingle to function?
  var $setup_complete;
  
  // Activity Types
  var $activity_types;
  
  // Notification Types
  var $notification_types;
  
  // Default Friends -- these guys are added automatically when users sign up
  var $default_friends;
  var $default_friends_str;
  
  // Invisible Users -- these guys aren't visible by Mingle
  var $invisible_users;
  var $invisible_users_str;
  
  // Pretty Profile Urls
  var $pretty_profile_urls;
  var $pretty_profile_urls_str;

  function MnglOptions()
  {
    $this->set_default_options();
  }

  function set_default_options()
  {
    if(!isset($this->profile_page_id))
      $this->profile_page_id = 0;

    if(!isset($this->profile_edit_page_id))
      $this->profile_edit_page_id = 0;

    if(!isset($this->friends_page_id))
      $this->friends_page_id = 0;

    if(!isset($this->friend_requests_page_id))
      $this->friend_requests_page_id = 0;

    if(!isset($this->activity_page_id))
      $this->activity_page_id = 0;

    if(!isset($this->directory_page_id))
      $this->directory_page_id = 0;
      
    if(!isset($this->login_page_id))
      $this->login_page_id = 0;
      
    if(!isset($this->signup_page_id))
      $this->signup_page_id = 0;

    $this->profile_page_id_str         = 'mngl-profile-page-id';
    $this->profile_edit_page_id_str    = 'mngl-profile-edit-page-id';
    $this->friends_page_id_str         = 'mngl-friends-page-id';
    $this->friend_requests_page_id_str = 'mngl-friend-requests-page-id';
    $this->activity_page_id_str        = 'mngl-activity-page-id';
    $this->directory_page_id_str       = 'mngl-directory-page-id';
    $this->login_page_id_str           = 'mngl-login-page-id';
    $this->signup_page_id_str          = 'mngl-signup-page-id';
    
    if( $this->profile_page_id == 0 or
        $this->profile_edit_page_id == 0 or
        $this->friends_page_id == 0 or
        $this->friend_requests_page_id == 0 or
        $this->activity_page_id == 0 )
      $this->setup_complete = 0;
    else
      $this->setup_complete = 1;

    if(!isset($this->show_url))
      $this->show_url = 'public';
    if(!isset($this->show_location))
      $this->show_location = 'public';
    if(!isset($this->show_sex))
      $this->show_sex = 'public';
    if(!isset($this->show_bio))
      $this->show_bio = 'public';
    
    $this->show_url_str           = 'mngl-show-url';
    $this->show_location_str      = 'mngl-show-location';
    $this->show_sex_str           = 'mngl-show-sex';
    $this->show_bio_str           = 'mngl-show-bio';

    if(!isset($this->default_friends))
    {
      if(isset($this->default_friend))
      {
        // Default Friend Migration
        $this->default_friends[] = $this->default_friend;
        unset($this->default_friend);
      }
      else
        $this->default_friends = array();
    }
    
    $this->default_friends_str = 'mngl-default-friends';
    
    if(!isset($this->invisible_users))
      $this->invisible_users = array();
    
    $this->invisible_users_str = 'mngl-invisible-users';
    
    
    if(!isset($this->pretty_profile_urls))
      $this->pretty_profile_urls = false;

    $this->pretty_profile_urls_str = 'mngl-pretty-profile-urls';
  }
  
  function validate($params,$errors)
  {   
    if($params[ $this->profile_page_id_str ] == 0)
      $errors[] = __("The Profile Page Must Not Be Blank.", 'mingle');
      
    if($params[ $this->profile_edit_page_id_str ] == 0)
      $errors[] = __("The Profile Edit Page Must Not Be Blank.", 'mingle');
    
    if($params[ $this->friends_page_id_str ] == 0)
      $errors[] = __("The Friends Page Must Not Be Blank.", 'mingle');
    
    if($params[ $this->friend_requests_page_id_str ] == 0)
      $errors[] = __("The Friend Request Page Must Not Be Blank.", 'mingle');
    
    if($params[ $this->activity_page_id_str ] == 0)
      $errors[] = __("The Activity Page Must Not Be Blank.", 'mingle');

    $errors = apply_filters( 'mngl_validate_options', $errors );

    return $errors;
  }
  
  function update($params)
  {
    $this->profile_page_id         = (int)$params[ $this->profile_page_id_str ];
    $this->profile_edit_page_id    = (int)$params[ $this->profile_edit_page_id_str ];
    $this->friends_page_id         = (int)$params[ $this->friends_page_id_str ];
    $this->friend_requests_page_id = (int)$params[ $this->friend_requests_page_id_str ];
    $this->activity_page_id        = (int)$params[ $this->activity_page_id_str ];
    $this->directory_page_id       = (int)$params[ $this->directory_page_id_str ];
    $this->login_page_id           = (int)$params[ $this->login_page_id_str ];
    $this->signup_page_id          = (int)$params[ $this->signup_page_id_str ];
    
    $this->show_url                = $params[ $this->show_url_str ];
    $this->show_location           = $params[ $this->show_location_str ];
    $this->show_sex                = $params[ $this->show_sex_str ];
    $this->show_bio                = $params[ $this->show_bio_str ];
    
    $this->default_friends         = $params[ $this->default_friends_str ];
    $this->invisible_users         = $params[ $this->invisible_users_str ];
    $this->pretty_profile_urls     = isset($params[ $this->pretty_profile_urls_str ]);
    
    do_action( 'mngl_update_options', $params );
  }
  
  function store()
  {
    // Save the posted value in the database
    delete_option( 'mngl_options' );
    add_option( 'mngl_options', $this);
    
    do_action( 'mngl_store_options' );
  }
  
  /** Allows custom plugins to register activity types. Each type should contain the following fields:
    * $activity_types['cool_activities'] = array( 'name' => 'Cool Activities',
    *                                               'description' => 'These are some really cool activities punk',
    *                                               'message' => '{$owner->screenname} did some cool stuff',
    *                                               'icon' => '/wp-content/plugin/cool-activities/images/cool_plugin.png');
    */
  function set_activity_types()
  {
    $this->activity_types = array();
    $this->activity_types = apply_filters('mngl-activity-types', $this->activity_types);
  }
  
  /** Allows custom plugins to register notification types. Each type should contain the following fields:
    * $notification_types['cool_activities'] = array( 'name' => 'Cool Notifications',
    *                                                   'description' => 'I\'m going to email this guy like none other');
    */
  function set_notification_types()
  {
    $this->notification_types = array();
    $this->notification_types = apply_filters('mngl-notification-types', $this->notification_types);
  }
  
  /** Allows custom plugins to register notification types. Each type should contain the following fields:
    * $notification_types['cool_activities'] = array( 'name' => 'Cool Notifications',
    *                                                   'description' => 'I\'m going to email this guy like none other');
    */
  function set_default_friends()
  {
    $this->default_friends = apply_filters('mngl-default-friends', $this->default_friends);
  }
}
?>
