<?php
/**
 * Tests for Gutenberg block registration.
 *
 * Verifies all WPCM shortcodes have corresponding Gutenberg blocks
 * with render callbacks that produce output.
 */

class BlockRegistrationTest extends WPCMTestCase {

	/** @dataProvider block_names */
	public function test_block_is_registered( $block_name ) {
		$registry = WP_Block_Type_Registry::get_instance();
		$this->assertTrue(
			$registry->is_registered( $block_name ),
			"Block {$block_name} should be registered"
		);
	}

	/** @dataProvider block_names */
	public function test_block_has_callable_render_callback( $block_name ) {
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
		$this->assertNotNull( $block_type, "Block {$block_name} should exist" );
		$this->assertTrue(
			is_callable( $block_type->render_callback ),
			"Block {$block_name} should have a callable render callback"
		);
	}

	/** @dataProvider block_names */
	public function test_block_render_returns_string( $block_name ) {
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
		$this->assertNotNull( $block_type, "Block {$block_name} should be registered before testing render" );

		// Suppress warnings from shortcodes when rendered with empty data.
		// The underlying shortcodes may trigger notices when no posts/terms exist.
		$previous = error_reporting( error_reporting() & ~E_WARNING & ~E_NOTICE ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_value_error_reporting,WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
		try {
			$output = call_user_func( $block_type->render_callback, array(), '', null );
		} finally {
			error_reporting( $previous ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_value_error_reporting,WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
		}

		$this->assertIsString( $output, "Block {$block_name} render should return a string" );
	}

	public function block_names() {
		return array(
			array( 'wpcm/match-list' ),
			array( 'wpcm/player-list' ),
			array( 'wpcm/player-gallery' ),
			array( 'wpcm/staff-list' ),
			array( 'wpcm/staff-gallery' ),
			array( 'wpcm/league-table' ),
			array( 'wpcm/map-venue' ),
		);
	}

	public function test_match_opponents_block_registered_in_club_mode() {
		if ( ! function_exists( 'is_club_mode' ) || ! is_club_mode() ) {
			$this->markTestSkipped( 'match-opponents block requires club mode' );
		}
		$registry = WP_Block_Type_Registry::get_instance();
		$this->assertTrue( $registry->is_registered( 'wpcm/match-opponents' ) );
	}

	public function test_block_category_is_registered() {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'post',
				'post_title'  => 'Block Category Test',
				'post_status' => 'draft',
			)
		);

		$post       = get_post( $post_id );
		$filter     = has_filter( 'block_categories_all', array( 'WPCM_Blocks', 'register_category' ) ) ? 'block_categories_all' : 'block_categories';
		$categories = apply_filters( $filter, array(), $post );
		$slugs      = wp_list_pluck( $categories, 'slug' );
		$this->assertContains( 'wp-club-manager', $slugs );

		wp_delete_post( $post_id, true );
	}

	/** @dataProvider block_names */
	public function test_block_has_editor_script( $block_name ) {
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
		$this->assertNotNull( $block_type );
		$this->assertContains( 'wpcm-blocks-editor', $block_type->editor_script_handles );
	}
}
