<?php
/**
 * WPClubManager Uninstall
 *
 * Uninstalling WPClubManager deletes user roles and options.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Uninstaller
 * @version     1.1
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

global $wpdb, $wp_roles;

// Roles + caps
$installer = include( 'includes/class-wpcm-install.php' );
$installer->remove_roles();

// Delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wpcm_%';");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wpclubmanager_%';");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_wpcm_%';");

$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'wpcm_player', 'wpcm_staff', wpcm_club', 'wpcm_match', 'wpcm_sponsor' );" );

$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE wp.ID IS NULL;" );

delete_option( 'wpclubmanager_installed' );