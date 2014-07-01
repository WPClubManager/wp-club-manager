<?php
/**
 * Single Player Bio
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );
$players = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );

$show_number = get_option('wpcm_player_profile_show_number');

$sport = get_option('wpcm_sport');

if ( $played ) {

	if ( $players ) {

		// Lineup and Subs sections
							
		if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) ) { ?>
						
			<div class="wpcm-match-stats-start">

				<h3><?php echo _e( 'Lineup', 'wpclubmanager' ); ?></h3>

				<table class="wpcm-lineup-table">

					<thead>

						<tr>

							<th class="name"><?php _e('Name', 'wpclubmanager') ?></th>

							<?php $wpcm_player_stats_labels = wpcm_get_sports_stats_labels();

							foreach( $wpcm_player_stats_labels as $key => $val ):

								if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) {

									if( $key != 'checked' && $key != 'greencards' && $key != 'yellowcards' && $key != 'blackcards' && $key != 'redcards' && $key != 'mvp' ) {  ?>

										<th><?php echo $val; ?></th>
									
									<?php
									}

								}

							endforeach;

							if( $sport == 'soccer' || $sport == 'rugby' || $sport == 'hockey_field' || $sport == 'footy' || $sport == 'floorball' || $sport == 'gaelic' ) { ?>
								<th class="notes"><?php _e('Cards', 'wpclubmanager') ?></th>
							<?php } ?>

						</tr>

					</thead>

					<tbody>
											
						<?php $count = 0;

						foreach( $players['lineup'] as $key => $value) {

							$count ++;
							echo wpcm_match_player_row( $key, $value, $count );

						} ?>
									
					</tbody>

				</table>

			</div>

		<?php
		}

		if ( array_key_exists( 'subs', $players ) && is_array( $players['subs'] ) ) { ?>
						
			<div class="wpcm-match-stats-subs">

				<h3><?php echo _e( 'Subs', 'wpclubmanager' ); ?></h3>

				<table class="wpcm-subs-table">

					<thead>

						<tr>

							<th class="name"><?php _e('Name', 'wpclubmanager') ?></th>

							<?php $wpcm_player_stats_labels = wpcm_get_sports_stats_labels();

							foreach( $wpcm_player_stats_labels as $key => $val ):

								if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) {

									if( $key != 'checked' && $key != 'greencards' && $key != 'yellowcards' && $key != 'blackcards'  && $key != 'redcards' && $key != 'mvp' ) {  ?>

										<th><?php echo $val; ?></th>
									
									<?php
									}

								}

							endforeach;

							if( $sport == 'soccer' || $sport == 'rugby' || $sport == 'hockey_field' || $sport == 'footy' || $sport == 'floorball' || $sport == 'gaelic' ) { ?>
								<th class="notes"><?php _e('Cards', 'wpclubmanager') ?></th>
							<?php } ?>

						</tr>

					</thead>

					<tbody>
											
						<?php $count = 0;

						foreach( $players['subs'] as $key => $value) {
										
							$count ++;
							echo wpcm_match_player_row( $key, $value, $count );
											
						} ?>
								
					</tbody>
							
				</table>

			</div>
						
		<?php
		}
	}

}