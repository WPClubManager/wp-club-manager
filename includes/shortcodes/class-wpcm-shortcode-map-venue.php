<?php
/**
 * Venue Map Shortcode
 *
 * @author      Clubpress
 * @category    Shortcodes
 * @package     WPClubManager/Shortcodes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPCM_Shortcode_Map_Venue {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract( shortcode_atts( array(), $atts ) );

		$id     = ( isset( $atts['id'] ) ? $atts['id'] : -1 );
		$title  = ( isset( $atts['title'] ) ? $atts['title'] : '' );
		$width  = ( isset( $atts['width'] ) ? $atts['width'] : '' );
		$height = ( isset( $atts['height'] ) ? $atts['height'] : '' );

		if ( $width == '' ) {
			$width = '100%';
		}
		if ( $height == '' ) {
			$height = '320';
		}

		$term_meta = get_option( "taxonomy_term_$id" );
		$address   = $term_meta['wpcm_address'];

		$latitude  = ( isset( $term_meta['wpcm_latitude'] ) ? $term_meta['wpcm_latitude'] : null );
		$longitude = ( isset( $term_meta['wpcm_longitude'] ) ? $term_meta['wpcm_longitude'] : null );

		if ( $latitude == null && $longitude == null ) {

			$coordinates   = new WPCM_Geocoder( $address );
				$latitude  = $coordinates->lat;
				$longitude = $coordinates->lng;

		}

		// $address = urlencode($address);
		// $maptype = strtolower( $maptype );
		// if ( '' === $address ) $address = '+';
		// if ( 'satellite' !== $maptype ) $maptype = 'roadmap';

		$service = get_option( 'wpcm_map_select', 'google' );
		$zoom    = get_option( 'wpcm_map_zoom', 15 );

		if ( $service == 'osm' ) {

			// $assets_path = WPCM()->plugin_url() . '/assets/';
			// wp_enqueue_script( 'leaflet-maps', $assets_path . 'js/leaflet/leaflet.js', array(), '1.6.0', false );

			$layers = get_option( 'wpcm_osm_layer', 'standard' );

			if ( $layers = 'mapbox' ) {

				$api_key = get_option( 'wpcm_mapbox_api' );
				$maptype = get_option( 'wpcm_mapbox_type', 'mapbox/streets-v11' );

			} else {

				$api_key = false;
				$maptype = false;
			}
		} else {

			$address = urlencode( $address );
			$api_key = get_option( 'wpcm_google_map_api' );
			$maptype = get_option( 'wpcm_map_type', 'roadmap' );
			$layers  = '';

		}

		if ( $latitude != null && $longitude != null ) {

			wpclubmanager_get_template( 'shortcodes/map-venue.php', array(
				'title'     => $title,
				'width'     => $width,
				'height'    => $height,
				'address'   => $address,
				'longitude' => $longitude,
				'latitude'  => $latitude,
				'zoom'      => $zoom,
				'maptype'   => $maptype,
				'api_key'   => $api_key,
				'service'   => $service,
				'layers'    => $layers,
			) );

		}
	}
}
