<?php
/**
 * Standing Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     1.4.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Standings {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract(shortcode_atts(array(
		), $atts));

		$limit 		= ( isset( $atts['limit'] ) ? $atts['limit'] : 7 );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : __( 'Standings', 'wp-club-manager' ));
		$comp 		= ( isset( $atts['comp'] ) ? $atts['comp'] : null );
		$season 	= ( isset( $atts['season'] ) ? $atts['season'] : null );
		$order 		= ( isset( $atts['order'] ) ? $atts['order'] : 'DESC' );
		$orderby 	= ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'pts' );
		$thumb 		= ( isset( $atts['thumb'] ) ? $atts['thumb'] : 1 );
		$stats 		= ( isset( $atts['stats'] ) ? $atts['stats'] : 'p,w,d,l,f,a,gd,pts' );
		$link_club  = ( isset( $atts['linkclub'] ) ? $atts['linkclub'] : 0 );
		$linktext 	= ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all standings', 'wp-club-manager' ));
		$linkpage 	= ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$type 		= ( isset( $atts['type'] ) ? $atts['type'] : '' );
		$excludes	= ( isset( $atts['excludes'] ) ? $atts['excludes'] : null );

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if( $disable_cache === 'no' && $type !== 'widget' ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'standings' );
			$output = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if( $output === false ) {

			if ( $limit == 0 ) $limit = -1;
			if ( $comp <= 0 ) $comp = null;
			if ( $season <= 0 ) $season = null;
			
			$center = get_default_club();
			$stats_labels = wpcm_get_preset_labels( 'standings', 'label' );
			$stats = explode( ',', $stats );
			
			$args = array(
				'post_type' => 'wpcm_club',
				'tax_query' => array(),
				'numberposts' => -1,
				'posts_per_page' => -1
			);
			if ( $excludes ) {
				$exclude = explode( ',', $excludes);
				$args['post__not_in'] = $exclude;
			}
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

			ob_start();
			wpclubmanager_get_template( 'shortcodes/standings.php', array(
				'type' 			=> $type,
				'title' 		=> $title,
				'limit'			=> $limit,
				'thumb'			=> $thumb,
				'clubs' 		=> $clubs,
				'stats' 		=> $stats,
				'stats_labels' 	=> $stats_labels,
				'center' 		=> $center,
				'type' 			=> $type,
				'link_club' 	=> $link_club,
				'linkpage' 		=> $linkpage,
				'linktext'  	=> $linktext
			) );
			$output = ob_get_clean();

			wp_reset_postdata();
			if( $disable_cache === 'no' && $type !== 'widget') {
				set_transient( $transient_name, $output, 4*WEEK_IN_SECONDS );
				do_action('update_plugin_transient_keys', $transient_name);
			}
		}

		echo $output;
	}
}