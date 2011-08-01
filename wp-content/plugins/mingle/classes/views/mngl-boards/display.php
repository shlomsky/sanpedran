<?php

if( $page <= 1 and 
    MnglUser::is_logged_in_and_visible() and
    ( ($owner_id==$author_id) or
      $mngl_friend->is_friend($owner_id, $author_id) ) )
{
  ?>
  <div id="mngl-fake-board-post-form" class="mngl-post-form">
    <a href="javascript:mngl_show_board_post_form()"><div id="mngl-fake-board-post-input" class="mngl-board-fake-input"><?php _e("What's on your mind?", 'mingle'); ?></div></a>
  </div>
  <table id="mngl-board-post-form" class="mngl-post-form mngl-hidden">
  <tr>
    <td colspan="2">
      <textarea id="mngl-board-post-input" class="mngl-board-input mngl-growable"></textarea>
    </td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
    <td width="0%">
      <input type="submit" class="mngl-share-button" id="mngl-board-post-button" onclick="javascript:mngl_post_to_board( '<?php echo MNGL_SCRIPT_URL; ?>', <?php echo $owner_id; ?>, <?php echo $author_id; ?>, document.getElementById('mngl-board-post-input').value, '<?php echo (($public)?'activity':'boards'); ?>')" name="Share" value="<?php _e('Share', 'mingle'); ?>"/>
    </td>
  </table>
  <?php
}
?>
  <?php
    require_once(MNGL_MODELS_PATH . "/MnglUser.php");
    foreach ($board_posts as $board_post)
    {
      $author = MnglUser::get_stored_profile_by_id($board_post->author_id);
      $owner  = MnglUser::get_stored_profile_by_id($board_post->owner_id);
      
      if($author and $owner)
        $this->display_board_post($board_post,$public);
    }
  ?>
  <?php if( count($board_posts) >= $page_size ) { ?>
    <div id="mngl-older-posts"><a href="javascript:mngl_show_older_posts( <?php echo ($page + 1) . ",'" . (($public)?'activity':'boards') . "','" . (($public)?$mngl_user->screenname:$owner->screenname) . "'"; ?> )"><?php _e('Show Older Posts', 'mingle'); ?></a></div>
  <?php } ?>
