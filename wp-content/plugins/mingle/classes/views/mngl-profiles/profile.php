<?php global $mngl_user, $mngl_friend, $mngl_options; ?>
<?php $display_profile = ( $user->privacy == 'public' or 
                           MnglUser::is_logged_in_and_an_admin() or 
                           MnglUser::is_logged_in_and_visible() ); ?>

<table class="mngl-profile-table">
  <tr>
    <td valign="top" class="mngl-profile-table-col-1 mngl-valign-top">
      <table>
        <tr>
          <td>
            <?php echo $avatar; ?>
            <?php echo $mngl_friends_controller->display_add_friend_button($mngl_user->id, $user->id); ?>
          </td>
        </tr>
        <tr>
          <td valign="top" class="mngl-valign-top">
            <?php if($display_profile) { ?>

              <?php if($mngl_options->show_bio == 'public' and !empty($user->bio)) { ?>
                <p class="mngl-profile-bio"><?php echo wptexturize(stripslashes($user->bio)); ?></p>
              <?php } ?>
              <div class="mngl-profile-information">
              <?php if(!empty($user->first_name) and ($user->first_name != $user->screenname)) { ?>
                <p class="mngl-profile-field"><strong><?php _e('Name', 'mingle'); ?>:</strong><br/><?php echo wptexturize(stripslashes($user->first_name)); ?>
                <?php if(!empty($user->last_name)){ ?>
                <?php echo " " . wptexturize(stripslashes($user->last_name)); ?>
                <?php } ?>
                </p>
              <?php } ?>
              <?php if($mngl_options->show_sex == 'public' and !empty($user->sex)) { ?>
                <p class="mngl-profile-sex"><strong><?php _e('Sex', 'mingle'); ?>:</strong><br/><?php echo $user->sex; ?></p>
              <?php } ?>
              <?php if($mngl_options->show_location == 'public' and !empty($user->location)) { ?>
                <p class="mngl-profile-location"><strong><?php _e('Location', 'mingle'); ?>:</strong><br/><?php echo wptexturize($user->location); ?></p>
              <?php } ?>
              <?php if($mngl_options->show_url == 'public' and !empty($user->url)) { ?>
                <p class="mngl-profile-url"><strong><?php _e('Website', 'mingle'); ?>:</strong><br/><?php echo make_clickable($user->url); ?></p>
              <?php } ?>
              </div>
            <?php } ?>
            <p><strong><?php _e('Friends', 'mingle'); ?>:</strong><div class="mngl-profile-friend-grid-wrap"><?php echo $mngl_friends_controller->display_friends_grid($user->id); ?></div></p>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top" class="mngl-profile-table-col-2">
      <table class="mngl-profile-body">
        <tr>
          <td>
            <div class="mngl-profile-name"><?php echo $user->screenname; ?></div>
            <?php if($display_profile) { ?>
              <?php //echo $this->display_status($user->id); ?>
            <?php } else { 
                    require_once( MNGL_VIEWS_PATH . '/mngl-boards/private.php' );
                  }
            ?>
          </td>
        </tr>
        <tr>
          <td>
          </td>
        </tr>
        <tr>
          <?php if($display_profile) { ?>
            <td valign="top" width="100%"><div class="mngl-board"><?php echo $mngl_boards_controller->display($user->id); ?></div></td>
          <?php } ?>
        </tr>
      </table>
    </td>
</table>
