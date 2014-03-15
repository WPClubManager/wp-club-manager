<?php
/**
 * Admin functions for the sponsor post type
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Post Types
 * @version     1.0.0
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
		add_filter( 'gettext', array( $this, 'text_replace' ), 10, 4 );
		add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

		add_filter( 'manage_edit-wpcm_sponsor_columns', array( $this, 'custom_edit_columns' ) );
		add_action( 'manage_wpcm_sponsor_posts_custom_column', array( $this, 'custom_columns' ) );
		add_filter( 'manage_edit-wpcm_sponsor_sortable_columns', array( $this, 'custom_sortable_columns' ) );
		add_filter( 'request', array( $this, 'custom_column_orderby' ) );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Check if we're editing or adding a match
	 * @return boolean
	 */
	private function is_editing_product() {
		if ( ! empty( $_GET['post_type'] ) && 'wpcm_sponsor' == $_GET['post_type'] ) {
			return true;
		}
		if ( ! empty( $_GET['post'] ) && 'wpcm_sponsor' == get_post_type( $_GET['post'] ) ) {
			return true;
		}
		if ( ! empty( $_REQUEST['post_id'] ) && 'wpcm_sponsor' == get_post_type( $_REQUEST['post_id'] ) ) {
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

		if ( $post->post_type == 'wpcm_sponsor' )
			return __( 'Sponsor name', 'wpclubmanager' );

		return $text;
	}

	// text replace
	public function text_replace( $string = '' ) {

		if ( 'Featured Image' == $string && $this->is_editing_product() ) {
			$string = __( 'Sponsor Logo', 'wpclubmanager' );
		} elseif ( 'Remove featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Remove sponsor logo', 'wpclubmanager' );
		} elseif ( 'Set featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Set sponsor logo', 'wpclubmanager' );
		}
		return $string;
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
				$strings['setFeaturedImageTitle'] = __( 'Set sponsor logo', 'wpclubmanager' );
				$strings['setFeaturedImage']      = __( 'Set sponsor logo', 'wpclubmanager' );
			}
		}

		return $strings;
	}

	// edit columns
	public function custom_edit_columns($columns) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Name' ),
			'link_url' => __( 'URL', 'wpclubmanager' ),
			'link_directly' => __( 'Link directly?', 'wpclubmanager' )
		);

		return $columns;
	}

	// custom columns
	public function custom_columns($column) {

		global $post, $typenow;

		$post_id = $post->ID;

		if ( $typenow == 'wpcm_sponsor' ) {
			switch ($column) {
			case 'link_url':
				$link_url = get_post_meta( $post_id, 'wpcm_link_url', true );
				if ( isset( $link_url ) )
					echo '<a href="'.$link_url.'" target="_blank">' . $link_url . '</a>';
				break;
			case 'link_directly':
				$link_directly = get_post_meta($post_id, 'wpcm_link_directly', true );
				echo $link_directly ? __( 'Yes' ) : __( 'No' );
				break;
			}
		}
	}

	// sortable columns
	public function custom_sortable_columns($columns) {

		$custom = array(
			'link_url' => 'link_url',
			'link_directly' => 'link_directly'
		);

		return wp_parse_args($custom, $columns);
	}

	// column sorting rules
	public function custom_column_orderby( $vars ) {

		global $pagenow;

		if ( $pagenow == 'edit.php' && $vars['post_type'] == 'wpcm_sponsor' && isset( $vars['orderby'] )):
			if ( in_array( $vars['orderby'], array( 'link_url', 'link_directly' ) ) ):
				$vars = array_merge( $vars, array(
					'meta_key' => 'wpcm_' . $vars['orderby'],
					'orderby' => 'meta_value'
				) );
			endif;
		endif;
		
		return $vars;
	}
}

endif;

return new WPCM_Admin_CPT_Sponsor();