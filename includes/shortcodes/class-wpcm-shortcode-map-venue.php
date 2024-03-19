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

/**
 * WPCM_Shortcode_Map_Venue
 */
class WPCM_Shortcode_Map_Venue {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract( shortcode_atts( array(), $atts ) ); // phpcs:ignore

		$id     = ( isset( $atts['id'] ) ? $atts['id'] : -1 );
		$title  = ( isset( $atts['title'] ) ? $atts['title'] : '' );
		$width  = ( isset( $atts['width'] ) ? $atts['width'] : '' );
		$height = ( isset( $atts['height'] ) ? $atts['height'] : '' );

		if ( '' === $width ) {
			$width = '100%';
		}
		if ( '' === $height ) {
			$height = '320';
		}

		$term_meta = get_option( "taxonomy_term_$id" );
		$address   = $term_meta['wpcm_address'];

		$latitude  = ( isset( $term_meta['wpcm_latitude'] ) ? $term_meta['wpcm_latitude'] : null );
		$longitude = ( isset( $term_meta['wpcm_longitude'] ) ? $term_meta['wpcm_longitude'] : null );

		if ( ! $latitude && ! $longitude ) {
			$coordinates = new WPCM_Geocoder( $address );
			$latitude    = $coordinates->lat;
			$longitude   = $coordinates->lng;
		}

		$service = get_option( 'wpcm_map_select', 'google' );
		$zoom    = get_option( 'wpcm_map_zoom', 15 );

		if ( 'osm' === $service ) {
			$layers = get_option( 'wpcm_osm_layer', 'standard' );

			if ( 'mapbox' === $layers ) {
				$api_key = get_option( 'wpcm_mapbox_api' );
				$maptype = get_option( 'wpcm_mapbox_type', 'mapbox/streets-v11' );

			} else {
				$api_key = false;
				$maptype = false;
			}
		} else {
			$address = rawurlencode( $address );
			$api_key = get_option( 'wpcm_google_map_api' );
			$maptype = get_option( 'wpcm_map_type', 'roadmap' );
			$layers  = '';

		}

		if ( null !== $latitude && null !== $longitude ) {

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
