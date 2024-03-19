<?php
/**
 * Add Player to Roster
 *
 * Displays the add player to roster box.
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
 * WPCM_Meta_Box_Player_Roster
 */
class WPCM_Meta_Box_Player_Roster {

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
				'name'             => 'add_to_roster',
				'id'               => 'add_to_roster',
				'post_type'        => 'wpcm_roster',
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

		$player_id = filter_input( INPUT_POST, 'add_to_roster', FILTER_VALIDATE_INT );
		if ( $player_id ) {

			$players = (array) unserialize( get_post_meta( $player_id, '_wpcm_roster_players', true ) );

			if ( ! in_array( $post_id, $players ) ) {
				array_push( $players, intval( $post_id ) );
				update_post_meta( $player_id, '_wpcm_roster_players', serialize( $players ) );

				$seasons = wp_get_post_terms( $player_id, 'wpcm_season' );
				$season  = $seasons[0]->term_id;
				wp_set_post_terms( $post_id, $season, 'wpcm_season', true );

				$teams = wp_get_post_terms( $player_id, 'wpcm_team' );
				$team  = $teams[0]->term_id;
				wp_set_post_terms( $post_id, $team, 'wpcm_team', true );
			}
		}
	}
}
