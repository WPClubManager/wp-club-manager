<?php
/**
 * Admin functions for post types
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_CPT' ) ) :

class WPCM_Admin_CPT {

	protected $type = '';

	/**
	 * Constructor
	 */
	public function __construct() {

		// Insert into X media browser
		//add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
	}

	/**
	 * Change label for insert buttons.
	 * @param array $strings
	 * @return array
	 */
	public function change_insert_into_post( $strings ) {
		
		global $post_type;

		if ( $post_type == $this->type ) {
			$obj = get_post_type_object( $this->type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'wp-club-manager' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'wp-club-manager' ), $obj->labels->singular_name );
		}

		return $strings;
	}
}

endif;