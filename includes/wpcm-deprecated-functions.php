<?php
/**
 * Deprecated functions
 *
 * Where functions come to retire.
 *
 * @author      Clubpress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @param mixed $code
 *
 * @return void
 */
function wpcm_enqueue_js( $code ) {
	_deprecated_function( 'wpcm_enqueue_js', '1.0.3', '' );
}

/**
 * @return void
 */
function wpcm_print_js() {
	_deprecated_function( 'wpcm_print_js', '1.0.3', '' );
}

/**
 * @return void
 */
function check_jquery() {
	_deprecated_function( 'check_jquery', '1.1.0', '' );
}

/**
 * @return void
 */
function is_sponsors() {
	_deprecated_function( 'is_sponsors', '1.1.1', '' );
}

/**
 * @param string $key
 * @param mixed  $value
 * @param int    $count
 *
 * @return null
 */
function wpcm_match_player_row( $key, $value, $count = 0 ) {
	_deprecated_function( 'wpcm_match_player_row', '1.4.0', '' );
	return wpclubmanager_get_template( 'single-match/lineup-row.php', array(
		'key'   => $key,
		'value' => $value,
		'count' => $count,
	) );
}

/**
 * @param array $stats
 * @param int   $team
 * @param int   $season
 *
 * @return null
 */
function wpcm_profile_stats_table( $stats = array(), $team = 0, $season = 0 ) {
	_deprecated_function( 'wpcm_profile_stats_table', '1.4.0', '' );
	return wpclubmanager_get_template( 'single-player/stats-table.php', array(
		'stats'  => $stats,
		'team'   => $team,
		'season' => $season,
	) );
}

/**
 * @param WP_Post|int|null $post
 *
 * @return array
 */
function get_wpcm_player_stats_from_post( $post = null ) {
	_deprecated_function( 'get_wpcm_player_stats_from_post', '1.4.0', '' );
	return get_wpcm_player_stats( $post );
}
