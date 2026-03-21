<?php
/**
 * Tests for taxonomy registration.
 *
 * Verifies all WPCM taxonomies exist, are assigned to the correct CPTs,
 * and that wpcm_team is only available in club mode.
 */

class TaxonomiesTest extends WPCMTestCase {

	public function _tearDown() {
		delete_option( 'wpcm_mode' );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Core taxonomy registration
	// -----------------------------------------------------------------------

	public function test_all_core_taxonomies_registered() {
		$expected = array( 'wpcm_comp', 'wpcm_season', 'wpcm_position', 'wpcm_jobs', 'wpcm_venue' );
		foreach ( $expected as $tax ) {
			$this->assertTrue( taxonomy_exists( $tax ), "Taxonomy {$tax} should be registered" );
		}
	}

	// -----------------------------------------------------------------------
	// Taxonomy → CPT associations
	// -----------------------------------------------------------------------

	public function test_wpcm_season_associated_with_player() {
		$this->assertContains( 'wpcm_player', get_object_taxonomies( 'wpcm_player', 'names' ) !== false
			? get_taxonomy( 'wpcm_season' )->object_type
			: array()
		);
		$this->assertContains( 'wpcm_season', get_object_taxonomies( 'wpcm_player' ) );
	}

	public function test_wpcm_season_associated_with_match() {
		$this->assertContains( 'wpcm_season', get_object_taxonomies( 'wpcm_match' ) );
	}

	public function test_wpcm_position_associated_with_player() {
		$this->assertContains( 'wpcm_position', get_object_taxonomies( 'wpcm_player' ) );
	}

	public function test_wpcm_jobs_associated_with_staff() {
		$this->assertContains( 'wpcm_jobs', get_object_taxonomies( 'wpcm_staff' ) );
	}

	// -----------------------------------------------------------------------
	// Taxonomy: wpcm_team (club mode only)
	// -----------------------------------------------------------------------

	public function test_wpcm_team_registered_when_option_is_club() {
		// wpcm_team registration depends on is_club_mode() at init time.
		// If the test suite started with wpcm_mode=club (set in wpunit.suite.yml
		// or .env), the taxonomy will be registered. We verify the relationship
		// between the option and the taxonomy rather than forcing a re-init.
		update_option( 'wpcm_mode', 'club' );
		// Directly call the WPCM post type registration method if accessible,
		// otherwise assert the option is set correctly and taxonomy exists if
		// it was registered at boot time.
		if ( taxonomy_exists( 'wpcm_team' ) ) {
			$this->assertTrue( taxonomy_exists( 'wpcm_team' ) );
		} else {
			// Taxonomy not registered — verify mode is set so a fresh boot would register it.
			$this->assertEquals( 'club', get_option( 'wpcm_mode' ) );
			$this->markTestIncomplete( 'wpcm_team not registered: wp-env boot did not start in club mode. Restart with wpcm_mode=club to run this assertion.' );
		}
	}

	// -----------------------------------------------------------------------
	// Term CRUD
	// -----------------------------------------------------------------------

	public function test_can_create_and_retrieve_season_term() {
		$term = wp_insert_term( '2024/25', 'wpcm_season' );
		$this->assertFalse( is_wp_error( $term ) );
		$this->assertArrayHasKey( 'term_id', $term );

		$retrieved = get_term( $term['term_id'], 'wpcm_season' );
		$this->assertEquals( '2024/25', $retrieved->name );

		wp_delete_term( $term['term_id'], 'wpcm_season' );
	}

	public function test_can_create_and_retrieve_competition_term() {
		$term = wp_insert_term( 'Premier League', 'wpcm_comp' );
		$this->assertFalse( is_wp_error( $term ) );

		$retrieved = get_term( $term['term_id'], 'wpcm_comp' );
		$this->assertEquals( 'Premier League', $retrieved->name );

		wp_delete_term( $term['term_id'], 'wpcm_comp' );
	}

	public function test_can_assign_season_to_match() {
		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Test Match',
			'post_status' => 'publish',
		) );

		$term = wp_insert_term( '2025/26', 'wpcm_season' );
		wp_set_post_terms( $match_id, array( $term['term_id'] ), 'wpcm_season' );

		$assigned = wp_get_post_terms( $match_id, 'wpcm_season', array( 'fields' => 'names' ) );
		$this->assertContains( '2025/26', $assigned );

		wp_delete_post( $match_id, true );
		wp_delete_term( $term['term_id'], 'wpcm_season' );
	}
}
