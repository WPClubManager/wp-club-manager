<?php
/**
 * League Table Notes
 *
 * Add notes to league table.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Table_Notes
 */
class WPCM_Meta_Box_Table_Notes {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		wpclubmanager_wp_textarea_input( array(
			'id'    => '_wpcm_table_notes',
			'label' => __( 'Add notes', 'wp-club-manager' ),
			'class' => 'regular-text',
		) );
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$notes = filter_input( INPUT_POST, '_wpcm_table_notes', FILTER_UNSAFE_RAW );
		if ( isset( $notes ) ) {
			update_post_meta( $post_id, '_wpcm_table_notes', sanitize_text_field( $notes ) );
		}
	}
}
