<?php
/**
 * Link Player to User
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Player_Users
 */
class WPCM_Meta_Box_Player_Users {

	/**
	 * Check if user has "Administrator" role assigned
	 *
	 * @global wpdb $wpdb
	 * @param int $user_id
	 * @return boolean returns true is user has Role "Administrator"
	 */
	public static function has_administrator_role( $user_id ) {
		global $wpdb;

		if ( empty( $user_id ) || ! is_numeric( $user_id ) ) {
			return false;
		}

		$meta_key       = $wpdb->prefix . 'capabilities';
		$has_admin_role = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$wpdb->prefix}usermeta WHERE user_id=%d AND meta_key=%s AND meta_value like %s", $user_id, $meta_key, '%administrator%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( $has_admin_role > 0 ) {
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		if ( 'publish' === $post->post_status ) {

			do_action( 'wpclubmanager_before_admin_player_user_meta', $post->ID );

			$user      = get_post_meta( $post->ID, '_wpcm_link_users', true );
			$user_data = get_userdata( $user );
			$edit_link = ( isset( $user ) && ! empty( $user ) ? '<a href="' . esc_url( get_edit_user_link( $user ) ) . '">' . esc_html__( 'Edit user', 'wp-club-manager' ) . '</a>' : '' ); ?>

			<p><strong><?php esc_html_e( 'Existing user', 'wp-club-manager' ); ?></strong></p>

			<p>
				<label><?php esc_html_e( 'Choose User', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'show_option_none' => __( 'None', 'wp-club-manager' ),
					'name'             => 'wpcm_link_users',
					'selected'         => $user,
					'include_selected' => true,
					'class'            => 'chosen_select',
				);

				wp_dropdown_users( $args );
				?>
				<span class="edit-user-player"><?php echo $edit_link; // phpcs:ignore ?></span>
			</p>

			<p><strong><?php esc_html_e( 'Create new user', 'wp-club-manager' ); ?></strong></p>

			<?php
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_create_user',
				'label' => __( 'Email address', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );

			wpclubmanager_wp_text_input( array(
				'id'          => 'wpcm_create_username',
				'label'       => __( 'Username', 'wp-club-manager' ),
				'class'       => 'regular-text',
				'placeholder' => __( 'Optional', 'wp-club-manager' ),
			) );

			do_action( 'wpclubmanager_after_admin_player_user_meta', $post->ID );

		}
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

		$user_id = filter_input( INPUT_POST, 'wpcm_link_users', FILTER_VALIDATE_INT );
		if ( isset( $user_id ) ) {
			$is_admin = self::has_administrator_role( $user_id );

			if ( ! $is_admin && $user_id > 0 ) {
				wp_update_user( array(
					'ID'   => $user_id,
					'role' => 'player',
				) );
			}
			wpcm_link_player_to_user( $post_id, $user_id );
		}

		$email = filter_input( INPUT_POST, 'wpcm_create_user', FILTER_VALIDATE_EMAIL );
		if ( ! empty( $email ) ) {
			$create_username = filter_input( INPUT_POST, 'wpcm_create_username', FILTER_UNSAFE_RAW );
			if ( isset( $create_username ) ) {
				$player = sanitize_text_field( $create_username );
			}
			$new_user = wpcm_create_new_user( $email, $player );
			if ( $new_user && ! is_wp_error( $new_user ) ) {
				wpcm_link_player_to_user( $post_id, $new_user );
			}
		}

		do_action( 'delete_plugin_transients' );

		do_action( 'wpclubmanager_after_admin_player_user_save', $post_id );
	}
}
