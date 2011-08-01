<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

<?php if ( is_home() && $paged < 2 && $wp_clear_features_on == 'yes') { ?>
<?php include (TEMPLATEPATH . '/features.php'); ?>
<?php } ?>

			<div id="content" class="clearfix">

				<?php if ( $wp_clear_home_posts_by_cat == yes ) { ?>
<div class="maincontent">
<?php include (TEMPLATEPATH . '/index2.php'); ?>
</div>
<?php } else { ?>

			<?php if ( $wp_clear_home_layout == '3-column' && $wp_clear_home_posts_by_cat == no ) { ?>
			<div class="col-3">
			<?php } ?>

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

				<div class="maincontent">

<?php
$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts("paged=$page");
if (have_posts()) : while (have_posts()) : the_post();
if ( $post->ID == $do_not_duplicate[$post->ID] ) continue; update_post_caches($posts); ?>

					<div class="post clearfix" id="post-main-<?php the_ID(); ?>">

						<?php include (TEMPLATEPATH . '/post-thumb.php'); ?>

						<div class="entry clearfix">
                        
                        	<div class="home-cats"><?php the_category(' &bull; '); ?></div>
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

			<?php } ?>

			</div>

			<?php if ( $wp_clear_home_layout == '3-column' && $wp_clear_home_posts_by_cat == no ) { ?>
<?php include (TEMPLATEPATH . "/sidebar-left.php"); ?>
			</div>
			<?php } ?>

		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>