<?php
/**
 * Widget Functions
 *
 * Widget related functions and widget registration
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include widget classes
include_once( 'abstracts/abstract-wpcm-widget.php' );
include_once( 'widgets/class-wpcm-widget-fixtures.php');
include_once( 'widgets/class-wpcm-widget-results.php');
include_once( 'widgets/class-wpcm-widget-standings.php');
include_once( 'widgets/class-wpcm-widget-sponsors.php');
include_once( 'widgets/class-wpcm-widget-players.php');
include_once( 'widgets/class-wpcm-widget-birthdays.php');

/**
 * Register Widgets
 *
 * @since 1.3.0
 */
function wpcm_register_widgets() {
	register_widget( 'WPCM_Fixtures_Widget' );
	register_widget( 'WPCM_Players_Widget' );
	register_widget( 'WPCM_Results_Widget' );
	register_widget( 'WPCM_Sponsors_Widget' );
	register_widget( 'WPCM_Standings_Widget' );
	register_widget( 'WPCM_Birthdays_Widget' );
}
add_action( 'widgets_init', 'wpcm_register_widgets' );