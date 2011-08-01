=== The Events Calendar ===

Contributors: Kelsey Damas, Matt Wiebe, Justin Endler, Reid Peifer produced by Shane & Peter, Inc.
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10750983
Tags: widget, events, simple, tooltips, grid, month, list, calendar, event, venue, eventbrite, registration, tickets, ticketing, eventbright, api, dates, date, plugin, posts, sidebar, template, theme, time, google maps, google, maps, conference, workshop, concert, meeting, seminar, summit, forum
Requires at least: 2.8
Tested up to: 2.9.1
Stable tag: 1.5.2

== Description ==

The Events Calendar plugin enables you to rapidly create and manage events using the post editor.  Features include optional Eventbrite integration, Google Maps integration as well as default templates such as a calendar grid and event list for streamlined one click installation.

= The Events Calendar =

* Manage event details right from your post editor
* Upcoming Events Widget
* Provides full template out of the box (month and list view)
* Extensive template tags for customization
* MU Compatible
* Google Maps Integration
* Posts are automatically moved to the top of the loop on the day of the event
* Month view with tooltips
* Includes support for venue, cost, address, start and end time, google maps link
* Support for international addresses and time
* Optional Ticketing With Eventbrite Integration - http://www.eventbrite.com/ - though the Eventbrite for The Events Calendar plugin (http://wordpress.org/extend/plugins/eventbrite-for-the-events-calendar/).

= Upcoming Features =

* Option to disable re-posting of event
* Repeated events
* Language files
* Dynamic categories (rather than requiring the use of event)
* Event subcategories
* A bunch of UI cleanup

Please visit the forum for feature suggestions: http://wordpress.org/tags/the-events-calendar/

This plugin is actively supported and we will do our best to help you. In return we simply as 2 things:

1. Donate - if this is generating enough revenue to support our time it makes all the difference in the world
1. If you make a new account with Eventbrite, please use our referral code. It helps, believe me: http://www.eventbrite.com/r/simpleevents

== Installation ==

= Install =

1. Unzip the `the-events-calendar.zip` file. 
1. Upload the the `the-events-calendar` folder (not just the files in it!) to your `wp-contents/plugins` folder. If you're using FTP, use 'binary' mode.
1. Update your permalinks to ensure that the event specific rewrite rules take effect.

= Activate =

No setup required. Just plug and play!

= Requirements =

PHP 5 ONLY!!!

= Advanced =

The built in template can be overridden by files within your template.

= Default vs. Custom Templates =

The Events Calendar plugin now comes with default templates for the list view, grid view and single post view. If you would like to alter them, create a new folder called "events" in your template directory and copy over the following files from within the plugin folder (simple-events/views/):

* gridview.php
* list.php
* single.php

Edit the new files to your hearts content. Please do not edit the one's in the plugin folder as that will cause conflicts when you update the plugin to the latest release.

= Supported Variables and URLs =

This plugin registers the following rewrite rules, which controls which posts are available in the loop.  The number of posts returned defaults to 10, but is configurable by the $count parameter to get_events().

Events/Upcoming 
&cat=<eventcategory>&eventDisplay=upcoming
  
Displays events starting today in ascending date order.
  
Events/Past
&cat=<eventcategory>&eventDisplay=past

Displays events that started before today in descending date order.
  
Events/2010-01-02
&cat=<eventcategory>&eventDisplay=bydate&eventDate=2010-01-02

Displays only events that start on Jan 2, 2010.

= Template Tags =

**the_event_start_date( $id )**
**the_event_end_date( $id )**
**the_event_cost( $id )**
**the_event_venue( $id )**
**the_event_address( $id )**
**the_event_city( $id )**
**the_event_state( $id )**
**the_event_province( $id )**
**the_event_zip( $id )**
**the_event_country( $id )**
**the_event_phone( $id )**

These functions will return the metadata associated with the event. The ID is optional.

**event_google_map_link( $id )**
**get_event_google_map_link( $id )**

Echos or returns, respectively, an http:// link to google maps for the event's address.  The ID is optional.

**get_jump_to_date_calendar( )**

Returns a string containing a javascript date calendar.

**is_event( $id )**

Returns true or false if the current post is an event.  ID is optional.

**is_featured_event( $id )**

Returns true or false if the current post is a featured event.  ID is optional.

**event_style( $id )**
**get_event_style( $id )**

Echos or returns, respectively, the event class specified in the admin panel.  ID is optional.

**is_new_event_day()**

Called inside of the loop, returns true if the current post's meta_value (EventStartDate) is different than the previous post.   Will always return true for the first event in the loop.

**get_events( $count )**

Call this function in a template to query the events and start the loop.   Do not subsequently call the_post() in your template, as this will start the loop twice and then you're in trouble.

http://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query#Query_based_on_Custom_Field_and_Category

**events_displaying_past()**

returns true if the query is set for past events

For those of you who have the Eventbrite plugin turned on:

**the_event_tickets( $id, $width, $height)**

This returns an EventBrite.com embedded ticket sales inline (not wordpress) widget 

= Top of the Loop Cron =

On the day of the event (at midnight) the plugin runs a cron which updates the post date to show the even at the top of the loop.

== Screenshots ==

1. Grid View Template
1. List View Template
1. Single Post Template
1. Settings Panel
1. Post (Event) Editor
1. Widget Admin
1. Unstyled Widget

== FAQ ==

= Where do I go to file a bug or ask a question? =

Please visit the forum for questions or comments: http://wordpress.org/tags/the-events-calendar/

== Changelog ==

= 1.5.2 =

* updated ticket display to hide after event end date
* fix exception handling bugs

= 1.5.1 =

* updated single.php to improve dependency on eventbrite
* updated cost function to use filter

= 1.5 =

* Fixed a whole pile of small bugs.
* Extract Eventbrite from the Events calendar into a stand alone plugin
* Add donate links
* Add settings panel
** Default View (calendar or list) for categories
** Default country for events
** Donate toggle on/off
* Upgrade for WP 2.9

= 1.5 alpha =

* Plug and Play install including default templates (list view, grid view and post)
* Theme overwrite of default templates (see instructions)
* 12 hour / 24 hour time display options
* Work with all permalink styles
* Hide data from custom fields
* Hide Eventbrite sales box in post if there is are tickets
* Multiple javascript bug fixes
* Pull price for 1st ticket from general event price
* Add some basic error messages from Eventbrite (much more to come)
* Remove dependencies on other S&P plugins

= 1.4.1 =

* Featured event checkbox and template tag is_featured_event()

= 1.4 =

* Grid View
* Additional Internationalization support added

= 1.3 =

* Built events list widget

= 1.2 =

* Added Internationalization (translation) support
* Added international addresses
* Extracted from S&P core plugin to stand alone. 

