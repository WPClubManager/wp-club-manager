<?php
/**
 * Extensions Page
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Extensions' ) ) :

/**
 * WPCM_Admin_Extensions Class
 */
class WPCM_Admin_Extensions {

	/**
	 * Handles output of the reports page in admin.
	 */
	public function output() {

		// if ( false === ( $extensions = get_transient( 'wpclubmanager_extensions_data' ) ) ) {
		// 	$extensions_json = wp_remote_get( 'http://d3t0oesq8995hv.cloudfront.net/wpclubmanager-extensions.json', array( 'user-agent' => 'WooCommerce Extensions Page' ) );
		// 	if ( ! is_wp_error( $extensions_json ) ) {
		// 		$extensions = json_decode( wp_remote_retrieve_body( $extensions_json ) );
		// 		if ( $extensions ) {
		// 			set_transient( 'wpclubmanager_extensions_data', $extensions, 60*60*24*7 ); // 1 Week
		// 		}
		// 	}
		// }

		// $view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : '';

		include_once( 'views/html-admin-page-extensions.php' );
	}
}

endif;

return new WPCM_Admin_Extensions();