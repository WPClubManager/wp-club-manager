<?php
/**
 * Match Video
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Match_Video
 */
class WPCM_Meta_Box_Match_Video {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$video = get_post_meta( $post->ID, '_wpcm_video', true );

		if ( $video ) : ?>
			<fieldset class="wpcm-video-embed">
				<?php echo apply_filters( 'the_content', '[embed width="254"]' . $video . '[/embed]' ); // phpcs:ignore ?>
				<p><a href="#" class="wpcm-remove-video"><?php esc_html_e( 'Remove video', 'wp-club-manager' ); ?></a></p>
			</fieldset>
			<?php endif; ?>
			<fieldset class="wpcm-video-field hidden">
				<p><strong><?php esc_html_e( 'URL', 'wp-club-manager' ); ?></strong></p>
				<p><input class="widefat" type="text" name="_wpcm_video" id="wpcm_video" value="<?php echo esc_url( $video ); ?>"></p>
				<p><a href="#" class="wpcm-remove-video"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a></p>
			</fieldset>
			<fieldset class="wpcm-video-adder
			<?php
			if ( $video ) :
				?>
				hidden<?php endif; ?>">
				<p><a href="#" class="wpcm-add-video"><?php esc_html_e( 'Add video', 'wp-club-manager' ); ?></a></p>
			</fieldset>
		<?php
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$video = filter_input( INPUT_POST, '_wpcm_video', FILTER_UNSAFE_RAW );
		if ( isset( $video ) ) {
			update_post_meta( $post_id, '_wpcm_video', sanitize_url( $video ) );
		}
	}
}
