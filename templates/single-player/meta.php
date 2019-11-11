<?php
/**
 * Single Player - Meta
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post; ?>

<table>			
	<tbody>

		<?php
		if ( get_option( 'wpcm_player_profile_show_number' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Squad No.', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo get_post_meta( $post->ID, 'wpcm_number', true ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_dob' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Date of Birth', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_age' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Age', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_height' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Height', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo get_post_meta( $post->ID, 'wpcm_height', true ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_weight' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Weight', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo get_post_meta( $post->ID, 'wpcm_weight', true ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_season' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Season', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wpcm_get_player_seasons( $post->ID ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_team' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Team', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wpcm_get_player_teams( $post->ID ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_position' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Position', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wpcm_get_player_positions( $post->ID ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes' || get_option( 'wpcm_player_profile_show_nationality' ) == 'yes' ) { ?>
			<tr>
				<th>
					<?php _e( 'Birthplace', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes' ?get_post_meta( $post->ID, 'wpcm_hometown', true ) : '' ); ?> <?php echo ( get_option( 'wpcm_player_profile_show_nationality' ) == 'yes' ? '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $post->ID, 'wpcm_natl', true ) . '.png" />' : '' ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_joined' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Joined', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_exp' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Experience', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?>
				</td>
			</tr>
		<?php }

		if ( get_option( 'wpcm_player_profile_show_prevclubs' ) == 'yes') { ?>
			<tr>
				<th>
					<?php _e( 'Previous Clubs', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo ( get_post_meta( $post->ID, 'wpcm_prevclubs', true ) ? get_post_meta( $post->ID, 'wpcm_prevclubs', true ) : __('None', 'wp-club-manager') ); ?>
				</td>
			</tr>
		<?php } ?>

	</tbody>	
</table>