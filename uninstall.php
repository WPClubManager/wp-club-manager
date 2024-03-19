<?php
/**
 * WPClubManager Uninstall
 *
 * Uninstalling WPClubManager deletes user roles and options.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Uninstaller
 * @version     1.1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb, $wp_roles;

// Roles + caps
$installer = include 'includes/class-wpcm-install.php';
$installer->remove_roles();

require_once 'includes/class-wpcm-reset-database.php';

( new WPCM_Reset_Database() )->reset();
