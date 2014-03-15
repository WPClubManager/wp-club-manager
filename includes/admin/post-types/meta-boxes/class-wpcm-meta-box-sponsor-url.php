<?php
/**
 * Sponsor Url
 *
 * Displays the sponsor url box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Sponsor_Url {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post;

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_link_url', 'label' => __( 'Link URL', 'wpclubmanager' ), 'class' => 'regular-text' ) );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$link_url = $_POST['wpcm_link_url'];
		
		if ( isset( $link_url ) && strpos( $link_url, 'http://' ) === false )
			$link_url = 'http://' . $link_url;

		update_post_meta( $post_id, 'wpcm_link_url', $link_url );
	}
}