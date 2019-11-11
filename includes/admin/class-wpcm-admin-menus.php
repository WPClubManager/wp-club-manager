<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Menus' ) ) :

class WPCM_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'dashboard_menu' ), 10 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'taxonomy_menu' ), 40 );
		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {

		global $menu;

		if ( current_user_can( 'manage_wpclubmanager' ) )
	    	$menu[] = array( '', 'read', 'separator-wpclubmanager', '', 'wp-menu-separator wpclubmanager' );

	    $main_page = add_menu_page( __( 'Club Manager', 'wp-club-manager' ), __( 'Club Manager', 'wp-club-manager' ), 'manage_wpclubmanager', 'wpcm-dashboard' , array( $this, 'dashboard_page' ), WPCM()->plugin_url() . '/assets/images/logo.png', '31' );

		remove_submenu_page('edit.php?post_type=wpcm_player', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_player');
		remove_submenu_page('edit.php?post_type=wpcm_player', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_player');
		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_staff');
		remove_submenu_page('edit.php?post_type=wpcm_staff', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_staff');
		remove_submenu_page('edit.php?post_type=wpcm_club', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_club');
		remove_submenu_page('edit.php?post_type=wpcm_club', 'edit-tags.php?taxonomy=wpcm_comp&amp;post_type=wpcm_club');
	}

	/**
	 * Add menu items
	 */
	public function taxonomy_menu() {

		add_submenu_page( 'wpcm-dashboard', __( 'Seasons', 'wp-club-manager' ),  __( 'Seasons', 'wp-club-manager' ) , 'manage_wpclubmanager', 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_club', false );
		add_submenu_page( 'wpcm-dashboard', __( 'Competitions', 'wp-club-manager' ),  __( 'Competitions', 'wp-club-manager' ) , 'manage_wpclubmanager', 'edit-tags.php?taxonomy=wpcm_comp&amp;post_type=wpcm_club', false );
		if( is_club_mode() ) {
			add_submenu_page( 'wpcm-dashboard', __( 'Teams', 'wp-club-manager' ),  __( 'Teams', 'wp-club-manager' ) , 'manage_wpclubmanager', 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_player', false );
			add_submenu_page( 'wpcm-dashboard', __( 'Rosters', 'wp-club-manager' ),  __( 'Rosters', 'wp-club-manager' ) , 'manage_wpclubmanager', 'edit.php?post_type=wpcm_roster', false );
		}
		add_submenu_page( 'wpcm-dashboard', __( 'League Tables', 'wp-club-manager' ),  __( 'League Tables', 'wp-club-manager' ) , 'manage_wpclubmanager', 'edit.php?post_type=wpcm_table', false );
	}

	/**
	 * Dashboard menu item
	 */
	public function dashboard_menu() {
		add_submenu_page( 'wpcm-dashboard', __( 'WP Club Manager Dashboard', 'wp-club-manager' ),  __( 'Dashboard', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-dashboard', array( $this, 'dashboard_page' ) );
	}

	/**
	 * Status menu item
	 */
	public function status_menu() {
		add_submenu_page( 'wpcm-dashboard', __( 'WP Club Manager Status', 'wp-club-manager' ),  __( 'Status', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-status', array( $this, 'status_page' ) );
	}

	/**
	 * Addons menu item
	 */
	public function extensions_menu() {
		add_submenu_page( 'wpcm-dashboard', __( 'WP Club Manager Extensions', 'wp-club-manager' ),  __( 'Upgrade to Pro', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-extensions', array( $this, 'extensions_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'wpcm-dashboard', __( 'Settings', 'wp-club-manager' ),  __( 'Settings', 'wp-club-manager' ) , 'manage_wpclubmanager', 'wpcm-settings', array( $this, 'settings_page' ) );
	}

	/**
	 * Init the settings page
	 */
	public function dashboard_page() {
		WPCM_Admin_Dashboard::output();
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
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		switch ( $post_type ) {
			case 'wpcm_roster' :
				//$parent_file = 'edit.php?post_type=wpcm_player';
				$parent_file = 'wpcm-dashboard';
			break;
		}

		switch ( $submenu_file ) {
			case 'edit-tags.php?taxonomy=wpcm_season&amp;post_type=wpcm_club' :
				$parent_file = 'wpcm-dashboard';
			break;
			case 'edit-tags.php?taxonomy=wpcm_comp&amp;post_type=wpcm_club' :
				$parent_file = 'wpcm-dashboard';
			break;
			case 'edit-tags.php?taxonomy=wpcm_team&amp;post_type=wpcm_player' :
				$parent_file = 'wpcm-dashboard';
			break;
		}
	}
}

endif;

return new WPCM_Admin_Menus();