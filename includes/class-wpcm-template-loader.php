<?php
/**
 * Template Loader
 *
 * @class 		WPCM_Template_Loader
 * @version		1.1.2
 * @package		WPClubManager/Classes
 * @category	Class
 * @author 		ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Template_Loader {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. wpclubmanager looks for theme
	 * overrides in /theme/wpclubmanager/ by default
	 *
	 * For beginners, it also looks for a wpclubmanager.php template first. If the user adds
	 * this to the theme (containing a wpclubmanager() inside) this will be used for all
	 * wpclubmanager templates.
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {

		$find = array( 'wpclubmanager.php' );
		$file = '';

		if ( is_single() && get_post_type() == 'wpcm_club' ) {

			$file 	= 'single-club.php';
			$find[] = $file;
			$find[] = WPCM_TEMPLATE_PATH . $file;

		}

		if ( is_single() && get_post_type() == 'wpcm_player' ) {

			$file 	= 'single-player.php';
			$find[] = $file;
			$find[] = WPCM_TEMPLATE_PATH . $file;

		}

		if ( is_single() && get_post_type() == 'wpcm_staff' ) {

			$file 	= 'single-staff.php';
			$find[] = $file;
			$find[] = WPCM_TEMPLATE_PATH . $file;

		}

		if ( is_single() && get_post_type() == 'wpcm_match' ) {

			$file 	= 'single-match.php';
			$find[] = $file;
			$find[] = WPCM_TEMPLATE_PATH . $file;

		}

		if ( is_single() && get_post_type() == 'wpcm_sponsor' ) {

			$file 	= 'single-sponsor.php';
			$find[] = $file;
			$find[] = WPCM_TEMPLATE_PATH . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = WPCM()->plugin_path() . '/templates/' . $file;
		}

		return $template;
	}
}

new WPCM_Template_Loader();