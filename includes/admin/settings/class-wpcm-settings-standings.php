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
		$this->label = __( 'League Tables', 'wp-club-manager' );

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

			array( 'title' => __( 'League Table Points', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'points_options' ),

			array(
				'title' => __( 'Points for win', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_win_points',
				'css' 		=> 'width:50px;',
				'default'	=> '3',
				'type' 		=> 'number'
			),

			array(
				'title' => __( 'Points for draw', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_draw_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number'
			),

			array(
				'title' => __( 'Points for loss', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_loss_points',
				'css' 		=> 'width:50px;',
				'default'	=> '0',
				'type' 		=> 'number'
			)
		);

		$sport = get_option('wpcm_sport');
		if( $sport == 'hockey' || $sport == 'basketball' ){

			$settings[] = array(
				'title' => __( 'Points for overtime win', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_otw_points',
				'css' 		=> 'width:50px;',
				'default'	=> '0',
				'type' 		=> 'number'
			);

			$settings[] = array(
				'title' => __( 'Points for overtime loss', 'wp-club-manager' ),
				'id' 		=> 'wpcm_standings_otl_points',
				'css' 		=> 'width:50px;',
				'default'	=> '1',
				'type' 		=> 'number'
			);

		}

		$settings[] = array( 'type' => 'sectionend', 'id' => 'points_options');

		$settings[] = array( 'title' => __( 'League Table Display', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'display_options' );

		$settings[] = array(
			'title' 	=> __( 'Order', 'wp-club-manager' ),
			'id' 		=> 'wpcm_standings_order',
			'class'     => 'chosen_select',
			'default'	=> 'DESC',
			'type' 		=> 'select',
			'options' => array(
				'DESC'  => __( 'Highest to lowest', 'wp-club-manager' ),
				'ASC'	=> __( 'Lowest to highest', 'wp-club-manager' ),
			),
			'desc_tip'	=>  false
		);

		$settings[] = array(
			'title' => __( 'Columns', 'wp-club-manager' ),
			'id' 		=> 'wpcm_standings_columns_display',
			'type' 		=> 'standings_columns'
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'display_options');

		$settings[] = array( 'title' => __( 'League Table Sorting', 'wp-club-manager' ), 'type' => 'title', 'desc' => '', 'id' => 'sorting_options' );

		$stats_names = wpcm_get_preset_labels( 'standings', 'name' );
		//$options = array_merge( $stats_names, array( 'compare' => __('Head-to-head', 'wpclubmanager' ) ) );
		$options = $stats_names;
		
		$settings[] = array(
			'title' 	=> __( 'Priority 1', 'wp-club-manager' ),
			'id' 		=> 'wpcm_standings_orderby',
			'class'     => 'chosen_select',
			'default'	=> 'pts',
			'type' 		=> 'select',
			'options'   => $options,
			'desc_tip'	=>  false
		);

		$settings[] = array(
			'id' 		=> 'wpcm_standings_priority_order',
			'class'     => '',
			'default'	=> 'DESC',
			'type' 		=> 'radio',
			'options' => array(
				'DESC'  => __( 'DESC', 'wp-club-manager' ),
				'ASC'	=> __( 'ASC', 'wp-club-manager' ),
			)
		);

		$settings[] = array(
			'title' 	=> __( 'Priority 2', 'wp-club-manager' ),
			'id' 		=> 'wpcm_standings_orderby_2',
			'class'     => 'chosen_select',
			'default'	=> 'gd',
			'type' 		=> 'select',
			'options'   => $options,
			'desc_tip'	=>  false
		);

		$settings[] = array(
			'id' 		=> 'wpcm_standings_priority_order_2',
			'class'     => '',
			'default'	=> 'DESC',
			'type' 		=> 'radio',
			'options' => array(
				'DESC'  => __( 'DESC', 'wp-club-manager' ),
				'ASC'	=> __( 'ASC', 'wp-club-manager' ),
			)
		);

		$settings[] = array(
			'title' 	=> __( 'Priority 3', 'wp-club-manager' ),
			'id' 		=> 'wpcm_standings_orderby_3',
			'class'     => 'chosen_select',
			'default'	=> 'f',
			'type' 		=> 'select',
			'options'   => $options,
			'desc_tip'	=>  false
		);

		$settings[] = array(
			'id' 		=> 'wpcm_standings_priority_order_3',
			'class'     => '',
			'default'	=> 'DESC',
			'type' 		=> 'radio',
			'options' => array(
				'DESC'  => __( 'DESC', 'wp-club-manager' ),
				'ASC'	=> __( 'ASC', 'wp-club-manager' ),
			)
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'sorting_options');

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
