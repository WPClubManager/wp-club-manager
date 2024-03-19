<?php
/**
 * Player Stats
 *
 * Displays the player stats box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Player_Stats
 */
class WPCM_Meta_Box_Player_Stats {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		if ( is_club_mode() ) {
			$teams = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_team' );
		} else {
			$teams = get_post_meta( '_wpcm_player_club', true );
		}
		$seasons = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_season' );

		$stats        = get_wpcm_player_stats( $post->ID );
		$stats_labels = array_merge( array( 'appearances' => _x( 'PL', 'Played', 'wp-club-manager' ) ), wpcm_get_preset_labels() );
		?>
		<div>

			<span class="type_box"> &mdash;

				<label for="player-stats-season-dropdown">
					<select id="player-stats-season-dropdown" class="wpcm-player-season-select" data-target=".wpcm-player-stat-season">

						<?php
						if ( is_array( $seasons ) ) :
							foreach ( $seasons as $season ) :
								?>

							<option value="wpcm_team-0_season-<?php echo esc_attr( $season->term_id ); ?>" data-show=".wpcm_team-0_season-<?php echo esc_attr( $season->term_id ); ?>"><?php echo esc_html( $season->name ); ?></option>

													<?php
						endforeach;
endif;
						?>

					</select>
				</label>

			</span>



				<?php
				if ( is_array( $teams ) && count( $teams ) > 1 ) {
					?>

					<p><?php esc_html_e( 'Choose a team and season to edit the manual stats.', 'wp-club-manager' ); ?></p>

					<?php
					foreach ( $teams as $team ) {

						$rand = rand( 1, 99999 );
						$name = $team->name;

						if ( $team->parent ) {
							$parent_team = get_term( $team->parent, 'wpcm_team' );
							$name       .= ' (' . $parent_team->name . ')';
						}
						?>

						<div class="wpcm-profile-stats-block">

							<h4><?php echo esc_html( $name ); ?></h4>

							<ul class="stats-tabs-<?php echo esc_attr( $rand ); ?> stats-tabs-multi">

								<li class="tabs-multi"><a href="#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?>"><?php esc_html_e( 'All Season', 'wp-club-manager' ); ?></a></li>

								<?php
								if ( is_array( $seasons ) ) :
									foreach ( $seasons as $season ) :
										?>

									<li><a href="#wpcm_team-<?php echo esc_attr( $team->term_id ); ?>_season-<?php echo esc_attr( $season->term_id ); ?>"><?php echo esc_html( $season->name ); ?></a></li>

																	<?php
								endforeach;
endif;
								?>

							</ul>

							<div id="wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?>" class="tabs-panel-<?php echo esc_attr( $rand ); ?> tabs-panel-multi">

								<?php self::wpcm_player_stats_table( $stats, $team->term_id, 0 ); ?>



							</div>

							<?php
							if ( is_array( $seasons ) ) :
								foreach ( $seasons as $season ) :
									?>

								<div id="wpcm_team-<?php echo esc_attr( $team->term_id ); ?>_season-<?php echo esc_attr( $season->term_id ); ?>" class="tabs-panel-<?php echo esc_attr( $rand ); ?> tabs-panel-multi stats-table-season-<?php echo esc_attr( $rand ); ?>" style="display: none;">

									<?php self::wpcm_player_stats_table( $stats, $team->term_id, $season->term_id ); ?>



									<script type="text/javascript">

										(function($) {

											$('#wpclubmanager-player-stats input').change(function() {

												index = $(this).attr('data-index');
												value = 0;

												$(this).closest('table').find('tbody tr').each(function() {
													value += parseInt($(this).find('input[data-index="' + index + '"]').val());
												});
												$(this).closest('table').find('tfoot tr input[data-index="' + index + '"]').val(value);

												<?php foreach ( $stats_labels as $key => $val ) { ?>

													var sum = 0;
													$('.stats-table-season-<?php echo esc_attr( $rand ); ?> .player-stats-manual-<?php echo esc_attr( $key ); ?>').each(function(){
														sum += Number($(this).val());
													});
													$('#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?> .player-stats-manual-<?php echo esc_attr( $key ); ?>').val(sum);

													var sum = 0;
													$('.stats-table-season-<?php echo esc_attr( $rand ); ?> .player-stats-auto-<?php echo esc_attr( $key ); ?>').each(function(){
														sum += Number($(this).val());
													});
													$('#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?> .player-stats-auto-<?php echo esc_attr( $key ); ?>').val(sum);

													var a = +$('#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?> .player-stats-auto-<?php echo esc_attr( $key ); ?>').val();
													var b = +$('#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?> .player-stats-manual-<?php echo esc_attr( $key ); ?>').val();
													var total = a+b;
													$('#wpcm_team-0_season-0-<?php echo esc_attr( $rand ); ?> .player-stats-total-<?php echo esc_attr( $key ); ?>').val(total);

												<?php } ?>

											});

										})(jQuery);

									</script>

								</div>

															<?php
							endforeach;
endif;
							?>

						</div>

						<script type="text/javascript">
							(function($) {
								$('.stats-tabs-<?php echo esc_attr( $rand ); ?> a').click(function(){
									var t = $(this).attr('href');

									$(this).parent().addClass('tabs-multi <?php echo esc_attr( $rand ); ?>').siblings('li').removeClass('tabs-multi <?php echo esc_attr( $rand ); ?>');
									$(this).parent().parent().parent().find('.tabs-panel-<?php echo esc_attr( $rand ); ?>').hide();
									$(t).show();

									return false;
								});
							})(jQuery);
						</script>
						<?php
					}
				} else {
					?>

					<div class="wpcm-player-stat-season">
						<?php
						if ( is_array( $seasons ) ) :
							foreach ( $seasons as $season ) :
								?>
							<div class="wpcm_team-0_season-<?php echo esc_attr( $season->term_id ); ?> stats-table-season">
															<?php self::wpcm_player_stats_table( $stats, 0, $season->term_id ); ?>
							</div>
													<?php
						endforeach;
endif;
						?>
					</div>

					<div class="wpcm-player-stat-total" style="display:none;">
						<div id="wpcm_team-0_season-0">
							<?php self::wpcm_player_stats_table( $stats, 0, 0 ); ?>
						</div>
					</div>

					<script type="text/javascript">
						(function($) {
							$('#wpclubmanager-player-stats input').change(function() {
								index = $(this).attr('data-index');
								value = 0;
								$(this).closest('table').find('tbody tr').each(function() {
									value += parseInt($(this).find('input[data-index="' + index + '"]').val());
								});
								$(this).closest('table').find('tfoot tr input[data-index="' + index + '"]').val(value);
								<?php
								foreach ( $stats_labels as $key => $val ) {
									?>
									var sum = 0;
									$('.stats-table-season .player-stats-manual-<?php echo esc_attr( $key ); ?>').each(function(){
										sum += Number($(this).val());
									});
									$('#wpcm_team-0_season-0 .player-stats-manual-<?php echo esc_attr( $key ); ?>').val(sum);
									var sum = 0;
									$('.stats-table-season .player-stats-auto-<?php echo esc_attr( $key ); ?>').each(function(){
										sum += Number($(this).val());
									});
									$('#wpcm_team-0_season-0 .player-stats-auto-<?php echo esc_attr( $key ); ?>').val(sum);

									var a = +$('#wpcm_team-0_season-0 .player-stats-auto-<?php echo esc_attr( $key ); ?>').val();
									var b = +$('#wpcm_team-0_season-0 .player-stats-manual-<?php echo esc_attr( $key ); ?>').val();
									var total = a+b;
									$('#wpcm_team-0_season-0 .player-stats-total-<?php echo esc_attr( $key ); ?>').val(total);
									<?php
								}
								?>
							});
						})(jQuery);
					</script>

					<?php
				}
				?>


			<div class="clear"></div>
		</div>

		<?php
	}

	/**
	 * Player stats table.
	 *
	 * @access public
	 *
	 * @param array  $stats
	 * @param string $team
	 * @param string $season
	 *
	 * @return void
	 */
	public static function wpcm_player_stats_table( $stats = array(), $team = 0, $season = 0 ) {

		if ( array_key_exists( $team, $stats ) ) :

			if ( array_key_exists( $season, $stats[ $team ] ) ) :

				$stats = $stats[ $team ][ $season ];
			endif;
		endif;

		$stats_labels = wpcm_get_player_stats_labels();
		?>

		<table>
			<thead>
				<tr>
					<td>&nbsp;</td>
					<?php
					foreach ( $stats_labels as $key => $val ) :
						if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
							?>
							<th><?php echo esc_html( $val ); ?></th>
							<?php
						endif;
					endforeach;
					?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th align="right">Total</th>
					<?php
					foreach ( $stats_labels as $key => $val ) :
						if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
							?>
							<td><input type="text" data-index="<?php echo esc_attr( $key ); ?>" value="<?php wpcm_stats_value( $stats, 'total', $key ); ?>" size="3" tabindex="-1" class="player-stats-total-<?php echo esc_attr( $key ); ?>" readonly /></td>
							<?php
						endif;
					endforeach;
					?>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td align="right"><?php esc_html_e( 'Auto' ); ?></td>
					<?php
					foreach ( $stats_labels as $key => $val ) :
						if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
							?>
							<td><input type="text" data-index="<?php echo esc_attr( $key ); ?>" value="<?php wpcm_stats_value( $stats, 'auto', $key ); ?>" size="3" tabindex="-1" class="player-stats-auto-<?php echo esc_attr( $key ); ?>" readonly /></td>
							<?php
						endif;
					endforeach;
					?>
				</tr>
				<tr>
					<td align="right"><?php esc_html_e( 'Manual', 'wp-club-manager' ); ?></td>
					<?php
					foreach ( $stats_labels as $key => $val ) :
						if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
							?>
							<td><input type="text" data-index="<?php echo esc_attr( $key ); ?>" name="wpcm_stats[<?php echo esc_attr( $team ); ?>][<?php echo esc_attr( $season ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php esc_attr( wpcm_stats_value( $stats, 'manual', $key ) ); ?>" size="3" class="player-stats-manual-<?php echo esc_attr( $key ); ?>"<?php echo ( 0 == $season ? ' readonly' : '' ); ?> /></td>
							<?php
						endif;
					endforeach;
					?>
				</tr>
			</tbody>
		</table>

		<?php
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

		$wpcm_stats = filter_input( INPUT_POST, 'wpcm_stats', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $wpcm_stats ) {
			$stats = $wpcm_stats;
		} else {
			$stats = array();
		}
		if ( is_array( $stats ) ) {
			array_walk_recursive( $stats, 'wpcm_array_values_to_int' );
		}

		update_post_meta( $post_id, 'wpcm_stats', serialize( $stats ) );

		do_action( 'delete_plugin_transients' );
	}
}
