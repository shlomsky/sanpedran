=== WP-Weather ===
Contributors: Matt Brotherson
Tags: weather,widget,shortcode
Requires at least: 2.7
Tested up to: 2.8
Stable tag: trunk

Display weather information for your locale.

== Description ==

Weather.com widget - shows forecast information for a city.  Displays image related to current conditions.  Weather can also be inserted into a post or page via the shortcode [weather_display].


== Installation ==

###Upgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

###Uploading The Plugin###

Extract all files from the ZIP file, **making sure to keep the file/folder structure intact**, and then upload it to `/wp-content/plugins/`.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Plugin Activation###

Go to the admin area of your WordPress install and click on the "Plugins" menu. Click on "Activate" for the "WP-Weather" plugin.

###Plugin Usage###

Use as a widget or the shortcode [weather_display] on a page or post.

== Frequently Asked Questions ==
###Q:  Where did my widget go?###

A:  Due to vast changes in the plugin architecture within Wordpress, a complete re-write of WP-Weather was necessary.  Care was utilized to have this as seamless as possible but the old widget method used in WP-Weather was obsolete.  You must now re-add the widget to your sidebar via the admin page for widgets.

== Screenshots ==

1. Widget display with default theme.
2. Setup page.

== ChangeLog ==


**Version 0.3.3**

* Added legacy support for template function weather_display().

**Version 0.3.2**

* Fixed DB storage of XML so not to hammer weather.com's servers.

**Version 0.3.1**

* Fixed hardcoded XML parser (uses SimpleXML for PHP5 or IsterXML for PHP4.)
* Fixed weather units in forecast_url.
* Added wind information in options.  (paul van zwalenburg - http://www.seattleoutrigger.com/)
* Minor code improvements.

**Version 0.3**

Major overhaul, mainly to extend flexibility and embrace current code standards for Wordpress.
