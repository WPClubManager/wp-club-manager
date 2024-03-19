<?php
/**
 * WPCM Geocoder.
 * Code adapted from Leaflet Map plugin by bozdoz - https://wordpress.org/plugins/leaflet-map/
 *
 * @class       WPCM_Geocoder
 * @version     2.2.0
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      Clubpress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Geocoder
 */
class WPCM_Geocoder {

	/**
	 * Geocoder should return this on error/not found
	 *
	 * @var array $not_found
	 */
	private $not_found = array(
		'lat' => 0,
		'lng' => 0,
	);
	/**
	 * Latitude
	 *
	 * @var float $lat
	 */
	public $lat = 0;
	/**
	 * Longitude
	 *
	 * @var float $lng
	 */
	public $lng = 0;

	/**
	 * New Geocoder from address
	 *
	 * Handles url encoding and caching
	 *
	 * @param string $address the requested address to look up
	 */
	public function __construct( $address ) {

		$address = urlencode( $address );

		$geocoder = get_option( 'wpcm_map_select', 'google' );

		$geocoding_method = $geocoder . '_geocode';

		try {

			$location = (object) $this->$geocoding_method( $address );

		} catch ( Exception $e ) {
			// failed
			$location = $this->not_found;
		}

		$this->lat = $location->lat;
		$this->lng = $location->lng;
	}

	/**
	 * Used by geocoders to make requests via curl or file_get_contents
	 *
	 * @param string $url
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	private function get_url( $url ) {
		$referer = get_site_url();

		if ( in_array( 'curl', get_loaded_extensions() ) ) {
			/* try curl */
			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_REFERER, $referer );
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

			$data = curl_exec( $ch );
			curl_close( $ch );

			return $data;
		} elseif ( ini_get( 'allow_url_fopen' ) ) {
			/* try file get contents */

			$opts    = array(
				'http' => array(
					'header' => array( "Referer: $referer\r\n" ),
				),
			);
			$context = stream_context_create( $opts );

			return file_get_contents( $url, false, $context );
		}

		$error_msg = 'Could not get url: ' . $url;
		throw new Exception( esc_html( $error_msg ) );
	}

	/**
	 * Google geocoder (https://developers.google.com/maps/documentation/geocoding/start)
	 *
	 * @param string $address
	 *
	 * @return object
	 * @throws Exception
	 */
	private function google_geocode( $address ) {

		$key = get_option( 'wpcm_google_map_api' );

		$geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s';
		$geocode_url = sprintf( $geocode_url, $address, $key );

		$json = $this->get_url( $geocode_url );
		$json = json_decode( $json );

		/* found location */
		if ( 'OK' === $json->status ) {

			$location = $json->results[0]->geometry->location;

			return (object) $location;
		}

		throw new Exception( 'No Address Found' );
	}

	/**
	 * OpenStreetMap geocoder Nominatim (https://nominatim.openstreetmap.org/)
	 *
	 * @param string $address    the urlencoded address to look up
	 * @return varies object from API or null (failed)
	 */
	private function osm_geocode( $address ) {
		$geocode_url  = 'https://nominatim.openstreetmap.org/?format=json&limit=1&q=';
		$geocode_url .= $address;
		$json         = $this->get_url( $geocode_url );
		$json         = json_decode( $json );

		return (object) array(
			'lat' => $json[0]->lat,
			'lng' => $json[0]->lon,
		);
	}
}
