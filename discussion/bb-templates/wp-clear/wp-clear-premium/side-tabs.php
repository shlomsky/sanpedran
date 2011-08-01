<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/tabs.js"></script>

	<ul class="tabs clearfix">  
		<li><a href="javascript:tabSwitch_2(1, 4, 'tab_', 'content_');" id="tab_1" class="on"><?php _e("Subscribe"); ?></a></li>
		<li><a href="javascript:tabSwitch_2(2, 4, 'tab_', 'content_');" id="tab_2"><?php _e("Archives"); ?></a></li>  
		<li><a href="javascript:tabSwitch_2(3, 4, 'tab_', 'content_');" id="tab_3"><?php _e("Tags"); ?></a></li>
		<li><a href="javascript:tabSwitch_2(4, 4, 'tab_', 'content_');" id="tab_4"><?php _e("Popular"); ?></a></li>
	</ul>

	<div style="clear:both;"></div>

	<div id="content_1" class="cat_content">
		<div class="sidebox rss">
			<p class="feeds"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e("RSS Feed"); ?></a> | <a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e("Comments Feed"); ?></a></p>
			<p class="email"><?php _e("Get the latest updates via email."); ?></p>
<?php if ( $wp_clear_fb_feed_id ) { ?>
			<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $wp_clear_fb_feed_id; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
				<input type="hidden" value="<?php echo $wp_clear_fb_feed_id; ?>" name="uri"/>
				<input type="hidden" name="loc" value="en_US"/>
				<p><input type="text" style="width:140px" name="email" value="<?php _e('enter email address'); ?>" /> <input type="submit" value="<?php _e('Subscribe'); ?>" /><br />
<small><?php _e('Privacy guaranteed. We will not share your information.'); ?></small></p>
			</form>
<?php } elseif ( $wp_clear_alt_email_code ) { ?>
			<?php echo stripslashes($wp_clear_alt_email_code); ?>
<?php } else { ?>
			<p><?php _e("I'm sorry but I haven't had time to set up the email subscription feature yet ... try the RSS feed in the meantime."); ?></p>
<?php } ?>
		</div> 
	</div>

	<div id="content_2" class="cat_content" style="display:none">
		<div class="sidebox">
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> 
				<option value=""><?php echo attribute_escape(__('Select Month')); ?></option> 
				<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?>
			</select>
		</div> 
	</div>
                     
	<div id="content_3" class="cat_content" style="display:none">
		<div class="sidebox">
			<select name="tag-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> 
			<option value=""><?php echo attribute_escape(__('Select a Tag')); ?></option>
			<?php $posttags = get_terms('post_tag'); ?>
			<?php if ($posttags) {
				foreach($posttags as $tag) { 
					echo "<option value='";
					echo get_tag_link($tag);
					echo "'>";
					echo $tag->name;
					echo "</option>"; }
				} ?>
			</select>
		</div>
	</div>

	<div id="content_4" class="cat_content" style="display:none">
		<?php /* Alex King's <a href="http://www.dyasonhat.com/wordpress-plugins/popularity-contest-plugin-wordpress-27-working-version/">Popularity Contest</a> plugin would work really well here. */ ?>
<?php if ( function_exists('akpc_most_popular') ) : ?>
		<ul class="clearfix"><?php akpc_most_popular($limit=5); ?></ul>
<?php else : ?>
		<div class="sidebox"><p><?php _e('This feature has not been activated yet.'); ?></p></div>
<?php endif; ?>

	</div>

