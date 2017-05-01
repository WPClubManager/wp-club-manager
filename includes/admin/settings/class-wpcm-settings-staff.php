<?php
/**
 * WPClubManager Staff Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Staff' ) ) :

class WPCM_Settings_Staff extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'staff';
		$this->label = __( 'Staff', 'wp-club-manager' );

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
		
		return apply_filters( 'wpclubmanager_staff_settings', array(

			array( 'title' => __( 'Staff Profile Options', 'wp-club-manager' ), 'type' => 'title', 'desc' => __( '<p>Choose which fields to display on staff profile pages.</p>', 'wp-club-manager' ), 'id' => 'staff_options' ),

			array(
				'title' => __( 'Birthday', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_dob',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Age', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_age',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Season', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_season',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Team', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_team',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Job Title', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_jobs',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Email', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_show_staff_email',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Phone', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_show_staff_phone',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Nationality', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_nationality',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Joined', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_staff_profile_show_joined',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array( 'type' => 'sectionend', 'id' => 'staff_options'),

			array(	'title' => __( 'Staff Image Sizes', 'wp-club-manager' ), 'type' => 'title','desc' => sprintf(__( 'These settings affect the actual dimensions of images in player and staff profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-club-manager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' ),

			array(
				'title' => __( 'Staff Profile Image', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'staff_single_image_size',
				'css' 		=> '',
				'type' 		=> 'image_width',
				'default'	=> array(
					'width' 	=> '300',
					'height'	=> '300',
					'crop'		=> 1
				),
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Staff Thumbnails', 'wp-club-manager' ),
				'desc' 		=> '',
				'id' 		=> 'staff_thumbnail_image_size',
				'css' 		=> '',
				'type' 		=> 'image_width',
				'default'	=> array(
					'width' 	=> '90',
					'height'	=> '90',
					'crop'		=> 1
				),
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'image_options' ),


		)); // End team settings
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

return new WPCM_Settings_Staff();
