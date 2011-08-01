<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:',$this->pluginDomain);?></label>
	<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Show:',$this->pluginDomain);?></label>
	<select id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" class="widefat" style="width:100%;">
	<?php for ($i=1; $i<=10; $i++)
	{?>
	<option <?php if ( $i == $instance['limit'] ) {echo 'selected="selected"';}?> > <?php echo $i;?> </option>
	<?php } ?>							
	</select>
</p>

<p>Display:</p>

<?php $displayoptions = array (
	"start" => __("Start Date & Time", $this->pluginDomain),
	"end" => __("End Date & Time", $this->pluginDomain),
	"venue" => __("Venue", $this->pluginDomain),
	"address" => __("Address", $this->pluginDomain),
	"city" => __("City", $this->pluginDomain),
	"state" => __("State (US)", $this->pluginDomain),
	"province" => __("Province (Int)", $this->pluginDomain),
	"zip" => __("Postal Code", $this->pluginDomain),
	"country" => __("Country", $this->pluginDomain),
	"phone" => __("Phone", $this->pluginDomain),
	"cost" => __("Price", $this->pluginDomain),
);

foreach ($displayoptions as $option => $label) {
	?><p>
		<input class="checkbox" type="checkbox" <?php checked( $instance[$option], 'on' ); ?> id="<?php echo $this->get_field_id( $option ); ?>" name="<?php echo $this->get_field_name( $option ); ?>" />
		<label for="<?php echo $this->get_field_id( $option ); ?>"><?php echo $label ?></label>
	</p>
<?php } ?>