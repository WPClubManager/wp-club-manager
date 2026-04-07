<?php
/**
 * Tests for widget render methods — specifically that string "-1"
 * values (produced by sanitize_text_field) are correctly excluded
 * from shortcode attribute output.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/102
 */

class WidgetRenderTest extends WPCMTestCase {

	/**
	 * Test that WPCM_Players_Widget::widget() excludes string "-1" values
	 * from the generated shortcode.
	 */
	public function test_players_widget_excludes_string_minus_one() {
		$widget = new WPCM_Players_Widget();

		// Simulate saved instance data — values come through sanitize_text_field()
		// so "-1" markers arrive as strings, not integers.
		$instance = array(
			'title'           => 'Players',
			'id'              => '42',
			'limit'           => '3',
			'position'        => '-1',
			'display_options' => '-1',
			'link_options'    => '-1',
			'linktext'        => 'View all',
			'linkpage'        => 'None',
		);

		$args = array(
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
			'widget_id'     => 'test-players-1',
		);

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		// The shortcode output should NOT contain position="-1" or link_options="-1".
		$this->assertStringNotContainsString( 'position="-1"', $output, 'String "-1" position should be excluded from shortcode' );
		$this->assertStringNotContainsString( 'display_options="-1"', $output, 'String "-1" display_options should be excluded from shortcode' );
		$this->assertStringNotContainsString( 'link_options="-1"', $output, 'String "-1" link_options should be excluded from shortcode' );

		// Valid values should still be present.
		$this->assertStringContainsString( 'title="Players"', $output );
		$this->assertStringContainsString( 'id="42"', $output );
	}

	/**
	 * Test that WPCM_Standings_Widget::widget() excludes string "-1" values
	 * from the generated shortcode.
	 */
	public function test_standings_widget_excludes_string_minus_one() {
		$widget = new WPCM_Standings_Widget();

		$instance = array(
			'title'        => 'Standings',
			'id'           => '99',
			'limit'        => '7',
			'link_options' => '-1',
			'linktext'     => 'View all standings',
			'linkpage'     => 'None',
		);

		$args = array(
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
			'widget_id'     => 'test-standings-1',
		);

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$this->assertStringNotContainsString( 'link_options="-1"', $output, 'String "-1" link_options should be excluded from shortcode' );
		$this->assertStringContainsString( 'title="Standings"', $output );
		$this->assertStringContainsString( 'id="99"', $output );
	}

	/**
	 * Test that integer -1 values are also excluded (original behaviour).
	 */
	public function test_players_widget_excludes_integer_minus_one() {
		$widget = new WPCM_Players_Widget();

		$instance = array(
			'title'           => 'Players',
			'id'              => '42',
			'display_options' => -1,
			'link_options'    => -1,
		);

		$args = array(
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
			'widget_id'     => 'test-players-2',
		);

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$this->assertStringNotContainsString( 'display_options="-1"', $output );
		$this->assertStringNotContainsString( 'link_options="-1"', $output );
	}
}
