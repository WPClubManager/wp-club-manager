<?php
/**
 * Tests for key helper functions in the plugin.
 *
 * Covers wpcm_get_team_name, wpcm_nonce, wpcm_placeholder_img_src,
 * wpcm_divide, get_default_club, get_match_title_format, has_teams,
 * wpcm_rand_hash, wpcm_player_labels, wpcm_staff_labels, and
 * get_wpcm_stats_value.
 */

class CoreFunctionsTest extends WPCMTestCase {

	public function _setUp() {
		parent::_setUp();
		update_option( 'wpcm_sport', 'soccer' );
	}

	public function _tearDown() {
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// wpcm_placeholder_img_src()
	// -----------------------------------------------------------------------

	public function test_placeholder_img_src_returns_url() {
		$src = wpcm_placeholder_img_src();
		$this->assertStringContainsString( 'placeholder.png', $src );
	}

	public function test_placeholder_img_src_contains_plugin_url() {
		$src = wpcm_placeholder_img_src();
		$this->assertStringContainsString( 'wp-club-manager', $src );
	}

	// -----------------------------------------------------------------------
	// wpcm_crest_placeholder_img_src()
	// -----------------------------------------------------------------------

	public function test_crest_placeholder_img_src_returns_url() {
		$src = wpcm_crest_placeholder_img_src();
		$this->assertStringContainsString( 'crest-placeholder.png', $src );
	}

	// -----------------------------------------------------------------------
	// wpcm_divide()
	// -----------------------------------------------------------------------

	public function test_wpcm_divide_normal_division() {
		$this->assertEquals( 5, wpcm_divide( 10, 2 ) );
	}

	public function test_wpcm_divide_by_zero_returns_zero() {
		$this->assertEquals( 0, wpcm_divide( 10, 0 ) );
	}

	public function test_wpcm_divide_zero_numerator() {
		$this->assertEquals( 0, wpcm_divide( 0, 5 ) );
	}

	public function test_wpcm_divide_decimal_result() {
		$this->assertEquals( 2.5, wpcm_divide( 5, 2 ) );
	}

	// -----------------------------------------------------------------------
	// wpcm_rand_hash()
	// -----------------------------------------------------------------------

	public function test_wpcm_rand_hash_returns_string() {
		$hash = wpcm_rand_hash();
		$this->assertIsString( $hash );
	}

	public function test_wpcm_rand_hash_returns_40_char_hex() {
		$hash = wpcm_rand_hash();
		$this->assertEquals( 40, strlen( $hash ) );
		$this->assertMatchesRegularExpression( '/^[0-9a-f]{40}$/', $hash );
	}

	public function test_wpcm_rand_hash_is_unique() {
		$hash1 = wpcm_rand_hash();
		$hash2 = wpcm_rand_hash();
		$this->assertNotEquals( $hash1, $hash2 );
	}

	// -----------------------------------------------------------------------
	// wpcm_nonce() — outputs a nonce field
	// -----------------------------------------------------------------------

	public function test_wpcm_nonce_outputs_nonce_field() {
		ob_start();
		wpcm_nonce();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'wpclubmanager_meta_nonce', $output );
		$this->assertStringContainsString( '<input type="hidden"', $output );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_team_name()
	// -----------------------------------------------------------------------

	public function test_wpcm_get_team_name_returns_title_for_non_default_club() {
		$club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Other Club FC',
			'post_status' => 'publish',
		) );

		update_option( 'wpcm_default_club', 99999 ); // Not the club we're looking at.

		$name = wpcm_get_team_name( $club_id, 0 );
		$this->assertEquals( 'Other Club FC', $name );

		wp_delete_post( $club_id, true );
		delete_option( 'wpcm_default_club' );
	}

	// -----------------------------------------------------------------------
	// wpcm_player_labels()
	// -----------------------------------------------------------------------

	public function test_player_labels_returns_array() {
		$labels = wpcm_player_labels();
		$this->assertIsArray( $labels );
	}

	public function test_player_labels_has_expected_keys() {
		$labels = wpcm_player_labels();

		$expected_keys = array( 'number', 'thumb', 'name', 'flag', 'position', 'age', 'height', 'weight', 'dob', 'hometown', 'joined' );
		foreach ( $expected_keys as $key ) {
			$this->assertArrayHasKey( $key, $labels, "Player labels should contain '{$key}'" );
		}
	}

	// -----------------------------------------------------------------------
	// wpcm_staff_labels()
	// -----------------------------------------------------------------------

	public function test_staff_labels_returns_array() {
		$labels = wpcm_staff_labels();
		$this->assertIsArray( $labels );
	}

	public function test_staff_labels_has_expected_keys() {
		$labels = wpcm_staff_labels();

		$expected_keys = array( 'name', 'job', 'email', 'phone', 'age', 'joined' );
		foreach ( $expected_keys as $key ) {
			$this->assertArrayHasKey( $key, $labels, "Staff labels should contain '{$key}'" );
		}
	}

	// -----------------------------------------------------------------------
	// wpcm_get_preset_labels()
	// -----------------------------------------------------------------------

	public function test_get_preset_labels_returns_array_for_players() {
		$labels = wpcm_get_preset_labels( 'players', 'label' );
		$this->assertIsArray( $labels );
	}

	public function test_get_preset_labels_returns_array_for_standings() {
		$labels = wpcm_get_preset_labels( 'standings', 'label' );
		$this->assertIsArray( $labels );
	}

	public function test_get_preset_labels_soccer_has_goals() {
		$labels = wpcm_get_preset_labels( 'players', 'label' );
		$this->assertArrayHasKey( 'goals', $labels );
	}

	public function test_get_preset_labels_standings_has_points() {
		$labels = wpcm_get_preset_labels( 'standings', 'label' );
		$this->assertArrayHasKey( 'pts', $labels );
	}

	// -----------------------------------------------------------------------
	// get_wpcm_stats_value()
	// -----------------------------------------------------------------------

	public function test_get_wpcm_stats_value_returns_value_when_exists() {
		$stats = array(
			'manual' => array(
				'goals' => 5,
			),
		);
		$this->assertEquals( 5, get_wpcm_stats_value( $stats, 'manual', 'goals' ) );
	}

	public function test_get_wpcm_stats_value_returns_zero_when_type_missing() {
		$stats = array(
			'manual' => array(
				'goals' => 5,
			),
		);
		$this->assertEquals( 0, get_wpcm_stats_value( $stats, 'auto', 'goals' ) );
	}

	public function test_get_wpcm_stats_value_returns_zero_when_index_missing() {
		$stats = array(
			'manual' => array(
				'goals' => 5,
			),
		);
		$this->assertEquals( 0, get_wpcm_stats_value( $stats, 'manual', 'assists' ) );
	}

	public function test_get_wpcm_stats_value_returns_zero_for_empty_array() {
		$this->assertEquals( 0, get_wpcm_stats_value( array(), 'manual', 'goals' ) );
	}

	// -----------------------------------------------------------------------
	// wpcm_exclude_keys()
	// -----------------------------------------------------------------------

	public function test_exclude_keys_returns_array() {
		$keys = wpcm_exclude_keys();
		$this->assertIsArray( $keys );
		$this->assertContains( 'checked', $keys );
		$this->assertContains( 'sub', $keys );
	}

	// -----------------------------------------------------------------------
	// wpcm_stats_cards()
	// -----------------------------------------------------------------------

	public function test_stats_cards_returns_array() {
		$cards = wpcm_stats_cards();
		$this->assertIsArray( $cards );
		$this->assertContains( 'yellowcards', $cards );
		$this->assertContains( 'redcards', $cards );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_core_supported_themes()
	// -----------------------------------------------------------------------

	public function test_get_core_supported_themes_returns_array() {
		$themes = wpcm_get_core_supported_themes();
		$this->assertIsArray( $themes );
		$this->assertContains( 'twentytwenty', $themes );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_appearance_labels()
	// -----------------------------------------------------------------------

	public function test_get_appearance_labels_has_appearances_key() {
		$labels = wpcm_get_appearance_labels();
		$this->assertArrayHasKey( 'appearances', $labels );
	}

	public function test_get_appearance_and_subs_labels_has_subs_key() {
		$labels = wpcm_get_appearance_and_subs_labels();
		$this->assertArrayHasKey( 'subs', $labels );
		$this->assertArrayHasKey( 'appearances', $labels );
	}
}
