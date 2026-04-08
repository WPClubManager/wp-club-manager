<?php
/**
 * Tests for widget render methods -- specifically that string "-1"
 * values (produced by sanitize_text_field) are correctly excluded
 * from shortcode attribute output.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/102
 */

class WidgetRenderTest extends WPCMTestCase {

	/**
	 * Default widget wrapper args.
	 *
	 * @var array
	 */
	private $widget_args = array(
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		'widget_id'     => 'test-widget-1',
	);

	/**
	 * Captured shortcode attributes from the stub.
	 *
	 * @var array|null
	 */
	private $captured_atts;

	/**
	 * Original shortcode callbacks to restore in teardown.
	 *
	 * @var array
	 */
	private $original_shortcodes = array();

	/**
	 * Register shortcode stubs before each test.
	 */
	public function _setUp() {
		parent::_setUp();

		global $shortcode_tags;

		$this->captured_atts = null;

		// Save original shortcode callbacks so we can restore them.
		foreach ( array( 'player_list', 'league_table' ) as $tag ) {
			if ( isset( $shortcode_tags[ $tag ] ) ) {
				$this->original_shortcodes[ $tag ] = $shortcode_tags[ $tag ];
			}
		}

		$stub = function ( $atts ) {
			$this->captured_atts = $atts;
			return '';
		};

		add_shortcode( 'player_list', $stub );
		add_shortcode( 'league_table', $stub );
	}

	/**
	 * Restore original shortcode callbacks after each test.
	 */
	public function _tearDown() {
		// Restore original shortcodes so other tests are not affected.
		foreach ( $this->original_shortcodes as $tag => $callback ) {
			add_shortcode( $tag, $callback );
		}
		$this->original_shortcodes = array();

		parent::_tearDown();
	}

	/**
	 * Test that WPCM_Players_Widget excludes string "-1" values from shortcode atts.
	 */
	public function test_players_widget_excludes_string_minus_one() {
		$widget   = new WPCM_Players_Widget();
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

		ob_start();
		$widget->widget( $this->widget_args, $instance );
		ob_get_clean();

		$this->assertIsArray( $this->captured_atts, 'player_list shortcode should be invoked.' );
		$this->assertArrayNotHasKey( 'position', $this->captured_atts, 'String "-1" position should be excluded.' );
		$this->assertArrayNotHasKey( 'display_options', $this->captured_atts, 'String "-1" display_options should be excluded.' );
		$this->assertArrayNotHasKey( 'link_options', $this->captured_atts, 'String "-1" link_options should be excluded.' );
		$this->assertSame( 'Players', $this->captured_atts['title'] );
		$this->assertSame( '42', $this->captured_atts['id'] );
	}

	/**
	 * Test that WPCM_Standings_Widget excludes string "-1" values from shortcode atts.
	 */
	public function test_standings_widget_excludes_string_minus_one() {
		$widget   = new WPCM_Standings_Widget();
		$instance = array(
			'title'        => 'Standings',
			'id'           => '99',
			'limit'        => '7',
			'link_options' => '-1',
			'linktext'     => 'View all standings',
			'linkpage'     => 'None',
		);

		ob_start();
		$widget->widget( $this->widget_args, $instance );
		ob_get_clean();

		$this->assertIsArray( $this->captured_atts, 'league_table shortcode should be invoked.' );
		$this->assertArrayNotHasKey( 'link_options', $this->captured_atts, 'String "-1" link_options should be excluded.' );
		$this->assertSame( 'Standings', $this->captured_atts['title'] );
		$this->assertSame( '99', $this->captured_atts['id'] );
	}

	/**
	 * Test that integer -1 values are also excluded for WPCM_Players_Widget.
	 */
	public function test_players_widget_excludes_integer_minus_one() {
		$widget   = new WPCM_Players_Widget();
		$instance = array(
			'title'           => 'Players',
			'id'              => '42',
			'display_options' => -1,
			'link_options'    => -1,
		);

		ob_start();
		$widget->widget( $this->widget_args, $instance );
		ob_get_clean();

		$this->assertIsArray( $this->captured_atts, 'player_list shortcode should be invoked.' );
		$this->assertArrayNotHasKey( 'display_options', $this->captured_atts, 'Integer -1 display_options should be excluded.' );
		$this->assertArrayNotHasKey( 'link_options', $this->captured_atts, 'Integer -1 link_options should be excluded.' );
	}

	/**
	 * Test that integer -1 values are also excluded for WPCM_Standings_Widget.
	 */
	public function test_standings_widget_excludes_integer_minus_one() {
		$widget   = new WPCM_Standings_Widget();
		$instance = array(
			'title'        => 'Standings',
			'id'           => '99',
			'limit'        => '7',
			'link_options' => -1,
			'linktext'     => 'View all standings',
			'linkpage'     => 'None',
		);

		ob_start();
		$widget->widget( $this->widget_args, $instance );
		ob_get_clean();

		$this->assertIsArray( $this->captured_atts, 'league_table shortcode should be invoked.' );
		$this->assertArrayNotHasKey( 'link_options', $this->captured_atts, 'Integer -1 link_options should be excluded.' );
		$this->assertSame( 'Standings', $this->captured_atts['title'] );
		$this->assertSame( '99', $this->captured_atts['id'] );
	}
}
