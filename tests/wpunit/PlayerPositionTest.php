<?php
/**
 * Tests for player position taxonomy assignment and retrieval.
 *
 * Verifies that the wpcm_position taxonomy can be assigned to players
 * and that the wpcm_get_player_positions() helper returns the correct
 * comma-separated string.
 */

class PlayerPositionTest extends WPCMTestCase {

	/** @var int */
	private $player_id;

	public function _setUp() {
		parent::_setUp();

		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Position Test Player',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->player_id, true );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Taxonomy assignment
	// -----------------------------------------------------------------------

	public function test_position_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'wpcm_position' ) );
	}

	public function test_can_assign_single_position() {
		$term = wp_insert_term( 'Goalkeeper', 'wpcm_position' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->player_id, $term_id, 'wpcm_position' );
		$terms = wp_get_object_terms( $this->player_id, 'wpcm_position' );

		$this->assertCount( 1, $terms );
		$this->assertEquals( 'Goalkeeper', $terms[0]->name );
	}

	public function test_can_assign_multiple_positions() {
		$term1 = wp_insert_term( 'Midfielder', 'wpcm_position' );
		$term2 = wp_insert_term( 'Forward', 'wpcm_position' );

		$ids = array();
		foreach ( array( $term1, $term2 ) as $term ) {
			if ( is_wp_error( $term ) ) {
				$ids[] = $term->get_error_data();
			} else {
				$ids[] = $term['term_id'];
			}
		}

		wp_set_object_terms( $this->player_id, $ids, 'wpcm_position' );
		$terms = wp_get_object_terms( $this->player_id, 'wpcm_position' );

		$this->assertCount( 2, $terms );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_positions()
	// -----------------------------------------------------------------------

	public function test_get_player_positions_returns_single_name() {
		$term = wp_insert_term( 'Defender PosTest', 'wpcm_position' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->player_id, $term_id, 'wpcm_position' );

		$positions = wpcm_get_player_positions( $this->player_id );

		$this->assertEquals( 'Defender PosTest', $positions );
	}

	public function test_get_player_positions_returns_comma_separated_for_multiple() {
		$t1 = wp_insert_term( 'Midfielder PosTest', 'wpcm_position' );
		$t2 = wp_insert_term( 'Forward PosTest', 'wpcm_position' );

		$ids = array();
		foreach ( array( $t1, $t2 ) as $term ) {
			if ( is_wp_error( $term ) ) {
				$ids[] = $term->get_error_data();
			} else {
				$ids[] = $term['term_id'];
			}
		}

		wp_set_object_terms( $this->player_id, $ids, 'wpcm_position' );

		$positions = wpcm_get_player_positions( $this->player_id );

		$this->assertStringContainsString( ',', $positions );
		$this->assertStringContainsString( 'Midfielder PosTest', $positions );
		$this->assertStringContainsString( 'Forward PosTest', $positions );
	}

	public function test_get_player_positions_with_no_positions_returns_none() {
		// No positions assigned — the function checks is_array on empty terms.
		// wp_get_object_terms returns empty array, which is_array returns true for.
		// With empty array, implode returns empty string.
		$positions = wpcm_get_player_positions( $this->player_id );

		// Empty array from wp_get_object_terms is still is_array = true,
		// so we get empty string from implode.
		$this->assertEmpty( $positions );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_teams()
	// -----------------------------------------------------------------------

	public function test_get_player_teams_returns_false_when_no_teams() {
		// wpcm_team may not be registered in league mode.
		// If it exists and no terms assigned, returns empty string.
		if ( taxonomy_exists( 'wpcm_team' ) ) {
			$teams = wpcm_get_player_teams( $this->player_id );
			$this->assertEmpty( $teams );
		} else {
			$this->markTestSkipped( 'wpcm_team taxonomy not registered (league mode).' );
		}
	}
}
