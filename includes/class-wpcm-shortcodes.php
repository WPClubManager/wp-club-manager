<?php
/**
 * WPCM_Shortcodes class.
 *
 * @class 		WPCM_Shortcodes
 * @version		1.4.9
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
			'class'  => 'wpcm-shortcode-wrapper',
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
		
		$api_key = urlencode( get_option( 'wpcm_google_map_api') );

		if ( $atts['address'] ) {
			$coordinates = wpcm_decode_address( $atts['address'] );
			if ( is_array ( $coordinates ) ) {
				$atts['lat'] = $coordinates['lat'];
				$atts['lng'] = $coordinates['lng'];
			}
		}
		
		$map_id = uniqid( 'wpcm_map_' );
		
		// show marker or not
		$atts['marker'] = (int) $atts['marker'] ? true : false;

		ob_start(); ?>
		<div class="wpcm_map_canvas" id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo esc_attr( $atts['height'] ); ?>px; width: <?php echo esc_attr( $atts['width'] ); ?>px"></div>
	    <script type="text/javascript">
			var map_<?php echo $map_id; ?>;
			var marker_<?php echo $map_id; ?>;
			var infowindow_<?php echo $map_id; ?>;
			//var geocoder = new google.maps.Geocoder();
			function wp_gmaps_<?php echo $map_id; ?>() {
				var location = { lat: <?php echo esc_attr( $atts['lat'] ); ?>, lng: <?php echo esc_attr( $atts['lng'] ); ?> };
				var map_options = {
					zoom: <?php echo esc_attr( $atts['zoom'] ) ?>,
					center: location,
					scrollwheel: false,
					gestureHandling: 'cooperative',
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				map_<?php echo $map_id; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id; ?>"), map_options);
				
				<?php if ( $atts['marker'] ): ?>
					marker_<?php echo $map_id ?> = new google.maps.Marker({
						position: location,
						map: map_<?php echo $map_id; ?>
					});
				
					<?php if ( $atts['infowindow'] ): ?>
						infowindow_<?php echo $map_id; ?> = new google.maps.InfoWindow({
							content: '<?php echo esc_attr( $atts['infowindow'] ) ?>'
						});
						google.maps.event.addListener(marker_<?php echo $map_id ?>, 'click', function() {
							infowindow_<?php echo $map_id; ?>.open(map_<?php echo $map_id; ?>, marker_<?php echo $map_id ?>);
						});
					<?php endif; ?>
				<?php endif; ?>
			}
			wp_gmaps_<?php echo $map_id; ?>();
		</script>
		<script async defer src="//maps.googleapis.com/maps/api/js?key=<?php echo $api_key ?>&callback=wp_gmaps_<?php echo $map_id; ?>"></script>
		<?php
		
		return ob_get_clean();
	}

}