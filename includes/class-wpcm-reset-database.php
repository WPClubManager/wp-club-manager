<?php
/**
 * Reset database to start again, use with caution
 *
 * @class       WPCM_Reset_Database
 * @version     2.2.12
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Reset_Database
 */
class WPCM_Reset_Database {

	/**
	 * Reset the database for WP Club Manager data
	 *
	 * @return void
	 */
	public function reset() {
		global $wpdb;

		// Delete options
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpcm_%';" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpclubmanager_%';" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_wpcm_%';" );

		$this->delete_terms( 'wpcm_comp' );
		$this->delete_terms( 'wpcm_jobs' );
		$this->delete_terms( 'wpcm_position' );
		$this->delete_terms( 'wpcm_season' );
		$this->delete_terms( 'wpcm_team' );
		$this->delete_terms( 'wpcm_venue' );

		$wpdb->query( "DELETE FROM meta {$wpdb->postmeta} meta INNER JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.post_type IN ( 'wpcm_player', 'wpcm_staff', 'wpcm_club', 'wpcm_match', 'wpcm_sponsor', 'wpcm_roster', 'wpcm_table' );" );
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'wpcm_player', 'wpcm_staff', 'wpcm_club', 'wpcm_match', 'wpcm_sponsor', 'wpcm_roster', 'wpcm_table' );" );

		delete_option( 'wpclubmanager_installed' );
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	protected function delete_terms( $taxonomy ) {
		$terms         = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );
		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, $taxonomy );
		}
	}
}
