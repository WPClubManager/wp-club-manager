<?php
/**
 * Map Venue
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wpcm-map_venue-shortcode wpcm-map-venue">

    <?php echo ( $title ? '<h3>' . $title . '</h3>' : ''); 

    if( $service == 'osm' ) { ?>
        
        <div id="wpcm-osm-map" style="height:<?php echo $height; ?>px;"></div>

        <script>

            var mapOptions = {
                center: [<?php echo $latitude; ?>, <?php echo $longitude; ?>],
                zoom: <?php echo $zoom; ?>
            }
            var wpcm_map = new L.map('wpcm-osm-map', mapOptions);
            var myIcon = new L.Icon.Default();
            myIcon.options.shadowSize = [0,0];
            var marker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>], {icon: myIcon});
            
            <?php
            if( $layers === 'mapbox' ) { ?>

                var layer = new L.TileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap contributors</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: '<?php echo $maptype; ?>',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '<?php echo $api_key; ?>'
                });

            <?php
            } else { ?>

                var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
                });

            <?php
            } ?>
            
            wpcm_map.addLayer(layer);
            marker.addTo(wpcm_map);

      </script>

    <?php
    } else { ?>

        <iframe class="wpcm-google-map wpcm-venue-map" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=<?php echo $api_key; ?>&amp;q=<?php echo $address; ?>&amp;center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&amp;zoom=<?php echo $zoom; ?>&amp;maptype=<?php echo $maptype; ?>" allowfullscreen></iframe>

    <?php
    } ?>

</div>