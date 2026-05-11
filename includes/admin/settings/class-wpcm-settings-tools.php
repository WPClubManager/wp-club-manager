<?php
/**
 * WPClubManager Tools Settings (Export/Import)
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPCM_Settings_Tools' ) ) :

	/**
	 * WPCM_Settings_Tools
	 */
	class WPCM_Settings_Tools extends WPCM_Settings_Page {

		/**
		 * Options that should not be exported or imported.
		 *
		 * @var array
		 */
		private static $excluded_options = array(
			'wpclubmanager_version',
			'wpclubmanager_installed',
			'wpcm_version_upgraded_from',
			'wpclubmanager_admin_footer_text_rated',
		);

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'tools';
			$this->label = __( 'Tools', 'wp-club-manager' );

			add_filter( 'wpclubmanager_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );
			add_action( 'wpclubmanager_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'wpclubmanager_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'admin_init', array( $this, 'handle_export' ) );
		}

		/**
		 * Get all WPCM option names from the database.
		 *
		 * @return array
		 */
		private static function get_wpcm_option_names() {
			global $wpdb;

			$results = $wpdb->get_col(
				"SELECT option_name FROM {$wpdb->options}
				WHERE option_name LIKE 'wpcm\_%'
				OR option_name LIKE 'wpclubmanager\_%'"
			);

			return is_array( $results ) ? $results : array();
		}

		/**
		 * Check if an option name is a valid WPCM setting.
		 *
		 * @param string $option_name Option name to check.
		 * @return bool
		 */
		private static function is_valid_option( $option_name ) {
			if ( in_array( $option_name, self::$excluded_options, true ) ) {
				return false;
			}

			return ( strpos( $option_name, 'wpcm_' ) === 0 || strpos( $option_name, 'wpclubmanager_' ) === 0 );
		}

		/**
		 * Get export data as an associative array.
		 *
		 * @return array
		 */
		public static function get_export_data() {
			$option_names = self::get_wpcm_option_names();
			$data         = array();

			foreach ( $option_names as $name ) {
				if ( self::is_valid_option( $name ) ) {
					$data[ $name ] = get_option( $name );
				}
			}

			ksort( $data );

			return $data;
		}

		/**
		 * Validate import JSON string.
		 *
		 * @param string $json JSON string to validate.
		 * @return array|WP_Error Decoded data array on success, WP_Error on failure.
		 */
		public static function validate_import_json( $json ) {
			$data = json_decode( $json, true );

			if ( null === $data ) {
				return new WP_Error(
					'invalid_json',
					__( 'The uploaded file does not contain valid JSON.', 'wp-club-manager' )
				);
			}

			if ( ! is_array( $data ) || empty( $data ) ) {
				return new WP_Error(
					'invalid_format',
					__( 'The uploaded file does not contain valid settings data.', 'wp-club-manager' )
				);
			}

			return $data;
		}

		/**
		 * Import settings from an associative array.
		 *
		 * @param array $data Settings data to import.
		 * @return bool True on success, false if data is empty.
		 */
		public static function import_settings( $data ) {
			if ( empty( $data ) ) {
				return false;
			}

			foreach ( $data as $name => $value ) {
				if ( self::is_valid_option( $name ) ) {
					update_option( $name, $value );
				}
			}

			return true;
		}

		/**
		 * Handle the export action via GET request.
		 */
		public function handle_export() {
			$action = filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW );
			$nonce  = filter_input( INPUT_GET, '_wpnonce', FILTER_UNSAFE_RAW );

			if ( 'wpcm_export_settings' !== $action ) {
				return;
			}

			if ( empty( $nonce ) || ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wpcm-export-settings' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wp-club-manager' ) );
			}

			if ( ! current_user_can( 'manage_wpclubmanager' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				wp_die( esc_html__( 'You do not have permission to export settings.', 'wp-club-manager' ) );
			}

			$data     = self::get_export_data();
			$filename = 'wpcm-settings-' . gmdate( 'Y-m-d' ) . '.json';

			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Expires: 0' );

			echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
			exit;
		}

		/**
		 * Save / process import.
		 */
		public function save() {
			$nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_UNSAFE_RAW );
			if ( empty( $nonce ) || ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wpclubmanager-settings' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_wpclubmanager' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				return;
			}

			if ( empty( $_FILES['wpcm_import_file'] ) || empty( $_FILES['wpcm_import_file']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				WPCM_Admin_Settings::add_error( __( 'Please select a JSON file to import.', 'wp-club-manager' ) );
				return;
			}

			$file = $_FILES['wpcm_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			if ( ! empty( $file['error'] ) ) {
				WPCM_Admin_Settings::add_error( __( 'There was an error uploading the file. Please try again.', 'wp-club-manager' ) );
				return;
			}

			$tmp_name = sanitize_text_field( $file['tmp_name'] );
			$json     = file_get_contents( $tmp_name ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			$data = self::validate_import_json( $json );

			if ( is_wp_error( $data ) ) {
				WPCM_Admin_Settings::add_error( $data->get_error_message() );
				return;
			}

			$result = self::import_settings( $data );

			if ( $result ) {
				WPCM_Admin_Settings::add_message( __( 'Settings imported successfully.', 'wp-club-manager' ) );
			} else {
				WPCM_Admin_Settings::add_error( __( 'No valid settings found in the uploaded file.', 'wp-club-manager' ) );
			}
		}

		/**
		 * Get settings array (empty — this tab uses custom output).
		 *
		 * @return array
		 */
		public function get_settings() {
			return array();
		}

		/**
		 * Output the tools page.
		 */
		public function output() {
			$GLOBALS['hide_save_button'] = true;

			$export_url = wp_nonce_url(
				admin_url( 'admin.php?page=wpcm-settings&tab=tools&action=wpcm_export_settings' ),
				'wpcm-export-settings'
			);
			?>
			<div class="stuffbox">
				<h3><?php esc_html_e( 'Export Settings', 'wp-club-manager' ); ?></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export your WP Club Manager settings as a JSON file. This can be used to transfer your settings to another site.', 'wp-club-manager' ); ?></p>
					<p>
						<a href="<?php echo esc_url( $export_url ); ?>" class="button">
							<?php esc_html_e( 'Export Settings', 'wp-club-manager' ); ?>
						</a>
					</p>
				</div>
			</div>

			<div class="stuffbox">
				<h3><?php esc_html_e( 'Import Settings', 'wp-club-manager' ); ?></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Import WP Club Manager settings from a previously exported JSON file. This will overwrite your current settings.', 'wp-club-manager' ); ?></p>
					<table class="form-table">
						<tr>
							<th scope="row" class="titledesc">
								<label for="wpcm_import_file"><?php esc_html_e( 'Settings File', 'wp-club-manager' ); ?></label>
							</th>
							<td class="forminp">
								<input type="file" name="wpcm_import_file" id="wpcm_import_file" accept=".json" />
								<span class="description"><?php esc_html_e( 'Upload a .json file previously exported from WP Club Manager.', 'wp-club-manager' ); ?></span>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input name="save" class="button-primary" type="submit" value="<?php esc_html_e( 'Import Settings', 'wp-club-manager' ); ?>" />
					</p>
				</div>
			</div>
			<?php
		}
	}

endif;

return new WPCM_Settings_Tools();
