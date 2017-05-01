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
 * @version     1.4.0
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
		add_action( 'admin_footer', 'wpclubmanager_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'wpclubmanager_admin_rate_us' ), 1 );
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
			include( 'class-wpcm-admin-permalink-settings.php' );
			include( 'class-wpcm-admin-editor.php' );
		}

		// Importers
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			include( 'class-wpcm-admin-importers.php' );
		}
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditonal_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'dashboard' :
				include( 'class-wpcm-admin-dashboard.php' );
			break;
			case 'options-permalink' :
				include( 'class-wpcm-admin-permalink-settings.php' );
			break;
			case 'users' :
			case 'user' :
			case 'profile' :
			case 'user-edit' :
				include( 'class-wpcm-admin-profile.php' );
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
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Add rating links to the admin dashboard
	 *
	 * @since	    1.3.2
	 * @param       string $footer_text
	 * @return      string
	 */
	public function wpclubmanager_admin_rate_us( $footer_text ) {
		if ( ! current_user_can( 'manage_wpclubmanager' ) ) {
			return;
		}

		$current_screen = get_current_screen();
		$wpcm_pages     = wpcm_get_screen_ids();

		if ( isset( $current_screen->id ) && apply_filters( 'wpclubmanager_display_admin_footer_text', in_array( $current_screen->id, $wpcm_pages ) ) ) {
			if ( ! get_option( 'wpclubmanager_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>WP Club Manager</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you in advance!', 'wp-club-manager' ), '<a href="https://wordpress.org/support/view/plugin-reviews/wp-club-manager?filter=5#postform" target="_blank" class="wpcm-rating-link" data-rated="' . esc_attr__( 'Many thanks :)', 'wp-club-manager' ) . '">', '</a>' );
				wpclubmanager_enqueue_js( "
					jQuery( 'a.wpcm-rating-link' ).click( function() {
						jQuery.post( '" . WPCM()->ajax_url() . "', { action: 'wpclubmanager_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for managing your club with WP Club Manager, your support is much appreciated.', 'wp-club-manager' );
			}
		}

		return $footer_text;
	}
}

return new WPCM_Admin();