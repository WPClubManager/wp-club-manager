<?php
/**
 * Tests for player creation and profile meta.
 *
 * Verifies that a wpcm_player post can be created with all profile
 * meta fields (dob, height, weight, nationality, etc) and that they
 * persist correctly.
 */

class PlayerCreationTest extends WPCMTestCase {

	/** @var int */
	private $player_id;

	public function _setUp() {
		parent::_setUp();

		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'John Smith',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->player_id, true );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Creation
	// -----------------------------------------------------------------------

	public function test_can_create_player_post() {
		$this->assertGreaterThan( 0, $this->player_id );
		$this->assertEquals( 'wpcm_player', get_post_type( $this->player_id ) );
	}

	public function test_player_title_is_saved() {
		$this->assertEquals( 'John Smith', get_the_title( $this->player_id ) );
	}

	// -----------------------------------------------------------------------
	// Profile meta fields
	// -----------------------------------------------------------------------

	public function test_player_dob_meta() {
		update_post_meta( $this->player_id, 'wpcm_dob', '1995-06-15' );
		$this->assertEquals( '1995-06-15', get_post_meta( $this->player_id, 'wpcm_dob', true ) );
	}

	public function test_player_height_meta() {
		update_post_meta( $this->player_id, 'wpcm_height', '185' );
		$this->assertEquals( '185', get_post_meta( $this->player_id, 'wpcm_height', true ) );
	}

	public function test_player_weight_meta() {
		update_post_meta( $this->player_id, 'wpcm_weight', '80' );
		$this->assertEquals( '80', get_post_meta( $this->player_id, 'wpcm_weight', true ) );
	}

	public function test_player_nationality_meta() {
		update_post_meta( $this->player_id, 'wpcm_natl', 'gb' );
		$this->assertEquals( 'gb', get_post_meta( $this->player_id, 'wpcm_natl', true ) );
	}

	public function test_player_number_meta() {
		update_post_meta( $this->player_id, 'wpcm_number', '10' );
		$this->assertEquals( '10', get_post_meta( $this->player_id, 'wpcm_number', true ) );
	}

	public function test_player_hometown_meta() {
		update_post_meta( $this->player_id, 'wpcm_hometown', 'Manchester' );
		$this->assertEquals( 'Manchester', get_post_meta( $this->player_id, 'wpcm_hometown', true ) );
	}

	public function test_player_firstname_meta() {
		update_post_meta( $this->player_id, '_wpcm_firstname', 'John' );
		$this->assertEquals( 'John', get_post_meta( $this->player_id, '_wpcm_firstname', true ) );
	}

	public function test_player_lastname_meta() {
		update_post_meta( $this->player_id, '_wpcm_lastname', 'Smith' );
		$this->assertEquals( 'Smith', get_post_meta( $this->player_id, '_wpcm_lastname', true ) );
	}

	// -----------------------------------------------------------------------
	// get_player_title() helper
	// -----------------------------------------------------------------------

	public function test_get_player_title_full_format() {
		$title = get_player_title( $this->player_id, 'full' );
		$this->assertStringContainsString( 'John', $title );
		$this->assertStringContainsString( 'Smith', $title );
	}

	public function test_get_player_title_first_format() {
		$title = get_player_title( $this->player_id, 'first' );
		$this->assertStringContainsString( 'John', $title );
		$this->assertStringNotContainsString( 'Smith', $title );
	}

	public function test_get_player_title_last_format() {
		$title = get_player_title( $this->player_id, 'last' );
		$this->assertStringContainsString( 'Smith', $title );
	}

	public function test_get_player_title_initial_format() {
		$title = get_player_title( $this->player_id, 'initial' );
		$this->assertStringContainsString( 'J.', $title );
		$this->assertStringContainsString( 'Smith', $title );
	}

	public function test_get_player_title_uses_custom_first_last_names() {
		update_post_meta( $this->player_id, '_wpcm_firstname', 'Jonny' );
		update_post_meta( $this->player_id, '_wpcm_lastname', 'Smyth' );

		$title = get_player_title( $this->player_id, 'full' );

		$this->assertStringContainsString( 'Jonny', $title );
		$this->assertStringContainsString( 'Smyth', $title );
	}

	// -----------------------------------------------------------------------
	// Season taxonomy
	// -----------------------------------------------------------------------

	public function test_player_can_be_assigned_to_season() {
		$term = wp_insert_term( '2025/26 Season', 'wpcm_season' );
		if ( is_wp_error( $term ) ) {
			$term_id = $term->get_error_data();
		} else {
			$term_id = $term['term_id'];
		}

		wp_set_object_terms( $this->player_id, $term_id, 'wpcm_season' );
		$terms = wp_get_object_terms( $this->player_id, 'wpcm_season' );

		$this->assertCount( 1, $terms );
	}
}
