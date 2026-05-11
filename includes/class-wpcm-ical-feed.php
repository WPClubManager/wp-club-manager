<?php
/**
 * WPCM iCal Feed
 *
 * Generates an iCalendar (.ics) feed of matches for subscribing
 * to fixtures in external calendar applications.
 *
 * @class       WPCM_ICal_Feed
 * @version     2.4.0
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_ICal_Feed
 */
class WPCM_ICal_Feed {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_feed' ) );
	}

	/**
	 * Register the iCal feed endpoint.
	 */
	public static function add_feed() {
		add_feed( 'wpcm_ical', array( __CLASS__, 'render_feed' ) );
	}

	/**
	 * Render the iCal feed response.
	 */
	public static function render_feed() {
		$comp   = isset( $_GET['comp'] ) ? absint( $_GET['comp'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification
		$season = isset( $_GET['season'] ) ? absint( $_GET['season'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification
		$team   = isset( $_GET['team'] ) ? absint( $_GET['team'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification

		$args = array_filter( array(
			'comp'   => $comp,
			'season' => $season,
			'team'   => $team,
		) );

		$feed = new self();

		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: inline; filename="matches.ics"' );

		echo $feed->generate( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Get the URL for the iCal feed.
	 *
	 * @param array $args Optional query parameters (comp, season, team).
	 * @return string
	 */
	public function get_feed_url( $args = array() ) {
		$url = get_feed_link( 'wpcm_ical' );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	/**
	 * Generate iCal output.
	 *
	 * @param array $args Optional filters: comp, season, team.
	 * @return string iCalendar formatted string.
	 */
	public function generate( $args = array() ) {
		$comp   = isset( $args['comp'] ) ? $args['comp'] : null;
		$season = isset( $args['season'] ) ? $args['season'] : null;
		$team   = isset( $args['team'] ) ? $args['team'] : null;

		// Limit feed to 6 months past and 12 months future to avoid large responses.
		$date_start = gmdate( 'Y-m-d', strtotime( '-6 months' ) );
		$date_end   = gmdate( 'Y-m-d', strtotime( '+12 months' ) );

		$query_args = array(
			'tax_query'      => array(), // phpcs:ignore
			'order'          => 'ASC',
			'orderby'        => 'post_date',
			'post_type'      => 'wpcm_match',
			'post_status'    => array( 'publish', 'future' ),
			'posts_per_page' => -1,
			'date_query'     => array(
				array(
					'after'     => $date_start,
					'before'    => $date_end,
					'inclusive' => true,
				),
			),
		);

		if ( is_club_mode() ) {
			$club                         = get_default_club();
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

		$lines   = array();
		$lines[] = 'BEGIN:VCALENDAR';
		$lines[] = 'VERSION:2.0';
		$lines[] = 'PRODID:-//WP Club Manager//NONSGML v' . WPCM_VERSION . '//EN';
		$lines[] = 'CALSCALE:GREGORIAN';
		$lines[] = 'METHOD:PUBLISH';
		/* translators: %s: site name */
		$lines[] = 'X-WR-CALNAME:' . $this->escape_ical_text( sprintf( __( '%s Matches', 'wp-club-manager' ), get_bloginfo( 'name' ) ) );

		foreach ( $matches as $match ) {
			$lines = array_merge( $lines, $this->build_vevent( $match ) );
		}

		$lines[] = 'END:VCALENDAR';

		wp_reset_postdata();

		$folded = array_map( array( $this, 'fold_line' ), $lines );

		return implode( "\r\n", $folded ) . "\r\n";
	}

	/**
	 * Build a VEVENT block for a match.
	 *
	 * @param WP_Post $match Match post object.
	 * @return array Lines for the VEVENT.
	 */
	private function build_vevent( $match ) {
		$timestamp  = get_post_time( 'U', true, $match );
		$dtstart    = gmdate( 'Ymd\THis\Z', $timestamp );
		// Default match duration: 2 hours.
		$dtend      = gmdate( 'Ymd\THis\Z', $timestamp + 7200 );
		$dtstamp    = gmdate( 'Ymd\THis\Z' );
		$uid        = 'wpcm-match-' . $match->ID . '@' . wp_parse_url( home_url(), PHP_URL_HOST );
		$url        = get_post_permalink( $match->ID, false, true );
		$played     = get_post_meta( $match->ID, 'wpcm_played', true );

		$sides   = wpcm_get_match_clubs( $match->ID, false );
		$summary = $sides[0] . ' vs ' . $sides[1];

		$description_parts = array();
		$comp_data         = wpcm_get_match_comp( $match->ID );
		if ( ! empty( $comp_data[0] ) ) {
			$description_parts[] = $comp_data[0];
		}
		if ( $played ) {
			$result              = wpcm_get_match_result( $match->ID );
			/* translators: %s: match result score */
			$description_parts[] = sprintf( __( 'Result: %s', 'wp-club-manager' ), $result[0] );
		}

		$venue_data = wpcm_get_match_venue( $match->ID );
		$location   = '';
		if ( isset( $venue_data['name'] ) && ! empty( $venue_data['name'] ) ) {
			$location = $venue_data['name'];
			if ( ! empty( $venue_data['address'] ) ) {
				$location .= ', ' . $venue_data['address'];
			}
		}

		$lines   = array();
		$lines[] = 'BEGIN:VEVENT';
		$lines[] = 'UID:' . $uid;
		$lines[] = 'DTSTAMP:' . $dtstamp;
		$lines[] = 'DTSTART:' . $dtstart;
		$lines[] = 'DTEND:' . $dtend;
		$lines[] = 'SUMMARY:' . $this->escape_ical_text( $summary );

		if ( ! empty( $description_parts ) ) {
			$lines[] = 'DESCRIPTION:' . $this->escape_ical_text( implode( "\n", $description_parts ) );
		}

		if ( '' !== $location ) {
			$lines[] = 'LOCATION:' . $this->escape_ical_text( $location );
		}

		$lines[] = 'URL:' . $url;
		$lines[] = 'STATUS:CONFIRMED';
		$lines[] = 'END:VEVENT';

		return $lines;
	}

	/**
	 * Escape text for iCalendar format (RFC 5545).
	 *
	 * @param string $text Text to escape.
	 * @return string Escaped text.
	 */
	private function escape_ical_text( $text ) {
		$text = str_replace( '\\', '\\\\', $text );
		$text = str_replace( "\r\n", '\n', $text );
		$text = str_replace( "\r", '\n', $text );
		$text = str_replace( "\n", '\n', $text );
		$text = str_replace( ',', '\,', $text );
		$text = str_replace( ';', '\;', $text );
		return $text;
	}

	/**
	 * Fold a single iCal line at 75 octets per RFC 5545.
	 *
	 * @param string $line The line to fold.
	 * @return string Folded line.
	 */
	private function fold_line( $line ) {
		if ( strlen( $line ) <= 75 ) {
			return $line;
		}

		$cut_func = function_exists( 'mb_strcut' ) ? 'mb_strcut' : 'substr';

		$folded    = $cut_func( $line, 0, 75 );
		$rest      = $cut_func( $line, 75 );
		$rest_len  = strlen( $rest );

		while ( $rest_len > 0 ) {
			$folded   .= "\r\n " . $cut_func( $rest, 0, 74 );
			$rest      = $cut_func( $rest, 74 );
			$rest_len  = strlen( $rest );
		}

		return $folded;
	}
}
