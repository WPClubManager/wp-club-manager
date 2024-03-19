<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="wpclubmanager-message error">
	<p>
		<strong><?php esc_html_e( 'Your theme does not declare WP Club Manager support!', 'wp-club-manager' ); ?></strong><br>
		<?php esc_html_e( 'If you encounter layout issues please read our integration guide or save yourself time by using a fully compatible WP Club Manager theme from our collection.', 'wp-club-manager' ); ?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( apply_filters( 'wpclubmanager_themes_url', 'https://wpclubmanager.com/themes/', 'theme-collection' ) ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Compatible themes', 'wp-club-manager' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'wpclubmanager_docs_url', 'https://wpclubmanager.com/documentation/third-party-theme-compatibility', 'theme-compatibility' ) ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Theme integration guide', 'wp-club-manager' ); ?></a>
		<a class="skip button-secondary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'theme_support' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php esc_html_e( 'Hide this notice', 'wp-club-manager' ); ?></a>
	</p>
</div>
