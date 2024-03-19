<?php
/**
 * Match Players
 *
 * Displays the match players box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Match_Players
 */
class WPCM_Meta_Box_Match_Players {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$players = maybe_unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) ); ?>

			<div class="playersdiv" id="wpcm_players">

				<ul class="wpcm_stats-tabs">
					<li class="tabs"><a href="#wpcm_lineup" tabindex="3"><?php esc_html_e( 'Starting Lineup', 'wp-club-manager' ); ?></a></li>
					<li class="hide-if-no-js"><a href="#wpcm_subs" tabindex="3"><?php esc_html_e( 'Substitutes Used', 'wp-club-manager' ); ?></a></li>
					<li class="hide-if-no-js"><a href="#wpcm_subs_not_used" tabindex="3"><?php esc_html_e( 'Substitutes Not Used', 'wp-club-manager' ); ?></a></li>
				</ul>
				<div id="wpcm_lineup" class="tabs-panel">
					<?php self::wpcm_match_player_stats_table( $players, 'lineup' ); ?>
					<p class="wpcm_counter"><?php esc_html_e( 'Starting lineup selected:', 'wp-club-manager' ); ?> <span class="counter"></span></p>
					<p><img src="<?php bloginfo( 'url' ); ?>/wp-admin/images/loading.gif" id="loading-animation" /></p>
				</div>
				<div id="wpcm_subs" class="tabs-panel" style="display: none;">
					<?php self::wpcm_match_player_stats_table( $players, 'subs' ); ?>
					<p class="wpcm_counter"><?php esc_html_e( 'Substitutes selected:', 'wp-club-manager' ); ?> <span class="counter"></span></p>
					<p><img src="<?php bloginfo( 'url' ); ?>/wp-admin/images/loading.gif" id="loading-animation" /></p>
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
	 *
	 * @param array  $players
	 * @param string $id
	 * @param bool   $disabled
	 *
	 * @return void
	 */
	public static function wpcm_player_subs_dropdown( $players = array(), $id = null, $disabled = false ) {

		global $post;

		$teams   = get_the_terms( $post->ID, 'wpcm_team' );
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
		} else {
			$match_seasons = array();
		}

		$args = array(
			'post_type'      => 'wpcm_roster',
			'posts_per_page' => -1,
			'tax_query'      => array(),
		);

		if ( $team ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'field'    => 'term_id',
				'terms'    => $match_teams,
			);
		}

		if ( $season ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'field'    => 'term_id',
				'terms'    => $match_seasons,
			);
		}

		$roster = get_posts( $args );

		if ( empty( $roster ) ) {

			$args = array(
				'post_type' => 'wpcm_player',
				// 'meta_key' => 'wpcm_number',
				'orderby'   => 'menu_order',
				'order'     => 'ASC',
				'showposts' => -1,
			);

			if ( $teams ) {
				$args['tax_query'] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'wpcm_team',
						'field'    => 'term_id',
						'terms'    => $match_teams,
					),
					array(
						'taxonomy' => 'wpcm_season',
						'field'    => 'term_id',
						'terms'    => $match_seasons,
					),
				);
			}

			$subs = get_posts( $args );

		} else {

			$post_id = $roster[0]->ID;

			$picked_players = (array) maybe_unserialize( get_post_meta( $post_id, '_wpcm_roster_players', true ) );

			$args = array(
				'post_type'      => 'wpcm_player',
				// 'meta_key' => 'wpcm_number',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				// 'suppress_filters' => 0,
				'post__in'       => $picked_players,
			);

			$subs = get_posts( $args );

		}
		?>

		<td>
			<select name="wpcm_players[subs][<?php echo esc_attr( $id ); ?>][sub]" data-player="<?php echo esc_attr( $id ); ?>" class="postform" <?php disabled( true, $disabled ); ?>>
				<option value="-1"><?php esc_html_e( 'None', 'wp-club-manager' ); ?></option>

				<?php foreach ( $subs as $sub ) { ?>
				<option value="<?php echo esc_attr( $sub->ID ); ?>"<?php echo ( get_wpcm_stats_value( $players['subs'], $id, 'sub' ) == $sub->ID ? ' selected' : '' ); ?>>
					<?php echo esc_html( get_post_meta( $sub->ID, 'wpcm_number', true ) ); ?>. <?php echo esc_html( $sub->post_title ); ?>
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
	 *
	 * @param array  $players
	 * @param string $id
	 * @param bool   $disabled
	 *
	 * @return void
	 */
	public static function wpcm_player_subs_minutes( $players = array(), $id = null, $disabled = false ) {
		global $player;

		$players = array(
			'lineup' => array(),
			'subs'   => array(),
		);
		?>

		<td>
			<input type="text" data-player="<?php echo esc_attr( $id ); ?>" name="wpcm_players[subs][<?php echo esc_attr( $id ); ?>][subtime]" value="<?php echo esc_html( get_wpcm_stats_value( $players['subs'], $id, 'subtime' ) ); ?>" size="2" <?php disabled( true, $disabled ); ?>/>
		</td>
		<?php
	}

	/**
	 * Player stats table.
	 *
	 * @access public
	 *
	 * @param array  $selected_players
	 * @param string $type
	 * @param bool   $keyarray
	 *
	 * @return void
	 */
	public static function wpcm_match_player_stats_table( $selected_players = array(), $type = 'lineup', $keyarray = false ) {

		global $post;

		$count      = 0;
		$teams      = get_the_terms( $post->ID, 'wpcm_team' );
		$seasons    = get_the_terms( $post->ID, 'wpcm_season' );
		$show_shirt = get_option( 'wpcm_lineup_show_shirt_numbers' );
		$captain    = get_post_meta( $post->ID, '_wpcm_match_captain', true );
		$not_used   = get_post_meta( $post->ID, '_wpcm_match_subs_not_used', true );
		if ( ! is_array( $not_used ) ) {
			$not_used = array();
		}
		if ( is_array( $teams ) ) {
			$team = $teams[0]->term_id;
		} else {
			$team = null;
		}
		if ( is_array( $seasons ) ) {
			$season = $seasons[0]->term_id;
		} else {
			$season = null;
		}

		$args = array(
			'post_type'      => 'wpcm_roster',
			'posts_per_page' => -1,
			'tax_query'      => array(),
		);

		if ( $team ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'field'    => 'term_id',
				'terms'    => $team,
			);
		}

		if ( $season ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'field'    => 'term_id',
				'terms'    => $season,
			);
		}

		$roster = get_posts( $args );

		if ( empty( $roster ) ) {

			$args = array(
				'post_type'      => 'wpcm_player',
				'meta_key'       => 'wpcm_number',
				'orderby'        => 'menu_order meta_value_num',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				'tax_query'      => array(),
			);

			if ( $team ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'field'    => 'term_id',
					'terms'    => $team,
				);
			}

			if ( $season ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'field'    => 'term_id',
					'terms'    => $season,
				);
			}

			$players = get_posts( $args );

		} else {

			$post_id = $roster[0]->ID;

			$picked_players = (array) maybe_unserialize( get_post_meta( $post_id, '_wpcm_roster_players', true ) );

			$args = array(
				'post_type'      => 'wpcm_player',
				// 'meta_key' => 'wpcm_number',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				// 'suppress_filters' => 0,
				'post__in'       => $picked_players,
			);

			$players = get_posts( $args );

		}

		if ( empty( $players ) ) {
			?>

			<div class="wpcm-notice-block">
				<p>
					<?php esc_html_e( 'No players found!', 'wp-club-manager' ); ?>
				</p>
			</div>
			<?php
		} else {

			if ( ! is_array( $selected_players ) ) {
				$selected_players = array();
			}

			$selected_players = array_merge( array(
				'lineup' => array(),
				'subs'   => array(),
			), $selected_players );

			$wpcm_player_stats_labels = wpcm_get_preset_labels();
			?>
			<table class="wpcm-match-players-table">
				<thead>
					<tr class="player-stats-list-labels">
						<?php
						if ( 'yes' == $show_shirt ) {
							?>
							<th>&nbsp;</th>
							<?php
						}
						?>
						<th>&nbsp;</th>

						<?php do_action( 'wpclubmanager_admin_before_lineup_stats_head' ); ?>

						<?php
						if ( 'subs_not_used' !== $type ) {
							foreach ( $wpcm_player_stats_labels as $key => $val ) {

								if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
									$class = '';
									if ( in_array( $key, array(
										'greencards',
										'yellowcards',
										'blackcards',
										'redcards',
									) ) ) {
										$class = 'th-checkbox';
									}
									if ( 'mvp' == $key ) {
										$class = 'th-radio';
									}
									?>
									<th class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $val ); ?></th>
									<?php
								endif;
							}
						}

						if ( 'lineup' == $type ) {
							?>
							<th><?php echo esc_html_x( 'CAP', 'Captain', 'wp-club-manager' ); ?></th>
							<?php
						}
						if ( 'subs' == $type ) {
							?>
							<th><?php esc_html_e( 'Player Off', 'wp-club-manager' ); ?></th>
						<?php } ?>

					</tr>
				</thead>
				<tbody class="wpcm-sortable">
					<?php foreach ( $players as $player ) { ?>
						<?php
						$played = (
							is_array( $selected_players ) &&
							array_key_exists( $type, $selected_players ) &&
							is_array( $selected_players[ $type ] ) &&
							array_key_exists( $player->ID, $selected_players[ $type ] ) &&
							is_array( $selected_players[ $type ][ $player->ID ] )
						);

						$teams   = get_the_terms( $player->ID, 'wpcm_team' );
						$seasons = get_the_terms( $player->ID, 'wpcm_season' );

						if ( $teams ) {
							$teamclass = array();
							foreach ( $teams as $team ) {
								$teamclass[] = 'team_' . $team->term_id . ' ';
							}
						} else {
							$teamclass = array();
						}
						$player_teams = implode( '', $teamclass );

						if ( $seasons > 0 ) {
							foreach ( $seasons as $season ) {
								$seasonclass = 'season_' . $season->term_id . ' ';
							}
						} else {
							$seasonclass = 'season_0 ';
						}

						$number = get_post_meta( $player->ID, 'wpcm_number', true );

						if ( $number ) {
							$squad_number = $number . '. ';
						} else {
							$squad_number = '';
						}

						++$count;

						if ( 'yes' == $show_shirt ) {
							$shirt = '<td class="shirt-number">' . $count . '</td>';
						} else {
							$shirt = '';
						}
						?>

						<tr id="<?php echo esc_attr( $player->ID ); ?>" data-player="<?php echo esc_attr( $player->ID ); ?>" class="player-stats-list <?php echo esc_attr( $player_teams ); ?> <?php echo esc_attr( $seasonclass ); ?> sortable sorted">
							<?php echo esc_html( apply_filters( 'wpcm_players_shirt_number_output', $shirt, $player->ID, $selected_players, $type, $count, $played ) ); ?>

								<td class="names">
									<i class="dashicons dashicons-move"></i>
									<label class="selectit">
										<input type="checkbox" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][checked]" class="player-select" value="1" <?php checked( true, $played ); ?> />
										<span class="name">
											<?php echo wp_kses_post( apply_filters( 'wpcm_player_squad_number_output', $squad_number, $player->ID ) ); ?> <?php echo wp_kses_post( get_player_title( $player->ID ) ); ?>
										</span>
									</label>
								</td>
							<?php

							do_action( 'wpclubmanager_admin_before_lineup_stats', $selected_players, $player->ID, ! $played );

							if ( 'subs_not_used' !== $type ) {
								foreach ( $wpcm_player_stats_labels as $key => $val ) :

									$keyarray = (
											is_array( $selected_players ) &&
											array_key_exists( $type, $selected_players ) &&
											is_array( $selected_players[ $type ] ) &&
											array_key_exists( $player->ID, $selected_players[ $type ] ) &&
											is_array( $selected_players[ $type ][ $player->ID ] ) &&
											array_key_exists( $key, $selected_players[ $type ][ $player->ID ] )
										);

									if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :

										if ( 'greencards' == $key ) {
											?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="checkbox" data-card="green" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( true, $keyarray ); ?>
																												<?php
																												if ( ! $played ) {
																													echo ' disabled';}
																												?>
													/>
											</td>

										<?php } elseif ( 'yellowcards' == $key ) { ?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="checkbox" data-card="yellow" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( true, $keyarray ); ?>
																													<?php
																													if ( ! $played ) {
																														echo ' disabled';}
																													?>
													/>
											</td>

										<?php } elseif ( 'blackcards' == $key ) { ?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="checkbox" data-card="black" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( true, $keyarray ); ?>
																												<?php
																												if ( ! $played ) {
																													echo ' disabled';}
																												?>
													/>
											</td>

										<?php } elseif ( 'redcards' == $key ) { ?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="checkbox" data-card="red" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( true, $keyarray ); ?>
																												<?php
																												if ( ! $played ) {
																													echo ' disabled';}
																												?>
													/>
											</td>

										<?php } elseif ( 'rating' == $key ) { ?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="number" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo ( 'subs_not_used' == $type ? '0' : wpcm_stats_value( $selected_players[ $type ], $player->ID, $key ) ); // phpcs:ignore ?>" min="0" max="10"
																							<?php
																							if ( ! $played ) {
																								echo ' disabled';}
																							?>
													/>
											</td>

										<?php } elseif ( 'mvp' == $key ) { ?>

											<td class="mvp">
												<input type="radio" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( true, $keyarray ); ?>
																							<?php
																							if ( ! $played ) {
																								echo ' disabled';}
																							?>
													/>
											</td>

										<?php } else { ?>

											<td class="<?php echo esc_attr( $key ); ?>">
												<input type="number" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_players[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $player->ID ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo ( 'subs_not_used' == $type ? '0' : wpcm_stats_value( $selected_players[ $type ], $player->ID, $key ) ); // phpcs:ignore ?>"
																							<?php
																							if ( ! $played ) {
																								echo ' disabled';}
																							?>
													/>
											</td>

											<?php
										}

									endif;

								endforeach;

								if ( 'lineup' == $type ) {
									?>

									<td class="captain">

										<input type="radio" data-player="<?php echo esc_attr( $player->ID ); ?>" name="wpcm_match_captain" value="<?php echo esc_html( $player->ID ); ?>"<?php checked( $captain, $player->ID ); ?>
																					<?php
																					if ( ! $played ) {
																						echo ' disabled';}
																					?>
											/>
									</td>

									<?php
								}

								if ( 'subs' == $type ) {

									self::wpcm_player_subs_dropdown( $selected_players, $player->ID, ! $played );

								}
							}

							do_action( 'wpclubmanager_admin_after_lineup_stats' );
							?>

						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
		}
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

		$players_data = filter_input( INPUT_POST, 'wpcm_players', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( $players_data ) {
			$players = (array) $players_data;
			if ( is_array( $players ) ) {
				if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) ) {
					$players['lineup'] = array_filter( $players['lineup'], 'wpcm_array_filter_checked' );
				}
				if ( array_key_exists( 'subs', $players ) && is_array( $players['subs'] ) ) {
					$players['subs'] = array_filter( $players['subs'], 'wpcm_array_filter_checked' );
				}
				if ( array_key_exists( 'subs_not_used', $players ) && is_array( $players['subs_not_used'] ) ) {
					$players['subs_not_used'] = array_filter( $players['subs_not_used'], 'wpcm_array_filter_checked' );
				}
			}
			update_post_meta( $post_id, 'wpcm_players', serialize( $players ) );
			update_post_meta( $post_id, '_wpcm_match_subs_not_used', $players['subs_not_used'] );
		}

		$match_captain = filter_input( INPUT_POST, 'wpcm_match_captain', FILTER_VALIDATE_INT );
		if ( $match_captain ) {
			update_post_meta( $post_id, '_wpcm_match_captain', $match_captain );
		}

		do_action( 'delete_plugin_transients' );
	}
}
