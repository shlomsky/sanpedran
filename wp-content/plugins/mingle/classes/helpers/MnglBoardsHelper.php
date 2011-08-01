<?php

class MnglBoardsHelper
{
  function display_message($message_class, $message)
  {
    $hidden = '';

    if(strlen($message) >= 255)
    {
      $hidden = ' class="mngl-hidden"';
      $teaser = mb_substr($message,0,255) . "...";
      $class_suffix = '-fake';
      
      $teaser_class = $message_class . $class_suffix;
      
      ?><span id="<?php echo $teaser_class; ?>"><?php echo MnglBoardsHelper::format_message($teaser) ?> <a href="javascript:mngl_toggle_two_ids('#<?php echo $message_class; ?>','#<?php echo $teaser_class; ?>')"><?php _e('Read More', 'mingle'); ?></a></span><?php
    }
    ?><span id="<?php echo $message_class; ?>"<?php echo $hidden; ?>><?php echo MnglBoardsHelper::format_message($message); ?></span><?php
  }
  
  function format_message($message)
  {
    global $wp_smiliessearch, $wpsmiliestrans;

    $message = stripslashes($message);
    $message = preg_replace("#\n#","<br/>",$message);
    $message = make_clickable($message);
    $message = wptexturize($message);
    $message = convert_smilies($message);
    $message = MnglBoardsHelper::make_tags_clickable($message);
    
    return $message;
  }

  function board_post_url($board_post_id)
  {
    global $mngl_options;

    if(isset($mngl_options->profile_page_id) and $mngl_options->profile_page_id != 0)
    {
      $permalink = get_permalink($mngl_options->profile_page_id);
      $param_char = ((preg_match("#\?#",$permalink))?'&':'?');
      return "{$permalink}{$param_char}mbpost={$board_post_id}";
    }

    return '';
  }
  
  function make_tags_clickable($message)
  {
    global $mngl_options;
    preg_match_all('#@([\w]+)#', $message, $matches);
    
    if(is_array($matches[1]))
    {
      foreach($matches[1] as $index => $username)
      {  
        require_once(ABSPATH . WPINC . '/registration.php');

        if($user_id = username_exists($username) and !in_array($user_id,$mngl_options->invisible_users))
        {
          $user = MnglUser::get_stored_profile_by_screenname($username);
        
          if($user)
          {
            $preg_string = '#('.$matches[0][$index].')#';
            $preg_link   = '<a href="' . $user->get_profile_url() . '">$1</a>';
            $message     = preg_replace( $preg_string, $preg_link, $message );
          }
        }
      }
    }
    return $message;
  }
}
?>
