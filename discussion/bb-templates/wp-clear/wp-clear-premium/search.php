<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

			<div id="content">

			<?php if ( $wp_clear_archive_layout == '3-column' ) { ?>
			<div class="col-3">
			<?php } ?>

				<div class="maincontent">

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

				<h1 class="archive-title"><?php _e("Search Results for"); ?> '<?php echo wp_specialchars($s, 1); ?>'</h1>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="post clearfix" id="post-<?php the_ID(); ?>">

					<?php include (TEMPLATEPATH . '/post-thumb.php'); ?>

					<div class="entry clearfix">
						<h2><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<?php if ( $wp_clear_post_content == 'Excerpts' ) { ?>
						<?php the_excerpt(); ?>
						<?php } else { ?>
						<?php the_content(''); ?>
						<?php } ?>
					</div>

				</div>

				<?php include (TEMPLATEPATH . "/postinfo.php"); ?>

<?php endwhile; endif; ?>

				<?php include (TEMPLATEPATH . "/bot-nav.php"); ?>

				</div>

			</div>

			<?php if ( $wp_clear_archive_layout == '3-column' ) { ?>
<?php include (TEMPLATEPATH . "/sidebar-left.php"); ?>

			</div>
			<?php } ?>

		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>