<?php
/**
 * Single Player Bio
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );
$players = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );

if ( $played ) {

	if ( $players ) {

		// Lineup and Subs sections
							
		if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) ) { ?>
						
			<div class="wpcm-match-stats-start">

				<h3><?php echo _e( 'Lineup', 'wpclubmanager' ); ?></h3>

				<table>

					<thead>

						<tr>

							<th><?php _e('Name', 'wpclubmanager') ?></th>

							<th><?php echo get_option('wpcm_player_goals_label'); ?></th>

							<?php if( get_option('wpcm_player_profile_show_assists' == 'yes') ) { ?>

								<th><?php echo get_option('wpcm_player_assists_label'); ?></th>

							<?php } ?>

							<?php if( get_option('wpcm_player_profile_show_ratings' == 'yes') ) { ?>

								<th><?php _e('Rating', 'wpclubmanager') ?></th>

							<?php } ?>

							<th><?php _e('Notes', 'wpclubmanager') ?></th>

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