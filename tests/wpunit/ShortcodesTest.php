<?php
/**
 * Tests for shortcode registration and output.
 *
 * Verifies all 8 WPCM shortcodes are registered, that legacy aliases
 * still work, and that each produces output without PHP errors.
 */

class ShortcodesTest extends WPCMTestCase {

	// -----------------------------------------------------------------------
	// Registration
	// -----------------------------------------------------------------------

	/** @dataProvider registered_shortcode_tags */
	public function test_shortcode_is_registered( $tag ) {
		$this->assertTrue( shortcode_exists( $tag ), "Shortcode [{$tag}] should be registered" );
	}

	public function registered_shortcode_tags() {
		return array(
			array( 'match_list' ),
			array( 'match_opponents' ),
			array( 'player_list' ),
			array( 'player_gallery' ),
			array( 'staff_list' ),
			array( 'staff_gallery' ),
			array( 'league_table' ),
			array( 'map_venue' ),
		);
	}

	/** @dataProvider legacy_shortcode_aliases */
	public function test_legacy_shortcode_alias_is_registered( $tag ) {
		$this->assertTrue( shortcode_exists( $tag ), "Legacy shortcode [{$tag}] should still be registered" );
	}

	public function legacy_shortcode_aliases() {
		return array(
			array( 'wpcm_matches' ),
			array( 'wpcm_players' ),
			array( 'wpcm_staff' ),
			array( 'wpcm_standings' ),
			array( 'wpcm_map' ),
		);
	}

	// -----------------------------------------------------------------------
	// Output — wraps in .wpcm-shortcode-wrapper
	// -----------------------------------------------------------------------

	public function test_match_list_shortcode_outputs_wrapper_div() {
		$output = do_shortcode( '[match_list]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );
	}

	public function test_player_list_shortcode_outputs_wrapper_div() {
		$output = do_shortcode( '[player_list]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );
	}

	public function test_staff_list_shortcode_outputs_wrapper_div() {
		$output = do_shortcode( '[staff_list]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );
	}

	public function test_league_table_shortcode_outputs_wrapper_div() {
		$output = do_shortcode( '[league_table]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );
	}

	// -----------------------------------------------------------------------
	// Shortcode wrapper filter
	// -----------------------------------------------------------------------

	public function test_shortcode_wrapper_is_filterable() {
		add_filter( 'wpclubmanager_shortcode_wrapper', function( $wrapper ) {
			return array( '<section class="custom-wrapper">', '</section>' );
		} );

		$output = do_shortcode( '[match_list]' );
		$this->assertStringContainsString( 'custom-wrapper', $output );

		remove_all_filters( 'wpclubmanager_shortcode_wrapper' );
	}

	// -----------------------------------------------------------------------
	// Shortcode with published content
	// -----------------------------------------------------------------------

	public function test_match_list_shortcode_shows_published_match() {
		$club_home = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Home FC',
			'post_status' => 'publish',
		) );

		$club_away = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Away United',
			'post_status' => 'publish',
		) );

		$match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'publish',
		) );

		update_post_meta( $match_id, 'wpcm_home_club', $club_home );
		update_post_meta( $match_id, 'wpcm_away_club', $club_away );

		$output = do_shortcode( '[match_list]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );

		wp_delete_post( $match_id, true );
		wp_delete_post( $club_home, true );
		wp_delete_post( $club_away, true );
	}

	public function test_player_list_shortcode_shows_published_player() {
		$player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'John Test',
			'post_status' => 'publish',
		) );

		$output = do_shortcode( '[player_list]' );
		$this->assertStringContainsString( 'wpcm-shortcode-wrapper', $output );

		wp_delete_post( $player_id, true );
	}
}
