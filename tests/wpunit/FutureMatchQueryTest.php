<?php
/**
 * Tests for scheduled (future) match visibility on the frontend.
 *
 * Verifies that future-dated wpcm_match posts are included in
 * single-post queries instead of returning 404.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/94
 */

class FutureMatchQueryTest extends WPCMTestCase {

	/** @var int */
	private $match_id;

	public function _setUp() {
		parent::_setUp();

		// Create a future-dated match.
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'future',
			'post_date'   => gmdate( 'Y-m-d H:i:s', strtotime( '+7 days' ) ),
		) );
	}

	public function _tearDown() {
		if ( $this->match_id ) {
			wp_delete_post( $this->match_id, true );
		}
		parent::_tearDown();
	}

	/**
	 * Helper: make a WP_Query instance appear as the main query.
	 *
	 * @param WP_Query $query The query to promote.
	 * @return WP_Query The original main query (restore after test).
	 */
	private function make_main_query( $query ) {
		$original                = $GLOBALS['wp_the_query'];
		$GLOBALS['wp_the_query'] = $query;
		return $original;
	}

	/**
	 * A main-query for a single future match by CPT query var should return results
	 * because the pre_get_posts action adds 'future' to the post_status.
	 */
	public function test_future_match_query_returns_post() {
		$match = get_post( $this->match_id );

		$query    = new WP_Query();
		$original = $this->make_main_query( $query );

		$query->query( array(
			'wpcm_match' => $match->post_name,
			'post_type'  => 'wpcm_match',
		) );

		$GLOBALS['wp_the_query'] = $original;

		$this->assertGreaterThan( 0, $query->post_count, 'Future match should be found by slug query' );
		$this->assertEquals( $this->match_id, $query->posts[0]->ID );
	}

	/**
	 * The pre_get_posts action should include future status in query
	 * when the wpcm_match query var is set.
	 */
	public function test_pre_get_posts_adds_future_status_for_match() {
		$query = new WP_Query();
		$query->set( 'post_type', 'wpcm_match' );
		$query->set( 'wpcm_match', 'some-slug' );
		$query->is_singular = true;

		$original = $this->make_main_query( $query );

		// Fire the pre_get_posts action to trigger the hook.
		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$GLOBALS['wp_the_query'] = $original;

		$status = $query->get( 'post_status' );
		$this->assertIsArray( $status );
		$this->assertContains( 'future', $status );
	}

	/**
	 * The pre_get_posts action should include future status when
	 * post_type is set without the CPT query var.
	 */
	public function test_pre_get_posts_adds_future_status_for_match_by_post_type() {
		$query = new WP_Query();
		$query->set( 'post_type', 'wpcm_match' );
		$query->is_singular = true;

		$original = $this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$GLOBALS['wp_the_query'] = $original;

		$status = $query->get( 'post_status' );
		$this->assertIsArray( $status );
		$this->assertContains( 'future', $status );
	}

	/**
	 * The action should not affect non-match queries.
	 */
	public function test_pre_get_posts_does_not_affect_other_post_types() {
		$query = new WP_Query();
		$query->set( 'post_type', 'post' );
		$query->is_singular = true;

		$original = $this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$GLOBALS['wp_the_query'] = $original;

		$status = $query->get( 'post_status' );
		// Should remain unchanged (empty string = default).
		$this->assertEmpty( $status );
	}

	/**
	 * Existing post_status should be preserved when future is appended.
	 */
	public function test_preserves_existing_post_status() {
		$query = new WP_Query();
		$query->set( 'post_type', 'wpcm_match' );
		$query->set( 'post_status', 'draft' );
		$query->is_singular = true;

		$original = $this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$GLOBALS['wp_the_query'] = $original;

		$status = $query->get( 'post_status' );
		$this->assertIsArray( $status );
		$this->assertContains( 'draft', $status );
		$this->assertContains( 'future', $status );
	}

	/**
	 * The action should not modify secondary queries.
	 */
	public function test_does_not_modify_secondary_queries() {
		$query = new WP_Query();
		$query->set( 'post_type', 'wpcm_match' );
		$query->is_singular = true;

		// Do NOT make this the main query — leave $wp_the_query alone.
		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$status = $query->get( 'post_status' );
		$this->assertEmpty( $status );
	}
}
