<?php
/**
 * Single match home badge
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true ); ?>

<div class="wpcm-match-home-club-badge">

	<?php echo get_the_post_thumbnail( $home_club, 'crest-medium', array( 'class' => 'home-logo' ) ); ?>

</div>