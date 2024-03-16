<?php
/**
 * Admin View: Bulk Edit Matches
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-right">
	<div id="wpclubmanager-fields-bulk" class="inline-edit-col">

		<h4><?php esc_html_e( 'Match Details', 'wp-club-manager' ); ?></h4>

		<?php do_action( 'wpclubmanager_wpcm_match_bulk_edit_start' ); ?>

		<label>
			<span class="title"><?php _esc_html_ee( 'Team', 'wp-club-manager' ); ?></span>
			<span class="input-text-wrap">
				<select class="team" name="_team">
					<option value=""><?php esc_html_e( '— No Change —', 'wp-club-manager' ); ?></option>
				<?php
				foreach ( $teams as $key => $value ) {
					echo '<option value="' . esc_attr( $value->slug ) . '">' . esc_html( $value->name ) . '</option>';
				}
				?>
				</select>
			</span>
		</label>

		<?php do_action( 'wpclubmanager_wpcm_match_bulk_edit_end' ); ?>

		<input type="hidden" name="wpclubmanager_bulk_edit" value="1" />
		<input type="hidden" name="wpclubmanager_bulk_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpclubmanager_bulk_edit_nonce' ) ); ?>" />
	</div>
</fieldset>
