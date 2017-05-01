<?php
/**
 * Admin functions for the sponsor post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_CPT' ) ) {
	include( 'class-wc-admin-cpt.php' );
}

if ( ! class_exists( 'WPCM_Admin_CPT_Sponsor' ) ) :

class WPCM_Admin_CPT_Sponsor extends WPCM_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->type = 'wpcm_sponsor';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'custom_admin_post_thumbnail_html' ) );
		add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {

		if ( $post->post_type == 'wpcm_sponsor' )
			return __( 'Enter sponsor name', 'wp-club-manager' );

		return $text;
	}

	public function custom_admin_post_thumbnail_html( $content ) {
	    global $current_screen;
	 
	    if( 'wpcm_sponsor' == $current_screen->post_type ) {

	        $content = str_replace( __( 'Set featured image' ), __( 'Set sponsor logo', 'wp-club-manager' ), $content);
	    	$content = str_replace( __( 'Remove featured image' ), __( 'Remove sponsor logo', 'wp-club-manager' ), $content );
	    }

	        return $content;
	}

	/**
	 * Change "Featured Image" to "Player Image" throughout media modals.
	 * @param  array  $strings Array of strings to translate.
	 * @param  object $post
	 * @return array
	 */
	public function media_view_strings( $strings = array(), $post = null ) {

		if ( is_object( $post ) ) {
			if ( 'wpcm_sponsor' == $post->post_type ) {
				$strings['setFeaturedImageTitle'] = __( 'Set sponsor logo', 'wp-club-manager' );
				$strings['setFeaturedImage']      = __( 'Set sponsor logo', 'wp-club-manager' );
			}
		}

		return $strings;
	}
	
}

endif;

return new WPCM_Admin_CPT_Sponsor();