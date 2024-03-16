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

		foreach ( $a as $k => $v ) {

			$b[ $k ] = (float) $v->wpcm_stats[ $subkey ];
		}

		if ( null != $b ) {

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
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	function wpcm_sort_table_clubs( $a, $b ) {

		$priorities = wpcm_table_priorities();

		// Loop through priorities
		foreach ( $priorities as $priority ) {

			if ( wpcm_array_value( $a->wpcm_stats, $priority['column'], 0 ) != wpcm_array_value( $b->wpcm_stats, $priority['column'], 0 ) ) {

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
		// return strcmp( wpcm_array_value( $a, 'name', '' ), wpcm_array_value( $b, 'name', '' ) );
		return strcmp( $a->post_name, $b->post_name );
	}
}
