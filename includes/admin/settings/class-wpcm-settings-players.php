<?php
/**
 * WPClubManager Players Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
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
		
		return apply_filters( 'wpclubmanager_players_settings', array(

			array( 'title' => __( 'Player Profile Options', 'wpclubmanager' ), 'type' => 'title', 'desc' => __( 'Choose which fields to display on player profile pages.', 'wpclubmanager' ), 'id' => 'players_options' ),

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
				'title' => __( 'Appearances', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_appearances',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Goals', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_goals',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Assists', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_assists',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Yellow Cards', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_yellowcards',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Red Cards', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_redcards',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Av. Rating', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_ratings',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Player of the Match Awards', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_mvp',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Date Joined', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_player_profile_show_joined',
				'default'	=> 'yes',
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

			array(	'title' => __( 'Player Image Sizes', 'wpclubmanager' ), 'type' => 'title','desc' => sprintf(__( 'These settings affect the actual dimensions of images in player and staff profiles - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wpclubmanager' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' ),

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
					'width' 	=> '90',
					'height'	=> '90',
					'crop'		=> 1
				),
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'image_options'),

			array( 'title' => __( 'Player Stat Labels', 'wpclubmanager' ), 'type' => 'title', 'desc' => '', 'id' => 'player_stat_label_options' ),

			array(
				'title' => __( 'Goals', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players goals.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_goals_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Goals',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Assists', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players assists.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_assists_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Assists',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Yellow Cards', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players yellow cards.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_yellowcards_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Yellow Cards',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Red Cards', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players red cards.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_redcards_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Red Cards',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Rating', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players ratings.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_rating_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Rating',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Av. Rating', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the players average ratings.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_ratings_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Av. Rating',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Player of the Match', 'wpclubmanager' ),
				'desc' 		=> __( 'The label to display the player of the match awards.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_player_mvp_label',
				'css' 		=> 'width:250px;',
				'default'	=> 'Player of the Match',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'player_stat_label_options'),


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

return new WPCM_Settings_Players();
