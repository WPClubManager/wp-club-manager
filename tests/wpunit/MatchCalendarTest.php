<?php
/**
 * Tests for the match_calendar shortcode and iCal feed.
 *
 * Covers shortcode registration, calendar grid output with matches,
 * month navigation attributes, taxonomy filtering, and iCal feed
 * generation with valid VCALENDAR/VEVENT structure.
 */

class MatchCalendarTest extends WPCMTestCase {

	/** @var int */
	private $home_club_id;

	/** @var int */
	private $away_club_id;

	/** @var int[] */
	private $match_ids = array();

	/** @var int */
	private $comp_term_id;

	/** @var int */
	private $season_term_id;

	public function _setUp() {
		parent::_setUp();

		$this->home_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Home FC',
			'post_status' => 'publish',
		) );

		$this->away_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Away United',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', $this->home_club_id );

		$comp = wp_insert_term( 'Test League', 'wpcm_comp' );
		$this->comp_term_id = is_wp_error( $comp ) ? $comp->get_error_data() : $comp['term_id'];

		$season = wp_insert_term( '2026', 'wpcm_season' );
		$this->season_term_id = is_wp_error( $season ) ? $season->get_error_data() : $season['term_id'];
	}

	public function _tearDown() {
		foreach ( $this->match_ids as $id ) {
			wp_delete_post( $id, true );
		}
		wp_delete_post( $this->home_club_id, true );
		wp_delete_post( $this->away_club_id, true );

		parent::_tearDown();
	}

	/**
	 * Helper to create a match on a specific date.
	 *
	 * @param string $date Y-m-d H:i:s format.
	 * @param bool   $played Whether the match has been played.
	 * @return int Post ID.
	 */
	private function create_match( $date, $played = false ) {
		$status = ( strtotime( $date ) > time() && ! $played ) ? 'future' : 'publish';

		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => $status,
			'post_date'   => $date,
		) );

		update_post_meta( $match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $match_id, 'wpcm_away_club', $this->away_club_id );
		update_post_meta( $match_id, '_wpcm_match_datetime', $date );

		if ( $played ) {
			update_post_meta( $match_id, 'wpcm_played', '1' );
			update_post_meta( $match_id, 'wpcm_home_goals', '2' );
			update_post_meta( $match_id, 'wpcm_away_goals', '1' );
		}

		wp_set_object_terms( $match_id, $this->comp_term_id, 'wpcm_comp' );
		wp_set_object_terms( $match_id, $this->season_term_id, 'wpcm_season' );

		$this->match_ids[] = $match_id;

		return $match_id;
	}

	// -------------------------------------------------------------------
	// Shortcode registration
	// -------------------------------------------------------------------

	public function test_match_calendar_shortcode_is_registered() {
		$this->assertTrue( shortcode_exists( 'match_calendar' ), 'Shortcode [match_calendar] should be registered' );
	}

	// -------------------------------------------------------------------
	// Calendar output structure
	// -------------------------------------------------------------------

	public function test_calendar_outputs_wrapper_div() {
		$output = do_shortcode( '[match_calendar]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );
	}

	public function test_calendar_outputs_calendar_table() {
		$output = do_shortcode( '[match_calendar]' );
		$this->assertStringContainsString( 'wpcm-calendar', $output );
		$this->assertStringContainsString( '<table', $output );
	}

	public function test_calendar_shows_month_and_year_heading() {
		$output = do_shortcode( '[match_calendar month="6" year="2026"]' );
		$this->assertStringContainsString( 'June', $output );
		$this->assertStringContainsString( '2026', $output );
	}

	public function test_calendar_shows_day_of_week_headers() {
		$output = do_shortcode( '[match_calendar month="6" year="2026"]' );
		$this->assertStringContainsString( 'Mon', $output );
		$this->assertStringContainsString( 'Fri', $output );
		$this->assertStringContainsString( 'Sun', $output );
	}

	// -------------------------------------------------------------------
	// Matches in the calendar
	// -------------------------------------------------------------------

	public function test_calendar_displays_match_on_correct_day() {
		$this->create_match( '2026-01-15 15:00:00', true );

		$output = do_shortcode( '[match_calendar month="1" year="2026"]' );
		$this->assertStringContainsString( 'wpcm-calendar-match', $output );
	}

	public function test_calendar_shows_future_fixtures() {
		$this->create_match( '2028-06-20 19:30:00', false );

		$output = do_shortcode( '[match_calendar month="6" year="2028"]' );
		$this->assertStringContainsString( 'wpcm-calendar-match', $output );
	}

	public function test_calendar_filters_by_competition() {
		$this->create_match( '2026-03-10 15:00:00', true );

		$output = do_shortcode( '[match_calendar month="3" year="2026" comp="' . $this->comp_term_id . '"]' );
		$this->assertStringContainsString( 'wpcm-calendar-match', $output );

		// Non-existent comp should show no matches.
		$output_empty = do_shortcode( '[match_calendar month="3" year="2026" comp="99999"]' );
		$this->assertStringNotContainsString( 'wpcm-calendar-match', $output_empty );
	}

	public function test_calendar_filters_by_season() {
		$this->create_match( '2026-04-05 14:00:00', true );

		$output = do_shortcode( '[match_calendar month="4" year="2026" season="' . $this->season_term_id . '"]' );
		$this->assertStringContainsString( 'wpcm-calendar-match', $output );
	}

	// -------------------------------------------------------------------
	// iCal feed
	// -------------------------------------------------------------------

	public function test_ical_feed_class_exists() {
		$this->assertTrue( class_exists( 'WPCM_iCal_Feed' ), 'WPCM_iCal_Feed class should exist' );
	}

	public function test_ical_generate_returns_valid_vcalendar() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		$this->assertStringContainsString( 'BEGIN:VCALENDAR', $output );
		$this->assertStringContainsString( 'END:VCALENDAR', $output );
		$this->assertStringContainsString( 'VERSION:2.0', $output );
		$this->assertStringContainsString( 'PRODID:', $output );
	}

	public function test_ical_contains_vevent_for_match() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		$this->assertStringContainsString( 'BEGIN:VEVENT', $output );
		$this->assertStringContainsString( 'END:VEVENT', $output );
		$this->assertStringContainsString( 'DTSTART:', $output );
		$this->assertStringContainsString( 'SUMMARY:', $output );
	}

	public function test_ical_vevent_contains_match_title() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		$this->assertStringContainsString( 'Home FC', $output );
	}

	public function test_ical_filters_by_competition() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate( array( 'comp' => $this->comp_term_id ) );
		$this->assertStringContainsString( 'BEGIN:VEVENT', $output );

		$output_empty = $feed->generate( array( 'comp' => 99999 ) );
		$this->assertStringNotContainsString( 'BEGIN:VEVENT', $output_empty );
	}

	public function test_ical_filters_by_season() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate( array( 'season' => $this->season_term_id ) );
		$this->assertStringContainsString( 'BEGIN:VEVENT', $output );
	}

	public function test_ical_dtstart_format_is_correct() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		// iCal datetime format: YYYYMMDDTHHMMSS
		$this->assertMatchesRegularExpression( '/DTSTART:\d{8}T\d{6}/', $output );
	}

	public function test_ical_has_uid_per_event() {
		$this->create_match( '2026-02-14 15:00:00', true );

		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		$this->assertStringContainsString( 'UID:', $output );
	}

	public function test_ical_empty_when_no_matches() {
		$feed   = new WPCM_iCal_Feed();
		$output = $feed->generate();

		$this->assertStringContainsString( 'BEGIN:VCALENDAR', $output );
		$this->assertStringNotContainsString( 'BEGIN:VEVENT', $output );
	}

	// -------------------------------------------------------------------
	// iCal feed URL
	// -------------------------------------------------------------------

	public function test_ical_feed_url_contains_wpcm_ical() {
		$feed = new WPCM_iCal_Feed();
		$url  = $feed->get_feed_url();

		$this->assertStringContainsString( 'wpcm_ical', $url );
	}
}
