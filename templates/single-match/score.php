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

$home_goals = get_post_meta( $post->ID, 'wpcm_home_goals', true );
$away_goals = get_post_meta( $post->ID, 'wpcm_away_goals', true );
$played = get_post_meta( $post->ID, 'wpcm_played', true ); ?>

<div class="wpcm-match-score">

	<?php if ( $played ) {

		echo $home_goals;

	} ?>

	<span class="wpcm-match-score-delimiter"><?php echo get_option( 'wpcm_match_goals_delimiter' ); ?></span>

	<?php if ( $played ) {

		echo $away_goals;

	} ?>

</div>