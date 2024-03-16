<?php
/**
 * Club Details
 *
 * Displays the club details box.
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
 * WPCM_Meta_Box_Club_Details
 */
class WPCM_Meta_Box_Club_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$defaults = get_club_details( $post );

		wpclubmanager_wp_text_input( array(
			'id'          => '_wpcm_club_abbr',
			'label'       => __( 'Abbreviation', 'wp-club-manager' ),
			'class'       => 'measure-text',
			'maxlength'   => '4',
			'placeholder' => get_club_abbreviation( $post->ID ),
		) );

		wpclubmanager_wp_text_input( array(
			'id'          => '_wpcm_club_formed',
			'label'       => __( 'Formed', 'wp-club-manager' ),
			'class'       => 'measure-text',
			'maxlength'   => '4',
			'placeholder' => $defaults['formed'],
		) );

		wpclubmanager_wp_color_input( array(
			'id'          => '_wpcm_club_primary_color',
			'label'       => __( 'Primary Color', 'wp-club-manager' ),
			'placeholder' => $defaults['primary_color'],
		) );

		wpclubmanager_wp_color_input( array(
			'id'          => '_wpcm_club_secondary_color',
			'label'       => __( 'Secondary Color', 'wp-club-manager' ),
			'placeholder' => $defaults['secondary_color'],
		) );

		wpclubmanager_wp_text_input( array(
			'id'          => '_wpcm_club_website',
			'label'       => __( 'Website', 'wp-club-manager' ),
			'class'       => 'regular-text',
			'type'        => 'url',
			'placeholder' => $defaults['website'],
		) );

		wpclubmanager_wp_textarea_input( array(
			'id'          => '_wpcm_club_honours',
			'label'       => __( 'Honours', 'wp-club-manager' ),
			'class'       => 'regular-text',
			'placeholder' => $defaults['honours'],
		) );

		do_action( 'wpclubmanager_admin_club_details', $post->ID );
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
		// if( isset( $_POST['parent_id'] )  ) {
		// update_post_meta( $post_id, '_wpcm_club_parent', $_POST['parent_id'] );
		// }
		$club_abr = filter_input( INPUT_POST, '_wpcm_club_abbr', FILTER_UNSAFE_RAW );
		if ( $club_abr ) {
			update_post_meta( $post_id, '_wpcm_club_abbr', sanitize_text_field( $club_abr ) );
		}

		$formed = filter_input( INPUT_POST, '_wpcm_club_formed', FILTER_UNSAFE_RAW );
		if ( $formed ) {
			update_post_meta( $post_id, '_wpcm_club_formed', sanitize_text_field( $formed ) );
		}

		$primary = filter_input( INPUT_POST, '_wpcm_club_primary_color', FILTER_UNSAFE_RAW );
		if ( $primary ) {
			update_post_meta( $post_id, '_wpcm_club_primary_color', sanitize_text_field( $primary ) );
		}

		$secondary = filter_input( INPUT_POST, '_wpcm_club_secondary_color', FILTER_UNSAFE_RAW );
		if ( $secondary ) {
			update_post_meta( $post_id, '_wpcm_club_secondary_color', sanitize_text_field( $secondary ) );
		}

		$website = filter_input( INPUT_POST, '_wpcm_club_website', FILTER_UNSAFE_RAW );
		if ( $website ) {
			update_post_meta( $post_id, '_wpcm_club_website', sanitize_text_field( $website ) );
		}

		$honours = filter_input( INPUT_POST, '_wpcm_club_honours', FILTER_UNSAFE_RAW );
		if ( $honours ) {
			update_post_meta( $post_id, '_wpcm_club_honours', sanitize_text_field( $honours ) );
		}

		do_action( 'delete_plugin_transients' );
	}
}
