<?php

class MnglOptionsHelper
{
  function wp_pages_dropdown($field_name, $page_id)
  {
    $pages = MnglAppHelper::get_pages();
    $field_value = $_POST[$field_name];
    
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="mngl-dropdown mngl-pages-dropdown">
        <option>&nbsp;</option>
      <?php
        foreach($pages as $page)
        {
          ?>
          <option value="<?php echo $page->ID; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $page->ID) or (!isset($_POST[$field_name]) and $page_id == $page->ID))?' selected="selected"':''); ?>><?php echo $page->post_title; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  function profile_name_dropdown($field_name, $field_value)
  {
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="mngl-dropdown mngl-profile-name-dropdown">
        <option value="fullname" <?php  echo (((isset($_POST[$field_name]) and $_POST[$field_name] == 'fullname') or (!isset($_POST[$field_name]) and $field_value == 'fullname'))?' selected="selected"':''); ?>><?php _e('Full Name', 'mingle'); ?>&nbsp;</option>
        <option value="screenname" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == 'screenname') or (!isset($_POST[$field_name]) and $field_value == 'screenname'))?' selected="selected"':''); ?>><?php _e('Screen Name', 'mingle'); ?>&nbsp;</option>
      </select>
    <?php
  }
  
  function field_visibility_dropdown($field_name, $field_value)
  {
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="mngl-dropdown mngl-field-visibility-dropdown">
        <option value="public" <?php  echo (((isset($_POST[$field_name]) and $_POST[$field_name] == 'public') or (!isset($_POST[$field_name]) and $field_value == 'public'))?' selected="selected"':''); ?>><?php _e('Public', 'mingle'); ?>&nbsp;</option>
        <option value="private" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == 'private') or (!isset($_POST[$field_name]) and $field_value == 'private'))?' selected="selected"':''); ?>><?php _e('Private', 'mingle'); ?>&nbsp;</option>
        <option value="hidden" <?php  echo (((isset($_POST[$field_name]) and $_POST[$field_name] == 'hidden') or (!isset($_POST[$field_name]) and $field_value == 'hidden'))?' selected="selected"':''); ?>><?php _e('Hidden', 'mingle'); ?>&nbsp;</option>
      </select>
    <?php
  }
  
  function users_dropdown($field_name, $user_id)
  {
    $users = MnglUtils::get_raw_users();
      
    $field_value = $_POST[$field_name];
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="mngl-dropdown mngl-users-dropdown">
        <option>&nbsp;</option>
      <?php
        foreach($users as $user)
        {
          ?>
          <option value="<?php echo $user->ID; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $user->ID) or (!isset($_POST[$field_name]) and $user_id == $user->ID))?' selected="selected"':''); ?>><?php echo $user->user_login; ?>&nbsp;(<?php echo $user->user_nicename; ?>)&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
  
  function users_multiselect($field_name, $user_array)
  {
    $users = MnglUtils::get_raw_users();

    $field_value = $_POST[$field_name];
    
    $check_selected = (is_array($user_array) or is_array($_POST[$field_name]));
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="mngl-dropdown mngl-user-multiselect" multiple="multiple">
      <?php
        foreach($users as $user)
        {
          $selected = '';

          if( ( is_array($user_array) and in_array($user->ID,$user_array) ) or 
              ( is_array($_POST[$field_name]) and in_array($user->ID,$_POST[$field_name]) ) )
            $selected = ' selected="selected"';
          ?>
          <option value="<?php echo $user->ID; ?>"<?php echo $selected ?>><?php echo $user->user_login; ?>&nbsp;(<?php echo $user->user_nicename; ?>)&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
}
?>
