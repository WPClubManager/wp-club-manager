<?php
/**
 * Admin View: Quick Edit Match
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-left">
	<div id="wpclubmanager-fields" class="inline-edit-col">

		<h4><?php _e( 'Match Details', 'wp-club-manager' ); ?></h4>

		<?php do_action( 'wpclubmanager_wpcm_match_quick_edit_start' ); ?>

		<label class="alignleft">
			<span class="title"><?php _e( 'Team', 'wp-club-manager' ); ?></span>
			<span class="input-text-wrap">
				<select class="team" name="wpcm_team">
				<?php
					foreach ( $teams as $key => $value ) {
						echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
					}
				?>
				</select>
			</span>
		</label>

		<?php do_action( 'wpclubmanager_wpcm_match_quick_edit_end' ); ?>

		<input type="hidden" name="wpclubmanager_quick_edit" value="1" />
		<input type="hidden" name="wpclubmanager_quick_edit_nonce" value="<?php echo wp_create_nonce( 'wpclubmanager_quick_edit_nonce' ); ?>" />
	</div>
</fieldset>