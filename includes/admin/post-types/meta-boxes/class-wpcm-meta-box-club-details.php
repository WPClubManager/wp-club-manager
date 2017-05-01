<?php
/**
 * Club Details
 *
 * Displays the club details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Club_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post, $wp_locale;

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
		
		wpclubmanager_wp_text_input( array( 'id' => '_wpcm_club_formed', 'label' => __( 'Formed', 'wp-club-manager' ), 'class' => 'small-text' ) );

		wpclubmanager_wp_color_input( array( 'id' => '_wpcm_club_primary_color', 'label' => __( 'Primary Color', 'wp-club-manager' ), 'class' => 'regular-text' ) );

		wpclubmanager_wp_color_input( array( 'id' => '_wpcm_club_secondary_color', 'label' => __( 'Secondary Color', 'wp-club-manager' ), 'class' => 'regular-text' ) );

		wpclubmanager_wp_textarea_input( array( 'id' => '_wpcm_club_honours', 'label' => __( 'Honours', 'wp-club-manager' ), 'class' => 'regular-text' ) );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['_wpcm_club_formed'] ) ) {
			update_post_meta( $post_id, '_wpcm_club_formed', $_POST['_wpcm_club_formed'] );
		}
		if( isset( $_POST['_wpcm_club_primary_color'] ) ) {
			update_post_meta( $post_id, '_wpcm_club_primary_color', $_POST['_wpcm_club_primary_color'] );
		}
		if( isset( $_POST['_wpcm_club_secondary_color'] ) ) {
			update_post_meta( $post_id, '_wpcm_club_secondary_color', $_POST['_wpcm_club_secondary_color'] );
		}
		if( isset( $_POST['_wpcm_club_honours'] ) ) {
			update_post_meta( $post_id, '_wpcm_club_honours', $_POST['_wpcm_club_honours'] );
		}

		do_action( 'delete_plugin_transients' );
	}
}