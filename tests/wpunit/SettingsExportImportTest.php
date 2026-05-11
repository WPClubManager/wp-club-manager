<?php
/**
 * Tests for settings export/import functionality.
 *
 * Verifies that WPCM settings can be exported to JSON and
 * imported back, preserving all option values.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/99
 */

class SettingsExportImportTest extends WPCMTestCase {

	/**
	 * Options to clean up after each test.
	 *
	 * @var array
	 */
	private $test_options = array();

	public function _setUp() {
		parent::_setUp();
		$this->test_options = array();
	}

	public function _tearDown() {
		foreach ( $this->test_options as $option ) {
			delete_option( $option );
		}
		parent::_tearDown();
	}

	/**
	 * Helper to set a test option and track it for cleanup.
	 */
	private function set_option( $name, $value ) {
		update_option( $name, $value );
		$this->test_options[] = $name;
	}

	// -------------------------------------------------------------------
	// Export
	// -------------------------------------------------------------------

	public function test_export_returns_array_of_wpcm_options() {
		$this->set_option( 'wpcm_sport', 'soccer' );
		$this->set_option( 'wpcm_mode', 'club' );

		$exported = WPCM_Settings_Tools::get_export_data();

		$this->assertIsArray( $exported );
		$this->assertArrayHasKey( 'wpcm_sport', $exported );
		$this->assertArrayHasKey( 'wpcm_mode', $exported );
		$this->assertEquals( 'soccer', $exported['wpcm_sport'] );
		$this->assertEquals( 'club', $exported['wpcm_mode'] );
	}

	public function test_export_includes_wpclubmanager_prefixed_options() {
		$this->set_option( 'wpclubmanager_player_slug', 'players' );

		$exported = WPCM_Settings_Tools::get_export_data();

		$this->assertArrayHasKey( 'wpclubmanager_player_slug', $exported );
		$this->assertEquals( 'players', $exported['wpclubmanager_player_slug'] );
	}

	public function test_export_excludes_internal_options() {
		$this->set_option( 'wpclubmanager_version', '2.3.3' );
		$this->set_option( 'wpclubmanager_installed', '1' );
		$this->set_option( 'wpcm_version_upgraded_from', '2.3.0' );
		$this->set_option( 'wpclubmanager_admin_footer_text_rated', '1' );

		$exported = WPCM_Settings_Tools::get_export_data();

		$this->assertArrayNotHasKey( 'wpclubmanager_version', $exported );
		$this->assertArrayNotHasKey( 'wpclubmanager_installed', $exported );
		$this->assertArrayNotHasKey( 'wpcm_version_upgraded_from', $exported );
		$this->assertArrayNotHasKey( 'wpclubmanager_admin_footer_text_rated', $exported );
	}

	public function test_export_excludes_non_wpcm_options() {
		$this->set_option( 'blogname', 'Test Site' );

		$exported = WPCM_Settings_Tools::get_export_data();

		$this->assertArrayNotHasKey( 'blogname', $exported );
	}

	public function test_export_handles_serialized_values() {
		$array_value = array( 'p', 'w', 'd', 'l', 'f', 'a', 'gd', 'pts' );
		$this->set_option( 'wpcm_standings_columns_display', $array_value );

		$exported = WPCM_Settings_Tools::get_export_data();

		$this->assertArrayHasKey( 'wpcm_standings_columns_display', $exported );
		$this->assertEquals( $array_value, $exported['wpcm_standings_columns_display'] );
	}

	// -------------------------------------------------------------------
	// Import
	// -------------------------------------------------------------------

	public function test_import_restores_options_from_valid_data() {
		$import_data = array(
			'wpcm_sport'           => 'rugby',
			'wpcm_mode'            => 'league',
			'wpcm_default_country' => 'GB',
		);

		$result = WPCM_Settings_Tools::import_settings( $import_data );

		$this->assertTrue( $result );
		$this->assertEquals( 'rugby', get_option( 'wpcm_sport' ) );
		$this->assertEquals( 'league', get_option( 'wpcm_mode' ) );
		$this->assertEquals( 'GB', get_option( 'wpcm_default_country' ) );

		$this->test_options = array_merge( $this->test_options, array_keys( $import_data ) );
	}

	public function test_import_rejects_non_wpcm_keys() {
		$import_data = array(
			'wpcm_sport'   => 'soccer',
			'blogname'     => 'Hacked Site',
			'admin_email'  => 'evil@example.com',
		);

		WPCM_Settings_Tools::import_settings( $import_data );

		$this->assertEquals( 'soccer', get_option( 'wpcm_sport' ) );
		$this->assertNotEquals( 'Hacked Site', get_option( 'blogname' ) );
		$this->assertNotEquals( 'evil@example.com', get_option( 'admin_email' ) );

		$this->test_options[] = 'wpcm_sport';
	}

	public function test_import_rejects_internal_options() {
		$original_version = get_option( 'wpclubmanager_version' );

		$import_data = array(
			'wpclubmanager_version'   => '9.9.9',
			'wpclubmanager_installed' => '0',
		);

		WPCM_Settings_Tools::import_settings( $import_data );

		$this->assertEquals( $original_version, get_option( 'wpclubmanager_version' ) );
	}

	public function test_import_handles_array_values() {
		$import_data = array(
			'wpcm_standings_columns_display' => array( 'p', 'w', 'd', 'l', 'pts' ),
		);

		$result = WPCM_Settings_Tools::import_settings( $import_data );

		$this->assertTrue( $result );
		$this->assertEquals( array( 'p', 'w', 'd', 'l', 'pts' ), get_option( 'wpcm_standings_columns_display' ) );

		$this->test_options[] = 'wpcm_standings_columns_display';
	}

	public function test_import_returns_false_for_empty_data() {
		$result = WPCM_Settings_Tools::import_settings( array() );

		$this->assertFalse( $result );
	}

	// -------------------------------------------------------------------
	// Round-trip
	// -------------------------------------------------------------------

	public function test_export_then_import_preserves_settings() {
		$this->set_option( 'wpcm_sport', 'hockey' );
		$this->set_option( 'wpcm_mode', 'club' );
		$this->set_option( 'wpcm_standings_win_points', '4' );
		$this->set_option( 'wpcm_map_select', 'osm' );

		$exported = WPCM_Settings_Tools::get_export_data();

		// Change settings.
		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_mode', 'league' );
		update_option( 'wpcm_standings_win_points', '3' );
		update_option( 'wpcm_map_select', 'google' );

		// Import the original export.
		WPCM_Settings_Tools::import_settings( $exported );

		$this->assertEquals( 'hockey', get_option( 'wpcm_sport' ) );
		$this->assertEquals( 'club', get_option( 'wpcm_mode' ) );
		$this->assertEquals( '4', get_option( 'wpcm_standings_win_points' ) );
		$this->assertEquals( 'osm', get_option( 'wpcm_map_select' ) );
	}

	// -------------------------------------------------------------------
	// JSON encoding
	// -------------------------------------------------------------------

	public function test_export_data_is_json_encodable() {
		$this->set_option( 'wpcm_sport', 'soccer' );
		$this->set_option( 'wpcm_mode', 'club' );

		$exported = WPCM_Settings_Tools::get_export_data();
		$json     = wp_json_encode( $exported );

		$this->assertNotFalse( $json );

		$decoded = json_decode( $json, true );
		$this->assertEquals( $exported, $decoded );
	}

	public function test_validate_import_json_rejects_invalid_json() {
		$result = WPCM_Settings_Tools::validate_import_json( 'not valid json{{{' );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	public function test_validate_import_json_rejects_non_object_json() {
		$result = WPCM_Settings_Tools::validate_import_json( '"just a string"' );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	public function test_validate_import_json_accepts_valid_settings() {
		$json = wp_json_encode( array( 'wpcm_sport' => 'soccer' ) );

		$result = WPCM_Settings_Tools::validate_import_json( $json );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'wpcm_sport', $result );
	}
}
