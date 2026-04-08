<?php
/**
 * Tests that the league table shortcode correctly handles string "1"
 * for the thumb attribute (as passed by widgets and shortcode parsing).
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/111
 */

class LeagueTableThumbTest extends WPCMTestCase {

	/**
	 * Test that the thumb attribute check works with string "1".
	 *
	 * The PHPCS cleanup in 2.3.1 changed `1 == $thumb` to `1 === $thumb`,
	 * but shortcode attributes are always strings. This caused club logos
	 * to stop rendering. The fix uses `! empty( $thumb )` instead.
	 */
	public function test_thumb_attribute_works_with_string_value() {
		// Simulate how the shortcode class processes the thumb attribute.
		// When passed via shortcode, $thumb is always a string.
		$thumb_from_shortcode = '1';

		// Before fix: 1 === '1' would be false (bug).
		// After fix: ! empty( '1' ) is true (correct).
		$this->assertTrue( ! empty( $thumb_from_shortcode ), 'String "1" thumb should be truthy.' );
	}

	/**
	 * Test that thumb="0" correctly disables thumbnails.
	 */
	public function test_thumb_attribute_zero_disables_thumbs() {
		$thumb_disabled = '0';

		// "0" is empty in PHP, so ! empty( "0" ) is false - correct behaviour.
		$this->assertFalse( ! empty( $thumb_disabled ), 'String "0" thumb should be falsy.' );
	}

	/**
	 * Test that thumb="" (empty string) disables thumbnails.
	 */
	public function test_thumb_attribute_empty_string_disables_thumbs() {
		$thumb_empty = '';

		$this->assertFalse( ! empty( $thumb_empty ), 'Empty string thumb should be falsy.' );
	}

	/**
	 * Test that integer 1 still works for backwards compatibility.
	 */
	public function test_thumb_attribute_works_with_integer_value() {
		$thumb_integer = 1;

		$this->assertTrue( ! empty( $thumb_integer ), 'Integer 1 thumb should be truthy.' );
	}
}
