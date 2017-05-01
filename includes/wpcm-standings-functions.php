<?php
/**
 * WPClubManager Standings Functions. Code adapted from Football Club Theme by themeboy
 *
 * Functions for standings.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wpcm_get_standings_stats_selection( $stats ) {

	$stats = explode( ',', $stats );
	foreach( $stats as $key => $value ) {
		$stats[$key] = strtolower( trim( $value ) );
		if ( !array_key_exists( $stats[$key], wpcm_get_preset_labels( 'standings', 'label' ) ) )
			unset( $stats[$key] );
	}

	return $stats;
}

/**
 * Standing table sorting.
 *
 * @access public
 * @param array
 * @param array
 * @return int
 */
if ( !function_exists( 'wpcm_club_standings_sort' ) ) {
	function wpcm_club_standings_sort( $a, $b ) {

		if ( $a->wpcm_stats['pts'] > $b->wpcm_stats['pts'] ) {

			return -1;

		} elseif  ( $a->wpcm_stats['pts'] < $b->wpcm_stats['pts'] ) {

			return 1;

		} else {

			if ( $a->wpcm_stats['gd'] > $b->wpcm_stats['gd'] ) {

				return -1;

			} elseif  ($a->wpcm_stats['gd'] < $b->wpcm_stats['gd']) {

				return 1;

			} else {

				if ( $a->wpcm_stats['f'] > $b->wpcm_stats['f'] ) {

					return -1;

				} elseif ( $a->wpcm_stats['f'] < $b->wpcm_stats['f']  ) {

					return 1;

				} else {

					if ( strcmp( $a->post_title, $b->post_title ) < 0 ) {

						return -1;

					} else {

						return 1;
					}
				}
			}
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
if ( !function_exists( 'wpcm_club_standings_pct_sort' ) ) {
	function wpcm_club_standings_pct_sort( $a, $b ) {

		if ( $a->wpcm_stats['pct'] > $b->wpcm_stats['pct'] ) {

			return -1;

		} elseif  ( $a->wpcm_stats['pct'] < $b->wpcm_stats['pct'] ) {

			return 1;

		} else {

			if ( $a->wpcm_stats['w'] > $b->wpcm_stats['w'] ) {

				return -1;

			} elseif  ($a->wpcm_stats['w'] < $b->wpcm_stats['w']) {

				return 1;

			} else {

				if ( $a->wpcm_stats['f'] > $b->wpcm_stats['f'] ) {

					return -1;

				} elseif ( $a->wpcm_stats['f'] < $b->wpcm_stats['f']  ) {

					return 1;

				} else {

					if ( strcmp( $a->post_title, $b->post_title ) < 0 ) {

						return -1;

					} else {

						return 1;
					}
				}
			}
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
if (!function_exists('wpcm_club_standings_sort_by')) {
	function wpcm_club_standings_sort_by( $subkey, $a ) {

		foreach( $a as $k => $v ) {

			$b[$k] = (float) $v->wpcm_stats[$subkey];
		}

		if ( $b != null ) {

			arsort( $b );
			foreach( $b as $key=>$val ) {

				$c[] = $a[$key];
			}

			return $c;
		}

		return array();
	}
}