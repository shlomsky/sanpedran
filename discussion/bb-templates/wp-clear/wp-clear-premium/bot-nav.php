<?php if ( is_single() ) { ?>
<div class="navigation" style="border-top:0;">
	<div class="alignleft"><?php previous_post_link('%link', 'Previous Post'); ?></div>
	<div class="alignright"><?php next_post_link('%link', 'Next Post'); ?></div>
	<div style="clear:both;"></div>
</div>
<?php } else { ?>
<div class="navigation">
	<div class="alignleft"><?php posts_nav_link('','','Older Entries') ?></div>
	<div class="alignright"><?php posts_nav_link('','Newer Entries','') ?></div>
	<div style="clear:both;"></div>
</div>
<?php } ?>