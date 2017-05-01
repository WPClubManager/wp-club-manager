<?php
/**
 * Post Types Admin
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Post_Types' ) ) :

class WPCM_Admin_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );

		add_filter( 'bulk_actions-edit-wpcm_match', array( $this, 'wpcm_match_bulk_actions' ) );

		// Bulk / quick edit
		//add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_save_post' ), 10, 2 );

		include_once( 'post-types/class-wpcm-admin-meta-boxes.php' );
	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['wpcm_player'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Player updated. <a href="%s">View Player</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'wp-club-manager' ),
			3 => __( 'Custom field deleted.', 'wp-club-manager' ),
			4 => __( 'Player updated.', 'wp-club-manager' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Player restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Player published. <a href="%s">View Player</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Player saved.', 'wp-club-manager' ),
			8 => sprintf( __( 'Player submitted. <a target="_blank" href="%s">Preview Player</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Player scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Player</a>', 'wp-club-manager' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Player draft updated. <a target="_blank" href="%s">Preview Player</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		$messages['wpcm_staff'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Staff updated. <a href="%s">View Staff</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'wp-club-manager' ),
			3 => __( 'Custom field deleted.', 'wp-club-manager' ),
			4 => __( 'Staff updated.', 'wp-club-manager' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Staff restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Staff published. <a href="%s">View Staff</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Staff saved.', 'wp-club-manager' ),
			8 => sprintf( __( 'Staff submitted. <a target="_blank" href="%s">Preview Staff</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Staff scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Staff</a>', 'wp-club-manager' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Staff draft updated. <a target="_blank" href="%s">Preview Staff</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		$messages['wpcm_match'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Match updated. <a href="%s">View Match</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'wp-club-manager' ),
			3 => __( 'Custom field deleted.', 'wp-club-manager' ),
			4 => __( 'Match updated.', 'wp-club-manager' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Match restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Match published. <a href="%s">View Match</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Match saved.', 'wp-club-manager' ),
			8 => sprintf( __( 'Match submitted. <a target="_blank" href="%s">Preview Match</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Match scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Match</a>', 'wp-club-manager' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Match draft updated. <a target="_blank" href="%s">Preview Match</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		$messages['wpcm_club'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Club updated. <a href="%s">View Club</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'wp-club-manager' ),
			3 => __( 'Custom field deleted.', 'wp-club-manager' ),
			4 => __( 'Club updated.', 'wp-club-manager' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Club restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Club published. <a href="%s">View Club</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Club saved.', 'wp-club-manager' ),
			8 => sprintf( __( 'Club submitted. <a target="_blank" href="%s">Preview Club</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Club scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Club</a>', 'wp-club-manager' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Club draft updated. <a target="_blank" href="%s">Preview Club</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		$messages['wpcm_sponsor'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Sponsor updated.', 'wp-club-manager' ),
			2 => __( 'Custom field updated.', 'wp-club-manager' ),
			3 => __( 'Custom field deleted.', 'wp-club-manager' ),
			4 => __( 'Sponsor updated.', 'wp-club-manager' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Sponsor restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Sponsor published.', 'wp-club-manager' ),
			7 => __( 'Sponsor saved.', 'wp-club-manager' ),
			8 => __( 'Sponsor submitted.', 'wp-club-manager' ),
			9 => sprintf( __( 'Sponsor scheduled for: <strong>%1$s</strong>.', 'wp-club-manager' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Sponsor draft updated.', 'wp-club-manager' ),
		);

		return $messages;
	}

	/**
	 * Conditonally load classes and functions only needed when viewing a post type.
	 */
	public function include_post_type_handlers() {
		
		//include( 'post-types/class-wpcm-admin-meta-boxes.php' );
		include( 'post-types/class-wpcm-admin-cpt-club.php' );
		include( 'post-types/class-wpcm-admin-cpt-match.php' );
		include( 'post-types/class-wpcm-admin-cpt-player.php' );
		include( 'post-types/class-wpcm-admin-cpt-sponsor.php' );
		include( 'post-types/class-wpcm-admin-cpt-staff.php' );
	}

	/**
	 * Remove edit from the bulk actions.
	 *
	 * @param array $actions
	 * @return array
	 */
	public function wpcm_match_bulk_actions( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		return $actions;
	}

	/**
	 * Custom bulk edit - form
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function bulk_edit( $column_name, $post_type ) {

		if ( 'wpcm_match' != $post_type ) {
			return;
		}

		if ( did_action( 'bulk_edit_custom_box' ) !== 1 ) return;

		$teams = get_terms( 'wpcm_team', array(
			'hide_empty' => false,
		) );

		include( WPCM()->plugin_path() . '/includes/admin/views/html-bulk-edit-match.php' );
	}

	/**
	 * Custom quick edit - form
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function quick_edit( $column_name, $post_type ) {

		if ( 'wpcm_match' != $post_type ) {
			return;
		}

		if ( did_action( 'quick_edit_custom_box' ) !== 1 ) return;

		$teams = get_terms( 'wpcm_team', array(
			'hide_empty' => false,
		) );

		include( WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-match.php' );
	}

	/**
	 * Quick and bulk edit saving
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @return int
	 */
	public function bulk_and_quick_edit_save_post( $post_id, $post ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check post type is match
		if ( 'wpcm_match' != $post->post_type ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpclubmanager_quick_edit_nonce'] ) && ! isset( $_REQUEST['wpclubmanager_bulk_edit_nonce'] ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['wpclubmanager_quick_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['wpclubmanager_quick_edit_nonce'], 'wpclubmanager_quick_edit_nonce' ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['wpclubmanager_bulk_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['wpclubmanager_bulk_edit_nonce'], 'wpclubmanager_bulk_edit_nonce' ) ) {
			return $post_id;
		}

		// Get the match and save
		$match = get_post( $post );

		if ( ! empty( $_REQUEST['wpclubmanager_quick_edit'] ) ) {
			$this->quick_edit_save( $post_id, $match );
		} else {
			$this->bulk_edit_save( $post_id, $match );
		}

		return $post_id;
	}

	/**
	 * Quick edit
	 *
	 * @param integer $post_id
	 * @param $match
	 */
	private function quick_edit_save( $post_id, $match ) {
		global $wpdb;

		// Save fields
		if ( ! empty( $_REQUEST['wpcm_team'] ) ) {
			wp_set_object_terms( $post_id, wpcm_clean( $_REQUEST['wpcm_team'] ), 'wpcm_team' );
		}

		do_action( 'wpclubmanager_match_quick_edit_save', $match );
	}

	/**
	 * Bulk edit
	 * @param integer $post_id
	 * @param $match
	 */
	public function bulk_edit_save( $post_id, $match ) {

		if ( ! empty( $_REQUEST['wpcm_team'] ) ) {
			$teams = '_no_team' == $_REQUEST['wpcm_team'] ? '' : wpcm_clean( $_REQUEST['wpcm_team'] );

			wp_set_object_terms( $post_id, $teams, 'wpcm_team' );
		}

		do_action( 'wpclubmanager_match_bulk_edit_save', $match );
	}
}

endif;

return new WPCM_Admin_Post_Types();