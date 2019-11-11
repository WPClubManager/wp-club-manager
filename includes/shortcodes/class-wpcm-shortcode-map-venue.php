<?php
/**
 * Venue Map Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Map_Venue {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		
		extract(shortcode_atts(array(
        ), $atts));

		$id 		= ( isset( $atts['id'] ) ? $atts['id'] : -1 );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : '');
		$width 	    = ( isset( $atts['width'] ) ? $atts['width'] : '' );
		$height 	= ( isset( $atts['height'] ) ? $atts['height'] : '' );

		if( $width == '' ) $width = '100%';
		if( $height == '' ) $height = '320';

		$term_meta = get_option( "taxonomy_term_$id" );
		$address = $term_meta['wpcm_address'];
		$latitude = ( isset( $term_meta['wpcm_latitude'] ) ? $term_meta['wpcm_latitude'] : null );
		$longitude = ( isset( $term_meta['wpcm_longitude'] ) ? $term_meta['wpcm_longitude'] : null );
        if ( $latitude == null && $longitude == null ) {
			$coordinates = wpcm_decode_address( $address );
			if ( is_array ( $coordinates ) ) {
				$latitude = $coordinates['lat'];
				$longitude = $coordinates['lng'];
			}
		}
        $address = urlencode($address);
		$zoom = get_option( 'wpcm_map_zoom', 15 );
		$maptype = get_option( 'wpcm_map_type', 'roadmap' );
		$maptype = strtolower( $maptype );
		if ( '' === $address ) $address = '+';
		if ( 'satellite' !== $maptype ) $maptype = 'roadmap';

		$api_key = get_option( 'wpcm_google_map_api' );

		//ob_start();

		if ( $latitude != null && $longitude != null ) {

			wpclubmanager_get_template( 'shortcodes/map-venue.php', array(
				'title' 	=> $title,
				'width' 	=> $width,
				'height' 	=> $height,
				'address' 	=> $address,
				'longitude' => $longitude,
				'latitude' 	=> $latitude,
				'zoom' 		=> $zoom,
				'maptype' 	=> $maptype,
				'api_key'	=> $api_key
			) );

		}
		
		//return ob_get_clean();
        
    }
}