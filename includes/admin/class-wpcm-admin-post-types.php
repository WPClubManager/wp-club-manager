<?php
/**
 * Post Types Admin
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Post_Types' ) ) :

class WPCM_Admin_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );
	}

	/**
	 * Conditonally load classes and functions only needed when viewing a post type.
	 */
	public function include_post_type_handlers() {
		
		include( 'post-types/class-wpcm-admin-meta-boxes.php' );
		include( 'post-types/class-wpcm-admin-cpt-club.php' );
		include( 'post-types/class-wpcm-admin-cpt-match.php' );
		include( 'post-types/class-wpcm-admin-cpt-player.php' );
		include( 'post-types/class-wpcm-admin-cpt-sponsor.php' );
		include( 'post-types/class-wpcm-admin-cpt-staff.php' );
	}
}

endif;

return new WPCM_Admin_Post_Types();