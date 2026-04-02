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

		// Disable all profile fields except title to avoid template errors
		// (age calculation, taxonomy lookups, etc) in test context.
		$fields = array( 'dob', 'age', 'season', 'team', 'jobs', 'joined', 'nationality', 'hometown' );
		foreach ( $fields as $field ) {
			update_option( 'wpcm_staff_profile_show_' . $field, 'no' );
		}
		update_option( 'wpcm_show_staff_email', 'no' );
		update_option( 'wpcm_show_staff_phone', 'no' );

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
	 * Render the staff template and return HTML.
	 *
	 * Uses @ suppression because the template has pre-existing PHP warnings
	 * when rendered outside a full WordPress page context (undefined array
	 * keys in profile fields, etc). We only care about the title assertion.
	 */
	private function render_staff_template() {
		global $post;
		$post = get_post( $this->staff_id );
		setup_postdata( $post );

		ob_start();
		@wpclubmanager_get_template( 'content-single-staff.php', array( 'post' => $post ) );
		$html = ob_get_clean();

		wp_reset_postdata();

		return $html;
	}

	/**
	 * When the option is set to 'no', the title should NOT appear.
	 */
	public function test_staff_title_hidden_when_option_is_no() {
		update_option( 'wpcm_staff_profile_show_title', 'no' );
		$html = $this->render_staff_template();
		$this->assertStringNotContainsString( '<h1 class="entry-title">', $html );
	}

	/**
	 * When the option is set to 'yes', the title SHOULD appear.
	 */
	public function test_staff_title_shown_when_option_is_yes() {
		update_option( 'wpcm_staff_profile_show_title', 'yes' );
		$html = $this->render_staff_template();
		$this->assertStringContainsString( '<h1 class="entry-title">', $html );
	}

	/**
	 * Default behaviour (no option set) should show the title.
	 */
	public function test_staff_title_shown_by_default() {
		delete_option( 'wpcm_staff_profile_show_title' );
		$html = $this->render_staff_template();
		$this->assertStringContainsString( '<h1 class="entry-title">', $html );
	}
}
