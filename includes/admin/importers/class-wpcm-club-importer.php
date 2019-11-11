<?php
/**
 * Club importer - import clubs into WP Club Manager.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WP_Importer' ) ) {
	class WPCM_Club_Importer extends WPCM_Importer {

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page = 'wpclubmanager_club_csv';
			$this->import_label = __( 'Import Clubs', 'wp-club-manager' );
			$this->columns = array(
				'post_title' => __( 'Name', 'wp-club-manager' ),
				'wpcm_venue' => __( 'Venue', 'wp-club-manager' ),
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

				$args = array( 'post_type' => 'wpcm_club', 'post_status' => 'publish', 'post_title' => $name );

				$id = wp_insert_post( $args );

				// Flag as import
				update_post_meta( $id, '_wpcm_import', 1 );

				// Update venues
				$venues = explode( '|', wpcm_array_value( $meta, 'wpcm_venue' ) );
				wp_set_object_terms( $id, $venues, 'wpcm_venue', false );

				$this->imported++;

			endforeach;

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>
				'.sprintf( __( 'Import complete - imported <strong>%s</strong> clubs and skipped <strong>%s</strong>.', 'wp-club-manager' ), $this->imported, $this->skipped ).'
			</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . __( 'All done!', 'wp-club-manager' ) . ' <a href="' . admin_url('edit.php?post_type=wpcm_club') . '">' . __( 'View Clubs', 'wp-club-manager' ) . '</a></p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<h2>' . __( 'Import Clubs', 'wp-club-manager' ) . '</h2>';	
		}

		/**
		 * greet function.
		 */
		public function greet() {
			echo '<div class="narrow">';
			echo '<p>' . __( 'Choose a .csv file to upload, then click "Upload file and import".', 'wp-club-manager' ).'</p>';
			echo '<p>' . sprintf( __( 'Clubs need to be defined with columns in a specific order (2 columns). <a href="%s">Click here to download a sample</a>.', 'wp-club-manager' ), plugin_dir_url( WPCM_PLUGIN_FILE ) . 'dummy-data/club-sample.csv' ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wpclubmanager_club_csv&step=1' );
			echo '</div>';
		}
	}
}