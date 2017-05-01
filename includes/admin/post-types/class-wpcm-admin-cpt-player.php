<?php
/**
 * Admin functions for the player post type
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

if ( ! class_exists( 'WPCM_Admin_CPT_Player' ) ) :

class WPCM_Admin_CPT_Player extends WPCM_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		$this->type = 'wpcm_player';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'gettext', array( $this, 'text_replace' ), 10, 4 );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'custom_admin_post_thumbnail_html' ) );
		add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

		add_filter( 'manage_edit-wpcm_player_columns', array( $this, 'custom_edit_columns' ) );
		add_action( 'manage_wpcm_player_posts_custom_column', array( $this, 'custom_columns' ) );
		add_filter( 'manage_edit-wpcm_player_sortable_columns', array( $this, 'custom_sortable_columns' ) );

		add_filter( 'request', array( $this, 'custom_column_orderby' ) );

		add_action( 'restrict_manage_posts', array( $this, 'request_filter_dropdowns' ) );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Check if we're editing or adding a match
	 * @return boolean
	 */
	private function is_editing_product() {
		if ( ! empty( $_GET['post_type'] ) && 'wpcm_player' == $_GET['post_type'] ) {
			return true;
		}
		if ( ! empty( $_GET['post'] ) && 'wpcm_player' == get_post_type( $_GET['post'] ) ) {
			return true;
		}
		if ( ! empty( $_REQUEST['post_id'] ) && 'wpcm_player' == get_post_type( $_REQUEST['post_id'] ) ) {
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
		
		if ( $post->post_type == 'wpcm_player' )
			return __( 'Enter players name', 'wp-club-manager' );

		return $text;
	}

	// text replace
	public function text_replace( $string = '' ) {

		if ( 'Scheduled for: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Joins on: <b>%1$s</b>', 'wp-club-manager' );
		} elseif ( 'Published on: <b>%1$s</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
		} elseif ( 'Publish <b>immediately</b>' == $string && $this->is_editing_product() ) {
			$string = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
		}

		return $string;
	}

	public function custom_admin_post_thumbnail_html( $content ) {
	    global $current_screen;
	 
	    if( 'wpcm_player' == $current_screen->post_type ) {

	        $content = str_replace( __( 'Set featured image' ), __( 'Set player image', 'wp-club-manager' ), $content);
	    	$content = str_replace( __( 'Remove featured image' ), __( 'Remove player image', 'wp-club-manager' ), $content );
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
			if ( 'wpcm_player' == $post->post_type ) {
				$strings['setFeaturedImageTitle'] = __( 'Set player image', 'wp-club-manager' );
				$strings['setFeaturedImage']      = __( 'Set player image', 'wp-club-manager' );
			}
		}

		return $strings;
	}

	// edit columns
	public function custom_edit_columns($columns) {
		
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'number' => '#',
			'title' => __( 'Name' ),
			'position' => __( 'Position', 'wp-club-manager' ),
			'team' => __( 'Team', 'wp-club-manager' ),
			'season' => __( 'Season', 'wp-club-manager' )
		);
		return $columns;	
	}

	// custom columns
	public function custom_columns($column) {
		
		global $post, $typenow;
		$post_id = $post->ID;
		if ( $typenow == 'wpcm_player' ) {
			switch ($column) {
			case 'number':
				$number = get_post_meta($post_id, 'wpcm_number', true);		
				echo $number;
				break;
			case 'position':
				the_terms($post_id, 'wpcm_position');
				break;
			case 'team':
				the_terms($post_id, 'wpcm_team');
				break;
			case 'season':
				the_terms($post_id, 'wpcm_season');
				break;
			}
		}
	}

	// sortable columns
	public function custom_sortable_columns($columns) {
		
		$custom = array(
			'number' => 'number'
		);
		return wp_parse_args($custom, $columns);
	}

	// column sorting rules
	public function custom_column_orderby( $vars ) {
		
		if (isset( $vars['orderby'] )) :
			if ( $vars['orderby'] == 'number' ):
				$vars = array_merge( $vars, array(
					'meta_key' => 'wpcm_number',
					'orderby' => 'meta_value_num'
				) );
			endif;
			if ( in_array( $vars['orderby'], array(  'position' ) ) ):
				$vars = array_merge( $vars, array(
					'meta_key' => 'wpcm_' . $vars['orderby'],
					'orderby' => 'meta_value'
				) );
			endif;
		endif;

		return $vars;
	}

	// taxonomy filter dropdowns
	public function request_filter_dropdowns() {
		
		global $typenow, $wp_query;
		if ( $typenow == 'wpcm_player' ) {
			// position dropdown
			$selected = isset( $_REQUEST['wpcm_position'] ) ? $_REQUEST['wpcm_position'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'positions', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_position',
				'name' => 'wpcm_position',
				'selected' => $selected
			);
			wpcm_dropdown_taxonomies($args);
			echo PHP_EOL;
			// team dropdown
			$selected = isset( $_REQUEST['wpcm_team'] ) ? $_REQUEST['wpcm_team'] : null;
			$args = array(
				'show_option_all' =>  sprintf( __( 'Show all %s', 'wp-club-manager' ), __( 'teams', 'wp-club-manager' ) ),
				'taxonomy' => 'wpcm_team',
				'name' => 'wpcm_team',
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

return new WPCM_Admin_CPT_Player();