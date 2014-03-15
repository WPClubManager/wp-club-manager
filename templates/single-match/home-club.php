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

$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true ); ?>

<div class="wpcm-match-home-club">

	<?php echo get_the_title( $home_club ); ?>

</div>