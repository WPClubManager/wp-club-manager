=== WP Club Manager ===
Contributors: ClubPress
Tags: clubs, teams, sports club, club management, team management, league tables, football, rugby, soccer, field hockey, ice hockey, baseball, basketball, aussie rules, netball, volleyball
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZGGZXX2EQTZ9E
Requires at least: 3.8
Tested up to: 3.9.1
Stable tag: 1.1.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WP Club Manager is a powerful tool to help you run a sports club website easily and quickly.

== Description ==

Create and manage a sports club website quickly and easily using WordPress. Manage multiple teams, player profiles and statistics, club staff, sponsors, league tables and keep track of upcoming and played matches all within one single plugin.

= Features =

Comes with all the tools you need to manage your sports club or team website. Features include:

* Supported sports: soccer, baseball, basketball, ice hockey, field hockey, netball, volleyball, Aussie rules, American football, rugby, gaelic football, hurling and floorball
* Manage multiple teams
* Player and staff profiles
* Player ratings
* Player stats for each season
* Fixtures and results table
* League tables
* Match reports
* Sponsors
* Fixture, results, player, table and sponsor widgets
* Flexible templates

= Available Languages =

* English - UK (en_GB)
* Arabic – العربية (ar)
* Czech – Čeština (cs_CZ)
* German - Deutsch (de_DE)
* French – Français (fr_FR)
* Greek - Ελληνικά (el_GR)
* Italian - Italiano (it_IT)
* Polish - Polski (pl_PL)
* Portuguese (Brazil) – Português do Brasil (pt_BR)
* Spanish - Español (es_ES)

= Get Involved =

Developers can contribute to the source code on the [WP Club Manager GitHub Repository](https://github.com/ClubPress/wpclubmanager).

Translators can contribute new languages to WP Club Manager through [Transifex](https://www.transifex.com/projects/p/wp-club-manager/).

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

1. Add matches and pick match teams
2. Choosing your team is easy and quick
3. Extensive player profiles with season stats
4. A slick admin interface blends in with WordPress

== Changelog ==

= 1.1.5 - 21/06/2014

* Feature - Add gaelic football
* Feature - Add hurling
* Feature - Add floorball
* Fix - Fixed missing cards section on match stats for subs
* Localisation - Add Arabic language
* Localisation - Add French language
* Localisation - Add Portuguese (Brazil) language
* Localisation - Updated German and Czech language files

= 1.1.4 - 13/06/2014

* Tweak - Replaced '0' with '-' in frontend match lineup
* Fix - Possible fix for localised date in match/results widget
* Fix - Fixed broken player image placeholder
* Localisation - Add Italian language
* Localisation - Add Spanish language
* Localisation - Add Czech language

= 1.1.3 - 10/06/2014

* Fix - Fixed missing title on match pages
* Fix - Fixed default club filter in fixtures and results widgets
* Fix - Fixed staff single profile display options
* Tweak - Added display option to staff display options
* Localisation - Updated wpclubmanager.pot
* Localisation - Add Greek language

= 1.1.2 - 02/06/2014

* Feature - Add staff shortcode and staff profile templates
* Tweak - Add staff single profiles
* Tweak - Alter staff shortcode to match players shortcode
* Tweak - Add alphabetical sorting option to player shortcode
* Fix - Fix broken staff shortcode filter
* Localisation - Updated wpclubmanager.pot

= 1.1.1 - 30/05/2014

* Feature - Added netball and volleyball
* Feature - Added shortcode templates
* Feature - Added fixtures and results widget templates
* Tweak - Add preset positions for rugby and hockey (field and ice)
* Tweak - Improved widget functions
* Tweak - Improved queries in fixtures and results widgets
* Tweak - Optimised matches shortcode query
* Tweak - Hidden duplicate title on players and standings widgets
* Tweak - Removed sponsors archive
* Tweak - Add theme support notice
* Localisation - Add German language by King3R
* Localisation - Add Polish language by rychu_cmg


= 1.1.0 - 08/05/2014

* Feature - Choose sport for preset player stats
* Feature - Loads more stats for each specific sport
* Feature - Preset player positions for each sport
* Feature - Added overtime losses to standings (Ice Hockey)
* Feature - Added win percentage to standings
* Feature - Added bonus points to standings (Rugby)
* Feature - New welcome page with plugin configuration options
* Tweak - Added experience settings for players
* Tweak - Added display player thumbnails option to player widget
* Tweak - Added display player thumbnails option to player shortcode
* Tweak - Added option to display club badge in standings
* Tweak - Improved match post player table usability
* Tweak - New sponsors menu dashicon
* Fix - Fixed match post player counter
* Fix - Fixed result widget showing 0-0 score before result added
* Fix - Fixed match post team players filter behaviour
* Localisation - Updated language files

= 1.0.4 - 23/04/2014

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

= 1.1.0 =

THIS IS A MAJOR UPGRADE AND WE RECOMMEND THAT YOU BACKUP YOUR FILES AND DATABASE BEFORE UPGRADING! The stats system has been revamped and any player stats that have already been added will probably get mixed up and need to be redone. I apologise for the inconvenience this will cause.