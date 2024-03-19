<?php
/**
 * Plugin Dashboard Page
 *
 * @author      Clubpress
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Admin_Dashboard Class
 */
class WPCM_Admin_Dashboard {

	/**
	 * Handles output of the dashboard page in admin.
	 */
	public static function output() {
		?>

		<div class="ui grid wpcm-dashboard">
			<div class="sixteen wide column">


				<?php
				if ( is_club_mode() ) {
					$default_club = get_default_club();
					$team      = filter_input( INPUT_POST, 'team_select', FILTER_UNSAFE_RAW );
					if ( $team ) {
						$term      = get_term( $team, 'wpcm_team' );
						$team_name = $term->name;
						$team_slug = $term->slug;
					} else {
						$teams = get_terms( array(
							'taxonomy'   => 'wpcm_team',
							'meta_key'   => 'tax_position',
							'orderby'    => 'tax_position',
							'hide_empty' => false,
						) );
						if ( $teams ) {
							$team      = $teams[0]->term_id;
							$team_name = $teams[0]->name;
							$team_slug = $teams[0]->slug;
						} else {
							$team      = null;
							$team_name = null;
							$team_slug = null;
						}
					}
					// Setup
					$seasons     = get_terms( array(
						'taxonomy'   => 'wpcm_season',
						'meta_key'   => 'tax_position',
						'orderby'    => 'tax_position',
						'hide_empty' => false,
					) );
					$season      = $seasons[0];
					$season_slug = $seasons[0]->slug;

					// Statistics
					$args = array(
						'tax_query'      => array(),
						'post_type'      => 'wpcm_match',
						'posts_per_page' => -1,

					);
					$args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'   => 'wpcm_home_club',
							'value' => $default_club,
						),
						array(
							'key'   => 'wpcm_away_club',
							'value' => $default_club,
						),
					);
					$args['meta_query'] = array(
						array(
							'key'   => 'wpcm_played',
							'value' => 1,
						),
					);
					if ( isset( $season ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_season',
							'terms'    => $season->term_id,
							'field'    => 'term_id',
						);
					}
					if ( isset( $team ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_team',
							'terms'    => $team,
							'field'    => 'term_id',
						);
					}
					$matches = get_posts( $args );

					if ( $matches ) {
						$statistics     = wpcm_head_to_head_count( $matches );
						$win_percent    = $statistics['wins'] / $statistics['total'] * 100;
						$win_percent    = round( $win_percent, 1 ) . '%';
						$goals_scored   = 0;
						$goals_conceded = 0;
						$sep            = get_option( 'wpcm_match_goals_delimiter' );
						foreach ( $matches as $match ) {
							$home_club  = get_post_meta( $match->ID, 'wpcm_home_club', true );
							$wpcm_goals = unserialize( get_post_meta( $match->ID, 'wpcm_goals', true ) );
							if ( $default_club == $home_club ) {
								$goals_scored   += $wpcm_goals['total']['home'];
								$goals_conceded += $wpcm_goals['total']['away'];
								$goals_for       = $wpcm_goals['total']['home'];
								$goals_against   = $wpcm_goals['total']['away'];
							} else {
								$goals_scored   += $wpcm_goals['total']['away'];
								$goals_conceded += $wpcm_goals['total']['home'];
								$goals_for       = $wpcm_goals['total']['away'];
								$goals_against   = $wpcm_goals['total']['home'];
							}
							$goal_diff[ $match->ID ]['id'] = $match->ID;
							$goal_diff[ $match->ID ]['gd'] = $goals_for - $goals_against;
							$goal_diff[ $match->ID ]['f']  = $goals_for;
						}
						usort( $goal_diff, 'sort_biggest_score' );
						$goal_diff      = array_reverse( $goal_diff );
						$biggest_win_id = $goal_diff[0]['id'];
						$wpcm_goals     = unserialize( get_post_meta( $biggest_win_id, 'wpcm_goals', true ) );
						$biggest_score  = $wpcm_goals['total']['home'] . ' ' . $sep . '  ' . $wpcm_goals['total']['away'];
					} else {
						$win_percent    = '';
						$biggest_score  = '';
						$goals_scored   = '';
						$goals_conceded = '';
					}

					$labels  = wpcm_get_preset_labels( 'standings', 'name' );
					$f_label = wpcm_array_value( $labels, 'f' );
					$a_label = wpcm_array_value( $labels, 'a' );

					// Matches
					$args               = array(
						'tax_query'      => array(),
						'order'          => 'DESC',
						'orderby'        => 'post_date',
						'post_type'      => 'wpcm_match',
						'posts_per_page' => 4,
					);
					$args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'   => 'wpcm_home_club',
							'value' => $default_club,
						),
						array(
							'key'   => 'wpcm_away_club',
							'value' => $default_club,
						),
					);
					if ( isset( $season ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_season',
							'terms'    => $season->term_id,
							'field'    => 'term_id',
						);
					}
					if ( isset( $team ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_team',
							'terms'    => $team,
							'field'    => 'term_id',
						);
					}
					$publish        = array(
						'post_status' => 'publish',
						'meta_query'  => array(
							'key'   => 'wpcm_played',
							'value' => false,
						),
					);
					$future         = array(
						'post_status' => 'future',
					);
					$played_matches = get_posts( array_merge( $args, $publish ) );
					$future_matches = get_posts( array_merge( $args, $future ) );

					$admin_url = admin_url( 'edit.php?s' );
					$new_query = esc_url( add_query_arg(
						array(
							'post_status'               => 'all',
							'post_type'                 => 'wpcm_match',
							'action'                    => -1,
							'm'                         => 0,
							'tax_input[wpcm_comp][0]'   => 0,
							'tax_input[wpcm_season][0]' => 0,
							'tax_input[wpcm_team][0]'   => 0,
							'wpcm_comp'                 => 0,
							'wpcm_team'                 => urlencode( $team_slug ),
							'wpcm_season'               => urlencode( $season_slug ),
							'filter_action'             => 'Filter',
							'paged'                     => 1,
							'action2'                   => -1,
						),
						$admin_url
					) );

					// Roster
					$args = array(
						'post_type' => 'wpcm_roster',
					);

					$args['tax_query'] = array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'wpcm_team',
							'field'    => 'term_id',
							'terms'    => $team,
						),
						array(
							'taxonomy' => 'wpcm_season',
							'field'    => 'term_id',
							'terms'    => $season->term_id,
						),
					);

					$roster = get_posts( $args );

					if ( $roster ) {

						$roster_id = $roster[0]->ID;

						$selected_players = (array) unserialize( get_post_meta( $roster_id, '_wpcm_roster_players', true ) );

						$args = array(
							'post_type'        => 'wpcm_player',
							'orderby'          => 'title',
							// 'meta_key' => 'wpcm_number',
							'order'            => 'ASC',
							'posts_per_page'   => -1,
							'suppress_filters' => 0,
							'post__in'         => $selected_players,
						);

						$players = get_posts( $args );

						$selected_staff = (array) unserialize( get_post_meta( $roster_id, '_wpcm_roster_staff', true ) );

						$args = array(
							'post_type'        => 'wpcm_staff',
							'order'            => 'ASC',
							'posts_per_page'   => -1,
							'suppress_filters' => 0,
							'post__in'         => $selected_staff,
						);

						$employees = get_posts( $args );

					} else {

						$players   = false;
						$employees = false;
					}

					// League Tables
					$args = array(
						'post_type' => 'wpcm_table',
					);

					$args['tax_query'] = array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'wpcm_team',
							'field'    => 'term_id',
							'terms'    => $team,
						),
						array(
							'taxonomy' => 'wpcm_season',
							'field'    => 'term_id',
							'terms'    => $season->term_id,
						),
					);

					$table = get_posts( $args );
					if ( $table ) {
						$table_id = $table[0]->ID;

						$stats          = get_option( 'wpcm_standings_columns_display' );
						$team_label     = wpcm_get_team_name( $default_club, $team );
						$comps          = get_the_terms( $table_id, 'wpcm_comp' );
						$comp           = $comps[0]->term_id;
						$manual_stats   = (array) unserialize( get_post_meta( $table_id, '_wpcm_table_stats', true ) );
						$selected_clubs = (array) unserialize( get_post_meta( $table_id, '_wpcm_table_clubs', true ) );
						// $columns = get_option( 'wpcm_standings_columns_display' );
						$stats = explode( ',', $stats );
						$order = get_option( 'wpcm_standings_order' );
						$notes = get_post_meta( $table_id, '_wpcm_table_notes', true );

						$args  = array(
							'post_type'      => 'wpcm_club',
							'tax_query'      => array(),
							'numberposts'    => -1,
							'posts_per_page' => -1,
							'post__in'       => $selected_clubs,
						);
						$clubs = get_posts( $args );

						$size = count( $clubs );

						foreach ( $clubs as $club ) {

							$auto_stats       = get_wpcm_club_auto_stats( $club->ID, $comp, $season );
							$club->wpcm_stats = $auto_stats;
							if ( array_key_exists( $club->ID, $manual_stats ) ) {
								$club->wpcm_manual_stats = $manual_stats[ $club->ID ];
								$club->wpcm_auto_stats   = $auto_stats;
								$total_stats             = get_wpcm_table_total_stats( $club->ID, $comp, $season, $manual_stats[ $club->ID ] );
								$club->wpcm_stats        = $total_stats;
							}
						}

						usort( $clubs, 'wpcm_sort_table_clubs' );

						if ( 'ASC' === $order ) {
							$clubs = array_reverse( $clubs );
						}
						foreach ( $clubs as $key => $value ) {
							$value->place = $key + 1;
						}

						$stats_labels = wpcm_get_preset_labels( 'standings', 'label' );
					} else {
						$clubs = false;
					}

					// Output
					include_once 'views/html-admin-page-dashboard.php';

				} else {
					// Setup
					$seasons     = get_terms( array(
						'taxonomy'   => 'wpcm_season',
						'meta_key'   => 'tax_position',
						'orderby'    => 'tax_position',
						'hide_empty' => false,
					) );
					$season      = $seasons[0];
					$season_slug = $seasons[0]->slug;

					// Statistics
					$args = array(
						'tax_query'      => array(),
						'post_type'      => 'wpcm_match',
						'posts_per_page' => -1,

					);

					$args['meta_query'] = array(
						array(
							'key'   => 'wpcm_played',
							'value' => 1,
						),
					);
					if ( isset( $season ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_season',
							'terms'    => $season->term_id,
							'field'    => 'term_id',
						);
					}

					// Matches
					$args               = array(
						'tax_query'      => array(),
						'order'          => 'DESC',
						'orderby'        => 'post_date',
						'post_type'      => 'wpcm_match',
						'posts_per_page' => 4,
					);

					if ( isset( $season ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => 'wpcm_season',
							'terms'    => $season->term_id,
							'field'    => 'term_id',
						);
					}

					$publish        = array(
						'post_status' => 'publish',
						'meta_query'  => array(
							'key'   => 'wpcm_played',
							'value' => false,
						),
					);
					$future         = array(
						'post_status' => 'future',
					);
					$played_matches = get_posts( array_merge( $args, $publish ) );
					$future_matches = get_posts( array_merge( $args, $future ) );

					$admin_url = admin_url( 'edit.php?s' );
					$new_query = esc_url( add_query_arg(
						array(
							'post_status'               => 'all',
							'post_type'                 => 'wpcm_match',
							'action'                    => -1,
							'm'                         => 0,
							'tax_input[wpcm_comp][0]'   => 0,
							'tax_input[wpcm_season][0]' => 0,
							'tax_input[wpcm_team][0]'   => 0,
							'wpcm_comp'                 => 0,
							'wpcm_season'               => urlencode( $season_slug ),
							'filter_action'             => 'Filter',
							'paged'                     => 1,
							'action2'                   => -1,
						),
						$admin_url
					) );

					// League Tables
					$args = array(
						'post_type' => 'wpcm_table',
					);

					$args['tax_query'] = array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'wpcm_season',
							'field'    => 'term_id',
							'terms'    => $season->term_id,
						),
					);

					$table = get_posts( $args );
					if ( $table ) {
						$table_id = $table[0]->ID;

						$stats          = get_option( 'wpcm_standings_columns_display' );
						$comps          = get_the_terms( $table_id, 'wpcm_comp' );
						$comp           = $comps[0]->term_id;
						$manual_stats   = (array) unserialize( get_post_meta( $table_id, '_wpcm_table_stats', true ) );
						$selected_clubs = (array) unserialize( get_post_meta( $table_id, '_wpcm_table_clubs', true ) );
						// $columns = get_option( 'wpcm_standings_columns_display' );
						$stats = explode( ',', $stats );
						$order = get_option( 'wpcm_standings_order' );
						$notes = get_post_meta( $table_id, '_wpcm_table_notes', true );

						$args  = array(
							'post_type'      => 'wpcm_club',
							'tax_query'      => array(),
							'numberposts'    => -1,
							'posts_per_page' => -1,
							'post__in'       => $selected_clubs,
						);
						$clubs = get_posts( $args );

						$size = count( $clubs );

						foreach ( $clubs as $club ) {

							$auto_stats       = get_wpcm_club_auto_stats( $club->ID, $comp, $season );
							$club->wpcm_stats = $auto_stats;
							if ( array_key_exists( $club->ID, $manual_stats ) ) {
								$club->wpcm_manual_stats = $manual_stats[ $club->ID ];
								$club->wpcm_auto_stats   = $auto_stats;
								$total_stats             = get_wpcm_table_total_stats( $club->ID, $comp, $season, $manual_stats[ $club->ID ] );
								$club->wpcm_stats        = $total_stats;
							}
						}

						usort( $clubs, 'wpcm_sort_table_clubs' );

						if ( 'ASC' === $order ) {
							$clubs = array_reverse( $clubs );
						}
						foreach ( $clubs as $key => $value ) {
							$value->place = $key + 1;
						}

						$stats_labels = wpcm_get_preset_labels( 'standings', 'label' );
					} else {
						$clubs = false;
					}

					include_once 'views/html-admin-page-league-dashboard.php';
				}
				?>

			</div>
		</div>
		<?php
	}
}
