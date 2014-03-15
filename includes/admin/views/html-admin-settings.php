<div class="wrap wpclubmanager">
	<form method="post" id="mainform" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-wpclubmanager-settings" id="icon-wpclubmanager"><br /></div><h2 class="nav-tab-wrapper wpcm-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label )
					echo '<a href="' . admin_url( 'admin.php?page=wpcm-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

				do_action( 'wpclubmanager_settings_tabs' );
			?>
		</h2>

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
	</form>
</div>