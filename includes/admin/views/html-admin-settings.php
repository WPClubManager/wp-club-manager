<div class="wrap wpclubmanager">
	<h2>WP Club Manager Settings</h2>
	<form method="post" id="mainform" action="" enctype="multipart/form-data">
		<h2 class="nav-tab-wrapper wpcm-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label )
					echo '<a href="' . admin_url( 'admin.php?page=wpcm-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

				do_action( 'wpclubmanager_settings_tabs' );
			?>
		</h2>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

				<div id="postbox-container-2" class="postbox-container">

					<?php
						do_action( 'wpclubmanager_settings_' . $current_tab );
					?>

					<p class="submit">
			        	<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
			        		<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wpclubmanager' ); ?>" />
			        	<?php endif; ?>
			        	<input type="hidden" name="subtab" id="last_tab" />
			        	<?php wp_nonce_field( 'wpclubmanager-settings' ); ?>
		        	</p>

				</div>

				<div id="postbox-container-1" class="postbox-container">

					<div id="wpcm-support" class="stuffbox ">
						<h3>WP Club Manager Themes</h3>
						<div class="inside">
							<p>Improve your club site with our purpose built themes! Feature packed, responsive and highly customizable, <a href="https://wpclubmanager.com/themes/scoreline/" target="_blank">Scoreline</a> is our first theme ready-made for WP Club Manager.</p>
							<p><a href="https://wpclubmanager.com/themes/scoreline/" target="_blank">Check it out!</a></p>
						</div>
					</div>

					<div id="wpcm-support" class="stuffbox ">
						<h3>Looking for support?</h3>
						<div class="inside">
							<p>Make sure to have a look at the plugin <a href="http://wpclubmanager.com/docs/" target="_blank">documentation</a> or ask any questions in our <a href="http://wpclubmanager.com/support/" target="_blank">support forums</a>.</p>
						</div>
					</div>
					
					<div id="wpcm-like-plugin" class="stuffbox ">
						<h3>Like this plugin?</h3>
						<div class="inside">
							<p>Please consider showing your appreciation by helping to spread the word.</p>
							<ul class="ul-disc">
								<li><a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/wp-club-manager?rate=5#postform">Leave a &#9733;&#9733;&#9733;&#9733;&#9733; plugin review on WordPress.org</a></li>
								<li><a target="_blank" href="http://twitter.com/?status=Showing%20my%20appreciation%20for%20WordPress%20plugin%3A%20WP%20Club%20Manager%20%20-%20check%20it%20out!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Fwp-club-manager%2F">Tweet about WP Club Manager</a></li>
								<li><a target="_blank" href="http://wordpress.org/plugins/wp-club-manager/">Vote "works" on the WordPress.org plugin page</a></li>
							</ul>
							<p>You can also follow WP Club Manager on Twitter <a href="https://twitter.com/WPClubManager" target="_blank">here</a>.</p>
						</div>
					</div>

				</div>

			</div>

		</div>

	</form>
</div>