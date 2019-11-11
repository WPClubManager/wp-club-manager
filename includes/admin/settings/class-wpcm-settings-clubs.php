<?php
/**
 * WPClubManager Clubs Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Clubs' ) ) :

class WPCM_Settings_Clubs extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'clubs';
		$this->label = __( 'Clubs', 'wp-club-manager' );

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

			array( 'title' => __( 'Club Profile', 'wp-club-manager' ), 'type' => 'title', 'id' => 'staff_options' ),

			array(
				'title' 	=> __( 'Display', 'wp-club-manager' ),
				'desc' 		=> __( 'Formed', 'wp-club-manager' ),
				'id' 		=> 'wpcm_club_settings_formed',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup' => 'start'
			),

		    array(
                'desc' => __( 'Colors', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_colors',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup'	=> '',
            ),

            array(
                'desc' => __( 'Honours', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_honors',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup' => ''
            ),

            array(
                'desc' => __( 'Website', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_website',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup' => ''
            ),

            array(
                'desc' => __( 'Venue Map', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_venue',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup' => ''
            ),

            array(
                'desc' => __( 'Match Stats', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_h2h',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup' => ''
            ),

            array(
                'desc' => __( 'Matches List', 'wp-club-manager' ),
                'id' 		=> 'wpcm_club_settings_matches',
                'default'	=> 'yes',
                'type' 		=> 'checkbox',
                'checkboxgroup' => 'end'
            ),

            array( 'type' => 'sectionend', 'id' => 'staff_options'),

            array(	
                'title' => __( 'Club Badge Sizes', 'wp-club-manager' ),
                'type' => 'title',
                'desc' => sprintf(__( 'These settings affect the actual dimensions of images in club profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-club-manager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'),
                'id' => 'image_options'
            ),

            array(
                'title' => __( 'Club Badge Image', 'wp-club-manager' ),
                'desc' 		=> '',
                'id' 		=> 'wpcm_club_settings_image',
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
                'title' => __( 'Club Badge Thumbnails', 'wp-club-manager' ),
                'desc' 		=> '',
                'id' 		=> 'wpcm_club_settings_thumbnail',
                'css' 		=> '',
                'type' 		=> 'image_width',
                'default'	=> array(
                    'width' 	=> '90',
                    'height'	=> '90',
                    'crop'		=> 1
                ),
                'desc_tip'	=>  true,
            ),

            array( 'type' => 'sectionend', 'id' => 'image_options' )
        
        
        );

		return apply_filters( 'wpclubmanager_club_settings', $settings );

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

return new WPCM_Settings_Clubs();
