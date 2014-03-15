<?php
/**
 * Single match away badge
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true ); ?>

<div class="wpcm-match-away-club-badge">

	<?php echo get_the_post_thumbnail( $away_club, 'crest-medium', array( 'class' => 'away-logo' ) ); ?>

</div>