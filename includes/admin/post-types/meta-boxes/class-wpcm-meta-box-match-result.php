<?php
/**
 * Match Result
 *
 * Displays the match result box.
 *
 * @author        ClubPress
 * @category      Admin
 * @package       WPClubManager/Admin/Meta Boxes
 * @version       2.1.9
 */

// phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect
// phpcs:disable PEAR.Functions.FunctionCallSignature.Indent
// phpcs:disable NormalizedArrays.Arrays.CommaAfterLast.MissingMultiLine
// phpcs:disable Squiz.PHP.EmbeddedPhp.ContentBeforeEnd
// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
// phpcs:disable WordPress.Arrays.ArrayIndentation.ItemNotAligned
// phpcs:disable WordPress.Arrays.ArrayIndentation.MultiLineArrayItemNotAligned
// phpcs:disable Squiz.PHP.EmbeddedPhp.ContentAfterOpen
// phpcs:disable WordPress.Arrays.ArrayIndentation.CloseBraceNotAligned
// phpcs:disable WordPress.WhiteSpace.ControlStructureSpacing.NoSpaceBeforeCloseParenthesis

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPCM_Meta_Box_Match_Result
 */
class WPCM_Meta_Box_Match_Result {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$sport = get_option( 'wpcm_sport', '' );

		$played    = get_post_meta( $post->ID, 'wpcm_played', true );
		$postponed = get_post_meta( $post->ID, '_wpcm_postponed', true );
		$walkover  = get_post_meta( $post->ID, '_wpcm_walkover', true );

		if ( 'cricket' == $sport ) {
			$wpcm_match_runs      = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, '_wpcm_match_runs', true ) ) );
			$wpcm_match_extras    = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, '_wpcm_match_extras', true ) ) );
			$wpcm_match_wickets   = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, '_wpcm_match_wickets', true ) ) );
			$wpcm_match_overs     = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, '_wpcm_match_overs', true ) ) );
			$wpcm_cricket_outcome = get_post_meta( $post->ID, '_wpcm_cricket_outcome', true );
			if ( ! is_array( $wpcm_cricket_outcome ) ) {
				$wpcm_cricket_outcome = array( 0 => '', 1 => '', 2 => '' );
			};
		} else {
			$goals = array_merge( array(
				'total' => array(
					'home' => '0',
					'away' => '0'
				)
			), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
		}

		if ( ! in_array( $sport, array( 'volleyball', 'baseball' ) ) ) {
			$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
		}
		if ( in_array( $sport, array( 'hockey', 'handball' ) ) ) {
			$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );
		}

		if ( 'soccer' == $sport ) {
			$shootout       = get_post_meta( $post->ID, 'wpcm_shootout', true );
			$shootout_score = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, '_wpcm_shootout_score', true ) ) );
		}

		if ( 'rugby' == $sport ) {
			$bonus = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, 'wpcm_bonus', true ) ) );
		}

		if ( 'gaelic' === $sport) {
			$gaa_goals  = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, 'wpcm_gaa_goals', true ) ) );
			$gaa_points = array_merge( array(
				'home' => '0',
				'away' => '0'
			), (array) unserialize( get_post_meta( $post->ID, 'wpcm_gaa_points', true ) ) );
		} ?>

		<p>
			<label class="selectit">
				<input type="checkbox" name="wpcm_played" id="wpcm_played"
					   value="1" <?php checked( true, $played ); ?> />
				<?php esc_html_e( 'Result', 'wp-club-manager' ); ?>
			</label>
		</p>
		<p>
			<label class="selectit">
				<input type="checkbox" name="_wpcm_postponed" id="_wpcm_postponed"
					   value="1" <?php checked( true, $postponed ); ?> />
				<?php esc_html_e( 'Postponed', 'wp-club-manager' ); ?>
			</label>
		</p>

		<?php wpclubmanager_wp_select( array(
			'id'            => '_wpcm_walkover',
			'value'         => $walkover,
			'class'         => 'chosen_select',
			'label'         => '',
			'wrapper_class' => 'wpcm-postponed-result',
			'options'       => array(
				''         => __( 'To be rescheduled', 'wp-club-manager' ),
				'home_win' => __( 'Home win', 'wp-club-manager' ),
				'away_win' => __( 'Away win', 'wp-club-manager' )
			)
		) ); ?>

		<div id="results-table">

			<?php
			if ( get_option( 'wpcm_match_box_scores' ) == 'yes' ) { ?>

				<table class="box-scores-table">
					<thead>
					<tr>
						<td>&nbsp;</td>
						<th><?php echo esc_html_x( 'Home', 'team', 'wp-club-manager' ); ?></th>
						<th><?php echo esc_html_x( 'Away', 'team', 'wp-club-manager' ); ?></th>
					</tr>
					</thead>
					<tbody>

					<?php
					if ( 'volleyball' === $sport ) :

						$box_goals = array_merge( array(
							'q1' => array(
								'home' => '0',
								'away' => '0'
							)
						), array( 'q2' => array( 'home' => '0', 'away' => '0' ) ), array(
							'q3' => array(
								'home' => '0',
								'away' => '0'
							)
						), array( 'q4' => array( 'home' => '0', 'away' => '0' ) ), array(
							'q5' => array(
								'home' => '0',
								'away' => '0'
							)
						), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr>
							<th align="right"><?php esc_html_e( '1st Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home"
									   value="<?php echo (int) $box_goals['q1']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away"
									   value="<?php echo (int) $box_goals['q1']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '2nd Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home"
									   value="<?php echo (int) $box_goals['q2']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away"
									   value="<?php echo (int) $box_goals['q2']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '3rd Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home"
									   value="<?php echo (int) $box_goals['q3']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away"
									   value="<?php echo (int) $box_goals['q3']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '4th Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q4][home]" id="wpcm_goals_q4_home"
									   value="<?php echo (int) $box_goals['q4']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q4][away]" id="wpcm_goals_q4_away"
									   value="<?php echo (int) $box_goals['q4']['away']; ?>" size="3"/></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php esc_html_e( '5th Set', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q5][home]" id="wpcm_goals_q5_home"
									   value="<?php echo (int) $box_goals['q5']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q5][away]" id="wpcm_goals_q5_away"
									   value="<?php echo (int) $box_goals['q5']['away']; ?>" size="3"/></td>
						</tr>

					<?php
					elseif ( in_array( $sport, array( 'basketball', 'football', 'footy' ) ) ) :

						$box_goals = array_merge( array(
							'q1' => array(
								'home' => '0',
								'away' => '0'
							)
						), array( 'q2' => array( 'home' => '0', 'away' => '0' ) ), array(
							'q3' => array(
								'home' => '0',
								'away' => '0'
							)
						), array(
							'q4' => array(
								'home' => '0',
								'away' => '0'
							)
						), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) ); ?>

						<tr>
							<th align="right"><?php esc_html_e( '1st Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home"
									   value="<?php echo (int) $box_goals['q1']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away"
									   value="<?php echo (int) $box_goals['q1']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '2nd Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home"
									   value="<?php echo (int) $box_goals['q2']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away"
									   value="<?php echo (int) $box_goals['q2']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '3rd Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home"
									   value="<?php echo (int) $box_goals['q3']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away"
									   value="<?php echo (int) $box_goals['q3']['away']; ?>" size="3"/></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php esc_html_e( '4th Quarter', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q4][home]" id="wpcm_goals_q4_home"
									   value="<?php echo (int) $box_goals['q4']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q4][away]" id="wpcm_goals_q4_away"
									   value="<?php echo (int) $box_goals['q4']['away']; ?>" size="3"/></td>
						</tr>

					<?php
					elseif ( in_array( $sport, array( 'hockey', 'floorball' ) ) ) :

						$box_goals = array_merge( array(
							'q1' => array(
								'home' => '0',
								'away' => '0'
							),
						), array(
							'q2' => array(
								'home' => '0',
								'away' => '0',
							),
						), array(
							'q3' => array(
								'home' => '0',
								'away' => '0',
							),
						), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
						?>

						<tr>
							<th align="right"><?php esc_html_e( '1st Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home"
									   value="<?php echo (int) $box_goals['q1']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away"
									   value="<?php echo (int) $box_goals['q1']['away']; ?>" size="3"/></td>
						</tr>
						<tr>
							<th align="right"><?php esc_html_e( '2nd Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q2][home]" id="wpcm_goals_q2_home"
									   value="<?php echo (int) $box_goals['q2']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q2][away]" id="wpcm_goals_q2_away"
									   value="<?php echo (int) $box_goals['q2']['away']; ?>" size="3"/></td>
						</tr>
						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php esc_html_e( '3rd Period', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q3][home]" id="wpcm_goals_q3_home"
									   value="<?php echo (int) $box_goals['q3']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q3][away]" id="wpcm_goals_q3_away"
									   value="<?php echo (int) $box_goals['q3']['away']; ?>" size="3"/></td>
						</tr>
					<?php
					else :
						$box_goals = array_merge( array(
							'q1' => array(
								'home' => '0',
								'away' => '0',
							),
						), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
						?>

						<tr class="wpcm-ss-admin-tr-last">
							<th align="right"><?php esc_html_e( 'Half Time', 'wp-club-manager' ); ?></th>
							<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home"
									   value="<?php echo (int) $box_goals['q1']['home']; ?>" size="3"/></td>
							<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away"
									   value="<?php echo (int) $box_goals['q1']['away']; ?>" size="3"/></td>
						</tr>

					<?php endif; ?>

					</tbody>
				</table>

				<?php
			}
			?>
			<table class="final-score-table">
				<?php
				if ( get_option( 'wpcm_match_box_scores' ) != 'yes' ) {
					?>
					<thead>
					<tr>
						<td>&nbsp;</td>
						<th><?php echo esc_html_x( 'Home', 'team', 'wp-club-manager' ); ?></th>
						<th><?php echo esc_html_x( 'Away', 'team', 'wp-club-manager' ); ?></th>
					</tr>
					</thead>
					<?php
				}
				if ( 'cricket' === $sport ) {
					?>

					<tbody>
					<tr>
						<th align="right"><?php esc_html_e( 'Runs', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_match_runs[home]" id="wpcm_match_runs_home"
								   value="<?php echo (int) $wpcm_match_runs['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_match_runs[away]" id="wpcm_match_runs_away"
								   value="<?php echo (int) $wpcm_match_runs['away']; ?>" size="3"/></td>
					</tr>
					<tr>
						<th align="right"><?php esc_html_e( 'Extras', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_match_extras[home]" id="wpcm_match_extras_home"
								   value="<?php echo (int) $wpcm_match_extras['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_match_extras[away]" id="wpcm_match_extras_away"
								   value="<?php echo (int) $wpcm_match_extras['away']; ?>" size="3"/></td>
					</tr>
					<tr>
						<th align="right"><?php esc_html_e( 'Wickets', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_match_wickets[home]" id="wpcm_match_wickets_home"
								   value="<?php echo (int) $wpcm_match_wickets['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_match_wickets[away]" id="wpcm_match_wickets_away"
								   value="<?php echo (int) $wpcm_match_wickets['away']; ?>" size="3"/></td>
					</tr>
					<tr>
						<th align="right"><?php esc_html_e( 'Overs', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_match_overs[home]" id="wpcm_match_overs_home"
								   value="<?php echo (float) $wpcm_match_overs['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_match_overs[away]" id="wpcm_match_overs_away"
								   value="<?php echo (float) $wpcm_match_overs['away']; ?>" size="3"/></td>
					</tr>
					</tbody>

					<?php
				} else {
					?>

					<tbody>

					<?php do_action( 'wpclubmanager_admin_results_table', $post->ID ); ?>
					<tr>
						<th align="right"><?php esc_html_e( 'Final Score', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_goals[total][home]" id="wpcm_goals_total_home"
								   value="<?php echo (int) $goals['total']['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_goals[total][away]" id="wpcm_goals_total_away"
								   value="<?php echo (int) $goals['total']['away']; ?>" size="3"/></td>
					</tr>
					</tbody>

				<?php } ?>

			</table>

			<?php if ( 'cricket' == $sport ) { ?>

				<p class="wpcm-results-outcome-title">
					<?php esc_html_e( 'Match Outcome', 'wp-club-manager' ); ?>
				</p>

				<div class="wpcm-results-outcome">

					<?php
					wpclubmanager_wp_select( array(
						'id'            => 'cricket_outcome_0',
						'value'         => $wpcm_cricket_outcome[0],
						'class'         => 'chosen_select_outcome',
						'label'         => '',
						'wrapper_class' => 'wpcm_cricket_outcome',
						'options'       => array(
							''        => '',
							'won_by'  => __( 'Won by', 'wp-club-manager' ),
							'lost_by' => __( 'Lost by', 'wp-club-manager' ),
							'drawn'   => __( 'Draw', 'wp-club-manager' ),
						),
					) );
					?>

					<input type="number" class="wpcm_cricket_outcome" name="cricket_outcome_1" min="0" max="999"
						   value="<?php echo esc_html( $wpcm_cricket_outcome[1] ); ?>"/>

					<?php
					wpclubmanager_wp_select( array(
						'id'            => 'cricket_outcome_2',
						'value'         => $wpcm_cricket_outcome[2],
						'class'         => 'chosen_select_outcome',
						'label'         => '',
						'wrapper_class' => 'wpcm_cricket_outcome',
						'options'       => array(
							''        => '',
							'runs'    => __( 'runs', 'wp-club-manager' ),
							'wickets' => __( 'wickets', 'wp-club-manager' ),
							'innings' => __( 'innings', 'wp-club-manager' ),
						),
					) );
					?>

				</div>

			<?php } ?>

			<?php if ( 'rugby' === $sport ) { ?>

				<table class="wpcm-results-bonus">
					<tbody>
					<tr>
						<th align="right"><?php esc_html_e( 'Bonus Points', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_bonus[home]" id="wpcm_bonus_home"
								   value="<?php echo (int) $bonus['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_bonus[away]" id="wpcm_bonus_away"
								   value="<?php echo (int) $bonus['away']; ?>" size="3"/></td>
					</tr>
					</tbody>
				</table>

			<?php } ?>

			<?php if ( 'gaelic' === $sport ) { ?>

				<table class="wpcm-results-gaelic">
					<tbody>
					<tr>
						<th align="right"><?php esc_html_e( 'Goals', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_gaa_goals[home]" id="wpcm_gaa_goals_home"
								   value="<?php echo (int) $gaa_goals['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_gaa_goals[away]" id="wpcm_gaa_goals_away"
								   value="<?php echo (int) $gaa_goals['away']; ?>" size="3"/></td>
					</tr>
					<tr>
						<th align="right"><?php esc_html_e( 'Points', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_gaa_points[home]" id="wpcm_gaa_points_home"
								   value="<?php echo (int) $gaa_points['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_gaa_points[away]" id="wpcm_gaa_points_away"
								   value="<?php echo (int) $gaa_points['away']; ?>" size="3"/></td>
					</tr>
					</tbody>
				</table>

			<?php } ?>

			<?php if ( ! in_array( $sport, array( 'cricket', 'soccer', 'volleyball', 'baseball' ) ) ) { ?>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_overtime" id="wpcm_overtime"
							   value="1" <?php checked( true, $overtime ); ?> />
						<?php esc_html_e( 'Overtime', 'wp-club-manager' ); ?>
					</label>
				</p>

			<?php } ?>

			<?php if ( in_array( $sport, array( 'hockey', 'handball' ) ) ) { ?>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_shootout" id="wpcm_shootout"
							   value="1" <?php checked( true, $shootout ); ?> />
						<?php esc_html_e( 'Shootout', 'wp-club-manager' ); ?>
					</label>
				</p>

			<?php } ?>

			<?php if ( 'soccer' === $sport ) { ?>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_overtime" id="wpcm_overtime"
							   value="1" <?php checked( true, $overtime ); ?> />
						<?php esc_html_e( 'Extra Time', 'wp-club-manager' ); ?>
					</label>
				</p>

				<p>
					<label class="selectit">
						<input type="checkbox" name="wpcm_shootout" id="wpcm_shootout"
							   value="1" <?php checked( true, $shootout ); ?> />
						<?php esc_html_e( 'Penalties', 'wp-club-manager' ); ?>
					</label>
				</p>

				<table class="wpcm-results-shootout">
					<tbody>
					<tr>
						<th align="right"><?php esc_html_e( 'Score', 'wp-club-manager' ); ?></th>
						<td><input type="text" name="wpcm_shootout_score[home]" id="wpcm_shootout_home"
								   value="<?php echo (int) $shootout_score['home']; ?>" size="3"/></td>
						<td><input type="text" name="wpcm_shootout_score[away]" id="wpcm_shootout_away"
								   value="<?php echo (int) $shootout_score['away']; ?>" size="3"/></td>
					</tr>
					</tbody>
				</table>

			<?php } ?>

		</div>

		<?php
		do_action( 'wpclubmanager_admin_after_results_table', $post->ID );
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$sport = get_option( 'wpcm_sport', '' );

		$played = filter_input( INPUT_POST, 'wpcm_played', FILTER_UNSAFE_RAW );
		if ( $played ) {
			update_post_meta( $post_id, 'wpcm_played', sanitize_text_field( $played ) );
		} else {
			update_post_meta( $post_id, 'wpcm_played', '' );
		}

		$postponed = filter_input( INPUT_POST, '_wpcm_postponed', FILTER_UNSAFE_RAW );
		if ( $postponed ) {
			update_post_meta( $post_id, '_wpcm_postponed', sanitize_text_field( $postponed ) );
		} else {
			update_post_meta( $post_id, '_wpcm_postponed', '' );
		}

		$walkover = filter_input( INPUT_POST, '_wpcm_walkover', FILTER_UNSAFE_RAW );
		if ( $walkover ) {
			update_post_meta( $post_id, '_wpcm_walkover', $walkover );
		}

		if ( 'cricket' == $sport ) {

			$wpcm_match_runs = filter_input( INPUT_POST, 'wpcm_match_runs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( $wpcm_match_runs ) {
				update_post_meta( $post_id, '_wpcm_match_runs', serialize( $wpcm_match_runs ) );
			}
			$wpcm_match_extras = filter_input( INPUT_POST, 'wpcm_match_extras', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( isset( $_POST['wpcm_match_extras'] ) ) {
				update_post_meta( $post_id, '_wpcm_match_extras', serialize( $wpcm_match_extras ) );
			}
			$wpcm_match_wickets = filter_input( INPUT_POST, 'wpcm_match_wickets', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( isset( $_POST['wpcm_match_wickets'] ) ) {
				update_post_meta( $post_id, '_wpcm_match_wickets', serialize( $wpcm_match_wickets ) );
			}
			$wpcm_match_overs = filter_input( INPUT_POST, 'wpcm_match_overs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( isset( $_POST['wpcm_match_overs'] ) ) {
				update_post_meta( $post_id, '_wpcm_match_overs', serialize( $wpcm_match_overs ) );
			}

			$cricket_outcome = filter_input( INPUT_POST, 'cricket_outcome_0', FILTER_UNSAFE_RAW );
			if ( $cricket_outcome && '' != $cricket_outcome ) {
				$outcome_0       = sanitize_text_field( $cricket_outcome );
				$cricket_outcome_1 = filter_input( INPUT_POST, 'cricket_outcome_1', FILTER_UNSAFE_RAW );
				$cricket_outcome_2 = filter_input( INPUT_POST, 'cricket_outcome_2', FILTER_UNSAFE_RAW );
				$outcome_1       = sanitize_text_field( $cricket_outcome_1 );
				$outcome_2       = sanitize_text_field( $cricket_outcome_2 );
				$cricket_outcome = array( $outcome_0, $outcome_1, $outcome_2 );
				update_post_meta( $post_id, '_wpcm_cricket_outcome', $cricket_outcome );
			}
		} else {
			$goals = filter_input( INPUT_POST, 'wpcm_goals', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( $goals ) {
				update_post_meta( $post_id, 'wpcm_goals', serialize( $goals ) );
				update_post_meta( $post_id, 'wpcm_home_goals', $goals['total']['home'] );
				update_post_meta( $post_id, 'wpcm_away_goals', $goals['total']['away'] );
			}
		}

		$bonus = filter_input( INPUT_POST, 'wpcm_bonus', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( 'rugby' == $sport && $bonus ) {
			update_post_meta( $post_id, 'wpcm_bonus', serialize( $bonus ) );
			update_post_meta( $post_id, 'wpcm_home_bonus', $bonus['home'] );
			update_post_meta( $post_id, 'wpcm_away_bonus', $bonus['away'] );
		}

		$overtime = filter_input( INPUT_POST, 'wpcm_overtime', FILTER_UNSAFE_RAW );
		if ( $overtime && ! in_array( $sport, array( 'volleyball', 'baseball' ) ) ) {
			update_post_meta( $post_id, 'wpcm_overtime', sanitize_text_field( $overtime ) );
		}

		if ( ! $overtime && ! in_array( $sport, array( 'volleyball', 'baseball' ) ) ) {
			delete_post_meta( $post_id, 'wpcm_overtime' );
		}

		$shootout = filter_input( INPUT_POST, 'wpcm_shootout', FILTER_UNSAFE_RAW );
		if ( $shootout && in_array( $sport, array( 'hockey', 'handball' ) ) ) {
			update_post_meta( $post_id, 'wpcm_shootout', sanitize_text_field( $shootout ) );
		}

		if ( 'soccer' === $sport && $shootout ) {
			update_post_meta( $post_id, 'wpcm_shootout', sanitize_text_field( $shootout ) );
			$shootout_score = filter_input( INPUT_POST, 'wpcm_shootout_score', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			update_post_meta( $post_id, '_wpcm_shootout_score', serialize( $shootout_score ) );
			update_post_meta( $post_id, '_wpcm_home_shootout_goals', $shootout_score['home'] );
			update_post_meta( $post_id, '_wpcm_away_shootout_goals', $shootout_score['away'] );
		}

		if ( ! $shootout && in_array( $sport, array( 'soccer', 'hockey', 'handball' ) ) ) {
			delete_post_meta( $post_id, 'wpcm_shootout' );
		}

		if ( 'soccer' === $sport && ! $shootout ) {
			delete_post_meta( $post_id, '_wpcm_shootout_score' );
			delete_post_meta( $post_id, '_wpcm_home_shootout_goals' );
			delete_post_meta( $post_id, '_wpcm_away_shootout_goals' );
		}

		if ( 'gaelic' === $sport ) {
			$gaa_goals = filter_input( INPUT_POST, 'wpcm_gaa_goals', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( $gaa_goals ) {
				update_post_meta( $post_id, 'wpcm_gaa_goals', serialize( $gaa_goals ) );
				update_post_meta( $post_id, 'wpcm_home_gaa_goals', $gaa_goals['home'] );
				update_post_meta( $post_id, 'wpcm_away_gaa_goals', $gaa_goals['away'] );
			}
			$gaa_points = filter_input( INPUT_POST, 'wpcm_gaa_points', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( $gaa_points ) {
				update_post_meta( $post_id, 'wpcm_gaa_points', serialize( $gaa_points ) );
				update_post_meta( $post_id, 'wpcm_home_gaa_points', $gaa_points['home'] );
				update_post_meta( $post_id, 'wpcm_away_gaa_points', $gaa_points['away'] );
			}
		}

		do_action( 'delete_plugin_transients' );
	}
}
