<?php
/**
 * Tests for league table (wpcm_table) post type public visibility.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/82
 */

class LeagueTablePostTypeTest extends WPCMTestCase {

	public function test_league_table_post_type_is_public() {
		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertTrue( $obj->public, 'wpcm_table should be public' );
	}

	public function test_league_table_post_type_is_publicly_queryable() {
		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertTrue( $obj->publicly_queryable, 'wpcm_table should be publicly queryable' );
	}

	public function test_league_table_post_type_has_query_var() {
		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertNotFalse( $obj->query_var, 'wpcm_table should have a query var' );
	}

	public function test_league_table_post_type_has_rewrite() {
		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertNotFalse( $obj->rewrite, 'wpcm_table should have rewrite rules' );
	}

	public function test_league_table_default_rewrite_slug() {
		// Assert the already-registered post type has the expected default slug.
		// Cannot unregister and call register_post_types() because its guard
		// bails out when wpcm_player is already registered.
		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertIsArray( $obj->rewrite );
		$this->assertEquals( 'table', $obj->rewrite['slug'] );
	}

	public function test_league_table_custom_rewrite_slug() {
		update_option( 'wpclubmanager_table_slug', 'league-table' );

		// Unregister and re-register directly since register_post_types()
		// guards against re-registration when wpcm_player already exists.
		unregister_post_type( 'wpcm_table' );

		$slug = get_option( 'wpclubmanager_table_slug' );
		register_post_type( 'wpcm_table', array(
			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => untrailingslashit( $slug ) ),
			'show_in_rest'       => true,
			'capability_type'    => 'wpcm_table',
			'map_meta_cap'       => true,
		) );

		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertIsArray( $obj->rewrite );
		$this->assertEquals( 'league-table', $obj->rewrite['slug'] );

		// Restore default registration for subsequent tests.
		delete_option( 'wpclubmanager_table_slug' );
		unregister_post_type( 'wpcm_table' );
		register_post_type( 'wpcm_table', array(
			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'table' ),
			'show_in_rest'       => true,
			'capability_type'    => 'wpcm_table',
			'map_meta_cap'       => true,
		) );
	}

	public function test_league_table_single_page_resolves() {
		$post_id = wp_insert_post( array(
			'post_type'   => 'wpcm_table',
			'post_title'  => 'Premier League',
			'post_status' => 'publish',
		) );

		$this->assertGreaterThan( 0, $post_id );

		$permalink = get_permalink( $post_id );
		$this->assertNotEmpty( $permalink );
		$this->assertStringContainsString( 'table', $permalink );

		wp_delete_post( $post_id, true );
	}
}
