<?php
global $spEvents;
$spEvents->loadStylesAndScripts();
include (TEMPLATEPATH.'/header.php'); ?>
<div id="content" class="grid">
	<div id='eventsCalendarHeader' class="clearfix">
		<h2 class="cal-title"><?php _e('Calendar of Events', $spEvents->pluginDomain) ?></h2>

		<?php get_jump_to_date_calendar(); ?>

		<span class='calendarButtons'> 
			<a class='listview' href='<?php echo events_get_listview_link(); ?>'>List View</a>
			<a class='gridview' href='<?php echo events_get_gridview_link(); ?>'>Grid View</a>
		</span>

	</div><!--#eventsCalendarHeader-->

	<?php event_grid_view( ); // See the plugins/the-events-calendar/views/table.php template for customization ?>	
</div>


<?php
include (TEMPLATEPATH.'/footer.php');
?>