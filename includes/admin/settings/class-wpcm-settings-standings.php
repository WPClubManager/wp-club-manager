<?php
/**
 * WPClubManager Standings Settings
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Settings_Standings' ) ) :

class WPCM_Settings_Standings extends WPCM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'standings';
		$this->label = __( 'Standings', 'wp-club-manager' );

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

			array( 'title' => __( 'Scoring System', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'scoring_options' ),

			array(
				'title' => __( 'Points for win', 'wp-club-manager' ),
				'desc' 		=> __( 'This sets the points for won matches.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_win_points',
				'css' 		=> 'width:50px;',
				'default'	=> '3',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Points for draw', 'wp-club-manager' ),
				'desc' 		=> __( 'This sets the points for drawn matches.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_draw_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Points for loss', 'wp-club-manager' ),
				'desc' 		=> __( 'This sets the points for lost matches.', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_loss_points',
				'css' 		=> 'width:50px;',
				'default'	=> '0',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			)
		);

		$sport = get_option('wpcm_sport');
		if( $sport == 'hockey' || $sport == 'basketball' ){

			$settings[] = array(
				'title' => __( 'Points for overtime win', 'wp-club-manager' ),
				'desc' 		=> __( 'This sets the points for win in overtime matches. Often used in Ice Hockey', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_otw_points',
				'css' 		=> 'width:50px;',
				'default'	=> '0',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			);

			$settings[] = array(
				'title' => __( 'Points for overtime loss', 'wp-club-manager' ),
				'desc' 		=> __( 'This sets the points for lost in overtime matches. Often used in Ice Hockey', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_otl_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			);

		}

			//array( 'type' => 'sectionend', 'id' => 'scoring_options'),

			// array( 'title' => __( 'Standings Labels', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'standings_labels_options' ),

			// array(
			// 	'title' => __( 'Position', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_pos_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'Pos',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Played', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_p_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'P',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Win', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_w_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'W',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Draw', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_d_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'D',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Lost', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_l_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'L',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Won in Overtime', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_otw_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'OTW',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Lost in Overtime', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_otl_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'OTL',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Win Percentage', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_pct_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'PCT',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Goals For', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_f_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'F',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Goals Against', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_a_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'A',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Goal Difference', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_gd_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'GD',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Bonus Points', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_bonus_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'B',
			// 	'type' 		=> 'text'
			// ),

			// array(
			// 	'title' => __( 'Points', 'wp-club-manager' ),
			// 	'desc' 		=> '',
			// 	'id' 		=> 'wpcm_standings_pts_label',
			// 	'css' 		=> 'width:150px;',
			// 	'default'	=> 'Pts',
			// 	'type' 		=> 'text'
			// ),

			// array( 'type' => 'sectionend', 'id' => 'standings_labels_options'),

		//)); // End standings settings

		$settings[] = array( 'type' => 'sectionend', 'id' => 'scoring_options');

		return apply_filters( 'wpclubmanager_standings_settings', $settings );
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

return new WPCM_Settings_Standings();
