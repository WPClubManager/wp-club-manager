<?php
/**
 * Taxonomy Ordering for Seasons and Teams.
 *
 * Code adapted from YIKES Simple Taxonomy Ordering plugin by Yikes Inc. and Evan Herman
 * https://wordpress.org/plugins/simple-taxonomy-ordering/
 *
 * @class       WPCM_Taxonomy_Order
 * @version     2.2.0
 * @package     WPClubManager/Classes/
 * @category    Class
 * @author      ClubPress
 */

/**
 * Class WPCM_Taxonomy_Order.
 */
class WPCM_Taxonomy_Order {

	/**
	 * Main Constructor.
	 */
	public function __construct() {

		// Hooks.
		add_action( 'current_screen', array( $this, 'admin_order_terms' ) );
		add_action( 'init', array( $this, 'front_end_order_terms' ) );
		add_action( 'wp_ajax_wpcm_update_taxonomy_order', array( $this, 'update_taxonomy_order' ) );
	}

	/**
	 * Order the terms on the admin side.
	 *
	 * @param WP_Screen $screen
	 */
	public function admin_order_terms( WP_Screen $screen ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Form data is not being used.
		if ( empty( $_GET['orderby'] ) && 'edit-tags' === $screen->base && $this->is_taxonomy_ordering_enabled( $screen->taxonomy ) ) {
			$this->enqueue();
			$this->default_term_order( $screen->taxonomy );
			$this->wpcm_custom_help_tab();

			add_filter( 'terms_clauses', array( $this, 'set_tax_order' ), 10, 3 );
		}
	}

	/**
	 * Add a help tab to the taxonomy screen.
	 */
	public function wpcm_custom_help_tab() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'      => 'wpcm_tax_order_help_tab',
				'title'   => __( 'Taxonomy Ordering', 'wp-club-manager' ),
				'content' => '<p>' . __( 'To reposition a taxonomy in the list, simply click on a taxonomy and drag & drop it into the desired position. Each time you reposition a taxonomy, the data will update in the database and on the front end of your site.', 'wp-club-manager' ) . '</p>',
			)
		);
	}

	/**
	 * Order the taxonomies on the front end.
	 */
	public function front_end_order_terms() {
		if ( ! is_admin() ) {
			add_filter( 'terms_clauses', array( $this, 'set_tax_order' ), 10, 3 );
		}
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue() {
		$tax = function_exists( 'get_current_screen' ) ? get_current_screen()->taxonomy : '';
		wp_enqueue_script( 'wpcm-tax-drag-drop', WPCM()->plugin_url() . '/assets/js/admin/wpclubmanager-tax-drag-drop.js', array( 'jquery-ui-core', 'jquery-ui-sortable' ), WPCM_VERSION, true );
		wp_localize_script(
			'wpcm-tax-drag-drop',
			'wpcm_taxonomy_ordering_data',
			array(
				'preloader_url'    => esc_url( admin_url( 'images/wpspin_light.gif' ) ),
				'term_order_nonce' => wp_create_nonce( 'term_order_nonce' ),
				'paged'            => isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 0,
				'per_page_id'      => "edit_{$tax}_per_page",
			)
		);
	}

	/**
	 * Default the taxonomy's terms' order if it's not set.
	 *
	 * @param string $tax_slug The taxonomy's slug.
	 */
	public function default_term_order( $tax_slug ) {
		$terms = get_terms( array(
			'taxonomy'   => $tax_slug,
			'hide_empty' => false,
		) );
		// $order = 1;
		$order = $this->get_max_taxonomy_order( $tax_slug );
		foreach ( $terms as $term ) {
			if ( ! get_term_meta( $term->term_id, 'tax_position', true ) ) {
				update_term_meta( $term->term_id, 'tax_position', $order );
				++$order;
			}
		}
	}

	/**
	 * Get the maximum tax_position for this taxonomy. This will be applied to terms that don't have a tax position.
	 *
	 * @param string $tax_slug
	 *
	 * @return int
	 */
	private function get_max_taxonomy_order( $tax_slug ) {
		global $wpdb;
		$max_term_order = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT MAX( CAST( tm.meta_value AS UNSIGNED ) )
				FROM $wpdb->terms t
				JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = %s
				JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id WHERE tm.meta_key = 'tax_position'",
				$tax_slug
			)
		);
		$max_term_order = is_array( $max_term_order ) ? current( $max_term_order ) : 0;
		return 0 === (int) $max_term_order || empty( $max_term_order ) ? 1 : (int) $max_term_order + 1;
	}

	/**
	 * Re-Order the taxonomies based on the tax_position value.
	 *
	 * @param array $pieces     Array of SQL query clauses.
	 * @param array $taxonomies Array of taxonomy names.
	 * @param array $args       Array of term query args.
	 */
	public function set_tax_order( $pieces, $taxonomies, $args ) {
		foreach ( $taxonomies as $taxonomy ) {
			if ( $this->is_taxonomy_ordering_enabled( $taxonomy ) ) {
				global $wpdb;

				$join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = 'tax_position'";

				if ( ! $this->does_substring_exist( $pieces['join'], $join_statement ) ) {
					$pieces['join'] .= $join_statement;
				}
				$pieces['orderby'] = 'ORDER BY CAST( term_meta.meta_value AS UNSIGNED )';
			}
		}
		return $pieces;
	}

	/**
	 * Check if a substring exists inside a string.
	 *
	 * @param string $string    The main string (haystack) we're searching in.
	 * @param string $substring The substring we're searching for.
	 *
	 * @return bool True if substring exists, else false.
	 */
	protected function does_substring_exist( $string, $substring ) {
		return strstr( $string, $substring ) !== false;
	}

	/**
	 * AJAX Handler to update terms' tax position.
	 */
	public function update_taxonomy_order() {
		if ( ! check_ajax_referer( 'term_order_nonce', 'term_order_nonce', false ) ) {
			wp_send_json_error();
		}

		$taxonomy_ordering_data = filter_var_array( wp_unslash( $_POST['taxonomy_ordering_data'] ), FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore
		$base_index             = filter_input( INPUT_POST, 'base_index', FILTER_SANITIZE_NUMBER_INT );
		foreach ( $taxonomy_ordering_data as $order_data ) {

			// Due to the way WordPress shows parent categories on multiple pages, we need to check if the parent category's position should be updated.
			// If the category's current position is less than the base index (i.e. the category shouldn't be on this page), then don't update it.
			if ( $base_index > 0 ) {
				$current_position = get_term_meta( $order_data['term_id'], 'tax_position', true );
				if ( (int) $current_position < (int) $base_index ) {
					continue;
				}
			}

			update_term_meta( $order_data['term_id'], 'tax_position', ( (int) $order_data['order'] + (int) $base_index ) );
		}

		do_action( 'wpcm_taxonomy_order_updated', $taxonomy_ordering_data, $base_index );

		wp_send_json_success();
	}

	/**
	 * Check if ordering has been enabled for this taxonomy.
	 *
	 * @param string $tax_slug A taxonomy's slug.
	 *
	 * @return bool True if ordering is enabled.
	 */
	public function is_taxonomy_ordering_enabled( $tax_slug ) {
		$enabled_taxonomies = array( 'wpcm_season', 'wpcm_team', 'wpcm_comp', 'wpcm_position', 'wpcm_jobs' );

		return in_array( $tax_slug, $enabled_taxonomies );
	}
}

new WPCM_Taxonomy_Order();
