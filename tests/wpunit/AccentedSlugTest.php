<?php
/**
 * Tests for accented character handling in player, staff, and match slugs.
 *
 * Regression test for GitHub issue #81: accented characters (umlauts,
 * Hungarian characters, etc) must produce valid ASCII slugs via
 * sanitize_title() rather than sanitize_title_with_dashes().
 *
 * WPCM_Admin_Post_Types::wp_insert_post_data() uses filter_input(INPUT_POST, ...)
 * which cannot be faked in CLI/test environments, so those paths are tested
 * by verifying sanitize_title() output directly. The importer integration
 * tests mirror the importer code path and insert real posts to verify slugs.
 */

class AccentedSlugTest extends WPCMTestCase {

	/**
	 * Original separator option value, saved/restored around tests.
	 *
	 * @var mixed
	 */
	private $original_separator;

	public function _setUp() {
		parent::_setUp();
		$this->original_separator = get_option( 'wpcm_match_clubs_separator' );
	}

	public function _tearDown() {
		if ( false === $this->original_separator ) {
			delete_option( 'wpcm_match_clubs_separator' );
		} else {
			update_option( 'wpcm_match_clubs_separator', $this->original_separator );
		}
		parent::_tearDown();
	}

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
		update_option( 'wpcm_match_clubs_separator', 'v' );
		$separator = get_option( 'wpcm_match_clubs_separator' );
		$title     = $match_id . '-' . $home . ' ' . $separator . ' ' . $away;
		$slug      = sanitize_title( $title );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_match_titles() {
		return array(
			'accented_clubs' => array( 1, 'München FC', 'Zürich SC', '1-munchen-fc-v-zurich-sc' ),
			'ascii_clubs'    => array( 2, 'Arsenal', 'Chelsea', '2-arsenal-v-chelsea' ),
		);
	}

	// -------------------------------------------------------------------
	// Player importer integration — mirrors the exact code path in
	// class-wpcm-player-importer.php and inserts a real post.
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_import_names
	 */
	public function test_player_import_creates_correct_slug( $first, $last, $expected_slug ) {
		$first_name = sanitize_text_field( $first );
		$last_name  = sanitize_text_field( $last );
		$name       = trim( $first_name . ' ' . $last_name );
		$post_name  = sanitize_title( $name );

		$id = wp_insert_post(
			array(
				'post_type'   => 'wpcm_player',
				'post_status' => 'publish',
				'post_title'  => $name,
				'post_name'   => $post_name,
			)
		);

		$post = get_post( $id );
		$this->assertEquals( $expected_slug, $post->post_name );
	}

	public function accented_import_names() {
		return array(
			'hungarian' => array( 'László', 'Balázs', 'laszlo-balazs' ),
			'german'    => array( 'Jörg', 'Müller', 'jorg-muller' ),
			'ascii'     => array( 'John', 'Smith', 'john-smith' ),
		);
	}

	// -------------------------------------------------------------------
	// Match importer integration — mirrors the exact code path in
	// class-wpcm-match-importer.php and verifies the slug via wp_insert_post.
	// -------------------------------------------------------------------

	public function test_match_import_creates_correct_slug() {
		update_option( 'wpcm_match_clubs_separator', 'v' );
		$separator = get_option( 'wpcm_match_clubs_separator' );
		$id_prefix = 99;
		$home      = 'München FC';
		$away      = 'Zürich SC';
		$title     = $id_prefix . '-' . $home . '-' . $separator . '-' . $away;
		$slug      = sanitize_title( $title );

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'wpcm_match',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_name'   => $slug,
			)
		);

		$post = get_post( $post_id );
		$this->assertEquals( '99-munchen-fc-v-zurich-sc', $post->post_name );
	}
}
