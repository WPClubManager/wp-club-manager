<?php
/**
 * WPClubManager General Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
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

		$settings = array(

			array( 'title' => __( 'General Settings', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title' 	=> __( 'Plugin Mode', 'wp-club-manager' ),
				'id' 		=> 'wpcm_mode',
				'default'	=> 'club',
				'type' 		=> 'radio',
				'options' 	=> array(
					'club'		=> __( 'Club', 'wp-club-manager' ),
					'league'	=> __( 'League', 'wp-club-manager' ),
				)
			),

			array(
				'title'     => __( 'Default Sport', 'wp-club-manager' ),
				'id'        => 'wpcm_sport',
				'class'     => 'chosen_select',
				'css' 		=> 'min-width:350px;',
				'default'   => 'soccer',
				'type'      => 'select',
				'options'   => $sports
			),

			array(
				'title' 	=> __( 'Default Location', 'wp-club-manager' ),
				'id' 		=> 'wpcm_default_country',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'EN',
				'type' 		=> 'single_select_country'
			)
		);

		if( get_option( 'wpcm_mode', 'club' ) == 'club') {
			$settings[] = array(
				'title' 	=> __( 'Default Club', 'wp-club-manager' ),
				'id' 		=> 'wpcm_default_club',
				'css' 		=> 'min-width:350px;',
				'default'	=> '',
				'type' 		=> 'default_club'
			);
		}

		$settings[] = array(
			'title' => __( 'Plugin Cache', 'wp-club-manager' ),
			'id' 		=> 'wpcm_disable_cache',
			'default'	=> 'no',
			'type' 		=> 'radio',
			'options'	=> array(
				'no' => __( 'Enable (Recommended)', 'wp-club-manager' ),
				'yes' => __( 'Disable', 'wp-club-manager' ),
			),
		);

		$settings[] = array(
			'title' => '',
			'id' 		=> 'wpcm_clear_cache',
			'type' 		=> 'cache_button'
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'general_options');

		$settings[] = array( 'title' => __( 'Google Maps Settings', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'map_options' );

		$settings[] = array(
			'title' 	=> __( 'Maps API Key', 'wp-club-manager' ),
			'id' 		=> 'wpcm_google_map_api',
			'css' 		=> 'width: 100%;max-width:350px;',
			'default'	=> '',
			'type' 		=> 'text',
			'desc'		=>  sprintf( __( '<a href="%s" target="_blank">Get API Key</a>', 'wp-club-manager' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key' )
		);

		$settings[] = array(
			'title' 	=> __( 'Map Type', 'wp-club-manager' ),
			'desc' 		=> '',
			'id' 		=> 'wpcm_map_type',
			'default'	=> 'roadmap',
			'type' 		=> 'radio',
			'options' => array(
				'roadmap'  => __( 'Roadmap', 'wp-club-manager' ),
				'satellite'	=> __( 'Satelitte', 'wp-club-manager' ),
			)
		);

		$settings[] = array(
			'title' 	=> __( 'Map Zoom', 'wp-club-manager' ),
			'id' 		=> 'wpcm_map_zoom',
			'class' 	=> 'small-text',
			'default'	=> '15',
			'type' 		=> 'number',
			'custom_attributes' => array(
				'min' 	=> 0,
				'max' 	=> 21,
				'step' 	=> 1
			),
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'map_options');

		return apply_filters( 'wpclubmanager_general_settings', $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		if ( isset( $_POST['wpcm_sport'] ) && ! empty( $_POST['wpcm_sport'] ) && get_option( 'wpcm_sport' ) != $_POST['wpcm_sport'] ) {
			$post = $_POST['wpcm_sport'];
			$sport = WPCM()->sports->$post;
			WPCM_Admin_Settings::configure_sport( $sport );
		}

		$settings = $this->get_settings();

		WPCM_Admin_Settings::save_fields( $settings );

		wpcm_flush_rewrite_rules();
	}

}

endif;

return new WPCM_Settings_General();
