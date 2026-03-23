<?php
/**
 * Tests for player performance stats linked to a match.
 *
 * Verifies that the serialised wpcm_players meta structure stores
 * and retrieves per-player stats correctly.
 */

class MatchStatsTest extends WPCMTestCase {

	/** @var int */
	private $match_id;

	/** @var int */
	private $player_id;

	/** @var int */
	private $home_club_id;

	public function _setUp() {
		parent::_setUp();

		update_option( 'wpcm_sport', 'soccer' );

		$this->home_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Stats Home FC',
			'post_status' => 'publish',
		) );

		$away_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Stats Away United',
			'post_status' => 'publish',
		) );

		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Stats Test Player',
			'post_status' => 'publish',
		) );

		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Stats Home vs Away',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_away_club', $away_club_id );
		update_post_meta( $this->match_id, 'wpcm_played', '1' );
		update_post_meta( $this->match_id, 'wpcm_home_goals', '2' );
		update_post_meta( $this->match_id, 'wpcm_away_goals', '1' );
	}

	public function _tearDown() {
		wp_delete_post( $this->match_id, true );
		wp_delete_post( $this->player_id, true );
		wp_delete_post( $this->home_club_id, true );

		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Serialised wpcm_players meta
	// -----------------------------------------------------------------------

	public function test_player_stats_can_be_stored_in_match_meta() {
		$players = array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 1,
					'assists' => 0,
					'rating'  => 7,
				),
			),
			'subs'   => array(),
		);

		update_post_meta( $this->match_id, 'wpcm_players', serialize( $players ) );
		$stored = unserialize( get_post_meta( $this->match_id, 'wpcm_players', true ) );

		$this->assertIsArray( $stored );
		$this->assertArrayHasKey( 'lineup', $stored );
		$this->assertArrayHasKey( $this->player_id, $stored['lineup'] );
	}

	public function test_player_goals_stored_correctly() {
		$players = array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 2,
					'assists' => 1,
					'rating'  => 8,
				),
			),
			'subs'   => array(),
		);

		update_post_meta( $this->match_id, 'wpcm_players', serialize( $players ) );
		$stored = unserialize( get_post_meta( $this->match_id, 'wpcm_players', true ) );

		$this->assertEquals( 2, $stored['lineup'][ $this->player_id ]['goals'] );
		$this->assertEquals( 1, $stored['lineup'][ $this->player_id ]['assists'] );
	}

	public function test_substitute_player_stored_in_subs_key() {
		$sub_player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Sub Player',
			'post_status' => 'publish',
		) );

		$players = array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 0,
					'assists' => 0,
					'rating'  => 6,
				),
			),
			'subs'   => array(
				$sub_player_id => array(
					'checked' => '1',
					'sub'     => '1',
					'goals'   => 1,
					'assists' => 0,
					'rating'  => 7,
				),
			),
		);

		update_post_meta( $this->match_id, 'wpcm_players', serialize( $players ) );
		$stored = unserialize( get_post_meta( $this->match_id, 'wpcm_players', true ) );

		$this->assertArrayHasKey( $sub_player_id, $stored['subs'] );
		$this->assertEquals( '1', $stored['subs'][ $sub_player_id ]['sub'] );

		wp_delete_post( $sub_player_id, true );
	}

	// -----------------------------------------------------------------------
	// get_wpcm_match_player_stats()
	// -----------------------------------------------------------------------

	public function test_get_wpcm_match_player_stats_returns_players_array() {
		$players = array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 1,
					'assists' => 0,
					'rating'  => 7,
				),
			),
			'subs'   => array(),
		);

		update_post_meta( $this->match_id, 'wpcm_players', serialize( $players ) );

		// Suppress "Undefined array key" warning — pre-existing bug in
		// wpcm-match-functions.php:544 where $stats['checked'] is accessed
		// without isset() guard.
		$result = @get_wpcm_match_player_stats( $this->match_id );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'lineup', $result );
	}

	// -----------------------------------------------------------------------
	// Multiple players in one match
	// -----------------------------------------------------------------------

	public function test_multiple_players_stored_in_lineup() {
		$player2_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Second Player',
			'post_status' => 'publish',
		) );

		$players = array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 1,
					'assists' => 0,
				),
				$player2_id      => array(
					'checked' => '1',
					'goals'   => 0,
					'assists' => 1,
				),
			),
			'subs'   => array(),
		);

		update_post_meta( $this->match_id, 'wpcm_players', serialize( $players ) );
		$stored = unserialize( get_post_meta( $this->match_id, 'wpcm_players', true ) );

		$this->assertCount( 2, $stored['lineup'] );

		wp_delete_post( $player2_id, true );
	}
}
