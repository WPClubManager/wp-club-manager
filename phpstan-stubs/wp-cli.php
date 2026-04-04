<?php
/**
 * Stub for WP-CLI classes to satisfy PHPStan analysis.
 * WP-CLI is not a Composer dependency so these classes are undefined during static analysis.
 */

// phpcs:disable

if ( ! class_exists( 'WP_CLI_Command' ) ) {
	class WP_CLI_Command {} // phpstan-stub
}

if ( ! class_exists( 'WP_CLI' ) ) {
	class WP_CLI {
		/**
		 * @param string $command
		 * @param class-string|object $class
		 * @param array  $args
		 * @return void
		 */
		public static function add_command( $command, $class, $args = array() ) {}

		/**
		 * @param string $message
		 * @return void
		 */
		public static function success( $message ) {}

		/**
		 * @param string $question
		 * @return void
		 */
		public static function confirm( $question ) {}
	}
}
