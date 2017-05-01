<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Menus' ) ) :

class WPCM_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'extensions_menu' ), 70 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'tools_menu' ), 60 );
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {

		global $menu, $wpclubmanager;

		if ( current_user_can( 'manage_wpclubmanager' ) )
	    	$menu[] = array( '', 'read', 'separator-wpclubmanager', '', 'wp-menu-separator wpclubmanager' );

	    $main_page = add_menu_page( __( 'Club Manager', 'wp-club-manager' ), __( 'Club Manager', 'wp-club-manager' ), 'manage_wpclubmanager', 'wpcm-settings' , array( $this, 'settings_page' ), WPCM()->plugin_url() . '/assets/images/logo.png', '55.4' );

		remove_submenu_page('edit.php?post_type=wpcm_player', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_player');

		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_staff');
		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_staff');
	}

	/**
	 * Tools menu item
	 */
	public function tools_menu() {
		add_submenu_page( 'wpcm-settings', __( 'WP Club Manager Tools', 'wp-club-manager' ),  __( 'Tools', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-tools', array( $this, 'tools_page' ) );
	}

	/**
	 * Status menu item
	 */
	public function status_menu() {
		add_submenu_page( 'wpcm-settings', __( 'WP Club Manager Status', 'wp-club-manager' ),  __( 'Status', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-status', array( $this, 'status_page' ) );
	}

	/**
	 * Addons menu item
	 */
	public function extensions_menu() {
		add_submenu_page( 'wpcm-settings', __( 'WP Club Manager Extensions', 'wp-club-manager' ),  __( 'Extensions', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-extensions', array( $this, 'extensions_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'wpcm-settings', __( 'Settings', 'wp-club-manager' ),  __( 'Settings', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-settings', array( $this, 'settings_page' ) );
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		include_once( 'class-wpcm-admin-settings.php' );
		WPCM_Admin_Settings::output();
	}

	/**
	 * Init the extensions page
	 */
	public function extensions_page() {
		WPCM_Admin_Extensions::output();
	}
	/**
	 * Init the extensions page
	 */
	public function status_page() {
		WPCM_Admin_Status::output();
	}
	/**
	 * Init the tools page
	 */
	public function tools_page() {
		WPCM_Admin_Tools::output();
	}
}

endif;

return new WPCM_Admin_Menus();