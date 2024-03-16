<?php
/**
 * WPClubManager Match Functions.
 *
 * Functions for matches.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Generate title
 *
 * @param string $title
 * @param int    $id
 *
 * @return mixed|string
 */
function match_title( $title, $id = null ) {
	if ( get_post_type( $id ) == 'wpcm_match' ) {

		$default_club = get_default_club();
		$title_format = get_match_title_format();
		$separator    = get_option( 'wpcm_match_clubs_separator' );
		$home_id      = (int) get_post_meta( $id, 'wpcm_home_club', true );
		$away_id      = (int) get_post_meta( $id, 'wpcm_away_club', true );
		$home_club    = get_post( $home_id );
		$away_club    = get_post( $away_id );
		if ( '%home% vs %away%' === $title_format ) {
			$side1 = $home_club->post_title;
			$side2 = $away_club->post_title;
		} else {
			$side1 = $away_club->post_title;
			$side2 = $home_club->post_title;
		}

		$title = $side1 . ' ' . $separator . ' ' . $side2;
	}
	return $title;
}
add_filter( 'the_title', 'match_title', 10, 2 );

/**
 * Generate match title
 *
 * @param string $title
 *
 * @return mixed|string
 */
function match_wp_title( $title ) {
	if ( get_post_type() == 'wpcm_match' ) {

		$id    = get_the_ID();
		$title = match_title( $title, $id ) . ' | ' . get_the_date() . ' | ';
	}
	return $title;
}
add_filter( 'wp_title', 'match_wp_title', 10, 2 );

/**
 * Save ajax menu_order sortable
 *
 * @access public
 * @since 1.1.9
 */
function wpcm_match_players_item_order() {

	global $wpdb;

	$order   = explode( ',', $_POST['order'] ); // phpcs:ignore
	$counter = 0;
	foreach ( $order as $item_id ) {
		if ( ! is_int( $item_id ) ) {
			continue;
		}
		$wpdb->update( $wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id ) );
		++$counter;
	}
	die( 1 );
}
add_action( 'wp_ajax_item_sort', 'wpcm_match_players_item_order' );
add_action( 'wp_ajax_nopriv_item_sort', 'wpcm_match_players_item_order' );

/**
 * Get match outcome - win, loss or draw.
 *
 * @access public
 * @param int $post
 * @return string $outcome
 * @since 1.4.0
 */
function wpcm_get_match_outcome( $post ) {

	$club      = get_default_club();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$walkover  = get_post_meta( $post, '_wpcm_walkover', true );
	$postponed = get_post_meta( $post, '_wpcm_postponed', true );
	if ( get_option( 'wpcm_sport' ) !== 'cricket' ) {
		if ( get_post_meta( $post, 'wpcm_shootout', true ) ) {
			$home_goals = get_post_meta( $post, '_wpcm_home_shootout_goals', true );
			$away_goals = get_post_meta( $post, '_wpcm_away_shootout_goals', true );
		} else {
			$home_goals = get_post_meta( $post, 'wpcm_home_goals', true );
			$away_goals = get_post_meta( $post, 'wpcm_away_goals', true );
		}
	} else {
		$runs       = unserialize( get_post_meta( $post, '_wpcm_match_runs', true ) );
		$extras     = unserialize( get_post_meta( $post, '_wpcm_match_extras', true ) );
		$home_goals = $runs['home'] + $extras['home'];
		$away_goals = $runs['away'] + $extras['away'];
	}
	if ( $postponed ) {
		if ( '' != $walkover ) {
			if ( $club == $home_club ) {
				if ( 'home_win' === $walkover ) {
					$outcome = 'win';
				} elseif ( 'away_win' === $walkover ) {
					$outcome = 'loss';
				}
			} elseif ( 'home_win' === $walkover ) {
					$outcome = 'loss';
			} elseif ( 'away_win' === $walkover ) {
				$outcome = 'win';
			}
		} else {
			$outcome = 'postponed';
		}
	} else {
		if ( $home_goals == $away_goals ) {
			$outcome = 'draw';
		}
		if ( $club == $home_club ) {
			if ( $home_goals > $away_goals ) {
				$outcome = 'win';
			}
			if ( $home_goals < $away_goals ) {
				$outcome = 'loss';
			}
		} else {
			if ( $home_goals > $away_goals ) {
				$outcome = 'loss';
			}
			if ( $home_goals < $away_goals ) {
				$outcome = 'win';
			}
		}
	}

	return $outcome;
}

if ( ! function_exists( 'wpcm_get_match_result' ) ) {
	/**
	 * Get match result.
	 *
	 * @access public
	 *
	 * @param int $post
	 *
	 * @return string $result
	 * @since  1.4.6
	 */
	function wpcm_get_match_result( $post ) {

		$sport      = get_option( 'wpcm_sport' );
		$format     = get_match_title_format();
		$hide       = get_option( 'wpcm_hide_scores' );
		$delimiter  = get_option( 'wpcm_match_goals_delimiter' );
		$played     = get_post_meta( $post, 'wpcm_played', true );
		$postponed  = get_post_meta( $post, '_wpcm_postponed', true );
		$walkover   = get_post_meta( $post, '_wpcm_walkover', true );
		$home_goals = get_post_meta( $post, 'wpcm_home_goals', true );
		$away_goals = get_post_meta( $post, 'wpcm_away_goals', true );
		if ( 'gaelic' === $sport ) {
			$home_gaa_goals  = get_post_meta( $post, 'wpcm_home_gaa_goals', true );
			$home_gaa_points = get_post_meta( $post, 'wpcm_home_gaa_points', true );
			$away_gaa_goals  = get_post_meta( $post, 'wpcm_away_gaa_goals', true );
			$away_gaa_points = get_post_meta( $post, 'wpcm_away_gaa_points', true );
		}
		if ( 'cricket' === $sport ) {
			$runs            = unserialize( get_post_meta( $post, '_wpcm_match_runs', true ) );
			$extras          = unserialize( get_post_meta( $post, '_wpcm_match_extras', true ) );
			$wickets         = unserialize( get_post_meta( $post, '_wpcm_match_wickets', true ) );
			$cricket_outcome = get_post_meta( $post, '_wpcm_cricket_outcome', true );
			if ( is_array( $cricket_outcome ) ) {
				if ( 'won_by' === $cricket_outcome[0] ) {
					$outcome1 = __( 'Won by', 'wp-club-manager' );
				} elseif ( 'lost_by' === $cricket_outcome[0] ) {
					$outcome1 = __( 'Lost by', 'wp-club-manager' );
				} elseif ( 'drawn' === $cricket_outcome[0] ) {
					$outcome1 = __( 'Match Drawn', 'wp-club-manager' );
				}
				if ( 'runs' === $cricket_outcome[2] ) {
					$outcome2 = __( 'runs', 'wp-club-manager' );
				} elseif ( 'wickets' === $cricket_outcome[2] ) {
					$outcome2 = __( 'wickets', 'wp-club-manager' );
				} elseif ( 'innings' === $cricket_outcome[2] ) {
					$outcome2 = __( 'innings', 'wp-club-manager' );
				}
				$outcome = $outcome1 . ' ' . $cricket_outcome[1] . ' ' . $outcome2;
			} else {
				$outcome = '';
			}
		}

		if ( $postponed ) {
			if ( 'home_win' === $walkover ) {
				$result = _x( 'H', 'HW - home walkover', 'wp-club-manager' ) . ' ' . $delimiter . ' ' . _x( 'W', 'HW - home walkover', 'wp-club-manager' );
				$side1  = _x( 'H', 'HW - home walkover', 'wp-club-manager' );
				$side2  = _x( 'W', 'HW - home walkover', 'wp-club-manager' );
			} elseif ( 'away_win' === $walkover ) {
				$result = _x( 'A', 'AW - away walkover', 'wp-club-manager' ) . ' ' . $delimiter . ' ' . _x( 'W', 'AW - away walkover', 'wp-club-manager' );
				$side1  = _x( 'A', 'AW - away walkover', 'wp-club-manager' );
				$side2  = _x( 'W', 'AW - away walkover', 'wp-club-manager' );
			} else {
				$result = _x( 'P', 'Postponed', 'wp-club-manager' ) . ' ' . $delimiter . ' ' . _x( 'P', 'Postponed', 'wp-club-manager' );
				$side1  = _x( 'P', 'Postponed', 'wp-club-manager' );
				$side2  = _x( 'P', 'Postponed', 'wp-club-manager' );
			}
		} elseif ( 'yes' === $hide && ! is_user_logged_in() ) {
			$result = ( $played ? __( 'x', 'wp-club-manager' ) . ' ' . $delimiter . ' ' . __( 'x', 'wp-club-manager' ) : '' );
			$side1  = __( 'x', 'wp-club-manager' );
			$side2  = __( 'x', 'wp-club-manager' );
		} elseif ( '%home% vs %away%' === $format ) {
			if ( 'gaelic' === $sport ) {
				$result = ( $played ? $home_gaa_goals . '-' . $home_gaa_points . ' ' . $delimiter . ' ' . $away_gaa_goals . '-' . $away_gaa_points : '' );
				$side1  = ( $played ? $home_gaa_goals . '-' . $home_gaa_points : '-' );
				$side2  = ( $played ? $away_gaa_goals . '-' . $away_gaa_points : '-' );

			} elseif ( 'cricket' === $sport ) {

				$home_score   = $runs['home'] + $extras['home'];
				$away_score   = $runs['away'] + $extras['away'];
				$home_wickets = ( '10' == $wickets['home'] ? '' : '/' . $wickets['home'] );
				$away_wickets = ( '10' == $wickets['away'] ? '' : '/' . $wickets['away'] );

				// $result = ( $played ? $home_score . $home_wickets . ' ' . $delimiter . ' ' . $away_score . $away_wickets : '' );
				$result = $outcome;
				$side1  = ( $played ? $home_score . $home_wickets : '-' );
				$side2  = ( $played ? $away_score . $away_wickets : '-' );

			} else {

				$result = ( $played ? $home_goals . ' ' . $delimiter . ' ' . $away_goals : '' );
				$side1  = ( $played ? $home_goals : '' );
				$side2  = ( $played ? $away_goals : '' );

			}
		} elseif ( 'gaelic' === $sport ) {

				$result = ( $played ? $away_gaa_goals . '-' . $away_gaa_points . ' ' . $delimiter . ' ' . $home_gaa_goals . '-' . $home_gaa_points : '' );
				$side1  = ( $played ? $away_gaa_goals . '-' . $away_gaa_points : '-' );
				$side2  = ( $played ? $home_gaa_goals . '-' . $home_gaa_points : '-' );

		} elseif ( 'cricket' === $sport ) {

			$home_score   = $runs['home'] + $extras['home'];
			$away_score   = $runs['away'] + $extras['away'];
			$home_wickets = ( '10' == $wickets['home'] ? '' : '/' . $wickets['home'] );
			$away_wickets = ( '10' == $wickets['away'] ? '' : '/' . $wickets['away'] );

			// $result = ( $played ? $away_score . $away_wickets . ' ' . $delimiter . ' ' . $home_score . $home_wickets : '' );
			$result = $outcome;
			$side1  = ( $played ? $away_score . $away_wickets : '-' );
			$side2  = ( $played ? $home_score . $home_wickets : '-' );

		} else {

			$result = ( $played ? $away_goals . ' ' . $delimiter . ' ' . $home_goals : '' );
			$side1  = ( $played ? $away_goals : '' );
			$side2  = ( $played ? $home_goals : '' );
		}

		// return $result;

		return array( $result, $side1, $side2, $delimiter );
	}
}

/**
 * Get match competition.
 *
 * @access public
 * @param int $post
 * @return array
 * @since 1.4.0
 */
function wpcm_get_match_comp( $post ) {

	$competitions = get_the_terms( $post, 'wpcm_comp' );
	$status       = get_post_meta( $post, 'wpcm_comp_status', true );

	if ( is_array( $competitions ) ) {
		foreach ( $competitions as $competition ) :
			$comp             = $competition->name;
			$competition      = reset( $competitions );
			$t_id             = $competition->term_id;
			$competition_meta = get_option( "taxonomy_term_$t_id" );
			if ( is_array( $competition_meta ) && ! empty( $competition_meta['wpcm_comp_label'] ) ) {
				$label = $competition_meta['wpcm_comp_label'];
			} else {
				$label = $comp;
			}
		endforeach;
	}

	return array( $comp, $label, $status );
}

/**
 * Get match team.
 *
 * @access public
 *
 * @param int $post
 *
 * @return array
 * @since  1.4.0
 */
function wpcm_get_match_team( $post ) {

	$teams = get_the_terms( $post, 'wpcm_team' );

	if ( is_array( $teams ) ) {
		foreach ( $teams as $team ) {
			$name      = $team->name;
			$team      = reset( $teams );
			$t_id      = $team->term_id;
			$team_meta = get_option( "taxonomy_term_$t_id" );
			if ( is_array( $team_meta ) && ! empty( $team_meta['wpcm_team_label'] ) ) {
				$label = $team_meta['wpcm_team_label'];
			} else {
				$label = $name;
			}
		}
	} else {
		$name  = '';
		$label = '';
	}

	return array( $name, $label );
}

/**
 * Get match team names.
 *
 * @access public
 *
 * @param int  $post
 * @param bool $abbr
 *
 * @return array $side1 $side2
 * @since  2.1.0
 */
function wpcm_get_match_clubs( $post, $abbr = false ) {

	$format    = get_match_title_format();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );

	if ( false == $abbr ) {
		if ( '%home% vs %away%' === $format ) {
			$side1 = wpcm_get_team_name( $home_club, $post );
			$side2 = wpcm_get_team_name( $away_club, $post );
		} else {
			$side1 = wpcm_get_team_name( $away_club, $post );
			$side2 = wpcm_get_team_name( $home_club, $post );
		}
	} elseif ( '%home% vs %away%' === $format ) {
			$side1 = get_club_abbreviation( $home_club );
			$side2 = get_club_abbreviation( $away_club );
	} else {
		$side1 = get_club_abbreviation( $away_club );
		$side2 = get_club_abbreviation( $home_club );
	}

	return array( $side1, $side2 );
}

/**
 * Get match opponents.
 *
 * @access public
 *
 * @param int  $post
 * @param bool $abbr
 *
 * @return string $opponent
 * @since  2.1.0
 */
function wpcm_get_match_opponents( $post, $abbr = false ) {

	$club      = get_default_club();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );
	$opponent  = '';
	if ( false == $abbr ) {
		if ( $club == $home_club ) {
			$opponent = get_the_title( $away_club, true );
		} elseif ( $club == $away_club ) {
			$opponent = get_the_title( $home_club, true );
		}
	} elseif ( $club == $home_club ) {
			$opponent = get_club_abbreviation( $away_club );
	} elseif ( $club == $away_club ) {
		$opponent = get_club_abbreviation( $home_club );
	}

	return $opponent;
}

/**
 * Get match club badges.
 *
 * @access public
 *
 * @param int         $post
 * @param null|string $size
 * @param null|array  $args
 *
 * @return array $home_badge $away_badge
 * @since  1.4.0
 */
function wpcm_get_match_badges( $post, $size = null, $args = null ) {

	$format    = get_match_title_format();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );

	if ( '%home% vs %away%' === $format ) {
		if ( has_post_thumbnail( $home_club ) ) {
			$badge1 = get_the_post_thumbnail( $home_club, $size, $args );
		} else {
			$badge1 = wpcm_crest_placeholder_img( $size );
		}
		if ( has_post_thumbnail( $away_club ) ) {
			$badge2 = get_the_post_thumbnail( $away_club, $size, $args );
		} else {
			$badge2 = wpcm_crest_placeholder_img( $size );
		}
	} else {
		if ( has_post_thumbnail( $away_club ) ) {
			$badge1 = get_the_post_thumbnail( $away_club, $size, $args );
		} else {
			$badge1 = wpcm_crest_placeholder_img( $size );
		}
		if ( has_post_thumbnail( $home_club ) ) {
			$badge2 = get_the_post_thumbnail( $home_club, $size, $args );
		} else {
			$badge2 = wpcm_crest_placeholder_img( $size );
		}
	}

	return array( $badge1, $badge2 );
}

/**
 * Get match venue.
 *
 * @access public
 * @param int $post
 * @return string $venue
 * @since 1.4.6
 */
function wpcm_get_match_venue( $post ) {

	$club      = get_default_club();
	$venues    = get_the_terms( $post, 'wpcm_venue' );
	$neutral   = get_post_meta( $post, 'wpcm_neutral', true );
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );

	if ( is_array( $venues ) ) {
		$venue                     = reset( $venues );
		$venue_info['name']        = $venue->name;
		$venue_info['id']          = $venue->term_id;
		$venue_info['description'] = $venue->description;
		$venue_meta                = get_option( 'taxonomy_term_' . $venue_info['id'] . '' );
		if ( $venue_meta ) {
			$venue_info['address']  = $venue_meta['wpcm_address'];
			$venue_info['capacity'] = $venue_meta['wpcm_capacity'];
		}
	} else {
		$venue_info['name']        = null;
		$venue_info['id']          = null;
		$venue_info['description'] = null;
		$venue_info['address']     = null;
		$venue_info['capacity']    = null;
	}

	if ( $neutral ) {
		$venue_info['status'] = _x( 'N', 'Neutral ground', 'wp-club-manager' );
	} elseif ( $club == $home_club ) {
			$venue_info['status'] = _x( 'H', 'Home ground', 'wp-club-manager' );
	} else {
		$venue_info['status'] = _x( 'A', 'Away ground', 'wp-club-manager' );
	}

	return $venue_info;
}

/**
 * Get match player stats.
 *
 * @access public
 * @param string $post_id
 * @return mixed $players
 */
if ( ! function_exists( 'get_wpcm_match_player_stats' ) ) {
	/**
	 * @param int|null $post_id
	 *
	 * @return mixed
	 */
	function get_wpcm_match_player_stats( $post_id = null ) {

		if ( ! $post_id ) {
			global $post_id;
		}

		$players = unserialize( get_post_meta( $post_id, 'wpcm_players', true ) );
		$output  = array();

		if ( is_array( $players ) ) :

			foreach ( $players as $id => $stats ) :

				if ( $stats['checked'] ) {

					$output[ $key ] = $stats;
				}
			endforeach;
		endif;

		return $players;
	}
}
