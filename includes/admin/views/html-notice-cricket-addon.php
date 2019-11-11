<?php
/**
 * Admin View: Notice - Cricket Extension
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div id="message" class="wpclubmanager-message updated">
	<p><strong><?php _e( 'Get our FREE WP Club Manager for Cricket Extension', 'wp-club-manager' ); ?></strong></p>
	<p><?php _e( 'We recommend that you install our FREE WP Club Manager for Cricket extension which includes loads more features to enhance your cricket club website.', 'wp-club-manager' ); ?></p>
	<p class="submit">
		<a class="button-primary thickbox" href="<?php echo esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=wpcm-cricket&TB_iframe=true&width=600&height=800' ) ); ?>"><?php _e( 'Install WPCM Cricket', 'wp-club-manager' ); ?></a> <a class="skip button-secondary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'cricket_addon' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Hide this notice', 'wp-club-manager' ); ?></a>
	</p>
</div>