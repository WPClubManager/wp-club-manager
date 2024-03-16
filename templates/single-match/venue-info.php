<?php
/**
 * Single Match - Venue Info
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$played     = get_post_meta( $post->ID, 'wpcm_played', true );
$venue_info = wpcm_get_match_venue( $post->ID );

if ( ! $played ) { ?>

	<div class="wpcm-match-venue-info">

		<h3><?php echo esc_html( $venue_info['name'] ); ?></h3>

		<?php
		if ( 'yes' === get_option( 'wpcm_results_show_map' ) ) {
			if ( $venue_info['address'] ) {
				echo do_shortcode( '[map_venue id="' . $venue_info['id'] . '" width="720" height="240" marker="1"]' );
			}
		}
		?>

		<div class="wpcm-match-venue-address">
			<?php
			if ( $venue_info['address'] ) {
				?>
				<h3><?php esc_html_e( 'Venue Address', 'wp-club-manager' ); ?></h3>

				<p class="address">
					<?php echo esc_html( stripslashes( nl2br( $venue_info['address'] ) ) ); ?>
				</p>
				<?php
			}
			if ( $venue_info['description'] ) {
				?>
				<p class="description">
					<?php esc_html( nl2br( $venue_info['description'] ) ); ?>
				</p>
				<?php
			}
			?>
		</div>

	</div>

	<?php
}
