<?php
/**
 * Update WPClubManager to 1.4.0
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Updates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Update Roles and capabilities
remove_role( 'team_manager' );
$player = get_role( 'player' );
$capabilities = array();
$capabilities['core'] = array( 
	'manage_wpclubmanager',
	'level_1',
	'level_0',
	'delete_posts',
    'edit_posts',
    'upload_files' 
);
$capability_types = array( 'wpcm_match', 'wpcm_club', 'wpcm_player', 'wpcm_sponsor', 'wpcm_staff' );
foreach ( $capability_types as $capability_type ) {
	$capabilities[ $capability_type ] = array(
		'edit_' . $capability_type,
		'read_' . $capability_type,
		'delete_' . $capability_type,
		'edit_' . $capability_type . 's',
		'edit_others_' . $capability_type . 's',
		'publish_' . $capability_type . 's',
		'read_private_' . $capability_type . 's',
		'delete_' . $capability_type . 's',
		'delete_private_' . $capability_type . 's',
		'delete_published_' . $capability_type . 's',
		'delete_others_' . $capability_type . 's',
		'edit_private_' . $capability_type . 's',
		'edit_published_' . $capability_type . 's',

		// Terms
		'manage_' . $capability_type . '_terms',
		'edit_' . $capability_type . '_terms',
		'delete_' . $capability_type . '_terms',
		'assign_' . $capability_type . '_terms'
	);
}
// Remove player caps
foreach ( $capabilities as $cap_group ) {
	foreach( $cap_group as $cap ) {
    	$player->remove_cap( $cap );
	}
}
$staff = get_role( 'staff' );
$caps = array(
    'level_9',
	'level_8',
	'level_7',
	'level_6',
	'level_5',
	'level_4',
	'level_3',
	'level_2',
	'level_1',
	'level_0',
	'read',
	'read_private_pages',
	'read_private_posts',
	'edit_users',
	'edit_posts',
	'edit_pages',
	'edit_published_posts',
	'edit_published_pages',
	'edit_private_pages',
	'edit_private_posts',
	'edit_others_posts',
	'edit_others_pages',
	'publish_posts',
	'publish_pages',
	'delete_posts',
	'delete_pages',
	'delete_private_pages',
	'delete_private_posts',
	'delete_published_pages',
	'delete_published_posts',
	'delete_others_posts',
	'delete_others_pages',
	'manage_categories',
	'manage_links',
	'moderate_comments',
	'unfiltered_html',
	'upload_files',
	'export',
	'import',
	'list_users'
);
// Add staff caps
foreach( $caps as $c) {
    $staff->add_cap( $c );
}