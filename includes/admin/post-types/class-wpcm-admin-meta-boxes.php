<?php
/**
 * WPClubManager Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types)
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Admin_Meta_Boxes {

	private static $meta_box_errors = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Club Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_club_meta', 'WPCM_Meta_Box_Club_Stats::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_club_meta', 'WPCM_Meta_Box_Club_Details::save', 10, 2 );

		// Save Match Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Fixture::save', 20, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Players::save', 30, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Result::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Video::save', 10, 2 );

		// Save Player Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Stats::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Users::save', 10, 2 );

		// Save Sponsor Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_sponsor_meta', 'WPCM_Meta_Box_Sponsor_Url::save', 10, 2 );

		// Save Staff Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_staff_meta', 'WPCM_Meta_Box_Staff_Details::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message
	 * @param string $text
	 */
	public static function add_error( $text ) {

		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option
	 */
	public function save_errors() {

		update_option( 'wpclubmanager_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		
		$errors = maybe_unserialize( get_option( 'wpclubmanager_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="wpclubmanager_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'wpclubmanager_meta_box_errors' );
		}
	}

	/**
	 * Add WPCM Meta boxes
	 */
	public function add_meta_boxes() {
		
		add_meta_box( 'wpclubmanager-club-details', __( 'Club Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Details::output', 'wpcm_club', 'normal', 'core' );
		add_meta_box( 'wpclubmanager-club-stats', __( 'Club Statistics', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Stats::output', 'wpcm_club', 'normal', 'low' );
		add_meta_box( 'postimagediv', __('Club Badge'), 'post_thumbnail_meta_box', 'wpcm_club', 'side', 'low' );

		add_meta_box( 'wpclubmanager-match-result', __('Match Result', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Result::output', 'wpcm_match', 'side', 'default');
		add_meta_box( 'wpclubmanager-match-video', __('Match Video', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Video::output', 'wpcm_match', 'side', 'default');
		add_meta_box( 'wpclubmanager-match-fixture', __('Match Fixture', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Fixture::output', 'wpcm_match', 'normal', 'high');
		add_meta_box( 'postexcerpt', __('Match Preview', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Preview::output', 'wpcm_match', 'normal');
		add_meta_box( 'wpclubmanager-match-details', __('Match Details', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Details::output', 'wpcm_match', 'normal', 'high');
		add_meta_box( 'wpclubmanager-match-players', __('Select Players', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Players::output', 'wpcm_match', 'normal', 'low');

		add_meta_box( 'wpclubmanager-player-details', __( 'Player Information', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Details::output', 'wpcm_player', 'normal', 'core' );
		add_meta_box( 'wpclubmanager-player-stats', __( 'Player Statistics', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Stats::output', 'wpcm_player', 'normal', 'low' );
		add_meta_box( 'wpclubmanager-player-users', __( 'Link Player to User', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Users::output', 'wpcm_player', 'normal', 'low' );
		add_meta_box( 'pageparentdiv', __('Player Order'), 'page_attributes_meta_box', 'wpcm_player', 'side', 'low' );
		add_meta_box( 'postimagediv', __('Player Image'), 'post_thumbnail_meta_box', 'wpcm_player', 'side', 'low' );

		add_meta_box( 'wpclubmanager-sponsor-link', __( 'Sponsor Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Sponsor_Url::output', 'wpcm_sponsor', 'normal', 'core' );
		add_meta_box( 'postimagediv', __('Sponsor Logo'), 'post_thumbnail_meta_box', 'wpcm_sponsor', 'side', 'low' );

		add_meta_box( 'wpclubmanager-staff-details', __( 'Staff Information', 'wp-club-manager' ), 'WPCM_Meta_Box_Staff_Details::output', 'wpcm_staff', 'normal', 'core' );
		add_meta_box( 'pageparentdiv', __('Staff Order'), 'page_attributes_meta_box', 'wpcm_staff', 'side', 'low' );
		add_meta_box( 'postimagediv', __('Staff Image'), 'post_thumbnail_meta_box', 'wpcm_staff', 'side', 'low' );
	}

	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {
		
		remove_meta_box( 'postexcerpt', 'wpcm_match', 'normal' );
		remove_meta_box( 'wpcm_compdiv', 'wpcm_match', 'side');
		remove_meta_box( 'wpcm_venuediv', 'wpcm_match', 'side');
		remove_meta_box( 'wpcm_seasondiv', 'wpcm_match', 'side');

		remove_meta_box( 'wpcm_positiondiv', 'wpcm_player', 'side' );

		remove_meta_box( 'wpcm_jobsdiv', 'wpcm_staff', 'side' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {

		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['wpclubmanager_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wpclubmanager_meta_nonce'], 'wpclubmanager_save_data' ) ) {
			return;
		}
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id  )) {
			return;
		}
		if ( $post->post_type != 'wpcm_club' && $post->post_type != 'wpcm_player' && $post->post_type != 'wpcm_match' && $post->post_type != 'wpcm_staff' && $post->post_type != 'wpcm_sponsor' ) {
			return;
		}

		do_action( 'wpclubmanager_process_' . $post->post_type . '_meta', $post_id, $post );
	}

}

new WPCM_Admin_Meta_Boxes();