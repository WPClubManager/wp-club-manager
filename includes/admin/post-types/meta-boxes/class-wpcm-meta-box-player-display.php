<?php
/**
 * Player Stats Display
 *
 * Displays the player stats display.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPCM_Meta_Box_Player_Display
 */
class WPCM_Meta_Box_Player_Display {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$stats_labels = wpcm_get_player_stats_names();
		$custom_stats = get_post_meta( $post->ID, '_wpcm_custom_player_stats', true )?>

		<div class="wpcm-profile-stats-custom">
			<?php
			foreach ( $stats_labels as $key => $val ) :
				if ( 'yes' === get_option( 'wpcm_show_stats_' . $key ) ) :
					?>

						<label>
							<input type="checkbox" name="_wpcm_custom_player_stats[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="" <?php echo ( is_array( $custom_stats ) ? checked( array_key_exists( $key, $custom_stats ) ) : 'checked' ); ?> />
							<?php echo esc_html( $val ); ?>
						</label>

					<?php
				endif;
			endforeach;
			?>
		</div>

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

		$player_stats_data = filter_input( INPUT_POST, '_wpcm_custom_player_stats', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$player_stats      = array();
		foreach ( $player_stats_data as $key => $value ) {
			$player_stats[ sanitize_text_field( $key ) ] = '';
		}
		update_post_meta( $post_id, '_wpcm_custom_player_stats', $player_stats );
	}
}
