<?php
/**
 * Single Match - Score
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );
$score = wpcm_get_match_result( $post->ID ); ?>

<div class="wpcm-match-score">

	<?php echo $score[1]; ?>

	<span class="wpcm-match-score-delimiter"><?php echo ( $played ? $score[3] : get_option( 'wpcm_match_clubs_separator' ) ); ?></span>

	<?php echo $score[2]; ?>

</div>