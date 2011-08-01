<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments."); ?></p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'alt';
?>

<!-- You can start editing here. -->

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<div id="allcomments">

<?php if ($comments) : ?>

<?php
/* Count the totals */
$numPingBacks = 0;
$numComments  = 0;
/* Loop through comments to count these totals */
foreach ($comments as $comment) {
if (get_comment_type() != "comment") { $numPingBacks++; }
else { $numComments++; }
}
?>

<?php 
/* This is a loop for printing comments */
if ($numComments != 0) : ?>

	<h3 id="comments"><a title="<?php _e("Comments RSS Feed for This Entry"); ?>" href="<?php the_permalink() ?>feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="<?php _e("Comments RSS Feed for This Entry"); ?>" style="float:right;margin: 4px 0 0 0;" /></a><?php _e("Comments"); ?>: <?php _e($numComments); ?>&nbsp; | &nbsp;<a href="#<?php _e("respond"); ?>" title="<?php _e("Post a Comment"); ?>"><?php _e("Post a Comment"); ?></a>&nbsp; | &nbsp;<a href="<?php trackback_url(); ?>" title="<?php _e("Trackback URL"); ?>"><?php _e("Trackback URL"); ?></a></h3>

	<ol class="commentlist">
	
<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type()=="comment") : ?>
	
		<li class="clearfix <?php if ( $comment->comment_author_email == get_the_author_email() ) echo 'mycomment'; else echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">

			<?php /* This adds the Gravatar */ if (function_exists('get_avatar')) { ?>
			<?php 
				$gravsize = $wp_clear_grav_size; 
				echo get_avatar($comment,$size="$gravsize"); 
			?>
			<?php } else { ?>
			<?php if ( !empty( $comment->comment_author_email ) ) {
				$gravsize = $wp_clear_grav_size;
				$md5 = md5( $comment->comment_author_email );
				echo "<img class='comment-grav' src='http://www.gravatar.com/avatar.php?gravatar_id=$md5&amp;size=$gravsize' alt='' />"; } ?>
			<?php } ?>

			<p class="commentmetadata">
				<cite><?php comment_author_link() ?></cite> | <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('M j, Y') ?></a> | <a href="#respond"><?php _e("Reply"); ?></a><?php edit_comment_link('Edit',' | ',''); ?></p>

<?php if ($comment->comment_approved == '0') : ?>
			<p><em><?php _e("Your comment is awaiting moderation."); ?></em></p>
<?php endif; ?>

			<?php comment_text() ?>

		</li>

	<?php
		/* Changes every other comment to a different class */
		$oddcomment = ( empty( $oddcomment ) ) ? 'alt' : '';
	?>
		
<?php endif; endforeach; ?>
	
	</ol>
	
<?php endif; ?>

<?php
/* This is a loop for printing trackbacks if there are any */
if ($numPingBacks != 0) : ?>

	<h3 id="trackbacks"><?php _e("Trackbacks"); ?>: <?php _e($numPingBacks); ?>&nbsp; | &nbsp;<a href="<?php trackback_url(); ?>" title="<?php _e("Trackback URL"); ?>"><?php _e("Trackback URL"); ?></a></h3>

	<ol class="tblist">
	
<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type()!="comment") : ?>

		<li id="comment-<?php comment_ID() ?>">
			<?php _e("From"); ?> <?php comment_author_link() ?> <?php _e("on"); ?> <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('M j, Y') ?></a><?php edit_comment_link('Edit',' | ',''); ?>
<?php if ($comment->comment_approved == '0') : ?>
			<p><em><?php _e("Your comment is awaiting moderation."); ?></em></p>
<?php endif; ?>
		</li>
	
<?php endif; endforeach; ?>

	</ol>

<?php endif; ?>
	
<?php else : 
/* No comments at all means a simple message instead */ 
?>

<?php endif; ?>

<?php if (comments_open()) : ?>

	<h3 id="respond"><a title="<?php _e("Comments RSS Feed for This Entry"); ?>" href="<?php the_permalink() ?>feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="<?php _e("Comments RSS Feed for This Entry"); ?>" style="float:right;margin: 4px 0 0 0;" /></a><?php _e("Post a Comment"); ?>&nbsp; | &nbsp;<a href="<?php trackback_url(); ?>" title="<?php _e("Trackback URL"); ?>"><?php _e("Trackback URL"); ?></a></h3>
	
	<?php if (get_option('comment_registration') && !$user_ID ) : ?>
	<p id="comments-blocked"><?php _e("You must be"); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e("logged in"); ?></a> <?php _e("to post a comment."); ?></p>
	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

		<?php if ($user_ID) : ?>
	
		<p><?php _e("You are logged in as"); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e("Log out of this account"); ?>"><?php _e("Logout"); ?></a>.</p>
	
		<?php else : ?>	
	
		<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
		<label for="author"><small><?php _e("Name"); ?> <?php if ($req) echo "(required)"; ?></small></label></p>

		<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
		<label for="email"><small><?php _e("Mail (will not be published)"); ?> <?php if ($req) echo "(required)"; ?></small></label></p>

		<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
		<label for="url"><small><?php _e("Website"); ?></small></label></p>

	
		<?php endif; ?>

		<p><textarea name="comment" id="comment" cols="5" rows="10" tabindex="4"></textarea></p>

		<p><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e("Submit Comment"); ?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
	
		<?php do_action('comment_form', $post->ID); ?>

	</form>

<?php endif; // If registration required and not logged in ?>

<?php else : // Comments are closed ?>
	<p id="comments-closed"><?php _e("Sorry, comments for this entry are closed at this time."); ?></p>
<?php endif; ?>

</div>