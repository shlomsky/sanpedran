<?php
/*
Plugin Name: Mingle
Plugin URI: http://blairwilliams.com/mingle
Description: The simplest way to turn your standard WordPress website with a standard WordPress theme into a Social Network.
Version: 0.0.20
Author: Blair Williams
Author URI: http://blairwilliams.com
Text Domain: mingle
Copyright: 2009, Blair Williams

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('MNGL_PLUGIN_NAME',"mingle");
$mngl_script_url = get_option('home') . '/index.php?plugin=' . MNGL_PLUGIN_NAME;
define('MNGL_PATH',WP_PLUGIN_DIR.'/'.MNGL_PLUGIN_NAME);
define('MNGL_IMAGES_PATH',MNGL_PATH.'/images');
define('MNGL_CSS_PATH',MNGL_PATH.'/css');
define('MNGL_JS_PATH',MNGL_PATH.'/js');
define('MNGL_I18N_PATH',MNGL_PATH.'/i18n');
define('MNGL_APIS_PATH',MNGL_PATH.'/classes/apis');
define('MNGL_MODELS_PATH',MNGL_PATH.'/classes/models');
define('MNGL_CONTROLLERS_PATH',MNGL_PATH.'/classes/controllers');
define('MNGL_VIEWS_PATH',MNGL_PATH.'/classes/views');
define('MNGL_WIDGETS_PATH',MNGL_PATH.'/classes/widgets');
define('MNGL_HELPERS_PATH',MNGL_PATH.'/classes/helpers');
define('MNGL_URL',plugins_url($path = '/'.MNGL_PLUGIN_NAME));
define('MNGL_IMAGES_URL',MNGL_URL.'/images');
define('MNGL_CSS_URL',MNGL_URL.'/css');
define('MNGL_JS_URL',MNGL_URL.'/js');
define('MNGL_SCRIPT_URL',$mngl_script_url);

// Gotta load the language before everything else
require_once(MNGL_CONTROLLERS_PATH . "/MnglAppController.php");
MnglAppController::load_language();

require_once(MNGL_MODELS_PATH.'/MnglOptions.php');

// For IIS compatibility
if (!function_exists('fnmatch'))
{
  function fnmatch($pattern, $string)
  {
    return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
  }
}

// More Global variables
global $mngl_blogurl;
global $mngl_siteurl;
global $mngl_blogname;
global $mngl_blogdescription;

$mngl_blogurl         = ((get_option('home'))?get_option('home'):get_option('siteurl'));
$mngl_siteurl         = get_option('siteurl');
$mngl_blogname        = get_option('blogname');
$mngl_blogdescription = get_option('blogdescription');

/***** SETUP OPTIONS OBJECT *****/
global $mngl_options;

$mngl_options = get_option('mngl_options');

// If unserializing didn't work
if(!$mngl_options)
{
  $mngl_options = new MnglOptions();

  delete_option('mngl_options');
  add_option('mngl_options',$mngl_options);
}
else
  $mngl_options->set_default_options(); // Sets defaults for unset options

// Instansiate Models
require_once(MNGL_MODELS_PATH . "/MnglUtils.php");
require_once(MNGL_MODELS_PATH . "/MnglUser.php");
require_once(MNGL_MODELS_PATH . "/MnglFriend.php");
require_once(MNGL_MODELS_PATH . "/MnglBoardComment.php");
require_once(MNGL_MODELS_PATH . "/MnglBoardPost.php");
require_once(MNGL_MODELS_PATH . "/MnglNotification.php");
require_once(MNGL_MODELS_PATH . "/MnglUpdate.php");

global $mngl_user;
global $mngl_friend;
global $mngl_board_comment;
global $mngl_board_post;
global $mngl_update;

$mngl_user          = MnglUser::get_stored_profile();
$mngl_friend        = new MnglFriend();
$mngl_board_comment = new MnglBoardComment();
$mngl_board_post    = new MnglBoardPost();
$mngl_notification  = new MnglNotification();
$mngl_update        = new MnglUpdate();

// Instansiate Controllers
require_once(MNGL_CONTROLLERS_PATH . "/MnglOptionsController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglProfilesController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglFriendsController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglUsersController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglBoardsController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglHelpController.php");
require_once(MNGL_CONTROLLERS_PATH . "/MnglShortcodesController.php");

global $mngl_app_controller;
global $mngl_options_controller;
global $mngl_profiles_controller;
global $mngl_friends_controller;
global $mngl_users_controller;
global $mngl_boards_controller;
global $mngl_help_controller;
global $mngl_shortcodes_controller;

$mngl_app_controller        = new MnglAppController();
$mngl_options_controller    = new MnglOptionsController();
$mngl_profiles_controller   = new MnglProfilesController();
$mngl_friends_controller    = new MnglFriendsController();
$mngl_users_controller      = new MnglUsersController();
$mngl_boards_controller     = new MnglBoardsController();
$mngl_help_controller       = new MnglHelpController();
$mngl_shortcodes_controller = new MnglShortcodesController();

// Instansiate Helpers
require_once(MNGL_HELPERS_PATH. "/MnglAppHelper.php");
require_once(MNGL_HELPERS_PATH. "/MnglOptionsHelper.php");
require_once(MNGL_HELPERS_PATH. "/MnglProfileHelper.php");
require_once(MNGL_HELPERS_PATH. "/MnglBoardsHelper.php");

global $mngl_app_helper;

$mngl_app_helper = new MnglAppHelper();

$mngl_options->set_activity_types();
$mngl_options->set_notification_types();
$mngl_options->set_default_friends();
$mngl_app_controller->setup_menus();

// Include Widgets
require_once(MNGL_WIDGETS_PATH . "/MnglLoginWidget.php");
require_once(MNGL_WIDGETS_PATH . "/MnglUsersWidget.php");

// Register Widgets
if(class_exists('WP_Widget'))
{
  add_action('widgets_init', create_function('', 'return register_widget("MnglLoginWidget");'));
  add_action('widgets_init', create_function('', 'return register_widget("MnglUsersWidget");'));
}

// Include APIs
require_once(MNGL_APIS_PATH . "/MnglBoardApi.php");
require_once(MNGL_APIS_PATH . "/MnglNotificationApi.php");

// Template Tags
if(!function_exists('mngl_display_user_grid'))
{
  function mngl_display_user_grid($cols='3', $rows='2', $type='random')
  {
    MnglShortcodesController::user_grid(array('cols' => $cols, 'rows' => $rows, 'type' => $type));
  }
}

if(!function_exists('mngl_display_login_nav'))
{
  function mngl_display_login_nav()
  {
    MnglShortcodesController::login(array());
  }
}

// Provide Back End Hooks to the Pro version of Mingle
if($mngl_update->pro_is_installed_and_authorized())
      require_once(MNGL_PATH.'/plus/mingle-plus.php');
?>
