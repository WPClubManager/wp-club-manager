<?php
/**
 * WPClubManager General Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.4.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_General' ) ) :

class WPCM_Settings_General extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'wp-club-manager' );

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

			array( 'title' => __( 'General Options', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title'     => __( 'Sport', 'wp-club-manager' ),
				'desc' 		=> __( 'Choose your sport. Your choice will determine the player stats that are available.', 'wp-club-manager' ),
				'id'        => 'wpcm_sport',
				'class'     => 'chosen_select',
				'css' 		=> 'min-width:350px;',
				'default'   => 'soccer',
				'type'      => 'select',
				'options'   => $sports,
				'desc_tip'	=>  true,
			),

			array(
				'title' 	=> __( 'Default Club', 'wp-club-manager' ),
				'desc' 		=> __( 'This is the default club.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_default_club',
				'css' 		=> 'min-width:350px;',
				'default'	=> '',
				'type' 		=> 'default_club',
				'desc_tip'	=>  true,
			),

			array(
				'title' 	=> __( 'Base Location', 'wp-club-manager' ),
				'desc' 		=> __( 'This is the base country for your club. Players nationality will default to this country.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_default_country',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'EN',
				'type' 		=> 'single_select_country',
				'desc_tip'	=>  true,
			),

			array(
				'title' 	=> __( 'Google Maps API Key', 'wp-club-manager' ),
				'desc' 		=> sprintf( wp_kses( __( '<a href="%s" target="_blank">Get API Key</a>', 'wp-club-manager' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( "https://developers.google.com/maps/documentation/javascript/get-api-key#key" ) ),
				'id' 		=> 'wpcm_google_map_api',
				'css' 		=> 'width:350px;',
				'default'	=> '',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Disable WPCM Cache', 'wp-club-manager' ),
				'desc' 		=> __( 'Check this box to disable the plugin cache.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_disable_cache',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array( 'type' => 'sectionend', 'id' => 'general_options'),

		)); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		if ( isset( $_POST['wpcm_sport'] ) && ! empty( $_POST['wpcm_sport'] ) && get_option( 'wpcm_sport', null ) != $_POST['wpcm_sport'] ):
			$post = $_POST['wpcm_sport'];
			$sport = WPCM()->sports->$post;
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
