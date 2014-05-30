<?php
/**
 * WPCM_Shortcodes class.
 *
 * @class 		WPCM_Shortcodes
 * @version		1.1.0
 * @package		WPClubManager/Classes
 * @category	Class
 * @author 		ClubPress
 */

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
			'wpcm_map'               		=> __CLASS__ . '::map',
			'wpcm_matches'         			=> __CLASS__ . '::matches',
			'wpcm_players'            		=> __CLASS__ . '::players',
			'wpcm_staff'            		=> __CLASS__ . '::staff',
			'wpcm_standings'              	=> __CLASS__ . '::standings',
		);

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
			'class'  => 'wpcm',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

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

		$atts = shortcode_atts(
			array(
				'address' 	=> false,
				'width' 	=> '100%',
				'height' 	=> '400px'
			),
			$atts
		);

		$address = $atts['address'];

		if( $address ) :

			wp_print_scripts( 'google-maps-api' );

			$coordinates = self::wpcm_map_get_coordinates( $address );

			if( !is_array( $coordinates ) )
				return;

			$map_id = uniqid( 'wpcm_map_' ); // generate a unique ID for this map

			ob_start(); ?>
			<div class="wpcm-match-map-canvas" id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo esc_attr( $atts['height'] ); ?>; width: <?php echo esc_attr( $atts['width'] ); ?>"></div>
		    <script type="text/javascript">
				var map_<?php echo $map_id; ?>;
				function wpcm_run_map_<?php echo $map_id ; ?>(){
					var location = new google.maps.LatLng("<?php echo $coordinates['lat']; ?>", "<?php echo $coordinates['lng']; ?>");
					var map_options = {
						zoom: 15,
						center: location,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);
					var marker = new google.maps.Marker({
					position: location,
					map: map_<?php echo $map_id ; ?>
					});
				}
				wpcm_run_map_<?php echo $map_id ; ?>();
			</script>
			<?php
		endif;
		return ob_get_clean();
	}

	public static function wpcm_map_get_coordinates( $address, $force_refresh = false ) {

	    $address_hash = md5( $address );

	    $coordinates = get_transient( $address_hash );

	    if ($force_refresh || $coordinates === false) {

	    	$args       = array( 'address' => urlencode( $address ), 'sensor' => 'false' );
	    	$url        = add_query_arg( $args, 'http://maps.googleapis.com/maps/api/geocode/json' );
	     	$response 	= wp_remote_get( $url );

	     	if( is_wp_error( $response ) )
	     		return;

	     	$data = wp_remote_retrieve_body( $response );

	     	if( is_wp_error( $data ) )
	     		return;

			if ( $response['response']['code'] == 200 ) {

				$data = json_decode( $data );

				if ( $data->status === 'OK' ) {

				  	$coordinates = $data->results[0]->geometry->location;

				  	$cache_value['lat'] 	= $coordinates->lat;
				  	$cache_value['lng'] 	= $coordinates->lng;
				  	$cache_value['address'] = (string) $data->results[0]->formatted_address;

				  	// cache coordinates for 3 months
				  	set_transient($address_hash, $cache_value, 3600*24*30*3);
				  	$data = $cache_value;

				} elseif ( $data->status === 'ZERO_RESULTS' ) {
				  	return __( 'No location found for the entered address.', 'pw-maps' );
				} elseif( $data->status === 'INVALID_REQUEST' ) {
				   	return __( 'Invalid request. Did you enter an address?', 'pw-maps' );
				} else {
					return __( 'Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'pw-maps' );
				}

			} else {
			 	return __( 'Unable to contact Google API service.', 'pw-maps' );
			}

	    } else {
	       // return cached results
	       $data = $coordinates;
	    }

	    return $data;
	}

}