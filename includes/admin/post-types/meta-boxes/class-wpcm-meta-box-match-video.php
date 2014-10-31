<?php
/**
 * Match Video
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.2.5
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

		$video = get_post_meta( $post->ID, '_wpcm_video', true );

		if ( $video ): ?>
			<fieldset class="wpcm-video-embed">
				<?php echo apply_filters( 'the_content', '[embed width="254"]' . $video . '[/embed]' ); ?>
				<p><a href="#" class="wpcm-remove-video"><?php _e( 'Remove video', 'wpclubmanager' ); ?></a></p>
			</fieldset>
			<?php endif; ?>
			<fieldset class="wpcm-video-field hidden">
				<p><strong><?php _e( 'URL', 'wpclubmanager' ); ?></strong></p>
				<p><input class="widefat" type="text" name="_wpcm_video" id="wpcm_video" value="<?php echo $video; ?>"></p>
				<p><a href="#" class="wpcm-remove-video"><?php _e( 'Cancel', 'wpclubmanager' ); ?></a></p>
			</fieldset>
			<fieldset class="wpcm-video-adder<?php if ( $video ): ?> hidden<?php endif; ?>">
				<p><a href="#" class="wpcm-add-video"><?php _e( 'Add video', 'wpclubmanager' ); ?></a></p>
			</fieldset>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, '_wpcm_video', wpcm_array_value( $_POST, '_wpcm_video', null ) );
	}
}