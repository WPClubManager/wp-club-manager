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

$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true ); ?>

<div class="wpcm-match-away-club">

	<?php echo get_the_title( $away_club ); ?>

</div>