<?php
/**
 * Single Match - Date
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$date = date_i18n( apply_filters( 'wpclubmanager_match_long_date_format', get_option( 'date_format' ) ), strtotime( $post->post_date ) );
$time = date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), strtotime( $post->post_date ) ); ?>

<div class="wpcm-match-date">
	<?php echo esc_html( $date ); ?>, <?php echo esc_html( $time ); ?>
</div>
