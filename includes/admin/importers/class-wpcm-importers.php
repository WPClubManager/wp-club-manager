<?php
/**
 * WP Club Manager Importer
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Importers
 * @version     1.2.11
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WP_Importer' ) ) {
	class WPCM_Importer extends WP_Importer {

		var $id;
		var $file_url;
		var $import_page;
		var $delimiter;
		var $posts = array();
		var $imported;
		var $skipped;
		var $import_label;
		var $columns = array();

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the CSV import process
		 */
		public function dispatch() {
			$this->header();

			if ( ! empty( $_POST['delimiter'] ) )
				$this->delimiter = stripslashes( trim( $_POST['delimiter'] ) );

			if ( ! $this->delimiter )
				$this->delimiter = ',';

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

			switch ( $step ):

				case 0:
					$this->greet();
				break;

				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ):

						if ( $this->id )
							$file = get_attached_file( $this->id );
						else
							$file = ABSPATH . $this->file_url;

						add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

						if ( function_exists( 'gc_enable' ) )
							gc_enable();

						@set_time_limit(0);
						@ob_flush();
						@flush();

						$this->import_table( $file );
					endif;
				break;

				case 2:
					check_admin_referer( 'import-upload' );
					if ( isset( $_POST['wpcm_import'] ) ):
						$columns = array_filter( wpcm_array_value( $_POST, 'wpcm_columns', array( 'post_title' ) ) );
						$this->import( $_POST['wpcm_import'], array_values( $columns ) );
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
		 * @param mixed $file
		 * @return void
		 */
		function dropdown( $selected ) {
			?>
			<select name="wpcm_columns[]" data-index="<?php echo array_search( $selected, array_keys( $this->columns ) ); ?>">
				<option value="0">&mdash; <?php _e( 'Disable', 'wp-club-manager' ); ?> &mdash;</option>
				<?php foreach ( $this->columns as $key => $label ): ?>
					<option value="<?php echo $key; ?>" <?php selected( $selected, $key ); ?>><?php echo $label; ?></option>
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
		function import_table( $file ) {
			global $wpdb;

			$this->imported = $this->skipped = 0;

			if ( ! is_file($file) ):

				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'wp-club-manager' ) . '</p>';

				$this->footer();

				die();

			endif;

			ini_set( 'auto_detect_line_endings', '1' );

			if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ):

				$header = fgetcsv( $handle, 0, $this->delimiter );

				if ( sizeof( $header ) >= 1 ):

					$action = 'admin.php?import=' . $this->import_page . '&step=2';
					?>
					<form enctype="multipart/form-data" id="import-upload-form" class="wpcm-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
						<table class="wp-list-table widefat fixed pages">
							<thead>
								<tr>
									<?php foreach ( $this->columns as $key => $label ): ?>
										<th scope="col" class="manage-column">
											<?php $this->dropdown( $key ); ?>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php while ( ( $row = fgetcsv( $handle, 0, $this->delimiter ) ) !== FALSE ): ?>
									<tr>
										<?php $index = 0; foreach ( $this->columns as $key => $label ): $value = wpcm_array_value( $row, $index ); ?>
											<td>
												<input type="text" class="widefat" value="<?php echo $value; ?>" name="wpcm_import[]">
											</td>
										<?php $index ++; endforeach; ?>
									</tr>
								<?php $this->imported++; endwhile; ?>
								<tr>
									<?php foreach ( $this->columns as $key => $label ): ?>
										<td>
											<input type="text" class="widefat" name="wpcm_import[]">
										</td>
									<?php endforeach; ?>
								</tr>
						    </tbody>
						</table>
						<p class="alignright">
							<?php printf( __( 'Displaying %s&#8211;%s of %s', 'wp-club-manager' ), 1, $this->imported+1, $this->imported+1 ); ?>
						</p>
						<p class="submit">
							<input type="submit" class="button button-primary" value="<?php echo esc_attr( $this->import_label ); ?>" />
						</p>
					</form>
					<?php
				else:

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
					echo __( 'The CSV is invalid.', 'wp-club-manager' ) . '</p>';
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
		 * @param mixed $data
		 * @param string $enc
		 * @return string
		 */
		function format_data_from_csv( $data, $enc ) {
			return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		function handle_upload() {

			if ( empty( $_POST['file_url'] ) ) {

				$file = wp_import_handle_upload();

				if ( isset( $file['error'] ) ) {
					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong><br />';
					echo esc_html( $file['error'] ) . '</p>';
					return false;
				}

				$this->id = (int) $file['id'];

			} else {

				if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

					$this->file_url = esc_attr( $_POST['file_url'] );

				} else {

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wp-club-manager' ) . '</strong></p>';
					return false;

				}

			}

			return true;
		}

		/**
		 * header function.
		 *
		 * @access public
		 * @return void
		 */
		function header() {
			echo '<div class="wrap"><h2>' . $this->import_label . '</h2>';
		}

		/**
		 * footer function.
		 *
		 * @access public
		 * @return void
		 */
		function footer() {
			echo '</div>';
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 * @param  int $val
		 * @return int 60
		 */
		function bump_request_timeout( $val ) {
			return 60;
		}
	}
}
