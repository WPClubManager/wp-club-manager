<?php
/**
 * Tests for individual (non-team) sport support.
 *
 * Verifies that matches can be created without an away club
 * and that match functions handle the absence gracefully.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/96
 */

class IndividualSportTest extends WPCMTestCase {

	/** @var int */
	private $club_id;

	/** @var int */
	private $match_id;

	/** @var string */
	private $original_sport;

	/** @var string|false */
	private $original_default_club;

	/** @var callable|null */
	private $sports_filter;

	/** @var array */
	private $original_options = array();

	public function _setUp() {
		parent::_setUp();

		$this->original_sport        = get_option( 'wpcm_sport' );
		$this->original_default_club = get_option( 'wpcm_default_club' );

		$options_to_snapshot = array(
			'wpcm_match_title_format',
			'wpcm_match_goals_delimiter',
			'wpcm_match_clubs_separator',
			'wpcm_hide_scores',
		);
		foreach ( $options_to_snapshot as $opt ) {
			$this->original_options[ $opt ] = get_option( $opt );
		}

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Athletics Club',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', $this->club_id );

		add_image_size( 'crest-small', 25, 25, false );
	}

	public function _tearDown() {
		if ( $this->match_id ) {
			wp_delete_post( $this->match_id, true );
		}
		wp_delete_post( $this->club_id, true );
		update_option( 'wpcm_sport', $this->original_sport );

		if ( false === $this->original_default_club ) {
			delete_option( 'wpcm_default_club' );
		} else {
			update_option( 'wpcm_default_club', $this->original_default_club );
		}

		if ( $this->sports_filter ) {
			remove_filter( 'wpcm_sports', $this->sports_filter );
			$this->sports_filter = null;
		}

		foreach ( $this->original_options as $opt => $value ) {
			if ( false === $value ) {
				delete_option( $opt );
			} else {
				update_option( $opt, $value );
			}
		}

		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Helper: set up an individual sport via filter
	// -----------------------------------------------------------------------

	private function register_individual_sport() {
		update_option( 'wpcm_sport', 'athletics' );

		$this->sports_filter = function ( $sports ) {
			$sports['athletics'] = array(
				'name'              => 'Athletics',
				'has_teams'         => false,
				'terms'             => array(
					'wpcm_position' => array(
						array( 'name' => '', 'slug' => '' ),
					),
				),
				'stats_labels'      => array(
					'mvp' => array(
						'name'  => 'Player of Match',
						'label' => 'POM',
					),
				),
				'standings_columns' => array(
					'p'   => array( 'name' => 'Played', 'label' => 'P' ),
					'w'   => array( 'name' => 'Won', 'label' => 'W' ),
					'l'   => array( 'name' => 'Lost', 'label' => 'L' ),
					'pts' => array( 'name' => 'Points', 'label' => 'Pts' ),
				),
			);
			return $sports;
		};
		add_filter( 'wpcm_sports', $this->sports_filter );
	}

	private function create_individual_match( $played = false ) {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Athletics Event',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->club_id );
		// No away club set — this is the key difference for individual sports.

		if ( $played ) {
			update_post_meta( $this->match_id, 'wpcm_played', '1' );
			update_post_meta( $this->match_id, 'wpcm_home_goals', '1' );
		}

		return $this->match_id;
	}

	// -----------------------------------------------------------------------
	// wpcm_is_team_sport()
	// -----------------------------------------------------------------------

	public function test_team_sport_returns_true_for_soccer() {
		update_option( 'wpcm_sport', 'soccer' );
		$this->assertTrue( wpcm_is_team_sport() );
	}

	public function test_team_sport_returns_false_for_individual_sport() {
		$this->register_individual_sport();
		$this->assertFalse( wpcm_is_team_sport() );
	}

	public function test_team_sport_defaults_to_true_for_unknown_sport() {
		update_option( 'wpcm_sport', 'unknown_sport_xyz' );
		$this->assertTrue( wpcm_is_team_sport() );
	}

	// -----------------------------------------------------------------------
	// Match title — should show only the club name, not "Club vs "
	// -----------------------------------------------------------------------

	public function test_match_title_shows_only_club_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		$title = match_title( 'Athletics Event', $match_id );

		$this->assertEquals( 'Athletics Club', $title );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_clubs() — side2 should be empty
	// -----------------------------------------------------------------------

	public function test_match_clubs_returns_empty_side2_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		$sides = wpcm_get_match_clubs( $match_id );

		$this->assertNotEmpty( $sides[0] );
		$this->assertEmpty( $sides[1] );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_opponents() — should return empty for individual sport
	// -----------------------------------------------------------------------

	public function test_match_opponents_returns_empty_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		$opponent = wpcm_get_match_opponents( $match_id );

		$this->assertEmpty( $opponent );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_result() — should return only home score
	// -----------------------------------------------------------------------

	public function test_match_result_returns_home_score_only_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match( true );

		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		update_option( 'wpcm_match_goals_delimiter', '-' );

		$result = wpcm_get_match_result( $match_id );

		$this->assertIsArray( $result );
		$this->assertEquals( '1', $result[1] );
		$this->assertEmpty( $result[2] );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_outcome() — no away club, so just check it handles gracefully
	// -----------------------------------------------------------------------

	public function test_match_outcome_returns_win_for_played_individual_match() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match( true );

		$outcome = wpcm_get_match_outcome( $match_id );

		$this->assertEquals( 'win', $outcome );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_badges() — should return only one badge
	// -----------------------------------------------------------------------

	public function test_match_badges_returns_empty_badge2_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		$badges = wpcm_get_match_badges( $match_id, 'crest-small' );

		$this->assertNotEmpty( $badges[0] );
		$this->assertEmpty( $badges[1] );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_match_venue() — should still work without away club
	// -----------------------------------------------------------------------

	public function test_match_venue_returns_home_status_for_individual_sport() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		$venue = wpcm_get_match_venue( $match_id );

		$this->assertIsArray( $venue );
	}

	// -----------------------------------------------------------------------
	// Match creation — can create match without away club
	// -----------------------------------------------------------------------

	public function test_can_create_match_without_away_club() {
		$this->register_individual_sport();
		$match_id = $this->create_individual_match();

		$this->assertGreaterThan( 0, $match_id );
		$this->assertEquals( 'wpcm_match', get_post_type( $match_id ) );
		$this->assertEquals(
			$this->club_id,
			(int) get_post_meta( $match_id, 'wpcm_home_club', true )
		);
		$this->assertEmpty( get_post_meta( $match_id, 'wpcm_away_club', true ) );
	}
}
