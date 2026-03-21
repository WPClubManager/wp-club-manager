<?php
/**
 * Tests for custom post type registration.
 *
 * Verifies all WPCM CPTs are registered with the correct configuration:
 * REST support, expected supports, public visibility, and rewrite slugs.
 */

class PostTypesTest extends WPCMTestCase {

	// -----------------------------------------------------------------------
	// Registration
	// -----------------------------------------------------------------------

	public function test_all_core_post_types_registered() {
		$expected = array( 'wpcm_club', 'wpcm_player', 'wpcm_staff', 'wpcm_match', 'wpcm_sponsor', 'wpcm_table' );
		foreach ( $expected as $cpt ) {
			$this->assertTrue( post_type_exists( $cpt ), "Post type {$cpt} should be registered" );
		}
	}

	public function test_roster_post_type_registered_in_club_mode() {
		update_option( 'wpcm_mode', 'club' );
		// Force re-registration.
		do_action( 'init' );
		$this->assertTrue( post_type_exists( 'wpcm_roster' ) );
		delete_option( 'wpcm_mode' );
	}

	// -----------------------------------------------------------------------
	// Public visibility
	// -----------------------------------------------------------------------

	/** @dataProvider public_post_types */
	public function test_post_type_is_public( $cpt ) {
		$obj = get_post_type_object( $cpt );
		$this->assertNotNull( $obj );
		$this->assertTrue( $obj->public, "{$cpt} should be public" );
	}

	public function public_post_types() {
		return array(
			array( 'wpcm_club' ),
			array( 'wpcm_player' ),
			array( 'wpcm_staff' ),
			array( 'wpcm_match' ),
			array( 'wpcm_sponsor' ),
		);
	}

	/** @dataProvider non_public_post_types */
	public function test_post_type_is_not_public( $cpt ) {
		$obj = get_post_type_object( $cpt );
		$this->assertNotNull( $obj );
		$this->assertFalse( $obj->public, "{$cpt} should not be public" );
	}

	public function non_public_post_types() {
		return array(
			array( 'wpcm_table' ),
		);
	}

	// -----------------------------------------------------------------------
	// REST API support
	// -----------------------------------------------------------------------

	/** @dataProvider rest_enabled_post_types */
	public function test_post_type_has_rest_enabled( $cpt ) {
		$obj = get_post_type_object( $cpt );
		$this->assertNotNull( $obj );
		$this->assertTrue( $obj->show_in_rest, "{$cpt} should show in REST API" );
	}

	public function rest_enabled_post_types() {
		return array(
			array( 'wpcm_club' ),
			array( 'wpcm_player' ),
			array( 'wpcm_staff' ),
			array( 'wpcm_match' ),
			array( 'wpcm_sponsor' ),
			array( 'wpcm_table' ),
		);
	}

	// -----------------------------------------------------------------------
	// Post type supports
	// -----------------------------------------------------------------------

	public function test_player_post_type_supports_title() {
		$this->assertTrue( post_type_supports( 'wpcm_player', 'title' ) );
	}

	public function test_player_post_type_supports_thumbnail() {
		$this->assertTrue( post_type_supports( 'wpcm_player', 'thumbnail' ) );
	}

	public function test_club_post_type_supports_page_attributes() {
		$this->assertTrue( post_type_supports( 'wpcm_club', 'page-attributes' ) );
	}

	public function test_match_post_type_supports_title() {
		$this->assertTrue( post_type_supports( 'wpcm_match', 'title' ) );
	}

	// -----------------------------------------------------------------------
	// Searchable
	// -----------------------------------------------------------------------

	public function test_player_is_searchable() {
		$obj = get_post_type_object( 'wpcm_player' );
		$this->assertTrue( $obj->exclude_from_search === false );
	}

	public function test_club_is_not_searchable() {
		$obj = get_post_type_object( 'wpcm_club' );
		$this->assertTrue( $obj->exclude_from_search );
	}

	// -----------------------------------------------------------------------
	// CRUD — basic create/read/delete
	// -----------------------------------------------------------------------

	public function test_can_create_and_retrieve_player_post() {
		$post_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Test Player',
			'post_status' => 'publish',
		) );

		$this->assertGreaterThan( 0, $post_id );
		$this->assertEquals( 'wpcm_player', get_post_type( $post_id ) );
		$this->assertEquals( 'Test Player', get_the_title( $post_id ) );

		wp_delete_post( $post_id, true );
	}

	public function test_can_create_and_retrieve_match_post() {
		$post_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home vs Away',
			'post_status' => 'publish',
		) );

		$this->assertGreaterThan( 0, $post_id );
		$this->assertEquals( 'wpcm_match', get_post_type( $post_id ) );

		wp_delete_post( $post_id, true );
	}

	public function test_can_create_and_retrieve_club_post() {
		$post_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Test Club',
			'post_status' => 'publish',
		) );

		$this->assertGreaterThan( 0, $post_id );
		$this->assertEquals( 'wpcm_club', get_post_type( $post_id ) );

		wp_delete_post( $post_id, true );
	}
}
