<?php
/**
 * WPClubManager Players Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Players' ) ) :

class WPCM_Settings_Players extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'players';
		$this->label = __( 'Players', 'wpclubmanager' );

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

			array( 'title' => __( 'Player Profile Options', 'wpclubmanager' ), 'type' => 'title', 'desc' => __( '<p>Choose which fields to display on player profile pages.</p>', 'wpclubmanager' ), 'id' => 'players_options' ),

			array(
				'title' => __( 'Number', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_number',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Date of Birth', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_dob',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Age', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_age',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Height', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_height',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Weight', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_weight',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Season', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_season',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Team', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_team',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Position', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_position',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Date Joined', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_joined',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Experience', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_exp',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Birthplace', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_hometown',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Previous Clubs', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_prevclubs',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array( 'type' => 'sectionend', 'id' => 'players_options'),

			array(	'title' => __( 'Player Image Sizes', 'wpclubmanager' ), 'type' => 'title','desc' => sprintf(__( '<p>These settings affect the actual dimensions of images in player and staff profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.</p>', 'wpclubmanager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' ),

			array(
				'title' => __( 'Player Profile Image', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'player_single_image_size',
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
				'title' => __( 'Player Thumbnails', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'player_thumbnail_image_size',
				'css' 		=> '',
				'type' 		=> 'image_width',
				'default'	=> array(
					'width' 	=> '25',
					'height'	=> '25',
					'crop'		=> 1
				),
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'image_options'),

			array( 'title' => __( 'Display Player Stats', 'wpclubmanager' ), 'type' => 'title', 'desc' => __( '<p>Choose which player stats to display throughout the site.</p>', 'wpclubmanager' ), 'id' => 'players_stats' ),
		);

		$wpcm_player_stats_labels = wpcm_get_sports_stats_labels();

		$stats_labels = array( 'appearances' => '<a title="' . __('Games Played', 'wpclubmanager') . '">' . __( 'GP', 'wpclubmanager' ) . '</a>' );
		$stats_labels = array_merge( $stats_labels, $wpcm_player_stats_labels );

		foreach ( $stats_labels as $key => $value ) {

			$settings[] = array(
				'title' => __( strip_tags($value), 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_show_stats_'. $key,
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			);
										
		}

		$settings[] = array( 'type' => 'sectionend', 'id' => 'players_stats');

		return apply_filters( 'wpclubmanager_players_settings', $settings );

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

return new WPCM_Settings_Players();
