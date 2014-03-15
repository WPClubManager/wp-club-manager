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

$post_id = $post->ID;
$match = get_post( $post_id );

$date = date_i18n( get_option( 'date_format' ), strtotime( $match->post_date ) );
$time = date_i18n( get_option( 'time_format' ), strtotime( $match->post_date ) ); ?>

<div class="wpcm-match-date">

	<?php echo $date; ?>, <?php echo $time; ?>

</div>