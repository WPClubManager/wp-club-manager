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

		// WordPress 6.7+ with twentytwentyfive theme triggers a "Block bindings
		// source already registered" notice. Suppress it so it doesn't cause
		// assert_post_conditions to fail every test that creates posts.
		$this->setExpectedIncorrectUsage( 'WP_Block_Bindings_Registry::register' );
	}

	/**
	 * Tear down after each test.
	 */
	public function _tearDown() {
		parent::_tearDown();
	}
}
