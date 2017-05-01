<?php
/**
 * Player Stats
 *
 * Displays the player stats box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Player_Stats {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		//wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$teams = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_team' );
		$seasons = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_season' );
		$stats = get_wpcm_player_stats( $post->ID );
		$stats_labels = array_merge( array( 'appearances' => __( 'PL', 'wp-club-manager' ) ), wpcm_get_preset_labels() );
		$custom_stats = get_post_meta( $post->ID, '_wpcm_custom_player_stats', true )?>

		<p><strong><?php _e( 'Profile stats display', 'wp-club-manager' ); ?></strong></p>

		<table class="wpcm-profile-stats-custom">
			<tr>
				<?php
				foreach( $stats_labels as $key => $val ):
					if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
						<td>
							<label for="_wpcm_custom_player_stats[<?php echo $key; ?>]"><?php echo $val; ?></label>
							<input type="checkbox" class="" name="_wpcm_custom_player_stats[<?php echo $key; ?>]" id="_wpcm_custom_player_stats[<?php echo $key; ?>]" value="" <?php echo ( is_array( $custom_stats ) ? checked( array_key_exists( $key, $custom_stats ) ) : 'checked' ); ?> />
						</td>
					<?php endif;
				endforeach; ?>
			</tr>
		</table>

		<?php
		if( is_array( $teams ) && count($teams) > 1 ) { ?>

			<p><?php _e('Choose a team and season to edit the manual stats.', 'wp-club-manager'); ?></p>

			<?php
			foreach( $teams as $team ) {

				$rand = rand(1,99999);
				$name = $team->name;

				if ( $team->parent ) {
					$parent_team = get_term( $team->parent, 'wpcm_team');
					$name .= ' (' . $parent_team->name . ')';
				} ?>

				<div class="wpcm-profile-stats-block">

					<h4><?php echo $name; ?></h4>

					<ul class="stats-tabs-<?php echo $rand; ?> stats-tabs-multi">
								
						<li class="tabs-multi"><a href="#wpcm_team-0_season-0-<?php echo $rand; ?>"><?php printf( __( 'All %s', 'wp-club-manager' ), __( 'Seasons', 'wp-club-manager' ) ); ?></a></li>

						<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>

							<li><a href="#wpcm_team-<?php echo $team->term_id; ?>_season-<?php echo $season->term_id; ?>"><?php echo $season->name; ?></a></li>

						<?php endforeach; endif; ?>
						
					</ul>

					<div id="wpcm_team-0_season-0-<?php echo $rand; ?>" class="tabs-panel-<?php echo $rand; ?> tabs-panel-multi">
									
						<?php self::wpcm_player_stats_table( $stats, $team->term_id, 0 ); ?>

						
								
					</div>
							
					<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
									
						<div id="wpcm_team-<?php echo $team->term_id; ?>_season-<?php echo $season->term_id; ?>" class="tabs-panel-<?php echo $rand; ?> tabs-panel-multi stats-table-season-<?php echo $rand; ?>" style="display: none;">
										
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

										<?php foreach( $stats_labels as $key => $val ) { ?>

											var sum = 0;
											$('.stats-table-season-<?php echo $rand; ?> .player-stats-manual-<?php echo $key; ?>').each(function(){
												sum += Number($(this).val());
											});
											$('#wpcm_team-0_season-0-<?php echo $rand; ?> .player-stats-manual-<?php echo $key; ?>').val(sum);

											var sum = 0;
											$('.stats-table-season-<?php echo $rand; ?> .player-stats-auto-<?php echo $key; ?>').each(function(){
												sum += Number($(this).val());
											});
											$('#wpcm_team-0_season-0-<?php echo $rand; ?> .player-stats-auto-<?php echo $key; ?>').val(sum);

											var a = +$('#wpcm_team-0_season-0-<?php echo $rand; ?> .player-stats-auto-<?php echo $key; ?>').val();
											var b = +$('#wpcm_team-0_season-0-<?php echo $rand; ?> .player-stats-manual-<?php echo $key; ?>').val();
											var total = a+b;
											$('#wpcm_team-0_season-0-<?php echo $rand; ?> .player-stats-total-<?php echo $key; ?>').val(total);

										<?php } ?>
										
									});

								})(jQuery);
								
							</script>
									
						</div>
						
					<?php endforeach; endif; ?>

				</div>

				<script type="text/javascript">
					(function($) {
						$('.stats-tabs-<?php echo $rand; ?> a').click(function(){
							var t = $(this).attr('href');
							
							$(this).parent().addClass('tabs-multi <?php echo $rand; ?>').siblings('li').removeClass('tabs-multi <?php echo $rand; ?>');
							$(this).parent().parent().parent().find('.tabs-panel-<?php echo $rand; ?>').hide();
							$(t).show();

							return false;
						});
					})(jQuery);
				</script>
			<?php }
		} else { ?>

			<div class="statsdiv">
				<ul class="wpcm_stats-tabs">
					<li class="tabs"><a href="#wpcm_team-0_season-0" tabindex="3"><?php printf( __( 'All %s', 'wp-club-manager' ), __( 'Seasons', 'wp-club-manager' ) ); ?></a></li>
					<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
						<li class="hide-if-no-js22"><a href="#wpcm_team-0_season-<?php echo $season->term_id; ?>" tabindex="3"><?php echo $season->name; ?></a></li>
					<?php endforeach; endif; ?>
				</ul>
				<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
					<div id="wpcm_team-0_season-<?php echo $season->term_id; ?>" class="tabs-panel stats-table-season" style="display: none;">
						<?php self::wpcm_player_stats_table( $stats, 0, $season->term_id ); ?>
						
						

					</div>
				<?php endforeach; endif; ?>
				<div id="wpcm_team-0_season-0" class="tabs-panel">
					<?php self::wpcm_player_stats_table( $stats, 0, 0 ); ?>

					<script type="text/javascript">

						(function($) {

							$('#wpclubmanager-player-stats input').change(function() {

								index = $(this).attr('data-index');
								value = 0;

								$(this).closest('table').find('tbody tr').each(function() {					
									value += parseInt($(this).find('input[data-index="' + index + '"]').val());
								});				
								$(this).closest('table').find('tfoot tr input[data-index="' + index + '"]').val(value);

								<?php foreach( $stats_labels as $key => $val ) { ?>

									var sum = 0;
									$('.stats-table-season .player-stats-manual-<?php echo $key; ?>').each(function(){
										sum += Number($(this).val());
									});
									$('#wpcm_team-0_season-0 .player-stats-manual-<?php echo $key; ?>').val(sum);

									var sum = 0;
									$('.stats-table-season .player-stats-auto-<?php echo $key; ?>').each(function(){
										sum += Number($(this).val());
									});
									$('#wpcm_team-0_season-0 .player-stats-auto-<?php echo $key; ?>').val(sum);

									var a = +$('#wpcm_team-0_season-0 .player-stats-auto-<?php echo $key; ?>').val();
									var b = +$('#wpcm_team-0_season-0 .player-stats-manual-<?php echo $key; ?>').val();
									var total = a+b;
									$('#wpcm_team-0_season-0 .player-stats-total-<?php echo $key; ?>').val(total);

								<?php } ?>

							});

						})(jQuery);
						
					</script>

				</div>
			</div>
			<div class="clear"></div>

		<?php
		}
	}

	/**
	 * Player stats table.
	 *
	 * @access public
	 * @param array
	 * @param string $team
	 * @param string $season
	 * @return void
	 */
	public static function wpcm_player_stats_table( $stats = array(), $team = 0, $season = 0 ) {

		if ( array_key_exists( $team, $stats ) ):

			if ( array_key_exists( $season, $stats[$team] ) ):

				$stats = $stats[$team][$season];
			endif;
		endif;

		$stats_labels = wpcm_get_player_stats_labels(); ?>

		<table>
			<thead>
				<tr>
					<td>&nbsp;</td>
					<?php foreach( $stats_labels as $key => $val ):
						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
							<th><?php echo $val; ?></th>
						<?php endif;
					endforeach; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th align="right">Total</th>
					<?php foreach( $stats_labels as $key => $val ):
						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
							<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'total', $key ); ?>" size="3" tabindex="-1" class="player-stats-total-<?php echo $key; ?>" readonly /></td>
						<?php endif;
					endforeach; ?>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td align="right"><?php _e( 'Auto' ); ?></td>
					<?php foreach( $stats_labels as $key => $val ):
						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
							<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'auto', $key ); ?>" size="3" tabindex="-1" class="player-stats-auto-<?php echo $key; ?>" readonly /></td>
						<?php endif;
					endforeach; ?>
				</tr>
				<tr>
					<td align="right"><?php _e( 'Manual', 'wp-club-manager' ); ?></td>
					<?php foreach( $stats_labels as $key => $val ):
						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
							<td><input type="text" data-index="<?php echo $key; ?>" name="wpcm_stats[<?php echo $team; ?>][<?php echo $season; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $stats, 'manual', $key ); ?>" size="3" class="player-stats-manual-<?php echo $key; ?>"<?php echo ( $season == 0 ? ' readonly' : '' ); ?> /></td>
						<?php endif;
					endforeach; ?>
				</tr>
			</tbody>
		</table>

	<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['wpcm_stats'] ) ) {
			$stats = $_POST['wpcm_stats'];
		} else {
			$stats = array();
		}
		if( is_array( $stats ) ) array_walk_recursive( $stats, 'wpcm_array_values_to_int' );
		
		update_post_meta( $post_id, 'wpcm_stats', serialize( $stats ) );

		//update_post_meta( $post_id, '_wpcm_custom_player_stats', serialize( $stats ) );

		$player_stats = $_POST['_wpcm_custom_player_stats'];
		update_post_meta( $post_id, '_wpcm_custom_player_stats', $player_stats );

		do_action( 'delete_plugin_transients' );
	}
}