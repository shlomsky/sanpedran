<div class="profile-edit-form">
<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data" method="post">
  <input type="hidden" name="action" id="action" value="process_form" />
  <input type="hidden" name="<?php echo $mngl_user->id_str; ?>" id="<?php echo $mngl_user->id_str; ?>" value="<?php echo $mngl_user->id; ?>" />
  <input type="hidden" name="<?php echo $mngl_user->screenname_str; ?>" id="<?php echo $mngl_user->screenname_str; ?>" value="<?php echo $mngl_user->screenname; ?>" />
  <h3><?php _e('Information', 'mingle'); ?>:</h3>
  <table width="100%" class="profile-edit-table">
    <tr>
      <td valign="top"><?php _e('First Name', 'mingle'); ?>:</td>
      <td valign="top"><input type="input" name="<?php echo $mngl_user->first_name_str; ?>" id="<?php echo $mngl_user->first_name_str; ?>" value="<?php echo $mngl_user->first_name; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <tr>
      <td valign="top"><?php _e('Last Name', 'mingle'); ?>:</td>
      <td valign="top"><input type="input" name="<?php echo $mngl_user->last_name_str; ?>" id="<?php echo $mngl_user->last_name_str; ?>" value="<?php echo $mngl_user->last_name; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <tr>
      <td valign="top"><?php _e('Email', 'mingle'); ?>:</td>
      <td valign="top"><input type="input" name="<?php echo $mngl_user->email_str; ?>" id="<?php echo $mngl_user->email_str; ?>" value="<?php echo $mngl_user->email; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <?php global $mngl_options; ?>
    <?php if($mngl_options->show_bio != 'hidden') { ?>
    <tr>
      <td valign="top"><?php _e('Bio', 'mingle'); ?>:<?php echo (($mngl_options->show_bio == 'private')?"<span class=\"description\"> (".__("private", 'mingle').")</span>":''); ?></td>
      <td valign="top"><textarea name="<?php echo $mngl_user->bio_str; ?>" id="<?php echo $mngl_user->bio_str; ?>" class="mngl-profile-edit-field"><?php echo stripslashes($mngl_user->bio); ?></textarea></td>
    </tr>
    <?php } ?>
    <?php if($mngl_options->show_url != 'hidden') { ?>
    <tr>
      <td valign="top"><?php _e('URL', 'mingle'); ?>:<?php echo (($mngl_options->show_url == 'private')?"<span class=\"description\"> (".__("private", 'mingle').")</span>":''); ?></td>
      <td valign="top"><input type="input" name="<?php echo $mngl_user->url_str; ?>" id="<?php echo $mngl_user->url_str; ?>" value="<?php echo $mngl_user->url; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <?php } ?>
    <?php if($mngl_options->show_location != 'hidden') { ?>
    <tr>
      <td valign="top"><?php _e('Location', 'mingle'); ?>:<?php echo (($mngl_options->show_location == 'private')?"<span class=\"description\"> (".__("private", 'mingle').")</span>":''); ?></td>
      <td valign="top"><input type="input" name="<?php echo $mngl_user->location_str; ?>" id="<?php echo $mngl_user->location_str; ?>" value="<?php echo $mngl_user->location; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <?php } ?>
    <?php if($mngl_options->show_sex != 'hidden') { ?>
    <tr>
      <td valign="top"><?php _e('Sex', 'mingle'); ?>:<?php echo (($mngl_options->show_sex == 'private')?"<span class=\"description\"> (".__("private", 'mingle').")</span>":''); ?></td>
      <td valign="top"><?php echo MnglProfileHelper::sex_dropdown($mngl_user->sex_str, $mngl_user->sex); ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td valign="top"><?php _e('Password', 'mingle'); ?>:</td>
      <td valign="top"><input type="password" name="<?php echo $mngl_user->password_str; ?>" id="<?php echo $mngl_user->password_str; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <tr>
      <td valign="top"><?php _e('Password Confirmation', 'mingle'); ?>:</td>
      <td valign="top"><input type="password" name="<?php echo $mngl_user->password_confirm_str; ?>" id="<?php echo $mngl_user->password_confirm_str; ?>" class="mngl-profile-edit-field" /></td>
    </tr>
    <tr>
      <td valign="top"><?php _e('Avatar', 'mingle'); ?>:</td>
      <td valign="top">
        <input type="file" name="<?php echo $mngl_user->avatar_str; ?>" id="<?php echo $mngl_user->avatar_str; ?>" class="mngl-profile-edit-field" /><br/>
          <?php require_once(MNGL_VIEWS_PATH . "/mngl-profiles/edit_avatar.php"); ?>
      </td>
    </tr>
  </table>
  <h3><?php _e('Privacy', 'mingle'); ?>:</h3>
  <div><?php echo MnglProfileHelper::privacy_dropdown($mngl_user->privacy_str, $mngl_user->privacy); ?></div>
  <h3><?php _e('Notification Settings', 'mingle'); ?>:</h3>
  <table width="100%" class="profile-edit-table">
    <?php
    foreach ($mngl_options->notification_types as $ntype => $settings)
    {
      ?>
      <tr>
        <td width="5%" valign="top"><input type="checkbox" name="<?php echo "{$mngl_user->hide_notifications_str}[$ntype]"; ?>" id="<?php echo "{$mngl_user->hide_notifications_str}[$ntype]"; ?>"<?php MnglAppHelper::value_is_checked_with_array($mngl_user->hide_notifications_str, $ntype, $mngl_user->hide_notifications[$ntype]); ?> /></td>
        <td width="95%" valign="top"><?php printf(__('Don\'t Send Me "%s" Notifications','mingle'), $settings['name']); ?></td>
      </tr> 
      <?php
    }
    ?>
  </table>
  <br/>
  <input type="submit" class="mngl-share-button" name="Update" value="<?php _e('Update', 'mingle'); ?>" />
</form>
</div>
