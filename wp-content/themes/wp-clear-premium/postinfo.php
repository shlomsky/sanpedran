<div class="postdate clearfix">
	<div class="left">
		<?php the_author_posts_link(); ?> | <?php the_time('M d, Y') ?> | <a href="<?php comments_link(); ?>"> <?php comments_number('0','1','%'); ?></a>
	</div>
	<div class="right">
		<a class="more-link" href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to "); ?><?php the_title(); ?>"><?php _e("Full Story"); ?></a>
	</div>
</div>