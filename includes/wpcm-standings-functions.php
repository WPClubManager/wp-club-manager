<?php
/**
 * WPClubManager Standings Functions. Code adapted from Football Club Theme by themeboy
 *
 * Functions for standings.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Standing table sorting.
 *
 * @access public
 * @param array
 * @param array
 * @return int
 */
if ( ! function_exists( 'wpcm_club_standings_sort' ) ) {
	/**
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @return int
	 */
	function wpcm_club_standings_sort( $a, $b ) {

		$priority_1 = get_option( 'wpcm_standings_orderby' );
		$priority_2 = get_option( 'wpcm_standings_orderby_2' );
		$priority_3 = get_option( 'wpcm_standings_orderby_3' );

		if ( $a->wpcm_stats[ $priority_1 ] > $b->wpcm_stats[ $priority_1 ] ) {

			return -1;

		} elseif ( $a->wpcm_stats[ $priority_1 ] < $b->wpcm_stats[ $priority_1 ] ) {

			return 1;

		} elseif ( $a->wpcm_stats[ $priority_2 ] > $b->wpcm_stats[ $priority_2 ] ) {

				return -1;

		} elseif ( $a->wpcm_stats[ $priority_2 ] < $b->wpcm_stats[ $priority_2 ] ) {

			return 1;

		} elseif ( $a->wpcm_stats[ $priority_3 ] > $b->wpcm_stats[ $priority_3 ] ) {

				return -1;

		} elseif ( $a->wpcm_stats[ $priority_3 ] < $b->wpcm_stats[ $priority_3 ] ) {

			return 1;

		} elseif ( strcmp( $a->post_title, $b->post_title ) < 0 ) {

				return -1;

		} else {

			return 1;
		}
	}
}

/**
 * Standing table sorting.
 *
 * @access public
 * @param array
 * @param array
 * @return int
 */
if ( ! function_exists( 'wpcm_club_standings_pct_sort' ) ) {
	/**
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @return int
	 */
	function wpcm_club_standings_pct_sort( $a, $b ) {

		if ( $a->wpcm_stats['pct'] > $b->wpcm_stats['pct'] ) {

			return -1;

		} elseif ( $a->wpcm_stats['pct'] < $b->wpcm_stats['pct'] ) {

			return 1;

		} elseif ( $a->wpcm_stats['w'] > $b->wpcm_stats['w'] ) {

				return -1;

		} elseif ( $a->wpcm_stats['w'] < $b->wpcm_stats['w'] ) {

			return 1;

		} elseif ( $a->wpcm_stats['f'] > $b->wpcm_stats['f'] ) {

				return -1;

		} elseif ( $a->wpcm_stats['f'] < $b->wpcm_stats['f'] ) {

			return 1;

		} elseif ( strcmp( $a->post_title, $b->post_title ) < 0 ) {

				return -1;

		} else {

			return 1;
		}
	}
}

/**
 * Standing table sort by.
 *
 * @access public
 * @param array
 * @param array
 * @return array
 */
if ( ! function_exists( 'wpcm_club_standings_sort_by' ) ) {
	/**
	 * @param string $subkey
	 * @param array  $a
	 *
	 * @return array
	 */
	function wpcm_club_standings_sort_by( $subkey, $a ) {

		$b = array();

		foreach ( $a as $k => $v ) {

			$b[ $k ] = (float) $v->wpcm_stats[ $subkey ];
		}

		if ( ! empty( $b ) ) {

			arsort( $b );
			foreach ( $b as $key => $val ) {

				$c[] = $a[ $key ];
			}

			return $c;
		}

		return array();
	}
}

/**
 * Get total club stats.
 *
 * @access public
 * @param string $post_id
 * @param string $comp
 * @param string $season
 * @return mixed $output
 */
if ( ! function_exists( 'get_wpcm_table_total_stats' ) ) {
	/**
	 * @param int   $post_id
	 * @param int   $comp
	 * @param int   $season
	 * @param array $manualstats
	 * @param int   $team
	 *
	 * @return array
	 */
	function get_wpcm_table_total_stats( $post_id = null, $comp = null, $season = null, $manualstats = null, $team = null ) {

		$sport     = get_option( 'wpcm_sport' );
		$output    = get_wpcm_club_stats_empty_row();
		$autostats = get_wpcm_club_auto_stats( $post_id, $comp, $season, $team );

		foreach ( $output as $key => $val ) {

			if ( 'pct' === $key ) {

				$combined_win    = $autostats['w'] + $manualstats['w'];
				$combined_played = $autostats['p'] + $manualstats['p'];
				if ( $combined_win > 0 || $combined_played > 0 ) {
					$wpct = $combined_win / $combined_played;
				} else {
					$wpct = '0';
				}

				$output[ $key ] = round( $wpct, 3 );

			} elseif ( 'footy' === $sport && 'gd' === $key ) {

				$combined_for     = $autostats['f'] + $manualstats['f'];
				$combined_against = $autostats['a'] + $manualstats['a'];
				if ( $combined_for > 0 || $combined_against > 0 ) {
					$gdpct = ( $combined_for / $combined_against ) * 100;
				} else {
					$gdpct = '0';
				}

				$output[ $key ] = round( $gdpct, 2 );

			} else {

				$output[ $key ] = $autostats[ $key ];
				if ( array_key_exists( $key, $manualstats ) ) {
					$output[ $key ] += $manualstats[ $key ];
				}
			}
		}

		return $output;
	}
}

/**
 * Store competition and season context for head-to-head lookups during sorting.
 *
 * @param int $comp   Competition term ID.
 * @param int $season Season term ID.
 */
function wpcm_set_h2h_context( $comp, $season ) {
	global $wpcm_h2h_comp, $wpcm_h2h_season;
	$wpcm_h2h_comp   = $comp;
	$wpcm_h2h_season = $season;
}

/**
 * Clear H2H context and in-memory cache after sorting is complete.
 */
function wpcm_clear_h2h_context() {
	global $wpcm_h2h_comp, $wpcm_h2h_season;
	$wpcm_h2h_comp   = null;
	$wpcm_h2h_season = null;
	wpcm_h2h_cache_reset();
}

/**
 * Reset the in-memory H2H results cache.
 */
function wpcm_h2h_cache_reset() {
	global $wpcm_h2h_cache;
	$wpcm_h2h_cache = array();
}

/**
 * Calculate head-to-head record between two clubs in the current H2H context.
 *
 * Results are cached in-memory for the duration of the request to avoid
 * repeated database queries when called from a usort comparator.
 *
 * Returns points, goal difference, and goals scored for each club
 * based only on their direct matches in the given competition and season.
 *
 * @param int $club_a_id Club A post ID.
 * @param int $club_b_id Club B post ID.
 * @return array {
 *     @type int $a_points Points earned by club A in H2H matches.
 *     @type int $b_points Points earned by club B in H2H matches.
 *     @type int $a_gd     Goal difference for club A in H2H matches.
 *     @type int $b_gd     Goal difference for club B in H2H matches.
 *     @type int $a_goals  Goals scored by club A in H2H matches.
 *     @type int $b_goals  Goals scored by club B in H2H matches.
 * }
 */
function wpcm_get_h2h_points( $club_a_id, $club_b_id ) {
	global $wpcm_h2h_comp, $wpcm_h2h_season, $wpcm_h2h_cache;

	if ( ! is_array( $wpcm_h2h_cache ) ) {
		$wpcm_h2h_cache = array();
	}
	$cache = &$wpcm_h2h_cache;

	// Return neutral result when H2H context is not set.
	if ( ! $wpcm_h2h_comp && ! $wpcm_h2h_season ) {
		return array(
			'a_points' => 0,
			'b_points' => 0,
			'a_gd'     => 0,
			'b_gd'     => 0,
			'a_goals'  => 0,
			'b_goals'  => 0,
		);
	}

	// Normalise cache key so (A,B) and (B,A) share the same entry.
	$lo  = min( $club_a_id, $club_b_id );
	$hi  = max( $club_a_id, $club_b_id );
	$key = "{$wpcm_h2h_comp}_{$wpcm_h2h_season}_{$lo}_{$hi}";

	if ( isset( $cache[ $key ] ) ) {
		$cached = $cache[ $key ];
		// If the caller asked with the IDs in the same order, return as-is.
		if ( $club_a_id === $lo ) {
			return $cached;
		}
		// Swap a/b in the result.
		return array(
			'a_points' => $cached['b_points'],
			'b_points' => $cached['a_points'],
			'a_gd'     => $cached['b_gd'],
			'b_gd'     => $cached['a_gd'],
			'a_goals'  => $cached['b_goals'],
			'b_goals'  => $cached['a_goals'],
		);
	}

	$win_pts  = (int) get_option( 'wpcm_standings_win_points', 3 );
	$draw_pts = (int) get_option( 'wpcm_standings_draw_points', 1 );
	$loss_pts = (int) get_option( 'wpcm_standings_loss_points', 0 );

	$result = array(
		'a_points' => 0,
		'b_points' => 0,
		'a_gd'     => 0,
		'b_gd'     => 0,
		'a_goals'  => 0,
		'b_goals'  => 0,
	);

	$tax_query = array();
	if ( $wpcm_h2h_comp ) {
		$tax_query[] = array(
			'taxonomy' => 'wpcm_comp',
			'terms'    => $wpcm_h2h_comp,
			'field'    => 'term_id',
		);
	}
	if ( $wpcm_h2h_season ) {
		$tax_query[] = array(
			'taxonomy' => 'wpcm_season',
			'terms'    => $wpcm_h2h_season,
			'field'    => 'term_id',
		);
	}

	// Find matches where club A is home and club B is away.
	$args = array(
		'post_type'      => 'wpcm_match',
		'posts_per_page' => -1,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => 'wpcm_home_club',
				'value' => $club_a_id,
			),
			array(
				'key'   => 'wpcm_away_club',
				'value' => $club_b_id,
			),
		),
		'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	);

	$matches = get_posts( $args );

	foreach ( $matches as $match ) {
		$played   = (int) get_post_meta( $match->ID, 'wpcm_played', true );
		$friendly = (int) get_post_meta( $match->ID, 'wpcm_friendly', true );

		if ( ! $played || $friendly ) {
			continue;
		}

		$home_goals = (int) get_post_meta( $match->ID, 'wpcm_home_goals', true );
		$away_goals = (int) get_post_meta( $match->ID, 'wpcm_away_goals', true );

		$result['a_goals'] += $home_goals;
		$result['b_goals'] += $away_goals;
		$result['a_gd']    += $home_goals - $away_goals;
		$result['b_gd']    += $away_goals - $home_goals;

		if ( $home_goals > $away_goals ) {
			$result['a_points'] += $win_pts;
			$result['b_points'] += $loss_pts;
		} elseif ( $home_goals < $away_goals ) {
			$result['a_points'] += $loss_pts;
			$result['b_points'] += $win_pts;
		} else {
			$result['a_points'] += $draw_pts;
			$result['b_points'] += $draw_pts;
		}
	}

	// Find matches where club B is home and club A is away.
	$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		array(
			'key'   => 'wpcm_home_club',
			'value' => $club_b_id,
		),
		array(
			'key'   => 'wpcm_away_club',
			'value' => $club_a_id,
		),
	);

	$matches = get_posts( $args );

	foreach ( $matches as $match ) {
		$played   = (int) get_post_meta( $match->ID, 'wpcm_played', true );
		$friendly = (int) get_post_meta( $match->ID, 'wpcm_friendly', true );

		if ( ! $played || $friendly ) {
			continue;
		}

		$home_goals = (int) get_post_meta( $match->ID, 'wpcm_home_goals', true );
		$away_goals = (int) get_post_meta( $match->ID, 'wpcm_away_goals', true );

		$result['b_goals'] += $home_goals;
		$result['a_goals'] += $away_goals;
		$result['b_gd']    += $home_goals - $away_goals;
		$result['a_gd']    += $away_goals - $home_goals;

		if ( $home_goals > $away_goals ) {
			$result['b_points'] += $win_pts;
			$result['a_points'] += $loss_pts;
		} elseif ( $home_goals < $away_goals ) {
			$result['b_points'] += $loss_pts;
			$result['a_points'] += $win_pts;
		} else {
			$result['a_points'] += $draw_pts;
			$result['b_points'] += $draw_pts;
		}
	}

	// Store result in cache, keyed with the lower ID first.
	if ( $club_a_id === $lo ) {
		$cache[ $key ] = $result;
	} else {
		$cache[ $key ] = array(
			'a_points' => $result['b_points'],
			'b_points' => $result['a_points'],
			'a_gd'     => $result['b_gd'],
			'b_gd'     => $result['a_gd'],
			'a_goals'  => $result['b_goals'],
			'b_goals'  => $result['a_goals'],
		);
	}

	return $result;
}

if ( ! function_exists( 'wpcm_table_priorities' ) ) {
	/**
	 * @return array
	 */
	function wpcm_table_priorities() {

		$priorities = array(
			array(
				'column' => get_option( 'wpcm_standings_orderby' ),
				'order'  => get_option( 'wpcm_standings_priority_order' ),
			),
			array(
				'column' => get_option( 'wpcm_standings_orderby_2' ),
				'order'  => get_option( 'wpcm_standings_priority_order_2' ),
			),
			array(
				'column' => get_option( 'wpcm_standings_orderby_3' ),
				'order'  => get_option( 'wpcm_standings_priority_order_3' ),
			),
		);
		return $priorities;
	}
}

if ( ! function_exists( 'wpcm_sort_table_clubs' ) ) {
	/**
	 * @param object $a
	 * @param object $b
	 *
	 * @return int
	 */
	function wpcm_sort_table_clubs( $a, $b ) {

		$priorities = wpcm_table_priorities();

		// Loop through priorities
		foreach ( $priorities as $priority ) {

			if ( 'h2h' === $priority['column'] ) {
				// Head-to-head tiebreaker: compare direct match record.
				$h2h = wpcm_get_h2h_points( $a->ID, $b->ID );

				// Compare H2H points first.
				if ( $h2h['a_points'] !== $h2h['b_points'] ) {
					$output = $h2h['a_points'] - $h2h['b_points'];
					if ( 'DESC' === $priority['order'] ) {
						$output = 0 - $output;
					}
					return ( $output > 0 ? 1 : -1 );
				}

				// If H2H points tied, compare H2H goal difference.
				if ( $h2h['a_gd'] !== $h2h['b_gd'] ) {
					$output = $h2h['a_gd'] - $h2h['b_gd'];
					if ( 'DESC' === $priority['order'] ) {
						$output = 0 - $output;
					}
					return ( $output > 0 ? 1 : -1 );
				}

				// If H2H GD tied, compare H2H goals scored.
				if ( $h2h['a_goals'] !== $h2h['b_goals'] ) {
					$output = $h2h['a_goals'] - $h2h['b_goals'];
					if ( 'DESC' === $priority['order'] ) {
						$output = 0 - $output;
					}
					return ( $output > 0 ? 1 : -1 );
				}

				// H2H completely tied, fall through to next priority.
				continue;
			}

			if ( wpcm_array_value( $a->wpcm_stats, $priority['column'], 0 ) !== wpcm_array_value( $b->wpcm_stats, $priority['column'], 0 ) ) {

				// Compare column values
				$output = wpcm_array_value( $a->wpcm_stats, $priority['column'], 0 ) - wpcm_array_value( $b->wpcm_stats, $priority['column'], 0 );

				// Flip value if descending order
				if ( 'DESC' === $priority['order'] ) {
					$output = 0 - $output;
				}

				return ( $output > 0 ? 1 : -1 );

			}
		}

		// Default sort by alphabetical
		return strcmp( $a->post_name, $b->post_name );
	}
}
