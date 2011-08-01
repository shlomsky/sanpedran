<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

				<?php if ( $wp_clear_cat_box_1 ) { ?>
				<ul class="home-left">
					<li class="title"><h2><?php echo $wp_clear_cat_box_1_title; ?></h2></li>
<?php query_posts('category_name=' .$wp_clear_cat_box_1. '&showposts=' .$wp_clear_num_home_posts_by_cat. ''); ?>
<?php while (have_posts()) : the_post(); 
$do_not_duplicate[$post->ID] = $post->ID; ?>
					<li class="homepost clearfix" id="post-1-<?php the_ID(); ?>">
						<a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php include (TEMPLATEPATH . '/post-thumb.php'); ?></a>
						<div class="entry clearfix">
							<h3><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>

							<?php if ( $wp_clear_home_posts_stack == yes ) { ?>
							<?php the_excerpt(); ?>
							<?php } else { ?>
							<?php
							$excerpt = get_the_excerpt();
							echo string_limit_words($excerpt,15);
							?> [...]
							<?php } ?>
						</div>
					</li>
<?php endwhile; ?>
					<li class="bottom"><a href="<?php bloginfo('url'); ?>/category/<?php echo $wp_clear_cat_box_1; ?>/"><?php _e("More from"); ?> <?php echo $wp_clear_cat_box_1_title; ?></a></li>
				</ul>
				<?php } ?>

				<?php if ( $wp_clear_cat_box_2 ) { ?>
				<ul class="home-right">
					<li class="title"><h2><?php echo $wp_clear_cat_box_2_title; ?></h2></li>
<?php query_posts('category_name=' .$wp_clear_cat_box_2. '&showposts=' .$wp_clear_num_home_posts_by_cat. ''); ?>
<?php while (have_posts()) : the_post(); 
$do_not_duplicate[$post->ID] = $post->ID; ?>
					<li class="homepost clearfix" id="post-2-<?php the_ID(); ?>">
						<a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php include (TEMPLATEPATH . '/post-thumb.php'); ?></a>
						<div class="entry clearfix">
							<h3><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>

							<?php if ( $wp_clear_home_posts_stack == yes ) { ?>
							<?php the_excerpt(); ?>
							<?php } else { ?>
							<?php
							$excerpt = get_the_excerpt();
							echo string_limit_words($excerpt,15);
							?> [...]
							<?php } ?>
						</div>
					</li>
<?php endwhile; ?>
					<li class="bottom"><a href="<?php bloginfo('url'); ?>/category/<?php echo $wp_clear_cat_box_2; ?>/"><?php _e("More from"); ?> <?php echo $wp_clear_cat_box_2_title; ?></a></li>
				</ul>
				<?php } ?>

				<div style="clear:both;height:0;"></div>

				<?php if ( $wp_clear_cat_box_3 ) { ?>
				<ul class="home-left">
					<li class="title"><h2><?php echo $wp_clear_cat_box_3_title; ?></h2></li>
<?php query_posts('category_name=' .$wp_clear_cat_box_3. '&showposts=' .$wp_clear_num_home_posts_by_cat. ''); ?>
<?php while (have_posts()) : the_post(); 
$do_not_duplicate[$post->ID] = $post->ID; ?>
					<li class="homepost clearfix" id="post-3-<?php the_ID(); ?>">
						<a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php include (TEMPLATEPATH . '/post-thumb.php'); ?></a>
						<div class="entry clearfix">
							<h3><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>

							<?php if ( $wp_clear_home_posts_stack == yes ) { ?>
							<?php the_excerpt(); ?>
							<?php } else { ?>
							<?php
							$excerpt = get_the_excerpt();
							echo string_limit_words($excerpt,15);
							?> [...]
							<?php } ?>
						</div>
					</li>
<?php endwhile; ?>
					<li class="bottom"><a href="<?php bloginfo('url'); ?>/category/<?php echo $wp_clear_cat_box_3; ?>/"><?php _e("More from"); ?> <?php echo $wp_clear_cat_box_3_title; ?></a></li>
				</ul>
				<?php } ?>

				<?php if ( $wp_clear_cat_box_4 ) { ?>
				<ul class="home-right">
					<li class="title"><h2><?php echo $wp_clear_cat_box_4_title; ?></h2></li>
<?php query_posts('category_name=' .$wp_clear_cat_box_4. '&showposts=' .$wp_clear_num_home_posts_by_cat. ''); ?>
<?php while (have_posts()) : the_post(); 
$do_not_duplicate[$post->ID] = $post->ID; ?>
					<li class="homepost clearfix" id="post-4-<?php the_ID(); ?>">
						<a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php include (TEMPLATEPATH . '/post-thumb.php'); ?></a>
						<div class="entry clearfix">
							<h3><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>

							<?php if ( $wp_clear_home_posts_stack == yes ) { ?>
							<?php the_excerpt(); ?>
							<?php } else { ?>
							<?php
							$excerpt = get_the_excerpt();
							echo string_limit_words($excerpt,15);
							?> [...]
							<?php } ?>
						</div>
					</li>
<?php endwhile; ?>
					<li class="bottom"><a href="<?php bloginfo('url'); ?>/category/<?php echo $wp_clear_cat_box_4; ?>/"><?php _e("More from"); ?> <?php echo $wp_clear_cat_box_4_title; ?></a></li>
				</ul>
				<?php } ?>

				<div style="clear:both;height:0;"></div>

<?php if ( $wp_clear_other_articles == yes ) { ?>
				<ul class="home-bottom">
					<li class="title"><h2><?php _e("Other Recent Articles"); ?></h2></li> 

<?php
$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
query_posts("paged=$page");
if (have_posts()) : while (have_posts()) : the_post(); 
if ( $post->ID == $do_not_duplicate[$post->ID] ) continue; ?>

					<li class="homepost clearfix" id="post-main-<?php the_ID(); ?>">
						<?php include (TEMPLATEPATH . '/post-thumb.php'); ?>
						<div class="entry clearfix">
							<h3><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
							<?php the_excerpt(); ?>
						</div>
					</li>

<?php endwhile; endif; ?>
					<li class="bottom"><a href="<?php bloginfo('url'); ?>/<?php echo date('Y'); ?>/"><?php _e("All Recent Articles"); ?></a></li>
				</ul>
<?php } ?>