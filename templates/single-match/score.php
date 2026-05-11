<?php
/**
 * Single Match - Score
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$played    = get_post_meta( $post->ID, 'wpcm_played', true );
$score     = wpcm_get_match_result( $post->ID );
$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true ); ?>

<div class="wpcm-match-score">

	<?php echo esc_html( $score[1] ); ?>

	<?php if ( $away_club ) : ?>
		<span class="wpcm-match-score-delimiter"><?php echo esc_html( $played ? $score[3] : get_option( 'wpcm_match_clubs_separator' ) ); ?></span>

		<?php echo esc_html( $score[2] ); ?>
	<?php endif; ?>

</div>
