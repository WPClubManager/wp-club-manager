<?php
/**
 * Add Club to League Table
 *
 * Displays the add club to league table box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Club_Table
 */
class WPCM_Meta_Box_Club_Table {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ); ?>

		<p>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'add_to_table',
				'id'               => 'add_to_table',
				'post_type'        => 'wpcm_table',
				'limit'            => -1,
				'show_option_none' => __( 'None', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'order'            => 'DESC',
				'orderby'          => 'date',
				'echo'             => false,
			));
			?>
		</p>

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

		$table_id = filter_input( INPUT_POST, 'add_to_table', FILTER_VALIDATE_INT );
		if ( isset( $table_id ) && null != $table_id ) {
			$clubs = (array) unserialize( get_post_meta( $table_id, '_wpcm_table_clubs', true ) );
			if ( ! in_array( $post_id, $clubs ) ) {
				array_push( $clubs, intval( $post_id ) );
				update_post_meta( $table_id, '_wpcm_table_clubs', serialize( $clubs ) );
			}
		}
	}
}
