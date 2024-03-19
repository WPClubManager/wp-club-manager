<?php
/**
 * WP Club Manager CLI Commands
 *
 * @class       WPCM_CLI
 * @version     2.2.12
 */

/**
 * WPCM_CLI
 */
class WPCM_CLI extends \WP_CLI_Command {

	/**
	 * Reset WP Club Manager data in the database
	 *
	 * @throws \WP_CLI\ExitException
	 */
	public function reset() {
		WP_CLI::confirm( esc_html__( 'Are you sure you want to reset the WP Club Manager data?', 'wp-club-manager' ) );
		require_once 'includes/class-wpcm-reset-database.php';

		( new WPCM_Reset_Database() )->reset();

		\WP_CLI::success( esc_html__( 'WP Club Manager data reset', 'wp-club-manager' ) );
	}
}
