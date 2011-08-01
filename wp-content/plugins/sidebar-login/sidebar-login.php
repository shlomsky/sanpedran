<?php
/*
Plugin Name: Sidebar Login
Plugin URI: http://wordpress.org/extend/plugins/sidebar-login/
Description: Adds a sidebar widget to let users login
Version: 2.2.9
Author: Mike Jolley
Author URI: http://blue-anvil.com
*/

// Pre 2.6 compatibility (BY Stephen Rider)
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	if ( defined( 'WP_SITEURL' ) ) define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
	else define( 'WP_CONTENT_URL', get_option( 'url' ) . '/wp-content' );
}
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

load_plugin_textdomain('sblogin', WP_PLUGIN_URL.'/sidebar-login/langs/', 'sidebar-login/langs/');

function wp_sidebarlogin_menu() {
	add_submenu_page('options-general.php', __('Sidebar Login','sblogin'), __('Sidebar Login','sblogin'), 9,'Sidebar Login', 'wp_sidebarlogin_admin');
}

if (!function_exists('is_ssl')) :
function is_ssl() {
return ( isset($_SERVER['HTTPS']) && 'on' == strtolower($_SERVER['HTTPS']) ) ? true : false;
}
endif;

function wp_sidebarlogin_admin(){
	// Update options
	if ($_POST) {
		update_option('sidebarlogin_login_redirect', stripslashes($_POST['sidebarlogin_login_redirect']));
		update_option('sidebarlogin_logout_redirect', stripslashes($_POST['sidebarlogin_logout_redirect']));
		update_option('sidebarlogin_register_link', stripslashes($_POST['sidebarlogin_register_link']));
		update_option('sidebarlogin_forgotton_link', stripslashes($_POST['sidebarlogin_forgotton_link']));
		update_option('sidebarlogin_logged_in_links', stripslashes($_POST['sidebarlogin_logged_in_links']));
		echo '<div id="message"class="updated fade">';	
		_e('<p>Changes saved</p>',"sblogin");			
		echo '</div>';
	}
	// Get options
	$sidebarlogin_login_redirect = get_option('sidebarlogin_login_redirect');
	$sidebarlogin_logout_redirect = get_option('sidebarlogin_logout_redirect');
	$sidebarlogin_register_link = get_option('sidebarlogin_register_link');
	$sidebarlogin_forgotton_link = get_option('sidebarlogin_forgotton_link');
	$sidebarlogin_logged_in_links = get_option('sidebarlogin_logged_in_links');
	?>
	<div class="wrap alternate">
        <h2><?php _e('Sidebar Login',"sblogin"); ?></h2>
        <br class="a_break" style="clear: both;"/>
        <form action="options-general.php?page=Sidebar%20Login" method="post">
            <table class="niceblue form-table">
                <tr>
                    <th scope="col"><?php _e('Login redirect URL',"sblogin"); ?>:</th>
                    <td><input type="text" name="sidebarlogin_login_redirect" value="<?php echo $sidebarlogin_login_redirect; ?>" /> <span class="setting-description"><?php _e('Url to redirect the user to after login. Leave blank to use their current page.','sblogin'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Logout redirect URL',"sblogin"); ?>:</th>
                    <td><input type="text" name="sidebarlogin_logout_redirect" value="<?php echo $sidebarlogin_logout_redirect; ?>" /> <span class="setting-description"><?php _e('Url to redirect the user to after logout. Leave blank to use their current page.','sblogin'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Show Register Link',"sblogin"); ?>:</th>
                    <td><select name="sidebarlogin_register_link">
                    	<option <?php if ($sidebarlogin_register_link=='yes') echo 'selected="selected"'; ?> value="yes"><?php _e('Yes','sblogin'); ?></option>
                    	<option <?php if ($sidebarlogin_register_link=='no') echo 'selected="selected"'; ?> value="no"><?php _e('No','sblogin'); ?></option>
                    </select> <span class="setting-description"><?php _e('User registrations must also be turned on for this to work (\'Anyone can register\' checkbox in settings).','sblogin'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Show Lost Password Link',"sblogin"); ?>:</th>
                    <td><select name="sidebarlogin_forgotton_link">
                    	<option <?php if ($sidebarlogin_forgotton_link=='yes') echo 'selected="selected"'; ?> value="yes"><?php _e('Yes','sblogin'); ?></option>
                    	<option <?php if ($sidebarlogin_forgotton_link=='no') echo 'selected="selected"'; ?> value="no"><?php _e('No','sblogin'); ?></option>
                    </select></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Logged in links',"sblogin"); ?>:</th>
                    <td><textarea name="sidebarlogin_logged_in_links" rows="3" cols="80" /><?php echo $sidebarlogin_logged_in_links; ?></textarea><br/><span class="setting-description"><?php _e("One link per line. Note: Logout link will always show regardless. Tip: Add <code>|true</code> after a link to only show it to admin users. If you add a further <code>|USER LEVEL</code> the link will only be shown to users with levels equal to or greater than those defined. See <a href='http://codex.wordpress.org/Roles_and_Capabilities' target='_blank'>http://codex.wordpress.org/Roles_and_Capabilities</a> for more info on roles and Capabilities.<br/> You can also type <code>%USERNAME%</code> which will be replaced by the user's username. Default:",'sblogin');
                    echo '<br/>&lt;a href="'.get_bloginfo('wpurl').'/wp-admin/"&gt;Dashboard&lt;/a&gt;<br/>&lt;a href="'.get_bloginfo('wpurl').'/wp-admin/profile.php"&gt;Profile&lt;/a&gt;'; ?></span></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" value="<?php _e('Save Changes',"sblogin"); ?>" /></p>
        </form>
    </div>
    <?php
}

/*
example of short call with text

	sidebarlogin('before_title=<h5>&after_title='</h5>');
	
suggested by dev.xiligroup.com
*/

function sidebarlogin($myargs = '') {
	if (is_array($myargs)) $args = &$myargs;
	else parse_str($myargs, $args);
	
	$defaults = array('before_widget'=>'','after_widget'=>'',
	'before_title'=>'<h2>','after_title'=>'</h2>'
	);
	$args = array_merge($defaults, $args);
	
	widget_wp_sidebarlogin($args);
}

function widget_wp_sidebarlogin($args) {
		global $user_ID, $current_user;
		
		/* To add more extend i.e when terms came from themes - suggested by dev.xiligroup.com */
		$defaults = array(
			'thelogin'=>__('Login','sblogin'),
			'thewelcome'=>__("Welcome",'sblogin'),
			'theusername'=>__('Username:','sblogin'),
			'thepassword'=>__('Password:','sblogin'),
			'theremember'=>__('Remember me','sblogin'),
			'theregister'=>__('Register','sblogin'),
			'thepasslostandfound'=>__('Password Lost and Found','sblogin'),
			'thelostpass'=>	__('Lost your password?','sblogin'),
			'thelogout'=> __('Logout','sblogin')
		);
		
		$args = array_merge($defaults, $args);
		extract($args);		
		
		get_currentuserinfo();

		if ($user_ID != '') {
			// User is logged in
			
			global $current_user;
      		get_currentuserinfo();
			
			echo $before_widget . $before_title .$thewelcome.' '.ucwords($current_user->display_name). $after_title;
			
			echo '<div class="avatar_container">'.get_avatar($user_ID, $size = '38').'</div>';
			
			echo '<ul class="pagenav">';
			
			if(isset($current_user->user_level) && $current_user->user_level) $level = $current_user->user_level;
					
			$links = do_shortcode(get_option('sidebarlogin_logged_in_links'));
			
			$links = explode("\n", $links);
			if (sizeof($links)>0)
			foreach ($links as $l) {
				$link = explode('|',$l);
				if (isset($link[2]) && is_numeric(intval(trim($link[2]))) && intval(trim($link[2])) <= 10 ) { // Thanks to John from http://www.area-europa.es/
					$req_level = intval(trim($link[2]));
				} else {
					$req_level=10;
				}
				if (isset($link[1]) && strtolower(trim($link[1]))=='true' && $level < $req_level) continue; 
				else {
					// Parse %USERNAME%
					$link[0] = str_replace('%USERNAME%',$current_user->user_login,$link[0]);
					$link[0] = str_replace('%username%',$current_user->user_login,$link[0]);
					echo '<li class="page_item">'.$link[0].'</li>';
				}
			}
			
			$redir = trim(stripslashes(get_option('sidebarlogin_logout_redirect')));
			if (!$redir || empty($redir)) $redir = wp_sidebarlogin_current_url('nologout');
			
			echo '<li class="page_item"><a href="'.wp_logout_url($redir).'">'.$thelogout.'</a></li></ul>';
			
		} else {
			// User is NOT logged in!!!
			echo $before_widget . $before_title .'<span>'. $thelogin .'</span>' . $after_title;
			// Show any errors
			global $myerrors;

			$wp_error = new WP_Error();
								
			if ( !empty($myerrors) && is_wp_error($myerrors) ) {
				$wp_error = $myerrors;
			}
			
			/* Cookies not supported error handling */
			if ( isset($_GET['_login']) && empty($_COOKIE[TEST_COOKIE]) ) $wp_error->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));
			
			if ( $wp_error->get_error_code() ) {
			
				$errors = '';
				$messages = '';
				
				foreach ( $wp_error->get_error_codes() as $code ) {
					$severity = $wp_error->get_error_data($code);
					foreach ( $wp_error->get_error_messages($code) as $error ) {
						if ( 'message' == $severity )
							$messages .= '	' . $error . "<br />\n";
						else
							$errors .= '	' . $error . "<br />\n";
					}
				}				
				
				if ( !empty($errors) )
					echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
				if ( !empty($messages) )
					echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
			}
			// login form		
			echo '<form method="post" action="'.wp_sidebarlogin_current_url().'">';
			?>
			<p><label for="user_login"><?php echo $theusername; ?></label><br/><input name="log" value="<?php echo attribute_escape(stripslashes($_POST['log'])); ?>" class="mid" id="user_login" type="text" /></p>
			<p><label for="user_pass"><?php echo $thepassword; ?></label><br/><input name="pwd" class="mid" id="user_pass" type="password" /></p>			

			<?php
			// OpenID Plugin (http://wordpress.org/extend/plugins/openid/) Integration
			if (function_exists('openid_wp_login_form')) {
			
				echo '<input type="hidden" name="redirect_to" value="'.wp_sidebarlogin_current_url().'" />';
				
				//openid_wp_login_form();
				echo '<hr id="openid_split" />';
			
				echo '
				<p>
					<label for="openid_field">' . __('Or login using an <a href="http://openid.net/what/" title="Learn about OpenID">OpenID</a>', 'sblogin') . '</label>
					<input type="text" name="openid_identifier" id="openid_field" class="input mid" value="" /></label>
				</p>';		
			}			
			
			?>
			
			<p class="rememberme"><input name="rememberme" class="checkbox" id="rememberme" value="forever" type="checkbox" /> <label for="rememberme"><?php echo $theremember; ?></label></p>
			<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" value="<?php echo $thelogin; ?> &raquo;" />
			
			<input type="hidden" name="sidebarlogin_posted" value="1" />
			<input type="hidden" name="testcookie" value="1" /></p>
			
			<?php
			// Facebook Plugin
			if (function_exists('fbc_init_auth')) do_action('fbc_display_login_button');
			?>
			
			</form>
			<?php 			
			// Output other links
			$isul = false;	/* ms for w3c - suggested by dev.xiligroup.com */		
			if (get_option('users_can_register') && get_option('sidebarlogin_register_link')=='yes') { 
				// MU FIX
				global $wpmu_version;
				if (empty($wpmu_version)) {
					echo '<ul class="sidebarlogin_otherlinks">';
					$isul = true;
					?>
						<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=register" rel="nofollow"><?php echo $theregister; ?></a></li>
					<?php 
				} else {
					echo '<ul class="sidebarlogin_otherlinks">';
					$isul = true;
					?>
						<li><a href="<?php bloginfo('wpurl'); ?>/wp-signup.php" rel="nofollow"><?php echo $theregister; ?></a></li>
					<?php 
				}
			}
			if (get_option('sidebarlogin_forgotton_link')=='yes') : 
				if ($isul == false) {
					echo '<ul class="sidebarlogin_otherlinks">';
					$isul = true;
				}
				?>
				<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php echo $thepasslostfound; ?>" rel="nofollow"><?php echo $thelostpass; ?></a></li>
				<?php 
			endif; 
			if ($isul) echo '</ul>';	
		}		
			
		// echo widget closing tag
		echo $after_widget;
}

if (class_exists('WP_Widget')) {
	
	function widget_wp_sidebarlogin_init() {
		// New Style widgets
		class SidebarLoginMultiWidget extends WP_Widget {
		    function SidebarLoginMultiWidget() {  
		        $widget_ops = array('description' => __( 'Sidebar Login.','sblogin') );
				$this->WP_Widget('wp_sidebarlogin', __('Sidebar Login','sblogin'), $widget_ops);  
		    }
		    function widget($args, $instance) {    
		        
		        widget_wp_sidebarlogin($args);
		
		    }
		} 
		register_widget('SidebarLoginMultiWidget');
	}
	add_action('init', 'widget_wp_sidebarlogin_init', 1);
	
} else {
	// Legacy Widgets
	function widget_wp_sidebarlogin_init() {
		if ( !function_exists('register_sidebar_widget') ) return;
		// Register widget for use
		register_sidebar_widget(array('Sidebar Login', 'widgets'), 'widget_wp_sidebarlogin');
	}
	add_action('widgets_init', 'widget_wp_sidebarlogin_init');
}

function widget_wp_sidebarlogin_check() {

	// Add options - they may not exist
	add_option('sidebarlogin_login_redirect','','no');
	add_option('sidebarlogin_logout_redirect','','no');
	add_option('sidebarlogin_register_link','yes','no');
	add_option('sidebarlogin_forgotton_link','yes','no');
	add_option('sidebarlogin_logged_in_links', "<a href=\"".get_bloginfo('wpurl')."/wp-admin/\">".__('Dashboard','sblogin')."</a>\n<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\">".__('Profile','sblogin')."</a>",'no');

	// Set a cookie now to see if they are supported by the browser.
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
	if ( SITECOOKIEPATH != COOKIEPATH )
		setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

	if (isset($_POST['sidebarlogin_posted'])) {
	
		global $myerrors;
		$myerrors = new WP_Error();
		
		nocache_headers();

		$secure_cookie = '';
		
		$redir = trim(stripslashes(get_option('sidebarlogin_login_redirect')));
		if ($redir && !empty($redir)) $redirect_to = $redir;
		else $redirect_to = wp_sidebarlogin_current_url('nologout');

		// If the user wants ssl but the session is not ssl, force a secure cookie.
		if ( !empty($_POST['log']) && !force_ssl_admin() ) {
			$user_name = sanitize_user(stripslashes($_POST['log']));
			if ( $user = get_userdatabylogin($user_name) ) {
				if ( get_user_option('use_ssl', $user->ID) ) {
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}

		if ( $redirect_to ) {
			// Redirect to https if user wants ssl
			if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
				$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
		}

		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) ) $secure_cookie = false;

		$user = wp_signon('', $secure_cookie);

		$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $redirect_to ) ? $redirect_to : '', $user);

		if ( $user && !is_wp_error($user) ) {
				wp_safe_redirect($redirect_to);
				exit;
			//}
		} elseif ($user) {
			$myerrors = $user;
			if ( empty($_POST['log']) && empty($_POST['pwd']) ) {
				$myerrors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username & password.', 'sblogin'));
			}
		}		
	}
}

if ( !function_exists('wp_sidebarlogin_current_url') ) :
function wp_sidebarlogin_current_url($url = '') {

	global $wpdb, $post, $cat, $tag, $author, $year, $monthnum, $day, $wp_query;
	$pageURL = "";

	if ( is_home() && $wp_query->is_posts_page==1)
	{
		$pageURL = get_permalink(get_option('page_for_posts'));
	}
	elseif (is_home() || is_front_page()) 
	{
		$pageURL = get_bloginfo('url');
	}
	elseif (is_single() || is_page())
	{
		$pageURL = get_permalink($wp_query->post->ID);
	}
	elseif (is_category()) 
	{
		$pageURL = get_category_link($cat);
	}
	elseif (is_tag()) 
	{
		$tag_id = $wpdb->get_var("SELECT ".$wpdb->terms.".term_id FROM $wpdb->term_taxonomy
			LEFT JOIN $wpdb->terms
			ON (".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id)
			WHERE ".$wpdb->terms.".slug = '$tag'
			AND ".$wpdb->term_taxonomy.".taxonomy = 'post_tag' LIMIT 1");
		$pageURL = get_tag_link($tag_id);
	}
	elseif (is_author()) 
	{
		$pageURL = get_author_posts_url($author);
	}
	elseif (is_date())
	{

		if ($day) 
		{
			$pageURL = get_day_link( $year,  $month,  $day);
		}
		elseif ($monthnum) 
		{
			$pageURL = get_month_link( $year,  $monthnum,  $day);
		}
		elseif ($year) 
		{
			$pageURL = get_year_link( $year,  $monthnum,  $day);
		}

	}
	elseif (is_search()) 
	{
		$pageURL = get_bloginfo('wpurl');
		if ("/" != substr($pageURL, -1)) $pageURL = $pageURL . "/";
		$pageURL .= '?s='.stripslashes(strip_tags($_REQUEST['s'])).'';
	}
	
	if (!$pageURL || $pageURL=="" || !is_string($pageURL)) {
		$pageURL = "";
		$pageURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}		
		
		//————–added by mick 
		if (!strstr(get_bloginfo('url'),'www.')) $pageURL = str_replace('www.','', $pageURL );
		//——————–	
	}
	if ($pageURL && !is_search()) if ("/" != substr($pageURL, -1)) $pageURL = $pageURL . "/";

	if ($url != "nologout") {
		if (!strpos($pageURL,'_login=')) {
			$rand_string = md5(uniqid(rand(), true));
			$rand_string = substr($rand_string, 0, 10);
			
			if (strpos($pageURL,'?')) 
				if (substr($pageURL,-1)=='/') 
					$pageURL = substr($pageURL,0,-1);
			
			$rand = (!strpos($pageURL,'?')) ? '?_login='.$rand_string : '&amp;_login='.$rand_string;
			$pageURL .= $rand;
		}	
	}
	
	if ( force_ssl_login() || force_ssl_admin() ) {
	    $pageURL = str_replace( 'http://', 'https://', $pageURL );
	}
	
	return $pageURL;
}
endif;

function wp_sidebarlogin_css() {
    $myStyleFile = WP_PLUGIN_URL . '/sidebar-login/style.css';
    wp_register_style('wp_sidebarlogin_css_styles', $myStyleFile);
    wp_enqueue_style( 'wp_sidebarlogin_css_styles');
}

// Run code and init
add_action('wp_print_styles', 'wp_sidebarlogin_css');
add_action('init', 'widget_wp_sidebarlogin_check',0);
add_action('admin_menu', 'wp_sidebarlogin_menu');
?>