<?php
/**
 * Player Stats Display
 *
 * Displays the player stats display.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPCM_Meta_Box_Player_Display {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

        wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

        $stats_labels = wpcm_get_player_stats_names();
		$custom_stats = get_post_meta( $post->ID, '_wpcm_custom_player_stats', true )?>

		<div class="wpcm-profile-stats-custom">
            <?php
            foreach( $stats_labels as $key => $val ):
                if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
                    
                        <label>
                            <input type="checkbox" name="_wpcm_custom_player_stats[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="" <?php echo ( is_array( $custom_stats ) ? checked( array_key_exists( $key, $custom_stats ) ) : 'checked' ); ?> />
                            <?php echo $val; ?>
                        </label>
                    
                <?php endif;
            endforeach; ?>
        </div>

    <?php    
    }

    /**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

        $player_stats = $_POST['_wpcm_custom_player_stats'];
		update_post_meta( $post_id, '_wpcm_custom_player_stats', $player_stats );
    }
}