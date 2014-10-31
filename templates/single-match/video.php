<?php
/**
 * Single Match Video
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
	$id = get_the_ID();

$video_url = get_post_meta( $id, '_wpcm_video', true );

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