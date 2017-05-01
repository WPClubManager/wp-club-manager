<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="updated wpclubmanager-message wpcm-connect">
	<p><?php _e( '<strong>Your theme does not declare WP Club Manager support</strong> &#8211; if you encounter layout issues please read our integration guide or save yourself time by using a fully compatible WP Club Manager theme from our collection.', 'wp-club-manager' ); ?></p>
	<p class="submit">
		<a href="<?php echo esc_url( apply_filters( 'wpclubmanager_themes_url', 'https://wpclubmanager.com/themes/', 'theme-collection' ) ); ?>" class="button-primary" target="_blank"><?php _e( 'Compatible Themes', 'wp-club-manager' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'wpclubmanager_docs_url', 'http://docs.wpclubmanager.com/article/64-third-party-theme-compatibility', 'theme-compatibility' ) ); ?>" class="button-primary" target="_blank"><?php _e( 'Theme integration guide', 'wp-club-manager' ); ?></a>
		<a class="skip button-secondary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'theme_support' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Hide this notice', 'wp-club-manager' ); ?></a>
	</p>
</div>
