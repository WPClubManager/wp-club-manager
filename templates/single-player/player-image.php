<?php
/**
 * Single Player - Image
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post; ?>

<div class="wpcm-profile-image">
			
	<?php echo wpcm_get_player_thumbnail( $post->ID, 'player_single' ); ?>

</div>