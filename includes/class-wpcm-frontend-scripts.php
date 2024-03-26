<?php
/**
 * Load frontend assets.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Frontend_Scripts
 */
class WPCM_Frontend_Scripts {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_head', array( $this, 'load_json_ld' ) );
	}

	/**
	 * Get styles for the frontend
	 *
	 * @return array
	 */
	public static function get_styles() {

		return apply_filters( 'wpclubmanager_enqueue_styles', array(
			'wpclubmanager-general' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WPCM()->plugin_url() ) . '/assets/css/wpclubmanager.css',
				'deps'    => '',
				'version' => WPCM_VERSION,
				'media'   => 'all',
			),
			'leaflet-styles'        => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WPCM()->plugin_url() ) . '/assets/js/vendor/leaflet/leaflet.css',
				'deps'    => '',
				'version' => '1.6.0',
				'media'   => 'all',
			),
		) );
	}

	/**
	 * Loads the scripts for the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function load_scripts() {

		global $post, $wp;

		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WPCM()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';
		$map_service          = get_option( 'wpcm_map_select', 'google' );

		if ( 'google' === $map_service ) {
			wp_register_script( 'google-maps-api', '//maps.google.com/maps/api/js?sensor=false' );
		} elseif ( 'osm' === $map_service ) {
			wp_enqueue_script( 'leaflet-maps', $assets_path . 'js/vendor/leaflet/leaflet.js' );
		}

		// Global frontend scripts
		wp_enqueue_script( 'wpclubmanager', $frontend_script_path . 'wpclubmanager.js', array( 'jquery' ), WPCM_VERSION, true );

		wp_localize_script( 'wpclubmanager', 'wpclubmanager_L10n', array(
			'days' => __( 'day', 'wpclubmanager' ),
			'hrs'  => __( 'hrs', 'wpclubmanager' ),
			'mins' => __( 'min', 'wpclubmanager' ),
			'secs' => __( 'sec', 'wpclubmanager' ),
		));

		// CSS Styles
		$enqueue_styles = $this->get_styles();

		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}
	}

	/**
	 * Loads the JSON-LD structured data.
	 *
	 * @return void
	 * @since  2.2.0
	 * @access public
	 */
	public function load_json_ld() {

		global $post;

		$club     = get_option( 'wpcm_default_club' );
		$post_url = get_permalink();
		if ( is_league_mode() ) {
			// $post_thumb = the_custom_logo();
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( has_custom_logo() ) {
				$post_thumb = $logo[0];
			} else {
				$post_thumb = '';
			}
		} else {
			$post_thumb = wp_get_attachment_url( get_post_thumbnail_id( $club ) );
		}

		if ( is_front_page() ) :

			$data['@context'] = 'http://schema.org/';
			$data['@type']    = 'Organization';
			$data['name']     = get_bloginfo( 'name' );
			$data['logo']     = $post_thumb;
			$data['url']      = site_url();

			/**
			 * Filters the front page LD+JSON schema.
			 *
			 * @since 2.2.5
			 *
			 * @param array $data SchemaOrg attribute-value pairs.
			 */
			$data = apply_filters( 'wpclubmanager_schema_front_page', $data );

			echo '<script type="application/ld+json">';
			echo json_encode( $data );
			echo '</script>';

		endif;

		if ( is_match() ) :

			$venues     = get_the_terms( $post->ID, 'wpcm_venue' );
			$venue_meta = false;
			$venue_name = '';
			if ( is_array( $venues ) ) {
				$venue      = reset( $venues );
				$t_id       = $venue->term_id;
				$venue_name = $venue->name;
				$venue_meta = get_option( 'taxonomy_term_' . $t_id );
			}
			if ( $venue_meta && is_array( $venue_meta ) ) {
				$address = $venue_meta['wpcm_address'];
			} else {
				$address = '';
			}

			$data['@context']  = 'http://schema.org/';
			$data['@type']     = 'SportsEvent';
			$data['name']      = $post->post_title;
			$data['image']     = $post_thumb;
			$data['url']       = $post_url;
			$data['location']  = array(
				'@type'   => 'Place',
				'name'    => $venue_name,
				'address' => array(
					'@type' => 'PostalAddress',
					'name'  => $address,
				),
			);
			$data['startDate'] = $post->post_date;

			/**
			 * Filters the SportsEvent LD+JSON schema.
			 *
			 * @since 2.2.5
			 *
			 * @param array $data SchemaOrg attribute-value pairs.
			 */
			$data = apply_filters( 'wpclubmanager_schema_sports_event', $data );

			echo '<script type="application/ld+json">';
			echo json_encode( $data );
			echo '</script>';

		endif;
	}
}

new WPCM_Frontend_Scripts();
