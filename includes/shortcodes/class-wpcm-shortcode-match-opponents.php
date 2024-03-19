<?php
/**
 * Match Opponents Shortcode
 *
 * @author      Clubpress
 * @category    Shortcodes
 * @package     WPClubManager/Shortcodes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Shortcode_Match_Opponents
 */
class WPCM_Shortcode_Match_Opponents {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract( shortcode_atts( array(), $atts ) ); // phpcs:ignore

		$title      = ( isset( $atts['title'] ) ? $atts['title'] : '' );
		$format     = ( isset( $atts['format'] ) ? $atts['format'] : '' );
		$id         = ( isset( $atts['id'] ) ? $atts['id'] : '' );
		$limit      = ( isset( $atts['limit'] ) ? $atts['limit'] : '' );
		$comp       = ( isset( $atts['comp'] ) ? $atts['comp'] : '' );
		$season     = ( isset( $atts['season'] ) ? $atts['season'] : '' );
		$team       = ( isset( $atts['team'] ) ? $atts['team'] : '' );
		$date_range = ( isset( $atts['date_range'] ) ? $atts['date_range'] : '' );
		$venue      = ( isset( $atts['venue'] ) ? $atts['venue'] : '' );
		$order      = ( isset( $atts['order'] ) ? $atts['order'] : '' );
		$show_abbr  = ( isset( $atts['show_abbr'] ) ? $atts['show_abbr'] : 0 );
		$show_thumb = ( isset( $atts['show_thumb'] ) ? $atts['show_thumb'] : 0 );
		$show_team  = ( isset( $atts['show_team'] ) ? $atts['show_team'] : 0 );
		$show_comp  = ( isset( $atts['show_comp'] ) ? $atts['show_comp'] : 1 );
		$show_venue = ( isset( $atts['show_venue'] ) ? $atts['show_venue'] : 1 );
		$linktext   = ( isset( $atts['linktext'] ) ? $atts['linktext'] : '' );
		$linkpage   = ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : '' );

		if ( '' === $limit ) {
			$limit = -1;
		}
		if ( '' === $comp ) {
			$comp = null;
		}
		if ( '' === $season ) {
			$season = null;
		}
		if ( '' === $team ) {
			$team = null;
		}
		if ( '' === $venue ) {
			$venue = null;
		}
		if ( '' === $date_range ) {
			$date_range = null;
		}
		if ( '' === $order ) {
			$order = 'ASC';
		}
		if ( '' === $show_abbr ) {
			$show_abbr = 0;
		}
		if ( '' === $show_thumb ) {
			$show_thumb = 0;
		}
		if ( '' === $show_team ) {
			$show_team = 0;
		}
		if ( '' === $show_comp ) {
			$show_comp = 1;
		}
		if ( '' === $show_venue ) {
			$show_venue = 0;
		}
		if ( '' === $linkpage ) {
			$linkpage = null;
		}

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if ( 'no' === $disable_cache || 'last_week' === $date_range || 'next_week' === $date_range ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'match_opponents' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			if ( is_club_mode() ) {
				$club = get_default_club();
			} else {
				$club = $id;
			}
			if ( '' === $format ) {
				$format = array( 'publish', 'future' );
			} elseif ( 'fixtures' === $format ) {
				$format = 'future';
			} elseif ( 'results' === $format ) {
				$format = 'publish';
			}

			// get matches
			$query_args = array(
				'tax_query'      => array(), // phpcs:ignore
				'numberposts'    => $limit,
				'order'          => $order,
				'orderby'        => 'post_date',
				'post_type'      => 'wpcm_match',
				'post_status'    => $format,
				'posts_per_page' => $limit,
			);

			if ( 'results' === $format ) {
				$query_args['meta_query'] = array( // phpcs:ignore
					array(
						'key'   => 'wpcm_played',
						'value' => false,
					),
				);
			}

			if ( isset( $venue ) && 'home' === $venue ) {
				$query_args['meta_query'] = array( // phpcs:ignore
					array(
						'key'   => 'wpcm_home_club',
						'value' => $club,
					),
				);
			} elseif ( isset( $venue ) && 'away' === $venue ) {
				$query_args['meta_query'] = array( // phpcs:ignore
					array(
						'key'   => 'wpcm_away_club',
						'value' => $club,
					),
				);
			} else {
				$query_args['meta_query'] = array( // phpcs:ignore
					'relation' => 'OR',
					array(
						'key'   => 'wpcm_home_club',
						'value' => $club,
					),
					array(
						'key'   => 'wpcm_away_club',
						'value' => $club,
					),
				);
			}

			if ( isset( $comp ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_comp',
					'terms'    => $comp,
					'field'    => 'term_id',
				);
			}
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
			if ( isset( $date_range ) ) {
				if ( 'last_week' === $date_range ) {
					$today                    = getdate();
					$query_args['date_query'] = array(
						'column' => 'post_date',
						'before' => array(
							'year'  => $today['year'],
							'month' => $today['mon'],
							'day'   => $today['mday'],
						),
						'after'  => '- 7 days',
					);
				} elseif ( 'next_week' === $date_range ) {
					$today                    = getdate();
					$query_args['date_query'] = array(
						'column' => 'post_date',
						'after'  => array(
							'year'  => $today['year'],
							'month' => $today['mon'],
							'day'   => $today['mday'],
						),
						'before' => '+ 7 days',
					);
				} else {
					$query_args['date_query'] = array(
						'month' => $date_range,
					);
				}
			}

			$matches = get_posts( $query_args );

			if ( $matches ) {
				ob_start();
				wpclubmanager_get_template( 'shortcodes/match-opponents.php', array(
					'title'      => $title,
					'club'       => $club,
					'show_abbr'  => $show_abbr,
					'show_thumb' => $show_thumb,
					'show_team'  => $show_team,
					'show_comp'  => $show_comp,
					'show_venue' => $show_venue,
					'matches'    => $matches,
					'linkpage'   => $linkpage,
					'linktext'   => $linktext,
				) );
				$output = ob_get_clean();

				wp_reset_postdata();
				if ( 'no' === $disable_cache || 'last_week' === $date_range || 'next_week' === $date_range ) {
					set_transient( $transient_name, $output, 4 * WEEK_IN_SECONDS );
					do_action( 'update_plugin_transient_keys', $transient_name );
				}
			}
		}

		echo $output; // phpcs:ignore
	}
}
