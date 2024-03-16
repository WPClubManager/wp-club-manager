<?php
/**
 * Roster Details
 *
 * Displays the league table details box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Roster_Details
 */
class WPCM_Meta_Box_Roster_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$seasons = get_the_terms( $post->ID, 'wpcm_season' );
		if ( is_array( $seasons ) ) {
			$season = $seasons[0]->term_id;
		} else {
			$season = -1;
		}

		$teams = get_the_terms( $post->ID, 'wpcm_team' );
		if ( is_array( $teams ) ) {
			$team = $teams[0]->term_id;
		} else {
			$team = -1;
		}?>

		<p>
			<label><?php esc_html_e( 'Season', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'taxonomy'     => 'wpcm_season',
				'hide_empty'   => false,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'selected'     => $season,
				'name'         => 'wpcm_roster_season',
				'class'        => 'chosen_select',
			));
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Team', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'taxonomy'     => 'wpcm_team',
				'hide_empty'   => false,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'selected'     => $team,
				'name'         => 'wpcm_roster_team',
				'class'        => 'chosen_select',
			));
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Import Players', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'roster_players_import',
				'id'               => 'roster_players_import',
				'post_type'        => 'wpcm_roster',
				'limit'            => -1,
				'show_option_none' => __( 'None', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'echo'             => false,
			));
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Import Staff', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'roster_staff_import',
				'id'               => 'roster_staff_import',
				'post_type'        => 'wpcm_roster',
				'limit'            => -1,
				'show_option_none' => __( 'None', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'echo'             => false,
			));
			?>
		</p>

		<?php
		do_action( 'wpclubmanager_admin_roster_details', $post->ID );
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

		$season_id = filter_input( INPUT_POST, 'wpcm_roster_season', FILTER_VALIDATE_INT );
		if ( $season_id ) {
			wp_set_post_terms( $post_id, $season_id, 'wpcm_season' );
		}

		$team_id = filter_input( INPUT_POST, 'wpcm_roster_team', FILTER_VALIDATE_INT );
		if ( $team_id ) {
			wp_set_post_terms( $post_id, $team_id, 'wpcm_team' );
		}

		$player_team_import_id = filter_input( INPUT_POST, 'roster_players_import', FILTER_VALIDATE_INT );
		if ( $player_team_import_id ) {

			$players = (array) unserialize( get_post_meta( $player_team_import_id, '_wpcm_roster_players', true ) );

			update_post_meta( $post_id, '_wpcm_roster_players', serialize( $players ) );

			if ( is_array( $players ) ) {

				$teams   = wp_get_post_terms( $post_id, 'wpcm_team' );
				$team    = $teams[0]->term_id;
				$seasons = wp_get_post_terms( $post_id, 'wpcm_season' );
				$season  = $seasons[0]->term_id;
				foreach ( $players as $player ) {
					wp_set_post_terms( $player, $season, 'wpcm_season', true );
					wp_set_post_terms( $player, $team, 'wpcm_team', true );
				}
			}
		}

		$staff_team_import_id = filter_input( INPUT_POST, 'roster_staff_import', FILTER_VALIDATE_INT );
		if ( isset( $_POST['roster_staff_import'] ) ) {

			$employees = (array) unserialize( get_post_meta( $staff_team_import_id, '_wpcm_roster_staff', true ) );

			update_post_meta( $post_id, '_wpcm_roster_staff', serialize( $employees ) );

			if ( is_array( $employees ) ) {

				$teams   = wp_get_post_terms( $post_id, 'wpcm_team' );
				$team    = $teams[0]->term_id;
				$seasons = wp_get_post_terms( $post_id, 'wpcm_season' );
				$season  = $seasons[0]->term_id;
				foreach ( $employees as $employee ) {
					wp_set_post_terms( $employee, $season, 'wpcm_season', true );
					wp_set_post_terms( $employee, $team, 'wpcm_team', true );
				}
			}
		}

		do_action( 'delete_plugin_transients' );
	}
}
