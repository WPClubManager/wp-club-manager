<?php
/**
 * Admin functions for the match post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_CPT' ) ) {
	include( 'class-wc-admin-cpt.php' );
}

if ( ! class_exists( 'WPCM_Admin_CPT_Match' ) ) :

class WPCM_Admin_CPT_Match extends WPCM_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		$this->type = 'wpcm_match';

		add_filter( 'the_posts', array( $this, 'show_scheduled_matches' ) );

		// Publish text
		add_filter( 'gettext', array( $this, 'text_replace' ) );

		// add_filter( 'the_title', array( $this, 'match_title' ), 10, 2 );
		// add_filter( 'wp_title', array( $this, 'match_wp_title' ), 10, 2 );

		add_filter( 'manage_edit-wpcm_match_columns', array( $this, 'custom_edit_columns' ) );
		add_action( 'manage_wpcm_match_posts_custom_column', array( $this, 'custom_columns' ) );

		add_action( 'restrict_manage_posts', array( $this, 'request_filter_dropdowns' ) );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Check if we're editing or adding a match
	 * @return boolean
	 */
	private function is_editing_product() {
		if ( ! empty( $_GET['post_type'] ) && 'wpcm_match' == $_GET['post_type'] ) {
			return true;
		}
		if ( ! empty( $_GET['post'] ) && 'wpcm_match' == get_post_type( $_GET['post'] ) ) {
			return true;
		}
		if ( ! empty( $_REQUEST['post_id'] ) && 'wpcm_match' == get_post_type( $_REQUEST['post_id'] ) ) {
			return true;
		}
		return false;
	}

	// show future
	public function show_scheduled_matches($posts) {

		global $wp_query, $wpdb;

		if(is_single() && $wp_query->post_count == 0 && isset($wp_query->query_vars['wpcm_match'])) {
			$posts = $wpdb->get_results($wp_query->request);
		}

		return $posts;
	}

	// text replace
	public function text_replace( $string = '' ) {

		if ( 'Scheduled for: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wpclubmanager' );
		} elseif ( 'Published on: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wpclubmanager' );
		} elseif ( 'Publish <b>immediately</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wpclubmanager' );
		}
		return $string;
	}

	// // generate title
	// public function match_title( $title, $id = null ) {

	// 	if ( get_post_type( $id ) == 'wpcm_match' ) {
			
	// 		$default_club = get_option('wpcm_default_club');
	// 		$title_format = get_option('wpcm_match_title_format');
	// 		$home_id = (int)get_post_meta( $id, 'wpcm_home_club', true );
	// 		$away_id = (int)get_post_meta( $id, 'wpcm_away_club', true );
	// 		$home_club = get_post( $home_id );
	// 		$away_club = get_post( $away_id );
	// 		$search = array( '%home%', '%away%' );
	// 		$replace = array( $home_club->post_title, $away_club->post_title );
			
	// 		if ( $away_id == $default_club ) {
	// 			//away
	// 			$title = str_replace( $search, $replace, $title_format );
	// 		} else {
	// 			// home
	// 			$title = str_replace( $search, $replace, $title_format );
	// 		}
	// 	}

	// 	return $title;
	// }

	// // generate title
	// public function match_wp_title( $title, $sep, $seplocation ) {

	// 	global $post;

	// 	if ( get_post_type( ) == 'wpcm_match' ) {

	// 		$title = '';

	// 		if ( $seplocation == 'left' ) {
	// 			$title .= ' ' . $sep . ' ';
	// 		}

	// 		$id = $post->ID;
	// 		$home_id = (int)get_post_meta( $id, 'wpcm_home_club', true );
	// 		$away_id = (int)get_post_meta( $id, 'wpcm_away_club', true );
	// 		$home_club = get_post( $home_id );
	// 		$away_club = get_post( $away_id );
	// 		$title = match_title( $title, $id ) . ' ' . $sep . ' ' . get_the_date();

	// 		if ( $seplocation == 'right' ) {
	// 			$title .= ' ' . $sep . ' ';
	// 		}

	// 		return $title;
	// 	}

	// 	return $title;
	// }

	// // edit columns
	public function custom_edit_columns($columns) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Fixture', 'wpclubmanager' ),
			'comp' => __( 'Competition', 'wpclubmanager' ),
			'season' => __( 'Season', 'wpclubmanager' ),
			'team' => __( 'Team', 'wpclubmanager' ),
			'date' => __( 'Date' ),
			'kickoff' => __( 'Kick-off', 'wpclubmanager' ),
			'results' => __( 'Results', 'wpclubmanager' )
		);

		return $columns;
	}

	// custom columns
	public function custom_columns($column) {

		global $post, $typenow;

		$post_id = $post->ID;

		if ( $typenow == 'wpcm_match' ) {
			switch ($column) {
			case 'comp' :
				the_terms( $post_id, 'wpcm_comp' );
				break;
			case 'season' :
				the_terms( $post_id, 'wpcm_season' );
				break;
			case 'team' :
				the_terms( $post_id, 'wpcm_team' );
				break;
			case 'date' :
				echo get_the_date ( get_option ( 'date_format' ) );
				break;
			case 'kickoff' :
				echo get_the_time ( get_option ( 'time_format' ) );
				break;
			case 'results' :
				$played = get_post_meta( $post_id, 'wpcm_played', true );

				if ( $played ) {
					echo get_post_meta( $post_id, 'wpcm_home_goals', true ) . ' ' . get_option( 'wpcm_match_goals_delimiter' ) . ' ' . get_post_meta( $post_id, 'wpcm_away_goals', true );
				}
				break;
			}
		}
	}

	// taxonomy filter dropdowns
	public function request_filter_dropdowns() {

		global $typenow, $wp_query;

		if ( $typenow == 'wpcm_match' ) {
			// comp dropdown
			$selected = isset( $_REQUEST['wpcm_comp'] ) ? $_REQUEST['wpcm_comp'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wpclubmanager' ), __( 'competitions', 'wpclubmanager' ) ),
				'taxonomy' => 'wpcm_comp',
				'name' => 'wpcm_comp',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// season dropdown
			$selected = isset( $_REQUEST['wpcm_season'] ) ? $_REQUEST['wpcm_season'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wpclubmanager' ), __( 'seasons', 'wpclubmanager' ) ),
				'taxonomy' => 'wpcm_season',
				'name' => 'wpcm_season',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// team dropdown
			$selected = isset( $_REQUEST['wpcm_team'] ) ? $_REQUEST['wpcm_team'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wpclubmanager' ), __( 'teams', 'wpclubmanager' ) ),
				'taxonomy' => 'wpcm_team',
				'name' => 'wpcm_team',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
		}
	}
}

endif;

return new WPCM_Admin_CPT_Match();