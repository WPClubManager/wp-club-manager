<?php
/**
 * Single Match - Venue
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$venue = wpcm_get_match_venue( $post->ID );

if ( $venue ) { ?>

	<div class="wpcm-match-venue">

		<?php echo $venue['name']; ?>

	</div>

<?php
}