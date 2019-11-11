<?php
/**
 * WPClubManager Template Functions
 *
 * Functions used in the template files to output content - in most cases hooked in via the template actions.
 * All functions are pluggable.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 * @return void
 */
function wpcm_generator_tag() {
	echo "\n\n" . '<!-- WP Club Manager Version -->' . "\n" . '<meta name="generator" content="WP Club Manager ' . esc_attr( WPCM_VERSION ) . '" />' . "\n\n";
}

/**
 * Add body classes for WPCM pages
 *
 * @param  array $classes
 * @return array
 */
function wpcm_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_wpclubmanager() ) {
		$classes[] = 'wpclubmanager';
	}
	if ( is_club() ) {
		$classes[] = 'club';
	}
	if ( is_player() ) {
		$classes[] = 'player';
	}
	if ( is_staff() ) {
		$classes[] = 'staff';
	}
	if ( is_match() ) {
		$classes[] = 'match';
	}
	if ( is_sponsor() ) {
		$classes[] = 'sponsor';
	}

	return array_unique( $classes );
}

/**
 * Adds extra post classes
 *
 * @since 1.0.0
 * @param array $classes
 * @return array
 */
function wpcm_post_class( $classes ) {

	if ( is_sponsor() ) {
		$classes[] = 'wpcm-single-sponsors';
	}
	if ( is_club() ) {
		$classes[] = 'wpcm-single-club';
	}
	if ( is_player() ) {
		$classes[] = 'wpcm-single-player';
	}
	if ( is_staff() ) {
		$classes[] = 'wpcm-single-staff';
	}
	if ( is_match() ) {
		$classes[] = 'wpcm-single-match';
	}

	return $classes;
}


/** Global ****************************************************************/

if ( ! function_exists( 'wpclubmanager_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function wpclubmanager_output_content_wrapper() {
		wpclubmanager_get_template( 'layout/wrapper-start.php' );
	}
}
if ( ! function_exists( 'wpclubmanager_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function wpclubmanager_output_content_wrapper_end() {
		wpclubmanager_get_template( 'layout/wrapper-end.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_get_sidebar' ) ) {

	/**
	 * Get the content sidebar template.
	 *
	 * @access public
	 * @return void
	 */
	function wpclubmanager_get_sidebar() {
		wpclubmanager_get_template( 'layout/sidebar.php' );
	}
}


/** Single Player ********************************************************/

if ( ! function_exists( 'wpclubmanager_template_single_player_images' ) ) {

	/**
	 * Output the player image before the single player summary.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_images() {
		wpclubmanager_get_template( 'single-player/player-image.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_player_title' ) ) {

	/**
	 * Output the player title.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_title() {
		wpclubmanager_get_template( 'single-player/title.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_player_meta' ) ) {

	/**
	 * Output the player meta.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_meta() {
		wpclubmanager_get_template( 'single-player/meta.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_player_stats' ) ) {

	/**
	 * Output the player stats.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_stats() {
		wpclubmanager_get_template( 'single-player/stats.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_player_bio' ) ) {

	/**
	 * Output the player bio.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_bio() {
		wpclubmanager_get_template( 'single-player/bio.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_player_dropdown' ) ) {

	/**
	 * Output the player dropdown.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_player_dropdown() {

		global $post;
		
		$teams = wp_get_object_terms( $post->ID, 'wpcm_team' );

		if ( is_array( $teams ) ) {
							
			$player_teams = array();

			foreach ( $teams as $team ) {
				
				$player_teams[] = $team->term_id;
			}
		}
		
		$args = array(
			'post_type' => 'wpcm_player',
			'tax_query' => array(),
			'numberposts' => -1,
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_key' => 'wpcm_number'
		);

		if ( is_array( $teams ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'field' => 'term_id',
				'terms' => $player_teams
			);
		}

		$player_posts = get_posts($args);
		$players = array();

		foreach($player_posts as $player_post):
					
			$custom = get_post_custom($player_post->ID);
					
			$players[get_permalink($player_post->ID)] = ($custom['wpcm_number'][0] == null ? '' : $custom['wpcm_number'][0].'. ').get_the_title($player_post->ID);
		endforeach;
									
		$custom = get_post_custom();
				
		if($custom['wpcm_number'][0] == null) {
			$number = '-';
			$name = get_the_title($post->ID);
		} else {
			$number = $custom['wpcm_number'][0];
			$name = $number.'. '.get_the_title($post->ID);
		}

		echo wpcm_form_dropdown('switch-player-profile', $players, get_permalink(), array('onchange' => 'window.location = this.value;'));
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_staff_dropdown' ) ) {

	/**
	 * Output the staff dropdown.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_staff_dropdown() {

		global $post;

		$teams = wp_get_object_terms( $post->ID, 'wpcm_team' );

		if ( is_array( $teams ) ) {
							
			$staff_teams = array();

			foreach ( $teams as $team ) {
				
				$staff_teams[] = $team->term_id;
			}
		}
		
		$args = array(
			'post_type' => 'wpcm_player',
			'tax_query' => array(),
			'numberposts' => -1,
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_key' => 'wpcm_number'
		);

		if ( is_array( $teams ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'field' => 'term_id',
				'terms' => $staff_teams
			);
		}

		$player_posts = get_posts($args);
		$players = array();

		foreach($player_posts as $player_post):
					
			$custom = get_post_custom($player_post->ID);
					
			$players[get_permalink($player_post->ID)] = get_the_title($player_post->ID);
		endforeach;

		echo wpcm_form_dropdown('switch-player-profile', $players, get_permalink(), array('onchange' => 'window.location = this.value;'));
	}
}

/** Single Match ********************************************************/

if ( ! function_exists( 'wpclubmanager_template_single_match_home_club_badge' ) ) {

	/**
	 * Output the match home club badge.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_home_club_badge() {
		wpclubmanager_get_template( 'single-match/home-badge.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_date' ) ) {

	/**
	 * Output the match date.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_date() {
		wpclubmanager_get_template( 'single-match/date.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_comp' ) ) {

	/**
	 * Output the match comp.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_comp() {
		wpclubmanager_get_template( 'single-match/comp.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_away_club_badge' ) ) {

	/**
	 * Output the match away club manager.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_away_club_badge() {
		wpclubmanager_get_template( 'single-match/away-badge.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_venue' ) ) {

	/**
	 * Output the match venue.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_venue() {
		wpclubmanager_get_template( 'single-match/venue.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_home_club' ) ) {

	/**
	 * Output the match home club.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_home_club() {
		wpclubmanager_get_template( 'single-match/home-club.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_score' ) ) {

	/**
	 * Output the match score.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_score() {
		wpclubmanager_get_template( 'single-match/score.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_away_club' ) ) {

	/**
	 * Output the match away club.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_away_club() {
		wpclubmanager_get_template( 'single-match/away-club.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_status' ) ) {

	/**
	 * Output the match away club.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_status() {

		$sport = get_option( 'wpcm_sport' );

		if( $sport == 'soccer' ) {
			wpclubmanager_get_template( 'single-match/status.php' );
		}
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_box_scores' ) ) {

	/**
	 * Output the match box_scores.
	 *
	 * @access public
	 * @subpackage	Match
	 * @return void
	 */
	function wpclubmanager_template_single_match_box_scores() {
		wpclubmanager_get_template( 'single-match/box-scores.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_referee' ) ) {

	/**
	 * Output the match referee.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_referee() {
		wpclubmanager_get_template( 'single-match/referee.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_attendance' ) ) {

	/**
	 * Output the match attendance.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_attendance() {
		wpclubmanager_get_template( 'single-match/attendance.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_team' ) ) {

	/**
	 * Output the match team.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_team() {
		wpclubmanager_get_template( 'single-match/team.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_lineup' ) ) {

	/**
	 * Output the match lineup.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_lineup() {
		wpclubmanager_get_template( 'single-match/lineup.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_venue_info' ) ) {

	/**
	 * Output the match venue info.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_venue_info() {
		wpclubmanager_get_template( 'single-match/venue-info.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_report' ) ) {

	/**
	 * Output the match report.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_report() {
		wpclubmanager_get_template( 'single-match/report.php' );
	}
}

if ( ! function_exists( 'wpclubmanager_template_single_match_video' ) ) {

	/**
	 * Output the match venue info.
	 *
	 * @access public
	 * @subpackage	Player
	 * @return void
	 */
	function wpclubmanager_template_single_match_video() {
		wpclubmanager_get_template( 'single-match/video.php' );
	}
}