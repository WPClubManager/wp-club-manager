<?php
/**
 * WPClubManager Settings Page/Tab
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Page' ) ) :

class WPCM_Settings_Page {

	protected $id    = '';
	protected $label = '';

	/**
	 * Add this page to settings
	 */
	public function add_settings_page( $pages ) {
		$pages[ $this->id ] = $this->label;

		return $pages;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return array();
	}

	/**
	 * Output the settings
	 */
	public function output() {
		$settings = $this->get_settings();

		WPCM_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings();
		WPCM_Admin_Settings::save_fields( $settings );

		 if ( $current_section )
	    	do_action( 'wpclubmanager_update_options_' . $this->id . '_' . $current_section );
	}
}

endif;