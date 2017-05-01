<?php
/**
 * Single Match - Venue Info
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$venues = get_the_terms( $post->ID, 'wpcm_venue' );
$played = get_post_meta( $post->ID, 'wpcm_played', true );

if ( is_array( $venues ) ) {
	$venue = reset($venues);
	$t_id = $venue->term_id;
	$venue_meta = get_option( "taxonomy_term_$t_id" );
	$address = $venue_meta['wpcm_address'];
} else {
	$venue = null;
	$address = null;
}

if ( ! $played ) { ?>

	<div class="wpcm-match-venue-info">

		<h3><?php echo $venue->name; ?></h3>

		<?php
		if ( $address ) {
			echo do_shortcode( '[wpcm_map address="' . $address . '" width="720" height="240" marker="1"]' );
		}
		?>

		<div class="wpcm-match-venue-address<?php echo ( $address ? ' with-map' : '' ); ?>">
			
		<?php
		if ( $address ) { ?>
			<h3><?php _e('Venue Address', 'wp-club-manager'); ?></h3>

			<p class="address">
				<?php echo stripslashes( nl2br( $address ) ); ?>
			</p>
		<?php
		}

		if ( $venue->description ) { ?>
			<p class="description">
				<?php nl2br( $venue->description ); ?>
			</p>
		<?php } ?>

		</div>

	</div>
					
<?php
}