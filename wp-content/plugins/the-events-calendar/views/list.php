<?php
global $spEvents;
$spEvents->loadStylesAndScripts();
include (TEMPLATEPATH.'/header.php'); ?>
<div id="content" class="upcoming">
	<div id='eventsCalendarHeader' class="clearfix">
		<h2 class="cal-title"><?php _e('Calendar of Events', $spEvents->pluginDomain) ?></h2>

		<span class='calendarButtons'> 
			<a class='listview' href='<?php echo events_get_listview_link(); ?>'>List View</a>
			<a class='gridview' href='<?php echo events_get_gridview_link(); ?>'>Grid View</a>
		</span>

	</div><!--#eventsCalendarHeader-->

	<div id="events-loop" class="events post-list clearfix">
	<?php while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID() ?>" class="event post clearfix<?php echo $alt ?>">
						    <div style="clear:both;"></div>
						        <?php if ( is_new_event_day() ) : ?>
				<h4 class="event-day"><?php echo the_event_start_date( null, false ); ?></h4>
						        <?php endif; ?>
					<?php the_title('<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a></h2>'); ?>
				<div class="entry-content event-entry">
					<?php the_excerpt() ?>
				</div> <!-- End event-entry -->

				<div class="event-list-meta">
	              <table cellspacing="0">
	                  <tr>
	                    <td class="event-meta-desc"><?php _e('Start:', $spEvents->pluginDomain) ?></td>
	                    <td class="event-meta-value"><?php echo the_event_start_date(); ?></td>
	                  </tr>
	                  <tr>
	                    <td class="event-meta-desc"><?php _e('End:', $spEvents->pluginDomain) ?></td>
	                    <td class="event-meta-value"><?php echo the_event_end_date(); ?></td>
	                  </tr>
	                  <?php
	                    $venue = the_event_venue();
	                    if ( !empty( $venue ) ) :
	                  ?>
	                  <tr>
	                    <td class="event-meta-desc"><?php _e('Venue:', $spEvents->pluginDomain) ?></td>
	                    <td class="event-meta-value"><?php echo $venue; ?></td>
	                  </tr>
	                  <?php endif; ?>
	                  <?php
	                    $phone = the_event_phone();
	                    if ( !empty( $phone ) ) :
	                  ?>
	                  <tr>
	                    <td class="event-meta-desc"><?php _e('Phone:', $spEvents->pluginDomain) ?></td>
	                    <td class="event-meta-value"><?php echo $phone; ?></td>
	                  </tr>
	                  <?php endif; ?>
	                  <?php
	                    $address = the_event_address(); $city = the_event_city(); $state = the_event_state(); $zip = the_event_zip();
	                    if (!empty( $address ) && !empty( $city ) && !empty( $state ) && !empty( $zip ) ) :
	                  ?>
	                  <tr>
	                    <td class="event-meta-desc"><?php _e('Address:', $spEvents->pluginDomain) ?><br /><a class="gmap" href="<?php event_google_map_link() ?>" title="Click to view a Google Map" target="_blank"f>Google Map</a></td>
	                    <td class="event-meta-value"><?php echo the_event_address(); ?> <br /> <?php echo the_event_city(); ?>, <?php echo the_event_state(); ?> <?php echo the_event_zip(); ?></td>
	                  </tr>
	                  <?php endif; ?>
	                  <?php
	                    $cost = the_event_cost();
	                    if ( !empty( $cost ) ) :
	                  ?>
 		              <tr>
						<td class="event-meta-desc"><?php _e('Cost:', $spEvents->pluginDomain) ?></td>
						<td class="event-meta-value"><?php echo $cost; ?></td>
					 </tr>
	                  <?php endif; ?>
	              </table>
				</div>
				<div style="clear:both;"></div>
			</div> <!-- End post -->
			<div class="events-list content_footer"></div>
<?php $alt = ( empty( $alt ) ) ? ' alt' : '';?> 
	<?php endwhile; // posts ?>

	

	</div><!-- #events-loop -->
	<div class="nav" id="nav-below">

		<div class="nav-previous"><?php 
		// Display Previous Page Navigation
		if( events_displaying_upcoming() && get_previous_posts_link( ) ) : ?>
			<?php previous_posts_link( '<span>&laquo; Previous Events</span>' ); ?>
		<?php elseif( events_displaying_upcoming() && !get_previous_posts_link( ) ) : ?>
			<a href='<?php echo events_get_past_link(); ?>'><span>&laquo; Previous Events</span></a>
		<?php elseif( events_displaying_past() && get_next_posts_link( ) ) : ?>
			<?php next_posts_link( '<span>&laquo; Previous Events</span>' ); ?>
		<?php endif; ?>
		</div>

		<div class="nav-next"><?php
		// Display Next Page Navigation
		if( events_displaying_upcoming() && get_next_posts_link( ) ) : ?>
			<?php next_posts_link( '<span>Next Events &raquo;</span>' ); ?>
		<?php elseif( events_displaying_past() && get_previous_posts_link( ) ) : ?>
			<?php previous_posts_link( '<span>Next Events &raquo;</span>' ); // a little confusing but in 'past view' to see newer events you want the previous page ?>
		<?php elseif( events_displaying_past() && !get_previous_posts_link( ) ) : ?>
			<a href='<?php echo events_get_upcoming_link(); ?>'><span>Next Events &raquo;</span></a>
		<?php endif; ?>
		</div>

	</div>
	
</div>


<?php
include (TEMPLATEPATH.'/footer.php');
?>