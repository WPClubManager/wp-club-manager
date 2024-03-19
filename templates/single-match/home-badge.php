<?php
/**
 * Single match - Home Badge
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.1.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$badges = wpcm_get_match_badges( $post->ID, 'crest-medium', array( 'class' => 'home-logo' ) ); ?>

<div class="wpcm-match-home-club-badge">

	<?php echo wp_kses_post( $badges[0] ); ?>

</div>
