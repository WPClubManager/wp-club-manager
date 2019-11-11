<?php
/**
 * Taxonomy Ordering for Seasons and Teams.
 *
 * Code adapted from YIKES Simple Taxonomy Ordering plugin by Yikes Inc. and Evan Herman
 * https://wordpress.org/plugins/simple-taxonomy-ordering/
 *
 * @class 		WPCM_Taxonomy_Order
 * @version		2.0.0
 * @package		WPClubManager/Classes/
 * @category	Class
 * @author 		ClubPress
 */

//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Taxonomy_Order {
	
	/*
	*	Main Constructor
	*/	
	function __construct() {
		
		add_action( 'admin_head', array( $this, 'wpcm_custom_tax_order_admin_init' ) );
		add_action( 'init', array( $this, 'wpcm_custom_tax_order_front_end_init' ) );
		add_action( 'wp_ajax_update_taxonomy_order', array( $this, 'wpcm_handle_ajax_request' ) );
	}
					
	/**
	 * Initiate admin filter.
	 */
	public function wpcm_custom_tax_order_admin_init() {

		if( is_admin() ) {
			
			$screen = get_current_screen();

			if( isset( $screen ) && isset( $screen->base ) ) {

				if( $screen->base == 'edit-tags' ) {
					
					$this->wpcm_ensure_terms_have_tax_position_value( $screen );
					$taxonomies = self::wpcm_get_registered_taxonomies();
					if( ! isset( $_GET['orderby'] ) && $this->wpcm_is_taxonomy_position_enabled( $screen->taxonomy ) ) {
						add_filter( 'admin_init', array( $this, 'wpcm_ensure_tax_position_set' ) );
						add_filter( 'terms_clauses', array( $this, 'wpcm_alter_tax_order' ), 10, 3 );
					}
				}
			}
		}
	}
	
	/*
	* Initiate fron-end filter.
	*/
	public function wpcm_custom_tax_order_front_end_init() {
		
		if( ! is_admin() ) {

			add_filter( 'terms_clauses', array( $this, 'wpcm_alter_tax_order' ), 10, 3 );
		}
	}
	
	
	/**
	 * Update term metas
	 *
	 * @param mixed $screen
	 */
	public function wpcm_ensure_terms_have_tax_position_value( $screen ) {

		if( isset( $screen ) && isset( $screen->taxonomy ) ) {

			$terms = get_terms( $screen->taxonomy, array( 'hide_empty' => false ) );
			$x = 1;
			foreach( $terms as $term ) {

				if( ! get_term_meta( $term->term_id, 'tax_position', true ) ) {
					update_term_meta( $term->term_id, 'tax_position', $x );
					$x++;
				}
			}
		}
	}
	
	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $pieces
	 * @param array $taxonomies
	 * @param array $args
	 * @return array $pieces
	 */
	public function wpcm_alter_tax_order( $pieces, $taxonomies, $args ) {
		
		foreach( $taxonomies as $taxonomy ) {
			// confirm the tax is set to hierarchical -- else do not allow sorting
			if( $this->wpcm_is_taxonomy_position_enabled( $taxonomy ) ) {
				global $wpdb;

				$join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = 'tax_position'";

				if ( ! $this->wpcm_does_substring_exist( $pieces['join'], $join_statement ) ) {
					$pieces['join'] .= $join_statement;
				}
				$pieces['orderby'] = "ORDER BY CAST( term_meta.meta_value AS UNSIGNED )";
			}
		}
		return $pieces;
	}

	/**
	* Check if a substring exists inside a string
	*/
	protected function wpcm_does_substring_exist( $string, $substring ) {
		
		// Check if the $substring exists already in the $string
		return ( strstr( $string, $substring ) === false ) ? false : true;
	}
		
	/**
	 * Handle ajax request
	 */
	public function wpcm_handle_ajax_request() {

		$array_data = $_POST['updated_array'];

		foreach( $array_data as $taxonomy_data ) {
			
			update_term_meta( $taxonomy_data[0], 'tax_position', (int) ( $taxonomy_data[1] + 1 ) );
		}

		wp_die();

		exit;
	}
	
	/**
	 * Check taxonomy ordering is enabled.
	 *
	 * @param array $taxonomy_name
	 * @return false
	 */
	public function wpcm_is_taxonomy_position_enabled( $taxonomy_name ) {
		// Confirm a taxonomy name was passed in
		if( ! $taxonomy_name ) {
			return false;
		}
		$tax_object = get_taxonomy( $taxonomy_name );
		if( $tax_object && is_object( $tax_object ) ) {
			
			$enabled_taxonomies = array( 'wpcm_season', 'wpcm_team', 'wpcm_comp', 'wpcm_position', 'wpcm_jobs' );
			//if 'tax_position' => true || is set on the settings page
			if( isset( $tax_object->tax_position ) && $tax_object->tax_position || in_array( $taxonomy_name, $enabled_taxonomies ) ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
		*	Helper function to return an array of enabled drag and drop taxonomies
		*	@since 0.1
		*	@returns array of enabled taxonomes, or empty if none enabled
		*/
		public static function wpcm_get_registered_taxonomies() {
			// get ALL taxonomies on site
			$registered_taxonomies = get_taxonomies();
			// Array of taxonomies we want to exclude from being displayed in our options
			$ignored_taxonomies = apply_filters( 'wpcm_simple_taxonomy_ordering_ignored_taxonomies', array(
				'nav_menu',
				'link_category',
				'post_format'
			) );
			// WooCommerce taxonomies
			$ignored_taxonomies = array_merge( $ignored_taxonomies, apply_filters( 'wpcm_simple_taxonomy_ordering_ignored_woocommerce_taxonomies', array(
				'product_shipping_class',
				'product_cat', // excluded because Woo has built in drag and drop support out of the box
				'product_type',
			) ) );
			// Strip Woocommerce product attributes
			foreach( $registered_taxonomies as $registered_tax ) {
				// strip all woocommerce product attributes
				if ( strpos( $registered_tax, 'pa_' ) !== false) {
					$location = array_search( $registered_tax, $registered_taxonomies );
					unset( $registered_taxonomies[$location] );
				}
			}
			// Strip Duplicate Taxonomies
			foreach( $ignored_taxonomies as $ignored_tax ) {
				if( in_array( $ignored_tax, $registered_taxonomies ) ) {
					$location = array_search( $ignored_tax, $registered_taxonomies );
					if( $location ) {
						unset( $registered_taxonomies[$location] );
					}
				}
			}
			// return the taxonomies
			return $registered_taxonomies;
		}

}

new WPCM_Taxonomy_Order;