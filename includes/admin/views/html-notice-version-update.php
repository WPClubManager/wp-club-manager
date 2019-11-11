<?php
/**
 * Admin View: Notice - Version update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="message" class="updated wpclubmanager-message">

	<p>
		<strong><?php echo sprintf( __( "What's new in version %s:", 'wp-club-manager' ), WPCM_VERSION ); ?></strong>
	</p>

	<p>
		<?php _e( "Thank you for upgrading to version 2 which has loads of new features, improvements and bug fixes. We recommend that you familiarise yourself with what's new and how to use the features included in this version. Some of the update highlights include:", 'wp-club-manager' ); ?>

		<ul>
			<li><strong><?php _e( 'Rosters', 'wp-club-manager'); ?></strong> &mdash; <?php _e( 'Easily manage each players and staff for each season.', 'wp-club-manager'); ?></li>
			<li><strong><?php _e( 'League Tables', 'wp-club-manager'); ?></strong> &mdash; <?php _e( 'New, improved league table management.', 'wp-club-manager'); ?></li>
			<li><strong><?php _e( 'League Mode', 'wp-club-manager'); ?></strong> &mdash; <?php _e( 'Use WP Club Manager to manage a league website.', 'wp-club-manager'); ?></li>
		</ul>

		<em><a href='https://wpclubmanager.com/version-2-upgrade-guide/' target='_blank'><?php _e( 'Find out more about how this update will affect your website', 'wp-club-manager' ); ?></a></em><br>
		<em><a href='https://docs.wpclubmanager.com' target='_blank'><?php _e( 'Check out our updated documentation', 'wp-club-manager' ); ?></a></em>
	</p>

	<?php
	if( in_array( 'wpcm-players-gallery/wpcm-player-gallery.php', apply_filters('active_plugins', get_option('active_plugins'))) || in_array( 'wp-club-manager-score-summary/wpcm-score-summary.php', apply_filters('active_plugins', get_option('active_plugins'))) ) { ?>
		<p><strong>
			<?php echo sprintf( __( "The following plugins have been added to the core plugin so we recommend that you deactivate and delete them from the %sPlugins page%s.", 'wp-club-manager' ),
			'<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">',
			'</a>' ); ?>
		</strong></p>
	<?php
	}

	if( in_array( 'wpcm-players-gallery/wpcm-player-gallery.php', apply_filters('active_plugins', get_option('active_plugins'))) ) { ?>
		<p>
			<span class="dashicons dashicons-warning"></span> <?php echo sprintf( __( "You have the %sPlayers Gallery%s plugin activated.", 'wp-club-manager' ),
			'<strong>',
			'</strong>' ); ?>
		</p>
	<?php
	}

	if( in_array( 'wp-club-manager-score-summary/wpcm-score-summary.php', apply_filters('active_plugins', get_option('active_plugins'))) ) { ?>
		<p>
			<span class="dashicons dashicons-warning"></span> <?php echo sprintf( __( "You have the %sScore Summary%s plugin activated.", 'wp-club-manager' ),
			'<strong>',
			'</strong>' ); ?>
		</p>
	<?php
	} ?>

	<p class="submit">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcm-settings' ) ); ?>" class="button-primary"><?php _e( 'Go to WP Club Manager settings', 'wp-club-manager' ); ?></a> <a class="skip button-secondary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'version_update' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>"><?php _e( 'Hide this notice', 'wp-club-manager' ); ?></a>
	</p>
</div>