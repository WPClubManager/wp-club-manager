<?php
/**
 * WPClubManager Hooks
 *
 * Action/filter hooks used for WPClubManager functions/templates
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Templates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter( 'body_class', 'wpcm_body_class' );
add_filter( 'post_class', 'wpcm_post_class', 20, 3 );
add_action( 'wp_head', 'wpcm_generator_tag' );

/** Template Hooks ********************************************************/

if ( ! is_admin() || defined('DOING_AJAX') ) {

	/**
	 * Content Wrappers
	 *
	 * @see wpclubmanager_output_content_wrapper()
	 * @see wpclubmanager_output_content_wrapper_end()
	 */
	add_action( 'wpclubmanager_before_main_content', 'wpclubmanager_output_content_wrapper', 10 );
	add_action( 'wpclubmanager_after_main_content', 'wpclubmanager_output_content_wrapper_end', 10 );

	/**
	 * Sidebar
	 *
	 * @see wpclubmanager_get_sidebar()
	 */
	add_action( 'wpclubmanager_sidebar', 'wpclubmanager_get_sidebar', 10 );


	/**
	 * Player Image Box
	 *
	 * @see wpclubmanager_template_single_player_images()
	 */
	add_action( 'wpclubmanager_single_player_image', 'wpclubmanager_template_single_player_images', 5 );

	/**
	 * Player Info Box
	 *
	 * @see wpclubmanager_template_single_player_title()
	 * @see wpclubmanager_template_single_player_meta()
	 */
	add_action( 'wpclubmanager_single_player_info', 'wpclubmanager_template_single_player_title', 5 );
	add_action( 'wpclubmanager_single_player_info', 'wpclubmanager_template_single_player_meta', 10 );

	/**
	 * Player Stats Table
	 *
	 * @see wpclubmanager_template_single_player_stats()
	 */
	add_action( 'wpclubmanager_single_player_stats', 'wpclubmanager_template_single_player_stats', 5 );

	/**
	 * Player Bio Div
	 *
	 * @see wpclubmanager_template_single_player_bio()
	 */
	add_action( 'wpclubmanager_single_player_bio', 'wpclubmanager_template_single_player_bio', 5 );

	/**
	 * After Single Players Bio Div
	 *
	 * @see wpclubmanager_template_single_player_dropdown()
	 */
	add_action( 'wpclubmanager_after_single_player', 'wpclubmanager_template_single_player_dropdown', 5 );

	/**
	 * After Single Staff Bio Div
	 *
	 * @see wpclubmanager_template_single_staff_dropdown()
	 */
	add_action( 'wpclubmanager_after_single_staff', 'wpclubmanager_template_single_staff_dropdown', 5 );


	/**
	 * Match Info Box
	 *
	 * @see wpclubmanager_template_single_match_home_club_badge()
	 * @see wpclubmanager_template_single_match_away_club_badge()
	 * @see wpclubmanager_template_single_match_comp()
	 * @see wpclubmanager_template_single_match_date()
	 */
	add_action( 'wpclubmanager_single_match_info', 'wpclubmanager_template_single_match_home_club_badge', 5 );
	add_action( 'wpclubmanager_single_match_info', 'wpclubmanager_template_single_match_away_club_badge', 10 );
	add_action( 'wpclubmanager_single_match_info', 'wpclubmanager_template_single_match_comp', 20 );
	add_action( 'wpclubmanager_single_match_info', 'wpclubmanager_template_single_match_date', 30 );

	/**
	 * Match Fixture Box
	 *
	 * @see wpclubmanager_template_single_match_home_club()
	 * @see wpclubmanager_template_single_match_score()
	 * @see wpclubmanager_template_single_match_away_club()
	 * @see wpclubmanager_template_single_match_status()
	 */
	add_action( 'wpclubmanager_single_match_fixture', 'wpclubmanager_template_single_match_home_club', 5 );
	add_action( 'wpclubmanager_single_match_fixture', 'wpclubmanager_template_single_match_score', 10 );
	add_action( 'wpclubmanager_single_match_fixture', 'wpclubmanager_template_single_match_away_club', 20 );
	add_action( 'wpclubmanager_single_match_fixture', 'wpclubmanager_template_single_match_status', 25 );
	add_action( 'wpclubmanager_single_match_fixture', 'wpclubmanager_template_single_match_box_scores', 30 );

	/**
	 * Match Meta Box
	 *
	 * @see wpclubmanager_template_single_match_team()
	 * @see wpclubmanager_template_single_match_referee()
	 */
	add_action( 'wpclubmanager_single_match_meta', 'wpclubmanager_template_single_match_team', 5 );
	add_action( 'wpclubmanager_single_match_meta', 'wpclubmanager_template_single_match_referee', 10 );

	/**
	 * Match Venue Box
	 *
	 * @see wpclubmanager_template_single_match_venue()
	 * @see wpclubmanager_template_single_match_attendance()
	 */
	add_action( 'wpclubmanager_single_match_venue', 'wpclubmanager_template_single_match_venue', 5 );
	add_action( 'wpclubmanager_single_match_venue', 'wpclubmanager_template_single_match_attendance', 10 );

	/**
	 * Match Details Box
	 *
	 * @see wpclubmanager_template_single_match_lineup()
	 * @see wpclubmanager_template_single_match_venue_info()
	 * @see wpclubmanager_template_single_match_video()
	 */
	add_action( 'wpclubmanager_single_match_details', 'wpclubmanager_template_single_match_lineup', 5 );
	add_action( 'wpclubmanager_single_match_details', 'wpclubmanager_template_single_match_venue_info', 10 );

	/**
	 * Match Report Box
	 *
	 * @see wpclubmanager_template_single_match_report()
	 */
	add_action( 'wpclubmanager_single_match_report', 'wpclubmanager_template_single_match_report', 5 );
	add_action( 'wpclubmanager_single_match_report', 'wpclubmanager_template_single_match_video', 10 );

}