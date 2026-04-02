<?php
/**
 * Tests for the staff title display option.
 *
 * Regression test: staff profile title (H1) should be hideable via an option,
 * matching the pattern used for all other staff profile fields.
 */

class StaffTitleTest extends WPCMTestCase {

	/**
	 * @var int
	 */
	private $staff_id;

	public function _setUp() {
		parent::_setUp();

		$this->staff_id = wp_insert_post( array(
			'post_type'   => 'wpcm_staff',
			'post_title'  => 'Test Coach',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		wp_delete_post( $this->staff_id, true );
		delete_option( 'wpcm_staff_profile_show_title' );
		parent::_tearDown();
	}

	/**
	 * When the option is set to 'no', the title should NOT appear in the template output.
	 */
	public function test_staff_title_hidden_when_option_is_no() {
		update_option( 'wpcm_staff_profile_show_title', 'no' );

		// Render the staff template — set $post globally and pass as arg.
		global $post;
		$post = get_post( $this->staff_id );
		setup_postdata( $post );

		ob_start();
		// Template reads $post via global and extract($args).
		wpclubmanager_get_template( 'content-single-staff.php', array( 'post' => $post ) );
		$html = ob_get_clean();

		wp_reset_postdata();

		$this->assertStringNotContainsString( '<h1 class="entry-title">', $html );
	}

	/**
	 * When the option is set to 'yes' (or not set), the title SHOULD appear.
	 */
	public function test_staff_title_shown_when_option_is_yes() {
		update_option( 'wpcm_staff_profile_show_title', 'yes' );

		global $post;
		$post = get_post( $this->staff_id );
		setup_postdata( $post );

		ob_start();
		wpclubmanager_get_template( 'content-single-staff.php' );
		$html = ob_get_clean();

		wp_reset_postdata();

		$this->assertStringContainsString( '<h1 class="entry-title">', $html );
	}

	/**
	 * Default behaviour (no option set) should show the title.
	 */
	public function test_staff_title_shown_by_default() {
		delete_option( 'wpcm_staff_profile_show_title' );

		global $post;
		$post = get_post( $this->staff_id );
		setup_postdata( $post );

		ob_start();
		wpclubmanager_get_template( 'content-single-staff.php' );
		$html = ob_get_clean();

		wp_reset_postdata();

		$this->assertStringContainsString( '<h1 class="entry-title">', $html );
	}
}
