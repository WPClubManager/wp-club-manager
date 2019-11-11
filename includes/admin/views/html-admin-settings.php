<div class="wrap wpclubmanager">
	<h1><?php _e( 'WP Club Manager Settings', 'wp-club-manager' ); ?></h1>
	<form method="<?php echo esc_attr( apply_filters( 'wpclubmanager_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<h2 class="nav-tab-wrapper wpcm-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label )
					echo '<a href="' . admin_url( 'admin.php?page=wpcm-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

				do_action( 'wpclubmanager_settings_tabs' );
			?>
		</h2>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-2">

				<div id="postbox-container-2" class="postbox-container">

					<?php
						do_action( 'wpclubmanager_sections_' . $current_tab );

						//self::show_messages();

						do_action( 'wpclubmanager_settings_' . $current_tab );
					?>

					<p class="submit">
			        	<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
			        		<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wp-club-manager' ); ?>" />
			        	<?php endif; ?>
			        	<input type="hidden" name="subtab" id="last_tab" />
			        	<?php wp_nonce_field( 'wpclubmanager-settings' ); ?>
		        	</p>

				</div>

				<?php include 'html-admin-sidebar.php'; ?>

			</div>

		</div>

	</form>
</div>