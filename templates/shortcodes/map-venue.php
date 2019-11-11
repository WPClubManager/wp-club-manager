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

	<?php echo ( $title ? '<h3>' . $title . '</h3>' : ''); ?>

    <iframe class="wpcm-google-map wpcm-venue-map" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=<?php echo $api_key; ?>&amp;q=<?php echo $address; ?>&amp;center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&amp;zoom=<?php echo $zoom; ?>&amp;maptype=<?php echo $maptype; ?>" allowfullscreen></iframe>

</div>