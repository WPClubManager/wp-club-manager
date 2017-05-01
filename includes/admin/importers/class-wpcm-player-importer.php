<?php
/**
 * Player importer - import players into WP Club Manager.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     1.2.11
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WP_Importer' ) ) {
	class WPCM_Player_Importer extends WPCM_Importer {

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page = 'wpclubmanager_player_csv';
			$this->import_label = __( 'Import Players', 'wp-club-manager' );
			$this->columns = array(
				'wpcm_number' => __( 'Number', 'wp-club-manager' ),
				'post_title' => __( 'Name', 'wp-club-manager' ),
				'wpcm_position' => __( 'Positions', 'wp-club-manager' ),
				'wpcm_team' => __( 'Teams', 'wp-club-manager' ),
				'wpcm_season' => __( 'Seasons', 'wp-club-manager' ),
				'wpcm_dob' => __( 'Date of Birth', 'wp-club-manager' ),
				'wpcm_height' => __( 'Height', 'wp-club-manager' ),
				'wpcm_weight' => __( 'Weight', 'wp-club-manager' ),
				'wpcm_hometown' => __( 'Birthplace', 'wp-club-manager' ),
				'wpcm_natl' => __( 'Nationality', 'wp-club-manager' ),
				'wpcm_prevclubs' => __( 'Previous Clubs', 'wp-club-manager' ),
			);
		}

		/**
		 * import function.
		 *
		 * @param mixed $file
		 */
		function import( $array = array(), $columns = array( 'post_title' ) ) {
			$this->imported = $this->skipped = 0;

			if ( ! is_array( $array ) || ! sizeof( $array ) ):
				$this->footer();
				die();
			endif;

			$rows = array_chunk( $array, sizeof( $columns ) );

			foreach ( $rows as $row ):

				$row = array_filter( $row );

				if ( empty( $row ) ) continue;

				$meta = array();

				foreach ( $columns as $index => $key ):
					$meta[ $key ] = wpcm_array_value( $row, $index );
				endforeach;

				$name = wpcm_array_value( $meta, 'post_title' );

				if ( ! $name ):
					$this->skipped++;
					continue;
				endif;

				$args = array( 'post_type' => 'wpcm_player', 'post_status' => 'publish', 'post_title' => $name );

				$id = wp_insert_post( $args );

				// Flag as import
				update_post_meta( $id, '_wpcm_import', 1 );

				// Update number
				update_post_meta( $id, 'wpcm_number', wpcm_array_value( $meta, 'wpcm_number' ) );

				// Update positions
				$positions = explode( '|', wpcm_array_value( $meta, 'wpcm_position' ) );
				wp_set_object_terms( $id, $positions, 'wpcm_position', false );

				// Update teams
				$teams = explode( '|', wpcm_array_value( $meta, 'wpcm_team' ) );
				wp_set_object_terms( $id, $teams, 'wpcm_team', false );

				// Update seasons
				$seasons = explode( '|', wpcm_array_value( $meta, 'wpcm_season' ) );
				wp_set_object_terms( $id, $seasons, 'wpcm_season', false );

				// Update date of birth
				update_post_meta( $id, 'wpcm_dob', wpcm_array_value( $meta, 'wpcm_dob' ) );

				// Update height
				update_post_meta( $id, 'wpcm_height', wpcm_array_value( $meta, 'wpcm_height' ) );

				// Update weight
				update_post_meta( $id, 'wpcm_weight', wpcm_array_value( $meta, 'wpcm_weight' ) );

				// Update hometown
				update_post_meta( $id, 'wpcm_hometown', wpcm_array_value( $meta, 'wpcm_hometown' ) );

				// Update nationality
				$natl = trim( strtolower( wpcm_array_value( $meta, 'wpcm_natl' ) ) );
				if ( $natl == '*' ) $natl = '';
				update_post_meta( $id, 'wpcm_natl', $natl );

				// Update previous clubs
				update_post_meta( $id, 'wpcm_prevclubs', wpcm_array_value( $meta, 'wpcm_prevclubs' ) );

				$this->imported++;

			endforeach;

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>
				'.sprintf( __( 'Import complete - imported <strong>%s</strong> players and skipped <strong>%s</strong>.', 'wp-club-manager' ), $this->imported, $this->skipped ).'
			</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . __( 'All done!', 'wp-club-manager' ) . ' <a href="' . admin_url('edit.php?post_type=wpcm_player') . '">' . __( 'View Players', 'wp-club-manager' ) . '</a></p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<h2>' . __( 'Import Players', 'wp-club-manager' ) . '</h2>';	
		}

		/**
		 * greet function.
		 */
		public function greet() {
			echo '<div class="narrow">';
			echo '<p>' . __( 'Choose a .csv file to upload, then click "Upload file and import".', 'wp-club-manager' ).'</p>';
			echo '<p>' . sprintf( __( 'Players need to be defined with columns in a specific order (11 columns). <a href="%s">Click here to download a sample</a>.', 'wp-club-manager' ), plugin_dir_url( WPCM_PLUGIN_FILE ) . 'dummy-data/player-sample.csv' ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wpclubmanager_player_csv&step=1' );
			echo '</div>';
		}
	}
}