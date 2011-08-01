<?php do_action( 'sp_events_user_profile_above' ); ?>
<style type="text/css">
.form-table form input {border:none;}
<?php if( eventsGetOptionValue('donateHidden', false) ) : ?>
	#sp-events-user-profile {display: none;}
<?php endif; ?>
#mainDonateRow label {line-height: 30px;}
#submitLabel {display: block;}
#submitLabel input {
	display: block;
	padding: 0;
}
</style>
<div id="sp-events-user-profile">
	<h3><?php _e('The Events Calendar'); ?></h3>
	
	<table class="form-table">
	    <tr id="mainDonateRow">
	    	<th scope="row"><?php _e('Donate',$this->pluginDomain); ?></th>
	        <td>
	            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	                <input type="hidden" name="cmd" value="_s-xclick">
	                <input type="hidden" name="hosted_button_id" value="10750983">
	                <input type="hidden" name="item_name" value="Events User Profile Page">
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

<?php do_action( 'sp_events_user_profile_below' ); ?>