<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.1
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
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {

		global $menu, $wpclubmanager;

		if ( current_user_can( 'manage_wpclubmanager' ) )
	    	$menu[] = array( '', 'read', 'separator-wpclubmanager', '', 'wp-menu-separator wpclubmanager' );

	    $main_page = add_menu_page( __( 'Club Manager', 'wpclubmanager' ), __( 'Club Manager', 'wpclubmanager' ), 'manage_wpclubmanager', 'wpcm-settings' , array( $this, 'settings_page' ), WPCM()->plugin_url() . '/assets/images/logo.png', '55.4' );

		remove_submenu_page('edit.php?post_type=wpcm_player', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_player');
		remove_submenu_page('edit.php?post_type=wpcm_player', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_player');

		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_staff');
		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_staff');
	}

	/**
	 * Addons menu item
	 */
	public function extensions_menu() {
		add_submenu_page( 'wpcm-settings', __( 'WP Club Manager Extensions', 'wpclubmanager' ),  __( 'Extensions', 'wpclubmanager' ) , 'manage_wpclubmanager', 'wpcm-extensions', array( $this, 'extensions_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'wpcm-settings', __( 'Settings', 'wpclubmanager' ),  __( 'Settings', 'wpclubmanager' ) , 'manage_wpclubmanager', 'wpcm-settings', array( $this, 'settings_page' ) );
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
		$page = include( 'class-wpcm-admin-extensions.php' );
		$page->output();
	}
}

endif;

return new WPCM_Admin_Menus();