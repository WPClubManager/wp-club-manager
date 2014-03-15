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
$teams = get_the_terms( $match->ID, 'wpcm_team' );
$show_team = get_option( 'wpcm_results_show_team' );

if ( $show_team == 'yes' && is_array( $teams ) ) { ?>

	<div class="wpcm-match-team">

	<?php foreach ( $teams as $team ) {

		echo $team->name; ?><br />

	<?php
	} ?>
	</div>
<?php
}