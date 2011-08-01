<?php 
list( $year, $month ) = split( '-', $spEvents->date );
$date = mktime(12, 0, 0, $month, 1, $year);
$daysInMonth = date("t", $date);
$offset = date("w", $date);
$rows = 1;
?>
<table class="calendar" id="big">

	<thead>
			<tr>
				<th id="Sunday"		abbr="<?php _e( 'Sunday' ); ?>"><?php _e( 'Sun' ); ?></th>
				<th id="Monday"		abbr="<?php _e( 'Monday' ); ?>"><?php _e( 'Mon' ); ?></th>
				<th id="Tuesday"	abbr="<?php _e( 'Tuesday' ); ?>"><?php _e( 'Tue' ); ?></th>
				<th id="Wednesday"	abbr="<?php _e( 'Wednesday' ); ?>"><?php _e( 'Wed' ); ?></th>
				<th id="Thursday"	abbr="<?php _e( 'Thursday' ); ?>"><?php _e( 'Thu' ); ?></th>
				<th id="Friday"		abbr="<?php _e( 'Friday' ); ?>"><?php _e( 'Fri' ); ?></th>
				<th id="Saturday"	abbr="<?php _e( 'Saturday' ); ?>"><?php _e( 'Sat' ); ?></th>
			</tr>
	</thead>

	<tbody>
		<tr>
		<?php
			// skip last month
			for( $i = 1; $i <= $offset; $i++ ){ 
				echo "<td class='othermonth'></td>";
			}
			// output this month
			for( $day = 1; $day <= $daysInMonth; $day++ ) {
			    if( ($day + $offset - 1) % 7 == 0 && $day != 1) {
			        echo "</tr>\n\t<tr>";
			        $rows++;
			    }
			    echo "<td class='thismonth'><div class='daynum'>" . $day . "</div>\n";
				echo display_day( $day, $monthView );
				echo "</td>";
			}
			// skip next month
			while( ($day + $offset) <= $rows * 7)
			{
			    echo "<td class='othermonth'></td>";
			    $day++;
			}
		?>
		</tr>
	</tbody>
</table>
<?php
function display_day( $day, $monthView ) {
	$output = '';
	for( $i = 0; $i < count( $monthView[$day] ); $i++ ) {
		$event 		= $monthView[$day][$i];
		$eventId	= $event->ID.'-'.$day;
		$start		= the_event_start_date( $event->ID );
		$end		= the_event_end_date( $event->ID );
		$cost		= the_event_cost( $event->ID );
		$address	= the_event_address( $event->ID );
		$city		= the_event_city( $event->ID );
		$state		= the_event_state( $event->ID );
		$province	= the_event_province( $event->ID );
		$country	= the_event_country( $event->ID );
		include( dirname( __FILE__ ) . '/gridview-day.php' );
		if( $i < count( $monthView[$day] ) - 1 ) { 
			echo "<hr />";
		}
	}
}

?>
