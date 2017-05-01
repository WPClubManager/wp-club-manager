<?php
/**
 * Admin View: Page - Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap wpclubmanager-tools">
	<h1>
		<?php _e( 'WP Club Manager Tools', 'wp-club-manager' ); ?>
	</h1>

	<p><?php _e( 'These tools can assist with the functionality of WP Club Manager.', 'wp-club-manager' ); ?></p>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

			<div id="postbox-container-2" class="postbox-container">

				<table class="wpcm_tools_table widefat" cellspacing="0" id="tools">
					<thead>
						<tr>
							<th colspan="1"><?php _e( 'WP Club Manager Cache', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="clear-transients">
								<p><?php _e( 'Transients are used to cache the output of shortcodes and widgets in WP Club Manager.', 'wp-club-manager' ); ?></p>
								<p><?php _e( 'They are set to expire after 4 weeks OR when a match is added/updated, whichever is sooner. You can use the button below to manually clear all WP Club Manager transients in the cache. Each transient will be reset when the next person views that widget or shortcode.', 'wp-club-manager' ); ?></p>
							</td>
						</tr>
						<tr>
							<td class="clear-transients">
								<form id="wpcm-form" action="" method="POST">
									<p>
										<input type="submit" name="wpcm-submit" id="wpcm_submit" class="button secondary" value="<?php _e('Clear the WPCM transients cache', 'wp-club-manager'); ?>"/>
										<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="wpcm_loading" style="display:none;"/>
									</p>
								</form>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include 'html-admin-sidebar.php'; ?>
		</div>
	</div>
</div>

<?php do_action( 'wpclubmanager_admin_tools' ); ?>