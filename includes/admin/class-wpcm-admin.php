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
		add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
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
			include( 'class-wpcm-admin-welcome.php' );
			include( 'class-wpcm-admin-notices.php' );
			include( 'class-wpcm-admin-assets.php' );
			include( 'class-wpcm-admin-editor.php' );
		}
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditonal_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'dashboard' :
				//include( 'class-wpcm-admin-dashboard.php' );
			break;
			case 'users' :
			case 'user' :
			case 'profile' :
			case 'user-edit' :
				//include( 'class-wpcm-admin-profile.php' );
			break;
		}
	}

	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, supporters etc) from accessing admin
	 */
	public function prevent_admin_access() {
		$prevent_access = false;

		if ( 'yes' == get_option( 'wpclubmanager_lock_down_admin' ) && ! is_ajax() && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_wpclubmanager' ) ) && basename( $_SERVER["SCRIPT_FILENAME"] ) !== 'admin-post.php' ) {
			$prevent_access = true;
		}

		$prevent_access = apply_filters( 'wpclubmanager_prevent_admin_access', $prevent_access );

		if ( $prevent_access ) {
			wp_safe_redirect( get_permalink( wpcm_get_page_id( 'myaccount' ) ) );
			exit;
		}
	}
}

return new WPCM_Admin();