<?php
/**
 * Tests for match importer taxonomy term lookup.
 *
 * @see https://github.com/WPClubManager/wp-club-manager/issues/95
 */
class MatchImportTaxonomyTest extends WPCMTestCase {

	/**
	 * @var WPCM_Match_Importer|null
	 */
	private $importer;

	/**
	 * @var int[]
	 */
	private $created_posts = array();

	/**
	 * @var int[]
	 */
	private $created_terms = array();

	public function _setUp() {
		parent::_setUp();

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

		$match_importer_file = WPCM()->plugin_path() . '/includes/admin/importers/class-wpcm-match-importer.php';
		if ( file_exists( $match_importer_file ) ) {
			require_once $match_importer_file;
		}

		if ( class_exists( 'WPCM_Match_Importer' ) ) {
			$this->importer = new WPCM_Match_Importer();
		}

		// Create home and away clubs for the import.
		$this->created_posts[] = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Arsenal',
			'post_status' => 'publish',
		) );
		$this->created_posts[] = wp_insert_post( array(
			'post_type'   => 'wpcm_club',
			'post_title'  => 'Chelsea',
			'post_status' => 'publish',
		) );
	}

	public function _tearDown() {
		foreach ( $this->created_posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
		foreach ( $this->created_terms as $term_id ) {
			wp_delete_term( $term_id, 'wpcm_comp' );
			wp_delete_term( $term_id, 'wpcm_season' );
			wp_delete_term( $term_id, 'wpcm_team' );
			wp_delete_term( $term_id, 'wpcm_venue' );
		}
		parent::_tearDown();
	}

	/**
	 * Test that the importer finds existing competition terms by name.
	 */
	public function test_import_finds_existing_competition() {
		if ( ! $this->importer ) {
			$this->markTestSkipped( 'WP_Importer class not available.' );
		}

		$term = wp_insert_term( 'Premier League', 'wpcm_comp' );
		$this->assertNotWPError( $term );
		$term_id                = $term['term_id'];
		$this->created_terms[] = $term_id;

		$columns = array_keys( $this->importer->columns );
		$data    = array(
			'2024/01/15', '15:00:00', 'Arsenal', 'Chelsea', '2-1',
			'Premier League', '2023-24', 'First Team', 'Emirates Stadium',
			'60000', 'Mike Dean', '',
		);

		ob_start();
		$this->importer->import( $data, $columns );
		ob_end_clean();

		// Find the imported match.
		$matches = get_posts( array(
			'post_type'   => 'wpcm_match',
			'numberposts' => 1,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		) );
		$this->assertNotEmpty( $matches, 'Match should have been imported.' );
		$match_id              = $matches[0]->ID;
		$this->created_posts[] = $match_id;

		// The match should be assigned to the existing competition term.
		$match_comps = wp_get_object_terms( $match_id, 'wpcm_comp' );
		$this->assertCount( 1, $match_comps, 'Match should have exactly one competition term.' );
		$this->assertEquals( $term_id, $match_comps[0]->term_id, 'Match should use the existing competition term, not create a new one.' );
		$this->assertEquals( 'Premier League', $match_comps[0]->name, 'Competition term should retain its original name.' );
	}

	/**
	 * Test that new terms created during import have proper names, not slugs.
	 */
	public function test_import_creates_terms_with_proper_names() {
		if ( ! $this->importer ) {
			$this->markTestSkipped( 'WP_Importer class not available.' );
		}

		$columns = array_keys( $this->importer->columns );
		$data    = array(
			'2024/01/15', '15:00:00', 'Arsenal', 'Chelsea', '2-1',
			'FA Cup', '2023-24', 'First Team', 'Wembley Stadium',
			'90000', 'Mike Dean', '',
		);

		ob_start();
		$this->importer->import( $data, $columns );
		ob_end_clean();

		$matches = get_posts( array(
			'post_type'   => 'wpcm_match',
			'numberposts' => 1,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		) );
		$this->assertNotEmpty( $matches );
		$match_id              = $matches[0]->ID;
		$this->created_posts[] = $match_id;

		// Check that terms were created with proper human-readable names.
		$comps = wp_get_object_terms( $match_id, 'wpcm_comp' );
		$this->assertCount( 1, $comps );
		$this->assertEquals( 'FA Cup', $comps[0]->name, 'Competition should be created with proper name, not slug.' );
		$this->created_terms[] = $comps[0]->term_id;

		$seasons = wp_get_object_terms( $match_id, 'wpcm_season' );
		$this->assertCount( 1, $seasons );
		$this->assertEquals( '2023-24', $seasons[0]->name, 'Season should be created with proper name, not slug.' );
		$this->created_terms[] = $seasons[0]->term_id;

		$teams = wp_get_object_terms( $match_id, 'wpcm_team' );
		$this->assertCount( 1, $teams );
		$this->assertEquals( 'First Team', $teams[0]->name, 'Team should be created with proper name, not slug.' );
		$this->created_terms[] = $teams[0]->term_id;

		$venues = wp_get_object_terms( $match_id, 'wpcm_venue' );
		$this->assertCount( 1, $venues );
		$this->assertEquals( 'Wembley Stadium', $venues[0]->name, 'Venue should be created with proper name, not slug.' );
		$this->created_terms[] = $venues[0]->term_id;
	}
}
