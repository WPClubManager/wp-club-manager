<?php
/**
 * Tests for accented character handling in player, staff, and match slugs.
 *
 * Regression test for GitHub issue #81: accented characters (umlauts,
 * Hungarian characters, etc) must produce valid ASCII slugs via
 * sanitize_title() rather than sanitize_title_with_dashes().
 *
 * The production code in WPCM_Admin_Post_Types::wp_insert_post_data() and
 * the CSV importers uses filter_input(INPUT_POST, ...) which cannot be
 * faked in CLI/test environments. These tests verify the slug generation
 * logic directly: sanitize_title() correctly calls remove_accents() before
 * generating the slug, while the old sanitize_title_with_dashes() did not.
 */

class AccentedSlugTest extends WPCMTestCase {

	// -------------------------------------------------------------------
	// Player slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_player_names
	 */
	public function test_player_slug_handles_accented_characters( $first, $last, $expected_slug ) {
		$name = $first . ' ' . $last;
		$slug = sanitize_title( $name );

		$this->assertEquals( $expected_slug, $slug );

		// Confirm the old function would NOT have produced the same result for accented names.
		if ( $name !== remove_accents( $name ) ) {
			$old_slug = sanitize_title_with_dashes( $name );
			$this->assertNotEquals( $expected_slug, $old_slug, 'sanitize_title_with_dashes should fail for accented characters' );
		}
	}

	public function accented_player_names() {
		return array(
			'hungarian'   => array( 'László', 'Balázs', 'laszlo-balazs' ),
			'german'      => array( 'Jörg', 'Müller', 'jorg-muller' ),
			'french'      => array( 'René', 'Côté', 'rene-cote' ),
			'czech'       => array( 'Tomáš', 'Dvořák', 'tomas-dvorak' ),
			'plain_ascii' => array( 'John', 'Smith', 'john-smith' ),
		);
	}

	// -------------------------------------------------------------------
	// Staff slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_staff_names
	 */
	public function test_staff_slug_handles_accented_characters( $first, $last, $expected_slug ) {
		$name = $first . ' ' . $last;
		$slug = sanitize_title( $name );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_staff_names() {
		return array(
			'german_umlaut' => array( 'Jürgen', 'Klopp', 'jurgen-klopp' ),
			'spanish'       => array( 'José', 'García', 'jose-garcia' ),
		);
	}

	// -------------------------------------------------------------------
	// Match slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_match_titles
	 */
	public function test_match_slug_handles_accented_club_names( $match_id, $home, $away, $expected_slug ) {
		$separator = get_option( 'wpcm_match_clubs_separator', 'vs' );
		$title     = $match_id . '-' . $home . ' ' . $separator . ' ' . $away;
		$slug      = sanitize_title( $title );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_match_titles() {
		return array(
			'accented_clubs' => array( 1, 'München FC', 'Zürich SC', '1-munchen-fc-vs-zurich-sc' ),
			'ascii_clubs'    => array( 2, 'Arsenal', 'Chelsea', '2-arsenal-vs-chelsea' ),
		);
	}

	// -------------------------------------------------------------------
	// Player importer slug — exercises the same pattern used in
	// class-wpcm-player-importer.php line 76.
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_import_names
	 */
	public function test_player_import_slug_handles_accented_names( $name, $expected_slug ) {
		// Mirrors the importer: sanitize_text_field() then sanitize_title().
		$sanitized_name = sanitize_text_field( $name );
		$slug           = sanitize_title( $sanitized_name );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_import_names() {
		return array(
			'hungarian' => array( 'László Balázs', 'laszlo-balazs' ),
			'german'    => array( 'Jörg Müller', 'jorg-muller' ),
			'ascii'     => array( 'John Smith', 'john-smith' ),
		);
	}

	// -------------------------------------------------------------------
	// Match importer slug — exercises the same pattern used in
	// class-wpcm-match-importer.php line 157.
	// -------------------------------------------------------------------

	public function test_match_import_slug_handles_accented_club_names() {
		$separator = get_option( 'wpcm_match_clubs_separator', 'vs' );
		$id        = 99;
		$home      = 'München FC';
		$away      = 'Zürich SC';
		$slug      = sanitize_title( $id . '-' . $home . '-' . $separator . '-' . $away );

		$this->assertEquals( '99-munchen-fc-vs-zurich-sc', $slug );
	}
}
