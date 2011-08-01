<?php get_header(); ?>

<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentleft">

			<div id="content">

			<?php if ( $wp_clear_archive_layout == '3-column' ) { ?>
			<div class="col-3">
			<?php } ?>

				<?php include (TEMPLATEPATH . '/banner468.php'); ?>

				<div class="maincontent">

<?php /* If this is a category archive */ if (is_category()) { ?>			
				<h1 class="archive-title"><a title="<?php _e("RSS Feed for the"); ?> '<?php echo single_cat_title(); ?>' <?php _e("Category"); ?>" href="<?php echo get_category_link($cat); ?>feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="<?php _e("RSS Feed for the"); ?> '<?php echo single_cat_title(); ?>' <?php _e("Category"); ?>" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("All Entries in the"); ?> "<?php echo single_cat_title(); ?>" <?php _e("Category"); ?></h1>
		
<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
				<h1 class="archive-title"><a title="<?php _e("Main Site RSS Feed"); ?>" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="Main Site RSS Feed" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("Archive for"); ?> <?php the_time('F jS, Y'); ?></h1>

<?php /* If this is a daily archive */ } elseif (is_search()) { ?>
				<h1 class="archive-title"><a title="<?php _e("Main Site RSS Feed"); ?>" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="Main Site RSS Feed" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("Search Results for"); ?> '<?php echo wp_specialchars($s, 1); ?>'</h1>
		
<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
				<h1 class="archive-title"><a title="<?php _e("Main Site RSS Feed"); ?>" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="Main Site RSS Feed" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("Archive for"); ?> <?php the_time('F, Y'); ?></h1>

<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
				<h1 class="archive-title"><a title="<?php _e("Main Site RSS Feed"); ?>" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="Main Site RSS Feed" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("Archive for"); ?> <?php the_time('Y'); ?></h1>
		
<?php /* If this is an author archive */ } elseif (is_author()) { ?>
<?php if(isset($_GET['author_name'])) : $author = get_userdata(get_query_var('author_name')); $link = get_author_link(false, $author->ID, $author->user_firstname, $author->user_nicename,$author->user_url,$author->user_description,$author->user_email,$author->display_name); else : $author = get_userdata(get_query_var('author')); $link = get_author_link(false, $author->ID, $author->user_firstname, $author->user_nicename,$author->user_url,$author->user_description, $author->user_email,$author->display_name); endif; ?>

				<h1 class="archive-title"><a title="<?php _e("RSS Feed for This Author"); ?>" href="<?php the_author_url(); ?>feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="<?php _e("RSS Feed for this author"); ?>" style="float:right;margin: 7px 0 0 5px" /></a><?php _e("Author Archive for"); ?> <?php echo $author->display_name; ?></h1>

				<div class="auth-bio auth-archive clearfix">
					<?php /* This adds the Gravatar */ if (function_exists('get_avatar')) { ?>
					<?php echo get_avatar($author->user_email,$size='64'); ?>
					<?php } else { ?>
					$md5 = md5( $email=$author->user_email );
					$default = urlencode( 'http://www.solostream.com/images/nophoto.gif' );
					echo "<img class='auth-archive-page' src='http://www.gravatar.com/avatar.php?gravatar_id=$md5&amp;size=60&amp;default=$default' alt='' />";
					?>
					<?php } ?>
					<p><?php echo $author->user_description; ?></p>
				</div> 

<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<h1 class="archive-title"><a title="<?php _e("Main Site RSS Feed"); ?>" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="Main Site RSS Feed" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("Blog Archives"); ?></h1>

<?php /* If this is a tag archive */ } elseif (is_tag()) { ?>
				<h1 class="archive-title"><a title="<?php _e("RSS Feed for This Tag"); ?>" href="<?php bloginfo('url'); ?>/tag/<?php echo str_replace(" ", "-", single_tag_title('', false)); ?>/feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/FeedIcon-16.gif" alt="<?php _e("RSS"); ?>" title="<?php _e("RSS Feed for This Tag"); ?>" style="float:right;margin: 7px 0 0 5px;" /></a><?php _e("All Entries Tagged With:"); ?> "<?php single_tag_title(); ?>"</h1>

<?php } ?>

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