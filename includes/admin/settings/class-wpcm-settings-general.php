<?php
/**
 * WPClubManager General Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
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

		$sports = wpcm_get_sport_options();

		return apply_filters( 'wpclubmanager_general_settings', array(

			array( 'title' => __( 'General Options', 'wpclubmanager' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title'     => __( 'Sport', 'wpclubmanager' ),
				'desc' 		=> __( 'Choose your sport. Your choice will determine the player stats that are available.', 'wpclubmanager' ),
				'id'        => 'wpcm_sport',
				'class'     => 'chosen_select',
				'css' 		=> 'min-width:350px;',
				'default'   => 'soccer',
				'type'      => 'select',
				'options'   => $sports,
				'desc_tip'	=>  true,
			),

			array(
				'title' 	=> __( 'Default Club', 'wpclubmanager' ),
				'desc' 		=> __( 'This is the default club.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_default_club',
				'css' 		=> 'min-width:350px;',
				'default'	=> '',
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
		if ( isset( $_POST['wpcm_sport'] ) && ! empty( $_POST['wpcm_sport'] ) && get_option( 'wpcm_sport', null ) != $_POST['wpcm_sport'] ):
			$sport = WPCM()->sports->$_POST['wpcm_sport'];
			WPCM_Admin_Settings::configure_sport( $sport );
    		update_option( '_wpcm_needs_welcome', 0 );
		elseif ( isset( $_POST['wpcm_primary_result'] ) ):
	    	update_option( 'wpcm_primary_result', $_POST['wpcm_primary_result'] );
		endif;

		$settings = $this->get_settings();

		WPCM_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WPCM_Settings_General();
