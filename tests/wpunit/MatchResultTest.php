<?php
/**
 * Tests for recording match results.
 *
 * Verifies that goal meta is saved correctly and the played flag
 * works as expected for both played and unplayed matches.
 */

class MatchResultTest extends WPCMTestCase {

	/** @var int */
	private $home_club_id;

	/** @var int */
	private $away_club_id;

	/** @var int */
	private $match_id;

	public function _setUp() {
		parent::_setUp();

		$this->home_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Result Home FC',
			'post_status' => 'publish',
		) );

		$this->away_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Result Away United',
			'post_status' => 'publish',
		) );

		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Result Home FC vs Result Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_away_club', $this->away_club_id );
	}

	public function _tearDown() {
		wp_delete_post( $this->match_id, true );
		wp_delete_post( $this->home_club_id, true );
		wp_delete_post( $this->away_club_id, true );

		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Played flag
	// -----------------------------------------------------------------------

	public function test_match_played_flag_defaults_to_empty() {
		$played = get_post_meta( $this->match_id, 'wpcm_played', true );
		$this->assertEmpty( $played );
	}

	public function test_match_played_flag_can_be_set() {
		update_post_meta( $this->match_id, 'wpcm_played', '1' );
		$this->assertEquals( '1', get_post_meta( $this->match_id, 'wpcm_played', true ) );
	}

	// -----------------------------------------------------------------------
	// Score meta
	// -----------------------------------------------------------------------

	public function test_home_goals_meta_is_saved() {
		update_post_meta( $this->match_id, 'wpcm_home_goals', '3' );
		$this->assertEquals( '3', get_post_meta( $this->match_id, 'wpcm_home_goals', true ) );
	}

	public function test_away_goals_meta_is_saved() {
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );
		$this->assertEquals( '1', get_post_meta( $this->match_id, 'wpcm_away_goals', true ) );
	}

	public function test_match_with_draw_score() {
		update_post_meta( $this->match_id, 'wpcm_home_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );

		$home = get_post_meta( $this->match_id, 'wpcm_home_goals', true );
		$away = get_post_meta( $this->match_id, 'wpcm_away_goals', true );

		$this->assertEquals( $home, $away );
	}

	public function test_match_with_home_win() {
		update_post_meta( $this->match_id, 'wpcm_home_goals', '3' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );

		$home = (int) get_post_meta( $this->match_id, 'wpcm_home_goals', true );
		$away = (int) get_post_meta( $this->match_id, 'wpcm_away_goals', true );

		$this->assertGreaterThan( $away, $home );
	}

	public function test_match_with_away_win() {
		update_post_meta( $this->match_id, 'wpcm_home_goals', '0' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );

		$home = (int) get_post_meta( $this->match_id, 'wpcm_home_goals', true );
		$away = (int) get_post_meta( $this->match_id, 'wpcm_away_goals', true );

		$this->assertGreaterThan( $home, $away );
	}

	// -----------------------------------------------------------------------
	// Postponed / Walkover
	// -----------------------------------------------------------------------

	public function test_match_postponed_meta() {
		update_post_meta( $this->match_id, '_wpcm_postponed', '1' );
		$this->assertEquals( '1', get_post_meta( $this->match_id, '_wpcm_postponed', true ) );
	}

	public function test_match_walkover_home_win() {
		update_post_meta( $this->match_id, '_wpcm_postponed', '1' );
		update_post_meta( $this->match_id, '_wpcm_walkover', 'home_win' );

		$this->assertEquals( 'home_win', get_post_meta( $this->match_id, '_wpcm_walkover', true ) );
	}

	public function test_match_walkover_away_win() {
		update_post_meta( $this->match_id, '_wpcm_postponed', '1' );
		update_post_meta( $this->match_id, '_wpcm_walkover', 'away_win' );

		$this->assertEquals( 'away_win', get_post_meta( $this->match_id, '_wpcm_walkover', true ) );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_result() — basic result formatting
	// -----------------------------------------------------------------------

	public function test_wpcm_get_match_result_returns_array() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		update_option( 'wpcm_match_goals_delimiter', '-' );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );

		$result = wpcm_get_match_result( $this->match_id );

		$this->assertIsArray( $result );
		$this->assertCount( 4, $result );
	}

	public function test_wpcm_get_match_result_contains_goals() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		update_option( 'wpcm_match_goals_delimiter', '-' );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );

		$result = wpcm_get_match_result( $this->match_id );

		// $result[1] = home side score, $result[2] = away side score
		$this->assertEquals( '2', $result[1] );
		$this->assertEquals( '1', $result[2] );
	}

	public function test_wpcm_get_match_result_empty_when_not_played() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		update_option( 'wpcm_match_goals_delimiter', '-' );
		// Do NOT set wpcm_played.

		$result = wpcm_get_match_result( $this->match_id );

		// When match is not played, the main result string should be empty.
		$this->assertEmpty( $result[0] );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_outcome() — win/loss/draw detection
	// -----------------------------------------------------------------------

	public function test_wpcm_get_match_outcome_home_win() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_default_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '3' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );

		$outcome = wpcm_get_match_outcome( $this->match_id );
		$this->assertEquals( 'win', $outcome );
	}

	public function test_wpcm_get_match_outcome_away_loss() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_default_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '0' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '2' );

		$outcome = wpcm_get_match_outcome( $this->match_id );
		$this->assertEquals( 'loss', $outcome );
	}

	public function test_wpcm_get_match_outcome_draw() {
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_default_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '1' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );

		$outcome = wpcm_get_match_outcome( $this->match_id );
		$this->assertEquals( 'draw', $outcome );
	}
}
