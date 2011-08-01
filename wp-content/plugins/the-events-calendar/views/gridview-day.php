<?php global $post; $post = $event; ?>
<div id='event_<?php echo $eventId; ?>' class="event">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

	<div id='tooltip_<?php echo $eventId; ?>' class="tooltip" style="display:none;">
		<h5 class="event-title"><?php _e($event->post_title); ?></h5>
		<div class="event-body">
			<?php if ( !the_event_all_day($event->ID) ) : ?>
			<div class="event-date">
				<?php if ( !empty( $start ) )	echo $start; ?>
				<?php if ( !empty( $end )  && $start !== $end )		echo " – " . $end . '<br />'; ?>
			</div>
			<?php endif; ?>
			<?php echo The_Events_Calendar::truncate($event->post_content, 30); ?>

		</div>
		<span class="arrow"></span>
	</div>
	
</div>