<?php
/**
 * Regression tests for league table shortcode thumb attribute handling.
 *
 * The PHPCS cleanup in 2.3.1 changed `1 == $thumb` to `1 === $thumb`,
 * but shortcode attributes are always strings. This caused club logos
 * to stop rendering because `1 === "1"` is false.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/111
 */

class LeagueTableThumbTest extends WPCMTestCase {

	/** @var int */
	private $club_id;

	/** @var int */
	private $table_id;

	/** @var int */
	private $comp_id;

	/** @var int */
	private $season_id;

	public function _setUp() {
		parent::_setUp();

		update_option( 'wpcm_sport', 'soccer' );
		update_option( 'wpcm_disable_cache', 'yes' );
		update_option( 'wpcm_standings_columns_display', 'p,w,d,l,pts' );

		$this->club_id = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Thumb Test FC',
			'post_status' => 'publish',
		) );

		$comp = wp_insert_term( 'Thumb Test League', 'wpcm_comp' );
		$this->comp_id = is_wp_error( $comp ) ? $comp->get_error_data() : $comp['term_id'];

		$season = wp_insert_term( 'Thumb Test Season', 'wpcm_season' );
		$this->season_id = is_wp_error( $season ) ? $season->get_error_data() : $season['term_id'];

		wp_set_object_terms( $this->club_id, $this->comp_id, 'wpcm_comp' );
		wp_set_object_terms( $this->club_id, $this->season_id, 'wpcm_season' );

		$this->table_id = wp_insert_post( array(
			'post_type'   => 'wpcm_table',
			'post_title'  => 'Thumb Test Table',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->table_id, '_wpcm_table_clubs', array( $this->club_id ) );
		update_post_meta( $this->table_id, '_wpcm_table_stats', array() );

		wp_set_object_terms( $this->table_id, $this->comp_id, 'wpcm_comp' );
		wp_set_object_terms( $this->table_id, $this->season_id, 'wpcm_season' );
	}

	public function _tearDown() {
		wp_delete_post( $this->table_id, true );
		wp_delete_post( $this->club_id, true );

		parent::_tearDown();
	}

	/**
	 * Helper: capture the league table shortcode output.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered HTML.
	 */
	private function get_league_table_output( $atts ) {
		ob_start();
		WPCM_Shortcode_League_Table::output( $atts );
		return ob_get_clean();
	}

	/**
	 * Regression test: thumb="1" (string from shortcode) should render crests.
	 *
	 * Before the fix, `1 === '1'` was false and logos never appeared.
	 */
	public function test_thumb_string_one_renders_crest() {
		$output = $this->get_league_table_output( array(
			'id'    => $this->table_id,
			'thumb' => '1',
		) );

		$this->assertStringContainsString( 'crest', $output, 'Shortcode with thumb="1" should render crest markup.' );
	}

	/**
	 * Test that thumb="0" suppresses crest output.
	 */
	public function test_thumb_zero_hides_crest() {
		$output = $this->get_league_table_output( array(
			'id'    => $this->table_id,
			'thumb' => '0',
		) );

		$this->assertStringNotContainsString( 'crest', $output, 'Shortcode with thumb="0" should not render crest markup.' );
	}

	/**
	 * Test that omitting thumb (empty string) defaults to showing crests
	 * for non-widget context.
	 *
	 * The shortcode normalises empty thumb to 1 when type is not "widget".
	 */
	public function test_empty_thumb_defaults_to_showing_crest() {
		$output = $this->get_league_table_output( array(
			'id'    => $this->table_id,
			'thumb' => '',
		) );

		$this->assertStringContainsString( 'crest', $output, 'Empty thumb attribute should default to showing crests for non-widget type.' );
	}

	/**
	 * Test that integer 1 still works for backwards compatibility.
	 */
	public function test_thumb_integer_one_renders_crest() {
		$output = $this->get_league_table_output( array(
			'id'    => $this->table_id,
			'thumb' => 1,
		) );

		$this->assertStringContainsString( 'crest', $output, 'Shortcode with thumb=1 (integer) should render crest markup.' );
	}
}
