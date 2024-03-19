<?php
/**
 * Widget Functions
 *
 * Widget related functions and widget registration
 *
 * @author      WooThemes
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include widget classes
require_once 'abstracts/class-wpcm-widget.php';
require_once 'widgets/class-wpcm-fixtures-widget.php';
require_once 'widgets/class-wpcm-results-widget.php';
require_once 'widgets/class-wpcm-standings-widget.php';
require_once 'widgets/class-wpcm-sponsors-widget.php';
require_once 'widgets/class-wpcm-players-widget.php';
require_once 'widgets/class-wpcm-birthdays-widget.php';

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
