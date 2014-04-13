<?php
/**
 * WPClubManager Player Functions
 *
 * Functions for players.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.0.3
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

		$wpcm_player_stats_labels = array(
			'goals' => get_option( 'wpcm_player_goals_label'),
			'assists' => get_option( 'wpcm_player_assists_label'),
			'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
			'redcards' => get_option( 'wpcm_player_redcards_label'),
			'rating' => get_option( 'wpcm_player_rating_label'),
			'mvp' => get_option( 'wpcm_player_mvp_label')
		);

		$output = array( 'appearances' => 0 );

		foreach( $wpcm_player_stats_labels as $key => $val ) {
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
						$output['goals'] += $stats['goals'];
						$output['assists'] += $stats['assists'];
						if(isset($stats['yellowcards'])){ $output['yellowcards'] += $stats['yellowcards']; }
						if(isset($stats['redcards'])){ $output['redcards'] += $stats['redcards']; }
						$output['rating'] += $stats['rating'];
						if(isset($stats['mvp'])){ $output['mvp'] += $stats['mvp']; }
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
						$output['goals'] += $stats['goals'];
						$output['assists'] += $stats['assists'];
						if(isset($stats['yellowcards'])){ $output['yellowcards'] += $stats['yellowcards']; }
						if(isset($stats['redcards'])){ $output['redcards'] += $stats['redcards']; }
						$output['rating'] += $stats['rating'];
						if(isset($stats['mvp'])){ $output['mvp'] += $stats['mvp']; }
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

	$wpcm_player_stats_labels = array(
		'goals' => get_option( 'wpcm_player_goals_label'),
		'assists' => get_option( 'wpcm_player_assists_label'),
		'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
		'redcards' => get_option( 'wpcm_player_redcards_label'),
		'rating' => get_option( 'wpcm_player_rating_label'),
		'mvp' => get_option( 'wpcm_player_mvp_label')
	);

	$stats_labels = array( 'appearances' => __( 'Apps', 'wpclubmanager' ) );
	$stats_labels = array_merge( $stats_labels, $wpcm_player_stats_labels );

	if ( array_key_exists( $team, $stats ) ):

		if ( array_key_exists( $season, $stats[$team] ) ):

			$stats = $stats[$team][$season];
		endif;
	endif; ?>

	<table>
		<thead>
			<tr>
				<td>&nbsp;</td>
				<?php foreach( $stats_labels as $key => $val ): ?>
					<th><?php echo $val; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th align="right">Total</th>
				<?php foreach( $stats_labels as $key => $val ): ?>
					<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'total', $key ); ?>" size="3" tabindex="-1" class="player-stats-total-<?php echo $key; ?>" readonly /></td>
				<?php endforeach; ?>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td align="right"><?php _e( 'Auto' ); ?></td>
				<?php foreach( $stats_labels as $key => $val ): ?>
					<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'auto', $key ); ?>" size="3" tabindex="-1" class="player-stats-auto-<?php echo $key; ?>" readonly /></td>
				<?php endforeach; ?>
			</tr>
			<tr>
				<td align="right"><?php _e( 'Manual', 'wpclubmanager' ); ?></td>
				<?php foreach( $stats_labels as $key => $val ): ?>
					<td><input type="text" data-index="<?php echo $key; ?>" name="wpcm_stats[<?php echo $team; ?>][<?php echo $season; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $stats, 'manual', $key ); ?>" size="3" class="player-stats-manual-<?php echo $key; ?>"<?php echo ( $season == 0 ? ' readonly' : '' ); ?> /></td>
				<?php endforeach; ?>
			</tr>
		</tbody>
	</table>
<?php }

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

	$wpcm_player_stats_labels = array(
		'goals' => get_option( 'wpcm_player_goals_label'),
		'assists' => get_option( 'wpcm_player_assists_label'),
		'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
		'redcards' => get_option( 'wpcm_player_redcards_label'),
		'rating' => get_option( 'wpcm_player_ratings_label'),
		'mvp' => get_option( 'wpcm_player_mvp_label')
	);

	$stats_labels = array( 'appearances' => __( 'Apps', 'wpclubmanager' ) );
	$stats_labels = array_merge( $stats_labels, $wpcm_player_stats_labels );

	if ( array_key_exists( $team, $stats ) ):

		if ( array_key_exists( $season, $stats[$team] ) ):

			$stats = $stats[$team][$season];
		endif;
	endif; 

	$show_appearances = get_option('wpcm_player_profile_show_appearances');
	$show_goals = get_option('wpcm_player_profile_show_goals');
	$show_assists = get_option('wpcm_player_profile_show_assists');
	$show_yellowcards = get_option('wpcm_player_profile_show_yellowcards');
	$show_redcards = get_option('wpcm_player_profile_show_redcards');
	$show_ratings = get_option('wpcm_player_profile_show_ratings');
	$show_mvp = get_option('wpcm_player_profile_show_mvp');
	?>

	<table>
		<thead>
			<tr>

				<?php if( $show_appearances == 'yes' ) { ?>

					<th><?php _e( 'Apps', 'wpclubmanager' ); ?></th>

				<?php } ?>

				<?php if( $show_goals == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_goals_label'); ?></th>

				<?php } ?>

				<?php if( $show_assists == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_assists_label'); ?></th>

				<?php } ?>

				<?php if( $show_yellowcards == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_yellowcards_label'); ?></th>

				<?php } ?>

				<?php if( $show_redcards == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_redcards_label'); ?></th>

				<?php } ?>

				<?php if( $show_ratings == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_ratings_label'); ?></th>

				<?php } ?>

				<?php if( $show_mvp == 'yes' ) { ?>

					<th><?php echo get_option( 'wpcm_player_mvp_label'); ?></th>

				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>

				<?php if( $show_appearances == 'yes' ) { ?>

					<td><span data-index="appearances"><?php wpcm_stats_value( $stats, 'total', 'appearances' ); ?></span></td>

				<?php } ?>
				
				<?php if( $show_goals == 'yes' ) { ?>

					<td><span data-index="goals"><?php wpcm_stats_value( $stats, 'total', 'goals' ); ?></span></td>

				<?php } ?>
				
				<?php if( $show_assists == 'yes' ) { ?>

					<td><span data-index="assists"><?php wpcm_stats_value( $stats, 'total', 'assists' ); ?></span></td>

				<?php } ?>
				
				<?php if( $show_yellowcards == 'yes' ) { ?>

					<td><span data-index="yellowcards"><?php wpcm_stats_value( $stats, 'total', 'yellowcards' ); ?></span></td>

				<?php } ?>

				<?php if( $show_redcards == 'yes' ) { ?>
					
					<td><span data-index="redcards"><?php wpcm_stats_value( $stats, 'total', 'redcards' ); ?></span></td>

				<?php } ?>

				<?php if( $show_ratings == 'yes' ) {

					$rating = get_wpcm_stats_value( $stats, 'total', 'rating' );
					$apps = get_wpcm_stats_value( $stats, 'total', 'appearances' );
					if( $apps > 0 ) {
						$avrating = $rating / $apps;
					} else {
						$avrating = 0;
					} ?>
					
					<td><span data-index="rating"><?php echo sprintf( "%01.2f", round($avrating, 2) ); ?></span></td>

				<?php } ?>

				<?php if( $show_mvp == 'yes' ) { ?>
					
					<td><span data-index="mvp"><?php wpcm_stats_value( $stats, 'total', 'mvp' ); ?></span></td>

				<?php } ?>
				
			</tr>
		</tbody>
	</table>
<?php }