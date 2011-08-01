  <?php
  global $mngl_friends_controller, $mngl_user;

  if(!$user_search)
  {
  ?>
      <div id="mngl-fake-search-form" class="mngl-search-form">
        <a href="javascript:mngl_show_search_form()"><div id="mngl-fake-search-input" class="mngl-board-fake-input"><?php _e("Search Users...", 'mingle'); ?></div></a>
      </div>
      <div id="mngl-search-form" class="mngl-search-form mngl-hidden">
        <input type="text" id="mngl-search-input" onkeyup="javascript:mngl_search_directory( this.value )" class="mngl-search-input mngl-board-input" />
      </div>
  <?php
  }
?>
<div id="mngl-profile-results">
<p><strong><?php printf( __ngettext("%s User Was Found", "%s Users Were Found", $record_count, 'mingle'), number_format( (float)$record_count )); ?></strong></p>
<?php
  if($prev_page > 0)
  {
    ?>
      <div id="mngl_prev_page"><a href="<?php echo get_permalink($mngl_options->directory_page_id) . "?mdp={$prev_page}"; ?>">&laquo; <?php _e('Previous Page', 'mingle'); ?></a></div>
    <?php
  }
  ?>
<table style="width: 100%;">
<?php

  $avatar_thumb_size = 64;
  
  if(is_array($profiles))
  {
    foreach ($profiles as $key => $profile)
    { 
      $avatar_link = $profile->get_avatar($avatar_thumb_size);
      
      $full_name = $profile->screenname;
    
      if(!empty($search_query))
      {
        $full_name = preg_replace( "#({$search_query})#i", "<span class=\"mngl-search-match\">$1</span>", $full_name );
      }
?>
  <tr>
    <td valign="top" style="width: <?php echo $avatar_thumb_size; ?>px; vertical-align: top;"><a href="<?php echo $profile->get_profile_url(); ?>"><?php echo $avatar_link; ?></a></td>
    <td valign="top" style="padding: 0px 0px 0px 10px; vertical-align: top;"><h3 style="margin: 0px;"><a href="<?php echo $profile->get_profile_url(); ?>"><?php echo "{$full_name}"; ?></a></h3><?php echo $mngl_friends_controller->display_add_friend_button($mngl_user->id, $profile->id); ?></td>
  </tr>
<?php
    }
  }
?>  
</table>
<?php
if($next_page > 0)
{
  ?>
    <div id="mngl_prev_page"><a href="<?php echo get_permalink($mngl_options->directory_page_id) . "?mdp={$next_page}"; ?>"><?php _e('Next Page', 'mingle'); ?> &raquo;</a></div>
  <?php
}
?>
</div>
