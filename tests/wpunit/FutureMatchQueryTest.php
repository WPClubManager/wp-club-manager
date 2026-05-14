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

	/** @var WP_Query|null */
	private $original_query;

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
		if ( $this->original_query ) {
			$GLOBALS['wp_the_query'] = $this->original_query;
			$this->original_query    = null;
		}

		if ( $this->match_id ) {
			wp_delete_post( $this->match_id, true );
		}
		parent::_tearDown();
	}

	/**
	 * Helper: make a WP_Query instance appear as the main query.
	 *
	 * @param WP_Query $query The query to promote.
	 */
	private function make_main_query( $query ) {
		$this->original_query    = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$GLOBALS['wp_the_query'] = $query;
	}

	/**
	 * A future match should be queryable when post_status includes 'future'.
	 * This verifies the post was created correctly and is findable.
	 */
	public function test_future_match_is_queryable_with_explicit_status() {
		$match = get_post( $this->match_id );
		$this->assertNotEmpty( $match, 'Match post should exist' );
		$this->assertEquals( 'future', $match->post_status, 'Match should have future status' );

		$query = new WP_Query( array(
			'post_type'   => 'wpcm_match',
			'name'        => $match->post_name,
			'post_status' => array( 'publish', 'future' ),
		) );

		$this->assertGreaterThan( 0, $query->post_count, 'Future match should be found when post_status includes future' );
		$this->assertEquals( $this->match_id, $query->posts[0]->ID );
	}

	/**
	 * The pre_get_posts action should include future status in query
	 * when the wpcm_match query var is set on a main query.
	 */
	public function test_pre_get_posts_adds_future_status_for_match() {
		$query = new WP_Query();
		$query->set( 'post_type', 'wpcm_match' );
		$query->set( 'wpcm_match', 'some-slug' );
		$query->is_singular = true;

		$this->make_main_query( $query );

		// Fire the pre_get_posts action to trigger the hook.
		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$status = $query->get( 'post_status' );
		$this->assertIsArray( $status );
		$this->assertContains( 'publish', $status );
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

		$this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$status = $query->get( 'post_status' );
		$this->assertIsArray( $status );
		$this->assertContains( 'publish', $status );
		$this->assertContains( 'future', $status );
	}

	/**
	 * The action should not affect non-match queries.
	 */
	public function test_pre_get_posts_does_not_affect_other_post_types() {
		$query = new WP_Query();
		$query->set( 'post_type', 'post' );
		$query->is_singular = true;

		$this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

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

		$this->make_main_query( $query );

		do_action_ref_array( 'pre_get_posts', array( &$query ) );

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

		// Do NOT make this the main query - leave $wp_the_query alone.
		do_action_ref_array( 'pre_get_posts', array( &$query ) );

		$status = $query->get( 'post_status' );
		$this->assertEmpty( $status );
	}
}
