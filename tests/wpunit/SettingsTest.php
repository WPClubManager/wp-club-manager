<?php
/**
 * Tests for plugin settings and options.
 *
 * Verifies that default settings values work correctly, including
 * wpcm_mode, wpcm_sport, and standings point values.
 */

class SettingsTest extends WPCMTestCase {

	public function _setUp() {
		parent::_setUp();
	}

	public function _tearDown() {
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// wpcm_sport option
	// -----------------------------------------------------------------------

	public function test_wpcm_sport_option_can_be_set() {
		update_option( 'wpcm_sport', 'soccer' );
		$this->assertEquals( 'soccer', get_option( 'wpcm_sport' ) );
	}

	public function test_wpcm_sport_option_accepts_different_sports() {
		$sports = array( 'soccer', 'football', 'rugby', 'hockey', 'baseball', 'basketball', 'cricket', 'gaelic' );
		foreach ( $sports as $sport ) {
			update_option( 'wpcm_sport', $sport );
			$this->assertEquals( $sport, get_option( 'wpcm_sport' ), "Sport '{$sport}' should be stored" );
		}
	}

	// -----------------------------------------------------------------------
	// wpcm_mode option
	// -----------------------------------------------------------------------

	public function test_wpcm_mode_can_be_set_to_club() {
		update_option( 'wpcm_mode', 'club' );
		$this->assertEquals( 'club', get_option( 'wpcm_mode' ) );
	}

	public function test_wpcm_mode_can_be_set_to_league() {
		update_option( 'wpcm_mode', 'league' );
		$this->assertEquals( 'league', get_option( 'wpcm_mode' ) );
	}

	// -----------------------------------------------------------------------
	// Default club
	// -----------------------------------------------------------------------

	public function test_get_default_club_returns_false_when_not_set() {
		delete_option( 'wpcm_default_club' );
		$this->assertFalse( get_default_club() );
	}

	public function test_get_default_club_returns_club_id_when_set() {
		$club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Default Club',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', $club_id );
		$this->assertEquals( $club_id, get_default_club() );

		wp_delete_post( $club_id, true );
		delete_option( 'wpcm_default_club' );
	}

	// -----------------------------------------------------------------------
	// Match title format
	// -----------------------------------------------------------------------

	public function test_match_title_format_can_be_set() {
		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		$this->assertEquals( '%home% vs %away%', get_match_title_format() );
	}

	public function test_match_title_format_away_first() {
		update_option( 'wpcm_match_title_format', '%away% vs %home%' );
		$this->assertEquals( '%away% vs %home%', get_match_title_format() );
	}

	// -----------------------------------------------------------------------
	// Standings point values
	// -----------------------------------------------------------------------

	public function test_standings_win_points_default() {
		delete_option( 'wpcm_standings_win_points' );
		$this->assertEquals( 3, (int) get_option( 'wpcm_standings_win_points', 3 ) );
	}

	public function test_standings_draw_points_default() {
		delete_option( 'wpcm_standings_draw_points' );
		$this->assertEquals( 1, (int) get_option( 'wpcm_standings_draw_points', 1 ) );
	}

	public function test_standings_loss_points_default() {
		delete_option( 'wpcm_standings_loss_points' );
		$this->assertEquals( 0, (int) get_option( 'wpcm_standings_loss_points', 0 ) );
	}

	public function test_standings_custom_point_values() {
		update_option( 'wpcm_standings_win_points', 5 );
		update_option( 'wpcm_standings_draw_points', 2 );
		update_option( 'wpcm_standings_loss_points', 1 );

		$this->assertEquals( 5, (int) get_option( 'wpcm_standings_win_points' ) );
		$this->assertEquals( 2, (int) get_option( 'wpcm_standings_draw_points' ) );
		$this->assertEquals( 1, (int) get_option( 'wpcm_standings_loss_points' ) );
	}

	// -----------------------------------------------------------------------
	// Map settings
	// -----------------------------------------------------------------------

	public function test_map_select_option() {
		update_option( 'wpcm_map_select', 'osm' );
		$this->assertEquals( 'osm', get_option( 'wpcm_map_select' ) );
	}

	public function test_map_select_google() {
		update_option( 'wpcm_map_select', 'google' );
		$this->assertEquals( 'google', get_option( 'wpcm_map_select' ) );
	}

	// -----------------------------------------------------------------------
	// Plugin version
	// -----------------------------------------------------------------------

	public function test_wpcm_version_constant_matches_expected_format() {
		$this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', WPCM_VERSION );
	}

	// -----------------------------------------------------------------------
	// Permalink slugs
	// -----------------------------------------------------------------------

	public function test_player_slug_option_can_be_set() {
		update_option( 'wpclubmanager_player_slug', 'players' );
		$this->assertEquals( 'players', get_option( 'wpclubmanager_player_slug' ) );
	}

	public function test_match_slug_option_can_be_set() {
		update_option( 'wpclubmanager_match_slug', 'matches' );
		$this->assertEquals( 'matches', get_option( 'wpclubmanager_match_slug' ) );
	}
}
