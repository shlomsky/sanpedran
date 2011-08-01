<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function(){
		// Register event handler for the event toggle
		jQuery("input[name='isEvent']").click(function(){ 
			if ( jQuery(this).val() == 'yes' ) {
				jQuery("#eventDetails").slideDown(200);
			} else {
				jQuery("#eventDetails").slideUp(200);
			}
		});
		// toggle time input
		jQuery('#allDayCheckbox').click(function(){
			jQuery(".timeofdayoptions").toggle();
			jQuery("#EventTimeFormatDiv").toggle();
		});
		if( jQuery('#allDayCheckbox').attr('checked') == true ) {
			jQuery(".timeofdayoptions").addClass("hide")
			jQuery("#EventTimeFormatDiv").addClass("hide");
		}
		// Set the initial state of the event detail and EB ticketing div
		jQuery("input[name='isEvent']").each(function(){
			if( jQuery(this).val() == 'no' && jQuery(this).attr('checked') == true ) {
				jQuery('#eventDetails, #eventBriteTicketing').hide();
			} else if( jQuery(this).val() == 'yes' && jQuery(this).attr('checked') == true ) {
				jQuery('#eventDetails, #eventBriteTicketing').show();
			}
		});
		
		//show state/province input based on first option in countries list, or based on user input of country
		function spShowHideCorrectStateProvinceInput(country) {
			if (country == 'United States') {
				jQuery("#USA").removeClass("hide");
				jQuery("#International").addClass("hide");
			}
			else {
				jQuery("#International").removeClass("hide");
				jQuery("#USA").addClass("hide");				
			}
		}
		
		spShowHideCorrectStateProvinceInput(jQuery("#EventCountry > option:first").val());
		
		jQuery("#EventCountry").change(function(){
			spShowHideCorrectStateProvinceInput(jQuery(this).attr("value"));
		});
				
	});
</script>
<style type="text/css">
	.eventForm td {
		padding:6px 6px 0 0;
		font-size:11px;
		vertical-align:middle;
	}
	.eventForm select, .eventForm input {
		font-size:11px;
	}
	.eventForm .hide {
		display:none;
	}
	.eventForm h4 {
		font-size:1.1em;
		margin:2em 0 1em;
	}
	.notice {
		background-color: rgb(255, 255, 224);
		border: 1px solid rgb(230, 219, 85);
		margin: 5px 0 15px;
	}
	.form-table form input {border:none;}
	<?php if( eventsGetOptionValue('donateHidden', false) ) : ?>
		#mainDonateRow {display: none;}
	<?php endif; ?>
	#mainDonateRow label {line-height: 30px;}
	#submitLabel {display: block;}
	#submitLabel input {
		display: block;
		padding: 0;
	}
</style>

<h2><?php _e('Event Details:',$this->pluginDomain); ?></h2>
<?php do_action('sp_events_errors', $postId ); ?>
<div>
	<?php _e('Is this post an event?',$this->pluginDomain); ?>&nbsp;
	<input tabindex="2001" type='radio' name='isEvent' value='yes' <?php echo $isEventChecked; ?> />&nbsp;<b>Yes</b>
	<input tabindex="2002" type='radio' name='isEvent' value='no' <?php echo $isNotEventChecked; ?> />&nbsp;<b>No</b>
</div>
<br />
<div id='eventDetails' class="eventForm">
	<?php do_action('sp_events_detail_top', $postId ); ?>
	<table cellspacing="0" cellpadding="0" id="EventInfo">
		<tr>
			<td><?php _e('All day event?'); ?></td>
			<td><input tabindex="2007" type='checkbox' id='allDayCheckbox' name='EventAllDay' value='yes' <?php echo $isEventAllDay; ?> /></td>
		</tr>
		<tr id='EventTimeFormatDiv'>
			<td><?php _e('Time Format (site wide option):',$this->pluginDomain); ?></td>
			<td>
				<input tabindex="2008" type='radio' name='EventTimeFormat' value='<?php echo The_Events_Calendar::DATETIMEFORMAT12; ?>' <?php echo $is12HourChecked; ?> />&nbsp;
				<?php _e('12 Hour'); ?>
				<input tabindex="2009" type='radio' name='EventTimeFormat' value='<?php echo The_Events_Calendar::DATETIMEFORMAT24; ?>' <?php echo $is24HourChecked; ?> />&nbsp;
				<?php _e('24 Hour'); ?>
			</td>
		</tr>
		<tr>
			<td style="width:100px;"><?php _e('Start Date / Time:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2010" name='EventStartMonth'>
					<?php echo $startMonthOptions; ?>
				</select>
				<select tabindex="2011" name='EventStartDay'>
					<?php echo $startDayOptions; ?>
				</select>
				<select tabindex="2012" name='EventStartYear'>
					<?php echo $startYearOptions; ?>
				</select>
				<span class='timeofdayoptions'>
					<?php _e('@',$this->pluginDomain); ?>
					<select tabindex="2013" name='EventStartHour'>
						<?php echo $startHourOptions; ?>
					</select>
					<select tabindex="2014" name='EventStartMinute'>
						<?php echo $startMinuteOptions; ?>
					</select>
					<select tabindex="2015" name='EventStartMeridian'>
						<?php echo $startMeridianOptions; ?>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php _e('End Date / Time:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2016" name='EventEndMonth'>
					<?php echo $endMonthOptions; ?>
				</select>
				<select tabindex="2017" name='EventEndDay'>
					<?php echo $endDayOptions; ?>
				</select>
				<select tabindex="2018" name='EventEndYear'>
					<?php echo $endYearOptions; ?>
				</select>
				<span class='timeofdayoptions'>
					<?php _e('@',$this->pluginDomain); ?>
					<select class="spEventsInput"tabindex="2019" name='EventEndHour'>
						<?php echo $endHourOptions; ?>
					</select>
					<select tabindex="2020" name='EventEndMinute'>
						<?php echo $endMinuteOptions; ?>
					</select>
					<select tabindex="2021" name='EventEndMeridian'>
						<?php echo $endMeridianOptions; ?>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php _e('Venue:',$this->pluginDomain); ?></td>
			<td>
				<input tabindex="2022" type='text' name='EventVenue' size='25'  value='<?php echo $_EventVenue; ?>' />
			</td>
		</tr>
		<tr>
			<td><?php _e('Country:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2023" name="EventCountry" id="EventCountry">
					<?php 
					$this->constructCountries();
				     foreach ($this->countries as $abbr => $fullname) {
				       print ("<option value=\"$fullname\" ");
				       if ($_EventCountry == $fullname) { 
				         print ('selected="selected" ');
				       }
				       print (">$fullname</option>\n");
				     }
				     ?>
			     </select>
			</td>
		</tr>
		<tr>
			<td><?php _e('Address:',$this->pluginDomain); ?></td>
			<td><input tabindex="2024" type='text' name='EventAddress' size='25' value='<?php echo $_EventAddress; ?>' /></td>
		</tr>
		<tr>
			<td><?php _e('City:',$this->pluginDomain); ?></td>
			<td><input tabindex="2025" type='text' name='EventCity' size='25' value='<?php echo $_EventCity; ?>' /></td>
		</tr>
		<tr id="International" <?php if($_EventCountry == 'United States' || $_EventCountry == '' ){echo('class="hide"'); } ?>>
			<td><?php _e('Province:',$this->pluginDomain); ?></td>
			<td><input tabindex="2026" type='text' name='EventProvince' size='10' value='<?php echo $_EventProvince; ?>' /></td>
		</tr>
		<tr id="USA" <?php if($_EventCountry !== 'United States'){echo('class="hide"');} ?>>
			<td><?php _e('State:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2027" name="EventState">
				    <option value=""><?php _e('Select a State:',$this->pluginDomain); ?></option> 
					<?php $states = array (
						"AL" => __("Alabama", $this->pluginDomain),
						"AK" => __("Alaska", $this->pluginDomain),
						"AZ" => __("Arizona", $this->pluginDomain),
						"AR" => __("Arkansas", $this->pluginDomain),
						"CA" => __("California", $this->pluginDomain),
						"CO" => __("Colorado", $this->pluginDomain),
						"CT" => __("Connecticut", $this->pluginDomain),
						"DE" => __("Delaware", $this->pluginDomain),
						"DC" => __("District of Columbia", $this->pluginDomain),
						"FL" => __("Florida", $this->pluginDomain),
						"GA" => __("Georgia", $this->pluginDomain),
						"HI" => __("Hawaii", $this->pluginDomain),
						"ID" => __("Idaho", $this->pluginDomain),
						"IL" => __("Illinois", $this->pluginDomain),
						"IN" => __("Indiana", $this->pluginDomain),
						"IA" => __("Iowa", $this->pluginDomain),
						"KS" => __("Kansas", $this->pluginDomain),
						"KY" => __("Kentucky", $this->pluginDomain),
						"LA" => __("Louisiana", $this->pluginDomain),
						"ME" => __("Maine", $this->pluginDomain),
						"MD" => __("Maryland", $this->pluginDomain),
						"MA" => __("Massachusetts", $this->pluginDomain),
						"MI" => __("Michigan", $this->pluginDomain),
						"MN" => __("Minnesota", $this->pluginDomain),
						"MS" => __("Mississippi", $this->pluginDomain),
						"MO" => __("Missouri", $this->pluginDomain),
						"MT" => __("Montana", $this->pluginDomain),
						"NE" => __("Nebraska", $this->pluginDomain),
						"NV" => __("Nevada", $this->pluginDomain),
						"NH" => __("New Hampshire", $this->pluginDomain),
						"NJ" => __("New Jersey", $this->pluginDomain),
						"NM" => __("New Mexico", $this->pluginDomain),
						"NY" => __("New York", $this->pluginDomain),
						"NC" => __("North Carolina", $this->pluginDomain),
						"ND" => __("North Dakota", $this->pluginDomain),
						"OH" => __("Ohio", $this->pluginDomain),
						"OK" => __("Oklahoma", $this->pluginDomain),
						"OR" => __("Oregon", $this->pluginDomain),
						"PA" => __("Pennsylvania", $this->pluginDomain),
						"RI" => __("Rhode Island", $this->pluginDomain),
						"SC" => __("South Carolina", $this->pluginDomain),
						"SD" => __("South Dakota", $this->pluginDomain),
						"TN" => __("Tennessee", $this->pluginDomain),
						"TX" => __("Texas", $this->pluginDomain),
						"UT" => __("Utah", $this->pluginDomain),
						"VT" => __("Vermont", $this->pluginDomain),
						"VA" => __("Virginia", $this->pluginDomain),
						"WA" => __("Washington", $this->pluginDomain),
						"WV" => __("West Virginia", $this->pluginDomain),
						"WI" => __("Wisconsin", $this->pluginDomain),
						"WY" => __("Wyoming", $this->pluginDomain),
					);
				      foreach ($states as $abbr => $fullname) {
				        print ("<option value=\"$abbr\" ");
				        if ($_EventState == $abbr) { 
				          print ('selected="selected" '); 
				        }
				        print (">$fullname</option>\n");
				      }
				      ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php _e('Postal Code:',$this->pluginDomain); ?></td>
			<td><input tabindex="2028" type='text' name='EventZip' size='6' value='<?php echo $_EventZip; ?>' /></td>
		</tr>
		<tr>
			<td><?php _e('Cost:',$this->pluginDomain); ?></td>
			<td><input tabindex="2029" type='text' name='EventCost' size='6' value='<?php echo $_EventCost; ?>' /></td>
		</tr>
		<tr>
			<td><?php _e('Phone:',$this->pluginDomain); ?></td>
			<td><input tabindex="2030" type='text' name='EventPhone' size='14' value='<?php echo $_EventPhone; ?>' /></td>
		</tr>
        <tr>
			<td><?php _e('Sub-category:',$this->pluginDomain); ?></td>
			<td><?php _e('<em>To assign this event to a custom sub-category, use the Wordpress categories chooser in the sidebar.</em>',$this->pluginDomain); ?></td>
		</tr>
		<tr id="mainDonateRow">
	    	<td><?php _e('Donate:',$this->pluginDomain); ?></td>
	        <td>
	            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	                <input type="hidden" name="cmd" value="_s-xclick">
	                <input type="hidden" name="hosted_button_id" value="10750983">
	                <input type="hidden" name="item_name" value="Events Post Editor">
	                <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	                <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	                <label id="submitLabel" for="submit">
	                	<?php _e('If you find this plugin useful, please consider donating to the producer of it, Shane &#38; Peter, Inc. Thank you!',$this->pluginDomain); ?>
	                </label>
		       </form>
	        </td>
	    </tr>
		
	</table>
</div>
<?php do_action( 'sp_events_details_bottom', $postId ); ?>