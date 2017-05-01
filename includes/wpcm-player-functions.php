<?php
/**
 * WPClubManager Player Functions.
 *
 * Functions for players and staff.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.4.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get player labels.
 *
 * @return array
 */
function wpcm_player_labels() {

	$labels = array(
		'number' => __( 'Number', 'wp-club-manager' ),
		'thumb' => __( 'Image', 'wp-club-manager' ),
		'name' => __( 'Name', 'wp-club-manager' ),
		'flag' => __( 'Flag', 'wp-club-manager' ),
		'position' => __( 'Position', 'wp-club-manager' ),
		'age' => __( 'Age', 'wp-club-manager' ),
		'height' => __( 'Height', 'wp-club-manager' ),
		'weight' => __( 'Weight', 'wp-club-manager' ),
		'team' => __( 'Team', 'wp-club-manager' ),
		'season' => __( 'Season', 'wp-club-manager' ),
		'dob' => __( 'Date of Birth', 'wp-club-manager' ),
		'hometown' => __( 'Hometown', 'wp-club-manager' ),
		'joined' => __( 'Joined', 'wp-club-manager' ),
		'subs' => __( 'Sub Appearances', 'wp-club-manager' )
	);

	return apply_filters( 'wpclubmanager_player_labels', $labels );
}

/**
 * Get player labels for table headers.
 *
 * @return array
 */
function wpcm_player_header_labels() {

	$labels = array(
		'number' => '&nbsp;',
		'thumb' => '&nbsp',
		'name' => __( 'Name', 'wp-club-manager' ),
		'flag' => '&nbsp;',
		'position' => __( 'Position', 'wp-club-manager' ),
		'age' => __( 'Age', 'wp-club-manager' ),
		'height' => __( 'Height', 'wp-club-manager' ),
		'weight' => __( 'Weight', 'wp-club-manager' ),
		'team' => __( 'Team', 'wp-club-manager' ),
		'season' => __( 'Season', 'wp-club-manager' ),
		'dob' => __( 'Date of Birth', 'wp-club-manager' ),
		'hometown' => __( 'Hometown', 'wp-club-manager' ),
		'joined' => __( 'Joined', 'wp-club-manager' ),
		'subs' => __( 'Sub Appearances', 'wp-club-manager' )
	);

	return apply_filters( 'wpclubmanager_player_header_labels', $labels );
}

/**
 * Get appearance label.
 *
 * @return array
 */
function wpcm_get_appearance_labels() {

	$appearances = array(
		'appearances' => _x( 'PL', 'Games Played (Appearances)', 'wp-club-manager' )
	);

	return apply_filters( 'wpcm_get_appearance_labels', $appearances );
}

/**
 * Get appearance and subs labels.
 *
 * @return array
 */
function wpcm_get_appearance_and_subs_labels() {

	$apps = wpcm_get_appearance_labels();
	$subs = array(
		'subs' 		  => __( 'SUBS', 'Substitute Appearances', 'wp-club-manager' ),
	);
	$appearances = array_merge( $apps, $subs );

	return apply_filters( 'wpcm_get_appearance_and_subs_labels', $appearances );
}

/**
 * Get appearance name.
 *
 * @return array
 */
function wpcm_get_appearance_names() {

	$appearances = array(
		'appearances' => __( 'Played', 'wp-club-manager' ),
	);

	return apply_filters( 'wpcm_get_appearance_names', $appearances );
}

/**
 * Get appearance and subs names.
 *
 * @return array
 */
function wpcm_get_appearance_and_subs_names() {

	$apps = wpcm_get_appearance_names();
	$subs = array(
		'subs' 		  => __( 'Sub Appearances', 'wp-club-manager' ),
	);
	$appearances = array_merge( $apps, $subs );

	return apply_filters( 'wpcm_get_appearance_and_subs_names', $appearances );
}

/**
 * Get preset player stats labels with appearances and subs.
 *
 * @param bool $subs
 * @return array
 */
function wpcm_get_player_stats_labels( $subs = false) {

	if( $subs ) {
		$appearance_label = wpcm_get_appearance_and_subs_labels();
	} else {
		$appearance_label = wpcm_get_appearance_labels();
	}

	$labels = wpcm_get_preset_labels();
	foreach( $labels as $label => $value ) {
		if( get_option( 'wpcm_show_stats_'. $label ) == 'yes' ) {
			$output[$label] = $value;
		}
	}

	$stats_labels = array_merge( $appearance_label, $output );

	return $stats_labels;
}

/**
 * Get all combined player labels.
 *
 * @return array
 */
function wpcm_get_player_all_labels() {

	$appearance_labels = wpcm_get_appearance_and_subs_labels();

	$labels = wpcm_get_preset_labels();
	foreach( $labels as $label => $value ) {
		if( get_option( 'wpcm_show_stats_'. $label ) == 'yes' ) {
			$output[$label] = $value;
		}
	}

	$stats_labels = array_merge( wpcm_player_labels(), $appearance_labels, $output );

	return $stats_labels;
}

/**
 * Get preset player stats names with appearances and subs.
 *
 * @param bool $subs
 * @return array
 */
function wpcm_get_player_stats_names( $subs = false) {

	if( $subs ) {
		$appearance_label = wpcm_get_appearance_and_subs_names();
	} else {
		$appearance_label = wpcm_get_appearance_names();
	}
	$labels = wpcm_get_preset_labels( 'players', 'name' );
	foreach( $labels as $label => $value ) {
		if( get_option( 'wpcm_show_stats_'. $label ) == 'yes' ) {
			$output[$label] = $value;
		}
	}

	$stats_labels = array_merge( $appearance_label, $output );

	return $stats_labels;
}

/**
 * Get all combined player names.
 *
 * @return array
 */
function wpcm_get_player_all_names() {

	$appearance_labels = wpcm_get_appearance_and_subs_names();

	$labels = wpcm_get_preset_labels( 'players', 'name' );
	foreach( $labels as $label => $value ) {
		if( get_option( 'wpcm_show_stats_'. $label ) == 'yes' ) {
			$output[$label] = $value;
		}
	}

	$stats_labels = array_merge( wpcm_player_labels(), $appearance_labels, $output );

	return $stats_labels;
}

/**
 * Filter keys to display.
 *
 * @return array
 */
function wpcm_exclude_keys() {

	$exclude_keys = array();
	$exclude_keys[] = 'checked';
	$exclude_keys[] = 'sub';
	$exclude_keys[] = 'greencards';
	$exclude_keys[] = 'yellowcards';
	$exclude_keys[] = 'blackcards';
	$exclude_keys[] = 'redcards';
	$exclude_keys[] = 'mvp';

	return $exclude_keys;
}

/**
 * Filter keys to display.
 *
 * @return array
 */
function wpcm_stats_cards() {

	$exclude_keys = apply_filters( 'wpclubmanager_stats_cards', array(
		'greencards',
		'yellowcards',
		'blackcards',
		'redcards',
	) );

	return $exclude_keys;
}

/**
 * Get player positions.
 *
 * @access public
 * @param int $post
 * @return mixed $position
 * @since 1.4.0
 */
function wpcm_get_player_positions( $post ) {

	$positions = wp_get_object_terms( $post, 'wpcm_position' );

	if ( is_array( $positions ) ) {
		$player_positions = array();
		foreach ( $positions as $position ) {
			$player_positions[] = $position->name;
		}
		$position = implode( ', ', $player_positions );
	} else {
		$position = __( 'None', 'wp-club-manager' );
	}

	return $position;
}

/**
 * Get player teams.
 *
 * @access public
 * @param int $post
 * @return mixed $team
 * @since 1.4.0
 */
function wpcm_get_player_teams( $post ) {

	$teams = wp_get_object_terms( $post, 'wpcm_team' );

	if ( is_array( $teams ) ) {
		$player_teams = array();
		foreach ( $teams as $team ) {
			$player_teams[] = $team->name;
		}
		$team = implode( ', ', $player_teams );
	} else {
		$team = false;
	}

	return $team;

}

/**
 * Get player seasons.
 *
 * @access public
 * @param int $post
 * @return mixed $season
 * @since 1.4.0
 */
function wpcm_get_player_seasons( $post ) {

	$seasons = wp_get_object_terms( $post, 'wpcm_season' );

	if ( is_array( $seasons ) ) {
		$player_seasons = array();
		foreach ( $seasons as $season ) {
			$player_seasons[] = $season->name;
		}
		$season = implode( ', ', $player_seasons );
	}

	return $season;
}

/**
 * Get player thumbnail.
 *
 * @access public
 * @param int $post
 * @return mixed $thumb
 * @since 1.4.0
 */
function wpcm_get_player_thumbnail( $post, $size = null, $args = null ) {

	if ( has_post_thumbnail( $post ) ) {
		$thumb = get_the_post_thumbnail( $post, $size, $args );
	} else {
		$thumb = wpcm_placeholder_img( $size );
	}

	return $thumb;
}

/**
 * Get average player rating - used in templates/shortcodes/players.php.
 *
 * @access public
 * @param int $rating
 * @param int $appearances
 * @return int $average
 * @since 1.4.0
 */
function wpcm_get_player_average_rating( $rating, $appearances ) {

	if ( $rating > 0 ) {
		$avrating = wpcm_divide( $rating, $appearances );
		$average = sprintf( "%01.2f", round($avrating, 2) );
	} else {
		$average = '0';
	}

	return $average;

}

/**
 * Get player appearances with/without subs - used in templates/shortcodes/players.php.
 *
 * @access public
 * @param array $player_detail
 * @return int $appearances
 * @since 1.4.5
 */
function wpcm_get_player_appearances( $player_detail ) {

	if ( array_key_exists( 'subs', $player_detail ) ) {
		$subs = $player_detail['subs'];
		if( $subs >= 1 ){
			$appearances = $player_detail['appearances'] . ' <span class="wpcm-sub-appearances">(' . $subs . ')</span>';
		} else {
			$appearances = $player_detail['appearances'];
		}
	} else {
		$appearances = $player_detail['appearances'];
	}

	return $appearances;
}

/**
 * Get player stat - used in templates/shortcodes/players.php.
 *
 * @access public
 * @param array $player_detail
 * @param string $stat
 * @return string $stat
 * @since 1.4.0
 */
function wpcm_get_player_stat( $player_detail, $stat ) {

	if ( $stat == 'rating' ) {
		$stat = wpcm_get_player_average_rating( $player_detail['rating'], $player_detail['appearances'] );
	} elseif ( $stat == 'appearances' ) {
		$stat = wpcm_get_player_appearances( $player_detail );
	} else {
		$stat = $player_detail[$stat];
	}

	return $stat;
}

/**
 * Get player stat value from presets - used in templates/shortcodes/players.php
 *
 * @access public
 * @param array $player_details
 * @param int $post
 * @param array $player_stats
 * @param string $stat
 * @param int $team
 * @param int $season
 * @return array $player_details
 * @since 1.4.0
 */
function wpcm_get_player_preset_stat( $player_details = array(), $post, $player_stats = array(), $stat, $team = 0, $season = 0 ) {

	if ( $team ) {
		if ( $season ) {
			$player_details[$post][$stat] = $player_stats[$team][$season]['total'][$stat];
		} else {
			$player_details[$post][$stat] = $player_stats[$team][0]['total'][$stat];
		}
	} else {
		if ( $season ) {
			$player_details[$post][$stat] = $player_stats[0][$season]['total'][$stat];
		} else {
			$player_details[$post][$stat] = $player_stats[0][0]['total'][$stat];
		}
	}

	return $player_details[$post][$stat];
}

/**
 * Get staff labels.
 *
 * @return array
 */
function wpcm_staff_labels() {

	$labels = array(
		'flag' => '&nbsp;',
		'number' => '&nbsp;',
		'name' => __( 'Name', 'wp-club-manager' ),
		'thumb' => '&nbsp',
		'job' => __( 'Job', 'wp-club-manager' ),
		'email' => __( 'Email', 'wp-club-manager' ),
		'phone' => __( 'Phone', 'wp-club-manager' ),
		'age' => __( 'Age', 'wp-club-manager' ),
		'team' => __( 'Team', 'wp-club-manager' ),
		'season' => __( 'Season', 'wp-club-manager' ),
		'joined' => __( 'Joined', 'wp-club-manager' )
	);

	return $labels;
}

/**
 * Get staff jobs.
 *
 * @access public
 * @param int $post
 * @return mixed $job
 * @since 1.4.5
 */
function wpcm_get_staff_jobs( $post ) {

	$jobs = wp_get_object_terms( $post, 'wpcm_jobs' );

	if ( is_array( $jobs ) ) {
		$employee_jobs = array();
		foreach ( $jobs as $job ) {
			$employee_jobs[] = $job->name;
		}
		$job = implode( ', ', $employee_jobs );
	} else {
		$job = __( 'None', 'wp-club-manager' );
	}

	return $job;
}