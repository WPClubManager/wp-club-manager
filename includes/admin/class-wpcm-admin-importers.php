<?php
/**
 * Setup importers for WPCM data.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.2.11
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Importers' ) ) :

/**
 * WPCM_Admin_Importers Class
 */
class WPCM_Admin_Importers {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_importers' ) );
	}

	/**
	 * Add menu items
	 */
	public function register_importers() {
		register_importer( 'wpclubmanager_player_csv', __( 'WPCM Players', 'wp-club-manager' ), __( 'Import <strong>players</strong> to your club via a csv file.', 'wp-club-manager'), array( $this, 'player_importer' ) );
		register_importer( 'wpclubmanager_staff_csv', __( 'WPCM Staff', 'wp-club-manager' ), __( 'Import <strong>staff</strong> to your club via a csv file.', 'wp-club-manager'), array( $this, 'staff_importer' ) );
		register_importer( 'wpclubmanager_club_csv', __( 'WPCM Clubs', 'wp-club-manager' ), __( 'Import <strong>clubs</strong> via a csv file.', 'wp-club-manager'), array( $this, 'club_importer' ) );
		register_importer( 'wpclubmanager_match_csv', __( 'WPCM Matches', 'wp-club-manager' ), __( 'Import <strong>matches</strong> via a csv file.', 'wp-club-manager'), array( $this, 'match_importer' ) );
	}

	/**
	 * Add importers
	 */
	public function player_importer() {
		$this->includes();

		// includes
		require 'importers/class-wpcm-player-importer.php';

		// Dispatch
		$importer = new WPCM_Player_Importer();
		$importer->dispatch();
	}

	public function staff_importer() {
		$this->includes();

		// includes
		require 'importers/class-wpcm-staff-importer.php';

		// Dispatch
		$importer = new WPCM_Staff_Importer();
		$importer->dispatch();
	}

	public function club_importer() {
		$this->includes();

		// includes
		require 'importers/class-wpcm-club-importer.php';

		// Dispatch
		$importer = new WPCM_Club_Importer();
		$importer->dispatch();
	}

	public function match_importer() {
		$this->includes();

		// includes
		require 'importers/class-wpcm-match-importer.php';

		// Dispatch
		$importer = new WPCM_Match_Importer();
		$importer->dispatch();
	}

	public static function includes() {
		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) )
				require $class_wp_importer;
		}

		// includes
		require 'importers/class-wpcm-importers.php';
	}

}

endif;

return new WPCM_Admin_Importers();
