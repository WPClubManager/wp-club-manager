<?php
/**
 * Plugin Name: WP Club Manager
 * Plugin URI: https://wpclubmanager.com
 * Description: A plugin to help you run a sports club website easily and quickly.
 * Author: WP Club Manager
 * Author URI: https://wpclubmanager.com
 * Requires PHP: 7.2
 * Version: 2.2.14
 * Text Domain: wp-club-manager
 * Domain Path: /languages/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WPClubManager
 * @category  Core
 * @author    Clubpress <info@wpclubmanager.com>
 */

if ( ! function_exists( 'WPCM' ) ) :
	/**
	 * Returns the main instance of WPCM to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return WP_Club_Manager
	 */
	function WPCM() {
		require_once __DIR__ . '/includes/class-wp-club-manager.php';

		return WP_Club_Manager::instance( __FILE__, '2.2.14' );
	}
endif;

WPCM();
