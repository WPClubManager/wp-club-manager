<?php
/**
 * Load assets.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
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
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_styles() {

		//wp_enqueue_style( 'thickbox' );

		// Sitewide menu CSS
		wp_enqueue_style( 'wpclubmanager_admin_menu_styles', WPCM()->plugin_url() . '/assets/css/menu.css', array(), WPCM_VERSION );

		$screen = get_current_screen();

		if ( in_array( $screen->id, wpcm_get_screen_ids() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			wp_enqueue_style( 'wpclubmanager_admin_styles', WPCM()->plugin_url() . '/assets/css/admin.css', array(), WPCM_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WPCM_VERSION );
		}

		if ( in_array( $screen->id, array( 'dashboard_page_wpcm-getting-started' ) ) ) {

	    	wp_enqueue_style( 'mailchimp-form', '//cdn-images.mailchimp.com/embedcode/slim-081711.css', array(), false );
		}

		do_action( 'wpclubmanager_admin_css' );
	}

	/**
	 * Loads the scripts for the backend.
	 *
	 * @since  1.1
	 * @access public
	 * @return void
	 */
	public function admin_scripts() {

		global $wp_query, $post;

		$screen       = get_current_screen();
		$wpcm_screen_id = strtolower( __( 'WPClubManager', 'wpclubmanager' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script( 'wpclubmanager_admin', WPCM()->plugin_url() . '/assets/js/admin/wpclubmanager_admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'jquery-ui-sortable' ), WPCM_VERSION );

		wp_register_script( 'jquery-tiptip', WPCM()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WPCM_VERSION, true );

		wp_register_script( 'wpclubmanager_admin_meta_boxes', WPCM()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery' ), WPCM_VERSION );

		wp_register_script( 'ajax-chosen', WPCM()->plugin_url() . '/assets/js/jquery-chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), WPCM_VERSION );

		wp_register_script( 'chosen', WPCM()->plugin_url() . '/assets/js/jquery-chosen/chosen.jquery' . $suffix . '.js', array('jquery'), WPCM_VERSION );

		// WPlubManager admin pages
	    if ( in_array( $screen->id, wpcm_get_screen_ids() ) ) {

	    	wp_enqueue_script( 'jquery' );
	    	wp_enqueue_script( 'ajax-chosen' );
	    	wp_enqueue_script( 'chosen' );
	    	wp_enqueue_script( 'wpclubmanager_admin' );
	    }

	    if ( in_array( $screen->id, array( 'wpcm_player', 'wpcm_club', 'wpcm_staff', 'wpcm_sponsor', 'wpcm_match', 'edit-wpcm_player', 'edit-wpcm_club', 'edit-wpcm_staff', 'edit-wpcm_sponsor', 'edit-wpcm_match' ) ) ) {

	    	wp_enqueue_script( 'wpclubmanager_admin_meta_boxes' );
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
		}
	}
}

endif;

return new WPCM_Admin_Assets();