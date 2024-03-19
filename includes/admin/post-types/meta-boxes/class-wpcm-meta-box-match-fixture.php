<?php
/**
 * Match Fixture
 *
 * Displays the match fixture box.
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
 * WPCM_Meta_Box_Match_Fixture
 */
class WPCM_Meta_Box_Match_Fixture {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$default_club = get_default_club();
		$home_club    = get_post_meta( $post->ID, 'wpcm_home_club', true );
		$away_club    = get_post_meta( $post->ID, 'wpcm_away_club', true );
		if ( '' == $home_club && is_club_mode() ) {
			$home_club = $default_club;
		}
		if ( '' == $away_club && is_club_mode() ) {
			$away_club = $default_club;
		}
		?>

		<p>
			<label><?php esc_html_e( 'Home', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'wpcm_home_club',
				'id'               => 'wpcm_home_club',
				'post_type'        => 'wpcm_club',
				'limit'            => -1,
				'show_option_none' => __( 'Choose club', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'echo'             => false,
				'selected'         => $home_club,
			));
			?>
		</p>

		<p>
			<label><?php esc_html_e( 'Away', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'wpcm_away_club',
				'id'               => 'wpcm_away_club',
				'post_type'        => 'wpcm_club',
				'limit'            => -1,
				'show_option_none' => __( 'Choose club', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'echo'             => false,
				'selected'         => $away_club,
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

		$home_club_id = filter_input( INPUT_POST, 'wpcm_home_club', FILTER_VALIDATE_INT );
		$away_club_id = filter_input( INPUT_POST, 'wpcm_away_club', FILTER_VALIDATE_INT );
		$comp_id      = filter_input( INPUT_POST, 'wpcm_comp', FILTER_VALIDATE_INT );
		$season_id    = filter_input( INPUT_POST, 'wpcm_season', FILTER_VALIDATE_INT );

		if ( isset( $home_club_id ) ) {
			update_post_meta( $post_id, 'wpcm_home_club', $home_club_id );
			wp_set_post_terms( $home_club_id, $comp_id, 'wpcm_comp', true );
			wp_set_post_terms( $home_club_id, $season_id, 'wpcm_season', true );
		}
		if ( isset( $away_club_id ) ) {
			update_post_meta( $post_id, 'wpcm_away_club', $away_club_id );
			wp_set_post_terms( $away_club_id, $comp_id, 'wpcm_comp', true );
			wp_set_post_terms( $away_club_id, $season_id, 'wpcm_season', true );
		}
	}
}
