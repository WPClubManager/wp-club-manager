<?php
/**
 * WP Club Manager Importer
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     1.2.11
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WP_Importer' ) ) {

	/**
	 * WPCM_Importer
	 */
	class WPCM_Importer extends WP_Importer {

		/**
		 * @var int
		 */
		public $id;

		/**
		 * @var string
		 */
		public $file_url;

		/**
		 * @var string
		 */
		public $import_page;

		/**
		 * @var string
		 */
		public $delimiter;

		/**
		 * @var array
		 */
		public $posts = array();

		/**
		 * @var int
		 */
		public $imported;

		/**
		 * @var int
		 */
		public $skipped;

		/**
		 * @var string
		 */
		public $import_label;

		/**
		 * @var array
		 */
		public $columns = array();

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the CSV import process
		 */
		public function dispatch() {
			$this->header();

			$delimiter = filter_input( INPUT_POST, 'delimiter', FILTER_UNSAFE_RAW );
			if ( $delimiter ) {
				$this->delimiter = stripslashes( trim( $delimiter ) );
			}

			if ( ! $this->delimiter ) {
				$this->delimiter = ',';
			}

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

			switch ( $step ) :

				case 0:
					$this->greet();
					break;

				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) :

						if ( $this->id ) {
							$file = get_attached_file( $this->id );
						} else {
							$file = ABSPATH . $this->file_url;
						}

						add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

						if ( function_exists( 'gc_enable' ) ) {
							gc_enable();
						}

						@set_time_limit( 0 );
						@ob_flush();
						@flush();

						$this->import_table( $file );
					endif;
					break;

				case 2:
					check_admin_referer( 'import-upload' );
					$import = filter_input( INPUT_POST, 'wpcm_import', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
					if ( $import ) :
						$columns = filter_input( INPUT_POST, 'wpcm_columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
						if ( empty( $columns ) ) {
							$columns = array( 'post_title' );
						}
						$this->import( $import, array_values( $columns ) );
					endif;
					break;

			endswitch;

			$this->footer();
		}

		/**
		 * dropdown function.
		 * Adapted from https://wordpress.org/plugins/sportspress/
		 *
		 * @access public
		 *
		 * @param string $selected
		 *
		 * @return void
		 */
		public function dropdown( $selected ) {
			?>
			<select name="wpcm_columns[]" data-index="<?php echo esc_attr( array_search( $selected, array_keys( $this->columns ) ) ); ?>">
				<option value="0">&mdash; <?php esc_html_e( 'Disable', 'wp-club-manager' ); ?> &mdash;</option>
				<?php foreach ( $this->columns as $key => $label ) : ?>
					<option value="<?php echo esc_html( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
		}

		/**
		 * Import table function.
		 * Adapted from https://wordpress.org/plugins/sportspress/
		 *
		 * @access public
		 * @param mixed $file
		 * @return void
		 */
		public function import_table( $file ) {
			global $wpdb;

			$this->imported = 0;
			$this->skipped  = 0;

			if ( ! is_file( $file ) ) :

				echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
				esc_html_e( 'The file does not exist, please try again.', 'wp-club-manager' ) . '</p>';

				$this->footer();

				die();

			endif;

			ini_set( 'auto_detect_line_endings', '1' );
			$handle = fopen( $file, 'r' );
			if ( false !== $handle ) :

				$header = fgetcsv( $handle, 0, $this->delimiter );

				if ( count( $header ) >= 1 ) :

					$action = 'admin.php?import=' . $this->import_page . '&step=2';
					?>
					<form enctype="multipart/form-data" id="import-upload-form" class="wpcm-form" method="post" action="<?php echo esc_attr( wp_nonce_url( $action, 'import-upload' ) ); ?>">
						<table class="wp-list-table widefat fixed pages">
							<thead>
								<tr>
									<?php foreach ( $this->columns as $key => $label ) : ?>
										<th scope="col" class="manage-column">
											<?php $this->dropdown( $key ); ?>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php while ( ( $row = fgetcsv( $handle, 0, $this->delimiter ) ) !== false ) : ?>
									<tr>
										<?php
										$index = 0;
										foreach ( $this->columns as $key => $label ) :
											$value = wpcm_array_value( $row, $index );
											?>
											<td>
												<input type="text" class="widefat" value="<?php echo esc_html( $value ); ?>" name="wpcm_import[]">
											</td>
																				<?php
																				++$index;
endforeach;
										?>
									</tr>
									<?php
									++$this->imported;
endwhile;
								?>
								<tr>
									<?php foreach ( $this->columns as $key => $label ) : ?>
										<td>
											<input type="text" class="widefat" name="wpcm_import[]">
										</td>
									<?php endforeach; ?>
								</tr>
							</tbody>
						</table>
						<p class="alignright">
							<?php
							/* translators: 1: imported total 2: imported total  */
							printf( esc_html__( 'Displaying %1$s&#8211;%2$s of %3$s', 'wp-club-manager' ), 1, esc_html( $this->imported + 1 ), esc_html( $this->imported + 1 ) );
							?>
						</p>
						<p class="submit">
							<input type="submit" class="button button-primary" value="<?php echo esc_attr( $this->import_label ); ?>" />
						</p>
					</form>
					<?php
				else :

					echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
					esc_html_e( 'The CSV is invalid.', 'wp-club-manager' ) . '</p>';
					$this->footer();
					die();

				endif;

				fclose( $handle );

			endif;
		}

		/**
		 * format_data_from_csv function.
		 *
		 * @access public
		 * @param mixed  $data
		 * @param string $enc
		 * @return string
		 */
		public function format_data_from_csv( $data, $enc ) {
			return ( 'UTF-8' == $enc ) ? $data : utf8_encode( $data );
		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		public function handle_upload() {

			$file_url = filter_input( INPUT_POST, 'file_url', FILTER_VALIDATE_URL );
			if ( empty( $file_url ) ) {

				$file = wp_import_handle_upload();

				if ( isset( $file['error'] ) ) {
					echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
					echo esc_html( $file['error'] ) . '</p>';
					return false;
				}

				$this->id = (int) $file['id'];

			} elseif ( file_exists( ABSPATH . $file_url ) ) {

					$this->file_url = esc_attr( $file_url );

			} else {

				echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong></p>';
				return false;
			}

			return true;
		}

		/**
		 * header function.
		 *
		 * @access public
		 * @return void
		 */
		public function header() {
			echo '<div class="wrap"><h2>' . esc_html( $this->import_label ) . '</h2>';
		}

		/**
		 * footer function.
		 *
		 * @access public
		 * @return void
		 */
		public function footer() {
			echo '</div>'; // phpcs:ignore
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 *
		 * @param  int $val
		 * @return int 60
		 */
		public function bump_request_timeout( $val ) {
			return 60;
		}
	}
}
