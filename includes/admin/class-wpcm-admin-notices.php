<?php
/**
 * Display notices in admin.
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Notices' ) ) :

/**
 * WPCM_Admin_Notices Class
 */
class WPCM_Admin_Notices {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'switch_theme', array( $this, 'reset_admin_notices' ) );
		add_action( 'wpclubmanager_updated', array( $this, 'reset_admin_notices' ) );
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
	}

	/**
	 * Reset notices for themes when switched or a new version of WPCM is installed
	 */
	public function reset_admin_notices() {
		update_option( 'wpclubmanager_admin_notices', array( 'template_files', 'theme_support' ) );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {
		// if ( get_option( '_wpcm_needs_welcome' ) == 1 ) {
		// 	wp_enqueue_style( 'wpclubmanager-activation', plugins_url(  '/assets/css/activation.css', WPCM_PLUGIN_FILE ) );
		// 	add_action( 'admin_notices', array( $this, 'install_notice' ) );
		// }

		$notices = get_option( 'wpclubmanager_admin_notices', array() );

		if ( ! empty( $_GET['hide_theme_support_notice'] ) ) {
			$notices = array_diff( $notices, array( 'theme_support' ) );
			update_option( 'wpclubmanager_admin_notices', $notices );
		}

		if ( ! empty( $_GET['hide_template_files_notice'] ) ) {
			$notices = array_diff( $notices, array( 'template_files' ) );
			update_option( 'wpclubmanager_admin_notices', $notices );
		}

		if ( in_array( 'theme_support', $notices ) && ! current_theme_supports( 'wpclubmanager' ) ) {
			$template = get_option( 'template' );

			if ( ! in_array( $template, array( 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' ) ) ) {
				wp_enqueue_style( 'wpclubmanager-activation', plugins_url(  '/assets/css/activation.css', WPCM_PLUGIN_FILE ) );
				add_action( 'admin_notices', array( $this, 'theme_check_notice' ) );
			}
		}

		if ( in_array( 'template_files', $notices ) ) {
			wp_enqueue_style( 'wpclubmanager-activation', plugins_url(  '/assets/css/activation.css', WPCM_PLUGIN_FILE ) );
//			add_action( 'admin_notices', array( $this, 'template_file_check_notice' ) );
		}
	}

	/**
	 * Show the install notices
	 */
	// public function install_notice() {
	// 	$screen = get_current_screen();

	// 	// If we have just installed, show a message with the install pages button
	// 	if ( get_option( '_wpcm_needs_welcome' ) == 1 && $screen->id != 'toplevel_page_wpclubmanager' ) {
	// 		include( 'views/html-notice-install.php' );
	// 	}
	// }

	/**
	 * Show the Theme Check notice
	 */
	public function theme_check_notice() {
//		include( 'views/html-notice-theme-support.php' );
	}

	/**
	 * Show a notice highlighting bad template files
	 */
	public function template_file_check_notice() {
		if ( isset( $_GET['page'] ) && 'wpcm-status' == $_GET['page'] ) {
			return;
		}

		$status         = include( 'class-wpcm-admin-status.php' );
		$core_templates = $status->scan_template_files( WPCM()->plugin_path() . '/templates' );
		$outdated       = false;

		foreach ( $core_templates as $file ) {
			$theme_file = false;
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/wpclubmanager/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/wpclubmanager/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif( file_exists( get_template_directory() . '/wpclubmanager/' . $file ) ) {
				$theme_file = get_template_directory() . '/wpclubmanager/' . $file;
			}

			if ( $theme_file ) {
				$core_version  = $status->get_file_version( WPCM()->plugin_path() . '/templates/' . $file );
				$theme_version = $status->get_file_version( $theme_file );

				if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
					$outdated = true;
					break;
				}
			}
		}

		if ( $outdated ) {
			include( 'views/html-notice-template-check.php' );
		}
	}
}

endif;

return new WPCM_Admin_Notices();