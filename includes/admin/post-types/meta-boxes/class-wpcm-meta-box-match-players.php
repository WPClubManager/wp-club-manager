<?php
/**
 * Match Players
 *
 * Displays the match players box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.5.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Players {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$players = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );

		$club = get_default_club(); ?>
		
			<div class="playersdiv" id="wpcm_players">
					
				<ul class="wpcm_stats-tabs">
					<li class="tabs"><a href="#wpcm_lineup" tabindex="3"><?php _e( 'Starting Lineup', 'wp-club-manager' ); ?></a></li>
					<li class="hide-if-no-js"><a href="#wpcm_subs" tabindex="3"><?php _e( 'Substitutes Used', 'wp-club-manager' ); ?></a></li>
					<li class="hide-if-no-js"><a href="#wpcm_subs_not_used" tabindex="3"><?php _e( 'Substitutes Not Used', 'wp-club-manager' ); ?></a></li>
				</ul>
				<div id="wpcm_lineup" class="tabs-panel">
					<?php self::wpcm_match_player_stats_table( $players, 'lineup' ); ?>
					<p class="wpcm_counter"><?php _e('You have selected', 'wp-club-manager'); ?> <span class="counter"></span> <?php _e('players', 'wp-club-manager'); ?></p>
				</div>
				<div id="wpcm_subs" class="tabs-panel" style="display: none;">
					<?php self::wpcm_match_player_stats_table( $players, 'subs' ); ?>
					<p class="wpcm_counter"><?php _e('You have selected', 'wp-club-manager'); ?> <span class="counter"></span> <?php _e('substitutes', 'wp-club-manager'); ?></p>
				</div>
				<div id="wpcm_subs_not_used" class="tabs-panel" style="display: none;">
					<?php self::wpcm_match_player_stats_table( $players, 'subs_not_used' ); ?>
				</div>
			</div>
		<?php
	}

	/**
	 * Match player subs dropdown.
	 *
	 * @access public
	 * @param array
	 * @param string $id
	 * @param bool $disabled = false
	 * @return void
	 */
	public static function wpcm_player_subs_dropdown( $players = array(), $id = null, $disabled = false ) {

		global $post;
		
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

		$subs = get_posts( $args ); ?>

		<td>
			<select name="wpcm_players[subs][<?php echo $id; ?>][sub]" data-player="<?php echo $id; ?>" class="postform" <?php disabled( true, $disabled ); ?>>
				<option value="-1"><?php _e( 'None', 'wp-club-manager' ); ?></option>

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
	public static function wpcm_player_subs_minutes( $players = array(), $id = null, $disabled = false ) {
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
	public static function wpcm_match_player_stats_table( $selected_players = array(), $type = 'lineup', $keyarray = false ) {

		global $post, $player;

		$count = 0;

		$teams = get_the_terms( $post->ID, 'wpcm_team' );
		$seasons = get_the_terms( $post->ID, 'wpcm_season' );
		$show_shirt = get_option('wpcm_lineup_show_shirt_numbers');
		$captain = get_post_meta( $post->ID, '_wpcm_match_captain', true );
		$not_used = get_post_meta( $post->ID, '_wpcm_match_subs_not_used', true );
		if( !is_array( $not_used ) ) {

			$not_used = array();
		}

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
					<?php _e( 'No players found!', 'wp-club-manager' ); ?>
				</p>
			</div>
		<?php
		} else {

			if ( ! is_array( $selected_players ) ) $selected_players = array();

			$selected_players = array_merge( array( 'lineup' => array(), 'subs' => array() ), $selected_players );

			$wpcm_player_stats_labels = wpcm_get_preset_labels(); ?>

			<p class="wpcm-match-players-desc">
				<strong><?php _e( 'Drag and drop players to control their display order.', 'wp-club-manager' ); ?></strong>
				<img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" />
			</p>
			<table class="wpcm-match-players-table">
				<thead>
					<tr class="player-stats-list-labels">
						<?php
						if($show_shirt == 'yes') { ?>
							<th>&nbsp;</th>
						<?php
						} ?>
						<th>&nbsp;</th>

						<?php do_action( 'wpclubmanager_admin_before_lineup_stats_head' ); ?>

						<?php foreach( $wpcm_player_stats_labels as $key => $val ) { 

							if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) : ?>
								<th<?php if( $key == 'greencards' ||$key == 'yellowcards' || $key == 'blackcards' || $key == 'redcards' ) echo ' class="th-checkbox"'; if( $key == 'mvp' ) echo ' class="th-radio"'; ?>><?php echo $val; ?></th>
							<?php
							endif;
						}

						if ( $type == 'lineup' ) { ?>
							<th><?php _e( 'Captain', 'wp-club-manager' ); ?></th>
						<?php }
						if ( $type == 'subs' ) { ?>
							<th><?php _e( 'Player Off', 'wp-club-manager' ); ?></th>
						<?php } ?>

					</tr>
				</thead>
				<tbody class="wpcm-sortable">
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

						if($teams){
							$teamclass = array();
							foreach( $teams as $team ) {
								$teamclass[] = 'team_' . $team->term_id . ' ';
							}
						}else{
							$teamclass = array();
						}
						$player_teams = implode( '', $teamclass );

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
						}

						$count++;
						
						if( $show_shirt == 'yes' ) {
							$shirt = '<td class="shirt-number">'.$count.'</td>';
						}else{
							$shirt = '';
						} ?>

						<tr id="<?php echo $player->ID; ?>" data-player="<?php echo $player->ID; ?>" class="player-stats-list <?php echo $player_teams; ?> <?php echo $seasonclass; ?> sortable sorted">
							<?php echo $shirt; ?>

							<?php
							if( $type == 'subs_not_used' ) { ?>
								<td class="">
									<label class="selectit">
										<input type="checkbox" data-player="<?php echo $player->ID; ?>" name="wpcm_match_subs_not_used[<?php echo $player->ID; ?>]" class="player-select" value="" <?php checked( array_key_exists( $player->ID, $not_used ) ); ?> />
										<span class="name">
											<?php echo $squad_number; ?> <?php echo $player->post_title; ?>
										</span>
									</label>
								</td>
							<?php
							} else { ?>
								<td class="names">
									<label class="selectit">
										<input type="checkbox" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][checked]" class="player-select" value="1" <?php checked( true, $played ); ?> />
										<span class="name">
											<?php echo $squad_number; ?> <?php echo $player->post_title; ?>
										</span>
									</label>
								</td>
							<?php
							}

							do_action( 'wpclubmanager_admin_before_lineup_stats', $selected_players, $player->ID, !$played );

							foreach( $wpcm_player_stats_labels as $key => $val ):

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
											<input type="number" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="<?php echo ( $type == 'subs_not_used' ? '0' : wpcm_stats_value( $selected_players[$type], $player->ID, $key ) ); ?>" min="0" max="10"<?php if ( !$played ) echo ' disabled'; ?>/>
										</td>

									<?php } elseif ( $key == 'mvp' ) { ?>

										<td class="mvp">
											<input type="radio" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="1" <?php checked( true, $keyarray ); ?><?php if ( !$played ) echo ' disabled'; ?> />
										</td>

									<?php } else { ?>

										<td class="<?php echo $key; ?>">
											<input type="number" data-player="<?php echo $player->ID; ?>" name="wpcm_players[<?php echo $type; ?>][<?php echo $player->ID; ?>][<?php echo $key; ?>]" value="<?php echo ( $type == 'subs_not_used' ? '0' : wpcm_stats_value( $selected_players[$type], $player->ID, $key ) ); ?>"<?php if ( !$played ) echo ' disabled'; ?>/>
										</td>

									<?php }

								endif;
							
							endforeach;

							if ( $type == 'lineup' ) { ?>

								<td class="captain">

									<input type="radio" data-player="<?php echo $player->ID; ?>" name="wpcm_match_captain" value="<?php echo $player->ID; ?>"<?php checked($captain, $player->ID); ?><?php if ( !$played ) echo ' disabled'; ?> />
								</td>

							<?php }

							if ( $type == 'subs' ) {
								
								self::wpcm_player_subs_dropdown( $selected_players, $player->ID, !$played );
								
							}
							
							do_action( 'wpclubmanager_admin_after_lineup_stats'); ?>

						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
		}
	}

	
	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if(isset($_POST['wpcm_players'])){
			$players = (array)$_POST['wpcm_players'];
			if ( is_array( $players ) ) {
				if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) )
					$players['lineup'] = array_filter( $players['lineup'], 'wpcm_array_filter_checked' );
				if ( array_key_exists( 'subs', $players ) &&  is_array( $players['subs'] ) )
					$players['subs'] = array_filter( $players['subs'], 'wpcm_array_filter_checked' );
				if ( array_key_exists( 'subs_not_used', $players ) &&  is_array( $players['subs_not_used'] ) )
					$players['subs_not_used'] = array_filter( $players['subs_not_used'], 'wpcm_array_filter_checked' );
			}
			update_post_meta( $post_id, 'wpcm_players', serialize( $players ) );
		}

		if(isset($_POST['wpcm_match_captain'])){
			update_post_meta( $post_id, '_wpcm_match_captain', $_POST['wpcm_match_captain'] );
		}

		if(isset($_POST['wpcm_match_subs_not_used'])){
			update_post_meta( $post_id, '_wpcm_match_subs_not_used', $_POST['wpcm_match_subs_not_used'] );
		}

		do_action( 'delete_plugin_transients' );
	}
}