<?php
/**
 * Base test case for all WP Club Manager unit tests.
 */

class WPCMTestCase extends \Codeception\TestCase\WPTestCase {

	/**
	 * Set up before each test.
	 */
	public function _setUp() {
		parent::_setUp();
	}

	/**
	 * Tear down after each test.
	 */
	public function _tearDown() {
		parent::_tearDown();
	}

	/**
	 * Filter out known WordPress 6.7+ block bindings "incorrect usage" notice
	 * from twentytwentyfive theme. This fires unpredictably depending on
	 * whether block registration is triggered during the test.
	 */
	public function assertPostConditions(): void {
		// Remove the block bindings notice before parent checks.
		$dominated = 'WP_Block_Bindings_Registry::register';
		if ( property_exists( $this, 'caught_doing_it_wrong' ) ) {
			$this->caught_doing_it_wrong = array_filter(
				$this->caught_doing_it_wrong,
				function ( $v ) use ( $dominated ) {
					return $v !== $dominated;
				}
			);
		}
		parent::assertPostConditions();
	}
}
