<?php
/**
 * Tests for player stats aggregation across matches.
 *
 * Verifies that manual stats can be stored and retrieved via the
 * wpcm_stats serialised meta, and that helper functions for stats
 * work correctly.
 */

class PlayerStatsTest extends WPCMTestCase {

	/** @var int */
	private $player_id;

	/** @var int */
	private $club_id;

	public function _setUp() {
		parent::_setUp();

		update_option( 'wpcm_sport', 'soccer' );

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Stats Club FC',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', $this->club_id );

		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Stats Player',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->player_id, true );
		wp_delete_post( $this->club_id, true );
		delete_option( 'wpcm_default_club' );

		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Manual stats storage (wpcm_stats meta)
	// -----------------------------------------------------------------------

	public function test_manual_stats_can_be_stored() {
		$stats = array(
			0 => array(
				0 => array(
					'appearances' => 5,
					'goals'       => 3,
					'assists'     => 2,
					'rating'      => 35,
				),
			),
		);

		update_post_meta( $this->player_id, 'wpcm_stats', serialize( $stats ) );
		$stored = unserialize( get_post_meta( $this->player_id, 'wpcm_stats', true ) );

		$this->assertIsArray( $stored );
		$this->assertEquals( 5, $stored[0][0]['appearances'] );
		$this->assertEquals( 3, $stored[0][0]['goals'] );
	}

	public function test_manual_stats_per_team_and_season() {
		$team_id   = 10;
		$season_id = 20;

		$stats = array(
			$team_id => array(
				$season_id => array(
					'appearances' => 8,
					'goals'       => 5,
					'assists'     => 3,
				),
			),
		);

		update_post_meta( $this->player_id, 'wpcm_stats', serialize( $stats ) );
		$stored = unserialize( get_post_meta( $this->player_id, 'wpcm_stats', true ) );

		$this->assertEquals( 8, $stored[ $team_id ][ $season_id ]['appearances'] );
	}

	// -----------------------------------------------------------------------
	// get_wpcm_player_manual_stats()
	// -----------------------------------------------------------------------

	public function test_get_wpcm_player_manual_stats_returns_stored_values() {
		$stats = array(
			0 => array(
				0 => array(
					'appearances' => 10,
					'goals'       => 7,
					'assists'     => 4,
				),
			),
		);

		update_post_meta( $this->player_id, 'wpcm_stats', serialize( $stats ) );

		$manual = get_wpcm_player_manual_stats( $this->player_id );

		$this->assertEquals( 10, $manual['appearances'] );
		$this->assertEquals( 7, $manual['goals'] );
	}

	public function test_get_wpcm_player_manual_stats_returns_empty_row_when_no_data() {
		$manual = get_wpcm_player_manual_stats( $this->player_id );

		$this->assertIsArray( $manual );
		$this->assertEquals( 0, $manual['appearances'] );
	}

	// -----------------------------------------------------------------------
	// get_wpcm_player_stats() — combined manual stats
	// -----------------------------------------------------------------------

	public function test_get_wpcm_player_stats_combined_manual_stats_uses_combined_entry() {
		$team = wp_insert_term( 'Stats Team', 'wpcm_team' );
		if ( is_wp_error( $team ) ) {
			$team_id = $team->get_error_data();
		} else {
			$team_id = $team['term_id'];
		}

		$season = wp_insert_term( 'Stats Season', 'wpcm_season' );
		if ( is_wp_error( $season ) ) {
			$season_id = $season->get_error_data();
		} else {
			$season_id = $season['term_id'];
		}

		wp_set_object_terms( $this->player_id, $team_id, 'wpcm_team' );
		wp_set_object_terms( $this->player_id, $season_id, 'wpcm_season' );

		// Store manual stats under the combined key (0,0) and a per-team key.
		$stats = array(
			0        => array(
				0 => array(
					'appearances' => 12,
					'goals'       => 8,
				),
			),
			$team_id => array(
				$season_id => array(
					'appearances' => 5,
					'goals'       => 2,
				),
			),
		);

		update_post_meta( $this->player_id, 'wpcm_stats', serialize( $stats ) );

		$result = get_wpcm_player_stats( $this->player_id );

		// The combined entry should use the (0,0) manual stats, not the per-team values.
		$this->assertArrayHasKey( 'manual', $result[0][0] );
		$this->assertEquals( 12, $result[0][0]['manual']['appearances'] );
		$this->assertEquals( 8, $result[0][0]['manual']['goals'] );

		wp_delete_term( $team_id, 'wpcm_team' );
		wp_delete_term( $season_id, 'wpcm_season' );
	}

	// -----------------------------------------------------------------------
	// get_wpcm_player_stats_empty_row()
	// -----------------------------------------------------------------------

	public function test_empty_row_has_appearances_key() {
		$empty = get_wpcm_player_stats_empty_row();

		$this->assertIsArray( $empty );
		$this->assertArrayHasKey( 'appearances', $empty );
		$this->assertEquals( 0, $empty['appearances'] );
	}

	public function test_empty_row_has_sport_specific_keys() {
		$empty = get_wpcm_player_stats_empty_row();

		// Soccer sport preset has goals, assists, etc.
		$this->assertArrayHasKey( 'goals', $empty );
		$this->assertArrayHasKey( 'assists', $empty );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_average_rating()
	// -----------------------------------------------------------------------

	public function test_average_rating_with_appearances() {
		$average = wpcm_get_player_average_rating( 35, 5 );
		$this->assertEquals( '7.00', $average );
	}

	public function test_average_rating_with_zero_rating() {
		$average = wpcm_get_player_average_rating( 0, 5 );
		$this->assertEquals( '0', $average );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_appearances()
	// -----------------------------------------------------------------------

	public function test_get_player_appearances_without_subs() {
		$detail = array( 'appearances' => 10 );
		$result = wpcm_get_player_appearances( $detail );

		$this->assertEquals( 10, $result );
	}

	public function test_get_player_appearances_with_subs() {
		$detail = array( 'appearances' => 10, 'subs' => 3 );
		$result = wpcm_get_player_appearances( $detail );

		$this->assertStringContainsString( '10', $result );
		$this->assertStringContainsString( '3', $result );
		$this->assertStringContainsString( 'wpcm-sub-appearances', $result );
	}

	public function test_get_player_appearances_with_zero_subs() {
		$detail = array( 'appearances' => 10, 'subs' => 0 );
		$result = wpcm_get_player_appearances( $detail );

		$this->assertEquals( 10, $result );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_stat()
	// -----------------------------------------------------------------------

	public function test_get_player_stat_returns_goals() {
		$detail = array( 'goals' => 5, 'appearances' => 10, 'rating' => 70 );
		$stat   = wpcm_get_player_stat( $detail, 'goals' );

		$this->assertEquals( 5, $stat );
	}

	public function test_get_player_stat_returns_rating_as_average() {
		$detail = array( 'goals' => 5, 'appearances' => 10, 'rating' => 70 );
		$stat   = wpcm_get_player_stat( $detail, 'rating' );

		$this->assertEquals( '7.00', $stat );
	}

	public function test_get_player_stat_returns_formatted_appearances() {
		$detail = array( 'appearances' => 10, 'subs' => 2, 'rating' => 0 );
		$stat   = wpcm_get_player_stat( $detail, 'appearances' );

		$this->assertStringContainsString( '10', $stat );
	}
}
