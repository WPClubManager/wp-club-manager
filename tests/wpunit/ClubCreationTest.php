<?php
/**
 * Tests for club creation, meta, and taxonomy assignment.
 *
 * Verifies that a wpcm_club post can be created with its specific
 * meta fields and assigned to competitions and seasons.
 */

class ClubCreationTest extends WPCMTestCase {

	/** @var int */
	private $club_id;

	public function _setUp() {
		parent::_setUp();

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Test Athletic FC',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->club_id, true );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Creation
	// -----------------------------------------------------------------------

	public function test_can_create_club_post() {
		$this->assertGreaterThan( 0, $this->club_id );
		$this->assertEquals( 'wpcm_club', get_post_type( $this->club_id ) );
	}

	public function test_club_title_is_saved() {
		$this->assertEquals( 'Test Athletic FC', get_the_title( $this->club_id ) );
	}

	// -----------------------------------------------------------------------
	// Club meta fields
	// -----------------------------------------------------------------------

	public function test_club_abbreviation_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_abbr', 'TAF' );
		$this->assertEquals( 'TAF', get_post_meta( $this->club_id, '_wpcm_club_abbr', true ) );
	}

	public function test_club_formed_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_formed', '1892' );
		$this->assertEquals( '1892', get_post_meta( $this->club_id, '_wpcm_club_formed', true ) );
	}

	public function test_club_primary_color_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_primary_color', '#ff0000' );
		$this->assertEquals( '#ff0000', get_post_meta( $this->club_id, '_wpcm_club_primary_color', true ) );
	}

	public function test_club_secondary_color_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_secondary_color', '#ffffff' );
		$this->assertEquals( '#ffffff', get_post_meta( $this->club_id, '_wpcm_club_secondary_color', true ) );
	}

	public function test_club_website_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_website', 'https://example.com' );
		$this->assertEquals( 'https://example.com', get_post_meta( $this->club_id, '_wpcm_club_website', true ) );
	}

	public function test_club_honours_meta() {
		update_post_meta( $this->club_id, '_wpcm_club_honours', 'League Champions 2024' );
		$this->assertEquals( 'League Champions 2024', get_post_meta( $this->club_id, '_wpcm_club_honours', true ) );
	}

	// -----------------------------------------------------------------------
	// Competition taxonomy
	// -----------------------------------------------------------------------

	public function test_club_can_be_assigned_to_competition() {
		$term = wp_insert_term( 'Club Test League', 'wpcm_comp' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->club_id, $term_id, 'wpcm_comp' );
		$terms = wp_get_object_terms( $this->club_id, 'wpcm_comp' );

		$this->assertCount( 1, $terms );
		$this->assertEquals( 'Club Test League', $terms[0]->name );
	}

	public function test_club_can_be_assigned_to_multiple_competitions() {
		$t1 = wp_insert_term( 'Club League One', 'wpcm_comp' );
		$t2 = wp_insert_term( 'Club Cup', 'wpcm_comp' );

		$ids = array();
		foreach ( array( $t1, $t2 ) as $term ) {
			$ids[] = is_wp_error( $term ) ? $term->get_error_data() : $term['term_id'];
		}

		wp_set_object_terms( $this->club_id, $ids, 'wpcm_comp' );
		$terms = wp_get_object_terms( $this->club_id, 'wpcm_comp' );

		$this->assertCount( 2, $terms );
	}

	// -----------------------------------------------------------------------
	// Season taxonomy
	// -----------------------------------------------------------------------

	public function test_club_can_be_assigned_to_season() {
		$term = wp_insert_term( 'Club 2025/26', 'wpcm_season' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->club_id, $term_id, 'wpcm_season' );
		$terms = wp_get_object_terms( $this->club_id, 'wpcm_season' );

		$this->assertCount( 1, $terms );
	}

	// -----------------------------------------------------------------------
	// get_club_abbreviation()
	// -----------------------------------------------------------------------

	public function test_get_club_abbreviation_returns_stored_abbr() {
		update_post_meta( $this->club_id, '_wpcm_club_abbr', 'TAF' );

		$abbr = get_club_abbreviation( $this->club_id );
		$this->assertEquals( 'TAF', $abbr );
	}

	public function test_get_club_abbreviation_auto_generates_from_title() {
		// No abbreviation set — should take first 3 chars of title (no spaces).
		$abbr = get_club_abbreviation( $this->club_id );

		// Title is "Test Athletic FC", spaces removed = "TestAthleticFC", first 3 = "Tes" -> uppercase = "TES"
		$this->assertEquals( 'TES', $abbr );
	}

	// -----------------------------------------------------------------------
	// Venue taxonomy
	// -----------------------------------------------------------------------

	public function test_club_can_be_assigned_to_venue() {
		$term = wp_insert_term( 'Test Stadium', 'wpcm_venue' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->club_id, $term_id, 'wpcm_venue' );
		$terms = wp_get_object_terms( $this->club_id, 'wpcm_venue' );

		$this->assertCount( 1, $terms );
		$this->assertEquals( 'Test Stadium', $terms[0]->name );
	}
}
