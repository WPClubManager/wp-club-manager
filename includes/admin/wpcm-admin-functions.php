<?php
/**
 * Admin Functions
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get all WPClubManager screen ids
 *
 * @return array
 */
function wpcm_get_screen_ids() {
	$wpcm_screen_id = strtolower( __( 'wpcm-settings', 'wpclubmanager' ) );

    return apply_filters( 'wpclubmanager_screen_ids', array(
    	'dashboard_page_wpcm-getting-started',
    	'toplevel_page_' . $wpcm_screen_id,
    	$wpcm_screen_id . '_page_wpcm-settings',
    	$wpcm_screen_id . '_page_wpcm-addons',
    	'club-manager_page_wpcm-status',
    	'club-manager_page_wpcm-tools',
    	'edit-wpcm_club',
    	'wpcm_club',
    	'edit-wpcm_match',
    	'wpcm_match',
    	'edit-wpcm_player',
    	'wpcm_player',
    	'edit-wpcm_staff',
    	'wpcm_staff',
    	'edit-wpcm_sponsor',
    	'wpcm_sponsor',
    	'edit-wpcm_club_cat'
    ) );
}

/**
 * Output admin fields.
 *
 * Loops though the wpclubmanager options array and outputs each field.
 *
 * @param array $options Opens array to output
 */
function wpclubmanager_admin_fields( $options ) {
    if ( ! class_exists( 'WPCM_Admin_Settings' ) )
        include 'class-wpcm-admin-settings.php';

    WPCM_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 *
 * @access public
 * @param array $options
 * @return void
 */
function wpclubmanager_update_options( $options ) {
    if ( ! class_exists( 'WPCM_Admin_Settings' ) )
        include 'class-wpcm-admin-settings.php';

    WPCM_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 *
 * @param mixed $option
 * @return string
 */
function wpclubmanager_settings_get_option( $option_name, $default = '' ) {
    if ( ! class_exists( 'WPCM_Admin_Settings' ) )
        include 'class-wpcm-admin-settings.php';

    return WPCM_Admin_Settings::get_option( $option_name, $default );
}