<?php
/**
 * Tests for league table standings computation.
 *
 * Creates clubs, records match results, and verifies that
 * get_wpcm_club_auto_stats() correctly computes W/D/L, points,
 * goals for/against, and goal difference for the soccer sport.
 */

class LeagueTableTest extends WPCMTestCase {

	/** @var int */
	private $club_a;

	/** @var int */
	private $club_b;

	/** @var int */
	private $club_c;

	/** @var int */
	private $comp_id;

	/** @var int */
	private $season_id;

	/** @var array Match IDs for cleanup. */
	private $match_ids = array();

	public function _setUp() {
		parent::_setUp();

		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_standings_win_points', 3 );
		update_option( 'wpcm_standings_draw_points', 1 );
		update_option( 'wpcm_standings_loss_points', 0 );

		$this->club_a = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'League Club A',
			'post_status' => 'publish',
		) );

		$this->club_b = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'League Club B',
			'post_status' => 'publish',
		) );

		$this->club_c = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'League Club C',
			'post_status' => 'publish',
		) );

		// Create competition and season.
		$comp = wp_insert_term( 'Test League', 'wpcm_comp' );
		$this->comp_id = is_wp_error( $comp ) ? $comp->get_error_data() : $comp['term_id'];

		$season = wp_insert_term( 'League Season', 'wpcm_season' );
		$this->season_id = is_wp_error( $season ) ? $season->get_error_data() : $season['term_id'];

		// Assign clubs to competition and season.
		foreach ( array( $this->club_a, $this->club_b, $this->club_c ) as $club_id ) {
			wp_set_object_terms( $club_id, $this->comp_id, 'wpcm_comp' );
			wp_set_object_terms( $club_id, $this->season_id, 'wpcm_season' );
		}
	}

	public function _tearDown() {
		foreach ( $this->match_ids as $id ) {
			wp_delete_post( $id, true );
		}
		wp_delete_post( $this->club_a, true );
		wp_delete_post( $this->club_b, true );
		wp_delete_post( $this->club_c, true );

		parent::_tearDown();
	}

	/**
	 * Helper: create a played match with a result.
	 */
	private function create_match( $home_id, $away_id, $home_goals, $away_goals ) {
		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'League Match',
			'post_status' => 'publish',
		) );

		update_post_meta( $match_id, 'wpcm_home_club', $home_id );
		update_post_meta( $match_id, 'wpcm_away_club', $away_id );
		update_post_meta( $match_id, 'wpcm_home_goals', $home_goals );
		update_post_meta( $match_id, 'wpcm_away_goals', $away_goals );
		update_post_meta( $match_id, 'wpcm_played', '1' );

		wp_set_object_terms( $match_id, $this->comp_id, 'wpcm_comp' );
		wp_set_object_terms( $match_id, $this->season_id, 'wpcm_season' );

		$this->match_ids[] = $match_id;

		return $match_id;
	}

	// -----------------------------------------------------------------------
	// Single match — home win
	// -----------------------------------------------------------------------

	public function test_home_win_gives_three_points_to_winner() {
		$this->create_match( $this->club_a, $this->club_b, 2, 0 );

		$stats_a = get_wpcm_club_auto_stats( $this->club_a, $this->comp_id, $this->season_id );

		$this->assertEquals( 1, $stats_a['p'] );
		$this->assertEquals( 1, $stats_a['w'] );
		$this->assertEquals( 0, $stats_a['d'] );
		$this->assertEquals( 0, $stats_a['l'] );
		$this->assertEquals( 3, $stats_a['pts'] );
	}

	public function test_home_win_gives_zero_points_to_loser() {
		$this->create_match( $this->club_a, $this->club_b, 2, 0 );

		$stats_b = get_wpcm_club_auto_stats( $this->club_b, $this->comp_id, $this->season_id );

		$this->assertEquals( 1, $stats_b['p'] );
		$this->assertEquals( 0, $stats_b['w'] );
		$this->assertEquals( 1, $stats_b['l'] );
		$this->assertEquals( 0, $stats_b['pts'] );
	}

	// -----------------------------------------------------------------------
	// Single match — draw
	// -----------------------------------------------------------------------

	public function test_draw_gives_one_point_each() {
		$this->create_match( $this->club_a, $this->club_b, 1, 1 );

		$stats_a = get_wpcm_club_auto_stats( $this->club_a, $this->comp_id, $this->season_id );
		$stats_b = get_wpcm_club_auto_stats( $this->club_b, $this->comp_id, $this->season_id );

		$this->assertEquals( 1, $stats_a['d'] );
		$this->assertEquals( 1, $stats_a['pts'] );

		$this->assertEquals( 1, $stats_b['d'] );
		$this->assertEquals( 1, $stats_b['pts'] );
	}

	// -----------------------------------------------------------------------
	// Goals for/against and goal difference
	// -----------------------------------------------------------------------

	public function test_goals_for_and_against_for_home_team() {
		$this->create_match( $this->club_a, $this->club_b, 3, 1 );

		$stats_a = get_wpcm_club_auto_stats( $this->club_a, $this->comp_id, $this->season_id );

		$this->assertEquals( 3, $stats_a['f'] );
		$this->assertEquals( 1, $stats_a['a'] );
		$this->assertEquals( 2, $stats_a['gd'] );
	}

	public function test_goals_for_and_against_for_away_team() {
		$this->create_match( $this->club_a, $this->club_b, 3, 1 );

		$stats_b = get_wpcm_club_auto_stats( $this->club_b, $this->comp_id, $this->season_id );

		$this->assertEquals( 1, $stats_b['f'] );
		$this->assertEquals( 3, $stats_b['a'] );
		$this->assertEquals( -2, $stats_b['gd'] );
	}

	// -----------------------------------------------------------------------
	// Multiple matches
	// -----------------------------------------------------------------------

	public function test_multiple_matches_accumulate_stats() {
		// Club A beats Club B 2-0 (home).
		$this->create_match( $this->club_a, $this->club_b, 2, 0 );
		// Club A draws with Club C 1-1 (home).
		$this->create_match( $this->club_a, $this->club_c, 1, 1 );
		// Club A loses to Club B 0-3 (away).
		$this->create_match( $this->club_b, $this->club_a, 3, 0 );

		$stats_a = get_wpcm_club_auto_stats( $this->club_a, $this->comp_id, $this->season_id );

		$this->assertEquals( 3, $stats_a['p'] );
		$this->assertEquals( 1, $stats_a['w'] );
		$this->assertEquals( 1, $stats_a['d'] );
		$this->assertEquals( 1, $stats_a['l'] );
		// Points: 3 (win) + 1 (draw) + 0 (loss) = 4
		$this->assertEquals( 4, $stats_a['pts'] );
		// Goals: f = 2+1+0 = 3, a = 0+1+3 = 4
		$this->assertEquals( 3, $stats_a['f'] );
		$this->assertEquals( 4, $stats_a['a'] );
		$this->assertEquals( -1, $stats_a['gd'] );
	}

	// -----------------------------------------------------------------------
	// Friendly matches should be excluded
	// -----------------------------------------------------------------------

	public function test_friendly_matches_excluded_from_stats() {
		$match_id = $this->create_match( $this->club_a, $this->club_b, 5, 0 );
		update_post_meta( $match_id, 'wpcm_friendly', '1' );

		$stats_a = get_wpcm_club_auto_stats( $this->club_a, $this->comp_id, $this->season_id );

		$this->assertEquals( 0, $stats_a['p'] );
		$this->assertEquals( 0, $stats_a['pts'] );
	}

	// -----------------------------------------------------------------------
	// Empty row helper
	// -----------------------------------------------------------------------

	public function test_get_wpcm_club_stats_empty_row_has_all_keys() {
		$empty = get_wpcm_club_stats_empty_row();

		$this->assertIsArray( $empty );
		$this->assertArrayHasKey( 'p', $empty );
		$this->assertArrayHasKey( 'w', $empty );
		$this->assertArrayHasKey( 'd', $empty );
		$this->assertArrayHasKey( 'l', $empty );
		$this->assertArrayHasKey( 'f', $empty );
		$this->assertArrayHasKey( 'a', $empty );
		$this->assertArrayHasKey( 'gd', $empty );
		$this->assertArrayHasKey( 'pts', $empty );
	}

	public function test_empty_row_all_values_are_zero() {
		$empty = get_wpcm_club_stats_empty_row();

		foreach ( $empty as $key => $value ) {
			$this->assertEquals( 0, $value, "Key '{$key}' should default to 0" );
		}
	}
}
