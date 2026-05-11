<?php
/**
 * Tests for the player_gallery shortcode linkimage attribute.
 *
 * Verifies that the linkimage attribute controls whether player
 * photos and names are wrapped in links to the player profile.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/97
 */

class PlayerGalleryLinkTest extends WPCMTestCase {

	/** @var int */
	private $roster_id;

	/** @var int */
	private $player_id;

	/** @var int */
	private $season_id;

	/** @var int */
	private $team_id;

	public function _setUp() {
		parent::_setUp();

		// Disable shortcode output caching for tests.
		update_option( 'wpcm_disable_cache', 'yes' );

		// Create a season and team term.
		$season = wp_insert_term( 'Test Season', 'wpcm_season' );
		if ( is_wp_error( $season ) ) {
			$this->season_id = $season->get_error_data();
		} else {
			$this->season_id = $season['term_id'];
		}
		$team = wp_insert_term( 'Test Team', 'wpcm_team' );
		if ( is_wp_error( $team ) ) {
			$this->team_id = $team->get_error_data();
		} else {
			$this->team_id = $team['term_id'];
		}

		// Create a player.
		$this->player_id = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Gallery Player',
			'post_status' => 'publish',
		) );
		update_post_meta( $this->player_id, 'wpcm_number', '10' );

		// Create a roster and assign the player.
		$this->roster_id = wp_insert_post( array(
			'post_type'   => 'wpcm_roster',
			'post_title'  => 'Test Roster',
			'post_status' => 'publish',
		) );
		update_post_meta( $this->roster_id, '_wpcm_roster_players', array( $this->player_id ) );
		wp_set_object_terms( $this->roster_id, $this->season_id, 'wpcm_season' );
		wp_set_object_terms( $this->roster_id, $this->team_id, 'wpcm_team' );
	}

	public function _tearDown() {
		wp_delete_post( $this->roster_id, true );
		wp_delete_post( $this->player_id, true );
		wp_delete_term( $this->season_id, 'wpcm_season' );
		wp_delete_term( $this->team_id, 'wpcm_team' );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// Default behaviour — images are linked
	// -----------------------------------------------------------------------

	public function test_player_gallery_image_is_linked_by_default() {
		$output = do_shortcode( '[player_gallery id="' . $this->roster_id . '"]' );
		$url    = get_permalink( $this->player_id );

		$this->assertStringContainsString( '<a href="' . esc_url( $url ) . '">', $output );
		$this->assertStringContainsString( '<h4><a href="' . esc_url( $url ) . '">', $output );
	}

	// -----------------------------------------------------------------------
	// linkimage="no" — images are NOT linked
	// -----------------------------------------------------------------------

	public function test_player_gallery_image_not_linked_when_linkimage_no() {
		$output = do_shortcode( '[player_gallery id="' . $this->roster_id . '" linkimage="no"]' );
		$url    = get_permalink( $this->player_id );

		$this->assertStringNotContainsString( '<a href="' . esc_url( $url ) . '">', $output );
		$this->assertStringNotContainsString( '<h4><a href="' . esc_url( $url ) . '">', $output );
		// The image should still be rendered, just without the link.
		$this->assertStringContainsString( 'wpcm-players-gallery', $output );
	}

	// -----------------------------------------------------------------------
	// linkimage="yes" — explicit yes still links
	// -----------------------------------------------------------------------

	public function test_player_gallery_image_linked_when_linkimage_yes() {
		$output = do_shortcode( '[player_gallery id="' . $this->roster_id . '" linkimage="yes"]' );
		$url    = get_permalink( $this->player_id );

		$this->assertStringContainsString( '<a href="' . esc_url( $url ) . '">', $output );
		$this->assertStringContainsString( '<h4><a href="' . esc_url( $url ) . '">', $output );
	}
}
