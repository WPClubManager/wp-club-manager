<?php
/**
 * Single Player Bio
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );
$players = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );

$show_number = get_option('wpcm_player_profile_show_number');
$show_assists = get_option('wpcm_player_profile_show_assists');
$show_ratings = get_option('wpcm_player_profile_show_ratings');

if ( $played ) {

	if ( $players ) {

		// Lineup and Subs sections
							
		if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) ) { ?>
						
			<div class="wpcm-match-stats-start">

				<h3><?php echo _e( 'Lineup', 'wpclubmanager' ); ?></h3>

				<table>

					<thead>

						<tr>

							<?php if( $show_number == 'yes') { ?>

								<th class="squadno"><?php _e('No.', 'wpclubmanager') ?></th>

							<?php } ?>

							<th class="name"><?php _e('Name', 'wpclubmanager') ?></th>

							<th class="goals"><?php echo get_option('wpcm_player_goals_label'); ?></th>

							<?php if( $show_assists == 'yes') { ?>

								<th class="assists"><?php echo get_option('wpcm_player_assists_label'); ?></th>

							<?php } ?>

							<?php if( $show_ratings == 'yes') { ?>

								<th class="rating"><?php _e('Rating', 'wpclubmanager') ?></th>

							<?php } ?>

							<th class="notes"><?php _e('Notes', 'wpclubmanager') ?></th>

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
						
			<div class="wpcm-match-stats-subs small-12 columns">

				<h3><?php echo _e( 'Subs', 'wpclubmanager' ); ?></h3>

				<table>

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