<?php
/**
 * Status Page
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPCM_Admin_Status Class
 */
class WPCM_Admin_Status {

	/**
	 * Handles output of the reports page in admin.
	 */
	public static function output() {

		include_once( 'views/html-admin-page-status.php' );
	}

	/**
	 * Retrieve metadata from a file. Based on WP Core's get_file_data function
	 * @since  2.1.1
	 * @param  string $file Path to the file
	 * @return string
	 */
	public static function get_file_version( $file ) {

		// Avoid notices if file does not exist
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version   = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] )
			$version = _cleanup_header_comment( $match[1] );

		return $version ;
	}

	/**
	 * Scan the template files
	 * @param  string $template_path
	 * @return array
	 */
	public static function scan_template_files( $template_path ) {

		$files         = scandir( $template_path );
		$result        = array();

		if ( $files ) {

			foreach ( $files as $key => $value ) {

				if ( ! in_array( $value, array( ".",".." ) ) ) {

					if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
						$sub_files = self::scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
						foreach ( $sub_files as $sub_file ) {
							$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
						}
					} else {
						$result[] = $value;
					}
				}
			}
		}
		return $result;
	}
}