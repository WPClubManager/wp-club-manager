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
		delete_option( 'wpclubmanager_table_slug' );
		// Unregister and re-register to pick up default slug.
		unregister_post_type( 'wpcm_table' );
		WPCM_Post_Types::register_post_types();

		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertIsArray( $obj->rewrite );
		$this->assertEquals( 'table', $obj->rewrite['slug'] );
	}

	public function test_league_table_custom_rewrite_slug() {
		update_option( 'wpclubmanager_table_slug', 'league-table' );
		// Unregister and re-register to pick up custom slug.
		unregister_post_type( 'wpcm_table' );
		WPCM_Post_Types::register_post_types();

		$obj = get_post_type_object( 'wpcm_table' );
		$this->assertNotNull( $obj );
		$this->assertIsArray( $obj->rewrite );
		$this->assertEquals( 'league-table', $obj->rewrite['slug'] );

		delete_option( 'wpclubmanager_table_slug' );
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
