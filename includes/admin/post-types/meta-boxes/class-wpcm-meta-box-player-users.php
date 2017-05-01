<?php
/**
 * Link Player to User
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
    public static function has_administrator_role($user_id) {
        global $wpdb;

        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $table_name = $wpdb->prefix . 'usermeta';
        $meta_key = $wpdb->prefix . 'capabilities';
        $query = "SELECT count(*)
                FROM $table_name
                WHERE user_id=$user_id AND meta_key='$meta_key' AND meta_value like '%administrator%'";
        $has_admin_role = $wpdb->get_var($query);
        if ($has_admin_role > 0) {
            $result = true;
        } else {
            $result = false;
        }
        // cache checking result for the future use
        //$this->user_to_check[$user_id] = $result;

        return $result;
    }

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		if( $post->post_status == 'publish' ) {

			do_action('wpclubmanager_before_admin_player_user_meta', $post->ID );

			$user = get_post_meta( $post->ID, '_wpcm_link_users', true );
			$user_data = get_userdata( $user );
			$edit_link = ( isset( $user ) && ! empty( $user ) ? '<a href="' . get_edit_user_link( $user ) . '">' . __( 'Edit user', 'wp-club-manager' ) . '</a>' : '' ); ?>

			<p><strong><?php _e( 'Existing user', 'wp-club-manager' ); ?></strong></p>

			<p>
				<label><?php _e( 'Choose User', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'show_option_none' 	=> __( 'None', 'wp-club-manager' ),
					'name' 				=> 'wpcm_link_users',
					'selected' 			=> $user,
					'include_selected' 	=> true,
					'class' 			=> 'chosen_select',
				);

				wp_dropdown_users( $args ); ?>
				<span class="edit-user-player"><?php echo $edit_link; ?></span>
			</p>
			
			<p><strong><?php _e( 'Create new user', 'wp-club-manager' ); ?></strong></p>

			<?php wpclubmanager_wp_text_input( array( 'id' => 'wpcm_create_user', 'label' => __( 'Email address', 'wp-club-manager' ), 'class' => 'regular-text' ) );

			wpclubmanager_wp_text_input( array( 'id' => 'wpcm_create_username', 'label' => __( 'Username', 'wp-club-manager' ), 'class' => 'regular-text', 'placeholder' => __( 'Optional', 'wp-club-manager' ) ) );

			do_action('wpclubmanager_after_admin_player_user_meta', $post->ID );

		}
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		
		if( isset( $_POST['wpcm_link_users'] ) ) {
			$user_id = $_POST['wpcm_link_users'];
			$is_admin = self::has_administrator_role( $user_id );
			update_post_meta( $post_id, '_wpcm_link_users', $user_id );

			if( !$is_admin ) {
				wp_update_user( array( 'ID' => $user_id, 'role' => 'player' ) );
			}
			update_user_meta( $user_id, '_linked_player', $post_id );
		}

		if( ! empty( $_POST['wpcm_create_user'] ) ) {
			$email = $_POST['wpcm_create_user'];
			if( isset( $_POST['wpcm_create_username'])) {
				$player = $_POST['wpcm_create_username'];
			}
			$new_user = wpcm_create_new_user( $email, $player );
			update_user_meta( $new_user, '_linked_player', $post_id );
			update_post_meta( $post_id, '_wpcm_link_users', $new_user );
		}

		do_action( 'delete_plugin_transients' );

		do_action('wpclubmanager_after_admin_player_user_save', $post_id );
	}
}