<?php
/**
 * Smoke tests to verify the plugin loads correctly.
 */

class PluginBootstrapTest extends WPCMTestCase {

	/**
	 * Test that the WPCM() function returns a WP_Club_Manager instance.
	 */
	public function test_wpcm_function_returns_instance() {
		$this->assertInstanceOf( 'WP_Club_Manager', WPCM() );
	}

	/**
	 * Test that the plugin version constant is defined.
	 */
	public function test_version_constant_is_defined() {
		$this->assertTrue( defined( 'WPCM_VERSION' ) );
		$this->assertNotEmpty( WPCM_VERSION );
	}

	/**
	 * Test that core custom post types are registered.
	 */
	public function test_custom_post_types_registered() {
		$this->assertTrue( post_type_exists( 'wpcm_club' ) );
		$this->assertTrue( post_type_exists( 'wpcm_player' ) );
		$this->assertTrue( post_type_exists( 'wpcm_staff' ) );
		$this->assertTrue( post_type_exists( 'wpcm_match' ) );
	}

	/**
	 * Test that core taxonomies are registered.
	 */
	public function test_taxonomies_registered() {
		$this->assertTrue( taxonomy_exists( 'wpcm_comp' ) );
		$this->assertTrue( taxonomy_exists( 'wpcm_season' ) );
		$this->assertTrue( taxonomy_exists( 'wpcm_position' ) );
		$this->assertTrue( taxonomy_exists( 'wpcm_team' ) );
	}
}
