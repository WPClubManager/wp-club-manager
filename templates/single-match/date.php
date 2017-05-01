<?php
/**
 * Single Match - Date
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$date = date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) );
$time = date_i18n( get_option( 'time_format' ), strtotime( $post->post_date ) ); ?>

<div class="wpcm-match-date">
	<?php echo $date; ?>, <?php echo $time; ?>
</div>