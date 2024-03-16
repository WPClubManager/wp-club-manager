<?php
/**
 * WPClubManager Club Functions.
 *
 * Functions for clubs.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get club head to heads.
 *
 * @access public
 * @param int $post_id
 * @return array $matches
 * @since 2.0.5
 */
function wpcm_head_to_heads( $post_id ) {

	$club = get_default_club();

	$args = array(
		'post_type'      => 'wpcm_match',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'relation' => 'OR',
				array(
					'key'   => 'wpcm_home_club',
					'value' => $post_id,
				),
				array(
					'key'   => 'wpcm_away_club',
					'value' => $post_id,
				),
			),
			array(
				'relation' => 'OR',
				array(
					'key'   => 'wpcm_home_club',
					'value' => $club,
				),
				array(
					'key'   => 'wpcm_away_club',
					'value' => $club,
				),
			),
		),
	);

	$matches = get_posts( $args );

	return $matches;
}

/**
 * Get club head to head stats.
 *
 * @access public
 * @param array $matches
 * @return mixed
 * @since 2.0.0
 */
function wpcm_head_to_head_count( $matches ) {

	$club   = get_default_club();
	$wins   = 0;
	$losses = 0;
	$draws  = 0;
	$count  = 0;
	foreach ( $matches as $match ) {

		if ( get_post_meta( $match->ID, '_wpcm_postponed', true ) != '1' && get_post_meta( $match->ID, '_wpcm_walkover', true ) == '' ) {

			++$count;
			$home_club  = get_post_meta( $match->ID, 'wpcm_home_club', true );
			$home_goals = get_post_meta( $match->ID, 'wpcm_home_goals', true );
			$away_goals = get_post_meta( $match->ID, 'wpcm_away_goals', true );

			if ( $home_goals == $away_goals ) {
				++$draws;
			}

			if ( $club == $home_club ) {
				if ( $home_goals > $away_goals ) {
					++$wins;
				}
				if ( $home_goals < $away_goals ) {
					++$losses;
				}
			} else {
				if ( $home_goals > $away_goals ) {
					++$losses;
				}
				if ( $home_goals < $away_goals ) {
					++$wins;
				}
			}
		}
	}
	$outcome           = array();
	$outcome['total']  = $count;
	$outcome['wins']   = $wins;
	$outcome['draws']  = $draws;
	$outcome['losses'] = $losses;

	return apply_filters( 'wpcm_head_to_head_count', $outcome, $matches );
}

/**
 * Get club venues.
 *
 * @access public
 * @param array $post
 * @return array $venue_info
 * @since 2.1.5
 */
function get_club_venue( $post ) {

	$venues = get_the_terms( $post, 'wpcm_venue' );

	if ( is_array( $venues ) ) {
		$venue                     = reset( $venues );
		$venue_info['name']        = $venue->name;
		$venue_info['id']          = $venue->term_id;
		$venue_info['description'] = $venue->description;
		$venue_meta                = get_option( 'taxonomy_term_' . $venue_info['id'] . '' );
		$venue_info['address']     = ( isset( $venue_meta['wpcm_address'] ) ? $venue_meta['wpcm_address'] : false );
		$venue_info['capacity']    = ( isset( $venue_meta['wpcm_capacity'] ) ? $venue_meta['wpcm_capacity'] : false );
	} else {
		$venue_info = false;
	}
	return $venue_info;
}

/**
 * Get club details.
 *
 * @access public
 *
 * @param array  $post
 * @param string $size
 *
 * @return array $details
 * @since  2.1.0
 */
function get_club_details( $post, $size = 'crest-small' ) {

	$details['abbr']            = get_post_meta( $post->ID, '_wpcm_club_abbr', true );
	$details['formed']          = get_post_meta( $post->ID, '_wpcm_club_formed', true );
	$details['primary_color']   = get_post_meta( $post->ID, '_wpcm_club_primary_color', true );
	$details['secondary_color'] = get_post_meta( $post->ID, '_wpcm_club_secondary_color', true );
	$details['website']         = get_post_meta( $post->ID, '_wpcm_club_website', true );
	$details['honours']         = get_post_meta( $post->ID, '_wpcm_club_honours', true );
	$details['venue']           = get_club_venue( $post->ID );
	$details['badge']           = get_the_post_thumbnail( $post->ID, $size );

	if ( $post->post_parent > 0 ) {
		if ( '' === $details['abbr'] ) {
			$details['abbr'] = get_post_meta( $post->post_parent, '_wpcm_club_abbr', true );
		}
		if ( '' === $details['formed'] ) {
			$details['formed'] = get_post_meta( $post->post_parent, '_wpcm_club_formed', true );
		}
		if ( '' === $details['primary_color'] ) {
			$details['primary_color'] = get_post_meta( $post->post_parent, '_wpcm_club_primary_color', true );
		}
		if ( '' === $details['secondary_color'] ) {
			$details['secondary_color'] = get_post_meta( $post->post_parent, '_wpcm_club_secondary_color', true );
		}
		if ( '' === $details['website'] ) {
			$details['website'] = get_post_meta( $post->post_parent, '_wpcm_club_website', true );
		}
		if ( '' === $details['honours'] ) {
			$details['honours'] = get_post_meta( $post->post_parent, '_wpcm_club_honours', true );
		}
		if ( ! $details['venue'] ) {
			$details['venue'] = get_club_venue( $post->post_parent );
		}
		if ( '' === $details['badge'] ) {
			$details['badge'] = get_the_post_thumbnail( $post->post_parent, $size );
		}
	}

	return $details;
}

/**
 * Get club abbreviations.
 *
 * @access public
 * @param int $post_id
 * @return string $abbr
 * @since 2.1.0
 */
function get_club_abbreviation( $post_id ) {

	$abbr = get_post_meta( $post_id, '_wpcm_club_abbr', true );

	if ( '' === $abbr ) {
		$title = get_the_title( $post_id, true );
		$title = str_replace( ' ', '', $title );
		$abbr  = substr( $title, 0, 3 );
	}

	return strtoupper( $abbr );
}

/**
 * Return matches for a club.
 *
 * @access public
 * @param int $post_id
 * @return Array
 * @since 2.1.5
 */
function club_matches_list( $post_id ) {

	$args = array(
		'post_type'      => 'wpcm_match',
		'posts_per_page' => -1,
		'post_status'    => array( 'publish', 'future' ),
		'meta_query'     => array(
			array(
				'relation' => 'OR',
				array(
					'key'   => 'wpcm_home_club',
					'value' => $post_id,
				),
				array(
					'key'   => 'wpcm_away_club',
					'value' => $post_id,
				),
			),
		),
	);

	$season              = get_current_season();
	$current_season      = $season['id'];
	$args['tax_query'][] = array(
		'taxonomy' => 'wpcm_season',
		'terms'    => $current_season,
		'field'    => 'term_id',
	);

	$matches = get_posts( $args );

	return $matches;
}
