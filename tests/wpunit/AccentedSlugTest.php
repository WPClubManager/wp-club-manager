<?php
/**
 * Tests for accented character handling in player, staff, and match slugs.
 *
 * Regression test for GitHub issue #81: accented characters (umlauts,
 * Hungarian characters, etc) must produce valid ASCII slugs via
 * sanitize_title() rather than sanitize_title_with_dashes().
 */

class AccentedSlugTest extends WPCMTestCase {

	/**
	 * The admin post types instance under test.
	 *
	 * @var WPCM_Admin_Post_Types
	 */
	private $admin_post_types;

	public function _setUp() {
		parent::_setUp();
		$this->admin_post_types = new WPCM_Admin_Post_Types();
	}

	// -------------------------------------------------------------------
	// Player slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_player_names
	 */
	public function test_player_slug_handles_accented_characters( $first, $last, $expected_slug ) {
		$_POST['_wpcm_firstname'] = $first;
		$_POST['_wpcm_lastname']  = $last;

		$data = array(
			'post_type'  => 'wpcm_player',
			'post_title' => '',
			'post_name'  => '',
		);

		$result = $this->admin_post_types->wp_insert_post_data( $data, array( 'ID' => 0 ) );

		$this->assertEquals( $expected_slug, $result['post_name'] );

		unset( $_POST['_wpcm_firstname'], $_POST['_wpcm_lastname'] );
	}

	public function accented_player_names() {
		return array(
			'hungarian'  => array( 'László', 'Balázs', 'laszlo-balazs' ),
			'german'     => array( 'Jörg', 'Müller', 'jorg-muller' ),
			'french'     => array( 'René', 'Côté', 'rene-cote' ),
			'czech'      => array( 'Tomáš', 'Dvořák', 'tomas-dvorak' ),
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
		$_POST['_wpcm_firstname'] = $first;
		$_POST['_wpcm_lastname']  = $last;

		$data = array(
			'post_type'  => 'wpcm_staff',
			'post_title' => '',
			'post_name'  => '',
		);

		$result = $this->admin_post_types->wp_insert_post_data( $data, array( 'ID' => 0 ) );

		$this->assertEquals( $expected_slug, $result['post_name'] );

		unset( $_POST['_wpcm_firstname'], $_POST['_wpcm_lastname'] );
	}

	public function accented_staff_names() {
		return array(
			'german_umlaut' => array( 'Jürgen', 'Klopp', 'jurgen-klopp' ),
			'spanish'       => array( 'José', 'García', 'jose-garcia' ),
		);
	}

	// -------------------------------------------------------------------
	// Player importer slug
	// -------------------------------------------------------------------

	public function test_player_import_slug_handles_accented_names() {
		$slug = sanitize_title( 'László Balázs' );
		$this->assertEquals( 'laszlo-balazs', $slug );
	}
}
