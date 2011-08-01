<div class="wrap">
<h2 id="mngl_title" style="margin: 10px 0px 0px 0px; padding: 0px 0px 0px 56px; height: 48px; background: url(<?php echo MNGL_URL . "/images/mingle_48.png"; ?>) no-repeat"><?php _e('Mingle: Options', 'mingle'); ?></h2>
<br/>

<form name="mngl_options_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="action" value="process-form">
<?php wp_nonce_field('update-options'); ?>

<h3><?php _e('Mingle Pages', 'mingle'); ?>:</h3>
<span class="description"><?php printf(__('Before you can get going with Mingle, you must configure where Mingle pages on your website will appear. You\'ll want to %1$screate a new page%2$s for each of these pages that mingle needs to work. You should give your page a title and optionally put some content into the page ... just know that once you set the page up here, the page\'s content will not display.', 'mingle'), '<a href="page-new.php">', '</a>'); ?></span>
<table class="form-table">
  <tr class="form-field">
    <td valign="top" style="text-align: right; width: 150px;"><?php _e('Profile Page', 'mingle'); ?>*: </td>
    <td style="width: 150px;">
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->profile_page_id_str, $mngl_options->profile_page_id )?>
    </td>
    <td valign="top" style="text-align: right; width: 150px;"><?php _e('Activity Page', 'mingle'); ?>*: </td>
    <td style="width: 150px;">
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->activity_page_id_str, $mngl_options->activity_page_id )?>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr class="form-field">
    <td valign="top" style="text-align: right;"><?php _e('Profile Edit Page', 'mingle'); ?>*: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->profile_edit_page_id_str, $mngl_options->profile_edit_page_id )?>
    </td>
    <td valign="top" style="text-align: right;"><?php _e('Directory Page', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->directory_page_id_str, $mngl_options->directory_page_id )?>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr class="form-field">
    <td valign="top" style="text-align: right;"><?php _e('Friends Page', 'mingle'); ?>*: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->friends_page_id_str, $mngl_options->friends_page_id )?>
    </td>
    <td valign="top" style="text-align: right;"><?php _e('Login Page', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->login_page_id_str, $mngl_options->login_page_id )?>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr class="form-field">
    <td valign="top" style="text-align: right;"><?php _e('Friend Requests Page', 'mingle'); ?>*: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->friend_requests_page_id_str, $mngl_options->friend_requests_page_id )?>
    </td>
    <td valign="top" style="text-align: right;"><?php _e('Signup Page', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::wp_pages_dropdown( $mngl_options->signup_page_id_str, $mngl_options->signup_page_id )?>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>

<h4><?php _e('Profile Options', 'mingle'); ?>:</h4>
<div class="mngl-options-pane">
<label for="<?php echo $mngl_options->pretty_profile_urls_str; ?>"><input type="checkbox" name="<?php echo $mngl_options->pretty_profile_urls_str; ?>" id="<?php echo $mngl_options->pretty_profile_urls_str; ?>"<?php echo (($mngl_options->pretty_profile_urls)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Pretty Profile Urls','mingle'); ?></label><br/>
<span class="description"><?php _e('When checked, Pretty Profile Urls will allow users to type their screenname following your site\'s domain name for their url. Note, if you do not have Apache rewrite functioning and have not selected something other than "Default" under your General Permalink settings, this will not work.', 'mingle'); ?></span>
</div>

<h4><?php _e('Default Friends', 'mingle'); ?>:</h4>
<div class="mngl-options-pane">
<span class="description"><?php _e('These Users will be added as a friends to all new signups.', 'mingle'); ?></span>
  <table class="form-table mngl-default-friends-table" style="width: auto;">
<?php

  if(count($mngl_options->default_friends) > 0)
  {
    foreach($mngl_options->default_friends as $default_friend)
    {
      $default_friend = (int)$default_friend;
      if($default_friend and !empty($default_friend))
        $this->display_default_friend_drop_down($default_friend);
    }
  }
  
?>
  </table>
  <p><a href="javascript:mngl_add_default_user();" class="button">+ <?php _e('Add a Default Friend', 'mingle'); ?></a></p>
</div>

<h4><?php _e('Invisible Users', 'mingle'); ?>:</h4>
<div class="mngl-options-pane">
<span class="description"><?php _e('Any users checked below will not be visible to Mingle. They won\'t have a profile page, friends, be listed in the directory or show up anywhere in mingle.', 'mingle'); ?></span>
<p><?php MnglOptionsHelper::users_multiselect($mngl_options->invisible_users_str . "[]", $mngl_options->invisible_users); ?><br/><span class="description"><?php _e('Hold down Control Key (the Command Key if you\'re on a Mac) or the Shift Key to select multiple users.', 'mingle'); ?></span></p>
</div>

<h4><?php _e('Field Display Options', 'mingle'); ?>:</h4>
<div class="mngl-options-pane">
<span class="description"><?php _e("Configure the fields you'd like your users to see, and if they will be able to display them on their profiles. <code>Public</code> indicates that the field will be available to the users and that the value they enter into it will show up on their public profiles. <code>Private</code> indicates that the field will be available to the users but the value they enter into it will not show up on their public profiles. <code>Hidden</code> indicates that this field won't be visible to the users or on their public profiles.", 'mingle'); ?></span>
<table class="form-table">
  <tr class="form-field">
    <td valign="top" width="10%"><?php _e('Show Website Field', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::field_visibility_dropdown($mngl_options->show_url_str, $mngl_options->show_url); ?>
    </td>
  </tr>
  <tr class="form-field">
    <td valign="top" width="10%"><?php _e('Show Location Field', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::field_visibility_dropdown($mngl_options->show_location_str, $mngl_options->show_location); ?>
    </td>
  </tr>
  <tr class="form-field">
    <td valign="top"><?php _e('Show Sex Field', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::field_visibility_dropdown($mngl_options->show_sex_str, $mngl_options->show_sex); ?>
    </td>
  </tr>
  <tr class="form-field">
    <td valign="top"><?php _e('Show Bio Field', 'mingle'); ?>: </td>
    <td>
      <?php MnglOptionsHelper::field_visibility_dropdown($mngl_options->show_bio_str, $mngl_options->show_bio); ?>
    </td>
  </tr>
</table>

<?php do_action('mngl_custom_fields'); ?>
</div>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mingle') ?>" />
</p>

</form>

<p><a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&action=add_default_friends_to_all_users"><?php _e('Add Default Friends to Existing Users', 'mingle'); ?></a></p>
</div>
