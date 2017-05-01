<?php
/**
 * Tools Page
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPCM_Admin_Tools Class
 */
class WPCM_Admin_Tools {

	/**
	 * Handles output of the tools page in admin.
	 */
	public static function output() {

		include_once( 'views/html-admin-page-tools.php' );
	}

	
}