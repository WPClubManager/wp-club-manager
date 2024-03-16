<?php
/**
 * Admin View: Quick Edit Staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-left">

	<legend class="inline-edit-legend"><?php esc_html_e( 'Quick Edit', 'wp-club-manager' ); ?></legend>

	<div id="wpclubmanager-fields" class="inline-edit-col">

		<?php do_action( 'wpclubmanager_staff_quick_edit_left_start' ); ?>

		<div class="staff_fields">

			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'First Name', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_wpcm_firstname" class="text fname" value="">
				</span>
			</label>

			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Last Name', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_wpcm_lastname" class="text lname" value="">
				</span>
			</label>
			<br>

			<?php if ( is_league_mode() ) : ?>
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Club', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<select class="staff_club" name="_wpcm_staff_club" id="post_club">
						<?php
						foreach ( $clubs as $key => $value ) {
							echo '<option value="' . esc_attr( $value->post_name ) . '">' . esc_html( $value->post_title ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>
				<br class="clear" />
			<?php endif; ?>

		</div>

		<?php do_action( 'wpclubmanager_staff_quick_edit_left_end' ); ?>

		<input type="hidden" name="wpclubmanager_quick_edit" value="1" />
		<input type="hidden" name="wpclubmanager_quick_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpclubmanager_quick_edit_nonce' ) ); ?>" />
	</div>
</fieldset>
