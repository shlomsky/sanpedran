=== Mingle ===
Contributors: supercleanse
Donate link: http://blairwilliams.com/mingle-donate
Tags: social, social network, network, networking, facebook, linkedin, twitter, mingle, wpmingle, wp-mingle, wpmingle.com, blair, blair williams, blairwilliams.com, pretty link, prettylink, pretty-link, buddypress, widget, admin, page, pages, post, posts, plugin, sidebar, comments, images, mingle, friends, friend, avatar, avatars, gravatar, directory, users, user, profile, profiles, activity, activities, email, notification, notify, privacy, member, membership site, members, membership sites, community, communities, messaging, messages, message, ajax, javascript
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 0.0.20

The simplest way to turn your standard WordPress website with a standard WordPress theme into a Social Network.

== Description ==
The simplest way to turn your WordPress website into a Social Network. Mingle uses your standard WordPress website and standard WordPress theme to create profile pages, user friending, profile page posts, profile activities, social comments, email notifications (with privacy settings) and a full directory of members. Mingle can be used to start a new Social Networking website or can be used with the existing registered users on your Website. So go ahead and try it out--give your users a more social experience on your website!

Note: This software still hasn't been tested with every theme or browser in existence. It has been tested thoroughly on the [Thesis WordPress Theme](http://blairwilliams.com/thesis "Thesis WordPress Theme") (aff link) and many others. If you experience problems, please leave a comment here:

[Mingle WordPress Plugin](http://blairwilliams.com/mingle "Mingle WordPress Plugin")

= Check it Out! =
You can now try Mingle out by joining my brand new Mingle Community! Come be my friend at:

[Mingle WordPress Community](http://wpmingle.com "Mingle WordPress Community")

= Features =
* User Profile Pages
* Ability to Upload custom avatars (falls back on Gravatar)
* User Friending
* User Profile Posting (for friends)
* User Profile Post Commenting (for friends)
* Friend Activity Pages
* Full Member Directory
* Login & Navigation Widget
* Random or Recent Users Widget
* Email Notifications
* User Email Notification Opt-Outs

= Translations =

Mingle is currently available in the following languages thanks to awesome translators!

* Albanian (sq_AL) -- [Klajdi Hena](http://dubupee.com "Klajdi Hena")
* Bulgarian (bg_BG) -- [Ivo Minchev](http://www.ivominchev.com "Ivo Minchev")
* Dutch (nl_NL) -- [Lourens Rolograaf](http://rolograaf.com "Lourens Rolograaf")
* English
* French (fr_FR) -- [Andre Lefebvre](http://www.gatewaytranslation.com "Andre Lefebvre")
* German (de_DE) -- [Bernd Zolchhofer](http://www.mindgears.de "Bernd Zolchhofer")
* Italian (it_IT) -- [Gianluca Storani](http://www.giast.com "Gianluca Storani")
* Polish (pl_PL) -- [Jacek Dudzic](http://www.jacekdudzic.eu "Jacek Dudzic")
* Spanish (es_ES) -- Carlos Mejias
* Swedish (sv_SE) -- [Philip Holm](http://www.exceedingthelimits.se "Philip Holm")
* Turkish (tr) -- [Mertcan Temel](http://www.vizyoncu.com "Mertcan Temel")

If you don't see your language on this list, please contact me on my blog at http://blairwilliams.com or at http://wpmingle.com and I can give you instructions on how to translate Mingle.

== Installation ==
1. Upload 'mingle.zip' to the '/wp-content/plugins/' directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Create a page for "Activity", "Profile", "Profile Settings", "Friends", "Friend Requests" and "Directory" -- and then configure mingle to use them in the Mingle menu.

If you have any issues please leave a comment here:

[Mingle WordPress Plugin](http://blairwilliams.com/mingle "Mingle WordPress Plugin")

== Frequently Asked Questions ==
[Mingle FAQ](http://blairwilliams.com/mingle "Mingle FAQ")

== Changelog ==

= 0.0.20 =
* Added a sweet login form that stays on the front end
* Added Pretty Profile Urls option that can be enabled in the admin
* Fixes to the avatar system ... classes should line up for mingle pages and not be present for other code calling WordPress' get_avatar() method
* Optimized user dropdown calls in admin
* Updated Spanish, Bulgarian, Dutch, & Turkish
* Added invisible users feature -- you can select users that will be invisible in the Mingle System
* No longer requiring pluggable.php up-front (hopefully this will help with plugin / theme conflicts)
* Made a significant SQL performance fix to the activity page
* Fixed some bugs in the profile page display
* Added user tagging in posts and comments

= 0.0.19 =
* Added French Translations -- Thank You [Andre Lefebvre!](http://www.gatewaytranslation.com "Andre Lefebvre!")
* Added Swedish Translations -- Thank You [Philip Holm!](http://www.exceedingthelimits.se "Philip Holm!")
* Added Turkish Translations -- Thank You [Mertcan Temel!](http://www.vizyoncu.com "Mertcan Temel!")
* Updated German Translations -- Thank You [Bernd Zolchhofer!](http://www.mindgears.de "Bernd Zolchhofer!")
* Updated Bulgarian Translations -- Thank You [Ivo Minchev!](http://www.ivominchev.com "Ivo Minchev!")
* Added tooltips to the user grid instead of printing the screenname below the avatar
* Went back to a standard screenname instead of a dynamic one ... will make it easier to reference users now and paving the way for simple pretty profile urls
* Added real name field to user profile
* Enhanced user signup process, including new welcome emails, the signup process never exposes the user to wordpress anymore
* Fixed numerous formatting issues
* Complete overhaul of the avatar system -- much less error prone, efficient and friendly with other plugins
* Fixed avatar override in WordPress Admin
* Fixed growable text areas when loaded via ajax
* Optimized user record calls and reduced the number of calls by caching user records
* Added emoticons (smilies)
* Re-named javascript and php functions to avoid conflicts
* Fixed several javascript issues
* Fixed the user dropdown performance issue in the mingle admin area
* Added a board reference to the activity board posts (whose board was the post posted on?)
* Fixed a display bug
* Fixed some formatting bugs in the admin
* Added some more hooks and revamped the enqueue script mechanism

= 0.0.18 =
* Fixed the "Show Older Posts" link
* Added German Translations -- Thanks [Bernd Zolchhofer!](http://www.mindgears.de "Bernd Zolchhofer!")

= 0.0.17 =
* Added a single post view to make locating posts easier
* Single post link goes out in board post notifications
* Fixed commenting on older post refresh bug
* Significantly reduced the number of database calls when posting comments
* Added Bulgarian Translations -- Thanks [Ivo Minchev!](http://www.ivominchev.com "Ivo Minchev!")
* Added Spanish Translations -- Thanks Carlos Mejias!

= 0.0.16 =
* Added growable textareas
* Added friend search
* Fixed javascript bug
* Fixed comment permission issues
* Optimized more database queries
* Updated Dutch language translations

= 0.0.15 =
* Changed the way screennames are handled (made them user configurable)
* Added user filter for directory
* Fixed some commenting permission bugs
* Optimized some database calls and reduced number of calls

= 0.0.14 =
* Updated Dutch and Polish -- Thanks Rolograaf & Jacek Dudzic!
* Fixed a PHP4 error affecting some users
* Fixed some of the activity screen bugs
* Fixed the comment not saving bug

= 0.0.13 =
* Added translation to Polish -- Thanks [Jacek Dudzic!](http://www.jacekdudzic.eu "Jacek Dudzic!")
* Added loading gif to board posts, comment posts, friend requests, friend accepting, friend ignoring and Show Older Posts
* Fixed HTML / CSS and other styling issues
* Fixed the preg_replace compilation error

= 0.0.12 =
* Added translation to Italian -- Thanks [Gianluca Storani!](http://www.giast.com/ "Gianluca Storani!")
* Added the ability to select multiple users as default friends
* Added the ability to apply default friends to existing users

= 0.0.11 =
* Created a way to view older posts on the activity and profile boards
* Fixed another text encoding issue with board posts,
* Added # of friend requests badge to friend requests login widget label
* Updated dutch translations -- Thanks [Rolograaf!](http://rolograaf.nl/ "Rolograaf!")
* Integrated mingle with wordpress comments (avatar override and profile link override for registered users)
* Fixed avatar upload & display issues and fixed numerous other bugs.

= 0.0.10 =
* Fixed CSS
* Added Profile Edit Link onto profile page
* Marked strings for translation throughout mingle
* Added a pot file in the i18n for translations (will try to update it on every release)
* Translated into Dutch (nl_NL) -- thanks to Lourens Rolograaf at [Rolograaf.nl](http://rolograaf.nl/ "Rolograaf.nl")!
* Translated into Albanian (sq_AL) -- thanks to Klajdi Hena at [Dub U Pee](http://dubupee.com/ "Dub U Pee")!
* Added truncating on the Profile Board with read more link for larger links
* Created a configurable signup page
* Created a configurable login page
* Created a template tag for loading the user grid: mngl_display_user_grid($cols='3', $rows='2', $type='random')
* Created a template tag for loading the login / nav: mngl_display_login_nav()

= 0.0.09 =
* Fixed a minor issue with the comment form

= 0.0.08 =
* HTML will no longer be visible in board posts & comments...

= 0.0.07 =
* Fixed Some CSS Styling
* Added Default User Assignment Feature

= 0.0.06 =
* Fixed AJAX loading error cleaned up CSS code

= 0.0.05 =
* Fixed the redirection bug

= 0.0.03 =
* Fixed some bugs with the Profile Status Board

= 0.0.02 =
* Altered Install Routines

= 0.0.01 =
* First Release

== Screenshots ==
[Mingle Screenshots](http://blairwilliams.com/mingle "Mingle Screenshots")
