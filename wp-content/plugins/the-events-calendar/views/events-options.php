<script type="text/javascript">
jQuery(document).ready(function() {

	function theEventsCalendarHideDonateButton() {
		jQuery('#mainDonateRow').hide();
		jQuery('#secondDonateRow').show();
	}
	
	jQuery('#hideDonateButton').click(function() {
		jQuery.post('/wp-admin/admin-ajax.php', { donateHidden: true, action: 'hideDonate' }, theEventsCalendarHideDonateButton, 'json' );
	});

});
</script>
<style type="text/css">
.form-table form input {border:none;}
<?php if( eventsGetOptionValue('donateHidden', false) ) : ?>
	#mainDonateRow {display: none;}
<?php else : ?>
	#mainDonateRow {background-color: #FCECA9;}
	#secondDonateRow {display: none;}
<?php endif; ?>
#mainDonateRow label {line-height: 30px;}
#submitLabel {display: block;}
#submitLabel input {
	display: block;
	padding: 0;
}
#hideDonateButton {}
#checkBoxLabel {}
.form-table form #secondSubmit {
	background-color: #F9F9F9;
	border-bottom: 1px solid;
	border-left: none;
	border-right: none;
	border-top: none;
	cursor: pointer;
	margin: 0;
	padding: 0;
}
</style>
<h2><?php _e('The Events Calendar Settings',$this->pluginDomain); ?></h2>

<table class="form-table">
    <tr id="mainDonateRow">
    	<th scope="row"><?php _e('Donate',$this->pluginDomain); ?></th>
        <td>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="10750983">
                <input type="hidden" name="item_name" value="Events Options Panel Main">
                <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
                <label id="submitLabel" for="submit">
                	<?php _e('If you find this plugin useful, please consider donating to the producer of it, Shane &#38; Peter, Inc. Thank you!',$this->pluginDomain); ?>
                </label>

                <input id="hideDonateButton" type="checkbox" name="hideDonateButton" value="" />
                <label id="checkBoxLabel" for="hideDonateButton"><?php _e('I have already donated, so please hide this button!',$this->pluginDomain); ?></label>
            </form>
        </td>
    </tr>
    <tr id="secondDonateRow">
        <td>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="10751527">
                <input type="hidden" name="item_name" value="Events Options Panel Secondary">
                <input id="secondSubmit" type="submit" value="Donate for this wonderful plugin" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
        </td>
    </tr>
</table>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
<?php
if ( function_exists('wp_nonce_field') ) {
	wp_nonce_field('saveEventsCalendarOptions');
}
?>

<table class="form-table">
	<tr>
		<th scope="row"><?php _e('Default View for the Events',$this->pluginDomain); ?></th>
        <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Default View for the Events',$this->pluginDomain); ?></span>
                </legend>
                <label title='Grid View'>
                    <?php 
                    $viewOptionValue = eventsGetOptionValue('viewOption','month'); 
                    if( $viewOptionValue == 'upcoming' ) {
                        $listViewStatus = 'checked="checked"';
                    } else {
                        $gridViewStatus = 'checked="checked"';
                    }
                    ?>
                    <input type="radio" name="viewOption" value="month" <?php echo $gridViewStatus; ?> /> 
                    <?php _e('Grid View',$this->pluginDomain); ?>
                </label><br />
                <label title='List View'>
                    <input type="radio" name="viewOption" value="upcoming" <?php echo $listViewStatus; ?> /> 
                    <?php _e('List View',$this->pluginDomain); ?>
                </label><br />
            </fieldset>
        </td>
	</tr>
    <tr>
    <th scope="row"><?php _e('Default Country for Events',$this->pluginDomain); ?></th>
    	<td>
            <select name="defaultCountry" id="defaultCountry">
				<?php 
				$this->constructCountries();
				$defaultCountry = eventsGetOptionValue('defaultCountry');
                foreach ($this->countries as $abbr => $fullname) {
                	print ("<option value=\"$fullname\" ");
                	if ($defaultCountry[1] == $fullname) { 
                		print ('selected="selected" ');
                	}
                	print (">$fullname</option>\n");
                }
                ?>
            </select>
        </td>
    </tr>
    <?php do_action( 'sp_events_options_bottom' ); ?>
	<tr>
    	<td>
    		<input id="saveEventsCalendarOptions" class="button-primary" type="submit" name="saveEventsCalendarOptions" value="Save Changes" />
        </td>
    </tr>
    
</table>

</form>