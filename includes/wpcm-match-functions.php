<?php
/**
 * WPClubManager Match Functions
 *
 * Functions for matches.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Match player subs dropdown.
 *
 * @access public
 * @param array
 * @param string $id
 * @param bool $disabled = false
 * @return void
 */
function wpcm_player_subs_dropdown( $players = array(), $id = null, $disabled = false ) {

	$subs = get_posts( array (
		'post_type' => 'wpcm_player',
		'meta_key' => 'wpcm_number',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'showposts' => -1
	) ); ?>

	<td>
		<select name="wpcm_players[subs][<?php echo $id; ?>][sub]" data-player="<?php echo $id; ?>" class="postform" <?php disabled( true, $disabled ); ?>>
			<option value="-1"><?php _e( 'None' ); ?></option>

			<?php foreach( $subs as $sub ) { ?>
			<option value="<?php echo $sub->ID; ?>"<?php echo ( $sub->ID == get_wpcm_stats_value( $players['subs'], $id, 'sub' ) ? ' selected' : '' ); ?>>
				<?php echo get_post_meta( $sub->ID, 'wpcm_number', true ); ?>. <?php echo $sub->post_title; ?>
			</option>
			<?php } ?>
		</select>
	</td>
<?php }

/**
 * Match player minutes input.
 *
 * @access public
 * @param array
 * @param string $id
 * @param bool $disabled = false
 * @return void
 */
function wpcm_player_subs_minutes( $players = array(), $id = null, $disabled = false ) {

	global $player;

	$players = array( 'lineup' => array(), 'subs' => array() ); ?>

	<td>
		<input type="text" data-player="<?php echo $id; ?>" name="wpcm_players[subs][<?php echo $id; ?>][subtime]" value="<?php echo get_wpcm_stats_value( $players['subs'], $id, 'subtime' ) ?>" size="2" <?php disabled( true, $disabled ); ?>/>
	</td>
<?php }

/**
 * Player stats table.
 *
 * @access public
 * @param array
 * @param string $club
 * @param string $type
 * @param bool $keyarray = false
 * @return void
 */
function wpcm_match_player_stats_table( $selected_players = array(), $club = null, $type = 'lineup', $keyarray = false ) {

	global $player;

	$wpcm_player_stats_labels = array(
		'goals' => get_option( 'wpcm_player_goals_label'),
		'assists' => get_option( 'wpcm_player_assists_label'),
		'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
		'redcards' => get_option( 'wpcm_player_redcards_label'),
		'rating' => get_option( 'wpcm_player_rating_label'),
		'mvp' => get_option( 'wpcm_player_mvp_label')
	);

	$args = array(
		'post_type' => 'wpcm_player',
		'meta_query' => array(
			array(
				'key' => 'wpcm_club',
				'value' => $club,
			)
		),
		'meta_key' => 'wpcm_number',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'showposts' => -1
	);

	$goals_label = get_option( 'wpcm_player_goals_label');
	$assist_label = get_option( 'wpcm_player_assists_label');
	$yellowcards_label = get_option( 'wpcm_player_yellowcards_label');
	$redcards_label = get_option( 'wpcm_player_redcards_label');
	$rating_label = get_option( 'wpcm_player_rating_label');
	$mvp_label = get_option( 'wpcm_player_mvp_label');

	if ( get_option( 'wpcm_player_order' ) == 'menu_order' ) {

	    $args['orderby'] = 'menu_order';
	}

	$players = get_posts( $args );

	if ( empty( $players ) ) {

		printf( __( 'No %s found', 'wpclubmanager' ), __( 'players', 'wpclubmanager' ) );
		return;
	}

	if ( ! is_array( $selected_players ) )

		$selected_players = array();

		$selected_players = array_merge( array( 'lineup' => array(), 'subs' => array() ), $selected_players ); ?>

	<table class="wpcm-match-players-table">
		<thead>
			<tr class="player-stats-list-labels">
				<th>&nbsp;</th>
				<th><?php echo $goals_label; ?></th>
				<th><?php echo $assist_label; ?></th>
				<th class="th-checkbox"><?php echo $yellowcards_label; ?></th>
				<th class="th-checkbox"><?php echo $redcards_label; ?></th>
				<th><?php echo $rating_label; ?></th>
				<th class="th-radio"><?php echo $mvp_label; ?></th>
				<?php if ( $type == 'subs' ) { ?>
					<th><?php _e( 'Player Off', 'wpclubmanager' ); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $players as $player ) { ?>
				<?php
					$played = (
						is_array( $selected_players ) &&
						array_key_exists( $type, $selected_players ) &&
						is_array( $selected_players[$type] ) &&
						array_key_exists( $player->ID, $selected_players[$type] )&&
						is_array( $selected_players[$type][$player->ID] )
					);

					$teams = get_the_terms( $player->ID, 'wpcm_team' );

					$teamclass = '';

					if($teams > 0){
						foreach( $teams as $team ) {
							$teamclass .= 'team_' . $team->term_id . ' ';
						}
					}else{
						$teamclass .= 'team_0 ';
					}
				?>

				<tr data-player="<?php echo $player->ID; ?>" class="player-stats-list <?php echo $teamclass; ?>">
					<td class="names">
						<label class="selectit">
							<input type="checkbox" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][checked]" class="player-select" value="1" <?php checked( true, $played ); ?> />
							<?php echo get_post_meta( $player->ID, 'wpcm_number', true ); ?>. <?php echo $player->post_title; ?>
						</label>
					</td>
					<?php foreach( $wpcm_player_stats_labels as $key => $val ):

						$keyarray = (
								is_array( $selected_players ) &&
								array_key_exists( $type, $selected_players ) &&
								is_array( $selected_players[$type] ) &&
								array_key_exists( $player->ID, $selected_players[$type] ) &&
								is_array( $selected_players[$type][$player->ID] ) &&
								array_key_exists( $key, $selected_players[$type][$player->ID] )
							);

						if ( $key == 'yellowcards' ) { ?>

							<td class="<?php echo $key; ?>">
								<input type="checkbox" data-card="yellow" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?>/>
							</td>

						<?php } elseif ( $key == 'redcards' ) { ?>

							<td class="<?php echo $key; ?>">
								<input type="checkbox" data-card="red" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?>/>
							</td>

						<?php } elseif ( $key == 'rating' ) { ?>

							<td class="<?php echo $key; ?>">
								<input type="number" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $selected_players[$type], $player->ID, $key ); ?>" min="0" max="10"<?php if ( !$played ) echo ' disabled'; ?>/>
							</td>

						<?php } elseif ( $key == 'mvp' ) { ?>

							<td class="mvp">
								<input type="radio" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?> />
							</td>

						<?php } else { ?>

							<td class="<?php echo $key; ?>">
								<input type="number" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $selected_players[$type], $player->ID, $key ); ?>" min="0"<?php if ( !$played ) echo ' disabled'; ?>/>
							</td>

						<?php }
					
					endforeach;

					if ( $type == 'subs' ) {
						
						wpcm_player_subs_dropdown( $selected_players, $player->ID, !$played );

						//wpcm_player_subs_minutes( $selected_players, $player->ID, !$played );
						
					} ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php }

/**
 * Single page match player row.
 *
 * @access public
 * @param array
 * @param string $key
 * @param string $value
 * @param int $count
 * @return mixed $output
 */
function wpcm_match_player_row( $key, $value, $count = 0 ) {

	$number = get_post_meta( $key, 'wpcm_number', true );

	$show_number = get_option('wpcm_player_profile_show_number');
	$show_assists = get_option('wpcm_player_profile_show_assists');
	$show_ratings = get_option('wpcm_player_profile_show_ratings');

	$output = '';

	$output .= '<tr>';

	if( $show_number == 'yes') {

		$output .= '<td>' . $number . '</td>';

	}

	$output .= '<td class="name">
					<a href="' . get_permalink( $key ) . '">' . get_the_title( $key ) . '
					</a>';

	if ( array_key_exists( 'sub', $value ) && $value['sub'] > 0 ) {

		$output .= '<span class="sub">&larr; ' . get_the_title( $value['sub'] ) . '</span>';
	}

	$output .= '</td>';

	$output .= '<td class="goals">' . $value['goals'] . '</td>';

	if( $show_assists == 'yes') {

		$output .= '<td class="assists">' . $value['assists'] . '</td>';

	}

	if( $show_ratings == 'yes') {

		if ( 0 < $value['rating'] ) {
	
			$output .= '<td class="rating">' . $value['rating'] . '</td>';
	
		}

	}

	$output .= '<td class="notes">';
	
	if ( isset( $value['yellowcards'] ) ) {
		
		$output .= '<span class="yellowcard" title="' . get_option( 'wpcm_player_yellowcards_label') . '">' . __( 'Yellow Card', 'wpclubmanager' ) . '</span>';
	}
	
	if ( isset( $value['redcards'] ) ) {
		$output .= '<span class="redcard" title="' . get_option( 'wpcm_player_redcards_label') . '">' . __( 'Red Card', 'wpclubmanager' ) . '</span>';
	}

	if ( isset( $value['mvp'] ) ) {
		$output .= '<span class="mvp" title="' . get_option( 'wpcm_player_mvp_label') . '">' . __( 'Man of Match', 'wpclubmanager' ) . '</span>';
	}

	$output .= '</td>';

	$output .= '</tr>';

	return $output;
}