<?php
/**
 * Staff shortcode template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-staff.php
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$default = array(
	'id' => null,
	'limit' => -1,
	'season' => null,
	'team' => null,
	'jobs' => null,
	'orderby' => 'name',
	'order' => 'ASC',
	'title' => __( 'Staff', 'wpclubmanager' ),
	'linktext' => __( 'View all staff', 'wpclubmanager' ),
	'linkpage' => null
);

extract( $default, EXTR_SKIP );

$stats_labels = array(
	'flag' => '&nbsp;',
	'number' => '&nbsp;',
	'name' => __( 'Name', 'wpclubmanager' ),
	'thumb' => '&nbsp',
	'job' => __( 'Job', 'wpclubmanager' ),
	'age' => __( 'Age', 'wpclubmanager' ),
	'team' => __( 'Team', 'wpclubmanager' ),
	'season' => __( 'Season', 'wpclubmanager' ),
	'joined' => __( 'Joined', 'wpclubmanager' )
);


if ( $limit == 0 )
	$limit = -1;
if ( $team <= 0 )
	$team = null;
// if ( $jobs <= 0 )
// 	$jobs = null;

$stats = explode( ',', $stats );

foreach( $stats as $key => $value ) {
	$stats[$key] = strtolower( trim( $value ) );
	if ( !array_key_exists( $stats[$key], $stats_labels ) )
		unset( $stats[$key] );
}

$orderby = strtolower( $orderby );	
$order = strtoupper( $order );
$output = '';

$query_args = array(
	'post_type' => 'wpcm_staff',
	'tax_query' => array(),
	'numposts' => $limit,
	'posts_per_page' => $limit,
	'orderby' => $orderby,
	'order' => $order
);

if ( $season ) {
	$query_args['tax_query'][] = array(
		'taxonomy' => 'wpcm_season',
		'terms' => $season,
		'field' => 'term_id'
	);
}

if ( $team ) {
	$query_args['tax_query'][] = array(
		'taxonomy' => 'wpcm_team',
		'terms' => $team,
		'field' => 'term_id'
	);
}

if ( $jobs ) {
	$query_args['tax_query'][] = array(
		'taxonomy' => 'wpcm_jobs',
		'terms' => $jobs,
		'field' => 'term_id'
	);
}

$staff = get_posts( $query_args );

$count = 0;	

if ( sizeof( $staff ) > 0 ) {

	if( $title ) {
		$title = '<h3 class="wpcm-sc-title">' . $title . '</h3>';
	} else{
		$title = '';
	}

	$output .= '<div class="wpcm-players-shortcode">
		' . $title . '
		<table>
			<thead>
				<tr>';
				foreach( $stats as $stat ) {
					$output .= '<th class="'. $stat . '">' . $stats_labels[$stat] .'</th>';
				}
				$output .= '</tr>
			</thead>
			<tbody>';

	$player_details = array();

	foreach( $staff as $player ) {

		$player_details[$player->ID] = array();
		$count++;

		
			$name = $player->post_title;
			$jobs = get_the_terms( $player->ID, 'wpcm_jobs' );

		if ( has_post_thumbnail( $player->ID ) ) {
			$thumb = get_the_post_thumbnail( $player->ID, 'staff_thumbnail' );
		} else {
			$thumb = wpcm_placeholder_img( 'staff_thumbnail' );
		}

		if ( is_array( $jobs ) ) {
			$job = reset($jobs);
			$job = $job->name;

		} else {

			$job = __( 'None', 'wpclubmanager' );

		}

		$natl = get_post_meta( $player->ID, 'wpcm_natl', true );

		foreach( $stats as $stat ) {

			switch ( $stat ) {
				case 'thumb':
					$player_details[$player->ID][$stat] = '';
					$player_details[$player->ID][$stat] .= '<a href="' . get_permalink( $player->ID ) . '">' . $thumb . '</a>';
					break;
				case 'flag':
					$player_details[$player->ID][$stat] = '';
					$player_details[$player->ID][$stat] .= '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . $natl . '.png" />';
					break;
				case 'name':
					$player_details[$player->ID][$stat] = '';
					$player_details[$player->ID][$stat] .= '<a href="' . get_permalink( $player->ID ) . '">' . $name . '</a>';
					break;
				case 'job':
					$player_details[$player->ID][$stat] = '';
					$jobs = get_the_terms( $player->ID, 'wpcm_jobs' );
					if ( is_array( $jobs ) ) {
						$player_jobs = array();
						foreach ( $jobs as $job ) {
							$player_jobs[] = $job->name;
						}
						$player_details[$player->ID][$stat] .= implode( ', ', $player_jobs );
					}
					break;
				case 'team':
					$player_details[$player->ID][$stat] = '';
					$teams = get_the_terms( $player->ID, 'wpcm_team' );
					if ( is_array( $teams ) ) {
						$player_teams = array();
						foreach ( $teams as $team ) {
							$player_teams[] = $team->name;
						}
						$player_details[$player->ID][$stat] .= implode( ', ', $player_teams );
					}
					break;
				case 'season':
					$player_details[$player->ID][$stat] = '';
					$seasons = get_the_terms( $player->ID, 'wpcm_season' );
					if ( is_array( $seasons ) ) {
						$player_seasons = array();
						foreach ( $seasons as $season ) {
							$player_seasons[] = $season->name;
						}
						$player_details[$player->ID][$stat] .= implode( ', ', $player_seasons );
					}
					break;
				case 'age':
					$player_details[$player->ID][$stat] = '';
					$player_details[$player->ID][$stat] .= get_age( get_post_meta( $player->ID, 'wpcm_dob', true ) );
					break;
				case 'joined':
					$player_details[$player->ID][$stat] = '';
					$player_details[$player->ID][$stat] = date_i18n( get_option( 'date_format' ), strtotime( $player->post_date ) );
					break;
			}
		}
	}
	// if ( array_key_exists( $orderby, $player_stats_labels ) ) {
	// 	$player_details = subval_sort( $player_details, $orderby );
	// 	if ( $order == 'DESC' )
	// 		$player_details = array_reverse( $player_details );
	// }
	$count = 0;
	foreach( $player_details as $player_detail ) {
		$count++;
		if ( $limit > 0 && $count > $limit )
			break;

		$output .=
		'<tr>';
		foreach( $stats as $stat ) {
			$output .= '<td class="'. $stat . '">';
			$output .= $player_detail[$stat];
			$output .= '</td>';
		}
		$output .= '</tr>';
	}
	$output .= '</tbody></table>';
	
	$output .= '</div>';

	if ( isset( $linkpage ) && $linkpage ) $output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

	wp_reset_postdata();

} else {

	$output = '';
}

echo $output;