<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

			<div id="content">

			<?php if ( $wp_clear_page_layout == '3-column' ) { ?>
			<div class="col-3">
			<?php } ?>

				<div class="maincontent">

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="singlepost">

				<div class="post clearfix" style="margin:0;" id="post-<?php the_ID(); ?>">

					<div class="entry clearfix">

						<h1><?php the_title(); ?></h1>

						<?php the_content(); ?>

					</div>

				</div>

				</div>

<?php endwhile; endif; ?>

				</div>

			</div>

			<?php if ( $wp_clear_page_layout == '3-column' ) { ?>
<?php include (TEMPLATEPATH . "/sidebar-left.php"); ?>

			</div>
			<?php } ?>

		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>