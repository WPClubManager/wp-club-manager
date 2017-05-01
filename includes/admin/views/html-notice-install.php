<?php
/**
 * Admin View: Notice - Install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="updated wpclubmanager-message wpcm-connect">
	<p><?php _e( '<strong>Welcome to WP Club Manager</strong> &#8211; Enjoy :)', 'wp-club-manager' ); ?></p>
	<p class="submit"><a class="button-secondary button skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'install' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'wp-club-manager' ); ?></a></p>
</div>