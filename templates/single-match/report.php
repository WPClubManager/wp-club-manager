<?php
/**
 * Single Match - Report
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );

if ( $played ) {
					
	if ( get_the_content() ) { ?>
					
		<div class="wpcm-match-report">

			<h3><?php _e( 'Match Report', 'wp-club-manager' ); ?></h3>

			<div class="wpcm-entry-content">

				<?php the_content(); ?>

			</div>

		</div>

	<?php }
} else { 

	if ( has_excerpt() ) { ?>

		<div class="wpcm-match-report wpcm-match-preview">

			<h3><?php _e( 'Match Preview', 'wp-club-manager' ); ?></h3>

			<div class="wpcm-entry-content">

				<?php the_excerpt(); ?>

			</div>

		</div>

	<?php }

}