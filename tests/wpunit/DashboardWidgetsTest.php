<?php
/**
 * Tests for WPCM_Admin_Dashboard_Widgets.
 *
 * Covers bugs where the upcoming matches widget produces PHP warnings
 * when a match has no competition terms assigned, and verifies the
 * "At a Glance" items are rendered correctly.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/59
 */

class DashboardWidgetsTest extends WPCMTestCase {

	/** @var int */
	private $home_club_id;

	/** @var int */
	private $away_club_id;

	/** @var int */
	private $match_id;

	public function _setUp() {
		parent::_setUp();

		// Create clubs.
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

		// Set default club and match format.
		update_option( 'wpcm_default_club', $this->home_club_id );
		update_option( 'wpcm_match_title_format', '%home% vs %away%' );
		update_option( 'wpcm_match_clubs_separator', 'vs' );
	}

	public function _tearDown() {
		if ( $this->match_id ) {
			wp_delete_post( $this->match_id, true );
		}
		wp_delete_post( $this->home_club_id, true );
		wp_delete_post( $this->away_club_id, true );

		delete_option( 'wpcm_default_club' );
		delete_option( 'wpcm_match_title_format' );
		delete_option( 'wpcm_match_clubs_separator' );

		parent::_tearDown();
	}

	/**
	 * Test that the upcoming matches widget does not produce PHP warnings
	 * when a match has no competition terms assigned.
	 *
	 * Before the fix, the $competition variable was undefined when no
	 * competition terms existed, causing an "Undefined variable" warning.
	 */
	public function test_upcoming_widget_no_warning_without_competition() {
		// Create a future match this week with no competition terms.
		$future_date    = gmdate( 'Y-m-d H:i:s', strtotime( 'next Saturday 15:00' ) );
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'future',
			'post_date'   => $future_date,
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_away_club', $this->away_club_id );

		// Load the widget class.
		$widgets = new WPCM_Admin_Dashboard_Widgets();

		// Capture output — before the fix this would trigger a PHP warning.
		$error_triggered = false;
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
		set_error_handler( function ( $errno, $errstr ) use ( &$error_triggered ) {
			if ( strpos( $errstr, 'competition' ) !== false || strpos( $errstr, 'Undefined variable' ) !== false ) {
				$error_triggered = true;
			}
			return true;
		} );

		ob_start();
		$widgets->upcoming_matches_widget();
		$output = ob_get_clean();

		restore_error_handler();

		$this->assertFalse( $error_triggered, 'No PHP warning should be triggered for undefined $competition variable.' );
		$this->assertStringContainsString( 'wpcm-matches-list', $output );
	}

	/**
	 * Test that the upcoming matches widget renders competition info
	 * when a match has a competition term assigned.
	 */
	public function test_upcoming_widget_shows_competition_when_assigned() {
		$future_date    = gmdate( 'Y-m-d H:i:s', strtotime( 'next Saturday 15:00' ) );
		$this->match_id = wp_insert_post( array(
			'post_type'   => 'wpcm_match',
			'post_title'  => 'Home FC vs Away United',
			'post_status' => 'future',
			'post_date'   => $future_date,
		) );

		update_post_meta( $this->match_id, 'wpcm_home_club', $this->home_club_id );
		update_post_meta( $this->match_id, 'wpcm_away_club', $this->away_club_id );
		update_post_meta( $this->match_id, 'wpcm_comp_status', 'Round 1' );

		// Create and assign a competition term.
		$comp = wp_insert_term( 'Premier League', 'wpcm_comp' );
		if ( ! is_wp_error( $comp ) ) {
			wp_set_post_terms( $this->match_id, array( $comp['term_id'] ), 'wpcm_comp' );
		}

		$widgets = new WPCM_Admin_Dashboard_Widgets();

		ob_start();
		$widgets->upcoming_matches_widget();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'wpcm-matches-list', $output );
		$this->assertStringContainsString( 'Premier League', $output );
	}

	/**
	 * Test that the glance items include WPCM post types.
	 */
	public function test_glance_items_include_wpcm_post_types() {
		// Clubs already created in setUp, so we have at least 2 wpcm_club posts.
		$widgets = new WPCM_Admin_Dashboard_Widgets();
		$items   = $widgets->glance_items( array() );

		$this->assertIsArray( $items );
		// We should have at least wpcm_club items.
		$has_club = false;
		foreach ( $items as $item ) {
			if ( strpos( $item, 'wpcm_club' ) !== false ) {
				$has_club = true;
				break;
			}
		}
		$this->assertTrue( $has_club, 'Glance items should include wpcm_club post type counts.' );
	}
}
