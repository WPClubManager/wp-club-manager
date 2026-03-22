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
		// WordPress 6.7+ twentytwentyfive theme registers block bindings that
		// trigger a "doing_it_wrong" notice. Remove it before the parent checks.
		unset( $this->caught_doing_it_wrong['WP_Block_Bindings_Registry::register'] );
		parent::assertPostConditions();
	}
}
