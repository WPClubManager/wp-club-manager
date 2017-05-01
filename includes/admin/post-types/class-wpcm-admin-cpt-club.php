<?php
/**
 * Admin functions for the club post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_CPT' ) ) {
	include( 'class-wpcm-admin-cpt.php' );
}

if ( ! class_exists( 'WPCM_Admin_CPT_Club' ) ) :

class WPCM_Admin_CPT_Club extends WPCM_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->type = 'wpcm_club';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Featured image text
		add_filter( 'admin_post_thumbnail_html', array( $this, 'custom_admin_post_thumbnail_html' ) );
		add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

		// Admin columns
		add_filter( 'manage_edit-wpcm_club_columns', array( $this, 'custom_edit_columns' ) );
		add_action( 'manage_wpcm_club_posts_custom_column', array( $this, 'custom_columns' ) );

		// Club filtering
		add_action( 'restrict_manage_posts', array( $this, 'request_filter_dropdowns' ) );

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

		if ( $post->post_type == 'wpcm_club' )
			return __( 'Enter club name', 'wp-club-manager' );

		return $text;
	}

	public function custom_admin_post_thumbnail_html( $content ) {
	    global $current_screen;
	 
	    if( 'wpcm_club' == $current_screen->post_type ) {

	        $content = str_replace( __( 'Set featured image' ), __( 'Set club badge', 'wp-club-manager' ), $content);
	    	$content = str_replace( __( 'Remove featured image' ), __( 'Remove club badge', 'wp-club-manager' ), $content );
	    }

	        return $content;
	}

	/**
	 * Change "Featured Image" to "Club Badge" throughout media modals.
	 * @param  array  $strings Array of strings to translate.
	 * @param  object $post
	 * @return array
	 */
	public function media_view_strings( $strings = array(), $post = null ) {

		if ( is_object( $post ) ) {
			if ( 'wpcm_club' == $post->post_type ) {
				$strings['setFeaturedImageTitle'] = __( 'Set club badge', 'wp-club-manager' );
				$strings['setFeaturedImage']      = __( 'Set club badge', 'wp-club-manager' );
			}
		}

		return $strings;
	}

	// edit columns
	public function custom_edit_columns($columns) {

		$columns = array(
			'cb' => "<input type=\"checkbox\" />",
			'crest' => '',
			'title' => __( 'Club', 'wp-club-manager' ),
			'comp' => __( 'Competition', 'wp-club-manager' ),
			'season' => __( 'Season', 'wp-club-manager' ),
		);

		return $columns;
	}

	// custom columns
	public function custom_columns($column) {

		global $post, $typenow;

		$post_id = $post->ID;

		if ( $typenow == 'wpcm_club' ) {

			switch ($column) {
			case 'crest' :
				echo get_the_post_thumbnail( $post_id, 'crest-small' );
				break;
			case 'comp' :
				the_terms( $post_id, 'wpcm_comp' );
				break;
			case 'season' :
				the_terms( $post_id, 'wpcm_season' );
				break;
			}
		}
	}

	// taxonomy filter dropdowns
	public function request_filter_dropdowns() {

		global $typenow, $wp_query;
		
		if ( $typenow == 'wpcm_club' ) {
			// comp dropdown
			$selected = isset( $_REQUEST['wpcm_comp'] ) ? $_REQUEST['wpcm_comp'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'competitions', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_comp',
				'name' => 'wpcm_comp',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// season dropdown
			$selected = isset( $_REQUEST['wpcm_season'] ) ? $_REQUEST['wpcm_season'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'seasons', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_season',
				'name' => 'wpcm_season',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
		}
	}
}

endif;

return new WPCM_Admin_CPT_Club();