<?php
/**
 * Admin functions for the club post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.0.0
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
		add_filter( 'gettext', array( $this, 'featured_image_gettext' ) );
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
	 * Check if we're editing or adding a club
	 * @return boolean
	 */
	private function is_editing_product() {

		if ( ! empty( $_GET['post_type'] ) && 'wpcm_club' == $_GET['post_type'] ) {
			return true;
		}
		if ( ! empty( $_GET['post'] ) && 'wpcm_club' == get_post_type( $_GET['post'] ) ) {
			return true;
		}
		if ( ! empty( $_REQUEST['post_id'] ) && 'wpcm_club' == get_post_type( $_REQUEST['post_id'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {

		if ( $post->post_type == 'wpcm_club' )
			return __( 'Club name', 'wpclubmanager' );

		return $text;
	}

	/**
	 * Replace 'Featured' when editing a club. Adapted from https://gist.github.com/tw2113/c7fd8da782232ce90176
	 * @param  string $string string being translated
	 * @return string after manipulation
	 */
	public function featured_image_gettext( $string = '' ) {

		if ( 'Featured Image' == $string && $this->is_editing_product() ) {
			$string = __( 'Club Badge', 'wpclubmanager' );
		} elseif ( 'Remove featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Remove club badge', 'wpclubmanager' );
		} elseif ( 'Set featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Set club badge', 'wpclubmanager' );
		}

		return $string;
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
				$strings['setFeaturedImageTitle'] = __( 'Set club badge', 'wpclubmanager' );
				$strings['setFeaturedImage']      = __( 'Set club badge', 'wpclubmanager' );
			}
		}

		return $strings;
	}

	// edit columns
	public function custom_edit_columns($columns) {

		$columns = array(
			'cb' => "<input type=\"checkbox\" />",
			'crest' => '',
			'title' => __( 'Club', 'wpclubmanager' ),
			'comp' => __( 'Competition', 'wpclubmanager' ),
			'season' => __( 'Season', 'wpclubmanager' ),
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
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wpclubmanager' ), __( 'competitions', 'wpclubmanager' ) ),
				'taxonomy' => 'wpcm_comp',
				'name' => 'wpcm_comp',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// season dropdown
			$selected = isset( $_REQUEST['wpcm_season'] ) ? $_REQUEST['wpcm_season'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wpclubmanager' ), __( 'seasons', 'wpclubmanager' ) ),
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