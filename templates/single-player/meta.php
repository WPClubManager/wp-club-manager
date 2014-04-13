<?php
/**
 * Single Player Meta
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $wpclubmanager;

$season = '0'; ?>

	<table>
					
		<tbody>

			<?php
			if ( get_option( 'wpcm_player_profile_show_number' ) == 'yes') { ?>

				<tr>
					<th>
						<?php _e( 'Squad No.', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo get_post_meta( $post->ID, 'wpcm_number', true ); ?>
					</td>
				</tr>
			<?php
			}

			if ( get_option( 'wpcm_player_profile_show_dob' ) == 'yes') { ?>

				<tr>
					<th>
						<?php _e( 'Birthday', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ); ?>
					</td>
				</tr>
			<?php }

			if ( get_option( 'wpcm_player_profile_show_age' ) == 'yes') { ?>

				<tr>
					<th>
						<?php _e( 'Age', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ); ?>
					</td>
				</tr>
			<?php }

			if ( get_option( 'wpcm_player_profile_show_height' ) == 'yes') {

				$height = get_post_meta( $post->ID, 'wpcm_height', true ); ?>

				<tr>
					<th>
						<?php _e( 'Height', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo $height; ?>
					</td>
				</tr>
			<?php
			}

			if ( get_option( 'wpcm_player_profile_show_weight' ) == 'yes') {

				$weight = get_post_meta( $post->ID, 'wpcm_weight', true ); ?>

				<tr>
					<th>
						<?php _e( 'Weight', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo $weight; ?>
					</td>
				</tr>
			<?php
			}

			if ( get_option( 'wpcm_player_profile_show_season' ) == 'yes') {

				$seasons = get_the_terms( $post->ID, 'wpcm_season' );
						
				if ( is_array( $seasons ) ) {

					$player_seasons = array();

					foreach ( $seasons as $value ) {

						$player_seasons[] = $value->name;
					} ?>

					<tr>
						<th>
							<?php _e( 'Season', 'wpclubmanager' ); ?>
						</th>
						<td>
							<?php echo implode( ', ', $player_seasons ); ?>
						</td>
					</tr>
				<?php
				}
			}

			if ( get_option( 'wpcm_player_profile_show_team' ) == 'yes') {

				$teams = get_the_terms( $post->ID, 'wpcm_team' );

				if ( is_array( $teams ) ) {
							
					$player_teams = array();

					foreach ( $teams as $team ) {
						
						$player_teams[] = $team->name;
					} ?>

					<tr>
						<th>
							<?php _e( 'Team', 'wpclubmanager' ); ?>
						</th>
						<td>
							<?php echo implode( ', ', $player_teams ); ?>
						</td>
					</tr>
				<?php
				}
			}

			if ( get_option( 'wpcm_player_profile_show_position' ) == 'yes') {

				$positions = get_the_terms( $post->ID, 'wpcm_position' );

				if ( is_array( $positions ) ) {

					$player_positions = array();

					foreach ( $positions as $position ) {
						
						$player_positions[] = $position->name;
					} ?>

					<tr>
						<th>
							<?php _e( 'Position', 'wpclubmanager' ); ?>
						</th>
						<td>
							<?php echo implode( ', ', $player_positions ); ?>
						</td>
					</tr>
				<?php
				}
			}

			if ( get_option( 'wpcm_player_profile_show_joined' ) == 'yes') { ?>

				<tr>
					<th>
						<?php _e( 'Joined', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ); ?>
					</td>
				</tr>
			<?php
			}

			if ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes') {

				$natl = get_post_meta( $post->ID, 'wpcm_natl', true );
				$hometown = get_post_meta( $post->ID, 'wpcm_hometown', true ); ?>

				<tr>
					<th>
						<?php _e( 'Hometown', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php echo $hometown; ?> <img class="flag" src="<?php echo WPCM_URL; ?>assets/images/flags/<?php echo $natl; ?>.png" />
					</td>
				</tr>
			<?php
			}

			if ( get_option( 'wpcm_player_profile_show_prevclubs' ) == 'yes') {

				$prevclubs = get_post_meta( $post->ID, 'wpcm_prevclubs', true ); ?>

				<tr>
					<th>
						<?php _e( 'Previous Clubs', 'wpclubmanager' ); ?>
					</th>
					<td>
						<?php
						if ( ! empty ( $prevclubs ) ) {
							echo $prevclubs;
						} else {
							_e('None', 'wpclubmanager');
						} ?>
					</td>
				</tr>
			<?php
			} ?>

		</tbody>
				
	</table>