<?php
/**
 * Single Match - Status
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );

if( $overtime || $shootout ) { ?>

	<div class="wpcm-match-status">

		<?php do_action( 'wpclubmanager_before_match_status' ); ?>

		<?php
		if( $overtime ) { ?>

			<span class="wpcm-match-overtime"><?php _e( 'AET', 'wp-club-manager'); ?></span>

		<?php
		}

		if( $shootout ) {

			$home_goals = get_post_meta( $post->ID, '_wpcm_home_shootout_goals', true );
			$away_goals = get_post_meta( $post->ID, '_wpcm_away_shootout_goals', true );
			$delimiter = get_option( 'wpcm_match_goals_delimiter' ); ?>

			<span class="wpcm-match-shootout"><?php _e( 'Pens:', 'wp-club-manager'); ?> <?php echo $home_goals; ?> <?php echo $delimiter; ?> <?php echo $away_goals; ?></span>
		
		<?php
		} ?>

		<?php do_action( 'wpclubmanager_after_match_status' ); ?>

	</div>

<?php
}