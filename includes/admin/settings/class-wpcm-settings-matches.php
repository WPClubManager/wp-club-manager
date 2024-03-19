<?php
/**
 * WPClubManager Matches Settings
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.1.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPCM_Settings_Matches' ) ) :

	/**
	 * WPCM_Settings_Matches
	 */
	class WPCM_Settings_Matches extends WPCM_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'matches';
			$this->label = __( 'Matches', 'wp-club-manager' );

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

				array(
					'title' => __( 'Match Settings', 'wp-club-manager' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'match_format_options',
				),

				array(
					'title'   => __( 'Title format', 'wp-club-manager' ),
					'desc'    => '',
					'id'      => 'wpcm_match_title_format',
					'default' => '%home% vs %away%',
					'type'    => 'radio',
					'options' => array(
						'%home% vs %away%' => __( 'Default - <i>Home v Away</i>', 'wp-club-manager' ),
						'%away% vs %home%' => __( 'Reverse - <i>Away v Home</i>', 'wp-club-manager' ),
					),
				),

				array(
					'title'   => __( 'Clubs Separator', 'wp-club-manager' ),
					'id'      => 'wpcm_match_clubs_separator',
					'css'     => 'width:50px;',
					'default' => 'v',
					'type'    => 'text',
				),

				array(
					'title'   => __( 'Goals Delimiter', 'wp-club-manager' ),
					'id'      => 'wpcm_match_goals_delimiter',
					'css'     => 'width:50px;',
					'default' => '-',
					'type'    => 'text',
				),

				array(
					'title'   => __( 'Start Time', 'wp-club-manager' ),
					'id'      => 'wpcm_match_time',
					'css'     => 'width:70px;',
					'type'    => 'text',
					'class'   => 'wpcm-default-time-picker',
					'default' => '15:00',
				),

				array(
					'title'   => __( 'Match Duration', 'wp-club-manager' ),
					'id'      => 'wpcm_match_duration',
					'css'     => 'width:50px;',
					'default' => '90',
					'type'    => 'text',
					'desc'    => __( 'minutes', 'wp-club-manager' ),
				),

				array(
					'title'   => __( 'Box Scores', 'wp-club-manager' ),
					'desc'    => __( 'Check this box to activate an interval score summary.', 'wp-club-manager' ),
					'id'      => 'wpcm_match_box_scores',
					'default' => 'no',
					'type'    => 'checkbox',
				),

				array(
					'title'   => __( 'Hide Scores', 'wp-club-manager' ),
					'desc'    => __( 'Check this box to hide scores from guests.', 'wp-club-manager' ),
					'id'      => 'wpcm_hide_scores',
					'default' => 'no',
					'type'    => 'checkbox',
				),

				array(
					'title'         => __( 'Display', 'wp-club-manager' ),
					'desc'          => __( 'Attendance', 'wp-club-manager' ),
					'id'            => 'wpcm_results_show_attendance',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),
			);

			if ( get_option( 'wpcm_mode', 'club' ) == 'club' ) {
				$settings[] = array(
					'desc'          => __( 'Team', 'wp-club-manager' ),
					'id'            => 'wpcm_results_show_team',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				);
			}

			$settings[] = array(
				'desc'          => __( 'Referee', 'wp-club-manager' ),
				'id'            => 'wpcm_results_show_referee',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			);

			$settings[] = array(
				'desc'          => __( 'Preview', 'wp-club-manager' ),
				'id'            => 'wpcm_match_show_preview',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			);

			$settings[] = array(
				'desc'          => __( 'Report', 'wp-club-manager' ),
				'id'            => 'wpcm_match_show_report',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			);

			$settings[] = array(
				'desc'          => __( 'Venue Map', 'wp-club-manager' ),
				'id'            => 'wpcm_results_show_map',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			);

			$settings[] = array(
				'desc'          => __( 'Lineup', 'wp-club-manager' ),
				'id'            => 'wpcm_match_show_lineup',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			);

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'match_format_options',
			);

			$settings[] = array(
				'title' => __( 'Match Lineup', 'wp-club-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'match_lineup_display_options',
			);

			$settings[] = array(
				'title'   => __( 'Name Format', 'wp-club-manager' ),
				'id'      => 'wpcm_name_format',
				'class'   => 'chosen_select',
				'default' => 'full',
				'type'    => 'select',
				'options' => array(
					'full'    => __( 'First Last', 'wp-club-manager' ),
					'last'    => __( 'Last', 'wp-club-manager' ),
					'initial' => __( 'F. Last', 'wp-club-manager' ),
				),
			);

			$settings[] = array(
				'title'         => __( 'Columns', 'wp-club-manager' ),
				'desc'          => __( 'Shirt Numbers', 'wp-club-manager' ),
				'id'            => 'wpcm_lineup_show_shirt_numbers',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			);

			$settings[] = array(
				'desc'          => __( 'Player Images', 'wp-club-manager' ),
				'id'            => 'wpcm_results_show_image',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			);

			$stats_labels = wpcm_get_preset_labels( 'players', 'name' );

			foreach ( $stats_labels as $key => $value ) {

				if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :

					$settings[] = array(
						'desc'          => strip_tags( $value ),
						'id'            => 'wpcm_match_show_stats_' . $key,
						'default'       => 'yes',
						'type'          => 'checkbox',
						'checkboxgroup' => '',
					);

				endif;

			}

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'match_lineup_display_options',
			);

			return apply_filters( 'wpclubmanager_matches_settings', $settings );
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

return new WPCM_Settings_Matches();
