<?php
/**
 * Staff
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$default = array(
	'id' => get_the_ID(),
	'id' => null,
	'limit' => -1,
	'season' => null,
	'team' => null,
	'jobs' => null,
	'title' => __( 'Staff', 'wpclubmanager' ),
	'linktext' => __( 'View all staff', 'wpclubmanager' ),
	'linkpage' => null
);

extract( $default, EXTR_SKIP );

if ( $limit == 0 )
	$limit = -1;
if ( $id <= 0 )
	$id = null;
if ( $team <= 0 )
	$team = null;

global $post;

$show_dob = get_option( 'wpcm_staff_profile_show_dob' );
$show_age = get_option( 'wpcm_staff_profile_show_age' );
$show_season = get_option( 'wpcm_staff_profile_show_season' );
$show_team = get_option( 'wpcm_staff_profile_show_team' );
$show_natl = get_option( 'wpcm_staff_profile_show_nationality' );
$show_jobs = get_option( 'wpcm_staff_profile_show_jobs' );

$output = '';
if ( $id ) {
	$post = get_post( $id );
	$posts = array();
	$posts[] = $post;
} else {
	$args = array(
		'post_type' => 'wpcm_staff',
		'tax_query' => array(),
		'numposts' => $limit,
		'posts_per_page' => $limit
	);
	if ( $season ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'wpcm_season',
			'terms' => $season,
			'field' => 'term_id'
		);
	}
	if ( $team ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'wpcm_team',
			'terms' => $team,
			'field' => 'term_id'
		);
	}
	if ( $jobs ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'wpcm_jobs',
			'terms' => $jobs,
			'field' => 'term_id'
		);
	}
	$posts = query_posts($args);
}
$count = 0;
$size = sizeof($posts);

if ($size > 0):

	while ( have_posts() ) : the_post();

		$output .= '<div class="wpcm-staff-shortcode row">';

		if (get_the_post_thumbnail( $post->ID, 'staff_single' ) != null) {

			$output .= '<div class="wpcm-staff-image">'.get_the_post_thumbnail( $post->ID, 'staff_single', array('title' => get_the_title()) ).'</div>';
		} else {

			$output .= '<div class="wpcm-staff-image">'.apply_filters( 'wpclubmanager_single_product_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID ).'</div>';
		}

		$profile_details = array();

		// job title
		if ( $show_jobs == 'yes' ) {

			$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );

			if ( is_array( $jobs ) ) {

				$staff_jobs = array();

				foreach ( $jobs as $value ) {

					$staff_jobs[] = $value->name;
				}

				$profile_details[ __('Job Title', 'wpclubmanager') ] = implode( ', ', $staff_jobs );
			}
		}

		// birthday
		if ( $show_dob == 'yes' )
			$profile_details[ __( 'Birthday', 'wpclubmanager' ) ] = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) );
		
		// age
		if ( $show_age == 'yes' )
			$profile_details[__('Age', 'wpclubmanager')] = get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) );
		
		// season
		if ( $show_season == 'yes' ) {
			$seasons = get_the_terms( $post->ID, 'wpcm_season' );
			if ( is_array( $seasons ) ) {
				$player_seasons = array();
				foreach ( $seasons as $value ) {
					$player_seasons[] = $value->name;
				}
				$profile_details[ __('Season', 'wpclubmanager') ] = implode( ', ', $player_seasons );
			}
		}
		
		// team
		if ( $show_team == 'yes' ) {
			$teams = get_the_terms( $post->ID, 'wpcm_team' );
			if ( is_array( $teams ) ) {
				$player_teams = array();
				foreach ( $teams as $team ) {
					$player_teams[] = $team->name;
				}
				$profile_details[ __('Team', 'wpclubmanager') ] = implode( ', ', $player_teams );
			}
		}

		// nationality
		if ( $show_natl == 'yes' ) {
			$natl = get_post_meta( $post->ID, 'wpcm_natl', true );
			$profile_details[ __( 'Nationality', 'wpclubmanager' ) ] = '<img class="flag" src="'. WPCM_URL .'assets/images/flags/' . $natl . '.png" />';
		}

		$output .= '<div class="wpcm-staff-info">
			<h1 class="entry-title">'.get_the_title($post->ID).'</h1>
			<div class="wpcm-staff-meta">';
			$count = 0;
			$size = sizeof( $profile_details );
			if ( $size > 0 ) {
				$output .= 
				'<table>' .
					'<tbody>';
				foreach ( $profile_details as $key => $value ) {
					$count++;
					$output .=
					'<tr>' .
						'<th>'.$key.'</th>' .
						'<td>'.$value.'</td>' .
					'</tr>';
				}
				$output .=
					'</tbody>' .
				'</table>';
			}
			$output .= '</div></div><div class="wpcm-staff-bio">' . apply_filters('the_content', get_the_content($post->ID)).'</div>';

			if ( isset( $linkpage ) && $linkpage ) $output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

		$output .= '</div>';

	endwhile;

	wp_reset_postdata();

endif;

wp_reset_query();

echo $output;