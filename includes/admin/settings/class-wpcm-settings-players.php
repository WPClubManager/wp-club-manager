<?php
/**
 * WPClubManager Players Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Players' ) ) :

class WPCM_Settings_Players extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'players';
		$this->label = __( 'Players', 'wp-club-manager' );

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

			array( 'title' => __( 'Player Profile', 'wp-club-manager' ), 'type' => 'title', 'id' => 'players_options' ),

			array(
				'title' 	=> __( 'Display', 'wp-club-manager' ),
				'desc' 		=> __( 'Squad Number', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_number',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> 'start',
			),

			array(
				'desc' => __( 'Date of Birth', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_dob',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			),

			array(
				'desc' => __( 'Age', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_age',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			),

			array(
				'desc' => __( 'Height', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_height',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			),

			array(
				'desc' => __( 'Weight', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_weight',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			),

			array(
				'desc' => __( 'Season', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_season',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			)
		
		);

		if( is_club_mode() ) {
			$settings[] = array(
				'desc' => __( 'Team', 'wp-club-manager' ),
				'id' 		=> 'wpcm_player_profile_show_team',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> '',
			);
		}

		$settings[] = array(
			'desc' => __( 'Position', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_position',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Date Joined', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_joined',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Experience', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_exp',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Birthplace', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_hometown',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Nationality', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_nationality',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup'	=> '',
		);

		$settings[] = array(
			'desc' => __( 'Previous Clubs', 'wp-club-manager' ),
			'id' 		=> 'wpcm_player_profile_show_prevclubs',
			'default'	=> 'yes',
			'type' 		=> 'checkbox',
			'checkboxgroup'		=> 'end',
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'players_options');

		$settings[] = array( 'title' => __( 'Player Statistics', 'wp-club-manager' ), 'type' => 'title', 'desc' => __( '<p>Choose which player statistics to display. These can be overridden for individual players. Go to Settings &rarr; Matches to choose statistics to display for matches.</p>', 'wp-club-manager' ), 'id' => 'players_stats' );

		$stats_labels = array_merge( wpcm_get_appearance_and_subs_names(), wpcm_get_preset_labels( 'players', 'name' ) );
		
		$first = true;
		foreach ( $stats_labels as $key => $value ) {

			if( $first ) {
				$settings[] = array(
					'title' 	=> __( 'Display', 'wp-club-manager' ),
					'desc' 		=> strip_tags($value),
					'id' 		=> 'wpcm_show_stats_'. $key,
					'default'	=> 'yes',
					'type' 		=> 'checkbox',
					'checkboxgroup' => 'start',
				);
				$first = false;
			} else {

				$settings[] = array(
					'desc' 	=> strip_tags($value),
					'id' 		=> 'wpcm_show_stats_'. $key,
					'default'	=> 'yes',
					'type' 		=> 'checkbox',
					'checkboxgroup' => '',
				);
			}							
		}

		$settings[] = array( 'type' => 'sectionend', 'id' => 'players_stats');

		$settings[] = array(	'title' => __( 'Player Image Sizes', 'wp-club-manager' ), 'type' => 'title','desc' => sprintf(__( '<p>These settings affect the actual dimensions of images in player and staff profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.</p>', 'wp-club-manager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' );
		
		$settings[] = array(
			'title' => __( 'Player Profile Image', 'wp-club-manager' ),
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
		);

		$settings[] = array(
			'title' => __( 'Player Thumbnails', 'wp-club-manager' ),
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
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'image_options');

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
