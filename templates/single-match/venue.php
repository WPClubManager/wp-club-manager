<?php
/**
 * Single Player Bio
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post;

$venues = get_the_terms( $post->ID, 'wpcm_venue' );

if ( is_array( $venues ) ) {
	$venue = reset($venues);
	$t_id = $venue->term_id;
	$venue_meta = get_option( "taxonomy_term_$t_id" );
} else {
	$venue = null;
}

if ( $venue ) { ?>

	<div class="wpcm-match-venue">

		<?php echo $venue->name; ?>

	</div>

<?php
}