<?php
/**
 * Single Match - Attendance
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$attendance      = get_post_meta( $post->ID, 'wpcm_attendance', true );
$show_attendance = get_option( 'wpcm_results_show_attendance' );
$played          = get_post_meta( $post->ID, 'wpcm_played', true );

if ( $played ) {

	if ( $attendance && 'yes' === $show_attendance ) { ?>

		<div class="wpcm-match-attendance">

			<?php echo esc_html_e( 'Attendance', 'wp-club-manager' ); ?>: <?php echo esc_html( $attendance ); ?>

		</div>

		<?php
	}
}
