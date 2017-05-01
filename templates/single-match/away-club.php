<?php
/**
 * Single Match - Away Club
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$side = wpcm_get_match_clubs( $post->ID ); ?>

<div class="wpcm-match-away-club">

	<?php echo $side[1]; ?>

</div>