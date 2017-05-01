<?php
/**
 * WPClubManager Licenses Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Licenses' ) ) :

class WPCM_Settings_Licenses extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'licenses';
		$this->label = __( 'Licenses', 'wp-club-manager' );

		add_filter( 'wpclubmanager_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'wpclubmanager_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'wpclubmanager_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'wpclubmanager_license_settings', array() );

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		WPCM_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WPCM_Settings_Licenses();