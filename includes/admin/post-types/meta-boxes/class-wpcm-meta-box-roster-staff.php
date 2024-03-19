<?php
/**
 * Staff Roster
 *
 * Add staff to roster.
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
 * WPCM_Meta_Box_Roster_Staff
 */
class WPCM_Meta_Box_Roster_Staff {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$seasons = get_the_terms( $post->ID, 'wpcm_season' );
		$season  = $seasons[0]->term_id;
		$teams   = get_the_terms( $post->ID, 'wpcm_team' );
		$team    = $teams[0]->term_id;
		$staff   = unserialize( get_post_meta( $post->ID, '_wpcm_roster_staff', true ) );

		if ( empty( $staff ) ) {

			$args = array(
				'post_type'      => 'wpcm_staff',
				'posts_per_page' => -1,
				'tax_query'      => array(),
			);
			if ( $season ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'field'    => 'term_id',
					'terms'    => $season,
				);
			}

			if ( $team ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'field'    => 'term_id',
					'terms'    => $team,
				);
			}

			$staff = get_posts( $args );

		} else {

			$args = array(
				'post_type'      => 'wpcm_staff',
				'numberposts'    => -1,
				'posts_per_page' => -1,
				'post__in'       => $staff,
			);

			$staff = get_posts( $args );

		} ?>

		<div id="wpcm-staff-roster-stats">
			<table>
				<?php
				if ( null != $staff ) {
					?>
					<thead>
						<tr>
							<th></th>
							<th><?php esc_html_e( 'Name', 'wp-club-manager' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<?php
				}
				?>
				<tbody>

				<?php
				foreach ( $staff as $employee ) {
					?>

					<tr data-club="<?php echo esc_attr( $employee->ID ); ?>">

						<td>
							<input type="checkbox" name="record">
						</td>
						<td class="club">
							<input type="hidden" name="wpcm_roster_staff[]" value="<?php echo esc_attr( $employee->ID ); ?>" />
							<?php echo esc_html( $employee->post_title ); ?>
						</td>
						<td class="roster-actions">
							<a class="" href="<?php echo esc_url( get_edit_post_link( $employee->ID ) ); ?>"><?php esc_html_e( 'Edit', 'wp-club-manager' ); ?></a>
						</td>

					</tr>
				<?php } ?>

				</tbody>
			</table>
		</div>

		<div class="wpcm-table-stats-footer clearfix">

			<div class="add-club">
				<?php
				wpcm_dropdown_posts(array(
					'class'            => 'staff-id',
					'name'             => 'roster_staff',
					'post_type'        => 'wpcm_staff',
					'limit'            => -1,
					'show_option_none' => __( 'Choose staff', 'wp-club-manager' ),
				));
				?>

				<input type="button" class="button-secondary wpcm-staff-roster-add-row" value="<?php esc_html_e( 'Add staff', 'wp-club-manager' ); ?>">
			</div>
			<a class="wpcm-staff-roster-delete-row <?php echo ( null != $staff ? '' : 'hidden-button' ); ?>"><?php esc_html_e( 'Remove selected', 'wp-club-manager' ); ?></a>

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

		$staff_data = filter_input( INPUT_POST, 'wpcm_roster_staff', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $staff_data ) {
			$staff = $staff_data;
		} else {
			$staff = array();
		}

		if ( is_array( $staff ) ) {
			array_walk_recursive( $staff, 'wpcm_array_values_to_int' );
		}

		update_post_meta( $post_id, '_wpcm_roster_staff', serialize( $staff ) );

		do_action( 'delete_plugin_transients' );
	}
}
