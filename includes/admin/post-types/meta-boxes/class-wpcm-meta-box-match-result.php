<?php
/**
 * Match Result
 *
 * Displays the match result box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.5.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Result {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$sport = get_option('wpcm_sport');

		$played = get_post_meta( $post->ID, 'wpcm_played', true );
		$friendly = get_post_meta( $post->ID, 'wpcm_friendly', true );

		if( $sport == 'cricket' ){
			$wpcm_match_runs = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_runs', true ) ) );
			$wpcm_match_extras = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_extras', true ) ) );
			$wpcm_match_wickets = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_wickets', true ) ) );
			$wpcm_match_overs = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_overs', true ) ) );
		}else{
			$goals = array_merge( array( 'total' => array( 'home' => 0, 'away' => 0	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
		}

		if ( $sport !== 'volleyball' || $sport !== 'baseball' ) {
			$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
		}
		if ( $sport == 'hockey' || $sport == 'handball' ) {
			$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );
		}

		if ( $sport == 'soccer' ) {
			$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );
			$shootout_score = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_shootout_score', true ) ) );
		}

		if( $sport == 'rugby' ){
			$bonus = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_bonus', true ) ) );
		}

		if( $sport == 'gaelic' ){
			$gaa_goals = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_gaa_goals', true ) ) );
			$gaa_points = array_merge( array( 'home' => 0, 'away' => 0	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_gaa_points', true ) ) );
		} ?>

		<p>
			<label class="selectit">
				<input type="checkbox" name="wpcm_played" id="wpcm_played" value="1" <?php checked( true, $played ); ?> />
				<?php _e( 'Result', 'wp-club-manager' ); ?>
			</label>
		</p>
		<div id="results-table">
			<?php if ( $sport == 'cricket') { ?>

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
							<th align="right"><?php _e( 'Runs', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_match_runs[home]" id="wpcm_match_runs_home" value="<?php echo (int)$wpcm_match_runs['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_match_runs[away]" id="wpcm_match_runs_away" value="<?php echo (int)$wpcm_match_runs['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( 'Extras', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_match_extras[home]" id="wpcm_match_extras_home" value="<?php echo (int)$wpcm_match_extras['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_match_extras[away]" id="wpcm_match_extras_away" value="<?php echo (int)$wpcm_match_extras['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( 'Wickets', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_match_wickets[home]" id="wpcm_match_wickets_home" value="<?php echo (int)$wpcm_match_wickets['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_match_wickets[away]" id="wpcm_match_wickets_away" value="<?php echo (int)$wpcm_match_wickets['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( 'Overs', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_match_overs[home]" id="wpcm_match_overs_home" value="<?php echo (float)$wpcm_match_overs['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_match_overs[away]" id="wpcm_match_overs_away" value="<?php echo (float)$wpcm_match_overs['away']; ?>" size="3" /></td>
						</tr>
					</tbody>
				</table>

			<?php }else{ ?>

				<table>
					<thead>
						<tr>
							<td>&nbsp;</td>
							<th><?php _ex( 'Home', 'team', 'wp-club-manager' ); ?></th>
							<th><?php _ex( 'Away', 'team', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action('wpclubmanager_admin_results_table', $post->ID ); ?>
						<tr>
							<th align="right"><?php _e( 'Score', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[total][home]" id="wpcm_goals_total_home" value="<?php echo (int)$goals['total']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[total][away]" id="wpcm_goals_total_away" value="<?php echo (int)$goals['total']['away']; ?>" size="3" /></td>
						</tr>
					</tbody>
				</table>

			<?php } ?>

			<?php if ( $sport == 'rugby') { ?>

				<table class="wpcm-results-bonus">
					<tbody>
						<tr>
							<th align="right"><?php _e( 'Bonus Points', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_bonus[home]" id="wpcm_bonus_home" value="<?php echo (int)$bonus['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_bonus[away]" id="wpcm_bonus_away" value="<?php echo (int)$bonus['away']; ?>" size="3" /></td>
						</tr>
					</tbody>
				</table>

			<?php } ?>

			<?php if ( $sport == 'gaelic' ) { ?>

				<table class="wpcm-results-gaelic">
					<tbody>
						<tr>
							<th align="right"><?php _e( 'Goals', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_gaa_goals[home]" id="wpcm_gaa_goals_home" value="<?php echo (int)$gaa_goals['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_gaa_goals[away]" id="wpcm_gaa_goals_away" value="<?php echo (int)$gaa_goals['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( 'Points', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_gaa_points[home]" id="wpcm_gaa_points_home" value="<?php echo (int)$gaa_points['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_gaa_points[away]" id="wpcm_gaa_points_away" value="<?php echo (int)$gaa_points['away']; ?>" size="3" /></td>
						</tr>
					</tbody>
				</table>

			<?php } ?>

			<?php if ( $sport !== 'cricket' && $sport !== 'soccer' && $sport !== 'volleyball' && $sport !== 'baseball' ) { ?>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_overtime" id="wpcm_overtime" value="1" <?php checked( true, $overtime ); ?> />
						<?php _e( 'Overtime', 'wp-club-manager' ); ?>
					</label>
				</p>

			<?php } ?>

			<?php if ( $sport == 'hockey' || $sport == 'handball' ) { ?>
				
				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_shootout" id="wpcm_shootout" value="1" <?php checked( true, $shootout ); ?> />
						<?php _e( 'Shootout', 'wp-club-manager' ); ?>
					</label>
				</p>

			<?php } ?>

			<?php if ( $sport == 'soccer' ) { ?>
				
				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_overtime" id="wpcm_overtime" value="1" <?php checked( true, $overtime ); ?> />
						<?php _e( 'Extra Time', 'wp-club-manager' ); ?>
					</label>
				</p>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_shootout" id="wpcm_shootout" value="1" <?php checked( true, $shootout ); ?> />
						<?php _e( 'Penalties', 'wp-club-manager' ); ?>
					</label>
				</p>

				<table class="wpcm-results-shootout">
					<tbody>
						<tr>
							<th align="right"><?php _e( 'Score', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_shootout_score[home]" id="wpcm_shootout_home" value="<?php echo (int)$shootout_score['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_shootout_score[away]" id="wpcm_shootout_away" value="<?php echo (int)$shootout_score['away']; ?>" size="3" /></td>
						</tr>
					</tbody>
				</table>

			<?php } ?>

		</div>
		<p>
			<label class="selectit">
				<input type="checkbox" name="wpcm_friendly" id="wpcm_friendly" value="1" <?php checked( true, $friendly ); ?> />
				<?php _e( 'Friendly', 'wp-club-manager' ); ?>
			</label>
		</p>

		<?php do_action('wpclubmanager_admin_after_results_table', $post->ID );

	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$sport = get_option('wpcm_sport');

		if( isset( $_POST['wpcm_played'] ) ) {
			update_post_meta( $post_id, 'wpcm_played', $_POST['wpcm_played'] );
		}
		if( isset( $_POST['wpcm_friendly'] ) ) {
			update_post_meta( $post_id, 'wpcm_friendly', $_POST['wpcm_friendly'] );
		}
		if( isset( $_POST['wpcm_goals'] ) ) {
			$goals = $_POST['wpcm_goals'];
			update_post_meta( $post_id, 'wpcm_goals', serialize( $goals ) );
			update_post_meta( $post_id, 'wpcm_home_goals', $goals['total']['home'] );
			update_post_meta( $post_id, 'wpcm_away_goals', $goals['total']['away'] );
		}

		if ( $sport == 'rugby' && isset( $_POST['wpcm_bonus'] ) ) {
			$bonus = $_POST['wpcm_bonus'];
			update_post_meta( $post_id, 'wpcm_bonus', serialize( $bonus ) );
			update_post_meta( $post_id, 'wpcm_home_bonus', $bonus['home'] );
			update_post_meta( $post_id, 'wpcm_away_bonus', $bonus['away'] );
		}
		if ( $sport !== 'volleyball' && isset( $_POST['wpcm_overtime'] ) || $sport !== 'baseball' && isset( $_POST['wpcm_overtime'] ) ) {
			update_post_meta( $post_id, 'wpcm_overtime', $_POST['wpcm_overtime'] );
		}
		if ( $sport == 'hockey' && isset( $_POST['wpcm_shootout'] ) || $sport == 'handball' && isset( $_POST['wpcm_shootout'] ) ) {
			update_post_meta( $post_id, 'wpcm_shootout', $_POST['wpcm_shootout'] );
		}
		if ( $sport == 'soccer' && isset( $_POST['wpcm_shootout'] ) ) {
			update_post_meta( $post_id, 'wpcm_shootout', $_POST['wpcm_shootout'] );
			$shootout_score = $_POST['wpcm_shootout_score'];
			update_post_meta( $post_id, '_wpcm_shootout_score', serialize( $shootout_score ) );
			update_post_meta( $post_id, '_wpcm_home_shootout_goals', $shootout_score['home'] );
			update_post_meta( $post_id, '_wpcm_away_shootout_goals', $shootout_score['away'] );
		}
		if ( $sport == 'gaelic' ) {
			if( isset( $_POST['wpcm_gaa_goals'] ) ) {
				$gaa_goals = $_POST['wpcm_gaa_goals'];
				update_post_meta( $post_id, 'wpcm_gaa_goals', serialize( $gaa_goals ) );
				update_post_meta( $post_id, 'wpcm_home_gaa_goals', $gaa_goals['home'] );
				update_post_meta( $post_id, 'wpcm_away_gaa_goals', $gaa_goals['away'] );
			}
			if( isset( $_POST['wpcm_gaa_points'] ) ) {
				$gaa_points = $_POST['wpcm_gaa_points'];
				update_post_meta( $post_id, 'wpcm_gaa_points', serialize( $gaa_points ) );
				update_post_meta( $post_id, 'wpcm_home_gaa_points', $gaa_points['home'] );
				update_post_meta( $post_id, 'wpcm_away_gaa_points', $gaa_points['away'] );
			}
		}
		if ( $sport == 'cricket' ) {
			if( isset( $_POST['wpcm_match_runs'] ) ) {
				$wpcm_match_runs = $_POST['wpcm_match_runs'];
				update_post_meta( $post_id, '_wpcm_match_runs', serialize( $wpcm_match_runs ) );
			}
			if( isset( $_POST['wpcm_match_extras'] ) ) {
				$wpcm_match_extras = $_POST['wpcm_match_extras'];
				update_post_meta( $post_id, '_wpcm_match_extras', serialize( $wpcm_match_extras ) );
			}
			if( isset( $_POST['wpcm_match_wickets'] ) ) {
				$wpcm_match_wickets = $_POST['wpcm_match_wickets'];
				update_post_meta( $post_id, '_wpcm_match_wickets', serialize( $wpcm_match_wickets ) );
			}
			if( isset( $_POST['wpcm_match_overs'] ) ) {
				$wpcm_match_overs = $_POST['wpcm_match_overs'];
				update_post_meta( $post_id, '_wpcm_match_overs', serialize( $wpcm_match_overs ) );
			}
		}

		do_action( 'delete_plugin_transients' );
	}
}