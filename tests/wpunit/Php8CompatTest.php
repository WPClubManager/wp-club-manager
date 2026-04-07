<?php
/**
 * Tests for PHP 8.x compatibility fixes (#91).
 *
 * Covers null property access, uninitialized array variables,
 * and false-to-array implicit conversion.
 */

class Php8CompatTest extends WPCMTestCase {

	public function _setUp() {
		parent::_setUp();
		update_option( 'wpcm_sport', 'soccer' );
	}

	public function _tearDown() {
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// get_current_season() — must not fatal when no seasons exist
	// -----------------------------------------------------------------------

	public function test_get_current_season_returns_empty_when_no_seasons() {
		// Stub get_terms() for wpcm_season to return an empty array,
		// avoiding state leakage from deleting real terms.
		$filter = function ( $terms, $query ) {
			if ( isset( $query->query_vars['taxonomy'] )
				&& in_array( 'wpcm_season', (array) $query->query_vars['taxonomy'], true )
			) {
				return array();
			}

			return $terms;
		};
		add_filter( 'terms_pre_query', $filter, 10, 2 );

		$result = get_current_season();

		remove_filter( 'terms_pre_query', $filter, 10 );

		$this->assertIsArray( $result );
		$this->assertNull( $result['id'] );
		$this->assertNull( $result['name'] );
		$this->assertNull( $result['slug'] );
	}

	public function test_get_current_season_returns_data_when_season_exists() {
		$season_name = 'Season 2024 ' . uniqid();
		$term        = wp_insert_term(
			$season_name,
			'wpcm_season',
			array(
				'slug' => sanitize_title( $season_name ),
			)
		);

		$this->assertFalse( is_wp_error( $term ), is_wp_error( $term ) ? $term->get_error_message() : '' );
		$this->assertIsArray( $term );

		$term_id = $term['term_id'];

		update_term_meta( $term_id, 'tax_position', 1 );

		// Stub get_terms() so only our created term is returned,
		// making the assertion deterministic regardless of other suite terms.
		$term_obj = get_term( $term_id, 'wpcm_season' );
		$filter   = function ( $terms, $query ) use ( $term_obj ) {
			if ( isset( $query->query_vars['taxonomy'] )
				&& in_array( 'wpcm_season', (array) $query->query_vars['taxonomy'], true )
			) {
				return array( $term_obj );
			}

			return $terms;
		};
		add_filter( 'terms_pre_query', $filter, 10, 2 );

		$result = get_current_season();

		remove_filter( 'terms_pre_query', $filter, 10 );

		$this->assertIsArray( $result );
		$this->assertEquals( $term_id, $result['id'] );
		$this->assertEquals( $season_name, $result['name'] );
		$this->assertNotEmpty( $result['slug'] );

		wp_delete_term( $term_id, 'wpcm_season' );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_preset_labels() — $output must be initialized as array
	// -----------------------------------------------------------------------

	public function test_wpcm_get_preset_labels_returns_array_not_undefined() {
		$result = wpcm_get_preset_labels( 'players', 'label' );
		$this->assertIsArray( $result );
	}

	public function test_wpcm_get_preset_labels_returns_empty_array_when_sport_missing() {
		delete_option( 'wpcm_sport' );

		$result = wpcm_get_preset_labels( 'players', 'label' );
		$this->assertIsArray( $result );
		$this->assertEmpty( $result );

		// Restore for subsequent tests.
		update_option( 'wpcm_sport', 'soccer' );
	}

	public function test_wpcm_get_preset_labels_returns_empty_array_when_sport_unknown() {
		update_option( 'wpcm_sport', 'nonexistent_sport' );

		$result = wpcm_get_preset_labels( 'players', 'label' );
		$this->assertIsArray( $result );
		$this->assertEmpty( $result );

		$standings = wpcm_get_preset_labels( 'standings', 'label' );
		$this->assertIsArray( $standings );
		$this->assertEmpty( $standings );

		// Restore for subsequent tests.
		update_option( 'wpcm_sport', 'soccer' );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_section_stats() — $output must be initialized as array
	// -----------------------------------------------------------------------

	public function test_wpcm_get_section_stats_returns_array() {
		// Use a section that likely has no stats to verify $output is initialized.
		$result = wpcm_get_section_stats( 'nonexistent_section' );
		$this->assertIsArray( $result );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_stats_labels() — $output = false then $output[$label]
	// -----------------------------------------------------------------------

	public function test_wpcm_get_player_stats_labels_no_fatal_when_no_stats_enabled() {
		// Disable all stats to force $output to remain false / empty.
		$labels = wpcm_get_preset_labels();
		foreach ( $labels as $label => $value ) {
			delete_option( 'wpcm_show_stats_' . $label );
		}

		$result = wpcm_get_player_stats_labels();
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'appearances', $result );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_all_labels() — same $output = false pattern
	// -----------------------------------------------------------------------

	public function test_wpcm_get_player_all_labels_no_fatal_when_no_stats_enabled() {
		$labels = wpcm_get_preset_labels();
		foreach ( $labels as $label => $value ) {
			delete_option( 'wpcm_show_stats_' . $label );
		}

		$result = wpcm_get_player_all_labels();
		$this->assertIsArray( $result );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_player_stats_names() — same $output = false pattern
	// -----------------------------------------------------------------------

	public function test_wpcm_get_player_stats_names_no_fatal_when_no_stats_enabled() {
		$labels = wpcm_get_preset_labels( 'players', 'name' );
		foreach ( $labels as $label => $value ) {
			delete_option( 'wpcm_show_stats_' . $label );
		}

		$result = wpcm_get_player_stats_names();
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'appearances', $result );
	}
}
