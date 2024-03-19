<?php
/**
 * WPClubManager Player Stats Functions.
 *
 * Functions for player stats.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get preset stats and standings.
 *
 * @param string $type
 * @param string $format
 * @return array
 */
function wpcm_get_preset_labels( $type = 'players', $format = 'label' ) {

	$sport = get_option( 'wpcm_sport' );
	$data  = wpcm_get_sport_presets();

	if ( 'standings' == $type ) {
		$stats = $data[ $sport ]['standings_columns'];
	} elseif ( 'players' == $type ) {
		$stats = $data[ $sport ]['stats_labels'];
	}

	foreach ( $stats as $key => $value ) {

		$output[ $key ] = $value[ $format ];
	}

	return $output;
}

/**
 * Get preset stats and standings.
 *
 * @param string $section
 *
 * @return array
 */
function wpcm_get_section_stats( $section = 'batting' ) {

	$sport = get_option( 'wpcm_sport' );
	$data  = wpcm_get_sport_presets();
	$stats = $data[ $sport ]['stats_labels'];

	foreach ( $stats as $key => $value ) {
		if ( $section == $value['section'] ) {

			$output[ $key ] = $value['label'];
		}
	}

	return $output;
}

if ( ! function_exists( 'get_wpcm_player_stats_empty_row' ) ) {
	/**
	 * Get empty player stats row.
	 *
	 * @access public
	 * @return mixed $output
	 */
	function get_wpcm_player_stats_empty_row() {

		$player_stats_labels = wpcm_get_preset_labels();

		$output = array( 'appearances' => 0 );

		foreach ( $player_stats_labels as $key => $val ) {
			$output[ $key ] = 0;
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_club_stats_empty_row' ) ) {
	/**
	 * Get empty club stats row.
	 *
	 * @access public
	 * @return array
	 */
	function get_wpcm_club_stats_empty_row() {

		$standings_stats_labels = wpcm_get_preset_labels( 'standings', 'label' );

		$output = array();

		foreach ( $standings_stats_labels as $key => $val ) {
			$output[ $key ] = 0;
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_player_total_stats' ) ) {
	/**
	 * Get total player stats.
	 *
	 * @access public
	 *
	 * @param string $post_id
	 * @param string $team
	 * @param string $season
	 *
	 * @return mixed $output
	 */
	function get_wpcm_player_total_stats( $post_id = null, $team = null, $season = null ) {

		$output      = get_wpcm_player_stats_empty_row();
		$autostats   = get_wpcm_player_auto_stats( $post_id, $team, $season );
		$manualstats = get_wpcm_player_manual_stats( $post_id, $team, $season );

		foreach ( $output as $key => $val ) {
			$output[ $key ] = $autostats[ $key ] + $manualstats[ $key ];
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_club_total_stats' ) ) {
	/**
	 * Get total club stats.
	 *
	 * @access public
	 *
	 * @param string $post_id
	 * @param string $comp
	 * @param string $season
	 *
	 * @return mixed $output
	 */
	function get_wpcm_club_total_stats( $post_id = null, $comp = null, $season = null ) {

		$output      = get_wpcm_club_stats_empty_row();
		$autostats   = get_wpcm_club_auto_stats( $post_id, $comp, $season );
		$manualstats = get_wpcm_club_manual_stats( $post_id, $comp, $season );

		foreach ( $output as $key => $val ) {

			if ( 'pct' == $key ) {

				$combined_win    = $autostats['w'] + $manualstats['w'];
				$combined_played = $autostats['p'] + $manualstats['p'];
				if ( $combined_win > 0 || $combined_played > 0 ) {
					$wpct = $combined_win / $combined_played;
				} else {
					$wpct = '0';
				}

				$output[ $key ] = round( $wpct, 3 );

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

if ( ! function_exists( 'get_wpcm_player_manual_stats' ) ) {
	/**
	 * Get manual player stats.
	 *
	 * @access public
	 *
	 * @param string $post_id
	 * @param string $team
	 * @param string $season
	 *
	 * @return mixed $output
	 */
	function get_wpcm_player_manual_stats( $post_id = null, $team = null, $season = null ) {

		$output = get_wpcm_player_stats_empty_row();

		if ( empty( $team ) ) {
			$team = 0;
		}
		if ( empty( $season ) ) {
			$season = 0;
		}

		$stats = unserialize( get_post_meta( $post_id, 'wpcm_stats', true ) );

		if ( is_array( $stats ) && array_key_exists( $team, $stats ) ) {

			if ( is_array( $stats[ $team ] ) && array_key_exists( $season, $stats[ $team ] ) ) {

				$output = $stats[ $team ][ $season ];
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_club_manual_stats' ) ) {
	/**
	 * Get manual club stats.
	 *
	 * @access public
	 *
	 * @param string $post_id
	 * @param string $comp
	 * @param string $season
	 *
	 * @return mixed $output
	 */
	function get_wpcm_club_manual_stats( $post_id = null, $comp = null, $season = null ) {

		$output = get_wpcm_club_stats_empty_row();

		if ( empty( $comp ) ) {
			$comp = 0;
		}
		if ( empty( $season ) ) {
			$season = 0;
		}

		$stats = unserialize( get_post_meta( $post_id, 'wpcm_stats', true ) );

		if ( is_array( $stats ) && array_key_exists( $comp, $stats ) ) {

			if ( is_array( $stats[ $comp ] ) && array_key_exists( $season, $stats[ $comp ] ) ) {

				$output = $stats[ $comp ][ $season ];
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_player_auto_stats' ) ) {
	/**
	 * Get auto player stats.
	 *
	 * @access public
	 *
	 * @param string $post_id
	 * @param string $team
	 * @param string $season_id
	 *
	 * @return mixed $output
	 */
	function get_wpcm_player_auto_stats( $post_id = null, $team = null, $season_id = null ) {

		// if ( !$post_id ) global $post_id;

		$stats_labels = wpcm_get_preset_labels();

		$club_id = get_default_club();
		$output  = get_wpcm_player_stats_empty_row();

		$args = array(
			'post_type'  => 'wpcm_match',
			'tax_query'  => array(),
			'showposts'  => -1,
			'meta_query' => array(
				array(
					'relation' => 'OR',
					array(
						'key'   => 'wpcm_home_club',
						'value' => $club_id,
					),
					array(
						'key'   => 'wpcm_away_club',
						'value' => $club_id,
					),
					array(
						'key'     => 'wpcm_friendly',
						'value'   => array( '', null ),
						'compare' => 'IN',
					),
				),
			),
		);

		if ( isset( $team ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'terms'    => $team,
				'field'    => 'term_id',
			);
		}

		if ( isset( $season_id ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms'    => $season_id,
			);
		}

		$matches = get_posts( $args );

		foreach ( $matches as $match ) {

			$all_players = unserialize( get_post_meta( $match->ID, 'wpcm_players', true ) );

			if ( is_array( $all_players ) ) {

				unset( $all_players['subs_not_used'] );

				foreach ( $all_players as $players ) {

					if ( is_array( $players ) && array_key_exists( $post_id, $players ) ) {

						$stats = $players[ $post_id ];
						++$output['appearances'];

						foreach ( $stats as $key => $value ) {
							if ( array_key_exists( $key, $stats_labels ) ) {
								if ( isset( $stats[ $key ] ) ) {
									$output[ $key ] += $stats[ $key ]; }
							}
						}
					}
				}
			}
		}

		return $output;
	}
}

/**
 * Get auto club stats.
 *
 * @access public
 * @param string $post_id
 * @param string $comp
 * @param string $season
 * @return mixed $output
 */
if ( ! function_exists( 'get_wpcm_club_auto_stats' ) ) {
	/**
	 * @param int $post_id
	 * @param int $comp
	 * @param int $season
	 * @param int $team
	 *
	 * @return array
	 */
	function get_wpcm_club_auto_stats( $post_id = null, $comp = null, $season = null, $team = null ) {

		if ( ! $post_id ) {
			global $post_id;
		}

		$output = get_wpcm_club_stats_empty_row();

		// get all home matches
		$args = array(
			'post_type'  => 'wpcm_match',
			'meta_key'   => 'wpcm_home_club',
			'meta_value' => $post_id,
			'tax_query'  => array(),
			'showposts'  => -1,
		);

		if ( isset( $comp ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_comp',
				'terms'    => $comp,
				'field'    => 'term_id',
			);
		}

		if ( isset( $season ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms'    => $season,
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

		foreach ( $matches as $match ) {

			$played    = get_post_meta( $match->ID, 'wpcm_played', true );
			$friendly  = get_post_meta( $match->ID, 'wpcm_friendly', true );
			$postponed = get_post_meta( $match->ID, '_wpcm_postponed', true );
			$overtime  = get_post_meta( $match->ID, 'wpcm_overtime', true );
			$walkover  = get_post_meta( $match->ID, '_wpcm_walkover', true );

			if ( $played && ! $friendly && ! $postponed ) {

				if ( get_option( 'wpcm_sport' ) == 'cricket' ) {
					$runs   = unserialize( get_post_meta( $match->ID, '_wpcm_match_runs', true ) );
					$extras = unserialize( get_post_meta( $match->ID, '_wpcm_match_extras', true ) );
					$f      = $runs['home'] + $extras['home'];
					$a      = $runs['away'] + $extras['away'];
				} else {
					$f = get_post_meta( $match->ID, 'wpcm_home_goals', true );
					$a = get_post_meta( $match->ID, 'wpcm_away_goals', true );
				}
				$hb   = get_post_meta( $match->ID, 'wpcm_home_bonus', true );
				$won  = 0 == $overtime && (int) ( $f > $a );
				$draw = (int) ( $f == $a );
				$lost = 0 == $overtime && (int) ( $f < $a );
				$otw  = 1 == $overtime && (int) ( $f > $a );
				$otl  = 1 == $overtime && (int) ( $f < $a );
				++$output['p'];
				$output['w'] += $won;
				if ( array_key_exists( 'd', $output ) ) {
					$output['d']   += $draw;
					$output['pts'] += $draw * get_option( 'wpcm_standings_draw_points' );
				}
				$output['l'] += $lost;
				if ( array_key_exists( 'otw', $output ) ) {
					$output['otw'] += $otw;
					$output['pts'] += $otw * get_option( 'wpcm_standings_otw_points' );
				}
				if ( array_key_exists( 'otl', $output ) ) {
					$output['otl'] += $otl;
					$output['pts'] += $otl * get_option( 'wpcm_standings_otl_points' );
				}
				$output['f'] += $f;
				$output['a'] += $a;
				if ( array_key_exists( 'gd', $output ) ) {
					$output['gd'] += $f - $a;
				}
				if ( array_key_exists( 'b', $output ) ) {
					$output['b']   += $hb;
					$output['pts'] += $hb;
				}
				$output['pts'] += $won * get_option( 'wpcm_standings_win_points' ) + $lost * get_option( 'wpcm_standings_loss_points' );
			}
			if ( $postponed && 'home_win' == $walkover ) {
				++$output['p'];
				$output['w']   += 1;
				$output['pts'] += get_option( 'wpcm_standings_win_points' );
			} elseif ( $postponed && 'away_win' == $walkover ) {
				++$output['p'];
				$output['l']   += 1;
				$output['pts'] += get_option( 'wpcm_standings_loss_points' );
			}
		}

		$args['meta_key'] = 'wpcm_away_club';

		$matches = get_posts( $args );

		foreach ( $matches as $match ) {

			$played    = get_post_meta( $match->ID, 'wpcm_played', true );
			$friendly  = get_post_meta( $match->ID, 'wpcm_friendly', true );
			$postponed = get_post_meta( $match->ID, '_wpcm_postponed', true );
			$overtime  = get_post_meta( $match->ID, 'wpcm_overtime', true );
			$walkover  = get_post_meta( $match->ID, '_wpcm_walkover', true );

			if ( $played && ! $friendly && ! $postponed ) {

				if ( get_option( 'wpcm_sport' ) == 'cricket' ) {
					$runs   = unserialize( get_post_meta( $match->ID, '_wpcm_match_runs', true ) );
					$extras = unserialize( get_post_meta( $match->ID, '_wpcm_match_extras', true ) );
					$f      = $runs['away'] + $extras['away'];
					$a      = $runs['home'] + $extras['home'];
				} else {
					$f = get_post_meta( $match->ID, 'wpcm_away_goals', true );
					$a = get_post_meta( $match->ID, 'wpcm_home_goals', true );
				}
				$ab   = get_post_meta( $match->ID, 'wpcm_away_bonus', true );
				$won  = 0 == $overtime && (int) ( $f > $a );
				$draw = (int) ( $f == $a );
				$lost = 0 == $overtime && (int) ( $f < $a );
				$otw  = 1 == $overtime && (int) ( $f > $a );
				$otl  = 1 == $overtime && (int) ( $f < $a );
				++$output['p'];
				$output['w'] += $won;
				if ( array_key_exists( 'd', $output ) ) {
					$output['d']   += $draw;
					$output['pts'] += $draw * get_option( 'wpcm_standings_draw_points' );
				}
				$output['l'] += $lost;
				if ( array_key_exists( 'otw', $output ) ) {
					$output['otw'] += $otw;
					$output['pts'] += $otw * get_option( 'wpcm_standings_otw_points' );
				}
				if ( array_key_exists( 'otl', $output ) ) {
					$output['otl'] += $otl;
					$output['pts'] += $otl * get_option( 'wpcm_standings_otl_points' );
				}
				$output['f'] += $f;
				$output['a'] += $a;
				if ( array_key_exists( 'gd', $output ) ) {
					$output['gd'] += $f - $a;
				}
				if ( array_key_exists( 'b', $output ) ) {
					$output['b']   += $ab;
					$output['pts'] += $ab;
				}
				$output['pts'] += $won * get_option( 'wpcm_standings_win_points' ) + $lost * get_option( 'wpcm_standings_loss_points' );
			}
			if ( $postponed && 'away_win' == $walkover ) {
				++$output['p'];
				$output['w']   += 1;
				$output['pts'] += get_option( 'wpcm_standings_win_points' );
			} elseif ( $postponed && 'home_win' === $walkover ) {
				++$output['p'];
				$output['l']   += 1;
				$output['pts'] += get_option( 'wpcm_standings_loss_points' );
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'get_wpcm_player_stats' ) ) {

	/**
	 * Get total player stats.
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	function get_wpcm_player_stats( $post = null ) {

		if ( ! $post ) {
			global $post;
		}

		$output  = array();
		$teams   = wp_get_object_terms( $post, 'wpcm_team', array( 'orderby' => 'tax_position' ) );
		$seasons = wp_get_object_terms( $post, 'wpcm_season', array( 'orderby' => 'tax_position' ) );

		// isolated team stats
		if ( is_array( $teams ) ) {

			foreach ( $teams as $team ) {

				// combined season stats per team
				$stats                       = get_wpcm_player_auto_stats( $post, $team->term_id, null );
				$output[ $team->term_id ][0] = array(
					'auto'  => $stats,
					'total' => $stats,
				);

				// isolated season stats per team
				if ( is_array( $seasons ) ) {

					foreach ( $seasons as $season ) {

						$stats                                        = get_wpcm_player_auto_stats( $post, $team->term_id, $season->term_id );
						$output[ $team->term_id ][ $season->term_id ] = array(
							'auto'  => $stats,
							'total' => $stats,
						);
					}
				}
			}
		}

		// combined season stats for combined team
		$stats        = get_wpcm_player_auto_stats( $post );
		$manual_stats = get_wpcm_player_manual_stats( $post, $team->term_id, $season->term_id );
		$output[0][0] = array(
			'auto'   => $stats,
			'total'  => $stats,
			'manual' => $manual_stats,
		);

		// isolated season stats for combined team
		if ( is_array( $seasons ) ) {

			foreach ( $seasons as $season ) {

				$stats                         = get_wpcm_player_auto_stats( $post, null, $season->term_id );
				$output[0][ $season->term_id ] = array(
					'auto'  => $stats,
					'total' => $stats,
				);
			}
		}

		// manual stats
		$manual_stats = (array) unserialize( get_post_meta( $post, 'wpcm_stats', true ) );

		if ( is_array( $manual_stats ) ) {

			foreach ( $manual_stats as $team_key => $team_val ) {

				if ( is_array( $team_val ) && array_key_exists( $team_key, $output ) ) {

					foreach ( $team_val as $season_key => $season_val ) {

						if ( array_key_exists( $season_key, $output[ $team_key ] ) ) {

							$output[ $team_key ][ $season_key ]['manual'] = $season_val;

							foreach ( $output[ $team_key ][ $season_key ]['total'] as $index_key => &$index_val ) {

								if ( array_key_exists( $index_key, $season_val ) ) {

									$index_val += $season_val[ $index_key ];
								}
							}
						}
					}
				}
			}
		}

		return $output;
	}

}

if ( ! function_exists( 'get_wpcm_club_stats' ) ) {
	/**
	 * Get club stats.
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	function get_wpcm_club_stats( $post = null ) {

		if ( ! $post ) {
			global $post;
		}

		$output  = array();
		$comps   = wp_get_object_terms( $post->ID, 'wpcm_comp' );
		$seasons = wp_get_object_terms( $post->ID, 'wpcm_season' );

		// isolated competition stats
		if ( is_array( $comps ) ) {

			foreach ( $comps as $comp ) {

				// combined season stats per competition
				$stats                       = get_wpcm_club_auto_stats( $post->ID, $comp->term_id, null );
				$output[ $comp->term_id ][0] = array(
					'auto'  => $stats,
					'total' => $stats,
				);

				// isolated season stats per competition
				if ( is_array( $seasons ) ) {

					foreach ( $seasons as $season ) {

						$stats                                        = get_wpcm_club_auto_stats( $post->ID, $comp->term_id, $season->term_id );
						$output[ $comp->term_id ][ $season->term_id ] = array(
							'auto'  => $stats,
							'total' => $stats,
						);
					}
				}
			}
		}

		// combined season stats for combined competitions
		$stats        = get_wpcm_club_auto_stats( $post->ID );
		$output[0][0] = array(
			'auto'  => $stats,
			'total' => $stats,
		);

		// isolated season stats for combined competitions
		if ( is_array( $seasons ) ) {

			foreach ( $seasons as $season ) {

				$stats                         = get_wpcm_club_auto_stats( $post->ID, null, $season->term_id );
				$output[0][ $season->term_id ] = array(
					'auto'  => $stats,
					'total' => $stats,
				);
			}
		}

		// manual stats
		$stats = (array) unserialize( get_post_meta( $post->ID, 'wpcm_stats', true ) );

		if ( is_array( $stats ) ) {

			foreach ( $stats as $comp_key => $comp_val ) {

				if ( is_array( $comp_val ) && array_key_exists( $comp_key, $output ) ) {

					foreach ( $comp_val as $season_key => $season_val ) {

						if ( array_key_exists( $season_key, $output[ $comp_key ] ) ) {

							$output[ $comp_key ][ $season_key ]['manual'] = $season_val;

							foreach ( $output[ $comp_key ][ $season_key ]['total'] as $index_key => &$index_val ) {

								if ( array_key_exists( $index_key, $season_val ) ) {

									$index_val += $season_val[ $index_key ];
								}
							}
						}
					}
				}
			}
		}

		return $output;
	}
}

/**
 * Get player subtitute appearances.
 *
 * @access public
 *
 * @param int $id
 * @param int $season
 * @param int $team
 *
 * @return int $total_subs
 */
function get_player_subs_total( $id = null, $season = null, $team = null ) {

	// convert atts to something more useful
	if ( $season <= 0 ) {
		$season = null;
	}
	if ( $team <= 0 ) {
		$team = null;
	}

	$default_club = get_option( 'wpcm_default_club' );

	// get results
	$query_args               = array(
		'tax_query'      => array(),
		'numberposts'    => '-1',
		'order'          => 'ASC',
		'orderby'        => 'post_date',
		'post_type'      => 'wpcm_match',
		'post_status'    => 'publish',
		'posts_per_page' => '-1',
	);
	$query_args['meta_query'] = array(
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
		$query_args['tax_query'][] = array(
			'taxonomy' => 'wpcm_season',
			'terms'    => $season,
			'field'    => 'term_id',
		);
	}
	if ( isset( $team ) ) {
		$query_args['tax_query'][] = array(
			'taxonomy' => 'wpcm_team',
			'terms'    => $team,
			'field'    => 'term_id',
		);
	}

	$matches = get_posts( $query_args );

	$size = count( $matches );

	$total_subs = '0';

	if ( $size > 0 ) {

		$total_subs = 0;

		foreach ( $matches as $match ) {

			$player = unserialize( get_post_meta( $match->ID, 'wpcm_players', true ) );

			if ( is_array( $player ) && array_key_exists( 'subs', $player ) && array_key_exists( $id, $player['subs'] ) ) {

				++$total_subs;

			}
		}
	}

	return $total_subs;
}

if ( ! function_exists( 'get_wpcm_stats_value' ) ) {
	/**
	 * Match player subs dropdown.
	 *
	 * @param array  $stats
	 * @param string $type
	 * @param string $index
	 *
	 * @return float|int
	 */
	function get_wpcm_stats_value( $stats = array(), $type = 'manual', $index = 'goals' ) {

		if ( is_array( $stats ) ) {

			if ( array_key_exists( $type, $stats ) ) {

				if ( array_key_exists( $index, $stats[ $type ] ) ) {

					return (float) $stats[ $type ][ $index ];
				}
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'wpcm_stats_value' ) ) {
	/**
	 * Get the value of the stats.
	 *
	 * @param array  $stats
	 * @param string $type
	 * @param string $index
	 *
	 * @return void
	 */
	function wpcm_stats_value( $stats, $type, $index ) {

		echo esc_html( get_wpcm_stats_value( $stats, $type, $index ) );
	}
}
