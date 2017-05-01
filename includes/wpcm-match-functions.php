<?php
/**
 * WPClubManager Match Functions.
 *
 * Functions for matches.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.5.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// generate title
function match_title( $title, $id = null ) {
	if ( get_post_type( $id ) == 'wpcm_match' ) {
		
		$default_club = get_default_club();
		$title_format = get_match_title_format();
		$separator = get_option('wpcm_match_clubs_separator');
		$home_id = (int)get_post_meta( $id, 'wpcm_home_club', true );
		$away_id = (int)get_post_meta( $id, 'wpcm_away_club', true );
		$home_club = get_post( $home_id );
		$away_club = get_post( $away_id );
		if( $title_format == '%home% vs %away%') {
			$side1 = $home_club->post_title;
			$side2 = $away_club->post_title;
		}else{
			$side1 = $away_club->post_title;
			$side2 = $home_club->post_title;
		}
		
		$title = $side1 . ' ' . $separator . ' ' . $side2;
	}
	return $title;
}
add_filter( 'the_title', 'match_title', 10, 2 );

// // generate title
function match_wp_title( $title ) {
	if ( get_post_type( ) == 'wpcm_match' ) {

		$id = get_the_ID();
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

    $order = explode(',', $_POST['order']);
    $counter = 0;
    foreach ($order as $item_id) {
        $wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
        $counter++;
    }
    die(1);
}
add_action('wp_ajax_item_sort', 'wpcm_match_players_item_order');
add_action('wp_ajax_nopriv_item_sort', 'wpcm_match_players_item_order');

/**
 * Get match outcome - win, loss or draw.
 *
 * @access public
 * @param int $post
 * @return string $outcome
 * @since 1.4.0
 */
function wpcm_get_match_outcome( $post ) {

	$club = get_default_club();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	if( get_option('wpcm_sport' ) !== 'cricket' ) {
		if( get_post_meta( $post, 'wpcm_shootout', true ) ) {
			$home_goals = get_post_meta( $post, '_wpcm_home_shootout_goals', true );
			$away_goals = get_post_meta( $post, '_wpcm_away_shootout_goals', true );
		} else {
			$home_goals = get_post_meta( $post, 'wpcm_home_goals', true );
			$away_goals = get_post_meta( $post, 'wpcm_away_goals', true );
		}
	} else {
		$runs = unserialize( get_post_meta( $post, '_wpcm_match_runs', true ) );
		$extras = unserialize( get_post_meta( $post, '_wpcm_match_extras', true ) );
		$home_goals = $runs['home'] + $extras['home'];
		$away_goals = $runs['away'] + $extras['away'];
	}

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

	return $outcome;
}

/**
 * Get match result.
 *
 * @access public
 * @param int $post
 * @return string $result
 * @since 1.4.6
 */
function wpcm_get_match_result( $post ) {

	$sport = get_option( 'wpcm_sport' );
	$format = get_match_title_format();
	$hide = get_option( 'wpcm_hide_scores');
	$delimiter = get_option( 'wpcm_match_goals_delimiter' );
	$played = get_post_meta( $post, 'wpcm_played', true );
	$home_goals = get_post_meta( $post, 'wpcm_home_goals', true );
	$away_goals = get_post_meta( $post, 'wpcm_away_goals', true );
	if( $sport == 'gaelic' ) {
		$home_gaa_goals = get_post_meta( $post, 'wpcm_home_gaa_goals', true );
		$home_gaa_points = get_post_meta( $post, 'wpcm_home_gaa_points', true );
		$away_gaa_goals = get_post_meta( $post, 'wpcm_away_gaa_goals', true );
		$away_gaa_points = get_post_meta( $post, 'wpcm_away_gaa_points', true );
	}
	if( $sport == 'cricket' ) {
		$runs = unserialize( get_post_meta( $post, '_wpcm_match_runs', true ) );
		$extras = unserialize( get_post_meta( $post, '_wpcm_match_extras', true ) );
		$wickets = unserialize( get_post_meta( $post, '_wpcm_match_wickets', true ) );
	}

	if( $hide == 'yes' && ! is_user_logged_in() ) {
		$result = ( $played ? __( 'x', 'wp-club-manager' ) . ' ' . $delimiter . ' ' . __( 'x', 'wp-club-manager' ) : '' );
		$side1 = __( 'x', 'wp-club-manager' );
		$side2 = __( 'x', 'wp-club-manager' );
	} else {
		if( $format == '%home% vs %away%' ) {
			if( $sport == 'gaelic' ) {
				$result = ( $played ? $home_gaa_goals . '-' . $home_gaa_points . ' ' . $delimiter . ' ' . $away_gaa_goals . '-' . $away_gaa_points : '' );
				$side1 = ( $played ? $home_gaa_goals . '-' . $home_gaa_points : '-' );
				$side2 = ( $played ? $away_gaa_goals . '-' . $away_gaa_points : '-' );

			} elseif( $sport == 'cricket' ) {

				$home_score = $runs['home'] + $extras['home'];
				$away_score = $runs['away'] + $extras['away'];
				$home_wickets = ( $wickets['home'] == '10' ? '' : '/' . $wickets['home'] );
				$away_wickets = ( $wickets['away'] == '10' ? '' : '/' . $wickets['away'] );

				$result = ( $played ? $home_score . $home_wickets . ' ' . $delimiter . ' ' . $away_score . $away_wickets : '' );
				$side1 = ( $played ? $home_score . $home_wickets : '-' );
				$side2 = ( $played ? $away_score . $away_wickets : '-' );

			} else {

				$result = ( $played ? $home_goals . ' ' . $delimiter . ' ' . $away_goals : '' );
				$side1 = ( $played ? $home_goals : '' );
				$side2 = ( $played ? $away_goals : '' );

			}
		} else {
			if( $sport == 'gaelic' ) {

				$result = ( $played ? $away_gaa_goals . '-' . $away_gaa_points . ' ' . $delimiter . ' ' . $home_gaa_goals . '-' . $home_gaa_points : '' );
				$side1 = ( $played ? $away_gaa_goals . '-' . $away_gaa_points : '-' );
				$side2 = ( $played ? $home_gaa_goals . '-' . $home_gaa_points : '-' );

			} elseif( $sport == 'cricket' ) {

				$home_score = $runs['home'] + $extras['home'];
				$away_score = $runs['away'] + $extras['away'];
				$home_wickets = ( $wickets['home'] == '10' ? '' : '/' . $wickets['home'] );
				$away_wickets = ( $wickets['away'] == '10' ? '' : '/' . $wickets['away'] );

				$result = ( $played ? $away_score . $away_wickets . ' ' . $delimiter . ' ' . $home_score . $home_wickets : '' );
				$side1 = ( $played ? $away_score . $away_wickets : '-' );
				$side2 = ( $played ? $home_score . $home_wickets : '-' );

			} else {

				$result = ( $played ? $away_goals . ' ' . $delimiter . ' ' . $home_goals : '' );
				$side1 = ( $played ? $away_goals : '' );
				$side2 = ( $played ? $home_goals : '' );
			}
		}
	}

	//return $result;

	return array( $result, $side1, $side2, $delimiter );

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
	$status = get_post_meta( $post, 'wpcm_comp_status', true );

	if ( is_array( $competitions ) ) {
		foreach ( $competitions as $competition ):
			$comp = $competition->name;
			$competition = reset($competitions);
			$t_id = $competition->term_id;
			$competition_meta = get_option( "taxonomy_term_$t_id" );
			$comp_label = $competition_meta['wpcm_comp_label'];
			if ( $comp_label ) {
				$label = $comp_label;
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
 * @param int $post
 * @return array
 * @since 1.4.0
 */
function wpcm_get_match_team( $post ) {

	$teams = get_the_terms( $post, 'wpcm_team' );

	if ( is_array( $teams ) ) {
		foreach ( $teams as $team ) {
			$name = $team->name;
			$team = reset($teams);
			$t_id = $team->term_id;
			$team_meta = get_option( "taxonomy_term_$t_id" );
			$team_label = $team_meta['wpcm_team_label'];
			if ( $team_label ) {
				$label = $team_label;
			} else {
				$label = $name;
			}
		}
	} else {
		$name = '';
		$label = '';
	}

	return array( $name, $label );
}

/**
 * Get match team names.
 *
 * @access public
 * @param int $post
 * @return array $side1 $side2
 * @since 1.4.0
 */
function wpcm_get_match_clubs( $post ) {

	$format = get_match_title_format();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );

	if( $format == '%home% vs %away%' ) {
		$side1 = wpcm_get_team_name( $home_club, $post );
		$side2 = wpcm_get_team_name( $away_club, $post );
	} else {
		$side1 = wpcm_get_team_name( $away_club, $post );
		$side2 = wpcm_get_team_name( $home_club, $post );
	}

	return array( $side1, $side2 );
}

/**
 * Get match club badges.
 *
 * @access public
 * @param int $post
 * @return array $home_badge $away_badge
 * @since 1.4.0
 */
function wpcm_get_match_badges( $post, $size = null, $args = null ) {

	$format = get_match_title_format();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );

	if( $format == '%home% vs %away%' ) {
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
	}else{
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

	$club = get_default_club();
	$venues = get_the_terms( $post, 'wpcm_venue' );
	$neutral = get_post_meta( $post, 'wpcm_neutral', true );
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );

	if ( is_array( $venues ) ) {
		$venue = reset($venues);
		$name = $venue->name;
		$t_id = $venue->term_id;
		$venue_meta = get_option( "taxonomy_term_$t_id" );
		$address = $venue_meta['wpcm_address'];
	} else {
		$name = null;
		$address = null;
	}

	if ( $neutral ) {
		$status = __('N', 'wp-club-manager');
	} else {
		if ( $club == $home_club ) {
			$status = __('H', 'wp-club-manager');
		} else {
			$status = __('A', 'wp-club-manager');
		}
	}

	return array( $name, $status, $address );
}

/**
 * Get match opponents.
 *
 * @access public
 * @param int $post
 * @param bool $link_club
 * @return string $opponent
 * @since 1.4.0
 */
function wpcm_get_match_opponents( $post, $link_club = true ) {

	$club = get_default_club();
	$home_club = get_post_meta( $post, 'wpcm_home_club', true );
	$away_club = get_post_meta( $post, 'wpcm_away_club', true );

	if ( $club == $home_club ) {
		$opponent = ( $link_club ? '<a href="' . get_post_permalink( $away_club, false, true ) . '">' : '' ) . '' . get_the_title( $away_club, true ) . '' . ( $link_club ? '</a>' : '');
	} elseif ( $club == $away_club ) {
		$opponent = ( $link_club ? '<a href="' . get_post_permalink( $home_club, false, true ) . '">' : '' ) . '' . get_the_title( $home_club, true ) . '' . ( $link_club ? '</a>' : '');
	}

	return $opponent;
}

/**
 * Get match player stats.
 *
 * @access public
 * @param string $post_id
 * @return mixed $players
 */
if (!function_exists('get_wpcm_match_player_stats')) {
	function get_wpcm_match_player_stats( $post_id = null ) {

		if ( !$post_id ) global $post_id;

		$players = unserialize( get_post_meta( $post_id, 'wpcm_players', true ) );
		$output = array();

		if( is_array( $players ) ):

			foreach( $players as $id => $stats ):

				if ( $stats['checked'] )

					$output[$key] = $stats;
			endforeach;
		endif;

		return $players;
	}
}