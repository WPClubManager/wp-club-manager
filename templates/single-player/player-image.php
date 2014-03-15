<?php
/**
 * Single Player Image
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post; ?>

<div class="wpcm-profile-image">
			
	<?php if ( has_post_thumbnail() ) {
			
		echo the_post_thumbnail( 'player_single' );
		
	} else {
					
		echo apply_filters( 'wpclubmanager_single_product_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID );
				
	} ?>

</div>