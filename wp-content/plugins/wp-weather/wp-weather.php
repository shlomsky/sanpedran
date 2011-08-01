<?php /*

**************************************************************************

Plugin Name: wp-weather
Plugin URI: http://devblog.x-sphere.com/index.php/projects/wordpress/wp-weather
Description: Weather.com widget - shows forecast information for a city.  Displays image related to current conditions.  Weather can also be inserted into a post or page via the shortcode [weather_display].  Upgrading from any version prior to 0.3 requires re-configuration of the <a href="widgets.php">widget here.</a>
Version: 0.3.3
Author: Matt Brotherson
Author URI: http://devblox.x-sphere.com/

**************************************************************************

Thanks to:

* Viper007bond for his syntax highlight plugin that demonstrates the correct way to use WP 2.8 plugin 
functionality.

**************************************************************************/

class WPWeather {
	// All of these variables are private. Filters are provided for things that can be modified.
	var $pluginver       = '0.3.3';   // Plugin version
	var $settings        = array();   // Contains the user's settings
	var $defaultsettings = array();   // Contains the default settings
	var $shortcodes      = array();   // Array of shortcodes to use


	// Initalize the plugin by registering the hooks
	function __construct() {


		// Load localization domain
		load_plugin_textdomain( 'wpweather', false, '/wp-weather/localization' );

		// Create array of default settings (you can use the filter to modify these)
		$this->defaultsettings = (array) apply_filters( 'wpweather_defaultsettings', array(
			'partner_id'		=> '',
			'license_key'		=> '',
			'location_id'		=> '',
			'forecast_length'   => 1,
			'image_size'		=> 2,
			'units'				=> 1,
			'own_css'			=> 'true',
			'show_tonight'      => 'true',
			'settings_upgraded'	=> 1,
			'show_wind'			=> 0,
		) );
		
		// Load settings.  Checks for upgrade.
		$this->load_settings();
		


		// Register generic hooks
		//add_filter( 'the_content',                array(&$this, 'parse_shortcodes'),                  7 );
		add_action( 'admin_menu',                 array(&$this, 'register_settings_page') ); // Not is_admin() only for my admin bar plugin + others



		// Admin-only stuff
		if ( is_admin() ) {
			add_action( 'admin_init',             array(&$this, 'register_setting') );
			add_filter( 'plugin_action_links',    array(&$this, 'settings_link'),                     10, 2 );
		}
		

		// Create a list of shortcodes to use. You can use the filter to add/remove ones.
		//$this->shortcodes = array( 'weather_display', 'weather');
		//$this->shortcodes = (array) apply_filters( 'wpweather_shortcodes', $this->shortcodes );

		add_shortcode('weather_display',		array(&$this, 'shortcode_handler'));
		add_action('after_plugin_row',			array(&$this, 'warn_on_plugin_page'));

	}

	

	function load_settings()  {
//		$this->test_create_deprecated_settings();
		$usersettings = (array) get_option('wpweather_settings');

		if (empty($usersettings['settings_upgraded']))  {

			$deprecatedsettings = (array) apply_filters( 'wpweather_deprecatedsettings', array(
				'partner_id'		=> 'weather_partner_id',
				'license_key'		=> 'weather_license_key',
				'location_id'		=> 'weather_location_id',
				'forecast_length'   => 'weather_forecast_length',
				'image_size'		=> 'weather_image_size',
				'units'				=> 'weather_units',
				'own_css'			=> 'weather_own_css',
				'show_tonight'      => 'weather_show_tonight',
			) );

			$usersettings = (array) apply_filters( 'wpweather_usersettings', array(
				'partner_id'		=> get_option('weather_partner_id'),
				'license_key'		=> get_option('weather_license_key'),
				'location_id'		=> get_option('weather_location_id'),
				'forecast_length'   => get_option('weather_forecast_length'),
				'image_size'		=> get_option('weather_image_size'),
				'units'				=> get_option('weather_units'),
				'own_css'			=> get_option('weather_own_css'),
				'show_tonight'      => get_option('weather_show_tonight'),
				'settings_upgraded' => 1,
				'show_wind'			=> 0,
			) );
			
			// Converts the settings to the new standard.
			$usersettings['units']			= $usersettings['units'] == 's' ? 1 : 2;
			$usersettings['own_css']		= $usersettings['own_css'] == 'true' ? 1 :0;
			$usersettings['show_tonight']	= $usersettings['show_tonight'] == 'true' ? 1 :0;
			
			// Delete the old options.
			$this->delete_deprecated_settings($deprecatedsettings);
			// Create the new options.
			//$this->settings = wp_parse_args( $usersettings, $this->defaultsettings );
			update_option('wpweather_settings', $usersettings);
		
		}
		else  {
			// Create the settings array by merging the user's settings and the defaults	
			$usersettings = (array) get_option('wpweather_settings');
		}

		$this->settings = wp_parse_args( $usersettings, $this->defaultsettings );

		
	}

	function delete_deprecated_settings($settings)  {
		foreach ($settings as $name => $value) {
			delete_option($value);
		}
	}

	function test_create_deprecated_settings()  {
		update_option('weather_partner_id', '1005784008');
		update_option('weather_license_key', '807e20ff6f834326');
		update_option('weather_location_id', '75034');
		update_option('weather_forecast_length', '1');
		update_option('weather_image_size', '2');
		update_option('weather_units', 's');
		update_option('weather_own_css', 'true');
		update_option('weather_show_tonight', 'true');
	
	}

	function clean_cache()	{
		global $cache_enabled, $file_prefix;

		if (!$cache_enabled) 
			return;

		wp_cache_phase2_clean_cache($file_prefix);
	}

	function empty_table()	{
		global $wpdb;

		$table_name = $wpdb->prefix . "weatherxml";
		$query = "TRUNCATE TABLE $table_name";
		$result = $wpdb->query($query);
	}

	function display($returnHtml = false)  {
		global $wpdb;

		$forecast_units			= $this->settings['units'] == 1 ? 's' : 'm';
		$forecast_url			= 'http://xoap.weather.com/weather/local/' . $this->settings['location_id'] . '?cc=*&dayf=' . $this->settings['forecast_length'] .'&prod=xoap&par=' . $this->settings['partner_id'] . '&key=' . $this->settings['license_key'] . '&link=xoap&unit=' . $forecast_units;

		$is_php_5				= phpversion() > 5 ? true: false;

		$table_name				= $wpdb->prefix . "weatherxml";
		$datetime				= date("Y-m-d h:i:s");
		$xml_url				= md5($forecast_url);
		$interval				= .5;	// Hours to keep data in db before being considered old
		$expires				= $interval*60*60;
		$expiredatetime			= date("Y-m-d H:i:s", time() - $expires);

		$query					= "SELECT xml_url, xml_data, last_updated FROM $table_name WHERE xml_url = '$xml_url'"; 
		$result					= $wpdb->get_row($query);
		$time_diff				= strtotime($datetime) - strtotime($result->last_updated);

		if ($time_diff > $expires || $time_diff < 0 || empty($result->xml_data))
		{
			$this->empty_table();
		}

		if (!isset($result->last_updated) || empty($result->xml_data) )
		{

			// Get XML Query Results from Weather.com
			//$fp = fopen($forecast_url,"r");
			//while (!feof ($fp))
			//	$xml .= fgets($fp, 4096);
			//fclose ($fp);

			require_once( ABSPATH . WPINC . '/class-snoopy.php' );
			$weather_snoopy = new Snoopy();
			$weather_snoopy->agent = 'WordPress/' . $wp_version;
			$weather_snoopy->read_timeout = 2;

			if( !$weather_snoopy->fetch( $forecast_url )) {
				die( "alert('Could not connect to lookup host.')" );
			}

			$xml = $weather_snoopy->results;

			// Fire up the built-in XML parser
			$parser = xml_parser_create(  ); 
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

			// Set tag names and values
			xml_parse_into_struct($parser,$xml,$values,$index); 

			// Close down XML parser
			xml_parser_free($parser);

			if ($forecast_url) // Only inserts forecast feed, not search results feed, into db
			{
				$query = "INSERT INTO $table_name VALUES ('$xml_url', '$xml', '$datetime')";
				$result = $wpdb->query($query) or die('Invalid query: ' . mysql_error());
			}

		}
		else // Data in table, and it is within expiration period - do not load from weather.com and use cached copy instead.
		{
			$xml = $result->xml_data;
		}

		$htmlstring = $this->parseXml($xml, $is_php_5);
		//$htmlstring = $this->parseXml($xml, false);

		if (!$returnHtml)
			echo $htmlstring;

		return $htmlstring;
	}

	function parseXml($xml, $is_php_5)	{
		$htmlstring = '';
		if ($is_php_5)
			$htmlstring = $this->parseSimpleXml($xml);
		else
			$htmlstring = $this->parseIsterSimpleXml($xml);

		return $htmlstring;
	}

	function parseSimpleXml($xml)	{
		global $wpdb;

		$xml = new SimpleXmlElement($xml);

		switch ($this->settings['image_size'])
		{
			case "1":
				$image_dimensions = "32x32";
				break;
			case "2":
				$image_dimensions = "64x64";
				break;
			case "3":
				$image_dimensions = "128x128";
				break;
		}

		$day_forecast = $xml->dayf;

		$htmlstring = '';

		$htmlstring .= '<div class="weather_info">';
		$htmlstring .= '<div class="weather_today"><div class="weather_link"><a href="http://www.weather.com/weather/local/' . $xml->loc[id] .'" title="';
		$htmlstring .= __('Forecast for ', 'wpweather').$day_forecast->day[t] . ', ' . $day_forecast->day[dt].'">';
		$htmlstring .= $day_forecast->day[t] . ", " . $day_forecast->day[dt];
		$htmlstring .= '</a></div>';
		$htmlstring .= '<div class="weather_date">'.$xml->cc->t.'</div>';
		$htmlstring .= __('<div class="weather_currently">Currently: ', 'wpweather').'<strong>';
		$htmlstring .= $xml->cc->tmp.'&#730;'.$xml->head->ut.'</strong></div>';
		$htmlstring .= __('<div class="weather_feelslike">Feels Like: ', 'wpweather').$xml->cc->flik.'&#730; '.$xml->head->ut.'</div>';
		$htmlstring .= '<div class="weather_hilo"><strong>'.__('Hi: ', 'wpweather') . $day_forecast->day[0]->hi . '&#730;, ';
		$htmlstring .= __('Lo: ', 'wpweather') . $day_forecast->day[0]->low . '&#730;</strong></div>';
		if ( (bool) $this->settings['show_wind'])
		{
			$htmlstring .= __('Wind: ', 'wpweather'). $xml->cc->wind->s.', '.__('Gust: ', 'wpweather');
			$htmlstring .= $xml->cc->wind->gust.' MPH <br/>';
			$htmlstring .= __('Wind Direction: ', 'wpweather').$xml->cc->wind->t;
			$htmlstring .= ' ('.$xml->cc->wind->d.') <br/>';
		}



		$htmlstring .= '<div class="weather_img"><img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$xml->cc->icon.'.png" alt="'.$xml->cc->t.'" /></div></div>';

		if ((bool)$this->settings['show_tonight'])
		{
			$htmlstring .= '<div class="weather_tonight"><div class="weather_tonighttemp">'.__('Tonight: ', 'wpweather').$day_forecast->day[0]->low.'&#730;</div>';
			$htmlstring .= __('<div class="weather_sunset">Sunset: ', 'wpweather'). $day_forecast->day[0]->suns.'</div>';
			$htmlstring .= __('<div class="weather_moon">Moon Phase: ', 'wpweather'). $xml->cc->moon->t.'</div>';
			$htmlstring .= '<div class="weather_tonightimg"><img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$day_forecast->day[0]->part[1]->icon.'.png" alt="'.$day_forecast->day[0]->part[1]->t.'" /></div>';
			$htmlstring .= '</div>';
		}
		
		if (sizeof($day_forecast->day) > 1)
		{

			foreach($day_forecast->day as $day)
			{	
				if ($day[d] == "0")
					continue;
				$htmlstring .= '<p>';
				$htmlstring .= '<i>'.$day[t] . ', ' . $day[dt].'</i><br/>';
				$htmlstring .= '<strong>'.__('Hi: ', 'wpweather') . $day->hi . '&#730;, ';
				$htmlstring .= __('Lo: ', 'wpweather') . $day->low . '&#730;</strong><br/>';
				if ( (bool) $this->settings['show_wind'])
				{
					$htmlstring .= __('Wind: ', 'wpweather'). $day->part[0]->wind->s.', '.__('Gust: ', 'wpweather');
					$htmlstring .= $day->part[0]->wind->gust.' MPH <br/>';
					$htmlstring .= __('Wind Direction: ', 'wpweather').$day->part[0]->wind->t;
					$htmlstring .= ' ('.$day->part[0]->wind->d.') <br/>';
				}
				$htmlstring .= '<img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$day->part[0]->icon.'.png" alt="'.$day->part[0]->t.'" />';
				$htmlstring .= '</p>';


			}
		}
		

		$htmlstring .= '<p class="weather_info">'.__('weather feed courtesy of ', 'wpweather').'<a href="http://www.weather.com/?prod=xoap&amp;par=' . $this->settings['partner_id'] . '" title="weather.com">weather.com</a> - '.__('thanks', 'wpweather').'!</p>';
		$htmlstring .= '</div>';

		return $htmlstring;
	}

	function parseIsterSimpleXml($xml)
	{
		global $wpdb;

		require_once(dirname(__FILE__).'/simplexml44/IsterXmlSimpleXMLImpl.php');
	  
		// read and write a document
		$impl = new IsterXmlSimpleXMLImpl;
		$xml = $impl->load_string($xml);

		switch ($this->settings['image_size'])
		{
			case "1":
				$image_dimensions = "32x32";
				break;
			case "2":
				$image_dimensions = "64x64";
				break;
			case "3":
				$image_dimensions = "128x128";
				break;
		}

		$day_forecast = $xml->weather->dayf;
		$days = $day_forecast->children();

		$attributes = $xml->weather->loc->attributes();
		$location_id = $attributes['id'];
		$day_attributes = $days[1]->attributes();

		$day = $day_attributes['t'];
		$date = $day_attributes['dt'];

		$current_temp		= $xml->weather->cc->tmp->CDATA();
		$feels_like			= $xml->weather->cc->flik->CDATA();
		$current_conditions = $xml->weather->cc->t->CDATA();
		$temp_unit			= $xml->weather->head->ut->CDATA();
		$image_icon			= $xml->weather->cc->icon->CDATA();
		
		
		$high_temp = $days[1]->hi->CDATA();
		$low_temp = $days[1]->low->CDATA();

		$wind_speed		= $xml->weather->cc->wind->s->CDATA();
		$wind_gust		= $xml->weather->cc->wind->gust->CDATA();
		$wind_direction = $xml->weather->cc->wind->t->CDATA();
		$wind_degrees	= $xml->weather->cc->wind->d->CDATA();

		
		$htmlstring .= '<div class="weather_info">';
		$htmlstring .= '<p><a href="http://www.weather.com/weather/local/' . $location_id .'" title="';
		$htmlstring .= __('Forecast for ', 'wpweather').$day . ', ' . $date.'">';
		$htmlstring .= $day . ", " . $date;
		$htmlstring .= '</a>';
		$htmlstring .= '<br />'.$current_conditions.'<br/>';
		$htmlstring .= __('Currently: ', 'wpweather').'<strong>'.$current_temp.'&#730; '.$temp_unit.'</strong><br/>';
		$htmlstring .= __('Feels Like: ', 'wpweather').$feels_like.'&#730; '.$temp_unit.'<br/>';
		$htmlstring .= '<strong>'.__('Hi: ', 'wpweather') . $high_temp . '&#730;, ';
		$htmlstring .= __('Lo: ', 'wpweather') . $low_temp . '&#730;</strong><br/>';
		if ( (bool) $this->settings['show_wind'])
		{
			$htmlstring .= __('Wind: ', 'wpweather'). $wind_speed.', '.__('Gust: ', 'wpweather');
			$htmlstring .= $wind_gust.' MPH <br/>';
			$htmlstring .= __('Wind Direction: ', 'wpweather').$wind_direction;
			$htmlstring .= ' ('.$wind_degrees.') <br/>';
		}
		$htmlstring .= '<img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$image_icon.'.png" alt="'.$current_conditions.'" /></p>';

		if ((bool) $this->settings['show_tonight'])
		{
			$htmlstring .= '<p>'.__('Tonight: ', 'wpweather').$days[1]->low->CDATA().'&#730;<br/>';
			$htmlstring .= __('Sunset: ', 'wpweather'). $days[1]->suns->CDATA().'<br/>';
			$htmlstring .= __('Moon Phase: ', 'wpweather'). $xml->weather->cc->moon->t->CDATA().'<br/>';
			$htmlstring .= '<img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$days[1]->part[1]->icon->CDATA().'.png" alt="'.$days[1]->part[1]->t->CDATA().'" /><br/>';
			$htmlstring .= '</p>';
		} 

		if (sizeof($days) > 2)
		{

			for($i = 1; $i < sizeof($days); $i++)
			{	

				$attributes = $days[$i]->attributes();

				if ($attributes['d'] == "0")
					continue;

				$day			= $attributes['t'];
				$date			= $attributes['dt'];

				$high_temp		= $days[$i]->hi->CDATA();
				$low_temp		= $days[$i]->low->CDATA();
				$image_icon		= $days[$i]->part[0]->icon->CDATA();
				$conditions		= $days[$i]->part[0]->t->CDATA();

				$wind_speed		= $days[$i]->part[0]->wind->s->CDATA();
				$wind_gust		= $days[$i]->part[0]->wind->gust->CDATA();
				$wind_direction = $days[$i]->part[0]->wind->t->CDATA();
				$wind_degrees	= $days[$i]->part[0]->wind->d->CDATA();

				$htmlstring .= '<p>';
				$htmlstring .= '<i>'.$day . ', ' . $date.'</i><br/>';
				$htmlstring .= '<strong>'.__('Hi: ', 'wpweather') . $high_temp . '&#730;, ';
				$htmlstring .= __('Lo: ', 'wpweather') . $low_temp . '&#730;</strong><br/>';
				if ( (bool) $this->settings['show_wind'])
				{
					$htmlstring .= __('Wind: ', 'wpweather'). $wind_speed.', '.__('Gust: ', 'wpweather');
					$htmlstring .= $wind_gust.' MPH <br/>';
					$htmlstring .= __('Wind Direction: ', 'wpweather').$wind_direction;
					$htmlstring .= ' ('.$wind_degrees.') <br/>';
				}
				$htmlstring .= '<img border="0" src="'. get_bloginfo(wpurl).'/wp-content/plugins/wp-weather/images/'.$image_dimensions.'/'.$image_icon.'.png" alt="'.$conditions.'" />';
				$htmlstring .= '</p>';
			}
		}
		
		$htmlstring .= '<p class="weather_info">'.__('weather feed courtesy of ', 'wpweather');
		$htmlstring .= '<a href="http://www.weather.com/?prod=xoap&amp;par=' . $this->settings['partner_id'] . '" title="weather.com">weather.com</a> - '.__('thanks', 'wpweather').'!</p>';
		$htmlstring .= '</div>';

		return $htmlstring;
	}

	// Register the settings page
	function register_settings_page() {
		add_options_page( __('WP-Weather Settings', 'wpweather'), __('WP-Weather', 'wpweather'), 'manage_options', 'wpweather', array(&$this, 'settings_page') );
	}


	// Register the plugin's setting
	function register_setting() {
		register_setting( 'wpweather_settings', 'wpweather_settings', array(&$this, 'validate_settings') );
	}




	// Add a "Settings" link to the plugins page
	function settings_link( $links, $file ) {
		static $this_plugin;
		
		if( empty($this_plugin) )
			$this_plugin = plugin_basename(__FILE__);

		if ( $file == $this_plugin )
			$links[] = '<a href="' . admin_url( 'options-general.php?page=wpweather' ) . '">' . __('Settings', 'wpweather') . '</a>';

		return $links;
	}


	


	// Settings page
	function settings_page() { ?>


<div class="wrap">
<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
	<h2><?php _e( 'WP-Weather Settings', 'wpweather' ); ?></h2>

	<form method="post" action="options.php">

	<?php settings_fields('wpweather_settings'); ?>
	<input name="wpweather_settings[settings_upgraded]" type="hidden" id="wpweather-settings_upgraded" value="1" />


	<p><?php _e('This plugin gathers weather information from weather.com and displays it via a widget.  You must obtain a partner id and license key from weather.com to use this widget.  Sign up for Weather.com\'s free XML service at <a href="http://www.weather.com/services/xmloap.html">Weather.com XML service</a>.  The data from weather.com is cached in the database for one half hour to avoid unnecessary calls to the web service.', 'wpweather'); ?></p>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wpweather-partner_id"><?php _e('Weather.com partner id', 'wpweather'); ?></label></th>
			<td><input name="wpweather_settings[partner_id]" type="text" id="wpweather-partner_id" value="<?php echo esc_attr( $this->settings['partner_id'] ); ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpweather-license_key"><?php _e('Weather.com license key', 'wpweather'); ?></label></th>
			<td><input name="wpweather_settings[license_key]" type="text" id="wpweather-license_key" value="<?php echo esc_attr( $this->settings['license_key'] ); ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpweather-location_id"><?php _e('Location id (zip code or city code)', 'wpweather'); ?></label></th>
			<td><input name="wpweather_settings[location_id]" type="text" id="wpweather-location_id" value="<?php echo esc_attr( $this->settings['location_id'] ); ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpweather-forecast_length"><?php _e('Forecast length (in days)', 'wpweather'); ?></label></th>
			<td><input name="wpweather_settings[forecast_length]" type="text" id="wpweather-forecast_length" value="<?php echo esc_attr( $this->settings['forecast_length'] ); ?>" class="regular-text" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="syntaxhighlighter-units"><?php _e('Forecast unit type', 'wpweather'); ?></label></th>
			<td>
				<select name="wpweather_settings[units]" id="wpweather-units" class="postform">
<?php
					$units = array(
						1		=> __( 'Standard', 'wpweather' ),
						2		=> __( 'Metric', 'wpweather' ),
					);

					foreach ( $units as $value => $name ) {
						echo '					<option value="' . esc_attr( $value ) . '"';
						selected( $this->settings['units'], $value );
						echo '>' . esc_html( $name ) . "</option>\n";
					}
?>
				</select>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="wpweather-image_size"><?php _e('Image size', 'wpweather'); ?></label></th>
			<td>
				<select name="wpweather_settings[image_size]" id="wpweather-image_size" class="postform">
<?php
					$image_sizes = array(
						1		=> __( '32x32', 'wpweather' ),
						2		=> __( '64x64', 'wpweather' ),
						3		=> __('128x128', 'wpweather'),
					);

					foreach ( $image_sizes as $value => $name ) {
						echo '					<option value="' . esc_attr( $value ) . '"';
						selected( $this->settings['image_size'], $value );
						echo '>' . esc_html( $name ) . "</option>\n";
					}
?>
				</select>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row"><?php _e('Miscellaneous', 'wpweather'); ?></th>
			<td>
				<fieldset>
					<legend class="hidden"><?php _e('Miscellaneous', 'wpweather'); ?></legend>

					<label for="wpweather-own_css"><input name="wpweather_settings[own_css]" type="checkbox" id="wpweather-own_css" value="1" <?php checked( $this->settings['own_css'], 1 ); ?> /> <?php _e('Use your own css (custom or theme)', 'wpweather'); ?></label><br />
					<label for="wpweather-show_wind"><input name="wpweather_settings[show_wind]" type="checkbox" id="wpweather-show_twind" value="1" <?php checked( $this->settings['show_wind'], 1 ); ?> /> <?php _e('Show wind information', 'wpweather'); ?></label><br />
					<label for="wpweather-show_tonight"><input name="wpweather_settings[show_tonight]" type="checkbox" id="wpweather-show_tonight" value="1" <?php checked( $this->settings['show_tonight'], 1 ); ?> /> <?php _e('Show tonight\'s conditions', 'wpweather'); ?></label><br />
				</fieldset>
			</td>
		</tr>		
		
	</table>

	<p class="submit">
		<input type="submit" name="syntaxhighlighter-submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>


</div>

<?php
	}


	// Validate the settings sent from the settings page
	function validate_settings( $settings ) {
		if ( !empty($_POST['wpweather-defaults']) ) {
			$settings = $this->defaultsettings;
			$_REQUEST['_wp_http_referer'] = add_query_arg( 'defaults', 'true', $_REQUEST['_wp_http_referer'] );
		} else {
			$settings['own_css']			= ( !empty($settings['own_css']) ) ? 1 : 0;
			$settings['show_tonight']		= ( !empty($settings['show_tonight']) )  ? 1 : 0;

			$settings['units'] = (int) $settings['units'];
			$settings['image_size'] = (int) $settings['image_size'];


			$settings['partner_id']  = ( !empty($settings['partner_id']) ) ? $settings['partner_id'] : $this->defaultsettings['partner_id'];
			$settings['license_key']    = ( !empty($settings['license_key']) )   ? $settings['license_key']   : $this->defaultsettings['license_key'];
			$settings['location_id']    = ( !empty($settings['location_id']) )   ? $settings['location_id']   : $this->defaultsettings['location_id'];

			$settings['forecast_length']    = (int) ( !empty($settings['forecast_length']) )   ? $settings['forecast_length']   : $this->defaultsettings['forecast_length'];
		}

		return $settings;
	}

	function warn_on_plugin_page($plugin_file) {
		if (strpos($plugin_file, 'wp-weather.php')) {
		
			$widgetversion = get_option('widget_weather');

			if (!empty($widgetversion))	{
				$message = '<strong>Note</strong>: You must set your widget up again on the <a href="widgets.php">widgets page here</a> due changes in the plugin architecture to embrace WP 2.8.';

				
			}
		
			if (!empty($message)) {
				print('
					<tr class="plugin-update-tr">
						<td colspan="5" class="plugin-update">
							<div class="update-message">
							'.$message.'
							</div>
						</td>
					</tr>
				');
			}
		}
	}


	function shortcode_handler($atts, $content=null)
	{
		return 	$this->display(true);
	}

	// PHP4 compatibility
	function WPWeather() {
		$this->__construct();
	}
}// WPWeather Class

class WPWeather_Widget extends WP_Widget {

	function WPWeather_Widget() {
		$widget_ops = array('classname' => 'widget_wpweather', 'description' => __( "WP-Weather Widget") );
		$control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('wpweather', __('Weather'), $widget_ops, $control_ops);    
	}

 
	function widget($args, $instance) {
		extract( $args );

		global $WPWeather;
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		echo $WPWeather->display(true);

		/* After widget (defined by themes). */
		echo $after_widget;
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		delete_option('widget_weather');

		return $instance;
	}
 
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Local Weather', 'weather'),  );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<?php
	}
}


// Create the table for holding the xml data.
add_action('activate_wp-weather/wp-weather.php', 'weather_install');

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'WPWeather', 5 );


// Widget stuffs.
function WPWeatherWidgetInit() {
  register_widget('WPWeather_Widget');
}
add_action('widgets_init', 'WPWeatherWidgetInit');


function WPWeather() {
	global $WPWeather;
	$WPWeather = new WPWeather();
}

// Legacy function support.
function weather_display()  {
	global $WPWeather;

	echo $WPWeather->display(true);
}

function weather_install()  {
	global $wpdb;

	$table_name = $wpdb->prefix . "weatherxml";

	if($wpdb->get_var("show tables like '$table_name'") != $table_name){

		$sql = "CREATE TABLE ".$table_name." (
		  xml_url varchar(150) NOT NULL default '',
		  xml_data text NOT NULL,
		  last_updated datetime NOT NULL default '0000-00-00 00:00:00',
		  KEY xml_url (xml_url)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
	}
}
?>