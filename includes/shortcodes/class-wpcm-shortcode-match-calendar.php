<?php
/**
 * Match Calendar Shortcode
 *
 * Displays matches in a monthly calendar grid view.
 *
 * @author      Clubpress
 * @category    Shortcodes
 * @package     WPClubManager/Shortcodes
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Shortcode_Match_Calendar
 */
class WPCM_Shortcode_Match_Calendar {

	/**
	 * Output the match calendar shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function output( $atts ) {

		// Allow query string overrides for calendar navigation.
		$get_month = isset( $_GET['month'] ) ? absint( $_GET['month'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$get_year  = isset( $_GET['year'] ) ? absint( $_GET['year'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification

		$month  = $get_month ? $get_month : ( isset( $atts['month'] ) && '' !== $atts['month'] ? absint( $atts['month'] ) : (int) gmdate( 'n' ) );
		$year   = $get_year ? $get_year : ( isset( $atts['year'] ) && '' !== $atts['year'] ? absint( $atts['year'] ) : (int) gmdate( 'Y' ) );
		$comp   = isset( $atts['comp'] ) && '' !== $atts['comp'] ? $atts['comp'] : null;
		$season = isset( $atts['season'] ) && '' !== $atts['season'] ? $atts['season'] : null;
		$team   = isset( $atts['team'] ) && '' !== $atts['team'] ? $atts['team'] : null;

		// Clamp month to valid range.
		if ( $month < 1 || $month > 12 ) {
			$month = (int) gmdate( 'n' );
		}

		$effective_atts = array(
			'month'  => $month,
			'year'   => $year,
			'comp'   => $comp,
			'season' => $season,
			'team'   => $team,
		);
		if ( is_club_mode() ) {
			$effective_atts['club'] = get_default_club();
		}

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if ( 'no' === $disable_cache ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $effective_atts, 'match_calendar' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			$first_day = gmdate( 'Y-m-d', gmmktime( 0, 0, 0, $month, 1, $year ) );
			$last_day  = gmdate( 'Y-m-t', gmmktime( 0, 0, 0, $month, 1, $year ) );

			$query_args = array(
				'tax_query'      => array(), // phpcs:ignore
				'order'          => 'ASC',
				'orderby'        => 'post_date',
				'post_type'      => 'wpcm_match',
				'post_status'    => array( 'publish', 'future' ),
				'posts_per_page' => -1,
				'date_query'     => array(
					array(
						'after'     => $first_day,
						'before'    => $last_day,
						'inclusive' => true,
					),
				),
			);

			if ( is_club_mode() ) {
				$club                     = get_default_club();
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

			$matches = get_posts( $query_args );

			// Group matches by day of month.
			$matches_by_day = array();
			foreach ( $matches as $match ) {
				$day = (int) gmdate( 'j', strtotime( $match->post_date ) );
				if ( ! isset( $matches_by_day[ $day ] ) ) {
					$matches_by_day[ $day ] = array();
				}
				$matches_by_day[ $day ][] = $match;
			}

			ob_start();
			wpclubmanager_get_template( 'shortcodes/match-calendar.php', array(
				'month'          => $month,
				'year'           => $year,
				'matches_by_day' => $matches_by_day,
			) );
			$output = ob_get_clean();

			wp_reset_postdata();
			if ( 'no' === $disable_cache ) {
				set_transient( $transient_name, $output, 4 * WEEK_IN_SECONDS );
				do_action( 'update_plugin_transient_keys', $transient_name );
			}
		}

		echo $output; // phpcs:ignore
	}
}
