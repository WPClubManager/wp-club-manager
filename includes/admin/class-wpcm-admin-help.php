<?php
/**
 * Add some content to the help tab
 *
 * @author      WPClubManager
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPCM_Admin_Help' ) ) :

/**
 * WPCM_Admin_Help Class.
 */
class WPCM_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( "current_screen", array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add Contextual help tabs.
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, wpcm_get_screen_ids() ) ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'        => 'wpclubmanager_support_tab',
			'title'     => __( 'Help &amp; Support', 'wp-club-manager' ),
			'content'   =>
				'<h2>' . __( 'Help &amp; Support', 'wp-club-manager' ) . '</h2>' .
				'<p>' . sprintf(
					__( 'Should you need help understanding, using, or extending WP Club Manager, %splease read our documentation%s. You will find all kinds of resources including snippets, tutorials and much more.' , 'wp-club-manager' ),
					'<a href="https://docs.wpclubmanager.com/?utm_source=helptab&utm_medium=settings&utm_content=docs&utm_campaign=wpclubmanagerplugin" target="_blank">',
					'</a>'
				) . '</p>' .
				'<p>' . sprintf(
					__( 'For further assistance with WP Club Manager core you can use the %scommunity forum%s.', 'wp-club-manager' ),
					'<a href="https://wordpress.org/support/plugin/wp-club-manager" target="_blank">',
					'</a>'
				) . '</p>' .
				'<p>' . __( 'Before asking for help we recommend checking the system status page to identify any problems with your configuration.', 'wp-club-manager' ) . '</p>' .
				'<p><a href="' . admin_url( 'admin.php?page=wpcm-status' ) . '" class="button button-primary">' . __( 'System Status', 'wp-club-manager' ) . '</a> <a href="' . 'https://wordpress.org/support/plugin/wp-club-manager' . '" class="button">' . __( 'Community Forum', 'wp-club-manager' ) . '</a></p>'
		) );

		$screen->add_help_tab( array(
			'id'        => 'wpclubmanager_onboard_tab',
			'title'     => __( 'Setup Wizard', 'wp-club-manager' ),
			'content'   =>
				'<h2>' . __( 'Setup Wizard', 'wp-club-manager' ) . '</h2>' .
				'<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'wp-club-manager' ) . '</p>' .
				'<p><a href="' . admin_url( 'index.php?page=wpcm-setup' ) . '" class="button button-primary">' . __( 'Setup Wizard', 'wp-club-manager' ) . '</a></p>'

		) );

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'wp-club-manager' ) . '</strong></p>' .
			'<p><a href="' . 'https://wpclubmanager.com/?utm_source=helptab&utm_medium=settings&utm_content=about&utm_campaign=wpclubmanagerplugin' . '" target="_blank">' . __( 'WP Club Manager Homepage', 'wp-club-manager' ) . '</a></p>' .
			'<p><a href="' . 'https://wordpress.org/extend/plugins/wp-club-manager/' . '" target="_blank">' . __( 'WordPress.org Project', 'wp-club-manager' ) . '</a></p>' .
			'<p><a href="' . 'https://wpclubmanager.com/themes/?utm_source=helptab&utm_medium=settings&utm_content=wpcmthemes&utm_campaign=wpclubmanagerplugin' . '" target="_blank">' . __( 'Official Themes', 'wp-club-manager' ) . '</a></p>'
		);
	}

}

endif;

return new WPCM_Admin_Help();
