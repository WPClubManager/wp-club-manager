<?php
/**
 * Match Video
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPCM_Meta_Box_Match_Video
 */
class WPCM_Meta_Box_Match_Video {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$video = get_post_meta( $post->ID, '_wpcm_video', true );

		if ( $video ): ?>
			<fieldset class="wpcm-video-embed">
				<?php echo apply_filters( 'the_content', '[embed width="254"]' . $video . '[/embed]' ); ?>
				<p><a href="#" class="wpcm-remove-video"><?php _e( 'Remove video', 'wp-club-manager' ); ?></a></p>
			</fieldset>
			<?php endif; ?>
			<fieldset class="wpcm-video-field hidden">
				<p><strong><?php _e( 'URL', 'wp-club-manager' ); ?></strong></p>
				<p><input class="widefat" type="text" name="_wpcm_video" id="wpcm_video" value="<?php echo $video; ?>"></p>
				<p><a href="#" class="wpcm-remove-video"><?php _e( 'Cancel', 'wp-club-manager' ); ?></a></p>
			</fieldset>
			<fieldset class="wpcm-video-adder<?php if ( $video ): ?> hidden<?php endif; ?>">
				<p><a href="#" class="wpcm-add-video"><?php _e( 'Add video', 'wp-club-manager' ); ?></a></p>
			</fieldset>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['_wpcm_video'] ) ) {
			update_post_meta( $post_id, '_wpcm_video', $_POST['_wpcm_video'] );
		}
	}
}