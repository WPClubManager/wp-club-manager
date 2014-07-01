<?php
/**
 * WPClubManager Player Functions. Code adapted from Football Club theme by themeboy.
 *
 * Functions for players.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get empty player stats row.
 *
 * @access public
 * @return mixed $output
 */
if (!function_exists('get_wpcm_player_stats_empty_row')) {
	function get_wpcm_player_stats_empty_row() {

		$stats_labels = wpcm_get_sports_stats_labels();

		$output = array( 'appearances' => 0 );

		foreach( $stats_labels as $key => $val ) {
			$output[$key] = 0;
		}

		return $output;
	}
}

/**
 * Get total player stats.
 *
 * @access public
 * @param string $post_id
 * @param string $team
 * @param string $season
 * @return mixed $output
 */
if (!function_exists('get_wpcm_player_total_stats')) {
	function get_wpcm_player_total_stats( $post_id = null, $team = null, $season = null ) {

		$output = get_wpcm_player_stats_empty_row();
		$autostats = get_wpcm_player_auto_stats( $post_id, $team, $season);
		$manualstats = get_wpcm_player_manual_stats( $post_id, $team, $season);

		foreach( $output as $key => $val ) {
			$output[$key] = $autostats[$key] + $manualstats[$key];
		}

		return $output;
	}
}

/**
 * Get manual player stats.
 *
 * @access public
 * @param string $post_id
 * @param string $team
 * @param string $season
 * @return mixed $output
 */
if (!function_exists('get_wpcm_player_manual_stats')) {
	function get_wpcm_player_manual_stats( $post_id = null, $team = null, $season = null ) {

		$output = get_wpcm_player_stats_empty_row();

		if ( empty ( $team ) ) $team = 0;
		if ( empty ( $season ) ) $season = 0;

		$stats = unserialize( get_post_meta( $post_id, 'wpcm_stats', true ) );

		if ( is_array( $stats ) && array_key_exists( $team, $stats ) ) {
			if ( is_array( $stats[$team] ) && array_key_exists ( $season, $stats[$team] ) ) {
				$output = $stats[$team][$season];
			}
		}

		return $output;
	}
}
/**
 * Get auto player stats.
 *
 * @access public
 * @param string $post_id
 * @param string $team_id
 * @param string $season_id
 * @return mixed $output
 */
if (!function_exists('get_wpcm_player_auto_stats')) {
	function get_wpcm_player_auto_stats( $post_id = null, $team_id = null, $season_id = null ) {

		if ( !$post_id ) global $post_id;

		$stats_labels = wpcm_get_sports_stats_labels();

		$club_id = get_post_meta( $post_id, 'wpcm_club', true );
		$output = get_wpcm_player_stats_empty_row();

		// get all home matches
		$args = array(
			'post_type' => 'wpcm_match',
			'tax_query' => array(),
			'showposts' => -1,
			'meta_query' => array(
				array(
					'key' => 'wpcm_home_club',
					'value' => $club_id
				)
			)
		);

		if ( isset( $season_id ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $season_id
			);
		}

		$matches = get_posts( $args );

		foreach( $matches as $match ) {

			$all_players = unserialize( get_post_meta( $match->ID, 'wpcm_players', true ) );

			if ( is_array( $all_players ) ) {

				foreach( $all_players as $players ) {

					if ( is_array( $players ) && array_key_exists( $post_id, $players ) ) {

						$stats = $players[$post_id];
						$output['appearances'] ++;

						foreach( $stats as $key => $value ) {
							if ( array_key_exists( $key, $stats_labels ) )  {
								if(isset($stats[ $key ])){ $output[ $key ] += $stats[ $key ]; }
							}
						}
					}
				}
			}
		}

		// get all away matches
		$args['meta_query'] = array(
			array(
				'key' => 'wpcm_away_club',
				'value' => $club_id
			)
		);
		$matches = get_posts( $args );

		foreach( $matches as $match ) {

			$all_players = unserialize( get_post_meta( $match->ID, 'wpcm_players', true ) );

			if ( is_array( $all_players ) ) {

				foreach( $all_players as $players ) {

					if ( is_array( $players ) && array_key_exists( $post_id, $players ) ) {

						$stats = $players[$post_id];
						$output['appearances'] ++;
						
						foreach( $stats as $key => $value ) {
							if ( array_key_exists( $key, $stats_labels ) )  {
								if(isset($stats[ $key ])){ $output[ $key ] += $stats[ $key ]; }
							}
						}
					}
				}
			}
		}

		return $output;
	}
}

/**
 * Get total player stats.
 *
 * @access public
 * @param string $post_id
 * @return mixed $output
 */
if (!function_exists('get_wpcm_player_stats')) {
	function get_wpcm_player_stats( $post_id = null ) {

		if ( !$post_id ) global $post_id;

		$output = array();
		$teams = get_the_terms( $post_id, 'wpcm_team' );
		$seasons = get_the_terms( $post_id, 'wpcm_season' );

		// combined season stats for combined team
		$stats = get_wpcm_player_auto_stats( $post_id );
		$output[0][0] = array(
			'auto' => $stats,
			'total' => $stats
		);

		// isolated season stats for combined team
		if ( is_array( $seasons ) ) {

			foreach ( $seasons as $season ) {

				$stats = get_wpcm_player_auto_stats( $post_id, null, $season->term_id );
				$output[0][$season->term_id] = array(
					'auto' => $stats,
					'total' => $stats
				);
			}
		}

		// manual stats
		$stats = (array)unserialize( get_post_meta( $post_id, 'wpcm_stats', true ) );
		if ( is_array( $stats ) ):

			foreach( $stats as $team_key => $team_val ):

				if ( is_array( $team_val ) && array_key_exists( $team_key, $output ) ):

					foreach( $team_val as $season_key => $season_val ):

						if ( array_key_exists ( $season_key, $output[$team_key] ) ) {

							$output[$team_key][$season_key]['manual'] = $season_val;

							foreach( $output[$team_key][$season_key]['total'] as $index_key => &$index_val ) {

								if ( array_key_exists( $index_key, $season_val ) )

								 $index_val += $season_val[$index_key];
							}
						}
					endforeach;
				endif;
			endforeach;
		endif;

		return $output;
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
function wpcm_player_stats_table( $stats = array(), $team = 0, $season = 0 ) {

	if ( array_key_exists( $team, $stats ) ):

		if ( array_key_exists( $season, $stats[$team] ) ):

			$stats = $stats[$team][$season];
		endif;
	endif;

	$wpcm_player_stats_labels = wpcm_get_sports_stats_labels();

	$stats_labels = array( 'appearances' => __( 'Apps', 'wpclubmanager' ) );
	$stats_labels = array_merge( $stats_labels, $wpcm_player_stats_labels ); ?>

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
				<td align="right"><?php _e( 'Manual', 'wpclubmanager' ); ?></td>
				<?php foreach( $stats_labels as $key => $val ):
					if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
						<td><input type="text" data-index="<?php echo $key; ?>" name="wpcm_stats[<?php echo $team; ?>][<?php echo $season; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $stats, 'manual', $key ); ?>" size="3" class="player-stats-manual-<?php echo $key; ?>"<?php echo ( $season == 0 ? ' readonly' : '' ); ?> /></td>
					<?php endif;
				endforeach; ?>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
		(function($) {
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
		})(jQuery);
	</script>

<?php
}

/**
 * Player profile stats table.
 *
 * @access public
 * @param array
 * @param string $team
 * @param string $season
 * @return void
 */
function wpcm_profile_stats_table( $stats = array(), $team = 0, $season = 0 ) {

	if ( array_key_exists( $team, $stats ) ):

		if ( array_key_exists( $season, $stats[$team] ) ):

			$stats = $stats[$team][$season];
		endif;
	endif;

	$wpcm_player_stats_labels = wpcm_get_sports_stats_labels();

	$stats_labels = array( 'appearances' => '<a title="' . __('Games Played', 'wpclubmanager') . '">' . __( 'GP', 'wpclubmanager' ) . '</a>' );
	$stats_labels = array_merge( $stats_labels, $wpcm_player_stats_labels ); ?>

	<table>
		<thead>
			<tr>
				<?php
				foreach( $stats_labels as $key => $val ) { 

					if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) { ?>

						<th><?php echo $val; ?></th>
					<?php
					}

				} ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php foreach( $stats_labels as $key => $val ) {

					if( $key == 'rating' ) {

						$rating = get_wpcm_stats_value( $stats, 'total', 'rating' );
						$apps = get_wpcm_stats_value( $stats, 'total', 'appearances' );
						if( $apps > 0 ) {
							$avrating = $rating / $apps;
						} else {
							$avrating = 0;
						}

						if( get_option( 'wpcm_show_stats_rating' ) == 'yes' ) : ?>
					
							<td><span data-index="rating"><?php echo sprintf( "%01.2f", round($avrating, 2) ); ?></span></td>
						<?php endif;

					} else { 

						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) { ?>

							<td><span data-index="<?php echo $key; ?>"><?php wpcm_stats_value( $stats, 'total', $key ); ?></span></td>
						<?php
						}
					}
				} ?>
				
			</tr>
		</tbody>
	</table>
<?php
}