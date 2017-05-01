<?php
/**
 * WPClubManager Club Functions.
 *
 * Functions for clubs.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wpcm_head_to_heads( $post ) {

	$club = get_default_club();

	// get matches
	$query_args = array(
		'numberposts' => '-1',
		'order' => 'ASC',
		'orderby' => 'post_date',
		'post_type' => 'wpcm_match',
		'post_status' => array('publish'),
		'posts_per_page' => '-1'
	);


	$query_args['meta_query'] = array(
		'relation' => 'OR',
		array(
			'relation' => 'AND',
			array (
				'key' => 'wpcm_home_club',
				'value' => $club,
			),
			array (
				'key' => 'wpcm_away_club',
				'value' => $post,
			),
		),
		array(
			'relation' => 'AND',
			array (
				'key' => 'wpcm_home_club',
				'value' => $post,
			),
			array (
				'key' => 'wpcm_away_club',
				'value' => $club,
			),
		)
	);

	$matches = get_posts( $query_args );

	wp_reset_postdata();

	return $matches;

}

function wpcm_head_to_head_count( $matches ) {

	$club = get_default_club();
	$wins = 0;
	$losses = 0;
	$draws = 0;
	$count = 0;
	foreach( $matches as $match ) {

		$count ++;
		$home_club = get_post_meta( $match->ID, 'wpcm_home_club', true );
		$home_goals = get_post_meta( $match->ID, 'wpcm_home_goals', true );
		$away_goals = get_post_meta( $match->ID, 'wpcm_away_goals', true );

		if ( $home_goals == $away_goals ) {
			$draws ++;
		}

		if ( $club == $home_club ) {
			if ( $home_goals > $away_goals ) {
				$wins ++;
			}
			if ( $home_goals < $away_goals ) {
				$losses ++;
			}
		} else {
			if ( $home_goals > $away_goals ) {
				$losses ++;
			}
			if ( $home_goals < $away_goals ) {
				$wins ++;
			}
		}

	}
	$outcome = array();
	$outcome['total'] = $count;
	$outcome['wins'] = $wins;
	$outcome['draws'] = $draws;
	$outcome['losses'] = $losses;

	return $outcome;

}