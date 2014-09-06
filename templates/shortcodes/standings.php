<?php
/**
 * Standings
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$default = array(
	'id' => get_the_ID(),
	'limit' => 7,
	'comp' => null,
	'season' => null,
	'orderby' => 'pts',
	'order' => 'DESC',
	'linktext' => __( 'View all standings', 'wpclubmanager' ),
	'linkpage' => null,
	'stats' => 'p,w,d,l,otl,pct,f,a,gd,b,pts',
	'title' => __( 'Standings', 'wpclubmanager' ),
	'thumb' => 1,
	'linkclub' => 0,
);

extract( $default, EXTR_SKIP );

$wpcm_standings_stats_labels = array(
	'p' => get_option( 'wpcm_standings_p_label' ),
	'w' => get_option( 'wpcm_standings_w_label' ),
	'd' => get_option( 'wpcm_standings_d_label' ),
	'l' => get_option( 'wpcm_standings_l_label' ),
	'otl' => get_option( 'wpcm_standings_otl_label' ),
	'pct' => get_option( 'wpcm_standings_pct_label' ),
	'f' => get_option( 'wpcm_standings_f_label' ),
	'a' => get_option( 'wpcm_standings_a_label' ),
	'gd' => get_option( 'wpcm_standings_gd_label' ),
	'b' => get_option( 'wpcm_standings_bonus_label' ),
	'pts' => get_option( 'wpcm_standings_pts_label' )
);
// convert atts to something more useful
$stats = explode( ',', $stats );
foreach( $stats as $key => $value ) {
	$stats[$key] = strtolower( trim( $value ) );
	if ( !array_key_exists( $stats[$key], $wpcm_standings_stats_labels ) )
		unset( $stats[$key] );
}
if ( $limit == 0 )
	$limit = -1;
if ( $comp <= 0 )
	$comp = null;
if ( $season <= 0 )
	$season = null;
$comp_slug = wpcm_get_term_slug( $comp, 'wpcm_comp' );
$season_slug = wpcm_get_term_slug( $season, 'wpcm_season' );
$club = get_option( 'wpcm_default_club' );
$center = $club;
$orderby = strtolower( $orderby );	
$order = strtoupper( $order );
if ( $linkpage <= 0 )
	$linkpage = null;
// get all clubs from comp and season
$args = array(
	'post_type' => 'wpcm_club',
	'tax_query' => array(),
	'numberposts' => -1,
	'posts_per_page' => -1
);
if ( $comp ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'wpcm_comp',
		'terms' => $comp,
		'field' => 'term_id'
	);
}
if ( $season ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'wpcm_season',
		'terms' => $season,
		'field' => 'term_id'
	);
}
$clubs = get_posts( $args );
$size = sizeof( $clubs );
if ( $size == 0 )
	return false;
if ( $limit == -1 )
	$limit = $size;
// attach stats to each club
foreach ( $clubs as $club ) {
	$club_stats = get_wpcm_club_total_stats( $club->ID, $comp, $season );		
	$club->wpcm_stats = $club_stats;
	if ( $thumb == 1 ) {
		if ( has_post_thumbnail( $club->ID ) ) {
			$club->thumb = get_the_post_thumbnail( $club->ID, 'crest-small' );
		} else {
			$club->thumb = wpcm_crest_placeholder_img( 'crest-small' );
		}
	} else {
		$club->thumb = '';
	}
}
// sort clubs
if ( $orderby == 'pts' ) {
	usort( $clubs, 'wpcm_club_standings_sort');
} elseif ( $orderby == 'pct' ) {
	usort( $clubs, 'wpcm_club_standings_pct_sort');
} else {
	$clubs = wpcm_club_standings_sort_by( $orderby, $clubs );
}
if ( $order == 'ASC' ) {
	$clubs = array_reverse( $clubs );
}
// add places to clubs
foreach ( $clubs as $key => $value ) {	
	$value->place = $key + 1;
}
// define center if null
if ( !isset( $center ) )
	$center = $clubs[0]->ID;
// if limit is smaller than table size, find range to display
if ( $limit < $size ) {
	// find middle
	$middle = 0;
	foreach( $clubs as $key => $value ) {
		if ( $value->ID == $center ) $middle = $key;
	}
	// find range to display
	$before = floor( ( $limit - 1 ) / 2 );
	$first = $middle - $before;
	$actual = $size - $first;
	if ( $actual < $limit ) {
		$first -= ( $limit - $actual );
	}
	if ( $first < 0 ) {
		$first = 0;
	}
} else {
	$first = 0;
	$limit = $size;
}

// slice array
$clubs = array_slice( $clubs, $first, $limit );
// initialize output
$output = '';
// table head
if( $title ) {
	$title = '<h3 class="wpcm-sc-title">' . $title . '</h3>';
} else{
	$title = '';
}
$output .=
'<div class="wpcm-standings-shortcode wpcm-standings">
	' . $title . '
	<table>
		<thead>
			<tr>
				<th></th>
				<th></th>';
	foreach( $stats as $stat ) {
		$output .= '<th class="' . $stat . '">' . $wpcm_standings_stats_labels[$stat] . '</th>';
	}
	$output .=
			'</tr>
		</thead>
	<tbody>';
	// insert rows
	$rownum = 0;
	foreach ( $clubs as $club ) {
		$rownum ++;
		$club_stats = $club->wpcm_stats;
		$output .= '<tr class="' . ( $center == $club->ID ? 'highlighted ' : '' ) . ( $rownum % 2 == 0 ? 'even' : 'odd' ) . ( $rownum == $limit ? ' last' : '' ) . '">';

		$output .= '<td class="pos">' . $club->place . '</td>';

		$output .= '<td class="club">' . $club->thumb;

		if( $linkclub ) {
			$output .= '<a href="' . get_the_permalink( $club->ID ) . '">';
		}

		$output .= ' ' . $club->post_title;

		if( $linkclub ) {
			$output .= '</a>';
		}

		$output .= '</td>';

		foreach( $stats as $stat ) {
			$output .= '<td class="' . $stat . '">' . $club_stats[$stat] . '</td>';
		}

		$output .= '</tr>';
	}
	$output.=
	'</tbody>
	</table>';
$output .= '</div>';

if ( isset( $linkpage ) )
	$output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

echo $output;