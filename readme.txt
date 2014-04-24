=== WP Club Manager ===
Contributors: Clubpress
Tags: clubs, teams, sports team, sports club, club management, team management, league tables, football, rugby, soccer, hockey, ice hockey
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZGGZXX2EQTZ9E
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.0.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WP Club Manager is a powerful tool to help you run any sports club website easily and quickly.

== Description ==

Create and manage a sports club website quickly and easily using WordPress. Manage multiple teams, player profiles and statistics, club staff, sponsors, league tables and keep track of upcoming and played matches all within one single plugin.

= Features =

Comes with all the tools you need to manage your sports club or team website. Features include:

* Can be used for almost any team sport
* Manage multiple teams
* Player and staff profiles
* Player ratings
* Player stats for each season
* Fixtures and results tables
* League tables
* Match reports
* Sponsors
* Fixture, results, player, table and sponsor widgets

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of WP Club Manager, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “WP Club Manager” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work as expected but as always, it is recommended that you backup your site just in case any problems should occur during the update process.

== Frequently Asked Questions ==

= Where can I find documentation and support for the WP Club Manager plugin? =

You can find full documentation for the plugin at [WP Club Manager documentation](http://wpclubmanager.com/docs/). Support is available in our [Support Forum](http://wpclubmanager.com/support/)

= Where can I report bugs or contribute to the project? =

You can help improve this plugin by reporting any bugs or contributing to the source code at our [Github repository](https://github.com/ClubPress/wpclubmanager).

== Screenshots ==

1. ...
2. ....

== Changelog ==

= 1.0.3 - 23/04/2014

* Fix - Fixed shortcode buttons compatibility with WP 3.9

= 1.0.3 - 13/04/2014

* Tweak - Added height and weight settings for players
* Tweak - Added height and weight to frontend templates
* Tweak - Added jobs filter to staff shortcode
* Tweak - Added classes to some `<th>` elements in single-match lineup template
* Tweak - Added number column to table in match lineup and removed number from name column
* Tweak - Improved php class autoloading
* Tweak - Replaced `<div>` with `<h4>` for club title in fixture/results widgets
* Tweak - Removed status column from matches shortcode, added status class and `<span>` to result column
* Fix - Fixed localisation
* Fix - Added .pot file
* Fix - Possible fix for 'expecting array' warning when adding/editing clubs
* Fix - Fixed division by zero warning in player stats tables
* Fix - Fixed broken wpclubmanager_get_template_part() function
* Fix - Fixed display of recorded stats in single-match lineup

= 1.0.2 - 30/03/2014

* Fix - Fixed match players not being saved
* Fix - Fixed 404 on scheduled matches
* Fix - Converted 'date' to 'wpcm-date' in frontend css
* Fix - Fixture widget order and orderby

= 1.0.1 - 29/03/2014

* Tweak - Improved admin UI
* Tweak - Added admin menu logo
* Tweak - Removed some background and color properties to improve frontend CSS compatibility with themes
* Tweak - Disabled manual player stats input for All Seasons. Makes sure stats are assigned to a season
* Fix - Total player stats input fixed
* Fix - Average rating calculated on player profiles
* Fix - Average rating label on player profiles and player lists
* Fix - Fixed manual club stats not updating

= 1.0.0 - 18/03/2014

* Initial release

== Upgrade Notice ==

= 1.0.3 =
1.0.3 is a minor update with a few minor fixes and improvements. There shouldn't be any problems upgrading but some styles may be affected depending on your theme.