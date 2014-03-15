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

$played = get_post_meta( $post->ID, 'wpcm_played', true );

if ( $played ) {
					
	if ( get_the_content() ) { ?>
					
		<div class="wpcm-match-report">

			<h3><?php _e( 'Match Report', 'wpclubmanager' ); ?></h3>

			<div class="wpcm-entry-content">

				<?php the_content(); ?>

			</div>

		</div>

	<?php }
}