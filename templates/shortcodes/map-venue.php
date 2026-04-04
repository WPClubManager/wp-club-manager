<?php
/**
 * Map Venue
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Initialize variables that may be set via extract() in the calling context.
$address   = isset( $address ) ? $address : '';
$api_key   = isset( $api_key ) ? $api_key : '';
$height    = isset( $height ) ? $height : 0;
$latitude  = isset( $latitude ) ? $latitude : '';
$layers    = isset( $layers ) ? $layers : '';
$longitude = isset( $longitude ) ? $longitude : '';
$maptype   = isset( $maptype ) ? $maptype : '';
$service   = isset( $service ) ? $service : '';
$title     = isset( $title ) ? $title : '';
$width     = isset( $width ) ? $width : '';
$zoom      = isset( $zoom ) ? $zoom : 0;
?>

<div class="wpcm-map_venue-shortcode wpcm-map-venue">

	<?php
	echo ( $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' );

	if ( 'osm' === $service ) {
		?>

		<div id="wpcm-osm-map" style="height:<?php echo esc_attr( $height ); ?>px;"></div>

		<script>

			var mapOptions = {
				center: [<?php echo esc_html( $latitude ); ?>, <?php echo esc_html( $longitude ); ?>],
				zoom: <?php echo esc_html( $zoom ); ?>
			}
			var wpcm_map = new L.map('wpcm-osm-map', mapOptions);
			var myIcon = new L.Icon.Default();
			myIcon.options.shadowSize = [0,0];
			var marker = L.marker([<?php echo esc_html( $latitude ); ?>, <?php echo esc_html( $longitude ); ?>], {icon: myIcon});

			<?php
			if ( 'mapbox' === $layers ) {
				?>

				var layer = new L.TileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap contributors</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
					maxZoom: 18,
					id: '<?php echo esc_html( $maptype ); ?>',
					tileSize: 512,
					zoomOffset: -1,
					accessToken: '<?php echo esc_html( $api_key ); ?>'
				});

				<?php
			} else {
				?>

				var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
				});

				<?php
			}
			?>

			wpcm_map.addLayer(layer);
			marker.addTo(wpcm_map);

		</script>

		<?php
	} else {
		// Build the embed URL — no API key needed for basic Google Maps embed.
		// Note: add_query_arg() handles URL-encoding internally.
		if ( empty( $api_key ) ) {
			$src_url = add_query_arg(
				array(
					'q'      => $address,
					'z'      => $zoom,
					'output' => 'embed',
				),
				'//maps.google.com/maps'
			);
		} else {
			$src_url = add_query_arg(
				array(
					'key'     => $api_key,
					'q'       => $address,
					'center'  => sprintf( '%s,%s', $latitude, $longitude ),
					'zoom'    => $zoom,
					'maptype' => $maptype,
				),
				'https://www.google.com/maps/embed/v1/search'
			);
		}
		?>

		<iframe class="wpcm-google-map wpcm-venue-map" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" frameborder="0" style="border:0" src="<?php echo esc_url( $src_url ); ?>" allowfullscreen></iframe>

		<?php
	}
	?>

</div>
