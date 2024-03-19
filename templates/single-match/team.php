<?php
/**
 * Single Match - Team
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$team      = wpcm_get_match_team( $post->ID );
$show_team = get_option( 'wpcm_results_show_team' );

if ( 'yes' === $show_team && $team ) { ?>

	<div class="wpcm-match-team">
		<?php echo esc_html( $team[0] ); ?>
	</div>

	<?php
}
