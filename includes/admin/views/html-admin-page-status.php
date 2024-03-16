<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap wpclubmanager-status">
	<h2>
		<?php esc_html_e( 'WP Club Manager Status', 'wp-club-manager' ); ?>
	</h2>

	<div class="error wpclubmanager-message">
		<p><?php esc_html_e( 'Please copy and paste this information in your ticket when contacting support:', 'wp-club-manager' ); ?> </p>
		<p class="submit"><a href="#" class="button-primary debug-report"><?php esc_html_e( 'Get System Report', 'wp-club-manager' ); ?></a>
		</p>
		<div id="debug-report">
			<textarea readonly="readonly"></textarea>
			<p class="submit"><button id="copy-for-support" class="button-primary" href="#"><?php esc_html_e( 'Copy for Support', 'wp-club-manager' ); ?></button></p>
		</div>
	</div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<div id="postbox-container-2" class="postbox-container">

				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="2" data-export-label="WordPress Environment"><?php esc_html_e( 'WordPress Environment', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-export-label="Home URL"><?php esc_html_e( 'Home URL', 'wp-club-manager' ); ?>:</td>
							<td><?php esc_html( form_option( 'home' ) ); ?></td>
						</tr>
						<tr>
							<td data-export-label="Site URL"><?php esc_html_e( 'Site URL', 'wp-club-manager' ); ?>:</td>
							<td><?php esc_url( form_option( 'siteurl' ) ); ?></td>
						</tr>
						<tr>
							<td data-export-label="WPCM Version"><?php esc_html_e( 'WPCM Version', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( WPCM()->version ); ?></td>
						</tr>
						<tr>
							<td data-export-label="WP Version"><?php esc_html_e( 'WP Version', 'wp-club-manager' ); ?>:</td>
							<td><?php esc_html( bloginfo( 'version' ) ); ?></td>
						</tr>
						<tr>
							<td data-export-label="WP Multisite"><?php esc_html_e( 'WP Multisite', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
							if ( is_multisite() ) {
								echo '<mark class="yes">&#10003;</mark>';
							} else {
								echo '<mark class="no">&#10007;</mark>';
							}
							?>
							</td>
						</tr>
						<tr>
							<td data-export-label="WP Memory Limit"><?php esc_html_e( 'WP Memory Limit', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
								$memory = wpcm_let_to_num( WP_MEMORY_LIMIT );

							if ( $memory < 67108864 ) {
								/* translators: 1: memory limit 2: URL to learn how to increase memory limit */
								echo '<mark class="error">' . wp_kses_post( sprintf( __( '%1$s - We recommend setting memory to at least 64MB. See: <a href="%2$s" target="_blank">Increasing memory allocated to PHP</a>', 'wp-club-manager' ), esc_html( size_format( $memory ) ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) ) . '</mark>';
							} else {
								echo '<mark class="yes">' . esc_html( size_format( $memory ) ) . '</mark>';
							}
							?>
							</td>
						</tr>
						<tr>
							<td data-export-label="WP Debug Mode"><?php esc_html_e( 'WP Debug Mode', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								echo '<mark class="yes">&#10003;</mark>';
							} else {
								echo '<mark class="no">&#10007;</mark>';
							}
							?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Language"><?php esc_html_e( 'Language', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( get_locale() ); ?></td>
						</tr>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="2" data-export-label="Server Environment"><?php esc_html_e( 'Server Environment', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-export-label="Server Info"><?php esc_html_e( 'Server Info', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); // phpcs:ignore ?></td>
						</tr>
						<tr>
							<td data-export-label="PHP Version"><?php esc_html_e( 'PHP Version', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
								// Check if phpversion function exists
							if ( function_exists( 'phpversion' ) ) {
								$php_version = phpversion();

								if ( version_compare( $php_version, '5.4', '<' ) ) {
									/* translators: 1: php version 2: link to update php */
									echo '<mark class="error">' . wp_kses_post( sprintf( __( '%1$s - We recommend a minimum PHP version of 5.4. See: <a href="%2$s" target="_blank">How to update your PHP version</a>', 'wp-club-manager' ), esc_html( $php_version ), 'http://docs.woothemes.com/document/how-to-update-your-php-version/' ) ) . '</mark>';
								} else {
									echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
								}
							} else {
								esc_html_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'wp-club-manager' );
							}
							?>
								</td>
						</tr>
						<?php if ( function_exists( 'ini_get' ) ) : ?>
							<tr>
								<td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP Post Max Size', 'wp-club-manager' ); ?>:</td>
								<td><?php echo esc_html( size_format( wpcm_let_to_num( ini_get( 'post_max_size' ) ) ) ); ?></td>
							</tr>
							<tr>
								<td data-export-label="PHP Time Limit"><?php esc_html_e( 'PHP Time Limit', 'wp-club-manager' ); ?>:</td>
								<td><?php echo esc_html( ini_get( 'max_execution_time' ) ); ?></td>
							</tr>
							<tr>
								<td data-export-label="PHP Max Input Vars"><?php esc_html_e( 'PHP Max Input Vars', 'wp-club-manager' ); ?>:</td>
								<td><?php echo esc_html( ini_get( 'max_input_vars' ) ); ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td data-export-label="MySQL Version"><?php esc_html_e( 'MySQL Version', 'wp-club-manager' ); ?>:</td>
							<td>
								<?php
								/** @global wpdb $wpdb */
								global $wpdb;
								echo esc_html( $wpdb->db_version() );
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Max Upload Size"><?php esc_html_e( 'Max Upload Size', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></td>
						</tr>
						<tr>
							<td data-export-label="Default Timezone is UTC"><?php esc_html_e( 'Default Timezone is UTC', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
								$default_timezone = date_default_timezone_get();
							if ( 'UTC' !== $default_timezone ) {
								/* translators: 1: timezone */
								echo '<mark class="error">&#10005; ' . sprintf( esc_html__( 'Default timezone is %s - it should be UTC', 'wp-club-manager' ), esc_html( $default_timezone ) ) . '</mark>';
							} else {
								echo '<mark class="yes">&#10003;</mark>';
							}
							?>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="3" data-export-label="Active Plugins (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)"><?php esc_html_e( 'Active Plugins', 'wp-club-manager' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$active_plugins = (array) get_option( 'active_plugins', array() );

						if ( is_multisite() ) {
							$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
						}

						foreach ( $active_plugins as $active_plugin ) {

							$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $active_plugin );
							$dirname        = dirname( $active_plugin );
							$version_string = '';
							$network_string = '';

							if ( ! empty( $plugin_data['Name'] ) ) {

								// link the plugin name to the plugin url if available
								$plugin_name = esc_html( $plugin_data['Name'] );

								if ( ! empty( $plugin_data['PluginURI'] ) ) {
									$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage', 'wp-club-manager' ) . '" target="_blank">' . $plugin_name . '</a>';
								}
								?>
								<tr>
									<td><?php echo wp_kses_post( $plugin_name ); ?></td>
									<td class="help">&nbsp;</td>
									<td>
										<?php
										/* translators: 1: author name */
										echo sprintf( esc_html_x( 'by %s', 'by author', 'wp-club-manager' ), wp_kses_post( $plugin_data['Author'] ) ) . ' &ndash; ' . esc_html( $plugin_data['Version'] . $version_string . $network_string );
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="2" data-export-label="Taxonomies"><?php esc_html_e( 'Taxonomies', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-export-label="Teams"><?php esc_html_e( 'Teams', 'wp-club-manager' ); ?>:</td>
							<td>
								<?php
								$display_terms = array();
								$all_terms     = get_terms(
									array(
										'taxonomy'   => 'wpcm_team',
										'hide_empty' => false,
									)
								);
								foreach ( $all_terms as $all_term ) {
									$display_terms[] = strtolower( $all_term->name ) . ' (' . $all_term->slug . ')';
								}
								echo implode( ', ', array_map( 'esc_html', $display_terms ) );
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Seasons"><?php esc_html_e( 'Seasons', 'wp-club-manager' ); ?>:</td>
							<td>
								<?php
								$display_terms = array();
								$terms         = get_terms(
									array(
										'taxonomy'   => 'wpcm_season',
										'hide_empty' => false,
									)
								);
								foreach ( $terms as $all_term ) {
									$display_terms[] = strtolower( $all_term->name ) . ' (' . $all_term->slug . ')';
								}
								echo implode( ', ', array_map( 'esc_html', $display_terms ) );
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Competitions"><?php esc_html_e( 'Competitions', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
							$display_terms = array();
							$all_terms     = get_terms(
								array(
									'taxonomy'   => 'wpcm_comp',
									'hide_empty' => false,
								)
							);
							foreach ( $all_terms as $all_term ) {
								$display_terms[] = strtolower( $all_term->name ) . ' (' . $all_term->slug . ')';
							}
								echo implode( ', ', array_map( 'esc_html', $display_terms ) );
							?>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="3" data-export-label="Theme"><?php esc_html_e( 'Theme', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
						<?php $active_theme = wp_get_theme(); ?>
					<tbody>
						<tr>
							<td data-export-label="Name"><?php esc_html_e( 'Name', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $active_theme->Name ); // phpcs:ignore ?></td>
						</tr>
						<tr>
							<td data-export-label="Version"><?php esc_html_e( 'Version', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $active_theme->Version); // phpcs:ignore ?></td>
						</tr>
						<tr>
							<td data-export-label="Author URL"><?php esc_html_e( 'Author URL', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_url( $active_theme->{'Author URI'} ); ?></td>
						</tr>
						<tr>
							<td data-export-label="Child Theme"><?php esc_html_e( 'Child Theme', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
								echo is_child_theme() ? '<mark class="yes">&#10003;</mark>' : '<mark class="no">&#10007;</mark>';
							?>
							</td>
						</tr>
						<?php
						if ( is_child_theme() ) :
							$parent_theme = wp_get_theme( $active_theme->Template ) // phpcs:ignore;
							?>
						<tr>
							<td data-export-label="Parent Theme Name"><?php esc_html_e( 'Parent Theme Name', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $parent_theme->Name ); // phpcs:ignore ?></td>
						</tr>
						<tr>
							<td data-export-label="Parent Theme Version"><?php esc_html_e( 'Parent Theme Version', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $parent_theme->Version ); // phpcs:ignore ?></td>
						</tr>
						<tr>
							<td data-export-label="Parent Theme Author URL"><?php esc_html_e( 'Parent Theme Author URL', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_url( $parent_theme->{'Author URI'} ); ?></td>
						</tr>
						<?php endif ?>
						<tr>
							<td data-export-label="WP Club Manager Support"><?php esc_html_e( 'WP Club Manager Support', 'wp-club-manager' ); ?>:</td>
							<td>
							<?php
							if ( ! current_theme_supports( 'wpclubmanager' ) && ! in_array( $active_theme->template, wpcm_get_core_supported_themes() ) ) {
								echo '<mark class="error">' . esc_html__( 'Not Declared', 'wp-club-manager' ) . '</mark>';
							} else {
								echo '<mark class="yes">&#10003;</mark>';
							}
							?>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="3" data-export-label="WP Club Manager Settings"><?php esc_html_e( 'WP Club Manager Settings', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$default_club = get_default_club();
							$sport        = get_option( 'wpcm_sport' );
						?>
						<tr>
							<td data-export-label="Preset Sport"><?php esc_html_e( 'Preset Sport', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( $sport ); ?></td>
						</tr>
						<tr>
							<td data-export-label="Default Club"><?php esc_html_e( 'Default Club', 'wp-club-manager' ); ?>:</td>
							<td><?php echo esc_html( get_the_title( $default_club ) ); ?></td>
						</tr>
					</tbody>
				</table>
				<table class="wpcm_status_table widefat" cellspacing="0" id="status">
					<thead>
						<tr>
							<th colspan="3" data-export-label="Templates"><?php esc_html_e( 'Templates', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php

							$template_paths     = apply_filters( 'wpclubmanager_template_overrides_scan_paths', array( 'WP Club Manager' => WPCM()->plugin_path() . '/templates/' ) );
							$scanned_files      = array();
							$found_files        = array();
							$outdated_templates = false;

						foreach ( $template_paths as $plugin_name => $template_path ) {
							$scanned_files[ $plugin_name ] = WPCM_Admin_Status::scan_template_files( $template_path );
						}

						foreach ( $scanned_files as $plugin_name => $files ) {
							foreach ( $files as $file ) {
								if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
									$theme_file = get_stylesheet_directory() . '/' . $file;
								} elseif ( file_exists( get_stylesheet_directory() . '/wpclubmanager/' . $file ) ) {
									$theme_file = get_stylesheet_directory() . '/wpclubmanager/' . $file;
								} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
									$theme_file = get_template_directory() . '/' . $file;
								} elseif ( file_exists( get_template_directory() . '/wpclubmanager/' . $file ) ) {
									$theme_file = get_template_directory() . '/wpclubmanager/' . $file;
								} else {
									$theme_file = false;
								}

								if ( $theme_file ) {
									$core_version  = WPCM_Admin_Status::get_file_version( WPCM()->plugin_path() . '/templates/' . $file );
									$theme_version = WPCM_Admin_Status::get_file_version( $theme_file );

									if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
										if ( ! $outdated_templates ) {
											$outdated_templates = true;
										}
										/* translators: 1: theme url 2: theme version 3: core plugin version */
										$found_files[ $plugin_name ][] = sprintf( __( '<code>%1$s</code> version <strong style="color:red">%2$s</strong> is out of date. The core version is %3$s', 'wp-club-manager' ), str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ), $theme_version ? $theme_version : '-', $core_version );
									} else {
										$found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ) );
									}
								}
							}
						}

						if ( $found_files ) {
							foreach ( $found_files as $plugin_name => $found_plugin_files ) {
								?>
									<tr>
										<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'wp-club-manager' ); ?> (<?php echo esc_html( $plugin_name ); ?>):</td>
										<td><?php echo wp_kses_post( implode( ', <br/>', $found_plugin_files ) ); ?></td>
									</tr>
									<?php
							}
						} else {
							?>
								<tr>
									<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'wp-club-manager' ); ?>:</td>
									<td>&ndash;</td>
								</tr>
								<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<?php require 'html-admin-sidebar.php'; ?>
		</div>
	</div>
</div>

<?php do_action( 'wpclubmanager_system_status' ); ?>

<script type="text/javascript">

	jQuery( 'a.debug-report' ).click( function() {

		var report = '';

		jQuery( '#status thead, #status tbody' ).each(function(){

			if ( jQuery( this ).is('thead') ) {

				var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
				report = report + "\n### " + jQuery.trim( label ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this ) ).each(function(){

					var label       = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
					var the_name    = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML
					var the_value   = jQuery.trim( jQuery( this ).find( 'td:eq(1)' ).text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ) {

						// If value have a list of plugins ','
						// Split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function( key, line ){
							temp_line = temp_line + line + '\n';
						});

						the_value = temp_line;
					}

					report = report + '' + the_name + ': ' + the_value + "\n";
				});

			}
		});

		try {
			jQuery( "#debug-report" ).slideDown();
			jQuery( "#debug-report textarea" ).val( report ).focus().select();
			jQuery( this ).fadeOut();
			return false;
		} catch( e ){
			console.log( e );
		}

		return false;
	});

	jQuery( document ).ready( function ( $ ) {
		$( 'body' ).on( 'copy', '#copy-for-support', function ( e ) {
			e.clipboardData.clearData();
			e.clipboardData.setData( 'text/plain', $( '#debug-report textarea' ).val() );
			e.preventDefault();
		});

	});

</script>
