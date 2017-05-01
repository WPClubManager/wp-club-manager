<?php
/**
 * Admin View: Notice - Default Club Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="error">
	<p><?php _e( '<strong>You have not set a Default Club!</strong> Some features of WP Club Manager will not work without a default club set, please choose your default club now.', 'wp-club-manager' ); ?></p>
	<p class="submit">
		<a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wpcm-settings' ) ); ?>"><?php _e( 'Set Default Club', 'wp-club-manager' ); ?></a> <a class="skip button-secondary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'club_check' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Hide this notice', 'wp-club-manager' ); ?></a>
	</p>
</div>