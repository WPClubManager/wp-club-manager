<?php
/**
 * WPClubManager sports. Code adapted from SportsPress
 *
 * @class       WPCM_Sports
 * @version     1.1.0
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      Clubpress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Sports
 */
class WPCM_Sports {

	/** @var array Array of sports */
	private $data;

	/**
	 * Constructor for the sports class - defines all preset sports.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->data = wpcm_get_sport_presets();
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function __get( $key ) {
		return ( array_key_exists( $key, $this->data ) ? $this->data[ $key ] : null );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->data[ $key ] = $value;
	}
}
