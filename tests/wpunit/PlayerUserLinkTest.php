<?php
/**
 * Tests for linking players to WordPress user accounts.
 *
 * Covers issue #100: allow multiple players per user account.
 */

class PlayerUserLinkTest extends WPCMTestCase {

	/** @var int */
	private $user_id;

	/** @var int */
	private $player_one;

	/** @var int */
	private $player_two;

	public function _setUp() {
		parent::_setUp();

		$this->user_id = $this->factory()->user->create( array(
			'role' => 'player',
		) );

		$this->player_one = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Alice Smith',
			'post_status' => 'publish',
		) );

		$this->player_two = wp_insert_post( array(
			'post_type'   => 'wpcm_player',
			'post_title'  => 'Bob Smith',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->player_one, true );
		wp_delete_post( $this->player_two, true );
		wp_delete_user( $this->user_id );
		parent::_tearDown();
	}

	// -----------------------------------------------------------------------
	// wpcm_get_linked_players()
	// -----------------------------------------------------------------------

	public function test_get_linked_players_returns_empty_array_for_unlinked_user() {
		$this->assertSame( array(), wpcm_get_linked_players( $this->user_id ) );
	}

	public function test_get_linked_players_returns_single_player() {
		add_user_meta( $this->user_id, '_linked_player', $this->player_one );

		$linked = wpcm_get_linked_players( $this->user_id );
		$this->assertSame( array( $this->player_one ), $linked );
	}

	public function test_get_linked_players_returns_multiple_players() {
		add_user_meta( $this->user_id, '_linked_player', $this->player_one );
		add_user_meta( $this->user_id, '_linked_player', $this->player_two );

		$linked = wpcm_get_linked_players( $this->user_id );
		$this->assertCount( 2, $linked );
		$this->assertContains( $this->player_one, $linked );
		$this->assertContains( $this->player_two, $linked );
	}

	// -----------------------------------------------------------------------
	// wpcm_link_player_to_user()
	// -----------------------------------------------------------------------

	public function test_link_player_to_user_creates_bidirectional_link() {
		wpcm_link_player_to_user( $this->player_one, $this->user_id );

		$this->assertEquals( $this->user_id, get_post_meta( $this->player_one, '_wpcm_link_users', true ) );
		$this->assertContains( $this->player_one, wpcm_get_linked_players( $this->user_id ) );
	}

	public function test_link_multiple_players_to_same_user() {
		wpcm_link_player_to_user( $this->player_one, $this->user_id );
		wpcm_link_player_to_user( $this->player_two, $this->user_id );

		$linked = wpcm_get_linked_players( $this->user_id );
		$this->assertCount( 2, $linked );
		$this->assertContains( $this->player_one, $linked );
		$this->assertContains( $this->player_two, $linked );
	}

	public function test_link_does_not_create_duplicate_entries() {
		wpcm_link_player_to_user( $this->player_one, $this->user_id );
		wpcm_link_player_to_user( $this->player_one, $this->user_id );

		$linked = wpcm_get_linked_players( $this->user_id );
		$this->assertCount( 1, $linked );
	}

	public function test_changing_user_removes_old_user_link() {
		$other_user = $this->factory()->user->create( array(
			'role' => 'player',
		) );

		wpcm_link_player_to_user( $this->player_one, $this->user_id );
		wpcm_link_player_to_user( $this->player_one, $other_user );

		// Old user should no longer have this player linked.
		$this->assertNotContains( $this->player_one, wpcm_get_linked_players( $this->user_id ) );
		// New user should have it.
		$this->assertContains( $this->player_one, wpcm_get_linked_players( $other_user ) );

		wp_delete_user( $other_user );
	}

	public function test_unlinking_user_removes_bidirectional_link() {
		wpcm_link_player_to_user( $this->player_one, $this->user_id );
		wpcm_link_player_to_user( $this->player_two, $this->user_id );

		// Unlink player one by setting user to 0 / none.
		wpcm_link_player_to_user( $this->player_one, 0 );

		$this->assertEquals( '', get_post_meta( $this->player_one, '_wpcm_link_users', true ) );
		$linked = wpcm_get_linked_players( $this->user_id );
		$this->assertCount( 1, $linked );
		$this->assertContains( $this->player_two, $linked );
	}
}
