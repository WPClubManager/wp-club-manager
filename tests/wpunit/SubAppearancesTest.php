<?php
/**
 * Tests for sub appearances count on the player profile.
 *
 * Verifies that get_player_subs_total() correctly counts only
 * checked substitute appearances, and that unchecked legacy subs
 * are excluded.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/83
 */

class SubAppearancesTest extends WPCMTestCase {

	/** @var int */
	private $club_id;

	/** @var int */
	private $away_club_id;

	/** @var int */
	private $player_id;

	public function _setUp() {
		parent::_setUp();

		update_option( 'wpcm_sport', 'soccer' );

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Subs Test FC',
			'post_status' => 'publish',
		) );

		$this->away_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Subs Away United',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', $this->club_id );

		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Subs Test Player',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->player_id, true );
		wp_delete_post( $this->club_id, true );
		wp_delete_post( $this->away_club_id, true );
		delete_option( 'wpcm_default_club' );

		parent::_tearDown();
	}

	/**
	 * Helper to create a match with player data.
	 *
	 * @param array $players Serialised wpcm_players structure.
	 * @return int Match post ID.
	 */
	private function create_match( $players ) {
		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Subs Test Match',
			'post_status' => 'publish',
		) );

		update_post_meta( $match_id, 'wpcm_home_club', $this->club_id );
		update_post_meta( $match_id, 'wpcm_away_club', $this->away_club_id );
		update_post_meta( $match_id, 'wpcm_played', '1' );
		update_post_meta( $match_id, 'wpcm_players', serialize( $players ) );

		return $match_id;
	}

	// -------------------------------------------------------------------
	// get_player_subs_total() — core sub count
	// -------------------------------------------------------------------

	public function test_checked_sub_is_counted() {
		$match_id = $this->create_match( array(
			'lineup' => array(),
			'subs'   => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 0,
					'assists' => 0,
				),
			),
		) );

		$subs = get_player_subs_total( $this->player_id );

		$this->assertEquals( 1, $subs );

		wp_delete_post( $match_id, true );
	}

	public function test_unchecked_sub_is_not_counted() {
		// Legacy data: player in subs array but without 'checked' key.
		$match_id = $this->create_match( array(
			'lineup' => array(),
			'subs'   => array(
				$this->player_id => array(
					'goals'   => 0,
					'assists' => 0,
				),
			),
		) );

		$subs = get_player_subs_total( $this->player_id );

		$this->assertEquals( 0, $subs, 'Unchecked sub should not be counted' );

		wp_delete_post( $match_id, true );
	}

	public function test_multiple_matches_count_only_checked_subs() {
		// Match 1: player is a checked sub.
		$match1 = $this->create_match( array(
			'lineup' => array(),
			'subs'   => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 1,
				),
			),
		) );

		// Match 2: player is in subs but NOT checked (legacy data).
		$match2 = $this->create_match( array(
			'lineup' => array(),
			'subs'   => array(
				$this->player_id => array(
					'goals' => 0,
				),
			),
		) );

		// Match 3: player is a checked sub.
		$match3 = $this->create_match( array(
			'lineup' => array(),
			'subs'   => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 0,
				),
			),
		) );

		$subs = get_player_subs_total( $this->player_id );

		$this->assertEquals( 2, $subs, 'Only checked subs should be counted' );

		wp_delete_post( $match1, true );
		wp_delete_post( $match2, true );
		wp_delete_post( $match3, true );
	}

	public function test_lineup_player_not_counted_as_sub() {
		$match_id = $this->create_match( array(
			'lineup' => array(
				$this->player_id => array(
					'checked' => '1',
					'goals'   => 2,
				),
			),
			'subs'   => array(),
		) );

		$subs = get_player_subs_total( $this->player_id );

		$this->assertEquals( 0, $subs );

		wp_delete_post( $match_id, true );
	}

	public function test_subs_total_returns_zero_with_no_matches() {
		$subs = get_player_subs_total( $this->player_id );

		$this->assertEquals( 0, $subs );
	}
}
