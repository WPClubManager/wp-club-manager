<?php
/**
 * Load assets.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Assets' ) ) :

class WPCM_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Loads the styles for the backend.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function admin_styles() {

		// Sitewide menu CSS
		wp_enqueue_style( 'wpclubmanager_admin_menu_styles', WPCM()->plugin_url() . '/assets/css/menu.css', array(), false );

		$screen = get_current_screen();

		if ( in_array( $screen->id, wpcm_get_screen_ids() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			wp_enqueue_style( 'wpclubmanager_admin_styles', WPCM()->plugin_url() . '/assets/css/admin.css', array(), WPCM_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WPCM_VERSION );

			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
			wp_enqueue_style( 'wpclubmanager_admin_dashboard_styles', WPCM()->plugin_url() . '/assets/css/dashboard.css', array(), WPCM_VERSION );
		}

		if ( in_array( $screen->id, array( 'toplevel_page_wpcm-dashboard' ) ) ) {
			wp_enqueue_style( 'wpclubmanager_dashboard_ui', WPCM()->plugin_url() . '/assets/css/wpcm-dashboard.css', array(), WPCM_VERSION );
		}

		do_action( 'wpclubmanager_admin_css' );
	}

	/**
	 * Loads the scripts for the backend.
	 *
	 * @since  2.1.10
	 * @access public
	 * @return void
	 */
	public function admin_scripts( $hook ) {

		global $wp_query, $post;

		$screen       	= get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$wpcm_screen_id = strtolower( __( 'WPClubManager', 'wp-club-manager' ) );
		$suffix       	= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$api_key      	= get_option( 'wpcm_google_map_api');
		$map			= get_option( 'wpcm_map_select', 'google' );

		// Register scripts
		wp_register_script( 'wpclubmanager_admin', WPCM()->plugin_url() . '/assets/js/admin/wpclubmanager_admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-sortable' ), WPCM_VERSION );

		wp_register_script( 'ajax-chosen', WPCM()->plugin_url() . '/assets/js/vendor/jquery-chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), WPCM_VERSION );

		wp_register_script( 'order-chosen', WPCM()->plugin_url() . '/assets/js/vendor/jquery-chosen/chosen.order.jquery' . $suffix . '.js', array('jquery'), '1.2.1' );

		wp_register_script( 'chosen', WPCM()->plugin_url() . '/assets/js/vendor/jquery-chosen/chosen.jquery' . $suffix . '.js', array('jquery'), '1.8.2' );

		wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places' );

		wp_register_script( 'jquery-locationpicker', WPCM()->plugin_url() . '/assets/js/vendor/locationpicker.jquery.js', array( 'jquery', 'google-maps' ), '0.1.16', true );

		wp_register_script( 'wpclubmanager-admin-locationpicker', WPCM()->plugin_url() . '/assets/js/admin/locationpicker.js', array( 'jquery', 'google-maps', 'jquery-locationpicker' ), WPCM_VERSION, true );

		wp_register_script( 'jquery-timepicker', WPCM()->plugin_url() . '/assets/js/vendor/jquery.timepicker' .$suffix . '.js', array( 'jquery' ), '1.13.4', true );

		wp_register_script( 'wpclubmanager-admin-combify', WPCM()->plugin_url() . '/assets/js/admin/combify' .$suffix . '.js', array( 'jquery' ), WPCM_VERSION, true );

		wp_register_script( 'wpclubmanager_admin_meta_boxes', WPCM()->plugin_url() . '/assets/js/admin/meta-boxes' .$suffix . '.js', array( 'jquery', 'chosen', 'order-chosen', 'iris', 'jquery-timepicker', 'jquery-ui-datepicker', 'wpclubmanager-admin-combify' ), WPCM_VERSION );

		if ( in_array( $screen_id, array( 'edit-wpcm_match', 'edit-wpcm_player', 'edit-wpcm_staff' ) ) ) {
			wp_register_script( 'wpclubmanager_quick-edit', WPCM()->plugin_url() . '/assets/js/admin/quick-edit.js', array( 'jquery', 'wpclubmanager_admin' ), WPCM_VERSION );
			wp_enqueue_script( 'wpclubmanager_quick-edit' );
		}

		if ( in_array( $screen_id, array( 'edit-wpcm_team', 'edit-wpcm_season', 'edit-wpcm_position', 'edit-wpcm_job', 'edit-wpcm_comp' ) ) ) {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

		// Edit venue pages
	    if ( in_array( $screen_id, array( 'edit-wpcm_venue' ) ) && $map == 'google' ) {
	    	wp_enqueue_script( 'google-maps' );
	    	wp_enqueue_script( 'jquery-locationpicker' );
	    	wp_enqueue_script( 'wpclubmanager-admin-locationpicker' );
		}

		// WPlubManager admin pages
	    if ( in_array( $screen_id, wpcm_get_screen_ids() ) ) {

	    	wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'order-chosen' );
	    	wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'jquery-timepicker' );
	    	wp_enqueue_script( 'wpclubmanager_admin' );

	    }

	    if ( in_array( $screen_id, array( 'wpcm_player', 'wpcm_club', 'wpcm_staff', 'wpcm_sponsor', 'wpcm_table', 'wpcm_roster', 'wpcm_match' ) ) ) {

			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'order-chosen' );
			wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'jquery-timepicker' );
			wp_enqueue_script( 'wpclubmanager-admin-combify' );
			wp_enqueue_script( 'wpclubmanager_admin_meta_boxes' );
		}

		// System status
		if ( 'club-manager_page_wpcm-status' === $screen_id ) {
			wp_enqueue_script( 'zeroclipboard', WPCM()->plugin_url() . '/assets/js/vendor/zeroclipboard/jquery.zeroclipboard' . $suffix . '.js', array( 'jquery' ), WPCM_VERSION );
		}

		if ( in_array( $screen_id, array( 'toplevel_page_wpcm-dashboard' ) ) ) {
			wp_enqueue_script( 'wpclubmanager_dashboard_js', WPCM()->plugin_url() . '/assets/js/admin/wpcm-dashboard.js', array( 'jquery' ), WPCM_VERSION );
		}
	}
}

endif;

return new WPCM_Admin_Assets();
