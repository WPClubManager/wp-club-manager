<?php
/**
 * WP Club Manager User Functions
 *
 * Functions for users (players and staff).
 *
 * @author      Clubpress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Prevent any user who cannot 'edit_posts' (subscribers, players etc) from seeing the admin bar.
 *
 * @access public
 * @param bool $show_admin_bar
 * @return bool
 */
function wpcm_disable_admin_bar( $show_admin_bar ) {
	if ( ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_wpclubmanager' ) ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'wpcm_disable_admin_bar', 10, 1 );

/**
 * Create a new player (user).
 *
 * @param  string $email
 * @param  string $username
 * @param  string $password
 * @return int|WP_Error on failure, Int (user ID) on success
 */
function wpcm_create_new_user( $email, $username = '', $password = '' ) {

	// Check the e-mail address
	if ( empty( $email ) || ! is_email( $email ) ) {
		return;
	}

	if ( email_exists( $email ) ) {
		return;
	}

	// Handle username creation
	if ( ! empty( $username ) ) {

		$username = sanitize_user( $username );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return;
		}

		if ( username_exists( $username ) ) {
			return;
		}
	} else {

		$username = sanitize_user( current( explode( '@', $email ) ), true );

		// Ensure username is unique
		$append     = 1;
		$o_username = $username;

		while ( username_exists( $username ) ) {
			$username = $o_username . $append;
			++$append;
		}
	}

	// Handle password creation
	if ( empty( $password ) ) {
		$password           = wp_generate_password();
		$password_generated = true;

	} else {
		$password_generated = false;
	}

	$new_user_data = apply_filters( 'wpclubmanager_new_user_data', array(
		'user_login' => $username,
		'user_pass'  => $password,
		'user_email' => $email,
		'role'       => 'player',
	) );

	$created_user = wp_insert_user( $new_user_data );

	wp_new_user_notification( $created_user );

	do_action( 'wpclubmanager_created_user', $created_user, $new_user_data, $password_generated );

	return $created_user;
}

/**
 * Get all player IDs linked to a WordPress user.
 *
 * Supports multiple players per user account (e.g. a parent
 * managing children's profiles).
 *
 * @param int $user_id WordPress user ID.
 * @return int[] Array of wpcm_player post IDs.
 */
function wpcm_get_linked_players( $user_id ) {
	if ( empty( $user_id ) ) {
		return array();
	}

	$values = get_user_meta( $user_id, '_linked_player', false );
	return array_map( 'intval', array_filter( $values ) );
}

/**
 * Link a player to a WordPress user account.
 *
 * Creates the bidirectional relationship between a wpcm_player post
 * and a WP user.  Handles cleanup when the linked user changes and
 * prevents duplicate entries so one user can manage many players.
 *
 * @param int $player_id The wpcm_player post ID.
 * @param int $user_id   The WordPress user ID (0 to unlink).
 */
function wpcm_link_player_to_user( $player_id, $user_id ) {
	$old_user_id = (int) get_post_meta( $player_id, '_wpcm_link_users', true );
	$user_id     = (int) $user_id;

	// Remove old user → player link when the user changes.
	if ( $old_user_id && $old_user_id !== $user_id ) {
		delete_user_meta( $old_user_id, '_linked_player', $player_id );
	}

	if ( $user_id > 0 ) {
		update_post_meta( $player_id, '_wpcm_link_users', $user_id );

		// Add user → player link only if it does not already exist.
		$existing = wpcm_get_linked_players( $user_id );
		if ( ! in_array( $player_id, $existing, true ) ) {
			add_user_meta( $user_id, '_linked_player', $player_id );
		}
	} else {
		// Unlinking — clear both sides.
		delete_post_meta( $player_id, '_wpcm_link_users' );
	}
}

/**
 * Modify the list of editable roles to prevent non-admin adding admin users.
 *
 * @param  array $roles
 * @return array
 */
function wpcm_modify_editable_roles( $roles ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		unset( $roles['administrator'] );
	}
	return $roles;
}
add_filter( 'editable_roles', 'wpcm_modify_editable_roles' );

/**
 * Modify capabiltiies to prevent non-admin users editing admin users.
 *
 * $args[0] will be the user being edited in this case.
 *
 * @param  array  $caps Array of caps
 * @param  string $cap Name of the cap we are checking
 * @param  int    $user_id ID of the user being checked against
 * @param  array  $args
 * @return array
 */
function wpcm_modify_map_meta_cap( $caps, $cap, $user_id, $args ) {
	switch ( $cap ) {
		case 'edit_user':
		case 'remove_user':
		case 'promote_user':
		case 'delete_user':
			if ( ! isset( $args[0] ) || $args[0] === $user_id ) {
				break;
			} elseif ( user_can( $args[0], 'manage_options' ) && ! current_user_can( 'manage_options' ) ) {
					$caps[] = 'do_not_allow';
			}
			break;
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'wpcm_modify_map_meta_cap', 10, 4 );

/**
 * Disable author archives for players.
 */
function wpcm_disable_author_archives_for_players() {
	global $wp_query, $author;

	if ( is_author() ) {
		$user = get_user_by( 'id', $author );

		if ( isset( $user->roles[0] ) && 'player' === $user->roles[0] ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'template_redirect', 'wpcm_disable_author_archives_for_players' );
