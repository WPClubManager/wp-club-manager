<?php
/**
 * WPClubManager Admin
 *
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @class 		WPCM_Admin
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_footer', 'wpcm_print_js', 25 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		include_once( 'wpcm-admin-functions.php' );
		include_once( 'wpcm-meta-box-functions.php' );

		include_once( 'class-wpcm-admin-post-types.php' );
		include_once( 'class-wpcm-admin-taxonomies.php' );

		// Classes we only need if the ajax is not-ajax
		if ( ! is_ajax() ) {
			include( 'class-wpcm-admin-menus.php' );
			include( 'class-wpcm-admin-assets.php' );
			include( 'class-wpcm-admin-editor.php' );
		}
	}
}

return new WPCM_Admin();