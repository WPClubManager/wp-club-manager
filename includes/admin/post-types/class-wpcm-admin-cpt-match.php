<?php
/**
 * Admin functions for the match post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.3
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

		// Title data
		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 99, 2 );

		// Publish text
		add_filter( 'gettext', array( $this, 'text_replace' ) );

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

	// Insert post title data
	public function wp_insert_post_data( $data, $postarr ) {
		if ( $data['post_type'] == 'wpcm_match' && $data['post_title'] == '' ) :

				$default_club = get_default_club();
				$title_format = get_match_title_format();
				$separator = get_option('wpcm_match_clubs_separator');
				$home_id = $_POST['wpcm_home_club'];
				$away_id = $_POST['wpcm_away_club'];
				$home_club = get_post( $home_id );
				$away_club = get_post( $away_id );
				if( $title_format == '%home% vs %away%') {
					$side1 = wpcm_get_team_name( $home_club, $postarr['ID'] );
					$side2 = wpcm_get_team_name( $away_club, $postarr['ID'] );
				}else{
					$side1 = wpcm_get_team_name( $away_club, $postarr['ID'] );
					$side2 = wpcm_get_team_name( $home_club, $postarr['ID'] );
				}

				$title = $side1 . ' ' . $separator . ' ' . $side2;
				$post_name = sanitize_title_with_dashes( $postarr['ID'] . '-' . $title );

				$data['post_title'] = $title;
				$data['post_name'] = $post_name;

		endif;

		return $data;
	}

	// text replace
	public function text_replace( $string = '' ) {

		if ( 'Scheduled for: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wp-club-manager' );
		} elseif ( 'Published on: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wp-club-manager' );
		} elseif ( 'Publish <b>immediately</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Kick-off: <b>%1$s</b>', 'wp-club-manager' );
		}
		return $string;
	}

	// // edit columns
	public function custom_edit_columns($columns) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Fixture', 'wp-club-manager' ),
			'comp' => __( 'Competition', 'wp-club-manager' ),
			'season' => __( 'Season', 'wp-club-manager' ),
			'team' => __( 'Team', 'wp-club-manager' ),
			'date' => __( 'Date' ),
			'kickoff' => __( 'Kick-off', 'wp-club-manager' ),
			'results' => __( 'Results', 'wp-club-manager' )
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
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'competitions', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_comp',
				'name' => 'wpcm_comp',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// season dropdown
			$selected = isset( $_REQUEST['wpcm_season'] ) ? $_REQUEST['wpcm_season'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'seasons', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_season',
				'name' => 'wpcm_season',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// team dropdown
			$selected = isset( $_REQUEST['wpcm_team'] ) ? $_REQUEST['wpcm_team'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'teams', 'wp-club-manager' ) ),
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