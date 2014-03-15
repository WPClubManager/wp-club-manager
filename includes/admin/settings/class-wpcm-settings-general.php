<?php
/**
 * WPClubManager General Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_General' ) ) :

class WPCM_Settings_General extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'wpclubmanager' );

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
		
		return apply_filters( 'wpclubmanager_general_settings', array(

			array( 'title' => __( 'General Options', 'wpclubmanager' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title' 	=> __( 'Default Club', 'wpclubmanager' ),
				'desc' 		=> __( 'This is the default club.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_default_club',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'None',
				'type' 		=> 'default_club',
				'desc_tip'	=>  true,
			),

			array(
				'title' 	=> __( 'Base Location', 'wpclubmanager' ),
				'desc' 		=> __( 'This is the base country for your club. Players nationality will default to this country.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_default_country',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'EN',
				'type' 		=> 'single_select_country',
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'general_options'),

		)); // End general settings
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

return new WPCM_Settings_General();
