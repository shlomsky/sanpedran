<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

			<div id="content">

			<?php if ( $wp_clear_archive_layout == '3-column' ) { ?>
			<div class="col-3">
			<?php } ?>

				<div class="maincontent">

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

				<div class="post clearfix">

					<div class="entry clearfix">

						<h1><?php _e("Sorry. Error 404: Page Not Found"); ?></h1>

 						<p><?php _e("I'm sorry, but the post or page you're looking for could not be found. It could be because of our recent re-design. Here are a couple of options that might help you."); ?></p>

						<ul>
							<li><?php _e("Use the search box in the upper right-hand corner."); ?></li>
							<li><?php _e("Try scrolling through the monthly archives to the right."); ?></li>
							<li><?php _e("Try scrolling through the categories at the top of the page."); ?></li>
							<li><?php _e("As a last resort, you can focus all your mental energy on what it is you're looking for, and it might magically appear on your screen (not likely though)."); ?></li>
						</ul> 

						<p><strong><?php _e("You can also take a look through our most recent posts. Perhaps you'll find what you're looking for there."); ?></strong></p>

						<ol>
							<?php query_posts('showposts=20'); ?>
							<?php while (have_posts()) : the_post(); ?>
							<li><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
						</ol>

					</div>

				</div>

				</div>

			</div>

			<?php if ( $wp_clear_archive_layout == '3-column' ) { ?>
<?php include (TEMPLATEPATH . "/sidebar-left.php"); ?>

			</div>
			<?php } ?>

		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>