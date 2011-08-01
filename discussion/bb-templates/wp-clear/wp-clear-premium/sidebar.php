<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

		<div id="contentright">

			<div id="sidebar" class="clearfix">

				<ul>

					<?php if ( $wp_clear_ad300 == yes ) { ?>
					<li id="banner300-top-right">
						<div class="textwidget"><?php echo stripslashes($wp_clear_ad300_code); ?></div>
					</li>
					<?php } ?>

					<li id="side-tabs">
						<?php include (TEMPLATEPATH . "/side-tabs.php"); ?>
					</li>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Right Sidebar - Top') ) : ?>
<?php endif; ?>	

				</ul>

			</div>

			<div id="sidebar-bottom-left">

				<ul>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Right Sidebar - Bottom Left') ) : ?>
<?php endif; ?>	

				</ul>

			</div>

			<div id="sidebar-bottom-right">

				<ul>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Right Sidebar - Bottom Right') ) : ?>
<?php endif; ?>	

				</ul>

			</div>



		</div>