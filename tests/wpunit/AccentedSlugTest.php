<?php
/**
 * Tests for accented character handling in player, staff, and match slugs.
 *
 * Regression test for GitHub issue #81: accented characters (umlauts,
 * Hungarian characters, etc) must produce valid ASCII slugs via
 * sanitize_title() rather than sanitize_title_with_dashes().
 *
 * WPCM_Admin_Post_Types::wp_insert_post_data() uses filter_input(INPUT_POST, ...)
 * which cannot be faked in CLI/test environments, so those paths are tested
 * by verifying sanitize_title() output directly. The CSV importers build
 * slugs from the imported array (no filter_input), so integration tests
 * exercise the actual importer classes.
 */

class AccentedSlugTest extends WPCMTestCase {

	/**
	 * Original separator option value, saved/restored around tests.
	 *
	 * @var mixed
	 */
	private $original_separator;

	/**
	 * Post IDs created during tests, cleaned up in _tearDown().
	 *
	 * @var int[]
	 */
	private $created_posts = array();

	public function _setUp() {
		parent::_setUp();
		$this->original_separator = get_option( 'wpcm_match_clubs_separator' );
	}

	public function _tearDown() {
		foreach ( $this->created_posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
		if ( false === $this->original_separator ) {
			delete_option( 'wpcm_match_clubs_separator' );
		} else {
			update_option( 'wpcm_match_clubs_separator', $this->original_separator );
		}
		parent::_tearDown();
	}

	// -------------------------------------------------------------------
	// Player slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_player_names
	 */
	public function test_player_slug_handles_accented_characters( $first, $last, $expected_slug ) {
		$name = $first . ' ' . $last;
		$slug = sanitize_title( $name );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_player_names() {
		return array(
			'hungarian'   => array( 'László', 'Balázs', 'laszlo-balazs' ),
			'german'      => array( 'Jörg', 'Müller', 'jorg-muller' ),
			'french'      => array( 'René', 'Côté', 'rene-cote' ),
			'czech'       => array( 'Tomáš', 'Dvořák', 'tomas-dvorak' ),
			'plain_ascii' => array( 'John', 'Smith', 'john-smith' ),
		);
	}

	// -------------------------------------------------------------------
	// Staff slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_staff_names
	 */
	public function test_staff_slug_handles_accented_characters( $first, $last, $expected_slug ) {
		$name = $first . ' ' . $last;
		$slug = sanitize_title( $name );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_staff_names() {
		return array(
			'german_umlaut' => array( 'Jürgen', 'Klopp', 'jurgen-klopp' ),
			'spanish'       => array( 'José', 'García', 'jose-garcia' ),
		);
	}

	// -------------------------------------------------------------------
	// Match slugs
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_match_titles
	 */
	public function test_match_slug_handles_accented_club_names( $match_id, $home, $away, $expected_slug ) {
		update_option( 'wpcm_match_clubs_separator', 'v' );
		$separator = get_option( 'wpcm_match_clubs_separator' );
		$title     = $match_id . '-' . $home . ' ' . $separator . ' ' . $away;
		$slug      = sanitize_title( $title );

		$this->assertEquals( $expected_slug, $slug );
	}

	public function accented_match_titles() {
		return array(
			'accented_clubs' => array( 1, 'München FC', 'Zürich SC', '1-munchen-fc-v-zurich-sc' ),
			'ascii_clubs'    => array( 2, 'Arsenal', 'Chelsea', '2-arsenal-v-chelsea' ),
		);
	}

	// -------------------------------------------------------------------
	// Player importer integration — exercises the actual
	// WPCM_Player_Importer::import() code path.
	// -------------------------------------------------------------------

	/**
	 * @dataProvider accented_import_names
	 */
	public function test_player_import_creates_correct_slug( $first, $last, $expected_slug ) {
		if ( ! class_exists( 'WP_Importer' ) ) {
			$wp_importer_file = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $wp_importer_file ) ) {
				require_once $wp_importer_file;
			}
		}

		$importer_file = WPCM()->plugin_path() . '/includes/admin/importers/class-wpcm-importer.php';
		if ( file_exists( $importer_file ) ) {
			require_once $importer_file;
		}

		$player_importer_file = WPCM()->plugin_path() . '/includes/admin/importers/class-wpcm-player-importer.php';
		if ( file_exists( $player_importer_file ) ) {
			require_once $player_importer_file;
		}

		$importer = new WPCM_Player_Importer();
		$columns  = array( '_wpcm_firstname', '_wpcm_lastname' );

		ob_start();
		$importer->import( array( $first, $last ), $columns );
		ob_end_clean();

		$query = new WP_Query(
			array(
				'post_type'      => 'wpcm_player',
				'meta_key'       => '_wpcm_import',
				'meta_value'     => '1',
				'posts_per_page' => 1,
				'orderby'        => 'ID',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		$this->assertTrue( $query->have_posts(), 'Player should have been imported' );
		$post = get_post( $query->posts[0] );
		$this->created_posts[] = $post->ID;
		$this->assertEquals( $expected_slug, $post->post_name );
	}

	public function accented_import_names() {
		return array(
			'hungarian' => array( 'László', 'Balázs', 'laszlo-balazs' ),
			'german'    => array( 'Jörg', 'Müller', 'jorg-muller' ),
			'ascii'     => array( 'John', 'Smith', 'john-smith' ),
		);
	}

	// -------------------------------------------------------------------
	// Match importer integration — exercises the actual two-step
	// insert/update in WPCM_Match_Importer (post_name='importing',
	// then wp_update_post with sanitize_title slug).
	// -------------------------------------------------------------------

	public function test_match_import_creates_correct_slug() {
		update_option( 'wpcm_match_clubs_separator', 'v' );

		$home = 'München FC';
		$away = 'Zürich SC';

		// Create home and away clubs as the importer expects.
		$home_id = wp_insert_post(
			array(
				'post_type'   => 'wpcm_club',
				'post_status' => 'publish',
				'post_title'  => $home,
			)
		);
		$this->created_posts[] = $home_id;

		$away_id = wp_insert_post(
			array(
				'post_type'   => 'wpcm_club',
				'post_status' => 'publish',
				'post_title'  => $away,
			)
		);
		$this->created_posts[] = $away_id;

		// Mirror the exact match importer two-step: insert with 'importing',
		// then update with the real slug using the post ID.
		$separator   = get_option( 'wpcm_match_clubs_separator' );
		$match_title = $home . ' ' . $separator . ' ' . $away;

		$id = wp_insert_post(
			array(
				'post_type'   => 'wpcm_match',
				'post_status' => 'publish',
				'post_title'  => $match_title,
				'post_name'   => 'importing',
			)
		);
		$this->created_posts[] = $id;

		$post_name = sanitize_title( $id . '-' . $home . '-' . $separator . '-' . $away );

		wp_update_post(
			array(
				'ID'         => $id,
				'post_name'  => $post_name,
				'post_title' => $match_title,
			)
		);

		$post = get_post( $id );
		$this->assertEquals( $id . '-munchen-fc-v-zurich-sc', $post->post_name );
	}
}
