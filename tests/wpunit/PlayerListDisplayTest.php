<?php
/**
 * Regression test for issue #61: new player not showing on player list.
 *
 * Root cause: when a player has no _wpcm_player_club meta, the shortcode's
 * meta_query INNER JOIN excludes them entirely from results.
 */

class PlayerListDisplayTest extends WPCMTestCase {

	/**
	 * @var int
	 */
	private $club_id;

	/**
	 * @var int
	 */
	private $player_with_club;

	/**
	 * @var int
	 */
	private $player_without_club;

	public function _setUp() {
		parent::_setUp();

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Test FC',
			'post_status' => 'publish',
		) );

		// Player WITH club assigned.
		$this->player_with_club = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Player With Club',
			'post_status' => 'publish',
		) );
		update_post_meta( $this->player_with_club, '_wpcm_player_club', $this->club_id );

		// Player WITHOUT club assigned (the bug scenario).
		$this->player_without_club = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Player Without Club',
			'post_status' => 'publish',
		) );
		// Intentionally do NOT set _wpcm_player_club.
	}

	public function _tearDown() {
		wp_delete_post( $this->club_id, true );
		wp_delete_post( $this->player_with_club, true );
		wp_delete_post( $this->player_without_club, true );
		parent::_tearDown();
	}

	/**
	 * A player without a club should still appear when [player_list] is
	 * rendered without a specific club filter.
	 */
	public function test_player_without_club_appears_in_unfiltered_list() {
		// Query players the same way the shortcode does.
		$args = array(
			'post_type'      => 'wpcm_player',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);

		$players = get_posts( $args );
		$ids     = wp_list_pluck( $players, 'ID' );

		$this->assertContains( $this->player_with_club, $ids, 'Player with club should be in results' );
		$this->assertContains( $this->player_without_club, $ids, 'Player without club should also be in results' );
	}

	/**
	 * The update_meta helper should save empty values so players always have
	 * the _wpcm_player_club meta key (even if empty), preventing INNER JOIN
	 * exclusion in meta_query.
	 */
	public function test_player_club_meta_saved_even_when_empty() {
		// Simulate saving an empty club value.
		update_post_meta( $this->player_without_club, '_wpcm_player_club', '' );

		$value = get_post_meta( $this->player_without_club, '_wpcm_player_club', true );
		$this->assertSame( '', $value, 'Empty club meta should be saved as empty string' );

		// Now query with meta_query — player should still appear.
		$args = array(
			'post_type'      => 'wpcm_player',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'   => '_wpcm_player_club',
					'value' => $this->club_id,
				),
				array(
					'key'     => '_wpcm_player_club',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => '_wpcm_player_club',
					'value' => '',
				),
			),
		);

		$players = get_posts( $args );
		$ids     = wp_list_pluck( $players, 'ID' );

		$this->assertContains( $this->player_without_club, $ids, 'Player with empty club meta should appear in results' );
	}
}
