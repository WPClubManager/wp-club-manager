<?php
/**
 * WPClubManager Admin
 *
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @class       WPCM_Admin
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Admin
 */
class WPCM_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		// add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		add_action( 'admin_init', array( $this, 'admin_redirects' ) );
		add_action( 'admin_footer', array( $this, 'wpclubmanager_print_js' ), 25 );
		add_filter( 'admin_footer_text', array( $this, 'wpclubmanager_admin_rate_us' ), 1 );
		add_filter( 'admin_body_class', array( $this, 'wpclubmanager_admin_body_class' ) );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		include_once 'wpcm-admin-functions.php';
		include_once 'wpcm-meta-box-functions.php';

		include_once 'class-wpcm-admin-post-types.php';
		include_once 'class-wpcm-admin-taxonomies.php';

		// Classes we only need if the ajax is not-ajax
		// if ( ! is_ajax() ) {
			include 'class-wpcm-admin-menus.php';
			include 'class-wpcm-admin-notices.php';
			include 'class-wpcm-admin-assets.php';
			include 'class-wpcm-admin-permalink-settings.php';
			include 'class-wpcm-admin-editor.php';
		// }

		// Help Tabs
		if ( apply_filters( 'wpclubmanager_enable_admin_help_tab', true ) ) {
			include_once 'class-wpcm-admin-help.php';
		}

		// Setup/welcome
		if ( ! empty( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'wpcm-setup':
					include_once 'class-wpcm-admin-setup-wizard.php';
					break;
			}
		}

		// Importers
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			include 'class-wpcm-admin-importers.php';
		}
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditonal_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'dashboard':
				include 'class-wpcm-admin-dashboard-widgets.php';
				break;
			case 'options-permalink':
				include 'class-wpcm-admin-permalink-settings.php';
				break;
			case 'users':
			case 'user':
			case 'profile':
			case 'user-edit':
				include 'class-wpcm-admin-profile.php';
				break;
		}
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function admin_redirects() {

		// Nonced plugin install redirects (whitelisted)
		$redirect = filter_input( INPUT_GET, 'wpcm-install-plugin-redirect', FILTER_UNSAFE_RAW );
		if ( ! empty( $redirect ) ) {
			$plugin_slug = wpcm_clean( $redirect );
			$url         = admin_url( 'plugin-install.php?tab=search&type=term&s=' . $plugin_slug );
			wp_safe_redirect( $url );
			exit;
		}

		// Setup wizard redirect
		if ( get_transient( '_wpcm_activation_redirect' ) ) {
			delete_transient( '_wpcm_activation_redirect' );

			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'wpcm-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( 'manage_wpclubmanager' ) || apply_filters( 'wpclubmanager_prevent_automatic_wizard_redirect', false ) ) {
				return;
			}

			// If the user needs to install, send them to the setup wizard
			if ( WPCM_Admin_Notices::has_notice( 'install' ) ) {
				wp_safe_redirect( admin_url( 'index.php?page=wpcm-setup' ) );
				exit;
			}
		}
	}

	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, supporters etc) from accessing admin
	 */
	public function prevent_admin_access() {
		$prevent_access = false;

		if ( get_option( 'wpclubmanager_lock_down_admin' ) === 'yes' && ! is_ajax() && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_wpclubmanager' ) ) && ( isset( $_SERVER['SCRIPT_FILENAME'] ) && basename( $_SERVER['SCRIPT_FILENAME'] ) !== 'admin-post.php' ) ) { // phpcs:ignore
			$prevent_access = true;
		}

		$prevent_access = apply_filters( 'wpclubmanager_prevent_admin_access', $prevent_access );

		if ( $prevent_access ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Queue some JavaScript code to be output in the footer.
	 *
	 * @param string $code
	 */
	public function wpclubmanager_enqueue_js( $code ) {

		global $wpclubmanager_queued_js;

		if ( empty( $wpclubmanager_queued_js ) ) {
			$wpclubmanager_queued_js = '';
		}

		$wpclubmanager_queued_js .= "\n" . $code . "\n";
	}

	/**
	 * Output any queued javascript code in the footer.
	 */
	public function wpclubmanager_print_js() {

		global $wpclubmanager_queued_js;

		if ( ! empty( $wpclubmanager_queued_js ) ) {

			echo "<!-- WP Club Manager JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

			// Sanitize
			$wpclubmanager_queued_js = wp_check_invalid_utf8( $wpclubmanager_queued_js );
			$wpclubmanager_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpclubmanager_queued_js );
			$wpclubmanager_queued_js = str_replace( "\r", '', $wpclubmanager_queued_js );

			echo $wpclubmanager_queued_js . "});\n</script>\n"; // phpcs:ignore

			unset( $wpclubmanager_queued_js );
		}
	}

	/**
	 * Add rating links to the admin dashboard
	 *
	 * @since       2.0.0
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

				/* translators: 1: review URL */
				$footer_text = sprintf( __( 'If you like <strong>WP Club Manager</strong> please leave us a %1$s&#9733;&#9733;&#9733;&#9733;&#9733;%2$s rating. A huge thank you in advance!', 'wp-club-manager' ), '<a href="https://wordpress.org/support/view/plugin-reviews/wp-club-manager?filter=5#postform" target="_blank" class="wpcm-rating-link" data-rated="' . esc_attr__( 'Many thanks :)', 'wp-club-manager' ) . '">', '</a>' );
				$this->wpclubmanager_enqueue_js( "
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

	/**
	 * @param string $classes
	 *
	 * @return string
	 */
	public function wpclubmanager_admin_body_class( $classes ) {

		$sport = get_option( 'wpcm_sport' );

		return $classes . ' ' . $sport;
	}
}

return new WPCM_Admin();
