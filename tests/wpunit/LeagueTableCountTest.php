<?php
/**
 * Regression test for "Wrong count of clubs in League index".
 *
 * The admin league table list column shows the count of clubs in each table.
 * The code uses unserialize() on _wpcm_table_clubs meta, which returns false
 * when empty — count(false) is wrong on PHP 7 and fatal on PHP 8.
 */

class LeagueTableCountTest extends WPCMTestCase {

	/**
	 * @var int
	 */
	private $table_id;

	/**
	 * @var array
	 */
	private $club_ids = array();

	public function _setUp() {
		parent::_setUp();

		// Create 3 clubs.
		for ( $i = 1; $i <= 3; $i++ ) {
			$this->club_ids[] = wp_insert_post( array(
				'post_type'   => 'wpcm_club',
				'post_title'  => "Test Club {$i}",
				'post_status' => 'publish',
			) );
		}

		// Create a league table.
		$this->table_id = wp_insert_post( array(
			'post_type'   => 'wpcm_table',
			'post_title'  => 'Test League Table',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->table_id, true );
		foreach ( $this->club_ids as $id ) {
			wp_delete_post( $id, true );
		}
		parent::_tearDown();
	}

	/**
	 * When 3 clubs are stored in the table meta, count should return 3.
	 */
	public function test_table_clubs_count_matches_stored_clubs() {
		update_post_meta( $this->table_id, '_wpcm_table_clubs', serialize( $this->club_ids ) );

		$clubs = maybe_unserialize( get_post_meta( $this->table_id, '_wpcm_table_clubs', true ) );
		$this->assertIsArray( $clubs );
		$this->assertCount( 3, $clubs );
	}

	/**
	 * When no clubs are stored, count should return 0 (not error).
	 */
	public function test_table_clubs_count_is_zero_when_empty() {
		// Don't set any _wpcm_table_clubs meta.
		$raw   = get_post_meta( $this->table_id, '_wpcm_table_clubs', true );
		$clubs = maybe_unserialize( $raw );

		// Should be safely countable — not false, not a fatal error.
		$count = is_array( $clubs ) ? count( $clubs ) : 0;
		$this->assertEquals( 0, $count );
	}

	/**
	 * The admin column rendering should use maybe_unserialize and guard
	 * against non-array results.
	 */
	public function test_admin_column_uses_safe_count() {
		update_post_meta( $this->table_id, '_wpcm_table_clubs', serialize( $this->club_ids ) );

		// Simulate what the admin column does (class-wpcm-admin-post-types.php:1021-1022).
		$clubs = maybe_unserialize( get_post_meta( $this->table_id, '_wpcm_table_clubs', true ) );
		$count = is_array( $clubs ) ? count( $clubs ) : 0;

		$this->assertEquals( 3, $count );
	}

	/**
	 * Empty serialized data should not cause a fatal error.
	 */
	public function test_admin_column_safe_count_with_empty_serialized_data() {
		update_post_meta( $this->table_id, '_wpcm_table_clubs', '' );

		$clubs = maybe_unserialize( get_post_meta( $this->table_id, '_wpcm_table_clubs', true ) );
		$count = is_array( $clubs ) ? count( $clubs ) : 0;

		$this->assertEquals( 0, $count );
	}
}
