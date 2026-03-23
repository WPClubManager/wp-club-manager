<?php
/**
 * Tests for is_league_mode() and is_club_mode() conditional functions.
 *
 * Covers the wpcm_mode option logic including edge cases noted in the
 * codebase (loose == comparison on option values).
 */

class ModeTest extends WPCMTestCase {

	public function _setUp() {
		parent::_setUp();
		delete_option( 'wpcm_mode' );
	}

	public function _tearDown() {
		delete_option( 'wpcm_mode' );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// is_league_mode()
	// -----------------------------------------------------------------------

	public function test_is_league_mode_returns_true_when_set_to_league() {
		update_option( 'wpcm_mode', 'league' );
		$this->assertTrue( is_league_mode() );
	}

	public function test_is_league_mode_returns_false_when_set_to_club() {
		update_option( 'wpcm_mode', 'club' );
		$this->assertFalse( is_league_mode() );
	}

	public function test_is_league_mode_returns_false_when_option_absent() {
		$this->assertFalse( is_league_mode() );
	}

	public function test_is_league_mode_returns_false_for_unexpected_value() {
		update_option( 'wpcm_mode', 'unknown' );
		$this->assertFalse( is_league_mode() );
	}

	// -----------------------------------------------------------------------
	// is_club_mode()
	// -----------------------------------------------------------------------

	public function test_is_club_mode_returns_true_when_set_to_club() {
		update_option( 'wpcm_mode', 'club' );
		$this->assertTrue( is_club_mode() );
	}

	public function test_is_club_mode_returns_false_when_set_to_league() {
		update_option( 'wpcm_mode', 'league' );
		$this->assertFalse( is_club_mode() );
	}

	public function test_is_club_mode_returns_false_when_option_absent() {
		$this->assertFalse( is_club_mode() );
	}

	public function test_is_club_mode_returns_false_for_unexpected_value() {
		update_option( 'wpcm_mode', 'unknown' );
		$this->assertFalse( is_club_mode() );
	}

	// -----------------------------------------------------------------------
	// Mutual exclusivity
	// -----------------------------------------------------------------------

	public function test_league_and_club_modes_are_mutually_exclusive_in_league() {
		update_option( 'wpcm_mode', 'league' );
		$this->assertFalse( is_league_mode() && is_club_mode() );
	}

	public function test_league_and_club_modes_are_mutually_exclusive_in_club() {
		update_option( 'wpcm_mode', 'club' );
		$this->assertFalse( is_league_mode() && is_club_mode() );
	}

	public function test_neither_mode_active_when_option_absent() {
		$this->assertFalse( is_league_mode() );
		$this->assertFalse( is_club_mode() );
	}
}
