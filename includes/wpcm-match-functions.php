<?php
/**
 * WPClubManager Match Functions. Some code adapted from Football Club theme by themeboy.
 *
 * Functions for matches.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// generate title
function match_title( $title, $id = null ) {
	if ( get_post_type( $id ) == 'wpcm_match' ) {
		
		$default_club = get_option('wpcm_default_club');
		$title_format = get_option('wpcm_match_title_format');
		$separator = get_option('wpcm_match_clubs_separator');
		$home_id = (int)get_post_meta( $id, 'wpcm_home_club', true );
		$away_id = (int)get_post_meta( $id, 'wpcm_away_club', true );
		$home_club = get_post( $home_id );
		$away_club = get_post( $away_id );
		$search = array( '%home%', 'vs', '%away%' );
		$replace = array( $home_club->post_title, $separator, $away_club->post_title );
		
		if ( $away_id == $default_club ) {
			//away
			$title = str_replace( $search, $replace, $title_format );
		} else {
			// home
			$title = str_replace( $search, $replace, $title_format );
		}
	}
	return $title;
}
add_filter( 'the_title', 'match_title', 10, 2 );

// generate title
function match_wp_title( $title ) {
	global $post;
	if ( get_post_type( ) == 'wpcm_match' ) {

		$id = $post->ID;
		$home_id = (int)get_post_meta( $id, 'wpcm_home_club', true );
		$away_id = (int)get_post_meta( $id, 'wpcm_away_club', true );
		$home_club = get_post( $home_id );
		$away_club = get_post( $away_id );
		$title = match_title( $title, $id ) . ' | ' . get_the_date();

		return $title;
	}
	return $title;
}
add_filter( 'wp_title', 'match_wp_title', 10, 2 );

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

	global $post;
	
	$teams = get_the_terms( $post->ID, 'wpcm_team' );
	$seasons = get_the_terms( $post->ID, 'wpcm_season' );

	if ( is_array( $teams ) ) {
						
		$match_teams = array();

		foreach ( $teams as $team ) {
			
			$match_teams[] = $team->term_id;
		}
	}

	if ( is_array( $seasons ) ) {
						
		$match_seasons = array();

		foreach ( $seasons as $season ) {
			
			$match_seasons[] = $season->term_id;
		}
	}

	$subs = get_posts( array (
		'post_type' => 'wpcm_player',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'wpcm_team',
				'field' => 'term_id',
				'terms' => $match_teams
			),
			array(
				'taxonomy' => 'wpcm_season',
				'field' => 'term_id',
				'terms' => $match_seasons
			)
		),
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
<?php
}

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
<?php
}

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
function wpcm_match_player_stats_table( $selected_players = array(), $type = 'lineup', $keyarray = false ) {

	global $post, $player;

	$teams = get_the_terms( $post->ID, 'wpcm_team' );
	$seasons = get_the_terms( $post->ID, 'wpcm_season' );

	if ( is_array( $teams ) ) {
						
		$match_teams = array();

		foreach ( $teams as $team ) {
			
			$match_teams[] = $team->term_id;
		}
	} else {
		$match_teams = array();
	}

	if ( is_array( $seasons ) ) {
						
		$match_seasons = array();

		foreach ( $seasons as $season ) {
			
			$match_seasons[] = $season->term_id;
		}
	}else {
		$match_seasons = array();
	}

	$args = array(
		'post_type' => 'wpcm_player',
		'meta_key' => 'wpcm_number',
		'orderby' => 'menu_order meta_value_num',
		'order' => 'ASC',
		'showposts' => -1
	);

	if( $teams ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'wpcm_team',
				'field' => 'term_id',
				'terms' => $match_teams
			),
			array(
				'taxonomy' => 'wpcm_season',
				'field' => 'term_id',
				'terms' => $match_seasons
			)
		);
	}

	$players = get_posts( $args );

	if ( empty( $players ) ) { ?>

		<div class="wpcm-notice-block">
			<p>
				<?php _e( 'No players found!', 'wpclubmanager' ); ?>
			</p>
		</div>
	<?php
	} else {

		if ( ! is_array( $selected_players ) ) $selected_players = array();

		$selected_players = array_merge( array( 'lineup' => array(), 'subs' => array() ), $selected_players );

		$wpcm_player_stats_labels = wpcm_get_sports_stats_labels(); ?>

		<p class="">
			<?php _e( 'Click and drag players to reorder', 'wpclubmanager' ); ?>
			<img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" />
		</p>
		<table class="wpcm-match-players-table">
			<thead>
				<tr class="player-stats-list-labels">
					<th>&nbsp;</th>

					<?php foreach( $wpcm_player_stats_labels as $key => $val ) { 

						if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
							<th<?php if( $key == 'greencards' ||$key == 'yellowcards' || $key == 'blackcards' || $key == 'redcards' ) echo ' class="th-checkbox"'; if( $key == 'mvp' ) echo ' class="th-radio"'; ?>><?php echo $val; ?></th>
						<?php
						endif;
					}

					if ( $type == 'subs' ) { ?>
						<th><?php _e( 'Player Off', 'wpclubmanager' ); ?></th>
					<?php } ?>

				</tr>
			</thead>
			<tbody id="sortable">
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
					$seasons = get_the_terms( $player->ID, 'wpcm_season' );

					if($teams > 0){
						foreach( $teams as $team ) {
							$teamclass = 'team_' . $team->term_id . ' ';
						}
					}else{
						$teamclass = 'team_0 ';
					}
					if($seasons > 0){
						foreach( $seasons as $season ) {
							$seasonclass = 'season_' . $season->term_id . ' ';
						}
					}else{
						$seasonclass = 'season_0 ';
					}

					$number = get_post_meta( $player->ID, 'wpcm_number', true );

					if( $number ) {
						$squad_number = $number . '. ';
					} else {
						$squad_number = '';
					} ?>

					<tr id="<?php echo $player->ID; ?>" data-player="<?php echo $player->ID; ?>" class="player-stats-list <?php echo $teamclass; ?> <?php echo $seasonclass; ?> sortable sorted">
						<td class="names">
							<label class="selectit">
								<input type="checkbox" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][checked]" class="player-select" value="1" <?php checked( true, $played ); ?> /><span class="name">
								<?php echo $squad_number; ?> <?php echo $player->post_title; ?></span>
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

							if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :

								if ( $key == 'greencards' ) { ?>

									<td class="<?php echo $key; ?>">
										<input type="checkbox" data-card="green" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?>/>
									</td>

								<?php } elseif ( $key == 'yellowcards' ) { ?>

									<td class="<?php echo $key; ?>">
										<input type="checkbox" data-card="yellow" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?>/>
									</td>

								<?php } elseif ( $key == 'blackcards' ) { ?>

									<td class="<?php echo $key; ?>">
										<input type="checkbox" data-card="black" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?>/>
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
										<input type="number" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $selected_players[$type], $player->ID, $key ); ?>"<?php if ( !$played ) echo ' disabled'; ?>/>
									</td>

								<?php }

							endif;
						
						endforeach;

						if ( $type == 'subs' ) {
							
							wpcm_player_subs_dropdown( $selected_players, $player->ID, !$played );
							
						} ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php
	}
}

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
	$sport = get_option('wpcm_sport');
	
	if( $show_number == 'yes') {
		$snumber = $number .'. ';
	}

	if ( isset( $value['mvp'] ) ) {
		$mvp = '<span class="mvp" title="' . __( 'Man of Match', 'wpclubmanager' ) . '">&#9733;</span>';
	} else {
		$mvp = '';
	}

	$output = '';

	$output .= '<tr>';

	$output .= '<th class="name"><div>' . $snumber . '<a href="' . get_permalink( $key ) . '">' . get_the_title( $key ) . '</a>' . $mvp;

	if ( array_key_exists( 'sub', $value ) && $value['sub'] > 0 ) {

		$output .= '<span class="sub">&larr; ' . get_the_title( $value['sub'] ) . '</span>';
	}

	$output .= '</div></th>';

	foreach( $value as $key => $stat ) {

		if( $stat == '0' || $stat == null ) {
			$stat = '&mdash;';
		}

		if( $key == 'checked' || $key == 'sub' || $key == 'greencards' || $key == 'yellowcards' || $key == 'blackcards' || $key == 'redcards' || $key == 'mvp' ) {
			$output .= '';
		} else {
			if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) {
				$output .= '<td class="'.$key.'">' . $stat . '</td>';
			}
		}
	}

	if( $sport == 'soccer' || $sport == 'rugby' || $sport == 'hockey_field' || $sport == 'footy' || $sport == 'floorball' || $sport == 'gaelic' ) {

		$output .= '<td class="notes">';

		if ( isset( $value['greencards'] ) ) {
			
			$output .= '<span class="greencard" title="' . __( 'Green Card', 'wpclubmanager' ) . '">' . __( 'Green Card', 'wpclubmanager' ) . '</span>';
		}
		
		if ( isset( $value['yellowcards'] ) ) {
			
			$output .= '<span class="yellowcard" title="' . __( 'Yellow Card', 'wpclubmanager' ) . '">' . __( 'Yellow Card', 'wpclubmanager' ) . '</span>';
		}

		if ( isset( $value['blackcards'] ) ) {
			
			$output .= '<span class="blackcard" title="' . __( 'Black Card', 'wpclubmanager' ) . '">' . __( 'Black Card', 'wpclubmanager' ) . '</span>';
		}
		
		if ( isset( $value['redcards'] ) ) {
			$output .= '<span class="redcard" title="' . __( 'Red Card', 'wpclubmanager' ) . '">' . __( 'Red Card', 'wpclubmanager' ) . '</span>';
		}

		$output .= '</td>';

	}

	$output .= '</tr>';

	return $output;
}

/**
 * Save ajax menu_order sortable
 *
 * @access public
 * @since 1.1.9
 */
function wpcm_match_players_item_order() {

    global $wpdb;

    $order = explode(',', $_POST['order']);
    $counter = 0;
    foreach ($order as $item_id) {
        $wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
        $counter++;
    }
    die(1);
}
add_action('wp_ajax_item_sort', 'wpcm_match_players_item_order');
add_action('wp_ajax_nopriv_item_sort', 'wpcm_match_players_item_order');