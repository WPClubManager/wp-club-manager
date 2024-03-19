<?php
/**
 * Table Details
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
 * WPCM_Meta_Box_Table_Details
 */
class WPCM_Meta_Box_Table_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$comps = get_the_terms( $post->ID, 'wpcm_comp' );
		if ( is_array( $comps ) ) {
			$comp      = $comps[0]->term_id;
			$comp_slug = $comps[0]->slug;
		} else {
			$comp      = 0;
			$comp_slug = null;
		}

		$seasons = get_the_terms( $post->ID, 'wpcm_season' );
		if ( is_array( $seasons ) ) {
			$season = $seasons[0]->term_id;
		} else {
			$season = 0;
		}

		$default_club = get_default_club();
		if ( null !== $default_club && has_teams() ) {
			$teams = get_the_terms( $post->ID, 'wpcm_team' );
			if ( is_array( $teams ) ) {
				$team = $teams[0]->term_id;
			} else {
				$team = 0;
			}
		} ?>

		<p>
			<label><?php esc_html_e( 'Competition', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'taxonomy'     => 'wpcm_comp',
				'hide_empty'   => false,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'selected'     => $comp,
				'name'         => 'wpcm_table_comp',
				'class'        => 'chosen_select',
			));
			?>
		</p>
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
				'name'         => 'wpcm_table_season',
				'class'        => 'chosen_select',
			));
			?>
		</p>
		<?php
		if ( null != $default_club && has_teams() ) {
			?>
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
					'name'         => 'wpcm_table_team',
					'class'        => 'chosen_select',
				));
				?>
			</p>
			<?php
		}
		?>

		<?php
		do_action( 'wpclubmanager_admin_table_details', $post->ID );
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

		$table_id = filter_input( INPUT_POST, 'wpcm_table_comp', FILTER_VALIDATE_INT );
		if ( $table_id ) {
			wp_set_post_terms( $post_id, $table_id, 'wpcm_comp' );
		}

		$season_id = filter_input( INPUT_POST, 'wpcm_table_season', FILTER_VALIDATE_INT );
		if ( $season_id ) {
			wp_set_post_terms( $post_id, $season_id, 'wpcm_season' );
		}

		$team_id = filter_input( INPUT_POST, 'wpcm_table_team', FILTER_VALIDATE_INT );
		if ( $team_id ) {
			wp_set_post_terms( $post_id, $team_id, 'wpcm_team' );
		}

		do_action( 'delete_plugin_transients' );
	}
}
