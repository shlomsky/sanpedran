<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

			<div id="content">

			<?php if ( $wp_clear_single_layout == '3-column' ) { ?>
			<div class="col-3">
			<?php } ?>

				<div class="maincontent">

					<?php include (TEMPLATEPATH . '/banner468.php'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<div class="singlepost">

						<div class="post clearfix" id="post-<?php the_ID(); ?>">
							<h1><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h1>
							<p class="postinfo"><?php the_author_posts_link(); ?> | <?php the_time('M d, Y') ?> | <a href="<?php comments_link(); ?>"><?php _e('Comments'); ?> <?php comments_number('0','1','%'); ?></a></p>
							<div class="entry clearfix">
								<?php the_content(''); ?>
							</div>
							<p class="cats"><strong><?php _e("Filed Under"); ?></strong>: <?php the_category(' &bull; '); ?></p>
							<?php the_tags("<p class='tags'><strong> Tags</strong>: "," &bull; ", "</p>"); ?>
						</div>

						<?php if ( $wp_clear_hide_auth_bio == 'yes' ) { ?>
						<?php } else { ?>
						<div class="auth-bio clearfix">
							<p class="bio">
							<?php // this is the author photo pulled from gravatar.com  
							if (function_exists('get_avatar')) {
							$gravsize = $wp_clear_grav_size; 
							$author_email = get_the_author_email();
							echo get_avatar($author_email,$size="$gravsize");
							} else {
							//alternate gravatar code for < 2.5
							$gravsize = $wp_clear_grav_size;
							$md5 = md5( $email=get_the_author_email() );
							echo "<img class='avatar' src='http://www.gravatar.com/avatar.php?gravatar_id=$md5&amp;size=$gravsize' alt='' />";
							} ?>
							<strong><?php _e("About the Author"); ?></strong>: <?php the_author_description(); ?></p>
						</div>
						<?php } ?>

						<?php comments_template('', true); ?>

<?php endwhile; endif; ?>

					</div>

				</div>

				</div>

			<?php if ( $wp_clear_single_layout == '3-column' ) { ?>
<?php include (TEMPLATEPATH . "/sidebar-left.php"); ?>

			</div>
			<?php } ?>

		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>