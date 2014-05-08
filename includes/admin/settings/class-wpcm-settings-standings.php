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
		$this->label = __( 'Standings', 'wpclubmanager' );

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
		
		return apply_filters( 'wpclubmanager_standings_settings', array(

			array( 'title' => __( 'Scoring System', 'wpclubmanager' ), 'type' => 'title', 'desc' => '', 'id' => 'scoring_options' ),

			array(
				'title' => __( 'Points for win', 'wpclubmanager' ),
				'desc' 		=> __( 'This sets the points for won matches.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_standings_win_points',
				'css' 		=> 'width:50px;',
				'default'	=> '3',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Points for draw', 'wpclubmanager' ),
				'desc' 		=> __( 'This sets the points for drawn matches.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_standings_draw_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Points for overtime loss', 'wpclubmanager' ),
				'desc' 		=> __( 'This sets the points for lost in overtime matches. Often used in Ice Hockey', 'wpclubmanager' ),
				'id' 		=> 'wpcm_standings_otl_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Points for loss', 'wpclubmanager' ),
				'desc' 		=> __( 'This sets the points for lost matches.', 'wpclubmanager' ),
				'id' 		=> 'wpcm_standings_loss_points',
				'css' 		=> 'width:50px;',
				'default'	=> '0',
				'type' 		=> 'number',
				'desc_tip'	=>  true,
			),

			array( 'type' => 'sectionend', 'id' => 'scoring_options'),

			array( 'title' => __( 'Standings Labels', 'wpclubmanager' ), 'type' => 'title', 'desc' => '', 'id' => 'standings_labels_options' ),

			array(
				'title' => __( 'Position', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_pos_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'Pos',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Played', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_p_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'P',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Win', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_w_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'W',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Draw', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_d_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'D',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Lost', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_l_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'L',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Lost in Overtime', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_otl_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'OTL',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Win Percentage', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_pct_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'PCT',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Goals For', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_f_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'F',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Goals Against', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_a_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'A',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Goal Difference', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_gd_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'GD',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Bonus Points', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_bonus_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'B',
				'type' 		=> 'text'
			),

			array(
				'title' => __( 'Points', 'wpclubmanager' ),
				'desc' 		=> '',
				'id' 		=> 'wpcm_standings_pts_label',
				'css' 		=> 'width:150px;',
				'default'	=> 'Pts',
				'type' 		=> 'text'
			),

			array( 'type' => 'sectionend', 'id' => 'standings_labels_options'),

		)); // End standings settings
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
