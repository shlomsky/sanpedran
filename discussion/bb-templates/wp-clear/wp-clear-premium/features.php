<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/prototype.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/effects.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/glider.js"></script>

		<div id="my-glider" class="clearfix">

			<!-- <div class="control-top">Featured Articles</div> -->

			<div class="scroller">

<?php $count = 1 ?>
<?php $featurecount = $wp_clear_features_number; ?>
<?php $my_query = new WP_Query("category_name=featured&showposts=$featurecount");
while ($my_query->have_posts()) : $my_query->the_post();
$do_not_duplicate[$post->ID] = $post->ID; ?>
<?php $images =& get_children( 'post_type=attachment&post_mime_type=image&orderby=menu_order&post_parent=' . $post->ID );
if ( $images ) { foreach( $images as $imageID => $imagePost ); } ?>

				<div class="section" id="section<?php the_ID(); ?>" onclick="location.href='<?php the_permalink(); ?>';" style="cursor: pointer; background:url( <?php if (get_post_meta($post->ID, home_feature_photo)) { ?><?php echo get_post_meta($post->ID, home_feature_photo, true); ?><?php } else { ?><?php echo wp_get_attachment_url($imageID, 'full', false); ?><?php } ?> ) top center no-repeat;" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>">
					<div class="feature-entry" id="post-<?php the_ID(); ?>">
						<h2><a href="<?php the_permalink() ?>" rel="<?php _e("bookmark"); ?>" title="<?php _e("Permanent Link to"); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</div>
				</div>

<?php $count = $count + 1 ?>
<?php endwhile; ?>

			</div>

			<div class="controls clearfix">

				<ul class="clearfix">

<?php $count = 1 ?>
<?php $featurecount = $wp_clear_features_number; ?>
<?php $my_query = new WP_Query("category_name=featured&showposts=$featurecount");
while ($my_query->have_posts()) : $my_query->the_post();
$do_not_duplicate[$post->ID] = $post->ID; ?>

<?php if ( $count == 1 ) { ?>
					<li id="post<?php echo $count; ?>">
						<a class="active" title="<?php the_title(); ?>" href="#section<?php the_ID(); ?>">
							<?php $defthumb = get_bloginfo('stylesheet_directory') . '/images/def-thumb.gif';
							if ( function_exists('get_the_image') ) { ?>
								<?php get_the_image(array(
									'custom_key' => array('post_thumbnail','thumbnail'),
									'default_size' => 'thumbnail',
									'default_image' => $defthumb,
									'link_to_post' => false,
									'image_class' => "post_thumbnail",
								)); ?>
							<?php } else { ?>
								<img src="<?php echo get_post_meta($post->ID, post_thumbnail, true); ?>" alt="" class="thumbnail" />
							<?php } ?>
						</a>
					</li>
<?php } else { ?>
					<li id="post<?php echo $count; ?>">
						<a title="<?php the_title(); ?>" href="#section<?php the_ID(); ?>">
							<?php $defthumb = get_bloginfo('stylesheet_directory') . '/images/def-thumb.gif';
							if ( function_exists('get_the_image') ) { ?>
								<?php get_the_image(array(
									'custom_key' => array('post_thumbnail','thumbnail'),
									'default_size' => 'thumbnail',
									'default_image' => $defthumb,
									'link_to_post' => false,
									'image_class' => "post_thumbnail",
								)); ?>
							<?php } else { ?>
								<img src="<?php echo get_post_meta($post->ID, post_thumbnail, true); ?>" alt="" class="thumbnail" />
							<?php } ?>
						</a>
					</li>
<?php } ?>

<?php $count = $count + 1 ?>
<?php endwhile; ?>

				</ul>

			</div>

		</div>

<?php if ( $wp_clear_features_auto_glide == no ) { ?>
		<script type="text/javascript" charset="utf-8">
			var my_glider = new Glider('my-glider', {duration:0.5});
		</script>
<?php } else { ?>
		<script type="text/javascript" charset="utf-8">
			var my_glider = new Glider('my-glider', {duration:0.5, autoGlide:true, frequency:7});
		</script>
<?php } ?>