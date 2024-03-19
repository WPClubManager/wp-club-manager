<?php
/**
 * Player importer - import players into WP Club Manager.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     2.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WP_Importer' ) ) {

	/**
	 * WPCM_Player_Importer
	 */
	class WPCM_Player_Importer extends WPCM_Importer {

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page  = 'wpclubmanager_player_csv';
			$this->import_label = __( 'Import Players', 'wp-club-manager' );
			$this->columns      = array(
				'wpcm_number'     => __( 'Number', 'wp-club-manager' ),
				'_wpcm_firstname' => __( 'First Name', 'wp-club-manager' ),
				'_wpcm_lastname'  => __( 'Last Name', 'wp-club-manager' ),
				'wpcm_position'   => __( 'Positions', 'wp-club-manager' ),
				'wpcm_dob'        => __( 'Date of Birth', 'wp-club-manager' ),
				'wpcm_height'     => __( 'Height', 'wp-club-manager' ),
				'wpcm_weight'     => __( 'Weight', 'wp-club-manager' ),
				'wpcm_hometown'   => __( 'Birthplace', 'wp-club-manager' ),
				'wpcm_natl'       => __( 'Nationality', 'wp-club-manager' ),
				'wpcm_prevclubs'  => __( 'Previous Clubs', 'wp-club-manager' ),
			);
		}

		/**
		 * import function.
		 *
		 * @param array $array
		 * @param array $columns
		 */
		public function import( $array = array(), $columns = array( 'post_title' ) ) {
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

				$first_name = sanitize_text_field( wpcm_array_value( $meta, '_wpcm_firstname' ) );
				$last_name  = sanitize_text_field( wpcm_array_value( $meta, '_wpcm_lastname' ) );
				$name       = $first_name . ' ' . $last_name;
				$post_name  = sanitize_title_with_dashes( $name );

				if ( ! $name ) :
					++$this->skipped;
					continue;
				endif;

				$args = array(
					'post_type'   => 'wpcm_player',
					'post_status' => 'publish',
					'post_title'  => $name,
					'post_name'   => $post_name,
				);

				$id = wp_insert_post( $args );

				// $post_name = sanitize_title_with_dashes( $name );

				// wp_update_post( array( 'ID' => $id, 'post_name' => $post_name, 'post_title' => $name ) );

				// Flag as import
				update_post_meta( $id, '_wpcm_import', 1 );

				// Update number
				update_post_meta( $id, 'wpcm_number', wpcm_array_value( $meta, 'wpcm_number' ) );

				// Update first name
				update_post_meta( $id, '_wpcm_firstname', $first_name );

				// Update last name
				update_post_meta( $id, '_wpcm_lastname', $last_name );

				// Update positions
				$positions = explode( '|', wpcm_array_value( $meta, 'wpcm_position' ) );
				wp_set_object_terms( $id, $positions, 'wpcm_position', false );

				// Update date of birth
				update_post_meta( $id, 'wpcm_dob', sanitize_text_field( wpcm_array_value( $meta, 'wpcm_dob' ) ) );

				// Update height
				update_post_meta( $id, 'wpcm_height', sanitize_text_field( wpcm_array_value( $meta, 'wpcm_height' ) ) );

				// Update weight
				update_post_meta( $id, 'wpcm_weight', sanitize_text_field( wpcm_array_value( $meta, 'wpcm_weight' ) ) );

				// Update hometown
				update_post_meta( $id, 'wpcm_hometown', sanitize_text_field( wpcm_array_value( $meta, 'wpcm_hometown' ) ) );

				// Update nationality
				$natl = trim( strtolower( wpcm_array_value( $meta, 'wpcm_natl' ) ) );
				if ( '*' === $natl ) {
					$natl = '';
				}
				update_post_meta( $id, 'wpcm_natl', sanitize_text_field( $natl ) );

				// Update previous clubs
				update_post_meta( $id, 'wpcm_prevclubs', sanitize_text_field( wpcm_array_value( $meta, 'wpcm_prevclubs' ) ) );

				++$this->imported;

			endforeach;

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>';
			/* translators: 1: imported total 2: skipped total */
			echo wp_kses_post( sprintf( __( 'Import complete - imported <strong>%1$s</strong> players and skipped <strong>%2$s</strong>.', 'wp-club-manager' ), $this->imported, $this->skipped ) );
			echo '</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . esc_html__( 'All done!', 'wp-club-manager' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=wpcm_player' ) ) . '">' . esc_html__( 'View Players', 'wp-club-manager' ) . '</a></p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<h2>' . esc_html__( 'Import Players', 'wp-club-manager' ) . '</h2>';
		}

		/**
		 * greet function.
		 */
		public function greet() {
			echo '<div class="narrow">';
			echo '<p>' . esc_html__( 'Choose a .csv file to upload, then click "Upload file and import".', 'wp-club-manager' ) . '</p>';
			/* translators: 1: sample file URL */
			echo '<p>' . wp_kses_post( sprintf( __( 'Players need to be defined with columns in a specific order (10 columns). <a href="%s">Click here to download a sample</a>.', 'wp-club-manager' ), esc_url( plugin_dir_url( WPCM_PLUGIN_FILE ) . 'dummy-data/player-sample.csv' ) ) ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wpclubmanager_player_csv&step=1' );
			echo '</div>';
		}
	}
}
