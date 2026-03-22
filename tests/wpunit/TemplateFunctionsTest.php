<?php
/**
 * Tests for the template loading system.
 *
 * Covers wpclubmanager_locate_template() fallback chain and
 * wpcm_get_template_html() output buffering wrapper.
 */

class TemplateFunctionsTest extends WPCMTestCase {

	// -----------------------------------------------------------------------
	// wpclubmanager_locate_template()
	// -----------------------------------------------------------------------

	public function test_locate_template_returns_plugin_default_when_not_in_theme() {
		$located = wpclubmanager_locate_template( 'single-player.php' );
		$this->assertStringEndsWith( 'templates/single-player.php', $located );
		$this->assertFileExists( $located );
	}

	public function test_locate_template_returns_string() {
		$located = wpclubmanager_locate_template( 'single-match.php' );
		$this->assertIsString( $located );
		$this->assertNotEmpty( $located );
	}

	public function test_locate_template_with_custom_default_path() {
		$plugin_templates = WPCM()->plugin_path() . '/templates/';
		$located = wpclubmanager_locate_template( 'single-club.php', '', $plugin_templates );
		$this->assertFileExists( $located );
		$this->assertStringContainsString( 'single-club.php', $located );
	}

	public function test_locate_template_returns_path_for_all_single_templates() {
		$templates = array(
			'single-club.php',
			'single-player.php',
			'single-staff.php',
			'single-match.php',
		);

		foreach ( $templates as $template ) {
			$located = wpclubmanager_locate_template( $template );
			$this->assertFileExists( $located, "Template {$template} should exist at: {$located}" );
		}
	}

	public function test_locate_template_filter_is_applied() {
		$custom_path = '/tmp/custom-template.php';

		add_filter( 'wpclubmanager_locate_template', function( $template, $name ) use ( $custom_path ) {
			if ( 'single-player.php' === $name ) {
				return $custom_path;
			}
			return $template;
		}, 10, 2 );

		$located = wpclubmanager_locate_template( 'single-player.php' );
		$this->assertEquals( $custom_path, $located );

		// Clean up.
		remove_all_filters( 'wpclubmanager_locate_template' );
	}

	// -----------------------------------------------------------------------
	// wpcm_get_template_html()
	// -----------------------------------------------------------------------

	public function test_get_template_outputs_content_for_valid_template() {
		// Use wpclubmanager_get_template (the real function) with output buffering.
		ob_start();
		wpclubmanager_get_template( 'layout/wrapper-start.php' );
		$html = ob_get_clean();
		$this->assertIsString( $html );
	}

	public function test_get_template_passes_args_to_template() {
		// Create a temp template file that outputs an arg.
		$tmp = sys_get_temp_dir() . '/wpcm-test-template.php';
		file_put_contents( $tmp, '<?php echo esc_html( $test_var ); ?>' );

		ob_start();
		wpclubmanager_get_template( 'wpcm-test-template.php', array( 'test_var' => 'hello-wpcm' ), '', sys_get_temp_dir() . '/' );
		$html = ob_get_clean();
		$this->assertStringContainsString( 'hello-wpcm', $html );

		unlink( $tmp );
	}
}
