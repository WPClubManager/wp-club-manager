<?php
/**
 * Admin View: Notice - Install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="message" class="wpclubmanager-message updated">

	<p>
		<strong><?php echo sprintf( __( "Welcome to WP Club Manager v%s:", 'wp-club-manager' ), WPCM_VERSION ); ?></strong>
	</p>
	<p>
		<?php _e( 'Thanks for installing WP Club Manager! We recommend that you use the setup wizard to get your website up and running as quickly as possible. It will guide you through the first steps of setting up your club or league website in only a couple of minutes.', 'wp-club-manager' ); ?>
	</p>

	<em><a href='https://docs.wpclubmanager.com' target='_blank'><?php _e( 'Check out our documentation for help getting started', 'wp-club-manager' ); ?></a></em>

	<p class="submit">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcm-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'wp-club-manager' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'install' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'wp-club-manager' ); ?></a>
	</p>

</div>