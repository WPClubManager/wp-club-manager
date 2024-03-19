<?php
/**
 * Table Stats
 *
 * Displays the league table stats box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Table_Stats
 */
class WPCM_Meta_Box_Table_Stats {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$default_club = get_default_club();
		$team_label   = '';
		if ( is_club_mode() ) {
			// $teams = get_the_terms( $post->ID, 'wpcm_team' );
			// $team_id = $teams[0]->term_id;
			$team_label = wpcm_get_team_name( $default_club, $post->ID );
		}
		$comps          = get_the_terms( $post->ID, 'wpcm_comp' );
		$comp           = $comps[0]->term_id;
		$seasons        = get_the_terms( $post->ID, 'wpcm_season' );
		$season         = $seasons[0]->term_id;
		$stats          = get_option( 'wpcm_standings_columns_display' );
		$stats          = explode( ',', $stats );
		$order          = get_option( 'wpcm_standings_order' );
		$stats_labels   = wpcm_get_preset_labels( 'standings', 'label' );
		$stats_rows     = count( $stats_labels );
		$manual_stats   = (array) unserialize( get_post_meta( $post->ID, '_wpcm_table_stats', true ) );
		$selected_clubs = (array) unserialize( get_post_meta( $post->ID, '_wpcm_table_clubs', true ) );

		if ( empty( $selected_clubs ) ) {

			$args = array(
				'post_type'      => 'wpcm_club',
				'posts_per_page' => -1,
				'tax_query'      => array(),
				'hide_empty'     => false,
			);

			if ( $comp ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_comp',
					'field'    => 'term_id',
					'terms'    => $comp,
				);
			}

			if ( $season ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'field'    => 'term_id',
					'terms'    => $season,
				);
			}

			$clubs = get_posts( $args );

			if ( empty( $clubs ) ) {
				if ( false != $default_club ) {
					$selected_clubs = array( $default_club );
				} else {
					$selected_clubs = null;
				}

				$args  = array(
					'post_type'      => 'wpcm_club',
					'posts_per_page' => -1,
					'post__in'       => $selected_clubs,
				);
				$clubs = get_posts( $args );
			}
		} else {

			$args  = array(
				'post_type'      => 'wpcm_club',
				'posts_per_page' => -1,
				'post__in'       => $selected_clubs,
			);
			$clubs = get_posts( $args );
		}

		foreach ( $clubs as $club ) {
			if ( empty( $selected_clubs ) ) {

				$auto_stats              = get_wpcm_club_auto_stats( $club->ID, $comp, $season );
				$manual_stats            = get_wpcm_club_manual_stats( $club->ID, $comp, $season );
				$club_stats              = get_wpcm_club_total_stats( $club->ID, $comp, $season );
				$club->wpcm_manual_stats = $manual_stats;
				$club->wpcm_auto_stats   = $auto_stats;
				$club->wpcm_stats        = $club_stats;

			} else {
				$auto_stats = get_wpcm_club_auto_stats( $club->ID, $comp, $season );
				if ( array_key_exists( $club->ID, $manual_stats ) ) {
					$club->wpcm_manual_stats = $manual_stats[ $club->ID ];
					$club->wpcm_auto_stats   = $auto_stats;
					$total_stats             = get_wpcm_table_total_stats( $club->ID, $comp, $season, $manual_stats[ $club->ID ] );
					$club->wpcm_stats        = $total_stats;
				}
			}
		}

		usort( $clubs, 'wpcm_sort_table_clubs' );

		if ( 'ASC' === $order ) {
			$clubs = array_reverse( $clubs );
		}
		foreach ( $clubs as $key => $value ) {
			$value->place = $key + 1;
		} ?>

		<div id="wpcm-table-stats">
			<table>
				<?php if ( null !== $selected_clubs ) { ?>
					<thead>
						<tr>
							<th><input type="hidden" class="stats-rows" value="<?php echo esc_html( $stats_rows ); ?>"></th>
							<th></th>
							<th></th>
							<?php foreach ( $stats as $stat ) { ?>

								<th class="<?php echo esc_attr( $stat ); ?>"><?php echo esc_html( $stats_labels[ $stat ] ); ?></th>

							<?php } ?>

						</tr>
					</thead>
					<?php
				}
				?>
				<tbody>

					<?php
					$rownum = 0;
					foreach ( $clubs as $club ) {
						++$rownum;
						$auto_stats   = $club->wpcm_auto_stats;
						$manual_stats = $club->wpcm_manual_stats;
						$total_stats  = $club->wpcm_stats;
						?>

						<tr data-club="<?php echo esc_attr( $club->ID ); ?>" class="hidden-row">

							<td></td>
							<td></td>
							<td></td>

							<?php foreach ( $stats as $stat ) { ?>

								<td class="wpcm-table-stats-auto"><input type="hidden" data-index="<?php echo esc_attr( $stat ); ?>" value="<?php echo isset( $auto_stats[ $stat ] ) ? esc_html( $auto_stats[ $stat ] ) : '0'; ?>" size="2" tabindex="-1" readonly /></td>

							<?php } ?>

						</tr>

						<tr data-club="<?php echo esc_attr( $club->ID ); ?>" class="count-row">

							<td>
								<input type="checkbox" name="record">
							</td>

							<td class="pos">
								<?php echo esc_html( $club->place ); ?>
							</td>

							<td class="club">
								<?php
								if ( $default_club == $club->ID ) {
									if ( $team_label ) {
										echo esc_html( $team_label );
									} else {
										echo esc_html( $club->post_title );
									}
								} else {
									echo esc_html( $club->post_title );
								}
								?>
							</td>

							<?php foreach ( $stats as $stat ) { ?>

								<td class="wpcm-admin-league-table-data wpcm-table-stats-total <?php echo esc_attr( $stat ); ?>"><input type="number" data-index="<?php echo esc_attr( $stat ); ?>" value="<?php echo isset( $total_stats[ $stat ] ) ? esc_html( $total_stats[ $stat ] ) : '0'; ?>" <?php echo ( 'gd' === $stat ? 'readonly' : '' ); ?>/></td>

							<?php } ?>

						</tr>

						<tr data-club="<?php echo esc_attr( $club->ID ); ?>" class="hidden-row">

							<td></td>
							<td></td>
							<td><input type="hidden" name="wpcm_table_clubs[]" value="<?php echo esc_html( $club->ID ); ?>" /></td>

							<?php foreach ( $stats as $stat ) { ?>

								<td class="wpcm-table-stats-manual"><input type="hidden" data-index="<?php echo esc_attr( $stat ); ?>" name="wpcm_table_stats[<?php echo esc_attr( $club->ID ); ?>][<?php echo esc_attr( $stat ); ?>]" value="<?php echo isset( $manual_stats[ $stat ] ) ? esc_html( $manual_stats[ $stat ] ) : '0'; ?>" size="2" tabindex="-1" readonly /></td>

							<?php } ?>

						</tr>

					<?php } ?>

				</tbody>
			</table>

		</div>

		<div class="wpcm-table-stats-footer clearfix">

			<div class="add-club">
				<?php
				wpcm_dropdown_posts(array(
					'id'               => 'id',
					'name'             => 'table_clubs',
					'post_type'        => 'wpcm_club',
					'limit'            => -1,
					'show_option_none' => __( 'Choose a club', 'wp-club-manager' ),
				));
				?>

				<input type="button" class="button-secondary wpcm-table-add-row" value="<?php esc_html_e( 'Add club', 'wp-club-manager' ); ?>">
			</div>

			<a class="wpcm-table-delete-row <?php echo ( null != $clubs ? '' : 'hidden-button' ); ?>"><?php esc_html_e( 'Remove selected', 'wp-club-manager' ); ?></a>

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

		$clubs_data = filter_input( INPUT_POST, 'wpcm_table_clubs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $clubs_data ) {
			$clubs = $clubs_data;
		} else {
			$clubs = array();
		}
		if ( is_array( $clubs ) ) {
			array_walk_recursive( $clubs, 'wpcm_array_values_to_int' );
		}

		update_post_meta( $post_id, '_wpcm_table_clubs', serialize( $clubs ) );

		$stats_data = filter_input( INPUT_POST, 'wpcm_table_stats', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $stats_data ) {
			$stats = $stats_data;
		} else {
			$stats = array();
		}
		if ( is_array( $stats ) ) {
			array_walk_recursive( $stats, 'wpcm_array_values_to_int' );
		}

		update_post_meta( $post_id, '_wpcm_table_stats', serialize( $stats ) );

		do_action( 'delete_plugin_transients' );
	}
}
