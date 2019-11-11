<?php
/**
 * Update WPClubManager to 2.0.0
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Updates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_option( 'wpcm_mode', 'club' );

// Add existing referees to wpcm_referee_list
$args = array(
	'post_type' => 'wpcm_match',
	'posts_per_page' => -1,
);
$matches = get_posts( $args );

foreach( $matches as $match ) {
	$referee = get_post_meta( $match->ID, 'wpcm_referee', true );
	$option_list = get_option( 'wpcm_referee_list', array() );
	if( !in_array( $referee, $option_list) ) {
		$option_list[] = $referee;
		update_option( 'wpcm_referee_list', $option_list );
	}
}

// Attempt to split existing player/staff names
$args = array(
	'post_type' => 'wpcm_player',
	'posts_per_page' => -1,
);
$players = get_posts( $args );

foreach( $players as $player ) {

	$name = get_the_title( $player );
	$name = trim( $name );
	if( strpos( $name, ' ' ) === false ) {
		update_post_meta( $player->ID, '_wpcm_firstname', '' );
		update_post_meta( $player->ID, '_wpcm_lastname', $name );
	} else {
		$first = strtok( $name, ' ' );
		$start = strrpos( $name, ' ' ) + 1;
		$last = substr( $name, $start );
		update_post_meta( $player->ID, '_wpcm_firstname', $first );
		update_post_meta( $player->ID, '_wpcm_lastname', $last );
	}

}

$args = array(
	'post_type' => 'wpcm_staff',
	'posts_per_page' => -1,
);
$employees = get_posts( $args );

foreach( $employees as $employee ) {

	$name = get_the_title( $employee );
	$name = trim( $name );
	if( strpos( $name, ' ' ) === false ) {
		update_post_meta( $employee->ID, '_wpcm_firstname', '' );
		update_post_meta( $employee->ID, '_wpcm_lastname', $name );
	} else {
		$first = strtok( $name, ' ' );
		$start = strrpos( $name, ' ' ) + 1;
		$last = substr( $name, $start );
		update_post_meta( $employee->ID, '_wpcm_firstname', $first );
		update_post_meta( $employee->ID, '_wpcm_lastname', $last );
	}

}

add_option( 'wpcm_match_time', '15:00');
add_option( 'wpcm_match_duration', '90');
add_option( 'wpcm_match_box_scores', 'no');
add_option( 'wpcm_name_format', 'full');
add_option( 'wpcm_map_zoom', '15');
add_option( 'wpcm_map_type', 'roadmap');
add_option( 'wpcm_club_settings_formed', 'yes');
add_option( 'wpcm_club_settings_colors', 'yes');
add_option( 'wpcm_club_settings_honors', 'yes');
add_option( 'wpcm_club_settings_website', 'yes');
add_option( 'wpcm_club_settings_venue', 'yes');
add_option( 'wpcm_club_settings_h2h', 'yes');
add_option( 'wpcm_club_settings_matches', 'yes');
add_option( 'wpcm_standings_order', 'DESC');
add_option( 'wpcm_standings_orderby', 'pts');
add_option( 'wpcm_standings_priority_order', 'DESC');
add_option( 'wpcm_standings_orderby_2', 'gd');
add_option( 'wpcm_standings_priority_order_2', 'DESC');
add_option( 'wpcm_standings_orderby_3', 'f');
add_option( 'wpcm_standings_priority_order_3', 'DESC');
add_option( 'wpcm_club_settings_colors', 'yes');

delete_option( '_wpcm_needs_welcome' );
delete_option( 'wpcm_primary_result' );
