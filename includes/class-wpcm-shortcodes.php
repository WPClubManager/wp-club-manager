<?php
/**
 * WPCM_Shortcodes class.
 *
 * @class 		WPCM_Shortcodes
 * @version		2.0.0
 * @package		WPClubManager/Classes
 * @category	Class
 * @author 		ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcodes {

	public function __construct() {

		add_action( 'wp_head', array( $this, 'wpcm_map_css' ) );
	}

	/**
	 * Init shortcodes
	 */
	public static function init() {

		// Define shortcodes
		$shortcodes = array(
			'match_list'               		=> __CLASS__ . '::match_list',
			'player_list'					=> __CLASS__ . '::player_list',
			'player_gallery'				=> __CLASS__ . '::player_gallery',
			'staff_list'					=> __CLASS__ . '::staff_list',
			'league_table'					=> __CLASS__ . '::league_table',
			'map_venue'						=> __CLASS__ . '::map_venue',

			//OLD SHORTCODES
			'wpcm_map'               		=> __CLASS__ . '::map',
			'wpcm_matches'         			=> __CLASS__ . '::matches',
			'wpcm_players'            		=> __CLASS__ . '::players',
			'wpcm_staff'            		=> __CLASS__ . '::staff',
			'wpcm_standings'              	=> __CLASS__ . '::standings',
		);

		if( is_club_mode() ){
			$shortcodes['match_opponents'] = __CLASS__ . '::match_opponents';
		}

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( $shortcode, $function );
		}
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'wpcm-shortcode-wrapper',
			'before' => null,
			'after'  => null
		) ) {
			
		$wrapper = apply_filters( 'wpclubmanager_shortcode_wrapper', $wrapper, $function, $atts );

		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

	/**
	 * Match List shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function match_list( $atts ) {

		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Match_List', 'output' ), $atts );
	}

	/**
	 * League Match List shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function match_opponents( $atts ) {

		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Match_Opponents', 'output' ), $atts );
	}

	/**
	 * Player List shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function player_list( $atts ) {
		
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Player_List', 'output' ), $atts );
	}

	/**
	 * Player Roster shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function player_gallery( $atts ) {
		
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Player_Gallery', 'output' ), $atts );
	}

	/**
	 * Staff List shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function staff_list( $atts ) {
		
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Staff_List', 'output' ), $atts );
	}

	
	/**
	 * Standings Table shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function league_table( $atts ) {
		
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_League_Table', 'output' ), $atts );
	}

	/**
	 * Map shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	 public static function map_venue( $atts ) {
		
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Map_Venue', 'output' ), $atts );
	}







	//OLD SHORTCODES
	/**
	 * Matches shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function matches( $atts ) {
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Matches', 'output' ), $atts );
	}

	/**
	 * Players shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function players( $atts ) {
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Players', 'output' ), $atts );
	}

	/**
	 * Standings shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function standings( $atts ) {
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Standings', 'output' ), $atts );
	}

	/**
	 * Staff shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function staff( $atts ) {
		return self::shortcode_wrapper( array( 'WPCM_Shortcode_Staff', 'output' ), $atts );
	}

	/**
	 * Display google map shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function map( $atts ) {

		$atts = shortcode_atts( array(
			'width' 		=> '584',
			'height' 		=> '320',
			'address' 		=> false,
			'lat' 			=> false,
			'lng' 			=> false,
			'zoom' 			=> '13',
			'marker'    	=> 1,
			'infowindow'	=> false,
		), $atts );
		// if ( is_array( $venues ) ) {
		// 	$venue = reset($venues);
		// 	$name = $venue->name;
		// 	$t_id = $venue->term_id;
		// 	$venue_meta = get_option( "taxonomy_term_$t_id" );
		// 	$address = $venue_meta['wpcm_address'];
		// } else {
		// 	$name = null;
		// 	$address = null;
		// }
		
		//$api_key = urlencode( get_option( 'wpcm_google_map_api') );
		
		if ( $atts['address'] ) {
			$coordinates = wpcm_decode_address( $atts['address'] );
			if ( is_array ( $coordinates ) ) {
				$latitude = $coordinates['lat'];
				$longitude = $coordinates['lng'];
			}
		}
		$address = urlencode($atts['address']);
		
		//$map_id = uniqid( 'wpcm_map_' );
		
		// show marker or not
		//$atts['marker'] = (int) $atts['marker'] ? true : false;
		$api_key = get_option( 'wpcm_google_map_api' );
		$zoom = get_option( 'wpcm_map_zoom', 15 );
		$maptype = get_option( 'wpcm_map_type', 'roadmap' );
		$maptype = strtolower( $maptype );
		if ( '' === $address ) $address = '+';
		if ( 'satellite' !== $maptype ) $maptype = 'roadmap';
		if( is_tax( 'wpcm_venue' ) ) $class = 'wpcm-venue-map';

		ob_start();
		if ( $latitude != null && $longitude != null ):
			?>
			<iframe
			class="wpcm-google-map <?php echo $class; ?>"
			width="600"
			height="320"
			frameborder="0" style="border:0"
			src="https://www.google.com/maps/embed/v1/search?key=<?php echo $api_key; ?>&amp;q=<?php echo $address; ?>&amp;center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&amp;zoom=<?php echo $zoom; ?>&amp;maptype=<?php echo $maptype; ?>" allowfullscreen>
			</iframe>
			<?php
		endif;
		
		return ob_get_clean();
	}

}