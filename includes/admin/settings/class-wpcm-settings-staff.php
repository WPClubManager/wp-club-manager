<?php
/**
 * WPClubManager Staff Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
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
		
		$settings = array(

			array( 'title' => __( 'Staff Profile', 'wp-club-manager' ), 'type' => 'title', 'id' => 'staff_options' ),

			array(
				'title' 	=> __( 'Display', 'wp-club-manager' ),
				'desc' 		=> __( 'Date of Birth', 'wp-club-manager' ),
				'id' 		=> 'wpcm_staff_profile_show_dob',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup' => 'start'
			),

			array(
				'desc' => __( 'Age', 'wp-club-manager' ),
				'id' 		=> 'wpcm_staff_profile_show_age',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup' => ''
			),

			array(
				'desc' => __( 'Season', 'wp-club-manager' ),
				'id' 		=> 'wpcm_staff_profile_show_season',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup' => ''
			)
		
		);

		if( is_club_mode() ) {
			$settings[] = array(
				'desc' => __( 'Team', 'wp-club-manager' ),
				'id' 		=> 'wpcm_staff_profile_show_team',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup' => ''
			);
		}

		$settings[] = array(
			'desc' => __( 'Job Title', 'wp-club-manager' ),
			'id' 		=> 'wpcm_staff_profile_show_jobs',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup' => ''
		);

		$settings[] = array(
			'desc' => __( 'Email', 'wp-club-manager' ),
			'id' 		=> 'wpcm_show_staff_email',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'checkboxgroup' => ''
		);

		$settings[] = array(
			'desc' => __( 'Phone', 'wp-club-manager' ),
			'id' 		=> 'wpcm_show_staff_phone',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'checkboxgroup' => ''
		);

		$settings[] = array(
			'desc' => __( 'Date Joined', 'wp-club-manager' ),
			'id' 		=> 'wpcm_staff_profile_show_joined',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup' => ''
		);

		$settings[] = array(
			'desc' => __( 'Experience', 'wp-club-manager' ),
			'id' 		=> 'wpcm_staff_profile_show_exp',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Birthplace', 'wp-club-manager' ),
			'id' 		=> 'wpcm_staff_profile_show_hometown',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Nationality', 'wp-club-manager' ),
			'id' 		=> 'wpcm_staff_profile_show_nationality',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup' => 'end'
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'staff_options');

		$settings[] = array(	'title' => __( 'Staff Image Sizes', 'wp-club-manager' ), 'type' => 'title','desc' => sprintf(__( 'These settings affect the actual dimensions of images in player and staff profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-club-manager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' );

		$settings[] = array(
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
		);

		$settings[] = array(
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
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'image_options' );

		return apply_filters( 'wpclubmanager_staff_settings', $settings );

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
