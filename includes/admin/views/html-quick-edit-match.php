<?php
/**
 * Admin View: Quick Edit Match
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-left">

	<legend class="inline-edit-legend"><?php _e( 'Quick Edit', 'wp-club-manager' ); ?></legend>
	
	<div id="wpclubmanager-fields" class="inline-edit-col">

		<?php do_action( 'wpclubmanager_match_quick_edit_left_start' ); ?>

        <div class="match_fields">

			<?php if ( is_club_mode() ) : ?>
				<label class="alignleft">
					<span class="title"><?php _e( 'Team', 'wp-club-manager' ); ?></span>
					<span class="input-text-wrap">
						<select class="team" name="wpcm_team" id="post_team">
							<?php
								foreach ( $teams as $key => $value ) {
									echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
								}
							?>
						</select>
					</span>
				</label>
				<br class="clear" />
			<?php endif; ?>

			<label class="alignleft">
				<span class="title"><?php _e( 'Competition', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<select class="team" name="wpcm_comp" id="post_comp">
						<?php
							foreach ( $comps as $key => $value ) {
								echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
							}
						?>
					</select>
				</span>
			</label>

			<label class="alignleft friendly">
                <input type="checkbox" name="wpcm_friendly" value="1">
                <span class="checkbox-title"><?php _e( 'Friendly?', 'wp-club-manager' ); ?></span>
            </label>
			<br class="clear" />

			<label class="alignleft">
				<span class="title"><?php _e( 'Season', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<select class="team" name="wpcm_season" id="post_season">
						<?php
							foreach ( $seasons as $key => $value ) {
								echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
							}
						?>
					</select>
				</span>
			</label>
			<br class="clear" />

			<label class="alignleft">
				<span class="title"><?php _e( 'Venue', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<select class="venue" name="wpcm_venue" id="post_venue">
						<?php
							foreach ( $venues as $key => $value ) {
								echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
							}
						?>
					</select>
				</span>
			</label>
			<br class="clear" />

			<label class="alignleft">
				<span class="title"><?php _e( 'Referee', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="wpcm_referee" class="text referee" value="">
				</span>
			</label>
			<br class="clear" />

			<label class="alignleft">
				<span class="title"><?php _e( 'Attendance', 'wp-club-manager' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="wpcm_attendance" class="text attendance" value="">
				</span>
			</label>
			<br class="clear" />

		</div>

		<?php do_action( 'wpclubmanager_match_quick_edit_left_end' ); ?>

		<input type="hidden" name="wpclubmanager_quick_edit" value="1" />
		<input type="hidden" name="wpclubmanager_quick_edit_nonce" value="<?php echo wp_create_nonce( 'wpclubmanager_quick_edit_nonce' ); ?>" />
	</div>
</fieldset>

<fieldset class="inline-edit-col-right">

	<div id="wpclubmanager-fields" class="inline-edit-col">

		<?php do_action( 'wpclubmanager_match_quick_edit_right_start' ); ?>

		<div class="result">

            <label class="alignleft played">
                <input type="checkbox" name="wpcm_played" value="1">
                <span class="checkbox-title"><?php _e( 'Played?', 'wp-club-manager' ); ?></span>
            </label>
			<br class="clear" />
			
			<table>
				<thead>
					<tr>
						<td>&nbsp;</td>
						<th><?php _ex( 'Home', 'team', 'wp-club-manager' ); ?></th>
						<th><?php _ex( 'Away', 'team', 'wp-club-manager' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php _e( 'Score', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_goals[total][home]" value="" size="3" /></td>
						<td><input type="text" name="wpcm_goals[total][away]" value="" size="3" /></td>
					</tr>
				</tbody>
			</table>

		</div>

		<?php do_action( 'wpclubmanager_match_quick_edit_right_end' ); ?>

		<input type="hidden" name="wpclubmanager_quick_edit" value="1" />
		<input type="hidden" name="wpclubmanager_quick_edit_nonce" value="<?php echo wp_create_nonce( 'wpclubmanager_quick_edit_nonce' ); ?>" />
	</div>
</fieldset>
