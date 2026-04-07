<?php
/**
 * Tests for .distignore to prevent accidental exclusion of shipped assets.
 */

class DistIgnoreTest extends WPCMTestCase {

	/**
	 * The parsed lines from .distignore (comments and blanks stripped).
	 *
	 * @var string[]
	 */
	private $patterns;

	public function _setUp() {
		parent::_setUp();

		$distignore_path = dirname( __DIR__, 2 ) . '/.distignore';
		$this->assertFileExists( $distignore_path, '.distignore must exist' );

		$lines = file( $distignore_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$this->patterns = array_filter(
			$lines,
			function ( $line ) {
				return '' !== trim( $line ) && '#' !== $line[0];
			}
		);
	}

	/**
	 * Vendor JS assets must not be excluded by .distignore.
	 *
	 * The "vendor" pattern in .distignore is intended to exclude the
	 * top-level Composer vendor/ directory. If it is not anchored with
	 * a leading slash, rsync will also exclude assets/js/vendor/ which
	 * ships required libraries (Chosen, Timepicker, etc.).
	 *
	 * @see https://github.com/WPClubManager/wp-club-manager/issues/104
	 */
	public function test_vendor_pattern_is_anchored_to_root() {
		foreach ( $this->patterns as $pattern ) {
			$trimmed = trim( $pattern );

			// Skip anything that isn't about the word "vendor".
			if ( false === strpos( $trimmed, 'vendor' ) ) {
				continue;
			}

			// An unanchored "vendor" (no leading slash) would match
			// assets/js/vendor/ and break the build.
			$this->assertStringStartsWith(
				'/',
				$trimmed,
				sprintf(
					'.distignore pattern "%s" is not anchored — it will exclude assets/js/vendor/. Use "/%s" instead.',
					$trimmed,
					ltrim( $trimmed, '/' )
				)
			);
		}
	}

	/**
	 * All four vendor JS files referenced by the admin scripts must exist.
	 */
	public function test_required_vendor_js_files_exist() {
		$plugin_dir = dirname( __DIR__, 2 );

		$required_files = array(
			'assets/js/vendor/jquery-chosen/chosen.jquery.min.js',
			'assets/js/vendor/jquery-chosen/chosen.order.jquery.min.js',
			'assets/js/vendor/jquery-chosen/ajax-chosen.jquery.min.js',
			'assets/js/vendor/jquery.timepicker.min.js',
		);

		foreach ( $required_files as $file ) {
			$this->assertFileExists(
				$plugin_dir . '/' . $file,
				sprintf( 'Required vendor JS file missing: %s', $file )
			);
		}
	}
}
