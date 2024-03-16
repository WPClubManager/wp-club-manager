<?php
/**
 * Staff importer - import staff into WP Club Manager.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     2.2.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WP_Importer' ) ) {

	/**
	 * WPCM_Staff_Importer
	 */
	class WPCM_Staff_Importer extends WPCM_Importer {

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page  = 'wpclubmanager_staff_csv';
			$this->import_label = __( 'Import Staff', 'wp-club-manager' );
			$this->columns      = array(
				'wpcm_first_name' => __( 'First Name', 'wp-club-manager' ),
				'wpcm_last_name'  => __( 'Last Name', 'wp-club-manager' ),
				'wpcm_jobs'       => __( 'Jobs', 'wp-club-manager' ),
				'wpcm_dob'        => __( 'Date of Birth', 'wp-club-manager' ),
				'wpcm_natl'       => __( 'Nationality', 'wp-club-manager' ),
				'wpcm_email'      => __( 'Email', 'wp-club-manager' ),
				'wpcm_phone'      => __( 'Phone', 'wp-club-manager' ),
			);
		}

		/**
		 * import function.
		 *
		 * @param array $array
		 * @param array $columns
		 */
		public function import( $array = array(), $columns = array() ) {
			$this->imported = 0;
			$this->skipped  = 0;

			if ( ! is_array( $array ) || ! count( $array ) ) :
				$this->footer();
				die();
			endif;

			$rows = array_chunk( $array, count( $columns ) );

			foreach ( $rows as $row ) :

				$row = array_filter( $row );

				if ( empty( $row ) ) {
					continue;
				}

				$meta = array();

				foreach ( $columns as $index => $key ) :
					$meta[ $key ] = wpcm_array_value( $row, $index );
				endforeach;

				$name = wpcm_array_value( $meta, 'wpcm_name' );

				if ( ! $name ) :
					++$this->skipped;
					continue;
				endif;

				$args = array(
					'post_type'   => 'wpcm_staff',
					'post_status' => 'publish',
					'post_title'  => $name,
				);

				$id = wp_insert_post( $args );

				// Flag as import
				update_post_meta( $id, '_wpcm_import', 1 );

				$parts = explode( ' ', $name );
				$lname = array_pop( $parts );
				$fname = implode( ' ', $parts );

				// Update first name
				update_post_meta( $id, '_wpcm_firstname', $fname );

				// Update last name
				update_post_meta( $id, '_wpcm_lastname', $lname );

				// Update positions
				$jobs = explode( '|', wpcm_array_value( $meta, 'wpcm_jobs' ) );
				wp_set_object_terms( $id, $jobs, 'wpcm_jobs', false );

				// Update date of birth
				update_post_meta( $id, 'wpcm_dob', wpcm_array_value( $meta, 'wpcm_dob' ) );

				// Update nationality
				$natl = trim( strtolower( wpcm_array_value( $meta, 'wpcm_natl' ) ) );
				if ( '*' === $natl ) {
					$natl = '';
				}
				update_post_meta( $id, 'wpcm_natl', $natl );

				// Update email
				update_post_meta( $id, '_wpcm_staff_email', wpcm_array_value( $meta, 'wpcm_email' ) );

				// Update phone
				update_post_meta( $id, '_wpcm_staff_phone', wpcm_array_value( $meta, 'wpcm_phone' ) );

				++$this->imported;

			endforeach;

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>';
			/* translators: 1: imported total 2: skipped total */
			echo wp_kses_post( sprintf( __( 'Import complete - imported <strong>%1$s</strong> staff and skipped <strong>%2$s</strong>.', 'wp-club-manager' ), $this->imported, $this->skipped ) );
			echo '</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . esc_html__( 'All done!', 'wp-club-manager' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=wpcm_staff' ) ) . '">' . esc_html__( 'View Staff', 'wp-club-manager' ) . '</a></p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<h2>' . esc_html__( 'Import Staff', 'wp-club-manager' ) . '</h2>';
		}

		/**
		 * greet function.
		 */
		public function greet() {
			echo '<div class="narrow">';
			echo '<p>' . esc_html__( 'Choose a .csv file to upload, then click "Upload file and import".', 'wp-club-manager' ) . '</p>';
			/* translators: 1: sample data URL */
			echo '<p>' . wp_kses_post( sprintf( __( 'Staff need to be defined with columns in a specific order (7 columns). <a href="%s">Click here to download a sample</a>.', 'wp-club-manager' ), esc_url( plugin_dir_url( WPCM_PLUGIN_FILE ) . 'dummy-data/staff-sample.csv' ) ) ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wpclubmanager_staff_csv&step=1' );
			echo '</div>';
		}
	}
}
