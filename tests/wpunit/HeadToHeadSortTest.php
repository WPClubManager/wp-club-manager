<?php
/**
 * Tests for head-to-head tiebreaker sorting in league tables.
 *
 * Verifies that when two clubs are tied on points and goal difference,
 * the head-to-head record between them is used as a tiebreaker when
 * the H2H sorting option is enabled.
 */

class HeadToHeadSortTest extends WPCMTestCase {

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
			'post_title'  => 'H2H Club A',
			'post_name'   => 'h2h-club-a',
			'post_status' => 'publish',
		) );

		$this->club_b = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'H2H Club B',
			'post_name'   => 'h2h-club-b',
			'post_status' => 'publish',
		) );

		$this->club_c = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'H2H Club C',
			'post_name'   => 'h2h-club-c',
			'post_status' => 'publish',
		) );

		$comp = wp_insert_term( 'H2H Test League', 'wpcm_comp' );
		$this->comp_id = is_wp_error( $comp ) ? $comp->get_error_data() : $comp['term_id'];

		$season = wp_insert_term( 'H2H Season', 'wpcm_season' );
		$this->season_id = is_wp_error( $season ) ? $season->get_error_data() : $season['term_id'];

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

		wpcm_clear_h2h_context();

		parent::_tearDown();
	}

	/**
	 * Helper: create a played match with a result.
	 */
	private function create_match( $home_id, $away_id, $home_goals, $away_goals ) {
		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'H2H Match',
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

	/**
	 * Build club objects with stats for sorting, same as the shortcode does.
	 */
	private function build_clubs_for_sort() {
		$club_ids = array( $this->club_a, $this->club_b, $this->club_c );
		$clubs    = array();

		foreach ( $club_ids as $club_id ) {
			$club             = get_post( $club_id );
			$club->wpcm_stats = get_wpcm_club_auto_stats( $club_id, $this->comp_id, $this->season_id );
			$clubs[]          = $club;
		}

		return $clubs;
	}

	// -----------------------------------------------------------------------
	// H2H points calculation
	// -----------------------------------------------------------------------

	public function test_h2h_points_returns_correct_values_for_winner() {
		// Club A beats Club B 2-1.
		$this->create_match( $this->club_a, $this->club_b, 2, 1 );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		$this->assertEquals( 3, $result['a_points'] );
		$this->assertEquals( 0, $result['b_points'] );
	}

	public function test_h2h_points_returns_correct_values_for_draw() {
		// Club A draws with Club B 1-1.
		$this->create_match( $this->club_a, $this->club_b, 1, 1 );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		$this->assertEquals( 1, $result['a_points'] );
		$this->assertEquals( 1, $result['b_points'] );
	}

	public function test_h2h_points_accumulates_over_multiple_matches() {
		// Club A beats Club B 2-0 at home.
		$this->create_match( $this->club_a, $this->club_b, 2, 0 );
		// Club B beats Club A 3-1 at home.
		$this->create_match( $this->club_b, $this->club_a, 3, 1 );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		// Club A: 1 win (3pts) + 1 loss (0pts) = 3
		// Club B: 1 loss (0pts) + 1 win (3pts) = 3
		$this->assertEquals( 3, $result['a_points'] );
		$this->assertEquals( 3, $result['b_points'] );
	}

	public function test_h2h_goal_difference() {
		// Club A beats Club B 2-0 at home.
		$this->create_match( $this->club_a, $this->club_b, 2, 0 );
		// Club B beats Club A 1-0 at home.
		$this->create_match( $this->club_b, $this->club_a, 1, 0 );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		// Club A: scored 2+0=2, conceded 0+1=1 => GD = +1
		// Club B: scored 0+1=1, conceded 2+0=2 => GD = -1
		$this->assertEquals( 1, $result['a_gd'] );
		$this->assertEquals( -1, $result['b_gd'] );
	}

	// -----------------------------------------------------------------------
	// Sorting with H2H tiebreaker
	// -----------------------------------------------------------------------

	public function test_h2h_tiebreaker_ranks_winner_higher() {
		// A and B both beat C, so they are tied on overall pts.
		// A beats B 1-0 in H2H, so A should rank above B.
		// A: 2W 0L = 6pts (beat B 1-0, beat C 1-0)
		// B: 1W 1L = 3pts (lost to A 0-1, beat C 2-0)
		// C: 0W 2L = 0pts
		$this->create_match( $this->club_a, $this->club_b, 1, 0 );
		$this->create_match( $this->club_a, $this->club_c, 1, 0 );
		$this->create_match( $this->club_b, $this->club_c, 2, 0 );

		update_option( 'wpcm_standings_orderby', 'pts' );
		update_option( 'wpcm_standings_priority_order', 'DESC' );
		update_option( 'wpcm_standings_orderby_2', 'h2h' );
		update_option( 'wpcm_standings_priority_order_2', 'DESC' );
		update_option( 'wpcm_standings_orderby_3', 'gd' );
		update_option( 'wpcm_standings_priority_order_3', 'DESC' );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$clubs = $this->build_clubs_for_sort();
		usort( $clubs, 'wpcm_sort_table_clubs' );

		$this->assertEquals( $this->club_a, $clubs[0]->ID, 'Club A should be 1st (most pts)' );
		$this->assertEquals( $this->club_b, $clubs[1]->ID, 'Club B should be 2nd' );
		$this->assertEquals( $this->club_c, $clubs[2]->ID, 'Club C should be 3rd (0 pts)' );
	}

	public function test_h2h_falls_through_to_next_priority_when_tied() {
		// A and B draw 0-0 in their only H2H match.
		// A also beats C 3-0, B also beats C 1-0.
		// A: 1W 1D 0L = 4pts, F=3 A=0 GD=+3 (H2H: 1pt)
		// B: 1W 1D 0L = 4pts, F=1 A=0 GD=+1 (H2H: 1pt)
		$this->create_match( $this->club_a, $this->club_b, 0, 0 );
		$this->create_match( $this->club_a, $this->club_c, 3, 0 );
		$this->create_match( $this->club_b, $this->club_c, 1, 0 );

		update_option( 'wpcm_standings_orderby', 'pts' );
		update_option( 'wpcm_standings_priority_order', 'DESC' );
		update_option( 'wpcm_standings_orderby_2', 'h2h' );
		update_option( 'wpcm_standings_priority_order_2', 'DESC' );
		update_option( 'wpcm_standings_orderby_3', 'gd' );
		update_option( 'wpcm_standings_priority_order_3', 'DESC' );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$clubs = $this->build_clubs_for_sort();
		usort( $clubs, 'wpcm_sort_table_clubs' );

		// A and B tied on pts (4). H2H: drew 0-0, both 1pt, GD=0. Tied on H2H.
		// Falls through to priority 3 (GD): A has +3, B has +1. A ranks higher.
		$this->assertEquals( $this->club_a, $clubs[0]->ID );
		$this->assertEquals( $this->club_b, $clubs[1]->ID );
		$this->assertEquals( $this->club_c, $clubs[2]->ID );
	}

	public function test_h2h_excludes_friendly_matches() {
		// Friendly: A beats B 5-0 (should be ignored).
		$friendly = $this->create_match( $this->club_a, $this->club_b, 5, 0 );
		update_post_meta( $friendly, 'wpcm_friendly', '1' );

		// Competitive: B beats A 1-0.
		$this->create_match( $this->club_b, $this->club_a, 1, 0 );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		// Only the competitive match counts: B won, A lost.
		$this->assertEquals( 0, $result['a_points'] );
		$this->assertEquals( 3, $result['b_points'] );
	}

	public function test_h2h_excludes_postponed_without_walkover() {
		// Postponed match without walkover should be excluded.
		$postponed = $this->create_match( $this->club_a, $this->club_b, 0, 0 );
		update_post_meta( $postponed, '_wpcm_postponed', '1' );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		$this->assertEquals( 0, $result['a_points'] );
		$this->assertEquals( 0, $result['b_points'] );
	}

	public function test_h2h_includes_walkover_results() {
		// Postponed match with home_win walkover should count.
		$walkover = $this->create_match( $this->club_a, $this->club_b, 0, 0 );
		update_post_meta( $walkover, '_wpcm_postponed', '1' );
		update_post_meta( $walkover, '_wpcm_walkover', 'home_win' );

		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		$this->assertEquals( 3, $result['a_points'] );
		$this->assertEquals( 0, $result['b_points'] );
	}

	public function test_h2h_with_no_direct_matches_returns_zero() {
		wpcm_set_h2h_context( $this->comp_id, $this->season_id );

		$result = wpcm_get_h2h_points( $this->club_a, $this->club_b );

		$this->assertEquals( 0, $result['a_points'] );
		$this->assertEquals( 0, $result['b_points'] );
		$this->assertEquals( 0, $result['a_gd'] );
		$this->assertEquals( 0, $result['b_gd'] );
	}
}
