<?php
/**
 * WPClubManager Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types)
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Admin_Meta_Boxes
 */
class WPCM_Admin_Meta_Boxes {

	/**
	 * @var array
	 */
	private static $meta_box_errors = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Club Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_club_meta', 'WPCM_Meta_Box_Club_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_club_meta', 'WPCM_Meta_Box_Club_Table::save', 10, 2 );
		// add_action( 'wpclubmanager_process_wpcm_club_meta', 'WPCM_Meta_Box_Club_Parent::save', 10, 2 );

		// Save Match Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Fixture::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Players::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Result::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_match_meta', 'WPCM_Meta_Box_Match_Video::save', 10, 2 );

		// Save Player Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Display::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Stats::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Users::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_player_meta', 'WPCM_Meta_Box_Player_Roster::save', 10, 2 );

		// Save Sponsor Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_sponsor_meta', 'WPCM_Meta_Box_Sponsor_Url::save', 10, 2 );

		// Save Staff Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_staff_meta', 'WPCM_Meta_Box_Staff_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_staff_meta', 'WPCM_Meta_Box_Staff_Roster::save', 10, 2 );

		// Save League Table Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_table_meta', 'WPCM_Meta_Box_Table_Stats::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_table_meta', 'WPCM_Meta_Box_Table_Notes::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_table_meta', 'WPCM_Meta_Box_Table_Details::save', 10, 2 );

		// Save League Table Meta Boxes
		add_action( 'wpclubmanager_process_wpcm_roster_meta', 'WPCM_Meta_Box_Roster_Details::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_roster_meta', 'WPCM_Meta_Box_Roster_Players::save', 10, 2 );
		add_action( 'wpclubmanager_process_wpcm_roster_meta', 'WPCM_Meta_Box_Roster_Staff::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message
	 *
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
	 * Venue taxonomy meta box callback
	 *
	 * @param WP_Post $post
	 * @param array   $box
	 */
	public function venue_meta_box_cb( $post, $box ) {

		$box['args']['taxonomy'] = 'wpcm_venue';

		post_categories_meta_box( $post, $box );
	}

	/**
	 * Add WPCM Meta boxes
	 */
	public function add_meta_boxes() {

		global $post;

		add_meta_box( 'wpclubmanager-club-parent', __( 'Linked Clubs', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Parent::output', 'wpcm_club', 'normal', 'high' );
		add_meta_box( 'wpclubmanager-club-details', __( 'Club Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Details::output', 'wpcm_club', 'normal', 'high' );
		add_meta_box( 'wpclubmanager-club-info', __( 'Club Information', 'wp-club-manager' ), function ( $post ) {
			wp_editor( $post->post_content, 'post_content', array(
				'name'          => 'post_content',
				'textarea_rows' => 10,
				'tinymce'       => array( 'resize' => false ),
			) );
		}, 'wpcm_club', 'normal', 'high' );
		if ( is_league_mode() && 'publish' === $post->post_status ) {
			add_meta_box( 'wpclubmanager-club-players', __( 'Players', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Players::output', 'wpcm_club', 'normal', 'high' );
			add_meta_box( 'wpclubmanager-club-staff', __( 'Staff', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Staff::output', 'wpcm_club', 'normal', 'high' );
		}
		add_meta_box( 'postimagediv', __( 'Club Badge', 'wp-club-manager' ), 'post_thumbnail_meta_box', 'wpcm_club', 'side' );
		add_meta_box( 'wpcm_venuediv', __( 'Home Venue', 'wp-club-manager' ), array( $this, 'venue_meta_box_cb' ), 'wpcm_club', 'side' );
		add_meta_box( 'wpclubmanager-club-table', __( 'Add to League Table', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Table::output', 'wpcm_club', 'side' );

		add_meta_box( 'wpclubmanager-match-fixture', __( 'Match Fixture', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Fixture::output', 'wpcm_match', 'normal', 'high' );
		add_meta_box( 'wpclubmanager-match-details', __( 'Match Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Details::output', 'wpcm_match', 'normal', 'high' );
		if ( get_option( 'wpcm_match_show_report', 'yes' ) == 'yes' ) {
			add_meta_box( 'wpclubmanager-match-report', __( 'Match Report', 'wp-club-manager' ), function ( $post ) {
				wp_editor( $post->post_content, 'post_content', array(
					'name'          => 'post_content',
					'textarea_rows' => 20,
				) );
			}, 'wpcm_match', 'normal', 'high' );
		}
		if ( get_option( 'wpcm_match_show_preview', 'no' ) == 'yes' ) {
			add_meta_box( 'postexcerpt', __( 'Match Preview', 'wp-club-manager' ), function ( $post ) {
				wp_editor( $post->post_excerpt, 'excerpt', array(
					'name'       => 'excerpt',
					'quicktags'  => array( 'buttons' => 'em,strong,link' ),
					'tinymce'    => array(
						'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
						'theme_advanced_buttons2' => '',
					),
					'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
				) );
			}, 'wpcm_match', 'normal', 'low' );
		}
		if ( '' !== get_post_meta( $post->ID, 'wpcm_home_club', true ) ) {
			add_meta_box( 'wpclubmanager-match-players', __( 'Select Players', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Players::output', 'wpcm_match', 'normal', 'low' );
		}
		add_meta_box( 'wpclubmanager-match-result', __( 'Match Result', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Result::output', 'wpcm_match', 'side' );
		add_meta_box( 'wpclubmanager-match-video', __( 'Match Video', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Video::output', 'wpcm_match', 'side' );

		add_meta_box( 'wpclubmanager-player-details', __( 'Player Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Details::output', 'wpcm_player', 'normal', 'high' );
		add_meta_box( 'wpclubmanager-player-bio', __( 'Player Biography', 'wp-club-manager' ), function ( $post ) {
			wp_editor( $post->post_content, 'post_content', array(
				'name'          => 'post_content',
				'textarea_rows' => 10,
				'tinymce'       => array( 'resize' => false ),
			) );
		}, 'wpcm_player', 'normal', 'high' );
		if ( 'publish' === $post->post_status ) {
			add_meta_box( 'wpclubmanager-player-stats', __( 'Player Statistics', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Stats::output', 'wpcm_player', 'normal', 'high' );
			add_meta_box( 'wpclubmanager-player-users', __( 'Link Player to User', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Users::output', 'wpcm_player', 'normal', 'high' );
		}
		add_meta_box( 'wpclubmanager-player-display', __( 'Player Stats Display', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Display::output', 'wpcm_player', 'side' );
		add_meta_box( 'postimagediv', __( 'Player Image' ), 'post_thumbnail_meta_box', 'wpcm_player', 'side' );
		if ( is_club_mode() ) {
			add_meta_box( 'wpclubmanager-player-roster', __( 'Add Player to Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Roster::output', 'wpcm_player', 'side' );
		}

		add_meta_box( 'wpclubmanager-staff-details', __( 'Staff Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Staff_Details::output', 'wpcm_staff', 'normal', 'high' );
		add_meta_box( 'wpclubmanager-staff-bio', __( 'Staff Biography', 'wp-club-manager' ), function ( $post ) {
			wp_editor( $post->post_content, 'post_content', array(
				'name'          => 'post_content',
				'textarea_rows' => 10,
				'tinymce'       => array( 'resize' => false ),
			) );
		}, 'wpcm_staff', 'normal', 'high' );
		add_meta_box( 'postimagediv', __( 'Staff Image' ), 'post_thumbnail_meta_box', 'wpcm_staff', 'side' );
		if ( is_club_mode() ) {
			add_meta_box( 'wpclubmanager-staff-roster', __( 'Add to Staff Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Staff_Roster::output', 'wpcm_staff', 'side' );
		}

		if ( 'publish' === $post->post_status ) {
			add_meta_box( 'wpclubmanager-table-stats', __( 'Manage League Table', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Stats::output', 'wpcm_table', 'normal', 'high' );
			add_meta_box( 'wpclubmanager-table-notes', __( 'Notes', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Notes::output', 'wpcm_table', 'normal', 'low' );
			add_meta_box( 'wpclubmanager-table-details', __( 'League Table Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Details::output', 'wpcm_table', 'side' );
		} else {
			add_meta_box( 'wpclubmanager-table-details', __( 'League Table Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Details::output', 'wpcm_table', 'normal', 'low' );
		}

		if ( 'publish' === $post->post_status ) {
			add_meta_box( 'wpclubmanager-roster-players', __( 'Manage Players Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Players::output', 'wpcm_roster', 'normal', 'high' );
			add_meta_box( 'wpclubmanager-roster-staff', __( 'Manage Staff Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Staff::output', 'wpcm_roster', 'normal', 'high' );
			add_meta_box( 'wpclubmanager-roster-details', __( 'Roster Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Details::output', 'wpcm_roster', 'side' );
		} else {
			add_meta_box( 'wpclubmanager-roster-details', __( 'Roster Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Details::output', 'wpcm_roster', 'normal', 'low' );
		}

		add_meta_box( 'wpclubmanager-sponsor-link', __( 'Sponsor Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Sponsor_Url::output', 'wpcm_sponsor', 'normal', 'high' );
		add_meta_box( 'postimagediv', __( 'Sponsor Logo' ), 'post_thumbnail_meta_box', 'wpcm_sponsor', 'side' );
	}

	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {

		remove_meta_box( 'postexcerpt', 'wpcm_match', 'normal' );
		remove_meta_box( 'wpcm_compdiv', 'wpcm_match', 'side' );
		remove_meta_box( 'wpcm_venuediv', 'wpcm_match', 'side' );
		remove_meta_box( 'wpcm_seasondiv', 'wpcm_match', 'side' );

		remove_meta_box( 'wpcm_positiondiv', 'wpcm_player', 'side' );
		remove_meta_box( 'wpcm_seasondiv', 'wpcm_player', 'side' );
		remove_meta_box( 'wpcm_teamdiv', 'wpcm_player', 'side' );

		remove_meta_box( 'wpcm_jobsdiv', 'wpcm_staff', 'side' );

		remove_meta_box( 'wpcm_venuediv', 'wpcm_club', 'side' );
		remove_meta_box( 'pageparentdiv', 'wpcm_club', 'side' );

		remove_meta_box( 'wpcm_seasondiv', 'wpcm_roster', 'side' );
		remove_meta_box( 'wpcm_teamdiv', 'wpcm_roster', 'side' );

		remove_meta_box( 'wpcm_seasondiv', 'wpcm_table', 'side' );
		remove_meta_box( 'wpcm_teamdiv', 'wpcm_table', 'side' );
		remove_meta_box( 'wpcm_compdiv', 'wpcm_table', 'side' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int    $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {

		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		$nonce = filter_input( INPUT_POST, 'wpclubmanager_meta_nonce', FILTER_UNSAFE_RAW );
		if ( empty( $nonce ) || ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wpclubmanager_save_data' ) ) {
			return;
		}
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! in_array( $post->post_type, array(
			'wpcm_club',
			'wpcm_player',
			'wpcm_match',
			'wpcm_staff',
			'wpcm_sponsor',
			'wpcm_table',
			'wpcm_roster',
		) ) ) {
			return;
		}

		do_action( 'wpclubmanager_process_' . $post->post_type . '_meta', $post_id, $post );
	}
}

new WPCM_Admin_Meta_Boxes();
