<?php
/**
 * Map Venue
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wpcm-map_venue-shortcode wpcm-map-venue">

    <?php echo ( $title ? '<h3>' . $title . '</h3>' : ''); 
    
    $map_service = get_option( 'wpcm_map_select', 'google' );
    if( $map_service == 'osm' ) { ?>

        <div id="mapid" style="height:280px;"></div>

        <script>

            var mymap = L.map('mapid').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], <?php echo $zoom; ?>);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: '<?php echo $api_key; ?>'
            }).addTo(mymap);

        </script>

    <?php
    } else { ?>

        <iframe class="wpcm-google-map wpcm-venue-map" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=<?php echo $api_key; ?>&amp;q=<?php echo $address; ?>&amp;center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&amp;zoom=<?php echo $zoom; ?>&amp;maptype=<?php echo $maptype; ?>" allowfullscreen></iframe>

    <?php
    } ?>

</div>