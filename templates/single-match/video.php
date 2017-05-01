<?php
/**
 * Single Match - Video
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$video_url = get_post_meta( $post->ID, '_wpcm_video', true );

if ( $video_url ):
	?>
	<div class="wpcm-match-video">
		<?php
	    global $wp_embed;
	    echo $wp_embed->autoembed( $video_url );
	    ?>
	</div>
    <?php
endif;
?>