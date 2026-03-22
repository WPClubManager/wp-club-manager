<?php
/**
 * Tests for match creation and meta storage.
 *
 * Verifies that a wpcm_match post can be created with home/away club
 * meta values and that all meta is persisted correctly.
 */

class MatchCreationTest extends WPCMTestCase {

	/** @var int */
	private $home_club_id;

	/** @var int */
	private $away_club_id;

	/** @var int */
	private $match_id;

	public function _setUp() {
		parent::_setUp();

		$this->home_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Home FC',
			'post_status' => 'publish',
		) );

		$this->away_club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Away United',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		if ( $this->match_id ) {
			wp_delete_post( $this->match_id, true );
		}
		wp_delete_post( $this->home_club_id, true );
		wp_delete_post( $this->away_club_id, true );

		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Creation
	// -----------------------------------------------------------------------

	public function test_can_create_match_post() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		$this->assertGreaterThan( 0, $this->match_id );
		$this->assertEquals( 'wpcm_match', get_post_type( $this->match_id ) );
	}

	// -----------------------------------------------------------------------
	// Home/Away club meta
	// -----------------------------------------------------------------------

	public function test_home_club_meta_is_saved() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );

		$this->assertEquals(
			$this->home_club_id,
			(int) get_post_meta( $this->match_id, 'wpcm_home_club', true )
		);
	}

	public function test_away_club_meta_is_saved() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_away_club', $this->away_club_id );

		$this->assertEquals(
			$this->away_club_id,
			(int) get_post_meta( $this->match_id, 'wpcm_away_club', true )
		);
	}

	public function test_both_clubs_stored_on_same_match() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_away_club', $this->away_club_id );

		$this->assertEquals(
			$this->home_club_id,
			(int) get_post_meta( $this->match_id, 'wpcm_home_club', true )
		);
		$this->assertEquals(
			$this->away_club_id,
			(int) get_post_meta( $this->match_id, 'wpcm_away_club', true )
		);
	}

	// -----------------------------------------------------------------------
	// Competition and season taxonomy
	// -----------------------------------------------------------------------

	public function test_match_can_be_assigned_to_competition() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		$term = wp_insert_term( 'Premier League', 'wpcm_comp' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->match_id, $term_id, 'wpcm_comp' );
		$terms = wp_get_object_terms( $this->match_id, 'wpcm_comp' );

		$this->assertCount( 1, $terms );
		$this->assertEquals( 'Premier League', $terms[0]->name );
	}

	public function test_match_can_be_assigned_to_season() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		$term = wp_insert_term( '2025/26', 'wpcm_season' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->match_id, $term_id, 'wpcm_season' );
		$terms = wp_get_object_terms( $this->match_id, 'wpcm_season' );

		$this->assertCount( 1, $terms );
		$this->assertEquals( '2025/26', $terms[0]->name );
	}

	// -----------------------------------------------------------------------
	// Additional match meta
	// -----------------------------------------------------------------------

	public function test_match_friendly_meta_defaults_to_empty() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		$friendly = get_post_meta( $this->match_id, 'wpcm_friendly', true );
		$this->assertEmpty( $friendly );
	}

	public function test_match_neutral_meta_can_be_set() {
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->match_id, 'wpcm_neutral', '1' );
		$this->assertEquals( '1', get_post_meta( $this->match_id, 'wpcm_neutral', true ) );
	}
}
