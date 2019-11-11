<?php
/**
 * Match Result
 *
 * Displays the match result box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.1.0
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
		$postponed = get_post_meta( $post->ID, '_wpcm_postponed', true );
		$walkover = get_post_meta( $post->ID, '_wpcm_walkover', true );

		if( $sport == 'cricket' ){
			$wpcm_match_runs = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_runs', true ) ) );
			$wpcm_match_extras = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_extras', true ) ) );
			$wpcm_match_wickets = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_wickets', true ) ) );
			$wpcm_match_overs = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, '_wpcm_match_overs', true ) ) );
			$wpcm_cricket_outcome = get_post_meta( $post->ID, '_wpcm_cricket_outcome', true );
			if( ! is_array($wpcm_cricket_outcome) ) {
				$wpcm_cricket_outcome = array( 0 => '', 1 => '', 2 => '' );
			};
		}else{
			$goals = array_merge( array( 'total' => array( 'home' => '0', 'away' => '0'	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
		}

		if ( $sport !== 'volleyball' || $sport !== 'baseball' ) {
			$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
		}
		if ( $sport == 'hockey' || $sport == 'handball' ) {
			$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );
		}

		if ( $sport == 'soccer' ) {
			$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );
			$shootout_score = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_shootout_score', true ) ) );
		}

		if( $sport == 'rugby' ){
			$bonus = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_bonus', true ) ) );
		}

		if( $sport == 'gaelic' ){
			$gaa_goals = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_gaa_goals', true ) ) );
			$gaa_points = array_merge( array( 'home' => '0', 'away' => '0'	), (array)unserialize( get_post_meta( $post->ID, 'wpcm_gaa_points', true ) ) );
		} ?>

		<p>
			<label class="selectit">
				<input type="checkbox" name="wpcm_played" id="wpcm_played" value="1" <?php checked( true, $played ); ?> />
				<?php _e( 'Result', 'wp-club-manager' ); ?>
			</label>
		</p>
		<p>
			<label class="selectit">
				<input type="checkbox" name="_wpcm_postponed" id="_wpcm_postponed" value="1" <?php checked( true, $postponed ); ?> />
				<?php _e( 'Postponed', 'wp-club-manager' ); ?>
			</label>
		</p>

		<?php wpclubmanager_wp_select( array( 'id' => '_wpcm_walkover', 'value' => $walkover, 'class' => 'chosen_select', 'label' => '', 'wrapper_class' => 'wpcm-postponed-result', 'options' => array(
			'' => __( 'To be rescheduled', 'wp-club-manager' ),
			'home_win' => __( 'Home win', 'wp-club-manager' ),
			'away_win' => __( 'Away win', 'wp-club-manager' )
		) ) ); ?>

		<div id="results-table">

		<?php
		if( get_option( 'wpcm_results_box_scores' ) == 'yes' ) { ?>

			<table class="box-scores-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<th><?php _ex( 'Home', 'team', 'wp-club-manager' ); ?></th>
						<th><?php _ex( 'Away', 'team', 'wp-club-manager' ); ?></th>
					</tr>
				</thead>
				<tbody>
							
					<?php
					if( $sport == 'volleyball' ) :

						$box_goals = array_merge( array( 'q1' => array( 'home' => '0', 'away' => '0'	) ), array( 'q2' => array( 'home' => '0', 'away' => '0'	) ), array( 'q3' => array( 'home' => '0', 'away' => '0'	) ), array( 'q4' => array( 'home' => '0', 'away' => '0'	) ), array( 'q5' => array( 'home' => '0', 'away' => '0'	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr>
							<th align="right"><?php _e( '1st Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home" value="<?php echo (int)$box_goals['q1']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away" value="<?php echo (int)$box_goals['q1']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '2nd Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home" value="<?php echo (int)$box_goals['q2']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away" value="<?php echo (int)$box_goals['q2']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '3rd Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home" value="<?php echo (int)$box_goals['q3']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away" value="<?php echo (int)$box_goals['q3']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '4th Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q4][home]" id="wpcm_goals_q4_home" value="<?php echo (int)$box_goals['q4']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q4][away]" id="wpcm_goals_q4_away" value="<?php echo (int)$box_goals['q4']['away']; ?>" size="3" /></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php _e( '5th Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q5][home]" id="wpcm_goals_q5_home" value="<?php echo (int)$box_goals['q5']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q5][away]" id="wpcm_goals_q5_away" value="<?php echo (int)$box_goals['q5']['away']; ?>" size="3" /></td>
						</tr>

					<?php
					elseif( $sport == 'basketball' || $sport == 'football' || $sport == 'footy' ) :

						$box_goals = array_merge( array( 'q1' => array( 'home' => '0', 'away' => '0'	) ), array( 'q2' => array( 'home' => '0', 'away' => '0'	) ), array( 'q3' => array( 'home' => '0', 'away' => '0'	) ), array( 'q4' => array( 'home' => '0', 'away' => '0'	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr>
							<th align="right"><?php _e( '1st Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home" value="<?php echo (int)$box_goals['q1']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away" value="<?php echo (int)$box_goals['q1']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '2nd Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home" value="<?php echo (int)$box_goals['q2']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away" value="<?php echo (int)$box_goals['q2']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '3rd Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home" value="<?php echo (int)$box_goals['q3']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away" value="<?php echo (int)$box_goals['q3']['away']; ?>" size="3" /></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php _e( '4th Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q4][home]" id="wpcm_goals_q4_home" value="<?php echo (int)$box_goals['q4']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q4][away]" id="wpcm_goals_q4_away" value="<?php echo (int)$box_goals['q4']['away']; ?>" size="3" /></td>
						</tr>

					<?php
					elseif( $sport == 'hockey' || $sport == 'floorball' ) :

						$box_goals = array_merge( array( 'q1' => array( 'home' => '0', 'away' => '0'	) ), array( 'q2' => array( 'home' => '0', 'away' => '0'	) ), array( 'q3' => array( 'home' => '0', 'away' => '0'	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr>
							<th align="right"><?php _e( '1st Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home" value="<?php echo (int)$box_goals['q1']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away" value="<?php echo (int)$box_goals['q1']['away']; ?>" size="3" /></td>
						</tr>
						<tr>
							<th align="right"><?php _e( '2nd Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home" value="<?php echo (int)$box_goals['q2']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away" value="<?php echo (int)$box_goals['q2']['away']; ?>" size="3" /></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php _e( '3rd Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home" value="<?php echo (int)$box_goals['q3']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away" value="<?php echo (int)$box_goals['q3']['away']; ?>" size="3" /></td>
						</tr>

					<?php
					else :

						$box_goals = array_merge( array( 'q1' => array( 'home' => '0', 'away' => '0'	) ), (array)unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php _e( 'Half Time', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home" value="<?php echo (int)$box_goals['q1']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away" value="<?php echo (int)$box_goals['q1']['away']; ?>" size="3" /></td>
						</tr>

					<?php
					endif; ?>

				</tbody>
			</table>

		<?php
		} ?>
			<table class="final-score-table">
				<?php
				if( get_option( 'wpcm_results_box_scores' ) != 'yes' ) { ?>
					<thead>
						<tr>
							<td>&nbsp;</td>
							<th><?php _ex( 'Home', 'team', 'wp-club-manager' ); ?></th>
							<th><?php _ex( 'Away', 'team', 'wp-club-manager' ); ?></th>
						</tr>
					</thead>
				<?php
				}
				if ( $sport == 'cricket') { ?>

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

				<?php
				} else { ?>

					<tbody>
						
						<?php do_action('wpclubmanager_admin_results_table', $post->ID ); ?>
						<tr>
							<th align="right"><?php _e( 'Final Score', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[total][home]" id="wpcm_goals_total_home" value="<?php echo (int)$goals['total']['home']; ?>" size="3" /></td>
							<td><input type="text" name="wpcm_goals[total][away]" id="wpcm_goals_total_away" value="<?php echo (int)$goals['total']['away']; ?>" size="3" /></td>
						</tr>
					</tbody>

				<?php } ?>

			</table>

			<?php if ( $sport == 'cricket') { ?>

				<p class="wpcm-results-outcome-title">
					<?php _e( 'Match Outcome', 'wp-club-manager' ); ?>
				</p>

				<div class="wpcm-results-outcome">

					<?php
					wpclubmanager_wp_select( array( 
						'id' => 'cricket_outcome_0',
						'value' => $wpcm_cricket_outcome[0],
						'class' => 'chosen_select_outcome',
						'label' => '',
						'wrapper_class' => 'wpcm_cricket_outcome',
						'options' => array(
							'' => '',
							'won_by' => __( 'Won by', 'wp-club-manager' ),
							'lost_by' => __( 'Lost by', 'wp-club-manager' ),
							'drawn' => __( 'Draw', 'wp-club-manager' )
						)
					));
					?>

					<input type="number" class="wpcm_cricket_outcome" name="cricket_outcome_1" min="0" max="999" value="<?php echo $wpcm_cricket_outcome[1]; ?>" />

					<?php
					wpclubmanager_wp_select( array( 
						'id' => 'cricket_outcome_2',
						'value' => $wpcm_cricket_outcome[2],
						'class' => 'chosen_select_outcome',
						'label' => '',
						'wrapper_class' => 'wpcm_cricket_outcome',
						'options' => array(
							'' => '',
							'runs' => __( 'runs', 'wp-club-manager' ),
							'wickets' => __( 'wickets', 'wp-club-manager' ),
							'innings' => __( 'innings', 'wp-club-manager' )
						)
					));
					?>

				</div>

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

		<?php do_action('wpclubmanager_admin_after_results_table', $post->ID );

	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$sport = get_option('wpcm_sport');

		if( ! empty( $_POST['wpcm_played'] ) ) {
			update_post_meta( $post_id, 'wpcm_played', $_POST['wpcm_played'] );
		} else {
			update_post_meta( $post_id, 'wpcm_played', '' );
		}
		if( ! empty( $_POST['_wpcm_postponed'] ) ) {
			update_post_meta( $post_id, '_wpcm_postponed', $_POST['_wpcm_postponed'] );
		} else {
			update_post_meta( $post_id, '_wpcm_postponed', '' );
		}
		if( isset( $_POST['_wpcm_walkover'] ) ) {
			update_post_meta( $post_id, '_wpcm_walkover', $_POST['_wpcm_walkover'] );
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
			if( $_POST['cricket_outcome_0'] != '' ) {
				$outcome_0 = $_POST['cricket_outcome_0'];
				$outcome_1 = $_POST['cricket_outcome_1'];
				$outcome_2 = $_POST['cricket_outcome_2'];
				$cricket_outcome = array( $outcome_0, $outcome_1, $outcome_2 );
				update_post_meta( $post_id, '_wpcm_cricket_outcome', $cricket_outcome );
			}
		} else {
			if( isset( $_POST['wpcm_goals'] ) ) {
				$goals = $_POST['wpcm_goals'];
				update_post_meta( $post_id, 'wpcm_goals', serialize( $goals ) );
				update_post_meta( $post_id, 'wpcm_home_goals', $goals['total']['home'] );
				update_post_meta( $post_id, 'wpcm_away_goals', $goals['total']['away'] );
			}
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

		do_action( 'delete_plugin_transients' );
	}
}