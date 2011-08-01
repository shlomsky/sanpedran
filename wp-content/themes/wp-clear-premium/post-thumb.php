<?php
$defthumb = get_bloginfo('stylesheet_directory') . '/images/def-thumb.gif';
if ( function_exists('get_the_image') && $wp_clear_default_thumbs == 'yes' ) { ?>

<?php get_the_image(array(
	'custom_key' => array('post_thumbnail','thumbnail'),
	'default_size' => 'thumbnail',
	'default_image' => $defthumb,
	'link_to_post' => false,
	'image_class' => "post_thumbnail",
	'width' => '100',
	'height' => '100',
)); ?>

<?php } elseif ( function_exists('get_the_image') && $wp_clear_default_thumbs == 'no' ) { ?>	

<?php get_the_image(array(
	'custom_key' => array('post_thumbnail','thumbnail'),
	'default_size' => 'thumbnail',
	'default_image' => false,
	'link_to_post' => false,
	'image_class' => "post_thumbnail",
	'width' => '100',
	'height' => '100',
)); ?>

<?php } elseif ( $wp_clear_default_thumbs == 'yes' ) { ?>

	<?php if (get_post_meta($post->ID, post_thumbnail)) { ?>

		<img src="<?php echo get_post_meta($post->ID, post_thumbnail, true); ?>" class="post-thum" alt="<?php _e("post thumbnail"); ?>" />

	<?php } else { ?>

		<img src="<?php bloginfo('stylesheet_directory'); ?>/images/def-thumb.gif"  class="post-thum" alt="<?php _e("post thumbnail"); ?>" />

	<?php }  ?>

<?php } elseif ( $wp_clear_default_thumbs == 'no' ) { ?>

	<?php if (get_post_meta($post->ID, post_thumbnail)) { ?>

		<img src="<?php echo get_post_meta($post->ID, post_thumbnail, true); ?>" class="post-thum" alt="<?php _e("post thumbnail"); ?>" />

	<?php }  ?>

<?php } ?>