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

		<h4><?php _e( 'Match Details', 'wp-club-manager' ); ?></h4>

		<?php do_action( 'wpclubmanager_wpcm_match_bulk_edit_start' ); ?>

		<label>
			<span class="title"><?php _e( 'Team', 'wp-club-manager' ); ?></span>
			<span class="input-text-wrap">
				<select class="team" name="_team">
					<option value=""><?php _e( '— No Change —', 'wp-club-manager' ); ?></option>
				<?php
					foreach ( $teams as $key => $value ) {
						echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
					}
				?>
				</select>
			</span>
		</label>

		<?php do_action( 'wpclubmanager_wpcm_match_bulk_edit_end' ); ?>

		<input type="hidden" name="wpclubmanager_bulk_edit" value="1" />
		<input type="hidden" name="wpclubmanager_bulk_edit_nonce" value="<?php echo wp_create_nonce( 'wpclubmanager_bulk_edit_nonce' ); ?>" />
	</div>
</fieldset>